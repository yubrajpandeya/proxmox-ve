<?php

use Illuminate\Database\Capsule\Manager as Capsule;

require_once dirname(__DIR__, 3) . '/init.php';
require_once dirname(__DIR__, 2) . '/addons/pvewhmcs/proxmox.php';
require_once __DIR__ . '/pvewhmcs.php';

header('Content-Type: application/json');

function pvewhmcs_json_response($payload, $statusCode = 200)
{
	http_response_code($statusCode);
	echo json_encode($payload);
	exit;
}

$serviceId = isset($_GET['serviceid']) ? (int) $_GET['serviceid'] : 0;
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
$loadStats = isset($_GET['stats']) && $_GET['stats'] === '1';

if ($serviceId <= 0 || $token === '') {
	pvewhmcs_json_response(array('success' => false, 'message' => 'Invalid request.'), 400);
}

if (
	empty($_SESSION['pvewhmcs_ajax_tokens'][$serviceId])
	|| !hash_equals($_SESSION['pvewhmcs_ajax_tokens'][$serviceId], $token)
) {
	pvewhmcs_json_response(array('success' => false, 'message' => 'Session expired. Refresh the page.'), 403);
}

$clientId = isset($_SESSION['uid']) ? (int) $_SESSION['uid'] : 0;
$isAdmin = !empty($_SESSION['adminid']);

$hostingQuery = Capsule::table('tblhosting')->where('id', '=', $serviceId);
if (!$isAdmin) {
	$hostingQuery->where('userid', '=', $clientId);
}
$pveservice = $hostingQuery->first();

if (!$pveservice) {
	pvewhmcs_json_response(array('success' => false, 'message' => 'Service not found.'), 404);
}

$guest = Capsule::table('mod_pvewhmcs_vms')->where('id', '=', $serviceId)->first();
if (!$guest) {
	pvewhmcs_json_response(array('success' => false, 'message' => 'Guest record not found.'), 404);
}

$pveserver = Capsule::table('tblservers')->where('id', '=', $pveservice->server)->first();
if (!$pveserver) {
	pvewhmcs_json_response(array('success' => false, 'message' => 'Proxmox server is not configured.'), 404);
}

try {
	$api_data = array('password2' => $pveserver->password);
	$serverpassword = localAPI('DecryptPassword', $api_data);

	$proxmox = new PVE2_API(
		$pveserver->ipaddress,
		$pveserver->username,
		'pam',
		$serverpassword['password'],
		$pveserver->port
	);

	if (!$proxmox->login()) {
		pvewhmcs_json_response(array('success' => false, 'message' => 'Unable to log in to Proxmox.'), 502);
	}

	$guestNode = pvewhmcs_find_guest_node($proxmox, $guest, $serviceId);
	if (empty($guestNode)) {
		pvewhmcs_json_response(array('success' => false, 'message' => 'Unable to determine Proxmox node.'), 404);
	}

	try {
		$vmConfig = $proxmox->get('/nodes/' . $guestNode . '/' . $guest->vtype . '/' . $guest->vmid . '/config');
		$vmStatus = $proxmox->get('/nodes/' . $guestNode . '/' . $guest->vtype . '/' . $guest->vmid . '/status/current');
	} catch (Exception $e) {
		unset($_SESSION['pvewhmcs_node_' . $serviceId]);
		$guestNode = pvewhmcs_find_guest_node($proxmox, $guest, $serviceId, false);
		if (empty($guestNode)) {
			throw $e;
		}
		$vmConfig = $proxmox->get('/nodes/' . $guestNode . '/' . $guest->vtype . '/' . $guest->vmid . '/config');
		$vmStatus = $proxmox->get('/nodes/' . $guestNode . '/' . $guest->vtype . '/' . $guest->vmid . '/status/current');
	}

	$vmStatus['uptime'] = !empty($vmStatus['uptime']) ? time2format($vmStatus['uptime']) : '0s';
	$vmStatus['cpu'] = isset($vmStatus['cpu']) ? round($vmStatus['cpu'] * 100, 2) : 0;
	$vmStatus['diskusepercent'] = (!empty($vmStatus['maxdisk']) && isset($vmStatus['disk'])) ? intval($vmStatus['disk'] * 100 / $vmStatus['maxdisk']) : 0;
	$vmStatus['memusepercent'] = (!empty($vmStatus['maxmem']) && isset($vmStatus['mem'])) ? intval($vmStatus['mem'] * 100 / $vmStatus['maxmem']) : 0;
	$vmStatus['swapusepercent'] = (!empty($vmStatus['maxswap']) && isset($vmStatus['swap'])) ? intval($vmStatus['swap'] * 100 / $vmStatus['maxswap']) : 0;

	$vmConfig['vtype'] = $guest->vtype;
	$vmConfig['ipv4'] = $guest->ipaddress;
	$vmConfig['netmask4'] = $guest->subnetmask;
	$vmConfig['gateway4'] = $guest->gateway;
	$vmConfig['created'] = $guest->created;
	$vmConfig['v6prefix'] = $guest->v6prefix;

	$statistics = array();
	if ($loadStats) {
		foreach (array('year', 'month', 'week', 'day') as $timeframe) {
			$statistics['cpu'][$timeframe] = pvewhmcs_fetch_rrd_stat($proxmox, $guestNode, $guest->vtype, $guest->vmid, $timeframe, 'cpu');
			$statistics['mem'][$timeframe] = pvewhmcs_fetch_rrd_stat($proxmox, $guestNode, $guest->vtype, $guest->vmid, $timeframe, 'mem');
			$statistics['netinout'][$timeframe] = pvewhmcs_fetch_rrd_stat($proxmox, $guestNode, $guest->vtype, $guest->vmid, $timeframe, 'netin,netout');
			$statistics['diskrw'][$timeframe] = pvewhmcs_fetch_rrd_stat($proxmox, $guestNode, $guest->vtype, $guest->vmid, $timeframe, 'diskread,diskwrite');
		}
	}

	pvewhmcs_json_response(array(
		'success' => true,
		'node' => $guestNode,
		'config' => $vmConfig,
		'status' => $vmStatus,
		'statistics' => $statistics,
	));
} catch (Throwable $e) {
	pvewhmcs_json_response(array('success' => false, 'message' => $e->getMessage()), 500);
}

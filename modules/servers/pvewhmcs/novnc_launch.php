<?php

use Illuminate\Database\Capsule\Manager as Capsule;

require_once dirname(__DIR__, 3) . '/init.php';
require_once __DIR__ . '/pvewhmcs.php';

header('Content-Type: application/json');

function pvewhmcs_novnc_launch_response($payload, $statusCode = 200)
{
	http_response_code($statusCode);
	echo json_encode($payload);
	exit;
}

$serviceId = isset($_POST['serviceid']) ? (int) $_POST['serviceid'] : 0;
$token = isset($_POST['token']) ? (string) $_POST['token'] : '';

if ($serviceId <= 0 || $token === '') {
	pvewhmcs_novnc_launch_response(array('success' => false, 'message' => 'Invalid console request.'), 400);
}

if (
	empty($_SESSION['pvewhmcs_ajax_tokens'][$serviceId])
	|| !hash_equals($_SESSION['pvewhmcs_ajax_tokens'][$serviceId], $token)
) {
	pvewhmcs_novnc_launch_response(array('success' => false, 'message' => 'Session expired. Refresh the service page.'), 403);
}

$clientId = isset($_SESSION['uid']) ? (int) $_SESSION['uid'] : 0;
$isAdmin = !empty($_SESSION['adminid']);

$hostingQuery = Capsule::table('tblhosting')->where('id', '=', $serviceId);
if (!$isAdmin) {
	$hostingQuery->where('userid', '=', $clientId);
}
$service = $hostingQuery->first();

if (!$service) {
	pvewhmcs_novnc_launch_response(array('success' => false, 'message' => 'Service not found.'), 404);
}

$server = Capsule::table('tblservers')->where('id', '=', $service->server)->first();
if (!$server) {
	pvewhmcs_novnc_launch_response(array('success' => false, 'message' => 'Proxmox server is not configured.'), 404);
}

$product = Capsule::table('tblproducts')->where('id', '=', $service->packageid)->first();
if (!$product) {
	pvewhmcs_novnc_launch_response(array('success' => false, 'message' => 'WHMCS product is not configured.'), 404);
}

try {
	$apiData = array('password2' => $server->password);
	$serverPassword = localAPI('DecryptPassword', $apiData);

	$params = array(
		'serviceid' => $serviceId,
		'serverhostname' => isset($server->hostname) ? $server->hostname : '',
		'serverip' => $server->ipaddress,
		'serverusername' => $server->username,
		'serverpassword' => $serverPassword['password'],
		'serverport' => $server->port,
		'configoption3' => isset($product->configoption3) ? $product->configoption3 : '',
	);

	$result = pvewhmcs_noVNC($params);
	if (preg_match('/href="([^"]+)"/', $result, $matches)) {
		$url = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$url = str_replace('&amp;', '&', $url);
		pvewhmcs_novnc_launch_response(array(
			'success' => true,
			'url' => $url,
		));
	}

	pvewhmcs_novnc_launch_response(array(
		'success' => false,
		'message' => strip_tags($result),
	), 500);
} catch (Throwable $e) {
	pvewhmcs_novnc_launch_response(array('success' => false, 'message' => $e->getMessage()), 500);
}

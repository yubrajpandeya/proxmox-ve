<?php

require_once dirname(__DIR__, 3) . '/init.php';
require_once __DIR__ . '/console_debug.php';

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

function pvewhmcs_novnc_params_response($payload, $statusCode = 200)
{
	http_response_code($statusCode);
	echo json_encode($payload);
	exit;
}

$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token === '' || empty($_SESSION['pvewhmcs_novnc'][$token])) {
	pvewhmcs_console_debug($token, 'params-fail', array('message' => 'Console session not found.'));
	pvewhmcs_novnc_params_response(array('success' => false, 'message' => 'Console session not found.'), 404);
}

$session = $_SESSION['pvewhmcs_novnc'][$token];
if (empty($session['expires']) || $session['expires'] < time()) {
	unset($_SESSION['pvewhmcs_novnc'][$token]);
	pvewhmcs_console_debug($token, 'params-fail', array('message' => 'Console session expired.'));
	pvewhmcs_novnc_params_response(array('success' => false, 'message' => 'Console session expired.'), 410);
}

$proxyPath = isset($session['reverseProxyPath']) ? trim($session['reverseProxyPath']) : '';
if ($proxyPath !== '') {
	$httpProxyPath = trim($proxyPath, '/');
	if (stripos($httpProxyPath, 'novnc') !== false) {
		$path = $httpProxyPath . '/' . rawurlencode($token);
		$probePath = '';
		$transport = 'node-websocket-proxy';
	} else {
		$wsProxyPath = $httpProxyPath . '-ws';
		$path = $wsProxyPath . '/' . ltrim($session['path'], '/');
		$probePath = $httpProxyPath . '/api2/json/version';
		$transport = 'reverse-proxy';
	}
} else {
	$path = 'modules/servers/pvewhmcs/novnc_ws.php?token=' . urlencode($token);
	$probePath = '';
	$transport = 'php-fallback';
}

pvewhmcs_console_debug($token, 'params-ok', array(
	'path' => $path,
	'probePath' => $probePath,
	'transport' => $transport,
));

pvewhmcs_novnc_params_response(array(
	'success' => true,
	'host' => '',
	'port' => '',
	'path' => $path,
	'probePath' => $probePath,
	'password' => isset($session['vncticket']) ? $session['vncticket'] : '',
	'transport' => $transport,
));

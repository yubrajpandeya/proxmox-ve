<?php

require_once dirname(__DIR__, 3) . '/init.php';
require_once __DIR__ . '/console_debug.php';

header('Content-Type: application/json');

$token = isset($_POST['token']) ? (string) $_POST['token'] : '';
if ($token === '' || empty($_SESSION['pvewhmcs_novnc'][$token])) {
	echo json_encode(array('success' => false));
	exit;
}

$payload = array(
	'message' => isset($_POST['message']) ? substr((string) $_POST['message'], 0, 500) : '',
	'clean' => isset($_POST['clean']) ? (string) $_POST['clean'] : '',
	'readyState' => isset($_POST['readyState']) ? (string) $_POST['readyState'] : '',
	'url' => isset($_POST['url']) ? substr((string) $_POST['url'], 0, 1200) : '',
	'path' => isset($_POST['path']) ? substr((string) $_POST['path'], 0, 1200) : '',
	'probePath' => isset($_POST['probePath']) ? substr((string) $_POST['probePath'], 0, 1200) : '',
	'status' => isset($_POST['status']) ? substr((string) $_POST['status'], 0, 60) : '',
	'ok' => isset($_POST['ok']) ? substr((string) $_POST['ok'], 0, 10) : '',
	'hasHtmlAmp' => isset($_POST['hasHtmlAmp']) ? substr((string) $_POST['hasHtmlAmp'], 0, 10) : '',
	'page' => isset($_POST['page']) ? substr((string) $_POST['page'], 0, 1200) : '',
	'error' => isset($_POST['error']) ? substr((string) $_POST['error'], 0, 500) : '',
	'userAgent' => isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 240) : '',
);

pvewhmcs_console_debug($token, 'browser-event', $payload);

echo json_encode(array('success' => true));

<?php

require_once dirname(__DIR__, 3) . '/init.php';
require_once __DIR__ . '/console_debug.php';

function pvewhmcs_ws_fail($message, $status = '403 Forbidden')
{
	$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
	pvewhmcs_console_debug($token, 'ws-fail', array('status' => $status, 'message' => $message));
	header('HTTP/1.1 ' . $status);
	header('Content-Type: text/plain');
	echo $message;
	exit;
}

$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token === '' || empty($_SESSION['pvewhmcs_novnc'][$token])) {
	pvewhmcs_ws_fail('Console session not found.');
}

$session = $_SESSION['pvewhmcs_novnc'][$token];
if (empty($session['expires']) || $session['expires'] < time()) {
	unset($_SESSION['pvewhmcs_novnc'][$token]);
	pvewhmcs_ws_fail('Console session expired.');
}

$host = $session['host'];
$port = (int) $session['port'];
$path = ltrim($session['path'], '/');
$pveticket = $session['pveticket'];
pvewhmcs_console_debug($token, 'ws-request', array(
	'host' => $host,
	'port' => $port,
	'path' => $path,
	'remoteAddr' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
	'httpHost' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null,
	'upgrade' => isset($_SERVER['HTTP_UPGRADE']) ? $_SERVER['HTTP_UPGRADE'] : null,
	'connection' => isset($_SERVER['HTTP_CONNECTION']) ? $_SERVER['HTTP_CONNECTION'] : null,
));

$headers = function_exists('getallheaders') ? getallheaders() : array();
$clientKey = '';
foreach ($headers as $name => $value) {
	if (strtolower($name) === 'sec-websocket-key') {
		$clientKey = trim($value);
		break;
	}
}

if ($clientKey === '') {
	pvewhmcs_ws_fail('Missing websocket key.', '400 Bad Request');
}

ignore_user_abort(true);
set_time_limit(0);

while (ob_get_level() > 0) {
	ob_end_clean();
}

$target = 'tls://' . $host . ':' . $port;
$errno = 0;
$errstr = '';
$upstream = stream_socket_client($target, $errno, $errstr, 15, STREAM_CLIENT_CONNECT);
if (!$upstream) {
	pvewhmcs_ws_fail('Unable to connect to Proxmox console endpoint: ' . $errstr, '502 Bad Gateway');
}
pvewhmcs_console_debug($token, 'upstream-connected', array('target' => $target));

stream_set_blocking($upstream, false);

$request = "GET /" . $path . " HTTP/1.1\r\n"
	. "Host: " . $host . ":" . $port . "\r\n"
	. "Upgrade: websocket\r\n"
	. "Connection: Upgrade\r\n"
	. "Sec-WebSocket-Key: " . $clientKey . "\r\n"
	. "Sec-WebSocket-Version: 13\r\n"
	. "Cookie: PVEAuthCookie=" . $pveticket . "\r\n"
	. "Origin: https://" . $host . ":" . $port . "\r\n"
	. "\r\n";

fwrite($upstream, $request);
pvewhmcs_console_debug($token, 'upstream-handshake-sent', array('path' => $path));

$responseHeaders = '';
$start = time();
while (strpos($responseHeaders, "\r\n\r\n") === false) {
	$chunk = fread($upstream, 8192);
	if ($chunk !== false && $chunk !== '') {
		$responseHeaders .= $chunk;
	}
	if ((time() - $start) > 15) {
		fclose($upstream);
		pvewhmcs_ws_fail('Timed out waiting for Proxmox websocket handshake.', '504 Gateway Timeout');
	}
	usleep(10000);
}

list($headerBlock, $leftover) = explode("\r\n\r\n", $responseHeaders, 2);
$headerLines = explode("\r\n", $headerBlock);
$statusLine = array_shift($headerLines);

if (strpos($statusLine, '101') === false) {
	fclose($upstream);
	pvewhmcs_console_debug($token, 'upstream-handshake-refused', array(
		'statusLine' => $statusLine,
		'headers' => $headerLines,
	));
	pvewhmcs_ws_fail('Proxmox refused websocket handshake: ' . $statusLine, '502 Bad Gateway');
}
pvewhmcs_console_debug($token, 'upstream-handshake-ok', array('statusLine' => $statusLine));

header('HTTP/1.1 101 Switching Protocols');
foreach ($headerLines as $line) {
	if (stripos($line, 'Upgrade:') === 0 || stripos($line, 'Connection:') === 0 || stripos($line, 'Sec-WebSocket-Accept:') === 0) {
		header($line, false);
	}
}
flush();

if ($leftover !== '') {
	echo $leftover;
	flush();
}

$client = fopen('php://input', 'rb');
$output = fopen('php://output', 'wb');
stream_set_blocking($client, false);

$lastActivity = time();
$upstreamBytes = 0;
$clientBytes = 0;
$loopStarted = time();
while (!feof($upstream) && !connection_aborted()) {
	$read = array($upstream, $client);
	$write = null;
	$except = null;
	$ready = @stream_select($read, $write, $except, 1);

	if ($ready === false) {
		pvewhmcs_console_debug($token, 'stream-select-failed');
		break;
	}

	foreach ($read as $stream) {
		$data = fread($stream, 8192);
		if ($data === false || $data === '') {
			continue;
		}

		$lastActivity = time();
		if ($stream === $upstream) {
			$upstreamBytes += strlen($data);
			fwrite($output, $data);
			flush();
		} else {
			$clientBytes += strlen($data);
			fwrite($upstream, $data);
		}
	}

	if ((time() - $lastActivity) > 300) {
		pvewhmcs_console_debug($token, 'idle-timeout', array('seconds' => 300));
		break;
	}
}

pvewhmcs_console_debug($token, 'ws-closed', array(
	'duration' => time() - $loopStarted,
	'upstreamBytes' => $upstreamBytes,
	'clientBytes' => $clientBytes,
	'connectionAborted' => connection_aborted(),
	'upstreamEof' => feof($upstream),
));

if ((time() - $loopStarted) <= 1 && $upstreamBytes === 0 && $clientBytes === 0 && feof($upstream)) {
	pvewhmcs_console_debug($token, 'php-websocket-tunnel-unsupported', array(
		'message' => 'Proxmox accepted the websocket handshake, but the PHP runtime closed before any browser frames were relayed. Use a web-server websocket reverse proxy for noVNC instead of the PHP fallback tunnel.',
		'httpHost' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null,
	));
}

fclose($upstream);
exit;

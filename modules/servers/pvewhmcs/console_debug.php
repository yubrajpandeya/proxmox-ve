<?php

function pvewhmcs_console_debug_file()
{
	return __DIR__ . '/console-debug.log';
}

function pvewhmcs_console_debug_id($token)
{
	return substr(hash('sha256', (string) $token), 0, 12);
}

function pvewhmcs_console_debug($token, $event, array $context = array())
{
	$context = pvewhmcs_console_debug_sanitize($context);
	$line = json_encode(array(
		'time' => gmdate('c'),
		'id' => pvewhmcs_console_debug_id($token),
		'event' => $event,
		'context' => $context,
	), JSON_UNESCAPED_SLASHES);

	@file_put_contents(pvewhmcs_console_debug_file(), $line . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function pvewhmcs_console_debug_sanitize(array $context)
{
	$blocked = array('pveticket', 'vncticket', 'ticket', 'password', 'cookie', 'sec-websocket-key');
	foreach ($context as $key => $value) {
		$lower = strtolower((string) $key);
		if ($lower === 'cookiemode') {
			if (is_string($value)) {
				$context[$key] = pvewhmcs_console_debug_sanitize_string($value);
			}
			continue;
		}
		foreach ($blocked as $blockedKey) {
			if (strpos($lower, $blockedKey) !== false) {
				$context[$key] = '[redacted]';
				continue 2;
			}
		}
		if (is_array($value)) {
			$context[$key] = pvewhmcs_console_debug_sanitize($value);
		} elseif (is_string($value)) {
			$context[$key] = pvewhmcs_console_debug_sanitize_string($value);
		}
	}
	return $context;
}

function pvewhmcs_console_debug_sanitize_string($value)
{
	$value = preg_replace('/([?&](?:vnc)?ticket=)[^&\s]+/i', '$1[redacted]', $value);
	$value = preg_replace('/(&amp;(?:vnc)?ticket=)[^&\s]+/i', '$1[redacted]', $value);
	$value = preg_replace('/([?&]token=)[^&\s]+/i', '$1[redacted]', $value);
	$value = preg_replace('/(&amp;token=)[^&\s]+/i', '$1[redacted]', $value);
	$value = preg_replace('/(PVEAuthCookie=)[^;\s]+/i', '$1[redacted]', $value);
	return $value;
}

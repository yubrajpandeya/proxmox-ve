<?php

/*  
    Bisup Proxmox VE for WHMCS - Addon/Server Modules for WHMCS (& PVE)
    Bisup white-label fork of The-Network-Crew/Proxmox-VE-for-WHMCS
    File: /modules/servers/pvewhmcs/novnc_router.php (VNC)

    Copyright (C) The Network Crew Pty Ltd (TNC) & Co.
    White-label modifications Copyright (C) Bisup.

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>. 
*/

// ---------------------------------------
// Check required GET parameters
// ---------------------------------------
require_once dirname(__DIR__, 3) . '/init.php';
require_once __DIR__ . '/console_debug.php';

if (!isset($_GET['token'])) {
    echo 'Error: Missing required info to route your request. Please try again.';
    exit;
}

// ---------------------------------------
// Assign GET parameters
// ---------------------------------------
$token      = $_GET['token'];
pvewhmcs_console_debug($token, 'router-opened', array(
    'remoteAddr' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
    'hostHeader' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null,
));

if (
    empty($_SESSION['pvewhmcs_novnc'][$token])
    || empty($_SESSION['pvewhmcs_novnc'][$token]['expires'])
    || $_SESSION['pvewhmcs_novnc'][$token]['expires'] < time()
) {
    echo 'Error: Console session expired. Please launch noVNC again.';
    exit;
}

// ---------------------------------------
// Build final noVNC URL
// ---------------------------------------
$session       = $_SESSION['pvewhmcs_novnc'][$token];
$proxy_path    = isset($session['reverseProxyPath']) ? trim($session['reverseProxyPath']) : '';

if ($proxy_path !== '') {
    $proxy_path = trim($proxy_path, '/');
    $target_path = ltrim($session['path'], '/');
    $ws_path = $proxy_path . '/' . $target_path;
    setcookie('PVEAuthCookie', '', time() - 3600, '/' . $proxy_path . '/', '', true, true);
    setcookie('PVEAuthCookie', '', time() - 3600, '/' . $proxy_path . '-ws/', '', true, true);
    setcookie('PVEAuthCookie', $session['pveticket'], time() + 120, '/', '', true, true);
    setcookie('PVEAuthCookie', $session['pveticket'], time() + 120, '/' . $proxy_path . '/', '', true, true);
    setcookie('PVEAuthCookie', $session['pveticket'], time() + 120, '/' . $proxy_path . '-ws/', '', true, true);
    $transport = 'reverse-proxy';
    $cookieMode = 'encoded-root-http-ws';
} else {
    $ws_path = 'modules/servers/pvewhmcs/novnc_ws.php?token=' . urlencode($token);
    $transport = 'php-fallback';
    $cookieMode = 'php-fallback';
}

$redirect_url  = './novnc/vnc_lite.html?autoconnect=true&encrypt=true'
               . '&debugtoken=' . urlencode($token);
pvewhmcs_console_debug($token, 'router-redirect', array(
    'wsPath' => $ws_path,
    'transport' => $transport,
    'cookieMode' => $cookieMode,
    'targetHost' => isset($session['host']) ? $session['host'] : null,
    'targetPort' => isset($session['port']) ? $session['port'] : null,
));

// ---------------------------------------
// Redirect to noVNC
// ---------------------------------------
header('Location: ' . $redirect_url);
exit;

?>

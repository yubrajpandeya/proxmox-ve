# Bisup noVNC Reverse Proxy

## Why this is required

Bisup WHMCS runs on `my.bisup.com`, while Proxmox runs on `epyc.bisuphost.com:8006`.
Proxmox noVNC requires an authenticated WebSocket connection with a `PVEAuthCookie`
and a short-lived VNC ticket. Browsers cannot use a WHMCS-created Proxmox cookie on
another parent domain, and many PHP-FPM hosting stacks cannot reliably tunnel upgraded
WebSocket traffic after returning `101 Switching Protocols`.

If `modules/servers/pvewhmcs/console-debug.log` contains:

```json
{"event":"upstream-handshake-ok","context":{"statusLine":"HTTP/1.1 101 Switching Protocols"}}
{"event":"ws-closed","context":{"duration":0,"upstreamBytes":0,"clientBytes":0}}
```

then Proxmox accepted the console connection, but the PHP runtime closed before browser
frames were relayed. Configure a web-server WebSocket reverse proxy on `my.bisup.com`.

## Nginx example

Add this to the HTTPS server block for `my.bisup.com`:

```nginx
location /bisup-proxmox/ {
    proxy_pass https://epyc.bisuphost.com:8006/;
    proxy_http_version 1.1;

    proxy_set_header Host epyc.bisuphost.com:8006;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto https;

    proxy_ssl_server_name on;
    proxy_ssl_name epyc.bisuphost.com;
    proxy_read_timeout 3600s;
    proxy_send_timeout 3600s;
    proxy_buffering off;
}
```

Reload Nginx after validating the config:

```bash
nginx -t
systemctl reload nginx
```

## Module setting

In WHMCS, edit each Proxmox product:

`Setup > Products/Services > Products/Services > Module Settings`

Set `noVNC Proxy Path` to:

```text
/bisup-proxmox/
```

Save the product. New noVNC launches will then use:

```text
wss://my.bisup.com/bisup-proxmox/api2/json/nodes/{node}/{qemu|lxc}/{vmid}/vncwebsocket
```

The router sets the short-lived `PVEAuthCookie` on `my.bisup.com` before it opens
the bundled noVNC client.

## What to test

1. Open client area service details on `my.bisup.com`.
2. Click `noVNC (HTML5)`.
3. If it fails, download `modules/servers/pvewhmcs/console-debug.log`.
4. Confirm that future logs redact `token`, `ticket`, `vncticket`, and cookies before sharing.

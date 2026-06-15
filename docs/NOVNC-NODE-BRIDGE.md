# Bisup noVNC Node Bridge

Use this when WHMCS runs on cPanel/LiteSpeed and Proxmox is on another host.
LiteSpeed proxies the browser WebSocket to a local Node service. The Node service
then connects to Proxmox with the server-side `PVEAuthCookie` and VNC ticket.

## Install

Run as root:

```bash
cd /home/mybisup/public_html/modules/servers/pvewhmcs
npm init -y
npm install ws
mkdir -p novnc_sessions
chown -R mybisup:mybisup novnc_sessions node_modules package.json package-lock.json
chmod 700 novnc_sessions
```

## systemd service

```bash
cat > /etc/systemd/system/bisup-novnc-proxy.service <<'EOF'
[Unit]
Description=Bisup WHMCS noVNC WebSocket Bridge
After=network-online.target
Wants=network-online.target

[Service]
Type=simple
User=mybisup
Group=mybisup
WorkingDirectory=/home/mybisup/public_html/modules/servers/pvewhmcs
Environment=PVEWHMCS_NOVNC_HOST=127.0.0.1
Environment=PVEWHMCS_NOVNC_PORT=8787
ExecStart=/usr/bin/node /home/mybisup/public_html/modules/servers/pvewhmcs/novnc_node_proxy.js
Restart=always
RestartSec=3

[Install]
WantedBy=multi-user.target
EOF

systemctl daemon-reload
systemctl enable --now bisup-novnc-proxy
systemctl status bisup-novnc-proxy --no-pager
```

If Node is not at `/usr/bin/node`, find it:

```bash
command -v node
```

Then update `ExecStart`.

## LiteSpeed include

```bash
cat > /etc/apache2/conf.d/userdata/ssl/2_4/mybisup/my.bisup.com/bisup-novnc.conf <<'EOF'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^/bisup-novnc/(.*)$ http://127.0.0.1:8787/$1 [P,L]
    ProxyPass "/bisup-novnc/" "ws://127.0.0.1:8787/"
</IfModule>
EOF

/scripts/verify_vhost_includes --user=mybisup
/scripts/ensure_vhost_includes --user=mybisup
/scripts/rebuildhttpdconf
/scripts/restartsrv_httpd
/usr/local/lsws/bin/lswsctrl restart
```

## WHMCS product setting

Set `noVNC Proxy Path` to:

```text
/bisup-novnc/
```

## Test

```bash
curl -s https://my.bisup.com/bisup-novnc/
systemctl status bisup-novnc-proxy --no-pager
journalctl -u bisup-novnc-proxy -n 80 --no-pager
tail -n 160 /home/mybisup/public_html/modules/servers/pvewhmcs/console-debug.log
```


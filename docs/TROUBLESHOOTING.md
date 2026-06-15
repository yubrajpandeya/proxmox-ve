# Bisup Proxmox VE for WHMCS Troubleshooting

## First Checks

- WHMCS Module Log is enabled.
- The WHMCS server uses module type `pvewhmcs`.
- The product Module Settings tab has a selected Proxmox plan and IPv4 pool.
- The product Module Settings tab was saved after selecting those values.
- The selected IPv4 pool has at least one free address.
- WHMCS can reach the Proxmox host on TCP 8006.
- Proxmox has a valid SSL certificate.

## Client Area Manage Loads Slowly

The normal Manage view should render skeleton placeholders immediately. Live Proxmox data should load afterward through `modules/servers/pvewhmcs/clientarea_data.php`.

Check:

- The URL for the normal Manage view does not include `a=vmStat`.
- Browser Network timing for `clientarea_data.php`.
- Whether `clientarea_data.php` returns JSON or an HTML/PHP error.
- WHMCS Module Log timestamps around async requests.
- Proxmox API response time for `/cluster/resources?type=vm` when `node_name` is not populated yet.
- Proxmox API response time for `/nodes/{node}/{qemu|lxc}/{vmid}/status/current`.

See `docs/CLIENT-AREA-MANAGE.md` for the expected behavior.

## Provisioning Fails

Collect:

- WHMCS service ID.
- Product ID.
- Selected plan ID and IP pool ID.
- Proxmox node name.
- Proxmox task ID if a task was created.
- WHMCS Module Log entry for the failed action.

Check:

- VMID start value in Module Config.
- Template custom fields such as `KVMTemplate` or LXC template values.
- Storage names in the plan match Proxmox exactly.
- Bridge, VLAN, CPU, memory, and disk values are accepted by the target node.

### Unable To Find A Free VMID

If Proxmox has free VMIDs but WHMCS reports:

```text
Unable to find a free VMID starting at 300 after 1000 attempts
```

deploy a build that includes the cluster-scan VMID allocator. Older builds tried to probe `/cluster/nextid` with a GET parameter through a wrapper that did not send GET query parameters, so it could loop without ever checking IDs such as `301` or `302`.

After deploying the fixed `modules/servers/pvewhmcs/pvewhmcs.php`, clear template cache and retry module create:

```bash
rm -rf /home/mybisup/public_html/templates_c/*
```

## Console Fails

Collect:

- Browser error message.
- WHMCS Module Log entry.
- Proxmox API task or console error.

Check:

- `vnc@pve` exists.
- Module Config `VNC Secret` matches the `vnc@pve` password.
- WHMCS is served over HTTPS.
- The service has a valid VMID in `mod_pvewhmcs_vms`.
- The guest still exists on the Proxmox node.
- The WHMCS server hostname is set to the Proxmox FQDN, for example `epyc.bisuphost.com`, and the port is `8006`.
- noVNC opens from `my.bisup.com` and connects through `modules/servers/pvewhmcs/novnc_ws.php`.
- Browser Network tab shows `novnc_ws.php` returning `101 Switching Protocols`.
- If `novnc_ws.php` returns `502`, verify WHMCS can reach `epyc.bisuphost.com:8006` outbound.
- If the log shows `upstream-handshake-ok` followed by `ws-closed` with `clientBytes:0` and `upstreamBytes:0`, the Proxmox side accepted the console but PHP could not tunnel the upgraded WebSocket. Use the Nginx reverse proxy in `docs/NOVNC-REVERSE-PROXY.md`.
- If WHMCS shows `Action Failed` with `Console is ready`, the click was handled by the default WHMCS module action modal. On the Manage page, the Bisup template intercepts the noVNC action and uses `modules/servers/pvewhmcs/novnc_launch.php` to open the console cleanly.

Debug log:

- Reproduce the noVNC failure once.
- Open `modules/servers/pvewhmcs/console-debug.log`.
- Share the latest lines for the same `"id"` value.
- The log redacts tickets/passwords, but still review it before sharing.

## Graphs Missing

Check:

- Guest has existed for at least 60 seconds.
- Proxmox RRD data is available on the node.
- Proxmox VE 9 RRD migration has completed after upgrades.

## Safe Debug Package

When sending an issue for fixing, include:

- Exact WHMCS action clicked.
- Expected result.
- Actual result.
- Sanitized WHMCS Module Log.
- Sanitized Proxmox task output.
- Relevant screenshots with passwords, tokens, IPs, and client data removed.

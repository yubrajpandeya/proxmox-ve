# Client Area Manage View

## Issue

The WHMCS client-area Manage view could load slowly because the module fetched all Proxmox RRD graph images during the normal page render.

That meant a standard Manage click could trigger:

- Proxmox login.
- Guest node lookup.
- Guest config lookup.
- Cluster resources lookup.
- LXC swap status lookup.
- 16 RRD graph requests for CPU, memory, network, and disk across day, week, month, and year.

The graph requests are useful only when the client clicks the Statistics action.

## Fix

The normal Manage view now renders immediately from WHMCS/database data:

- Service ID.
- Stored guest type.
- Stored IP address, subnet mask, and gateway.
- Stored Proxmox node name when available.

Live Proxmox details now load asynchronously from:

`modules/servers/pvewhmcs/clientarea_data.php`

The template shows skeleton placeholders while that endpoint fetches:

- Proxmox login.
- Guest node lookup when `node_name` is not already stored.
- Guest config.
- Guest current status.

RRD graph requests run only when WHMCS opens the Statistics action with `a=vmStat`, and those graphs also load through the async endpoint.

Guest node discovery now uses `/cluster/resources?type=vm` instead of the full cluster resource list. The resolved node is cached in the WHMCS session for five minutes per service, so repeated Manage/action clicks avoid repeated cluster scans. If the guest moves and the cached node fails, the module clears the cache and rediscovers once.

Version `1.3.6` also adds `mod_pvewhmcs_vms.node_name`. New services store the Proxmox node at provisioning time. Existing services populate this field after the next successful node discovery, then future Manage loads can skip cluster discovery entirely.

The template was also adjusted to:

- Render WHMCS action buttons with small Font Awesome 4-compatible icons instead of oversized icons or raw image labels.
- Show skeleton loading rows and gauges while live Proxmox data is fetched.
- Prevent long values such as MAC addresses, SSH keys, and IP config strings from overflowing.
- Stack the specification table cleanly on mobile.
- Use responsive grid gauges.
- Use service-specific gauge element IDs.

## Verification

1. Open a client service in WHMCS.
2. Click Manage.
3. Confirm the page renders immediately with skeleton placeholders.
4. Confirm the placeholders are replaced by live Proxmox data.
5. Confirm CPU, RAM, Disk, and Swap gauges render.
6. Confirm long networking values do not overlap or break the layout.
7. Click Statistics.
8. Confirm graph loading still works from the Statistics action.

If the initial Manage page is still slow, the delay is probably outside Proxmox API calls because `pvewhmcs_ClientArea` no longer calls Proxmox during initial render. Check WHMCS theme hooks, product detail widgets, and browser network timing for `clientarea_data.php`.

## noVNC Routing

The browser should not connect directly from `my.bisup.com` to `epyc.bisuphost.com:8006` because the Proxmox `PVEAuthCookie` cannot be set cross-domain. noVNC now connects to WHMCS first:

`my.bisup.com/modules/servers/pvewhmcs/novnc_ws.php`

That endpoint forwards the websocket to Proxmox and attaches the Proxmox cookie server-side.

# Bisup Proxmox VE for WHMCS

Bisup-branded WHMCS addon/server module for provisioning and managing Proxmox VE virtual machines and containers from WHMCS.

This repository is a white-label fork of the open-source `The-Network-Crew/Proxmox-VE-for-WHMCS` module. The WHMCS module slug remains `pvewhmcs` so existing WHMCS discovery, database tables, hooks, product configuration, and service records continue to work.

## What This Module Does

- Provisions QEMU VMs and LXC containers from WHMCS products.
- Lets WHMCS clients start, stop, reboot, shut down, and open console access.
- Maps WHMCS products to Proxmox plans and IPv4 pools.
- Imports existing Proxmox guests into WHMCS or links them to already-ordered WHMCS services.
- Shows VM/CT status and resource graphs where Proxmox RRD data is available.
- Provides WHMCS admin tabs for nodes, guests, plans, IP pools, logs, support, and module configuration.

## Requirements

- WHMCS 8.x over HTTPS.
- PHP 8.x with cURL enabled.
- Proxmox VE 8.4 or 9.x.
- WHMCS to Proxmox API connectivity on TCP 8006.
- Valid SSL certificate on each Proxmox host.
- One Proxmox API/admin user configured in WHMCS Servers.
- One restricted Proxmox VNC-only user for console proxying.

## Installation

1. Upload the `modules/addons/pvewhmcs` and `modules/servers/pvewhmcs` folders into the matching folders in the WHMCS installation.
2. In WHMCS Admin, go to `System Settings > Addon Modules`.
3. Activate `Bisup Proxmox VE for WHMCS`.
4. Grant access to the administrator roles that should manage the module.
5. In `System Settings > Servers`, add each Proxmox host with module type `pvewhmcs`.
6. Use the Proxmox host IP or hostname without a port suffix. The module defaults to Proxmox port `8006`.
7. Test the WHMCS server connection before creating products.

## Required Proxmox Users

The module uses two Proxmox access paths:

- WHMCS server credentials: used for provisioning and lifecycle actions.
- `vnc@pve`: used for noVNC/SPICE console access.

Create the VNC user in Proxmox VE, assign only the console permissions needed by the module, and set a strong password. Enter that password in:

`WHMCS Admin > Addons > Bisup Proxmox VE for WHMCS > Module Config > VNC Secret`

## Product Setup

1. Open `WHMCS Admin > Addons > Bisup Proxmox VE for WHMCS`.
2. Create at least one QEMU or LXC plan.
3. Create at least one IPv4 pool.
4. Create or edit a WHMCS product.
5. On the product Module Settings tab, choose module `pvewhmcs`.
6. Select the Bisup Proxmox plan and IPv4 pool.
7. Save the product Module Settings tab. This save step is required before provisioning.

## Console Access

The client area exposes noVNC and SPICE buttons when the service is active and the guest exists in Proxmox. Console access depends on:

- Correct `vnc@pve` password in Module Config.
- WHMCS HTTPS being valid.
- Proxmox API connectivity from WHMCS.
- Browser access to the generated console route.

## RRD Graphs

Proxmox VE 9 uses updated RRD paths. If graphs are missing after a Proxmox upgrade, verify that the Proxmox node has migrated RRD data to the current schema. Newly-created guests can also take around one minute before RRD graph data is available.

## Troubleshooting Checklist

Before escalating an issue, capture:

- WHMCS Module Log entries for `pvewhmcs`.
- WHMCS service ID and product ID.
- Proxmox node name, VMID, and task ID.
- Module version from the Support/Health tab.
- Exact action that failed, such as Create, Suspend, Terminate, noVNC, or Import Guest.

Common checks:

- Confirm the WHMCS server entry uses module type `pvewhmcs`.
- Confirm the WHMCS product Module Settings tab was saved after selecting plan and IP pool.
- Confirm at least one free IP exists in the selected IPv4 pool.
- Confirm the VMID start range does not collide with existing Proxmox guests.
- Confirm the Proxmox host certificate and firewall allow WHMCS to reach TCP 8006.

## White-label Notes

See [docs/BISUP-WHITELABEL.md](docs/BISUP-WHITELABEL.md) for the white-label scope, modified surfaces, and maintenance rules.

For already-created WHMCS services, use the link flow in [docs/LINK-EXISTING-SERVICE.md](docs/LINK-EXISTING-SERVICE.md). This preserves the original WHMCS order ID.

## License And Attribution

This fork remains licensed under GPLv3. Keep `LICENSE`, `CONTRIBUTORS.md`, and upstream copyright notices intact.

Original open-source project: `The-Network-Crew/Proxmox-VE-for-WHMCS`

Bisup white-label modifications are maintained for Bisup use.

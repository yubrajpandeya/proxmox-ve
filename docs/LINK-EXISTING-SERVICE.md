# Link an Existing WHMCS Service to a Proxmox VM

Use this when the client already has a WHMCS order/service and the VM or CT already exists in Proxmox.

This flow does not create a new service and does not change `tblhosting.orderid`.

## Admin UI

1. Open WHMCS Admin.
2. Go to `Addons` > `Bisup Proxmox VE`.
3. Open `VM Plans`.
4. Click `Link Existing Service`.
5. Select a recent Proxmox service or type the WHMCS Service ID manually.
6. Enter the Proxmox VMID.
7. Enter the Proxmox node name, for example `epyc`.
8. Select `QEMU` or `LXC`.
9. Optionally enter hostname, IPv4, subnet, and gateway.
10. Click `Link Existing Service`.

## Notes

- The service ID is `tblhosting.id`, not the WHMCS order ID.
- The module refuses to link a VMID that is already assigned to another service.
- If the selected service is already linked, tick `Replace existing link` to update the mapping.
- Tick `Also update service hostname/IP fields` only when you want the WHMCS service hostname or dedicated IP changed.

## Result

The module writes or updates the row in `mod_pvewhmcs_vms`:

```sql
id        = existing WHMCS service ID
vmid      = Proxmox VMID
vtype     = qemu or lxc
node_name = Proxmox node name
```

After linking, the client can use Manage, power actions, status, and noVNC from the existing WHMCS service.

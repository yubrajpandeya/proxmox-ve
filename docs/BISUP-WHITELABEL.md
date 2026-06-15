# Bisup White-label Notes

## Scope

This fork brands the WHMCS-facing module as `Bisup Proxmox VE for WHMCS`.

The following surfaces were white-labeled:

- WHMCS server module metadata in `modules/servers/pvewhmcs/whmcs.json`.
- Addon module display name, author, activation/deactivation messages, and description.
- WHMCS server module display name.
- Admin Support and Module Config panel copy.
- Client area template header comment.
- Project README and security reporting documentation.
- Runtime logo assets in `_images`, `modules/addons/pvewhmcs/img`, and `modules/servers/pvewhmcs/img`.

## What Must Not Be Renamed Without A Migration

Do not rename these identifiers unless a full WHMCS migration is planned:

- Module directory: `pvewhmcs`
- PHP function prefix: `pvewhmcs_`
- Database tables: `mod_pvewhmcs*`
- WHMCS server module type: `pvewhmcs`
- Session/log keys that WHMCS or existing installs already reference

Renaming those would break installed services, product module settings, database lookups, or WHMCS module discovery.

## Update Policy

The upstream GitHub update checker is disabled in this fork. Bisup should distribute approved module packages through its own release process.

When merging future upstream fixes:

1. Apply the upstream patch to a branch.
2. Search for upstream-facing names and URLs before release.
3. Re-run PHP syntax checks on changed PHP files.
4. Test activation, server connection, product provisioning, suspend/unsuspend, terminate, noVNC, and import guest.
5. Update this document if new branded surfaces are added.

## Attribution

GPLv3 and third-party notices must remain intact. Keep original copyright headers and contributor references, and add Bisup modification notes where project-owned files are changed.

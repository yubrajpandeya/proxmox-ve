# Security Policy

## Supported Versions

Only the latest Bisup-approved build of this fork is supported.

| Version | Supported |
| ------- | --------- |
| Latest Bisup build | Yes |
| Older Bisup builds | No |
| Upstream-only builds | No |

## Secure Code Contributions

Security-sensitive changes should follow:

- Principle of least privilege.
- Input validation and sanitization.
- HTTPS-only administrative and console flows.
- Compatibility with supported WHMCS and Proxmox versions.
- Robust error handling and logging.
- Secure defaults for credentials, tokens, and console routing.

Useful references:

- https://owasp.org/
- https://wiki.sei.cmu.edu/confluence/display/seccode/SEI+CERT+Coding+Standards
- https://csrc.nist.gov/Projects/ssdf

## Reporting a Vulnerability

Report vulnerabilities through Bisup's internal engineering/security process for this white-label fork.

Do not raise a public issue or share exploit details in client-facing channels where there is threat to users of the module.

Include:

- Affected module version.
- Affected WHMCS and Proxmox versions.
- Reproduction steps.
- Impact summary.
- Sanitized logs or screenshots.

## No Bounties

This fork does not define a public bounty program.

## Attribution

This fork is based on the GPLv3 open-source `The-Network-Crew/Proxmox-VE-for-WHMCS` module. Keep license and contributor notices intact when modifying or redistributing.

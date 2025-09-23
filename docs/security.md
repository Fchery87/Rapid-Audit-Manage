# Security Posture

This document outlines baseline security controls and practices to adopt and maintain.

Authentication & Authorization:
- Migrate to Symfony's authenticator-based security (password_hashers)
- Store users with hashed passwords (argon2id or bcrypt) and rehash on login if needed
- Principle of least privilege in access_control rules

Web App Protections:
- Enable CSRF protection for all forms (Symfony Forms)
- Validate and sanitize all inputs using the Validator component
- Output escape in Twig (autoescape on; avoid |raw)
- Configure security headers (CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, HSTS) via NelmioSecurityBundle or kernel response listeners

File Uploads:
- Accept only whitelisted extensions (e.g., .html for reports)
- Enforce max file size and scan if feasible
- Store under var/uploads/ with randomized names and no execute permissions
- Never use user input directly to build file paths; normalize and validate paths

Data Protection:
- Add validation constraints for PII; consider field-level encryption for sensitive data
- Log access and changes to sensitive records where appropriate

Operational Security:
- Use environment variables and Symfony secrets for production config
- Separate app secrets per environment; never commit secrets
- Rotate keys and credentials regularly; revoke on compromise

Monitoring & Incident Response:
- Integrate error tracking (e.g., Sentry) and structured logging
- Document incident response steps in runbooks

Logging & Observability Controls:
- Application events emit structured JSON logs via Monolog with dedicated `audit.log` and `monitoring.log` streams.
- Security-sensitive actions (client document handling, task acknowledgements, account maintenance, report access) are persisted to the `audit_log_entries` table through the `AuditTrailService`.
- `/healthz` exposes a JSON health probe that checks database connectivity, log writability, and secure storage capacity for monitoring systems.

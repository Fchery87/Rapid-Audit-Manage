# ADR 0001: Architecture and Modernization Strategy

Date: 2025-09-21
Status: Proposed

Context:
- The application runs on PHP 7.1 and Symfony 4.2 (EOL), with deprecated components (Swiftmailer).
- Controllers contain business logic, DB access, and filesystem handling.
- Upload and parsing flows are insecure and brittle.
- Tests and CI are minimal/absent.

Decision:
- Incrementally modernize to a layered architecture and supported platform versions.
- Establish quality gates (CI, linting, tests) before large refactors.
- Introduce services for parsing and accounts; move persistence to Doctrine repositories.
- Plan an upgrade path to PHP 8.2+ and Symfony 6.4 LTS.
- Harden security: forms with CSRF, secure file uploads, modern Security component.

Consequences:
- Short-term: additional scaffolding work; some churn in controllers and services.
- Medium-term: improved maintainability, testability, and security posture.
- Long-term: reduced operational risk and easier future upgrades.

Notes:
- Future ADRs should capture discrete decisions (e.g., storage location for uploads, entity design, parsing strategy, security headers policy).
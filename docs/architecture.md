# Architecture Overview

This document summarizes the current architecture, identified issues, and the target state with guiding principles.

Current stack:
- PHP 7.1, Symfony 4.2, Twig, Doctrine (DBAL/ORM pack)
- Controllers hold business logic, file parsing, and persistence concerns
- Static admin theme assets under public/templates/joli
- Minimal tests and no CI

Issues:
- End-of-life platform versions (PHP 7.1, Symfony 4.2)
- Tight coupling: controllers directly access DB and filesystem
- Insecure request handling (superglobals, weak upload validation)
- Incomplete/unused entities; raw SQL prevalent
- Inconsistent template/layout usage

Target architecture (incremental):
- Runtime: PHP 8.2+, Symfony 6.4 LTS (or 7.x where feasible)
- Layered design:
  - Controller: HTTP concerns only; delegates to application services
  - Application services: use-cases (e.g., ReportParserService, AccountService)
  - Domain: entities/aggregates, value objects, domain services
  - Infrastructure: Doctrine repositories, filesystem adapters, mailers
- Persistence: Doctrine entities and repositories; migrations for schema evolution
- Frontend: consistent Twig layout hierarchy; assets via Asset component or Webpack Encore
- Security: new authenticator system, CSRF on all forms, strong headers

Conventions:
- PSR-12 code style, strict_types, typed properties
- Dependency Injection for all services; avoid container-aware patterns
- DTOs for input/output across layers (no leaking HTTP layer into domain)
- Avoid raw SQL in controllers; use repositories or QueryBuilder

Near-term steps:
1) Introduce services for parsing and account management
2) Migrate controllers to use Request/Form/Validator and services
3) Add proper Doctrine entities and migrations
4) Establish CI with linting and tests
5) Plan and execute platform upgrade path
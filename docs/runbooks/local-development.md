# Local Development Runbook

This runbook captures the repeatable steps for exercising the automated checks that now protect the legacy Symfony codebase. The commands below assume PHP 8.2 with the extensions listed in `composer.json` and that Composer dependencies have been installed.

## Quality gates

Run the individual checks when iterating locally, or execute the full suite before sending a change for review.

```bash
# Lint configuration and templates
php bin/console lint:yaml config --parse-tags
php bin/console lint:container
php bin/console lint:twig templates

# Static analysis
vendor/bin/phpstan analyse --no-progress --memory-limit=1G

# Automated tests
php bin/phpunit --testdox
```

The Twig linter still emits deprecation warnings because of the legacy Symfony 4.x stack, but syntax errors now surface reliably after patching the upstream Twig ternary bug for PHP 8.

## Test suites

The PHPUnit configuration exposes three primary suites:

- **Unit** – fast tests covering isolated services such as the encryption helpers.
- **Parser** – snapshot coverage for the IdentityIQ HTML parser to detect regressions in extraction logic.
- **Functional** – HTTP-level checks using Symfony's test client to exercise controller flows like `/parse-html-raw`.

Use the `--testsuite` flag to run a subset, for example `php bin/phpunit --testsuite "Parser"`.

Fixtures that back the parser tests live under `tests/Fixtures/reports/`. They can be refreshed by re-running the snapshot generator script documented in the test source.

# Development Runbook

This guide describes the repeatable workflow for developing features or fixes on the Rapid Audit Manage
Symfony application. Steps assume you are working from the repository root on macOS, Linux, or WSL2.

## Prerequisites

* Docker Desktop 4.x or Docker Engine 24+ with Compose v2 (`docker compose version`).
* 8 GB of free RAM for the PHP, MySQL, and Mailhog containers.
* Access to the private package mirrors if your environment blocks outbound HTTPS (otherwise Composer will
  fall back to packagist.org).

Verify Docker is available:

```bash
docker version
docker compose version
```

## First-time environment bootstrap

1. Clone the repository and create a feature branch.
2. Create an `.env.local` file so you can override credentials without committing them.
   ```bash
   cat <<'ENV' > .env.local
   APP_ENV=dev
   APP_SECRET=dev-secret-change-me
   APP_DEBUG=1
   DATABASE_URL=mysql://symfony:symfony@db:3306/symfony?serverVersion=8.0
   MAILER_DSN=smtp://mailhog:1025
   ENV
   ```
3. Build the runtime images and start the stack in the background.
   ```bash
   docker compose up --build -d
   ```
4. Install PHP dependencies inside the PHP container (this updates `vendor/` on the host because the
   repository is bind-mounted).
   ```bash
   docker compose exec php composer install
   ```
5. Create the application database schema. The default credentials match the values in `.env.local`.
   ```bash
   docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
   ```
6. Visit [http://localhost:8080](http://localhost:8080) to load the UI. Mail sent by the application is
   captured in Mailhog at [http://localhost:8025](http://localhost:8025).

## Daily workflow

* Start the containers before you begin working:
  ```bash
  docker compose up -d
  ```
* Run Composer, Symfony, or PHPUnit commands inside the PHP container:
  ```bash
  docker compose exec php composer update symfony/framework-bundle
  docker compose exec php php bin/console cache:clear
  docker compose exec php php bin/phpunit --testdox
  ```
* Use the database container for ad-hoc queries:
  ```bash
  docker compose exec db mysql -usymfony -psymfony symfony
  ```
* Stop the environment when finished:
  ```bash
  docker compose down
  ```

## Quality gates before opening a pull request

Execute the same checks that CI runs:

```bash
docker compose exec php php bin/console lint:yaml config --parse-tags
docker compose exec php php bin/console lint:container
docker compose exec php php bin/console lint:twig templates
docker compose exec php vendor/bin/phpstan analyse --no-progress --memory-limit=1G
docker compose exec php php bin/phpunit --testdox
```

Static analysis and tests are configured to use the SQLite database under `var/cache/test/` so the MySQL
container is unaffected.

## Troubleshooting

* **Composer cannot reach packagist.org** – configure the `composer config -g repos.packagist composer` mirror
  to point at an internal proxy, then rerun `docker compose exec php composer install`.
* **Database migrations fail** – drop the schema with `docker compose exec db mysql -usymfony -psymfony -e 'DROP DATABASE IF EXISTS symfony; CREATE DATABASE symfony CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'` and rerun migrations.
* **File permission errors** – run `docker compose exec php chown -R www-data:www-data var` to reset cache/log
  ownership after switching between host and container tooling.
* **Reset everything** – `docker compose down --volumes --remove-orphans` removes containers and the MySQL
  data volume so you can rebuild from scratch.

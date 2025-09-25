# Deployment Runbook

Use this runbook to promote a tested build of Rapid Audit Manage to staging and production. The application
runs in Docker containers defined in `docker-compose.yml`; both environments should have the repository
cloned to `/srv/rapid-audit-manage` and Docker Engine 24+ installed.

## Roles and responsibilities

* **Release manager** – drives the checklist, coordinates approvals, and pushes tags.
* **Site reliability engineer (SRE)** – executes container builds, migrations, and smoke tests.
* **Product owner** – signs off staging and production verification.

## Pre-deployment checklist

1. CI is green for the target commit (lint, phpstan, phpunit).
2. Database migrations have been reviewed and tested locally.
3. Secrets in `/srv/rapid-audit-manage/.env.local` are up to date for each environment.
4. Planned deployment window announced in the #release channel with change summary and rollback owner.

## Build and publish the release image

1. Check out the release commit locally and tag it:
   ```bash
   git checkout main
   git pull
   export VERSION=2024.09.24-1   # increment using YYYY.MM.DD-<n>
   git tag -a "$VERSION" -m "Release $VERSION"
   git push origin "$VERSION"
   ```
2. Build the PHP runtime image and push it to GHCR (`ghcr.io/rapid-audit-manage/php`).
   ```bash
   docker buildx build --platform linux/amd64 \
     -t ghcr.io/rapid-audit-manage/php:$VERSION \
     -f docker/php/Dockerfile .
   docker push ghcr.io/rapid-audit-manage/php:$VERSION
   ```
3. Update the deployment repository reference so `APP_VERSION` resolves to the new tag during compose runs:
   ```bash
   python3 - <<'PY'
   import os, pathlib
   version = os.environ["VERSION"]
   path = pathlib.Path("ops/.env.deploy")
   lines = []
   if path.exists():
       lines = path.read_text().splitlines()
   with path.open("w") as handle:
       replaced = False
       for line in lines:
           if line.startswith("APP_VERSION="):
               handle.write(f"APP_VERSION={version}\n")
               replaced = True
           else:
               handle.write(line + "\n")
       if not replaced:
           handle.write(f"APP_VERSION={version}\n")
   PY
   git commit ops/.env.deploy -m "chore: release $VERSION"
   git push
   ```
   *(If the operations repo is private, file a change request with SRE to update it.)*

## Deploy to staging

1. SSH to the staging host and pull the latest code:
   ```bash
   ssh deploy@staging.rapid-audit.example.com
   cd /srv/rapid-audit-manage
   git fetch --tags
   git checkout "$VERSION"
   ```
2. Export the release version for Compose and pull images:
   ```bash
   export APP_VERSION=$VERSION
   docker compose pull php
   ```
3. Recreate containers with the new image (nginx/mailhog pull automatically):
   ```bash
   docker compose up -d --remove-orphans
   ```
4. Run Doctrine migrations:
   ```bash
   docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
   ```
5. Warm caches and verify assets resolve:
   ```bash
   docker compose exec php php bin/console cache:clear --env=prod
   docker compose exec php php bin/console cache:warmup --env=prod
   ```
6. Execute smoke tests from the bastion host and share results:
   ```bash
   curl -I https://staging.rapid-audit.example.com/healthz
   curl -I https://staging.rapid-audit.example.com/login
   docker compose logs web --tail=50
   ```
7. Product owner validates the UI and signs off in #release. Document the verification in the ticket.

## Promote to production

1. Confirm no new commits merged since staging sign-off (`git log $VERSION..origin/main`).
2. Repeat the staging deployment steps on `prod.rapid-audit.example.com`.
3. After migrations succeed, monitor access/error logs for five minutes:
   ```bash
   docker compose logs -f web
   ```
4. Announce completion in #release with a link to the deployed tag and smoke-test evidence.

## Rollback procedure

1. Identify the prior stable tag (for example `2024.09.10-2`).
2. SSH to the affected environment, set `APP_VERSION` to the rollback target, and redeploy:
   ```bash
   export APP_VERSION=2024.09.10-2
   git checkout "$APP_VERSION"
   docker compose pull php
   docker compose up -d --remove-orphans
   docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
   ```
3. Restore the database from the most recent snapshot if the schema is incompatible.
4. Capture a postmortem in the incident tracker and notify stakeholders once the environment is stable.

## Post-deployment tasks

* Close the release ticket with links to the staging and production confirmations.
* Rotate any short-lived secrets used during the deployment (for example temporary DB credentials).
* Schedule a retro if repeated manual steps could be automated in CI/CD.


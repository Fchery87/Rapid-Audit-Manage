# Incident Response Runbook

This runbook covers the first hour of response when Rapid Audit Manage is degraded or unavailable. All
timestamps are in UTC and communications occur in the #incident channel unless otherwise specified.

## Severity definitions

| Severity | Criteria | Initial response time |
| --- | --- | --- |
| **SEV-1** | Full outage of the client portal, security incident, or data loss | 5 minutes |
| **SEV-2** | Degraded performance, partial feature impact, or repeated job failures | 15 minutes |
| **SEV-3** | Non-urgent defects with a workaround | Next business day |

## Roles

* **Incident Commander (IC)** – coordinates response, documents timeline, ensures communication cadence.
* **Subject-Matter Expert (SME)** – troubleshoots the affected subsystem (application, database, infra).
* **Communications Lead (CL)** – posts stakeholder updates and manages client notifications if needed.

Assign IC/SME/CL explicitly at incident start. The on-call SRE defaults to IC if not otherwise designated.

## Initial triage (first 15 minutes)

1. Acknowledge the alert in PagerDuty and announce the incident in `#incident`:
   ```
   :rotating_light: SEV-1 Rapid Audit Manage outage detected at 14:32 UTC. IC: @alice, SME: @bob, CL: @carol.
   ```
2. Check container health and service availability on the affected environment:
   ```bash
   ssh deploy@prod.rapid-audit.example.com
   cd /srv/rapid-audit-manage
   docker compose ps
   docker compose logs web --tail=200
   curl -I https://prod.rapid-audit.example.com/healthz
   ```
3. Capture Doctrine and Symfony logs for the incident window:
   ```bash
   docker compose exec php tail -n 200 var/log/prod.log
   docker compose exec php tail -n 200 var/log/prod.error.log
   ```
4. If the database is suspected, verify connectivity and replication lag:
   ```bash
   docker compose exec db mysqladmin ping -psymfony -usymfony
   docker compose exec db mysql -usymfony -psymfony -e 'SHOW GLOBAL STATUS LIKE "Threads_connected";'
   ```
5. Decide whether to mitigate immediately (rollback, feature flag) or continue debugging. Record the
   decision in the incident channel.

## Mitigation playbooks

* **Restart application containers** – clears PHP opcache or stuck workers without downtime longer than a few
  seconds.
  ```bash
  docker compose restart php web
  ```
* **Rollback to prior release** – follow the [Deployment Runbook](deployment.md#rollback-procedure) using the
  previous known-good tag. Notify IC before executing.
* **Disable outbound email** – if Mailhog or SMTP is causing cascading failures, set `MAILER_DSN=null://null`
  in `.env.local`, run `docker compose up -d`, and announce the temporary change.
* **Database outage** – promote the read replica using the DBA playbook, point `DATABASE_URL` to the new
  primary, and redeploy containers.

## Communication cadence

* SEV-1 – updates every 15 minutes, including mitigation progress and ETA.
* SEV-2 – updates every 30 minutes or when state changes.
* Include: impact summary, user-visible symptoms, next actions, and blockers.
* Document all commands run and findings in the incident notes (Google Doc linked from PagerDuty).

## Resolution and postmortem

1. IC declares the incident resolved when impact stops and stability is confirmed for at least 15 minutes.
2. Capture the final timeline entry with root cause and mitigation summary.
3. File a postmortem within 48 hours including:
   * customer impact and duration
   * contributing factors
   * corrective actions with owners and due dates
4. Schedule a postmortem review in the next operations meeting.

## Useful references

* [Deployment Runbook](deployment.md)
* Grafana dashboard: `https://grafana.example.com/d/rapid-audit/manage`
* PagerDuty service: `Rapid Audit Manage` (escalation policy `Operations Primary`)


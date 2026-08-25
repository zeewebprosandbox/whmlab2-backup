# Automation, Background Workers & Cron Guide

This guide describes the unified automation stack running across **WHMLab** and **ZodPanel VPS Nodes**.

---

## 1. Unified Background Automation Command

All periodic maintenance, billing sync, service expiration, suspension, invoice generation, and account health checks are consolidated into a single high-performance Artisan command:

```bash
php artisan zod:automate-cron
```

### What this single command handles:
1. **Invoice Generation**: Checks pending recurring subscriptions and issues invoices before due dates.
2. **Payment Processing**: Executes automated recurring gateway charges where supported.
3. **Account Expiry & Suspension**:
   - Sends reminder emails 7 days, 3 days, and 1 day before expiration.
   - Automatically suspends hosting accounts on remote ZodPanel nodes via API when past due.
   - Suspends associated mail accounts and databases to conserve server resources.
4. **Remote Server Synchronization (`zod:sync-servers`)**:
   - Queries all configured nodes.
   - Syncs active account counts, resource usage (disk, bandwidth), and verifies server health.
   - Permanently purges cancelled / terminated services from remote nodes.
5. **Mail Deliverability Self-Healing (`zod:repair-mail-deliverability`)**:
   - Inspects SPF, DKIM (1024/2048-bit keys), DMARC policies, and PTR records.
   - Auto-generates missing DKIM keys and updates Exim4/Named configuration.

---

## 2. Production Crontab Setup

Add the following single entry to your server crontab (`crontab -e`):

```bash
# Run WHMLab automation every minute
* * * * * cd /home/admin/web/whmlab.zodserver.cloud/public_html && php artisan schedule:run >> /dev/null 2>&1
```

Or for standalone execution:

```bash
# Run full automation cycle every 5 minutes
*/5 * * * * cd /home/admin/web/whmlab.zodserver.cloud/public_html && php artisan zod:automate-cron >> /var/log/zod-cron.log 2>&1
```

---

## 3. Remote CLI Utilities Reference

| Utility Script | Location | Purpose |
| :--- | :--- | :--- |
| `v-zodpanel-node-app` | `/usr/local/hestia/bin/` | Manages Node.js applications and Nginx reverse proxying |
| `v-zodpanel-git-deploy` | `/usr/local/hestia/bin/` | Handles Git webhook auto-deployments and builds |
| `v-zodpanel-repair-mail-deliverability` | `/usr/local/hestia/bin/` | Repairs SPF, DKIM, DMARC, and mail DNS records |
| `v-zodpanel-snapshot` | `/usr/local/hestia/bin/` | Generates instant tar.zst snapshots of websites and DBs |
| `v-zodpanel-run-domain-command` | `/usr/local/hestia/bin/` | Executes sandboxed commands inside user home directories |
| `v-add-user-pma-temp-user` | `/usr/local/hestia/bin/` | Provisions MariaDB ephemeral user for phpMyAdmin SSO |

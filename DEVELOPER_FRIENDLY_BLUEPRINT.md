# ZodPanel Developer-Friendly & Security Architecture Blueprint

This blueprint details the architectural enhancements to make **ZodPanel** the most developer-friendly, secure, and effortless hosting panel for modern developers (Node.js, Next.js, Laravel, Python, Docker, React, Go).

---

## 🚀 1. Developer Experience (DX) Enhancements

### A. 1-Click Git Auto-Deploy (GitHub & GitLab Webhooks)
- **Zero-Downtime Deployment**: Connect GitHub/GitLab repository via Webhooks (`https://zodserver.cloud/api/whmlab/index.php?endpoint=git/webhook`).
- **Auto Build & PM2 Reload**: Automatically runs `npm install && npm run build` (or `composer install --no-dev`) on `git push main` and reloads PM2/PHP-FPM seamlessly without dropping HTTP requests.
- **Preview Deployments**: Automatic staging URLs for pull requests (e.g. `pr-12-app.zodserver.cloud`).

### B. In-Browser Web Terminal & Web SSH
- **Isolated User Terminal**: Embedded `xterm.js` web console in ZodPanel (`/web/terminal/`).
- **Strict User Sandboxing**: Confined to the user's home directory (`/home/{user}`) using `chroot` or `bwrap` (Bubblewrap) to prevent unauthorized root system access.

### C. Developer CLI Tool (`zodctl`)
- Single binary CLI for developers to manage their applications directly from their local terminal:
  ```bash
  # Example local developer workflow:
  zodctl login --token ghp_...
  zodctl deploy --domain myapp.com --env production
  zodctl logs --tail 100
  zodctl db:export --output backup.sql
  ```

---

## 🛡️ 2. Security & Guardrails (Safe Production)

### A. Resource Jailing (Linux Cgroups & Limits)
- Prevent runaway Node.js memory leaks or CPU spikes from affecting other tenants or node services:
  ```ini
  [User-Resource-Limits]
  CPU_LIMIT = 200% (2 cores max)
  RAM_LIMIT = 2048M (2GB max)
  MAX_PROCESSES = 100
  NPROC_LIMIT = 50
  ```

### B. Instant Staging Subdomains & Wildcard Auto-SSL
- Instant staging URL provided for every project: `{project}-{user}.zodserver.cloud`.
- Wildcard Let's Encrypt SSL pre-installed so devs test HTTPS APIs without SSL errors.

### C. 1-Click Snapshot & Instant Rollback
- Automatically takes a filesystem snapshot (`tar` / Btrfs / ZFS snapshot) before `git pull` or DB migration.
- **1-Click Rollback**: If a deployment breaks, revert to the previous working state in under 5 seconds.

### D. Real-Time Telemetry & Log Streamer
- Real-time SSE (Server-Sent Events) log streamer for:
  - PM2 stdout / stderr logs
  - Nginx access / error logs
  - PHP-FPM error logs
  - Database slow query logs

---

## 📊 3. Feature Comparison Matrix

| Feature | Generic cPanel / WHM | Traditional HestiaCP | **ZodPanel Developer Edition** |
| :--- | :--- | :--- | :--- |
| **Node.js / Next.js Hosting** | Complex Passenger setup | Manual CLI proxy | **1-Click Auto PM2 + Nginx Proxy** |
| **Git Push Auto-Deploy** | Manual cPanel Git | None | **Native GitHub / GitLab Webhook** |
| **In-Browser Terminal** | Full shell (risky) | None | **Sandboxed In-Browser Web Terminal** |
| **Developer CLI (`zodctl`)** | None | None | **Native Developer CLI Binary** |
| **Auto-SSL Guarantee** | Delayed AutoSSL | Manual LE | **Automated Systemd Wildcard SSL** |
| **Instant Rollbacks** | Full backup restore | Manual file copy | **5-Second Snapshot Rollback** |

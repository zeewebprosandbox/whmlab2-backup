# ZodHost & ZodPanel Platform (ZodHostVpS)

> **Next-Generation Cloud Web Hosting, VPS Management & Billing Automation Suite**  
> Built for High Performance, Resiliency, Multi-PHP, Node.js Apps, Git Auto-Deploy, Single Sign-On (phpMyAdmin & Roundcube), and Automated Lifecycle Management.

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Platform](https://img.shields.io/badge/platform-Ubuntu%2024.04%20LTS-orange.svg)](https://ubuntu.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%20%7C%208.3%20%7C%208.4%20%7C%208.5-purple.svg)](https://php.net)
[![Node.js](https://img.shields.io/badge/Node.js-18%20%7C%2020%20%7C%2022-green.svg)](https://nodejs.org)
[![HestiaCP](https://img.shields.io/badge/core-HestiaCP%201.8+-red.svg)](https://hestiacp.com)

---

## 📑 Table of Contents

- [Overview & Architecture](#-overview--architecture)
- [Key Features](#-key-features)
- [Repository Structure](#-repository-structure)
- [Quick Start & Automated Node Installation](#-quick-start--automated-node-installation)
- [Client Area & Billing Engine (WHMLab)](#-client-area--billing-engine-whmlab)
- [Single Sign-On (SSO) Engines](#-single-sign-on-sso-engines)
  - [phpMyAdmin Multi-Database SSO](#phpmyadmin-multi-database-sso)
  - [Roundcube Webmail SSO (Dovecot Master Auth)](#roundcube-webmail-sso-dovecot-master-auth)
- [Node.js Engine & Git Push-to-Deploy](#-nodejs-engine--git-push-to-deploy)
- [Unified Cron Automation](#-unified-cron-automation)
- [Documentation Index](#-documentation-index)
- [Security & Hardening](#-security--hardening)
- [License](#-license)

---

## 🏛 Overview & Architecture

**ZodHostVpS** is a unified, full-stack hosting platform engineered to provide a self-hosted alternative to cPanel/WHM with modern web technologies. It bridges two integrated subsystems:

1. **ZodPanel VPS Node Engine (`/vps`)**:
   - Hardened Linux hosting server stack running on **Ubuntu 24.04 LTS**.
   - Dual-engine web server: **Nginx** (Reverse Proxy / SSL Termination / HTTP/2) + **Apache2** / **PHP-FPM** backend.
   - Multi-PHP manager supporting concurrent PHP versions from **5.6 up to 8.5**.
   - **Dovecot IMAP/POP3** with Master User authentication & **Exim4** with automatic SPF/DKIM/DMARC deliverability repair.
   - Isolated **Node.js App Engine** with Systemd process supervisors and automatic Nginx reverse proxying.
   - **Git Auto-Deploy Webhook Engine** with dedicated SSH deploy key management.

2. **WHMLab Client Area & Billing Suite (`/app`, `/resources`, `/frontend`)**:
   - Modern Laravel backend & React/Next.js frontend dashboard.
   - Automated hosting account provisioning, upgrade/downgrade, and termination.
   - Automated invoice generation, payment gateways, and recurring subscriptions.
   - Single-entrypoint cron orchestrator (`php artisan zod:automate-cron`) for automated expiration warnings, service suspensions, server resource sync, and email deliverability health checks.

```
+-------------------------------------------------------------------------+
|                         WHMLab Client Area / Billing                    |
|                (Laravel API + Next.js Frontend Dashboard)               |
+-------------------------------------------------------------------------+
                                    │
                                    │ REST API / SSH Automation
                                    ▼
+-------------------------------------------------------------------------+
|                       ZodPanel Host Node (VPS)                          |
|  ┌──────────────────────┐  ┌─────────────────────┐  ┌────────────────┐  |
|  | Nginx Reverse Proxy  |  | phpMyAdmin SSO      |  | Roundcube SSO  |  |
|  | (Port 80/443/HTTP2)  |  | (hestia-sso.php)    |  | (Dovecot Mast) |  |
|  └──────────┬───────────┘  └─────────────────────┘  └────────────────┘  |
|             │                                                           |
|             ├───────────────┬───────────────┬───────────────┐           |
|             ▼               ▼               ▼               ▼           |
|       ┌───────────┐   ┌───────────┐   ┌───────────┐   ┌───────────┐     |
|       | Apache2 / |   | Multi-PHP |   | Node.js   |   | MariaDB   |     |
|       | FastCGI   |   | 5.6 - 8.5 |   | Apps      |   | Database  |     |
|       └───────────┘   └───────────┘   └───────────┘   └───────────┘     |
+-------------------------------------------------------------------------+
```

---

## ✨ Key Features

- **Seamless Single Sign-On (SSO)**:
  - Open phpMyAdmin for any database or all databases with a single click.
  - Open Roundcube Webmail instantly using cryptographic HMAC-SHA256 tokens and Dovecot Master User authentication.
- **Node.js Application Manager**:
  - Deploy Next.js, Express, Nest.js, Remix, and Nuxt apps alongside PHP sites.
  - Automatic port allocation, Systemd service generation, environment variable management, and Nginx proxy routing.
- **Git Push-to-Deploy**:
  - Deploy private or public repositories with webhook listeners and automated build pipelines (`composer install`, `npm run build`, migrations).
- **Automated Mail Deliverability**:
  - Automatic 2048-bit DKIM key generation, SPF record alignment, DMARC policy enforcement, and Exim4 mail server tuning.
- **Zero-Friction Node Setup**:
  - Single-script automated setup (`vps/install_vps_node.sh`) that provisions a complete production node in minutes.

---

## 📂 Repository Structure

```
.
├── vps/                               # VPS Node Engine & Server Files
│   ├── install_vps_node.sh            # Automated 1-Click VPS Node Installer
│   ├── usr_local_hestia_bin/          # Custom CLI binaries (v-zodpanel-*)
│   ├── web/                           # Custom ZodPanel Web GUI modules & templates
│   ├── roundcube_plugins/             # Roundcube Webmail SSO plugin (zodpanel_sso)
│   ├── phpmyadmin/                    # phpMyAdmin multi-database SSO engine (hestia-sso.php)
│   ├── etc_whmpanel/                  # Configuration & Master SSO environment files
│   ├── etc_nginx_custom/              # Nginx templates (phpmyadmin.inc, cloudflare.inc)
│   └── dovecot_configs/               # Dovecot master user authentication configs
│
├── app/                               # Laravel WHMLab Backend Core
│   ├── Console/Commands/              # Automation commands (AutomateCronCommand, etc.)
│   ├── Http/Controllers/              # Billing, provisioning, and API controllers
│   ├── Models/                        # Eloquent models (User, Service, Server, Invoice)
│   └── Services/                      # Server provisioning & remote synchronization
│
├── frontend/                          # Next.js Modern Client Area & Dashboard
│   ├── src/app/                       # App Router pages (Services, Billing, Domains, etc.)
│   └── src/components/                # Reusable UI components
│
├── resources/                         # Blade templates and legacy client views
├── routes/                            # Web, Admin, and API routes
├── scratch/                           # Deployment, migration, and testing utilities
│
├── README.md                          # Master Project Guide
├── VPS_INSTALLATION_GUIDE.md          # Step-by-step VPS Node Deployment Guide
├── SSO_AUTHENTICATION_GUIDE.md        # Technical Guide for phpMyAdmin & Webmail SSO
├── NODEJS_AND_GIT_ENGINE_GUIDE.md     # Node.js process and Git deployment documentation
├── AUTOMATION_AND_CRON_GUIDE.md       # Background cron jobs & automation documentation
├── DNS_DOMAIN_RECORDS_GUIDE.md        # DNS Records and Zone Management Guide
└── DEVELOPER_FRIENDLY_BLUEPRINT.md    # Developer Blueprint & Architecture Reference
```

---

## 🚀 Quick Start & Automated Node Installation

### 1. Provision a New VPS Node

On a fresh Ubuntu 24.04 LTS instance:

```bash
# 1. Clone repository
git clone https://github.com/zeewebprosandbox/ZodHostVpS.git /opt/zodhost-vps
cd /opt/zodhost-vps/vps

# 2. Make installer executable and run
chmod +x install_vps_node.sh
./install_vps_node.sh
```

### 2. Configure Hostname & SSL

```bash
# Set FQDN
v-change-sys-hostname zodpanel.zodserver.cloud

# Issue Let's Encrypt SSL for the control panel
v-add-letsencrypt-host
```

---

## 💼 Client Area & Billing Engine (WHMLab)

### Local / Production WHMLab Setup

```bash
# 1. Install PHP dependencies
composer install --no-dev --optimize-autoloader

# 2. Configure Environment
cp .env.example .env
php artisan key:generate

# 3. Run database migrations
php artisan migrate --force

# 4. Install & Build Frontend
npm install
npm run build
```

---

## 🔑 Single Sign-On (SSO) Engines

### phpMyAdmin Multi-Database SSO
- **Handler**: `/open/phpmyadmin/index.php`
- **Target**: `/usr/share/phpmyadmin/hestia-sso.php`
- Authenticates panel users across all owned databases without entering database passwords.

### Roundcube Webmail SSO (Dovecot Master Auth)
- **Plugin**: `/var/lib/roundcube/plugins/zodpanel_sso/zodpanel_sso.php`
- **Mechanism**: Dovecot master authentication (`mailbox@domain.com*masteruser`) with HMAC-SHA256 timestamped signatures.
- Automatically creates Roundcube sessions and redirects users straight into their inbox.

---

## ⚡ Node.js Engine & Git Push-to-Deploy

### Deploying a Node.js App via CLI

```bash
v-zodpanel-node-app zodhost zodhost.com create 3000 /home/zodhost/web/zodhost.com/public_html "npm run start" 20
```

### Configuring Git Auto-Deploy

```bash
v-zodpanel-git-deploy zodhost zodhost.com \
    "git@github.com:myorg/app.git" \
    "main" \
    "npm install && npm run build && v-zodpanel-node-app zodhost zodhost.com restart"
```

---

## ⏰ Unified Cron Automation

Consolidate all background hosting workers into a single cron job:

```bash
* * * * * cd /path/to/whmlab && php artisan schedule:run >> /dev/null 2>&1
```

Or trigger manual execution:

```bash
php artisan zod:automate-cron
```

---

## 📚 Documentation Index

| Guide | Description |
| :--- | :--- |
| [VPS Installation Guide](VPS_INSTALLATION_GUIDE.md) | Full guide for provisioning and hardening Ubuntu 24.04 VPS nodes |
| [SSO Authentication Guide](SSO_AUTHENTICATION_GUIDE.md) | Technical architecture of phpMyAdmin and Roundcube Webmail SSO |
| [Node.js & Git Engine Guide](NODEJS_AND_GIT_ENGINE_GUIDE.md) | Node.js lifecycle manager and Git auto-deploy webhooks |
| [Automation & Cron Guide](AUTOMATION_AND_CRON_GUIDE.md) | WHMLab background worker and cron automation reference |
| [DNS & Domain Guide](DNS_DOMAIN_RECORDS_GUIDE.md) | Complete DNS zones and deliverability reference |
| [Developer Blueprint](DEVELOPER_FRIENDLY_BLUEPRINT.md) | Architectural specification and development guidelines |

---

## 🔒 Security & Hardening

- **Firewall**: Managed via `ufw` and `fail2ban` with jail rules for SSH, Web, Mail, and Control Panel brute-force attempts.
- **Isolated User System**: Each web application and Node.js process runs under its isolated POSIX user account.
- **Session Tokens**: All internal API and SSO endpoints enforce time-bound HMAC signatures and CSRF tokens.

---

## 📄 License

This software is released under the **MIT License**.

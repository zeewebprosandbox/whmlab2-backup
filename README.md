# WHM/cPanel Billing Platform — Next.js 14 & Laravel 11 Workspace

A high-performance, modern WHM/cPanel Billing & Server Management Platform featuring an ultra-clean **Next.js 14+ (App Router)** client frontend with a **shadcn/ui** design system, paired with a robust **Laravel** backend and WHMPanel control layer.

---

## 🚀 Key Features & UI Surface

- **Aesthetic Direction**: "Orbital Minimalism" — Dark-mode Linear meets Vercel meets Stripe aesthetic (`#09090b` zinc canvas, `#18181b` card surfaces, subtle `border-zinc-800` structural borders, electric indigo `#6366F1` & cyan `#22D3EE` accents).
- **Dual Typography**: **Inter** for clean technical reading and **JetBrains Mono** for IPs, server specs, DNS records, and terminal logs.
- **Server Health Telemetry**: Live CPU load, RAM usage, and NVMe disk gauges with cluster status badges.
- **cPanel Replacement Console (`/services/[id]`)**:
  1. **Overview**: Resource usage circular progress gauges & Quick Utilities grid (phpMyAdmin, Mailboxes, SSL, Backups, Subdomains).
  2. **Files & Databases**: MySQL database management table.
  3. **Email**: Mailbox list with quota usage bars and "Create Account" modal with password generator.
  4. **Domains & DNS**: Inline DNS Zone editor table (A, CNAME, MX records).
  5. **Security**: Let's Encrypt Wildcard AutoSSL status and one-click installer.
  6. **Advanced**: PHP Version selector (PHP 8.2, 8.1, 8.0) and php.ini settings.
- **Domain Portfolio (`/domains`)**: Domain table with auto-renew switches, WHOIS privacy toggle, and bulk nameserver toolbar.
- **Financial Core (`/billing`)**: Credit balance card, spending analytics, filterable invoice list, and clean receipt view.
- **Support Center (`/support`)**: Support tickets center with priority badges (High/Low) and 6-digit Support PIN verification.
- **Account & Settings (`/settings`)**: Profile avatar, API key manager table, 2FA setup, and granular notification matrix.

---

## 📦 Directory Structure

```text
whmlab2.0/
├── frontend/                 # Next.js 14+ (App Router) + shadcn/ui Frontend
│   ├── src/
│   │   ├── app/              # App Router routes (/dashboard, /services, /domains, /billing, /support, /settings, /login)
│   │   ├── components/       # shadcn/ui primitives & layout components (sidebar, header)
│   │   └── lib/              # Utility helpers (cn class merger)
│   ├── package.json
│   └── next.config.ts
├── app/                      # Laravel Controllers, Models, Middleware & Services
├── config/                   # Laravel Configurations
├── database/                 # Migrations & Seeders
├── public/                   # Web Server Document Root
├── resources/                # Blade Templates & Tailwind CSS Design System Core
└── routes/                   # Web, User & API Route Definitions
```

---

## ⚡ Quick Start: Running the Platform

### 1. Start Next.js Frontend (Port 3000)

```bash
# Navigate to the Next.js frontend directory
cd frontend

# Install UI dependencies (if not already installed)
npm install

# Run Next.js in Development Mode
npm run dev
```

Open your browser at **`http://localhost:3000`** to view the clean Next.js + shadcn/ui frontend.

#### Building Frontend for Production

```bash
# Inside the frontend directory
cd frontend

# Run optimized production build
npm run build

# Start production server
npm start
```

---

### 2. Start Laravel Backend & WHMPanel (Port 8000)

```bash
# From the project root
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Start Laravel development server
php artisan serve
```

The backend API and admin control panel will be available at **`http://127.0.0.1:8000`** (or `/admin`).

---

## 🗺️ Next.js App Router Map

| Route | Description | Component Surface |
| :--- | :--- | :--- |
| `/dashboard` | Main Client Console | Telemetry cluster meters, active instances, balance snapshot, support PIN |
| `/services` | Hosting Services | Grid of running cPanel, WordPress & VPS instances |
| `/services/[id]` | cPanel Replacement | 6-tab console (Overview, Files & DB, Email, DNS Zone Editor, SSL, Advanced) |
| `/domains` | Domain Portfolio | Domains table, auto-renew switches, bulk nameserver toolbar |
| `/billing` | Financial Core | Credit balance, deposit funds, itemized invoice list & receipt view |
| `/support` | Support Center | Ticket threads, priority dot badges, create ticket trigger |
| `/settings` | Account & Security | Profile details, API tokens table, 2FA QR code modal, notification matrix |
| `/login` | Auth Layer | Split-screen layout with animated mesh background & SSL trust footer |

---

## ⚙️ Control Panel Subdomain Routing (Optional)

To serve WHMPanel from a dedicated subdomain such as `panel.example.com`:

```env
APP_URL=https://example.com
WHMPANEL_DOMAIN=panel.example.com
SESSION_DOMAIN=.example.com
```

Clear cached configuration:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
```

---

## 🔒 Private GitHub Authentication & Domain DNS Setup

### Keeping GitHub Repositories 100% Private

Your ZodPanel repositories (`whmlab2-backup` and `zodpanel-hestia-custom-backup`) can remain **100% PRIVATE**. When adding a new VPS via 1-Click Auto-Merge, WHMLab authenticates with GitHub using a **GitHub Personal Access Token (PAT)**.

1. **Add Token to `.env`**:
   ```env
   ZODPANEL_GITHUB_TOKEN=ghp_abcdefghijklmnopqrstuvwxyz0123456789
   ZODPANEL_BACKUP_REPO=https://github.com/zeewebprosandbox/zodpanel-hestia-custom-backup
   WHMLAB_BACKUP_REPO=https://github.com/zeewebprosandbox/whmlab2-backup
   ```
2. **Automated Node Deployment**:
   During 1-Click VPS Auto-Merge, WHMLab uses `https://{TOKEN}@github.com/...` to clone and update the latest ZodPanel release directly onto the node without exposing public access.

---

## 🌐 Full Domain DNS Records & Registrar Setup

For full technical specifications on all DNS records (A, NS, CNAME, MX, SPF, DKIM, DMARC, SRV) and step-by-step registrar glue record guides for Cloudflare, Namecheap, GoDaddy, NameSilo, etc., see:

👉 **[Complete Domain DNS Records & Registrar Guide](DNS_DOMAIN_RECORDS_GUIDE.md)**

---

## 🛡️ License & Credits

Built for high-performance server administration & hosting billing. Built with Next.js 14, shadcn/ui, Tailwind CSS, Lucide Icons, and Laravel.

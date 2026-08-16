# WHMLab

WHMLab is a Laravel-based hosting billing and management system. The public website, client area, cart, invoices, support, and admin control panel are served by the same app, with optional subdomain routing for the control panel.

## Requirements

- PHP 8.3 or newer
- Composer
- MySQL or MariaDB
- A web server pointing to the `public` directory

## Local Setup

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Open the app at:

```text
http://127.0.0.1:8000
```

## Control Panel Subdomain

By default, the admin/control panel is available at:

```text
/admin
```

To serve it from a subdomain such as `panel.example.com`, set:

```env
APP_URL=https://example.com
WHMPANEL_DOMAIN=panel.example.com
SESSION_DOMAIN=.example.com
```

Then refresh cached configuration and routes:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

When `WHMPANEL_DOMAIN` is set:

- `https://panel.example.com` serves the WHMPanel/admin routes.
- `https://example.com/admin` redirects to the configured panel subdomain.
- Leaving `WHMPANEL_DOMAIN` empty keeps the panel on `/admin`.

## DNS

Create an `A` or `CNAME` record for the control panel hostname:

```text
panel.example.com -> your server IP
```

For local testing, add a hosts entry:

```text
127.0.0.1 panel.whmlab.test
127.0.0.1 whmlab.test
```

Then use:

```env
APP_URL=http://whmlab.test
WHMPANEL_DOMAIN=panel.whmlab.test
SESSION_DOMAIN=.whmlab.test
```

## Web Server

Both the main domain and panel subdomain should point to the same Laravel `public` directory.

Example Nginx server block:

```nginx
server {
    listen 80;
    server_name example.com panel.example.com;
    root /path/to/whmlab2.0/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
    }
}
```

## Important Notes

- The `public` folder must be the web root.
- Use HTTPS in production for both the main domain and panel subdomain.
- If users need to stay logged in across the main app and panel subdomain, set `SESSION_DOMAIN` to the root domain with a leading dot, for example `.example.com`.
- Do not commit `.env` because it contains database credentials and app secrets.

## Local WHMPanel Test Harness

WHMLab includes a local WHMPanel foundation for development. It is not the full system daemon yet, but it provides the same integration surface that the future HestiaCP-derived node will use.

Open the local panel:

```text
http://127.0.0.1:8000/whmpanel
```

Useful API endpoints:

```text
GET  /whmpanel/api/v1/server/info
GET  /whmpanel/api/v1/server/stats
GET  /whmpanel/api/v1/users
POST /whmpanel/api/v1/users
GET  /whmpanel/api/v1/websites
POST /whmpanel/api/v1/auth/sso
```

Example user provisioning request:

```bash
curl -X POST http://127.0.0.1:8000/whmpanel/api/v1/users \
  -H "Accept: application/json" \
  -d "username=demo" \
  -d "email=demo@example.com" \
  -d "package=starter" \
  -d "domain=demo.test"
```

To require API auth locally, set:

```env
WHMPANEL_API_TOKEN=change-this-token
```

Then send requests with:

```text
Authorization: Bearer change-this-token
```

Billing automation can use WHMPanel by creating a server group with type `WHMPanel` in the admin area, then assigning products to that server group.

## HestiaCP-Derived WHMPanel Node

The HestiaCP fork scaffold lives in:

```text
whmpanel/
```

Important paths:

```text
whmpanel/upstream      HestiaCP source checkout on branch whmlab-main
whmpanel/bin           WHMPanel install/sync/overlay scripts
whmpanel/config        Node environment example
whmpanel/fork.json     Fork metadata
```

Install a WHMPanel node on a clean Ubuntu/Debian server:

```bash
sudo WHMPANEL_ADMIN_EMAIL=admin@example.com \
  WHMPANEL_ADMIN_PASSWORD='change-me-fast' \
  WHMPANEL_HOSTNAME=panel.example.com \
  WHMPANEL_MASTER_URL=https://example.com \
  WHMPANEL_NODE_TOKEN='shared-node-token' \
  ./whmpanel/bin/install-node.sh
```

After install, set the same shared token in the WHMLab admin server record as the WHMPanel `API Token`.

Bridge endpoints exposed by the node:

```text
GET  /api/whmlab/index.php?endpoint=server/info
GET  /api/whmlab/index.php?endpoint=server/stats
POST /api/whmlab/index.php?endpoint=users
GET  /api/whmlab/index.php?endpoint=users/{username}
POST /api/whmlab/index.php?endpoint=users/{username}/suspend
POST /api/whmlab/index.php?endpoint=users/{username}/unsuspend
```

WHMLab uses this bridge automatically when a `WHMPanel` server has an API token configured. Without a token, it uses the local simulator for development.

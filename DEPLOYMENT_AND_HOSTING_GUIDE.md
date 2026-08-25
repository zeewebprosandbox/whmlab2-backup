# ZodHost & WHMLab 2.0 — Production Hosting & Deployment Guide

This guide details the complete server architecture, installation steps, Nginx configuration, background automation crons, and remote ZodPanel node integration.

---

## 1. System & Server Requirements

| Component | Minimum Specification | Recommended |
|---|---|---|
| **Operating System** | Ubuntu 22.04 LTS / Debian 12 | Ubuntu 24.04 LTS |
| **PHP Version** | PHP 8.2.x | PHP 8.3.x |
| **Web Server** | Nginx 1.20+ | Nginx (Latest Stable) |
| **Database** | MySQL 8.0+ / MariaDB 10.6+ | MariaDB 10.11 LTS |
| **RAM / Memory** | 2 GB | 4 GB+ |
| **Storage** | 20 GB NVMe | 50 GB+ NVMe SSD |

### Required PHP Extensions:
```bash
sudo apt update && sudo apt install -y \
    php8.2-fpm php8.2-cli php8.2-mysql php8.2-curl php8.2-gd php8.2-mbstring \
    php8.2-xml php8.2-zip php8.2-bcmath php8.2-intl php8.2-soap php8.2-sodium \
    php8.2-readline unzip curl git
```

---

## 2. Directory Structure & File Permissions

Assuming the application is placed in `/var/www/zodhost`:

```bash
# Set ownership to the web server user
sudo chown -R www-data:www-data /var/www/zodhost

# Set directory and file permissions
sudo find /var/www/zodhost -type d -exec chmod 755 {} \;
sudo find /var/www/zodhost -type f -exec chmod 644 {} \;

# Grant writable permissions to storage and cache
sudo chmod -R 775 /var/www/zodhost/storage /var/www/zodhost/bootstrap/cache
sudo chown -R www-data:www-data /var/www/zodhost/storage /var/www/zodhost/bootstrap/cache

# Create storage symlink
cd /var/www/zodhost && php artisan storage:link
```

---

## 3. Environment Configuration (`.env`)

Create your `.env` file from `.env.example`:

```env
APP_NAME="ZodHost"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_APP_KEY
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=whmlab_db
DB_USERNAME=whmlab_user
DB_PASSWORD=YOUR_STRONG_DB_PASSWORD

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Apple Mail Inspired Email Delivery (SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="support@zodhost.com"
MAIL_FROM_NAME="ZodHost Cloud"

# Real-Time Telegram Channel Notifications
TELEGRAM_BOT_TOKEN="YOUR_TELEGRAM_BOT_TOKEN"
TELEGRAM_CHAT_ID="@your_channel_or_chat_id"
```

---

## 4. Production Deployment & Database Seeding

Run the following commands during deployment:

```bash
cd /var/www/zodhost

# 1. Install Composer dependencies
composer install --no-dev --optimize-autoloader

# 2. Generate application encryption key (if new installation)
php artisan key:generate --force

# 3. Execute database migrations
php artisan migrate --force

# 4. Seed 106 High-Authority SEO Education Guides
php artisan zodpanel:seed-education-seo --force

# 5. Seed Apple Mail HTML Email Templates
php artisan zodpanel:seed-apple-emails

# 6. Optimize and cache routes, configs, and views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Run test automation cron to verify all pipelines
php artisan zodpanel:cron-run
```

---

## 5. Complete Unified Automation & Crontab Setup

The application features a **unified automation pipeline** (`php artisan zodpanel:cron-run`) that automatically handles:
1. **Invoice Generation**: Automated invoice creation before renewal due dates.
2. **Multi-Stage Expiry Reminders**: Customer notifications & Telegram alerts at **7 days**, **3 days**, **1 day**, and **0 days (due date)** before expiration.
3. **Unpaid Invoice Reminders**: Scheduled reminders for pending bills.
4. **1st, 2nd, 3rd Overdue Notices**: Escalating overdue notices.
5. **Automated Server Suspension**: Remote suspension on ZodPanel/Hestia nodes for accounts overdue by 1+ days with unpaid invoices.
6. **Late Fee Calculations**: Automatic late fee additions after grace period.
7. **Abandoned Cart Purge**: Removal of stale unauthenticated shopping carts.
8. **DNS Zone Sync**: Automatic enforcement of Anycast default DNS records.
9. **Server Counter Synchronization**: Accurate active account count tracking.

### Crontab Setup (Recommended):

Edit the server's crontab using:
```bash
sudo crontab -u www-data -e
```

Add the standard Laravel scheduler entry:
```cron
# Run Laravel automation scheduler every minute
* * * * * cd /var/www/zodhost && php artisan schedule:run >> /dev/null 2>&1
```

### Direct Cron Alternative:
If running outside Laravel schedule runner, add:
```cron
# Execute unified ZodPanel automation pipeline every 5 minutes
*/5 * * * * /usr/bin/php /var/www/zodhost/artisan zodpanel:cron-run --silent >> /var/www/zodhost/storage/logs/cron.log 2>&1
```

### Webhook / URL Alternative:
If using an external uptime monitor or cPanel URL cron:
```cron
*/5 * * * * /usr/bin/curl -s "https://yourdomain.com/cron" > /dev/null 2>&1
```

---

## 6. Nginx Virtual Host Configuration

Create `/etc/nginx/sites-available/zodhost.conf`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;

    root /var/www/zodhost/public;
    index index.php index.html;

    # SSL Certificates (Let's Encrypt / Certbot)
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy "no-referrer-when-downgrade";

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied any;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml image/svg+xml;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_buffer_size 16k;
        fastcgi_buffers 4 16k;
    }

    # Deny access to hidden files (.env, .git, etc.)
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|svg)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }
}
```

Enable site and restart Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/zodhost.conf /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

---

## 7. Connecting ZodPanel Node Server

In **WHMLab Admin Panel** &rarr; **Server Management** &rarr; **Servers**:
- **Server Name**: `ZodPanel Main Node`
- **Host / IP**: `169.58.176.53`
- **Server Group**: Type `ZodPanel (WHMPanel / Hestia API)`
- **Port**: `8083`
- **Control Panel SSO URL**: `https://zodpanel.zodserver.cloud:8083`
- **Nameservers**: `ns1.zodserver.cloud`, `ns2.zodserver.cloud`

---

## 8. Telegram Bot Verification

1. Navigate to **Admin &rarr; General Settings**.
2. Fill in **Telegram Bot Token** and **Telegram Channel/Chat ID**.
3. Toggle **Telegram Notifications** to **Active**.
4. Click **Send Test Alert to Channel** or execute from terminal:
   ```bash
   php artisan zodpanel:test-telegram
   ```

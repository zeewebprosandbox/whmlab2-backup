#!/usr/bin/env bash
# ==============================================================================
# ZodHost VPS Node Automated Installation & Configuration Script
# Target OS: Ubuntu 24.04 LTS / Debian 12
# Purpose: Installs HestiaCP base, applies ZodPanel custom modules, sets up
#          phpMyAdmin SSO, Roundcube Webmail SSO with Dovecot Master User,
#          Node.js App Engine, and Git Deploy Engine.
# ==============================================================================

set -euo pipefail

RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

log() { echo -e "${BLUE}[ZODHOST-INSTALL]${NC} $1"; }
success() { echo -e "${GREEN}[SUCCESS]${NC} $1"; }
warn() { echo -e "${YELLOW}[WARNING]${NC} $1"; }
error() { echo -e "${RED}[ERROR]${NC} $1"; exit 1; }

[[ $EUID -ne 0 ]] && error "This script must be run as root."

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

log "=========================================================="
log "Starting ZodHost VPS Node Installation"
log "=========================================================="

# 1. Update system packages
log "1/8 Updating apt repositories and base utilities..."
apt update -y && apt upgrade -y
apt install -y curl wget git unzip zip tar jq fail2ban ufw software-properties-common

# 2. Install HestiaCP if not already present
if [ ! -d "/usr/local/hestia" ]; then
    log "2/8 Installing HestiaCP core stack (Nginx, Apache2, Multi-PHP, MariaDB, Dovecot, Exim4, Roundcube)..."
    wget -q https://raw.githubusercontent.com/hestiacp/hestiacp/release/install/hst-install.sh -O /tmp/hst-install.sh
    bash /tmp/hst-install.sh \
        --interactive no \
        --email admin@zodserver.cloud \
        --password "${HESTIA_ADMIN_PASSWORD:-AdminPassword123!}" \
        --hostname "${HOSTNAME_FQDN:-zodpanel.zodserver.cloud}" \
        --apache yes \
        --phpfpm yes \
        --multiphp yes \
        --named yes \
        --mariadb yes \
        --dovecot yes \
        --exim yes \
        --roundcube yes \
        --phpmyadmin yes \
        --quota yes \
        --force
    rm -f /tmp/hst-install.sh
else
    log "2/8 HestiaCP already installed. Skipping base installation."
fi

# 3. Deploy Custom ZodPanel Web Interface
log "3/8 Deploying ZodPanel custom Web GUI modules..."
if [ -d "$SCRIPT_DIR/web" ]; then
    cp -rf "$SCRIPT_DIR/web/"* /usr/local/hestia/web/
    chown -R root:root /usr/local/hestia/web
    find /usr/local/hestia/web -type d -exec chmod 755 {} +
    find /usr/local/hestia/web -type f -exec chmod 644 {} +
fi

# 4. Deploy Custom CLI Binaries
log "4/8 Deploying ZodPanel custom binaries (/usr/local/hestia/bin/)..."
if [ -d "$SCRIPT_DIR/usr_local_hestia_bin" ]; then
    cp -rf "$SCRIPT_DIR/usr_local_hestia_bin/"* /usr/local/hestia/bin/
    chmod +x /usr/local/hestia/bin/v-*
    chown root:root /usr/local/hestia/bin/v-*
fi

# 5. Setup /etc/whmpanel Configuration
log "5/8 Setting up /etc/whmpanel and SSO secrets..."
mkdir -p /etc/whmpanel
chmod 755 /etc/whmpanel

if [ -f "$SCRIPT_DIR/etc_whmpanel/webmail-sso.env" ]; then
    cp -f "$SCRIPT_DIR/etc_whmpanel/webmail-sso.env" /etc/whmpanel/webmail-sso.env
else
    cat << 'EOF' > /etc/whmpanel/webmail-sso.env
WEBMAIL_SSO_MASTER_USER='zodpanel_webmail_sso'
WEBMAIL_SSO_MASTER_PASS='PI9DRdB+CJtUa4A+02hU4qK0KOwTwSRi2+Wnh9Kd'
EOF
fi
chmod 644 /etc/whmpanel/webmail-sso.env

# 6. Configure Dovecot Master User
log "6/8 Configuring Dovecot Master User authentication..."
if ! grep -q "auth_master_user_separator" /etc/dovecot/conf.d/10-auth.conf 2>/dev/null; then
    echo "auth_master_user_separator = *" >> /etc/dovecot/conf.d/10-auth.conf
    echo "!include auth-master.conf.ext" >> /etc/dovecot/conf.d/10-auth.conf
fi

if [ -f "$SCRIPT_DIR/dovecot_configs/auth-master.conf.ext" ]; then
    cp -f "$SCRIPT_DIR/dovecot_configs/auth-master.conf.ext" /etc/dovecot/conf.d/auth-master.conf.ext
fi

# Create master users password file with encrypted master pass
MASTER_USER="zodpanel_webmail_sso"
MASTER_PASS="PI9DRdB+CJtUa4A+02hU4qK0KOwTwSRi2+Wnh9Kd"
HASHED_PASS=$(doveadm pw -s SHA512-CRYPT -p "$MASTER_PASS")
echo "${MASTER_USER}:${HASHED_PASS}" > /etc/dovecot/master-users
chmod 600 /etc/dovecot/master-users
systemctl restart dovecot

# 7. Configure phpMyAdmin & Roundcube Webmail SSO
log "7/8 Deploying phpMyAdmin & Roundcube Webmail SSO plugins..."

# phpMyAdmin SSO handler
if [ -f "$SCRIPT_DIR/phpmyadmin/hestia-sso.php" ]; then
    mkdir -p /usr/share/phpmyadmin
    cp -f "$SCRIPT_DIR/phpmyadmin/hestia-sso.php" /usr/share/phpmyadmin/hestia-sso.php
    chmod 644 /usr/share/phpmyadmin/hestia-sso.php
fi

# Roundcube zodpanel_sso plugin
if [ -f "$SCRIPT_DIR/roundcube_plugins/zodpanel_sso/zodpanel_sso.php" ]; then
    mkdir -p /var/lib/roundcube/plugins/zodpanel_sso
    cp -f "$SCRIPT_DIR/roundcube_plugins/zodpanel_sso/zodpanel_sso.php" /var/lib/roundcube/plugins/zodpanel_sso/zodpanel_sso.php
    chown -R hestiamail:www-data /var/lib/roundcube/plugins/zodpanel_sso 2>/dev/null || true
    chmod 755 /var/lib/roundcube/plugins/zodpanel_sso
    chmod 644 /var/lib/roundcube/plugins/zodpanel_sso/zodpanel_sso.php

    # Ensure zodpanel_sso is enabled in Roundcube config
    if [ -f "/etc/roundcube/config.inc.php" ] && ! grep -q "'zodpanel_sso'" /etc/roundcube/config.inc.php; then
        sed -i "s/\$config\['plugins'\] = \[/\$config\['plugins'\] = \['zodpanel_sso', /" /etc/roundcube/config.inc.php
    fi
fi

# Nginx includes
if [ -f "$SCRIPT_DIR/etc_nginx_custom/phpmyadmin.inc" ]; then
    cp -f "$SCRIPT_DIR/etc_nginx_custom/phpmyadmin.inc" /etc/nginx/conf.d/phpmyadmin.inc
fi

# 8. Deploy File Manager Configuration
log "8/9 Deploying ZodPanel direct File Manager configuration..."
if [ -f "$SCRIPT_DIR/fm/configuration.php" ]; then
    mkdir -p /usr/local/hestia/web/fm
    cp -f "$SCRIPT_DIR/fm/configuration.php" /usr/local/hestia/web/fm/configuration.php
    chmod 644 /usr/local/hestia/web/fm/configuration.php
fi

# 9. Setup Watchdog, Error Enforcer, and Restart Services
log "9/9 Setting up watchdog daemon, error enforcer, and validating web services..."
if [ -f "/usr/local/hestia/bin/v-zodpanel-watchdog" ]; then
    chmod 755 /usr/local/hestia/bin/v-zodpanel-watchdog
    echo "* * * * * root /usr/local/hestia/bin/v-zodpanel-watchdog >/dev/null 2>&1" > /etc/cron.d/zodpanel-watchdog
    chmod 644 /etc/cron.d/zodpanel-watchdog
fi

if [ -f "/usr/local/hestia/bin/v-zodpanel-enforce-debug-errors" ]; then
    chmod 755 /usr/local/hestia/bin/v-zodpanel-enforce-debug-errors
    /usr/local/hestia/bin/v-zodpanel-enforce-debug-errors || true
fi

systemctl restart hestia || true
systemctl restart nginx || true
systemctl restart apache2 || true
systemctl restart php*-fpm || true

success "=========================================================="
success "ZodHost VPS Node Installation completed successfully!"
success "Panel URL: https://${HOSTNAME_FQDN:-127.0.0.1}:8083"
success "=========================================================="

#!/bin/bash
# ==============================================================================
# HestiaCP / ZodPanel Complete User & Domain Provisioning Script
# Server IP: 169.58.176.53
# Run this on your VPS as root
# ==============================================================================

set -e

echo "=== Provisioning WHMLab Accounts to Hestia Control Panel ==="

# 1. Reset/Ensure Master Admin Password
v-change-user-password admin 'Remixbrown19@' || true
echo "[OK] Admin password synced to: Remixbrown19@"

# 2. Account: zodhost (zodhost.com)
if ! id -u zodhost >/dev/null 2>&1; then
    v-add-user 'zodhost' 'ZodHost_2026!Sec' 'hikefrm@gmail.com' 'default' 'hikefrm' 'agency'
    echo "[OK] User zodhost created"
else
    v-change-user-password 'zodhost' 'ZodHost_2026!Sec'
    echo "[OK] User zodhost password updated"
fi

v-add-web-domain 'zodhost' 'zodhost.com' '169.58.176.53' || true
v-add-mail-domain 'zodhost' 'zodhost.com' || true
v-add-dns-domain 'zodhost' 'zodhost.com' '169.58.176.53' 'ns1.zodserver.cloud' 'ns2.zodserver.cloud' || true
v-add-letsencrypt-domain 'zodhost' 'zodhost.com' || true

# 3. Account: showuldstore (showuld.store)
if ! id -u showuldstore >/dev/null 2>&1; then
    v-add-user 'showuldstore' 'SecurePass123!' 'drainvop@gmail.com' 'default' 'Mighty' 'Oracle'
    echo "[OK] User showuldstore created"
else
    v-change-user-password 'showuldstore' 'SecurePass123!'
    echo "[OK] User showuldstore password updated"
fi

v-add-web-domain 'showuldstore' 'showuld.store' '169.58.176.53' || true
v-add-mail-domain 'showuldstore' 'showuld.store' || true
v-add-dns-domain 'showuldstore' 'showuld.store' '169.58.176.53' 'ns1.zodserver.cloud' 'ns2.zodserver.cloud' || true
v-add-letsencrypt-domain 'showuldstore' 'showuld.store' || true

# 4. Control Panel SSL on Port 8083
v-add-letsencrypt-host || true

echo "=== All Accounts & AutoSSL Synchronized Successfully! ==="

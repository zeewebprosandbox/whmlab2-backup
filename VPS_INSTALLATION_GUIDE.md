# ZodHost VPS Node Installation & Architecture Guide

A complete, production-ready guide to deploying and provisioning bare-metal and cloud VPS nodes for **ZodHost & ZodPanel** (built on top of hardened HestiaCP + Multi-PHP, Nginx, Apache2, Dovecot, Exim4, MariaDB, Node.js, and Roundcube).

---

## 1. System Requirements

| Specification | Minimum | Recommended |
| :--- | :--- | :--- |
| **Operating System** | Ubuntu 24.04 LTS (x86_64) | Ubuntu 24.04 LTS (x86_64) |
| **CPU** | 2 vCPUs | 4+ vCPUs |
| **RAM** | 2 GB RAM | 4 GB+ RAM |
| **Storage** | 25 GB NVMe / SSD | 80 GB+ NVMe SSD |
| **Network** | 1 Gbps with Public IPv4 | 1 Gbps Static IPv4 + IPv6 |
| **Required Ports** | `80`, `443`, `8083`, `22`, `21`, `25`, `465`, `587`, `110`, `995`, `143`, `993`, `53` |

---

## 2. Automated One-Click Installation

On a clean Ubuntu 24.04 VPS instance, run the following commands as `root`:

```bash
# Clone the repository
git clone https://github.com/zeewebprosandbox/ZodHostVpS.git /opt/zodhost-vps
cd /opt/zodhost-vps/vps

# Execute automated installer
chmod +x install_vps_node.sh
./install_vps_node.sh
```

The automated installer will:
1. Update system repositories and install core dependencies (`fail2ban`, `ufw`, `jq`, `unzip`, `tar`, `curl`, `software-properties-common`).
2. Install the hardened HestiaCP stack with Multi-PHP (5.6, 7.0, 7.1, 7.2, 7.3, 7.4, 8.0, 8.1, 8.2, 8.3, 8.4, 8.5).
3. Deploy the custom **ZodPanel Web GUI** modules (`File Manager`, `Node.js App Engine`, `Git Auto-Deploy Engine`, `DNS Editor`, `Mail Deliverability Repair`, and `Modern Dark Theme`).
4. Install all custom CLI binaries (`v-zodpanel-node-app`, `v-zodpanel-git-deploy`, `v-zodpanel-repair-mail-deliverability`, `v-zodpanel-snapshot`, etc.) into `/usr/local/hestia/bin/`.
5. Configure **Dovecot Master User** authentication with SHA512 encrypted master credentials.
6. Install and activate **Roundcube Webmail SSO** (`zodpanel_sso`) and **phpMyAdmin SSO** (`hestia-sso.php`).
7. Configure Nginx and Apache reverse proxy routing, reload services, and activate automatic certificate management.

---

## 3. Firewall Configuration

Ensure required ports are allowed through `ufw` and cloud security groups:

```bash
ufw allow 22/tcp comment 'SSH'
ufw allow 80/tcp comment 'HTTP'
ufw allow 443/tcp comment 'HTTPS'
ufw allow 8083/tcp comment 'ZodPanel GUI'
ufw allow 25/tcp comment 'SMTP'
ufw allow 465/tcp comment 'SMTPS'
ufw allow 587/tcp comment 'Submission'
ufw allow 143/tcp comment 'IMAP'
ufw allow 993/tcp comment 'IMAPS'
ufw allow 110/tcp comment 'POP3'
ufw allow 995/tcp comment 'POP3S'
ufw allow 53 comment 'DNS'
ufw enable
```

---

## 4. Hostname & SSL Configuration

Issue a Let's Encrypt SSL certificate for the server FQDN:

```bash
# Set server hostname (replace with your domain)
v-change-sys-hostname zodpanel.zodserver.cloud

# Generate Let's Encrypt certificate for the Control Panel
v-add-letsencrypt-host
```

---

## 5. Connecting Node to WHMLab / Client Area

To connect your newly provisioned node to the WHMLab billing and provisioning backend:

1. In WHMLab Admin Area: navigate to **Servers** > **Add Server**.
2. Fill in:
   - **Hostname**: `zodpanel.zodserver.cloud`
   - **IP Address**: `<Your_VPS_IP>`
   - **Port**: `8083`
   - **API User**: `admin`
   - **API Hash / Access Key**: generated in ZodPanel under `Users > admin > Edit > Access Key`.
3. Test connection to verify automated user creation, package limits, suspension, and single sign-on synchronization.

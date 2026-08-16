# Complete ZodPanel & WHMLab Domain DNS Records Guide

This guide provides the 100% complete specifications for all DNS records (A, NS, CNAME, MX, TXT, SPF, DKIM, DMARC, SRV), step-by-step registrar glue record setup, and private GitHub repository authentication.

---

## 1. Authority Nameservers & Glue Records

To use `ns1.zodserver.cloud` and `ns2.zodserver.cloud` as your authoritative nameservers across all tenant domains:

| Nameserver Hostname | IPv4 Address | IPv6 Address (Optional) | Role |
| :--- | :--- | :--- | :--- |
| **`ns1.zodserver.cloud`** | `169.58.176.53` | - | Primary Nameserver |
| **`ns2.zodserver.cloud`** | `169.58.176.53` | - | Secondary Nameserver |

---

## 2. Complete Zone Records Table (`zodserver.cloud`)

When provisioning the main panel domain `zodserver.cloud` (or any custom tenant domain), the following complete set of DNS records is generated in Bind9:

| Record Name | Type | Value / Target | Priority | TTL | Purpose |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `@` | `A` | `169.58.176.53` | - | 14400 | Root Domain IP Pointer |
| `@` | `NS` | `ns1.zodserver.cloud.` | - | 14400 | Primary Authority NS |
| `@` | `NS` | `ns2.zodserver.cloud.` | - | 14400 | Secondary Authority NS |
| `ns1` | `A` | `169.58.176.53` | - | 14400 | Primary Nameserver IP |
| `ns2` | `A` | `169.58.176.53` | - | 14400 | Secondary Nameserver IP |
| `zodpanel` | `A` | `169.58.176.53` | - | 14400 | Subdomain Panel Access |
| `www` | `CNAME` | `zodserver.cloud.` | - | 14400 | Web Alias |
| `ftp` | `CNAME` | `zodserver.cloud.` | - | 14400 | FTP Alias |
| `mail` | `A` | `169.58.176.53` | - | 14400 | Mail Server Host |
| `webmail` | `CNAME` | `mail.zodserver.cloud.` | - | 14400 | Webmail Client |
| `smtp` | `CNAME` | `mail.zodserver.cloud.` | - | 14400 | Outbound SMTP Alias |
| `imap` | `CNAME` | `mail.zodserver.cloud.` | - | 14400 | IMAP Client Alias |
| `@` | `MX` | `mail.zodserver.cloud.` | 10 | 14400 | Mail Exchange Priority |
| `@` | `TXT` | `"v=spf1 a mx ip4:169.58.176.53 -all"` | - | 14400 | Strict SPF Sender Auth |
| `_dmarc` | `TXT` | `"v=DMARC1; p=quarantine; pct=100"` | - | 14400 | DMARC Anti-Spoof Policy |
| `mail._domainkey` | `TXT` | `"v=DKIM1; k=rsa; p=MIGfMA0GCS..."` | - | 14400 | RSA 2048 DKIM Signature |
| `_submission._tcp` | `SRV` | `0 587 mail.zodserver.cloud.` | 1 | 14400 | Authenticated SMTP Port |
| `_imap._tcp` | `SRV` | `0 143 mail.zodserver.cloud.` | 1 | 14400 | Standard IMAP Port |
| `_imaps._tcp` | `SRV` | `0 993 mail.zodserver.cloud.` | 1 | 14400 | Secure SSL IMAP Port |
| `_pop3._tcp` | `SRV` | `0 110 mail.zodserver.cloud.` | 1 | 14400 | Standard POP3 Port |
| `_pop3s._tcp` | `SRV` | `0 995 mail.zodserver.cloud.` | 1 | 14400 | Secure SSL POP3 Port |

---

### BIND 9 Zone File Format (RFC 1035 Standard for Hostinger / Cloudflare / cPanel Import)

Use this complete RFC 1035 zone file to import all records at once into Hostinger, Cloudflare, cPanel, Route 53, or Namecheap:

```bind
$ORIGIN zodserver.cloud.
$TTL 14400

@ 14400 IN SOA ns1.zodserver.cloud. admin.zodserver.cloud. ( 2026081601 14400 3600 1209600 3600 )

@ 14400 IN NS ns1.zodserver.cloud.
@ 14400 IN NS ns2.zodserver.cloud.

@ 14400 IN A 169.58.176.53
ns1 14400 IN A 169.58.176.53
ns2 14400 IN A 169.58.176.53
zodpanel 14400 IN A 169.58.176.53
vps 14400 IN A 169.58.176.53
mail 14400 IN A 169.58.176.53

www 14400 IN CNAME zodserver.cloud.
ftp 14400 IN CNAME zodserver.cloud.
webmail 14400 IN CNAME mail.zodserver.cloud.
smtp 14400 IN CNAME mail.zodserver.cloud.
imap 14400 IN CNAME mail.zodserver.cloud.
pop 14400 IN CNAME mail.zodserver.cloud.
pop3 14400 IN CNAME mail.zodserver.cloud.

@ 14400 IN MX 10 mail.zodserver.cloud.

@ 14400 IN TXT "v=spf1 a mx ip4:169.58.176.53 -all"
_dmarc 14400 IN TXT "v=DMARC1; p=quarantine; pct=100; ri=86400; sp=quarantine; aspf=r; adkim=r"

_submission._tcp 14400 IN SRV 1 0 587 mail.zodserver.cloud.
_imap._tcp 14400 IN SRV 1 0 143 mail.zodserver.cloud.
_imaps._tcp 14400 IN SRV 1 0 993 mail.zodserver.cloud.
_pop3._tcp 14400 IN SRV 1 0 110 mail.zodserver.cloud.
_pop3s._tcp 14400 IN SRV 1 0 995 mail.zodserver.cloud.
```

---

## 3. Registrar Setup Guides (Setting Up Custom Nameservers & Glue Records)

### A. Namecheap
1. Log in to your Namecheap Dashboard and select **Domain List**.
2. Click **Manage** next to `zodserver.cloud`.
3. Select **Advanced DNS** tab -> Scroll down to **Personal DNS Servers** (Glue Records).
4. Click **Add Nameserver**:
   - Hostname: `ns1` | IP: `169.58.176.53`
   - Hostname: `ns2` | IP: `169.58.176.53`
5. Return to **Domain** tab -> Under **Nameservers**, select **Custom DNS** and enter `ns1.zodserver.cloud` and `ns2.zodserver.cloud`.
6. Save changes.

### B. Cloudflare
1. Log in to Cloudflare and select domain `zodserver.cloud`.
2. Go to **DNS** -> **Records** -> Add Record:
   - Type `A` | Name `ns1` | IPv4 `169.58.176.53` | Proxy: OFF (DNS Only)
   - Type `A` | Name `ns2` | IPv4 `169.58.176.53` | Proxy: OFF (DNS Only)
   - Type `A` | Name `@` | IPv4 `169.58.176.53` | Proxy: OFF or ON
3. Go to **Custom Nameservers** (Business/Enterprise or Glue Record in Domain Registrar section) and register `ns1.zodserver.cloud` and `ns2.zodserver.cloud`.

### C. GoDaddy
1. Log in to GoDaddy Domain Portfolio and click domain `zodserver.cloud`.
2. Select **DNS** -> **Host Names** (Glue Records).
3. Click **Add Host**:
   - Host: `ns1` | IP: `169.58.176.53`
   - Host: `ns2` | IP: `169.58.176.53`
4. Under **Nameservers**, click **Change Nameservers** -> **I'll use my own nameservers** and enter `ns1.zodserver.cloud` & `ns2.zodserver.cloud`.

---

## 4. Private GitHub Repository Authentication Guide

To keep your GitHub repositories (`whmlab2-backup` and `zodpanel-hestia-custom-backup`) **100% PRIVATE** while allowing 1-Click VPS Auto-Merge to pull updates automatically:

1. **Generate a GitHub Personal Access Token (PAT)**:
   - Go to GitHub -> **Settings** -> **Developer Settings** -> **Personal Access Tokens** -> **Tokens (classic)**.
   - Click **Generate new token** with `repo` read/write permissions.
   - Copy the generated token string (e.g. `ghp_abcdefghijklmnopqrstuvwxyz0123456789`).

2. **Configure `.env` File**:
   Add your token to `.env` in your WHMLab installation:
   ```env
   ZODPANEL_GITHUB_TOKEN=ghp_abcdefghijklmnopqrstuvwxyz0123456789
   ZODPANEL_BACKUP_REPO=https://github.com/zeewebprosandbox/zodpanel-hestia-custom-backup
   WHMLAB_BACKUP_REPO=https://github.com/zeewebprosandbox/whmlab2-backup
   ```

3. **Automated Merging Operation**:
   When `ZODPANEL_GITHUB_TOKEN` is configured:
   - Any newly merged VPS connects to GitHub using `https://{TOKEN}@github.com/...`.
   - Clones and installs the latest version of the private repository onto the node automatically.
   - Your source code remains 100% private and protected from unauthorized public access.

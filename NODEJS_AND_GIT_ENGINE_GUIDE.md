# Node.js Application Engine & Git Auto-Deploy Engine Guide

This document details the architecture, CLI commands, and automated lifecycle management of **Node.js Applications** and **Git Push-to-Deploy Webhooks** inside ZodPanel.

---

## 1. Node.js Application Manager

### Overview
ZodPanel allows hosting Node.js applications (Next.js, Express, Nest.js, Fastify, Remix, Nuxt, etc.) directly alongside standard PHP web applications without needing a separate VPS or PaaS.

Each application runs isolated under its user account with:
- Configurable Node.js version (using `nvm` or system Node.js)
- Automatic reverse proxy routing via Nginx on dedicated internal ports (`3000-3999` or custom)
- Process management with auto-restart via Systemd or PM2
- Custom environment variables (`.env`) support
- Start script customization (`npm run start`, `node server.js`, `pnpm start`)

### CLI Command: `v-zodpanel-node-app`

```bash
# Usage:
v-zodpanel-node-app USER DOMAIN ACTION [PORT] [APP_ROOT] [START_SCRIPT] [NODE_VERSION]

# Examples:
# 1. Create and start a new Node.js app on port 3000
v-zodpanel-node-app zodhost zodhost.com create 3000 /home/zodhost/web/zodhost.com/public_html "npm run start" 20

# 2. Restart an existing application
v-zodpanel-node-app zodhost zodhost.com restart

# 3. Stop an application
v-zodpanel-node-app zodhost zodhost.com stop

# 4. View application status and logs
v-zodpanel-node-app zodhost zodhost.com status
```

### Nginx Reverse Proxy Architecture
When an app is enabled, ZodPanel updates `/home/{USER}/conf/web/{DOMAIN}/nginx.ssl.conf_*` to proxy all incoming web requests to the Node.js process:

```nginx
location / {
    proxy_pass http://127.0.0.1:3000;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection 'upgrade';
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_cache_bypass $http_upgrade;
}
```

---

## 2. Git Push-to-Deploy Engine

### Overview
The Git Auto-Deploy engine allows developers to deploy code to their websites and Node.js applications automatically on every `git push` to GitHub, GitLab, or Bitbucket.

### CLI Command: `v-zodpanel-git-deploy`

```bash
# Usage:
v-zodpanel-git-deploy USER DOMAIN REPO_URL BRANCH [DEPLOY_SCRIPT]

# Example:
v-zodpanel-git-deploy zodhost zodhost.com \
    "git@github.com:myorg/mywebsite.git" \
    "main" \
    "npm install && npm run build && v-zodpanel-node-app zodhost zodhost.com restart"
```

### Features
1. **Webhook Endpoint**: Generates a secure webhook URL (e.g. `https://zodpanel.zodserver.cloud:8083/api/git-webhook.php?token=...`) to paste into GitHub/GitLab repo settings.
2. **Deploy Key Generation**: Automatically generates dedicated Ed25519 SSH deploy keys with read-only access for private repositories.
3. **Automated Build Scripts**: Executes custom post-checkout scripts (e.g. Composer install, NPM build, database migrations, process reloads).
4. **Deploy Logs**: Stores timestamped build and deployment output in `/home/{USER}/web/{DOMAIN}/logs/git-deploy.log`.

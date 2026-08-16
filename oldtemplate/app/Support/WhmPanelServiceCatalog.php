<?php

namespace App\Support;

class WhmPanelServiceCatalog
{
    public static function keys(): array
    {
        return array_column(self::modules(), 'key');
    }

    public static function find(string $key, ?array $nodeFeatures = null, ?array $packageFeatures = null): ?array
    {
        foreach (self::modules($nodeFeatures, $packageFeatures) as $module) {
            if ($module['key'] === $key) {
                return $module;
            }
        }

        return null;
    }

    public static function modules(?array $nodeFeatures = null, ?array $packageFeatures = null): array
    {
        $nodeFeatures ??= [];
        $packageFeatures ??= [];

        return [
            self::module('file_manager', 'File Manager', 'Browse, upload, edit, compress, and recover site files.', 'folder-open', 'live', true, true, [
                'Open domain root',
                'Upload/download',
                'Zip/unzip',
                'Permissions',
                'Code editor',
            ], 'Open file workspace'),
            self::module('webmail', 'Webmail', 'Roundcube mailbox access with DNS, SSL, DKIM, SPF, and mailbox repair tooling.', 'mail', self::status($nodeFeatures, $packageFeatures, 'webmail'), $nodeFeatures['webmail'] ?? false, $packageFeatures['webmail'] ?? true, [
                'Mail domain list',
                'Open webmail',
                'Mailbox manager',
                'Password reset',
                'Mailbox quota',
                'Forwarders',
                'Aliases',
                'Autoresponders',
                'Catch-all',
                'DKIM/SPF/DMARC status',
                'MX status check',
                'DNS blockers',
                'Mail SSL repair',
                'Mail logs',
            ], 'Manage mailboxes'),
            self::module('php_selector', 'PHP Selector', 'Per-domain PHP version switching, php.ini controls, extensions, and error logs.', 'file-code-2', self::status($nodeFeatures, $packageFeatures, 'php_selector'), $nodeFeatures['php_selector'] ?? false, $packageFeatures['php_selector'] ?? true, [
                'Available versions',
                'Per-domain switch',
                'PHP-FPM reload',
                'php.ini limits',
                'Extensions',
                'Composer',
            ], 'Open PHP controls'),
            self::module('terminal', 'Terminal', 'Browser terminal for eligible developer packages with strict non-root access.', 'square-terminal', self::status($nodeFeatures, $packageFeatures, 'terminal'), $nodeFeatures['terminal'] ?? false, $packageFeatures['terminal'] ?? false, [
                'Open shell',
                'Domain root shortcut',
                'Audit logs',
                'Package permission',
                'Disable per account',
            ], 'Open terminal'),
            self::module('nodejs', 'Node.js Apps', 'Deploy Node apps with environment variables, process control, logs, and domain proxying.', 'hexagon', self::status($nodeFeatures, $packageFeatures, 'nodejs'), $nodeFeatures['nodejs'] ?? false, $packageFeatures['nodejs'] ?? false, [
                'Create app',
                'Startup file',
                'NPM install',
                'Env variables',
                'Restart/logs',
                'Domain mapping',
            ], 'Create Node app'),
            self::module('python', 'Python Apps', 'Run Flask, Django, and FastAPI apps with virtualenv, logs, and domain mapping.', 'blocks', self::status($nodeFeatures, $packageFeatures, 'python'), $nodeFeatures['python'] ?? false, $packageFeatures['python'] ?? false, [
                'Virtualenv',
                'Requirements',
                'WSGI/ASGI',
                'Env variables',
                'Restart/logs',
            ], 'Create Python app'),
            self::module('dns', 'DNS Center', 'Zone editing, external DNS detection, propagation checks, and record repair.', 'network', $nodeFeatures['auto_dns'] ?? true ? 'live' : 'blocked', $nodeFeatures['auto_dns'] ?? true, $packageFeatures['auto_dns'] ?? true, [
                'Zone list',
                'Zone editor',
                'DNS templates',
                'External DNS warning',
                'Nameserver delegation',
                'Propagation check',
                'Mail records',
                'DKIM',
                'DMARC',
                'CAA',
            ], 'Repair DNS'),
            self::module('ssl', 'SSL Center', 'Let’s Encrypt, force HTTPS, mail SSL, expiry checks, and bulk repair.', 'shield-check', 'live', true, $packageFeatures['auto_ssl'] ?? true, [
                'SSL status',
                'Issue SSL',
                'Force HTTPS',
                'Mail SSL',
                'DNS blockers',
                'Expiry tracking',
                'Manual upload',
                'HSTS toggle',
                'Bulk repair',
            ], 'Open SSL center'),
            self::module('databases', 'Databases', 'MySQL databases, users, phpMyAdmin, imports, exports, and backup restore.', 'database', 'live', true, true, [
                'DB list',
                'Create DB',
                'DB users',
                'Reset DB password',
                'phpMyAdmin',
                'Import/export',
                'Remote MySQL',
                'Size usage',
                'DB backup/restore',
            ], 'Create database'),
            self::module('backups', 'Backups', 'Manual and scheduled backups with full, file, database, and mail restores.', 'archive-restore', self::status($nodeFeatures, $packageFeatures, 'backups'), true, $packageFeatures['backups'] ?? false, [
                'Backup list',
                'Create backup',
                'Download',
                'Restore account',
                'Restore files',
                'Restore DB',
                'Restore mail',
                'Remote destination',
                'Retention',
                'Failed backup alerts',
            ], 'Create backup'),
            self::module('logs', 'Logs', 'Access logs, error logs, PHP logs, mail logs, and repair recommendations.', 'scroll-text', 'planned', true, true, [
                'Web access',
                'Web errors',
                'PHP errors',
                'Mail logs',
                'DNS status',
                'SSL issue logs',
                'Login history',
                'Resource spikes',
                'Search',
            ], 'Inspect logs'),
            self::module('security', 'Security Center', '2FA, IP blocks, protected directories, failed-login defense, and isolation checks.', 'lock-keyhole', 'planned', true, true, [
                '2FA',
                'Failed login protection',
                'IP deny',
                'IP unblock',
                'Protected dirs',
                'SSH/terminal policy',
                'Login history',
                'Permissions',
                'Isolation checks',
            ], 'Open security'),
            self::module('apps', 'App Installer', 'WordPress, Laravel, static sites, Git deploys, staging, and update safety.', 'rocket', 'planned', true, true, [
                'WordPress',
                'Laravel',
                'Static site deploy',
                'Git deploy',
                'Environment manager',
                'Staging',
                'Update backup',
            ], 'Install app'),
            self::module('fleet', 'Server Fleet', 'Multi-node health, service restarts, routing, capacity, package sync, and token rotation.', 'server-cog', 'live', true, true, [
                'Multiple ZodPanel servers',
                'Node feature discovery',
                'Health checks',
                'Service status',
                'Restart services',
                'Capacity routing',
                'Package sync',
                'Token rotation',
            ], 'Inspect fleet'),
            self::module('control_strip', 'Client Control Strip', 'Client-facing shortcuts for panel, file manager, webmail, phpMyAdmin, DNS, SSL, usage, and package features.', 'panel-top', 'live', true, true, [
                'Open ZodPanel',
                'Open File Manager',
                'Open Webmail',
                'Open phpMyAdmin',
                'Open DNS',
                'Repair SSL',
                'View usage',
                'Reset panel password',
            ], 'Preview controls'),
        ];
    }

    public static function summary(array $modules): array
    {
        return [
            'total' => count($modules),
            'live' => count(array_filter($modules, fn($module) => $module['status'] === 'live')),
            'planned' => count(array_filter($modules, fn($module) => $module['status'] === 'planned')),
            'blocked' => count(array_filter($modules, fn($module) => $module['status'] === 'blocked')),
        ];
    }

    private static function status(array $nodeFeatures, array $packageFeatures, string $key): string
    {
        if (array_key_exists($key, $packageFeatures) && !$packageFeatures[$key]) {
            return 'blocked';
        }

        if (array_key_exists($key, $nodeFeatures) && !$nodeFeatures[$key]) {
            return 'blocked';
        }

        if (($nodeFeatures[$key] ?? false) || ($packageFeatures[$key] ?? false)) {
            return 'live';
        }

        return 'planned';
    }

    private static function module(string $key, string $name, string $description, string $icon, string $status, bool $nodeReady, bool $packageReady, array $capabilities, string $primaryAction): array
    {
        return compact('key', 'name', 'description', 'icon', 'status', 'nodeReady', 'packageReady', 'capabilities', 'primaryAction');
    }
}

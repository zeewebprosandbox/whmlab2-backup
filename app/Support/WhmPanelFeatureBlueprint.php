<?php

namespace App\Support;

use App\Models\Product;

class WhmPanelFeatureBlueprint
{
    public static function featureKeys(): array
    {
        return [
            'file_manager',
            'auto_dns',
            'auto_ssl',
            'force_https',
            'webmail',
            'terminal',
            'php_selector',
            'nodejs',
            'python',
            'composer',
            'backups',
        ];
    }

    public static function limitKeys(): array
    {
        return [
            'disk_limit_mb',
            'bandwidth_limit_mb',
            'cpu_quota',
            'cpu_quota_period',
            'memory_limit',
            'swap_limit',
            'web_domains',
            'web_aliases',
            'dns_domains',
            'dns_records',
            'mail_domains',
            'mail_accounts',
            'databases',
            'cron_jobs',
            'backups',
        ];
    }

    public static function fromProduct(Product $product): array
    {
        $savedBlueprint = is_array($product->whmpanel_features) ? $product->whmpanel_features : null;

        if (self::hasSavedBlueprint($savedBlueprint)) {
            return self::normalizeBlueprint($savedBlueprint);
        }

        return self::fromProductText($product);
    }

    public static function fromRequest(Product $product, array $input): array
    {
        $blueprint = self::fromProductText($product);

        foreach (self::featureKeys() as $feature) {
            $blueprint['features'][$feature] = !empty($input['features'][$feature]);
        }

        foreach (self::limitKeys() as $limit) {
            $isUnlimited = !empty($input['limit_unlimited'][$limit]);
            $value = $input['limits'][$limit] ?? null;

            $blueprint['limits'][$limit] = $isUnlimited
                ? 'unlimited'
                : max(0, (int) $value);
        }

        $blueprint['recommended_links'] = self::recommendedLinks($blueprint['features']);
        $blueprint['source'] = 'admin';

        return self::normalizeBlueprint($blueprint);
    }

    private static function fromProductText(Product $product): array
    {
        $text = strtolower(trim(implode("\n", array_filter([
            $product->name,
            optional($product->serviceCategory)->name,
            $product->description,
        ]))));

        $websiteLimit = self::countLimit($text, 'websites?', str_contains($text, 'unlimited websites') ? 'unlimited' : 1);

        $backups = self::backupCount($text);
        $features = [
            'file_manager' => true,
            'auto_dns' => !self::hasAny($text, ['no dns', 'manual dns', 'dns excluded']),
            'auto_ssl' => !self::hasAny($text, ['no ssl', 'ssl excluded']),
            'force_https' => !self::hasAny($text, ['no force https', 'manual https redirect']),
            'webmail' => !self::hasAny($text, ['no email', 'mail excluded', 'email excluded']),
            'terminal' => self::hasAny($text, ['terminal', 'ssh', 'shell access', 'developer', 'dev tools']),
            'php_selector' => !self::hasAny($text, ['static php', 'no php selector']),
            'nodejs' => self::hasAny($text, ['node.js', 'nodejs', 'node app', 'npm']),
            'python' => self::hasAny($text, ['python', 'django', 'flask', 'fastapi']),
            'composer' => self::hasAny($text, ['composer', 'laravel', 'php developer', 'developer']),
            'backups' => $backups > 0 && !self::hasAny($text, ['no backup', 'backup excluded']),
        ];

        $limits = [
            'disk_limit_mb' => self::limitFromText($text, 'storage', 10240),
            'bandwidth_limit_mb' => self::limitFromText($text, 'bandwidth', 102400),
            'cpu_quota' => self::cpuQuotaFromText($text),
            'cpu_quota_period' => 100000,
            'memory_limit' => self::memoryLimitFromText($text),
            'swap_limit' => self::memoryLimitFromText($text),
            'web_domains' => $websiteLimit,
            'web_aliases' => $websiteLimit === 'unlimited' ? 'unlimited' : max(1, (int) $websiteLimit),
            'dns_domains' => $features['auto_dns'] ? $websiteLimit : 0,
            'dns_records' => $features['auto_dns'] ? 'unlimited' : 0,
            'mail_domains' => $features['webmail'] ? $websiteLimit : 0,
            'mail_accounts' => $features['webmail'] ? self::countLimit($text, 'email accounts?', str_contains($text, 'unlimited email') ? 'unlimited' : 5) : 0,
            'databases' => self::countLimit($text, '(?:mysql\s+|postgresql\s+)?databases?', str_contains($text, 'unlimited database') ? 'unlimited' : 5),
            'cron_jobs' => 'unlimited',
            'backups' => $features['backups'] ? $backups : 0,
        ];

        return [
            'version' => 1,
            'features' => $features,
            'limits' => $limits,
            'recommended_links' => self::recommendedLinks($features),
            'source' => 'description',
        ];
    }

    public static function packageSpec(Product $product): array
    {
        $blueprint = self::fromProduct($product);

        return array_merge($blueprint['limits'], [
            'features' => $blueprint['features'],
            'blueprint' => $blueprint,
        ]);
    }

    private static function recommendedLinks(array $features): array
    {
        $links = ['panel', 'file_manager', 'ssl_repair'];

        if ($features['webmail']) {
            $links[] = 'webmail';
        }

        if ($features['php_selector']) {
            $links[] = 'php_selector';
        }

        if ($features['terminal']) {
            $links[] = 'terminal';
        }

        if ($features['nodejs']) {
            $links[] = 'nodejs';
        }

        return $links;
    }

    private static function hasSavedBlueprint(?array $blueprint): bool
    {
        return is_array($blueprint)
            && data_get($blueprint, 'source') === 'admin'
            && is_array(data_get($blueprint, 'features'))
            && is_array(data_get($blueprint, 'limits'));
    }

    private static function normalizeBlueprint(array $blueprint): array
    {
        $features = [];
        foreach (self::featureKeys() as $feature) {
            $features[$feature] = (bool) data_get($blueprint, "features.$feature", false);
        }

        $limits = [];
        foreach (self::limitKeys() as $limit) {
            $value = data_get($blueprint, "limits.$limit", 0);
            $limits[$limit] = $value === 'unlimited' ? 'unlimited' : max(0, (int) $value);
        }

        return [
            'version' => (int) data_get($blueprint, 'version', 1),
            'features' => $features,
            'limits' => $limits,
            'recommended_links' => self::recommendedLinks($features),
            'source' => data_get($blueprint, 'source', 'admin'),
        ];
    }

    private static function hasAny(string $text, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($text, $needle)) {
                return true;
            }
        }

        return false;
    }

    private static function countLimit(string $description, string $labelPattern, int|string $fallback): int|string
    {
        if (is_string($fallback) && $fallback === 'unlimited') {
            return 'unlimited';
        }

        if (preg_match('/(\d+)\s+' . $labelPattern . '/', $description, $match)) {
            return (int) $match[1];
        }

        return $fallback;
    }

    private static function backupCount(string $text): int
    {
        if (self::hasAny($text, ['daily backup', 'daily backups'])) {
            return 7;
        }

        if (self::hasAny($text, ['weekly backup', 'weekly backups', 'backup'])) {
            return 1;
        }

        return 0;
    }

    private static function limitFromText(string $text, string $kind, int $fallback): int
    {
        if ($kind === 'storage' && preg_match('/unlimited\s+(?:ssd\s+|nvme\s+)?(?:storage|disk|disk\s+space|web\s+space)/', $text)) {
            return 1048576;
        }

        if ($kind === 'bandwidth' && self::hasAny($text, ['unlimited bandwidth', 'unlimited transfer', 'unlimited traffic'])) {
            return 10485760;
        }

        $patterns = $kind === 'storage'
            ? ['/(\\d+(?:\\.\\d+)?)\\s*(tb|gb|mb)\\s*(?:ssd\\s+|nvme\\s+)?(?:storage|disk|disk\\s+space|web\\s+space)/']
            : ['/(\\d+(?:\\.\\d+)?)\\s*(tb|gb|mb)\\s*(?:bandwidth|transfer|traffic)/'];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $match)) {
                return match (strtolower($match[2])) {
                    'tb' => (int) round((float) $match[1] * 1024 * 1024),
                    'gb' => (int) round((float) $match[1] * 1024),
                    default => (int) round((float) $match[1]),
                };
            }
        }

        return $fallback;
    }

    private static function cpuQuotaFromText(string $text): int|string
    {
        if (preg_match('/(\d+(?:\.\d+)?)\s*(?:vcpu|vcpus|cpu cores?|cores?)/', $text, $match)) {
            return max(100, (int) round((float) $match[1] * 100));
        }

        return 'unlimited';
    }

    private static function memoryLimitFromText(string $text): int|string
    {
        if (preg_match('/(\d+(?:\.\d+)?)\s*(tb|gb|mb)\s*(?:ram|memory)/', $text, $match)) {
            return match (strtolower($match[2])) {
                'tb' => (int) round((float) $match[1] * 1024 * 1024),
                'gb' => (int) round((float) $match[1] * 1024),
                default => (int) round((float) $match[1]),
            };
        }

        return 'unlimited';
    }
}

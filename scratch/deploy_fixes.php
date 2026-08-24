<?php
require __DIR__ . '/../vendor/autoload.php';

$ssh = new \phpseclib3\Net\SSH2('169.58.176.53', 22);
if (!$ssh->login('root', 'Remixbrown99@')) {
    die("SSH login failed\n");
}

echo "1. Deploying /usr/local/hestia/bin/v-add-user-pma-temp-user...\n";
$pmaTempUserScript = <<<'BASH'
#!/usr/bin/env bash
# info: Provision all-database access for Hestia/ZodPanel user
user="$1"
[ -n "$user" ] || exit 1

pma_user="pma_${user}"
pma_pass="ZodHostPass_${user}_2026!"

# MySQL Grant
mysql -uroot <<SQL 2>/dev/null || mariadb -uroot <<SQL 2>/dev/null || true
CREATE USER IF NOT EXISTS '${pma_user}'@'localhost' IDENTIFIED BY '${pma_pass}';
ALTER USER '${pma_user}'@'localhost' IDENTIFIED BY '${pma_pass}';
SQL

if [ "$user" = "admin" ] || [ "$user" = "root" ]; then
mysql -uroot <<SQL 2>/dev/null || mariadb -uroot <<SQL 2>/dev/null || true
GRANT ALL PRIVILEGES ON *.* TO '${pma_user}'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
SQL
else
mysql -uroot <<SQL 2>/dev/null || mariadb -uroot <<SQL 2>/dev/null || true
GRANT ALL PRIVILEGES ON \`${user}_%\`.* TO '${pma_user}'@'localhost';
GRANT ALL PRIVILEGES ON \`${user}\`.* TO '${pma_user}'@'localhost';
FLUSH PRIVILEGES;
SQL
fi

echo -n "$pma_pass"
BASH;

$ssh->exec("cat << 'EOF' > /usr/local/hestia/bin/v-add-user-pma-temp-user\n" . $pmaTempUserScript . "\nEOF\n");
$ssh->exec("chmod 755 /usr/local/hestia/bin/v-add-user-pma-temp-user");

echo "2. Deploying /usr/share/phpmyadmin/hestia-sso.php & phpMyAdmin config...\n";
$ssoScript = <<<'PHP'
<?php
declare(strict_types=1);

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);
$session_name = "SignonSession";
session_name($session_name);
@session_start();

function session_invalid(): void {
	global $session_name;
	$_SESSION = [];
	if (session_status() === PHP_SESSION_ACTIVE) {
		@session_destroy();
	}
	setcookie($session_name, "", time() - 3600, "/");
	header("Location: index.php");
	exit();
}

if (isset($_GET["logout"])) {
	$_SESSION = [];
	if (session_status() === PHP_SESSION_ACTIVE) {
		@session_destroy();
	}
	setcookie($session_name, "", time() - 3600, "/");
	header("Location: /list/db/");
	exit();
}

if (!empty($_GET["user"]) && !empty($_GET["pma_pass"]) && !empty($_GET["token"]) && isset($_GET["zod_all"])) {
	$user = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $_GET["user"]);
	$raw_pass = base64_decode((string) $_GET["pma_pass"], true) ?: '';
	$exp = (int) ($_GET["exp"] ?? 0);
	$token = (string) $_GET["token"];

	$expected_token = md5($user . $raw_pass . $exp . "ZODPANEL_SECRET");
	if (abs(time() - $exp) <= 600 && hash_equals($expected_token, $token)) {
		$pma_user = "pma_" . $user;
		$_SESSION["PMA_single_signon_user"] = $pma_user;
		$_SESSION["PMA_single_signon_password"] = $raw_pass;
		$_SESSION["PMA_single_signon_host"] = "localhost";
		$_SESSION["PMA_single_signon_port"] = "3306";
		$_SESSION["HESTIA_sso_user"] = $user;
		@session_write_close();
		setcookie($session_name, session_id(), [
            'expires' => 0,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
		header("Location: index.php");
		exit();
	} else {
		session_invalid();
	}
}

if (empty($_SESSION["PMA_single_signon_user"])) {
	if (!empty($_COOKIE[$session_name])) {
		header("Location: index.php");
		exit();
	}
}
header("Location: index.php");
exit();
PHP;

$ssh->exec("cat << 'EOF' > /usr/share/phpmyadmin/hestia-sso.php\n" . $ssoScript . "\nEOF\n");
$ssh->exec("chmod 644 /usr/share/phpmyadmin/hestia-sso.php; chown root:hestiamail /usr/share/phpmyadmin/hestia-sso.php");

$pmaConf = <<<'PHP'
<?php
if (!isset($cfg['Servers'][$i])) { $cfg['Servers'][$i] = []; }
$cfg['Servers'][$i]['auth_type'] = 'signon';
$cfg['Servers'][$i]['SignonSession'] = 'SignonSession';
$cfg['Servers'][$i]['SignonURL'] = 'hestia-sso.php';
$cfg['Servers'][$i]['LogoutURL'] = 'hestia-sso.php?logout=1';
PHP;
$ssh->exec("cat << 'EOF' > /etc/phpmyadmin/conf.d/00-hestia-sso.php\n" . $pmaConf . "\nEOF\n");

echo "3. Updating /usr/local/hestia/web/fm/domains/index.php...\n";
$fmDomainsScript = <<<'PHP'
<?php

$TAB = "FM";
include __DIR__ . "/../../inc/main.php";

$fm_user = $user;
if (!empty($_SESSION["look"]) && $_SESSION["userContext"] === "admin") {
	$fm_user = $_SESSION["look"];
}

$domains = [];
$output = [];
$return_var = 0;
exec("/usr/local/hestia/bin/v-list-web-domains " . escapeshellarg($fm_user) . " json", $output, $return_var);
if ($return_var === 0) {
	$domains = json_decode(implode("", $output), true) ?: [];
}

render_page($user, $TAB, function () use ($domains) {
?>
<div class="toolbar">
	<div class="toolbar-inner">
		<div class="toolbar-buttons">
			<a class="button button-secondary" href="/list/web/">
				<i class="fas fa-arrow-left icon-blue"></i> <?= tohtml(_("Web Domains")) ?>
			</a>
		</div>
	</div>
</div>

<div class="container zod-fm-domain-page">
	<section class="zod-fm-hero" style="margin: 16px 0 24px 0; text-align: left;">
		<span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #6366f1;"><?= tohtml(_("File Manager")) ?></span>
		<h1 style="font-size: 22px; font-weight: 800; color: #fff; margin: 4px 0 8px 0; letter-spacing: -0.02em;"><?= tohtml(_("Choose a website")) ?></h1>
		<p style="font-size: 13px; color: #a1a1aa; margin: 0;"><?= tohtml(_("Select a domain below to jump directly into its public_html directory.")) ?></p>
	</section>

	<?php if (empty($domains)) { ?>
		<div class="zod-fm-empty" style="background: #14141a; border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 40px 20px; text-align: center;">
			<i class="fas fa-folder-open" style="font-size: 42px; color: #6366f1; margin-bottom: 16px;"></i>
			<h2 style="font-size: 18px; color: #fff; margin-bottom: 8px;"><?= tohtml(_("No web domains yet")) ?></h2>
			<p style="color: #a1a1aa; margin-bottom: 20px;"><?= tohtml(_("Add a web domain first, then its public_html folder will appear here.")) ?></p>
			<a class="button button-primary" href="/add/web/"><i class="fas fa-plus"></i> <?= tohtml(_("Add Web Domain")) ?></a>
		</div>
	<?php } else { ?>
		<div class="zod-fm-domain-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 14px;">
			<?php foreach ($domains as $domain => $data) {
				$is_suspended = ($data["SUSPENDED"] ?? "no") === "yes";
				$disk = $data["U_DISK"] ?? "0";
				$bandwidth = $data["U_BANDWIDTH"] ?? "0";
			?>
				<a class="zod-fm-domain-card <?= $is_suspended ? "is-disabled" : "" ?>" href="/fm/?<?= tohtml(http_build_query(["domain" => $domain])) ?>" style="background: #14141a; border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; padding: 16px; display: flex; align-items: center; justify-content: space-between; text-decoration: none; transition: all 0.2s ease;">
					<span class="zod-fm-domain-icon" style="background: rgba(99,102,241,0.12); border: 1px solid rgba(99,102,241,0.25); border-radius: 8px; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; margin-right: 14px; flex-shrink: 0;">
						<i class="fas fa-folder-open" style="color: #818cf8; font-size: 18px;"></i>
					</span>
					<span class="zod-fm-domain-body" style="flex: 1; min-width: 0;">
						<strong style="display: block; font-size: 14px; font-weight: 700; color: #f4f4f7; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= tohtml($domain) ?></strong>
						<small style="display: block; font-size: 11px; color: #a1a1aa; margin: 2px 0 4px 0;">public_html</small>
						<em style="display: block; font-style: normal; font-size: 11px; font-family: 'JetBrains Mono', monospace; color: #71717a;"><?= tohtml(humanize_usage_size($disk)) ?> <?= tohtml(humanize_usage_measure($disk)) ?></em>
					</span>
					<span class="zod-fm-domain-action" style="color: #6366f1; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
						<?= tohtml(_("Open")) ?> <i class="fas fa-arrow-right"></i>
					</span>
				</a>
			<?php } ?>
		</div>
	<?php } ?>
</div>
<?php
});
PHP;

$ssh->exec("cat << 'EOF' > /usr/local/hestia/web/fm/domains/index.php\n" . $fmDomainsScript . "\nEOF\n");
$ssh->exec("chmod 644 /usr/local/hestia/web/fm/domains/index.php");

echo "4. Ensuring Hestia Nginx conf has /fm/domains/ block...\n";
$nginxConf = $ssh->exec('cat /usr/local/hestia/nginx/conf/nginx.conf');
if (!str_contains($nginxConf, 'location ^~ /fm/domains/')) {
    $patch = <<<'NGINX'
		location = /fm/domains {
			return 301 /fm/domains/;
		}

		location ^~ /fm/domains/ {
			alias /usr/local/hestia/web/fm/domains/;
			index index.php;

			location ~ \.php$ {
				include       fastcgi_params;
				fastcgi_param HTTP_EARLY_DATA $rfc_early_data if_not_empty;
				fastcgi_param SCRIPT_FILENAME /usr/local/hestia/web/fm/domains/index.php;
				fastcgi_pass  unix:/run/hestia-php.sock;
			}
		}

NGINX;
    $nginxConf = str_replace('location /fm/ {', $patch . "\t\tlocation /fm/ {", $nginxConf);
    $ssh->exec("cat << 'EOF' > /usr/local/hestia/nginx/conf/nginx.conf\n" . $nginxConf . "\nEOF\n");
    $ssh->exec("/usr/local/hestia/nginx/sbin/hestia-nginx -t && systemctl restart hestia");
}

echo "5. Deploying refined CSS to /usr/local/hestia/web/css/zodpanel-modules.css...\n";
$cssContent = file_get_contents(__DIR__ . '/zodpanel-modules.css');
$ssh->exec("cat << 'EOF' > /usr/local/hestia/web/css/zodpanel-modules.css\n" . $cssContent . "\nEOF\n");
$ssh->exec("chmod 644 /usr/local/hestia/web/css/zodpanel-modules.css");

echo "6. Restarting hestia, nginx, and apache2 services...\n";
$ssh->exec("systemctl restart hestia nginx apache2");

echo "Done!\n";

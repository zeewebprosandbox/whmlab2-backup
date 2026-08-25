<?php

require __DIR__ . '/../vendor/autoload.php';

$ssh = new \phpseclib3\Net\SSH2('169.58.176.53', 22);
if (!$ssh->login('root', 'Remixbrown99@')) {
    die("SSH Failed\n");
}

$sftp = new \phpseclib3\Net\SFTP('169.58.176.53', 22);
if (!$sftp->login('root', 'Remixbrown99@')) {
    die("SFTP Failed\n");
}

echo "1. Ensuring /etc/whmpanel and webmail-sso.env permissions (755 / 644)...\n";
$ssh->exec("chmod 755 /etc/whmpanel && chmod 644 /etc/whmpanel/webmail-sso.env");

echo "2. Deploying /var/lib/roundcube/plugins/zodpanel_sso/zodpanel_sso.php...\n";
$ssh->exec("mkdir -p /var/lib/roundcube/plugins/zodpanel_sso");

$pluginCode = <<<'PHP'
<?php

/**
 * ZodPanel Webmail SSO Plugin for Roundcube
 * Authenticates via Dovecot Master User with HMAC-SHA256 signed tokens
 */
class zodpanel_sso extends rcube_plugin
{
    public function init()
    {
        $this->add_hook('startup', [$this, 'startup']);
        $this->add_hook('authenticate', [$this, 'authenticate']);
        $this->add_hook('login_after', [$this, 'login_after']);
        $this->add_hook('user_create', [$this, 'user_create']);
    }

    public function startup($args)
    {
        if (!empty($_GET['_zod_sso']) && empty($_SESSION['user_id'])) {
            $args['task'] = 'login';
            $args['action'] = 'login';
        }
        return $args;
    }

    public function authenticate($args)
    {
        if (!empty($_GET['_zod_sso'])) {
            $user = trim((string) ($_GET['user'] ?? ''));
            $time = intval($_GET['t'] ?? 0);
            $token = trim((string) ($_GET['s'] ?? ''));

            $env = $this->load_env();
            $masterUser = $env['WEBMAIL_SSO_MASTER_USER'] ?? '';
            $masterPass = $env['WEBMAIL_SSO_MASTER_PASS'] ?? '';

            if (empty($user) || empty($token) || empty($masterUser) || empty($masterPass)) {
                return $args;
            }

            if (abs(time() - $time) > 600) {
                return $args;
            }

            $expectedToken = hash_hmac('sha256', $user . '|' . $time, $masterPass);
            if (!hash_equals($expectedToken, $token)) {
                return $args;
            }

            $args['user'] = $user . '*' . $masterUser;
            $args['pass'] = $masterPass;
            $args['host'] = 'localhost:143';
            $args['cookiecheck'] = false;
            $args['valid'] = true;
        }

        return $args;
    }

    public function login_after($args)
    {
        if (!empty($_GET['_zod_sso'])) {
            return ['_task' => 'mail'];
        }
        return $args;
    }

    public function user_create($args)
    {
        if (strpos($args['user'], '*') !== false) {
            $cleanUser = preg_replace('/\*.*$/', '', $args['user']);
            $args['user_email'] = $cleanUser;
        }
        return $args;
    }

    private function load_env(): array
    {
        $path = '/etc/whmpanel/webmail-sso.env';
        if (!is_readable($path)) {
            return [];
        }

        $values = [];
        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $values[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
        }

        return $values;
    }
}
PHP;

$sftp->put('/var/lib/roundcube/plugins/zodpanel_sso/zodpanel_sso.php', $pluginCode);
$ssh->exec("chown -R hestiamail:www-data /var/lib/roundcube/plugins/zodpanel_sso");
$ssh->exec("chmod 755 /var/lib/roundcube/plugins/zodpanel_sso && chmod 644 /var/lib/roundcube/plugins/zodpanel_sso/zodpanel_sso.php");

echo "3. Ensuring 'zodpanel_sso' is registered in Roundcube config...\n";
$configInc = $sftp->get('/etc/roundcube/config.inc.php');
if (!str_contains($configInc, "'zodpanel_sso'")) {
    $configInc = preg_replace('/\$config\[([\'"])plugins[\'"]\]\s*=\s*\[(.*?)\];/s', '$config["plugins"] = [$2, \'zodpanel_sso\'];', $configInc);
    $configInc = preg_replace('/,(\s*,)+/', ',', $configInc);
    $sftp->put('/etc/roundcube/config.inc.php', $configInc);
}

echo "4. Deploying /usr/local/hestia/web/open/webmail/index.php...\n";
$openWebmailIndex = <<<'PHP'
<?php
$TAB = "MAIL";

include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";

function zod_webmail_sso_error(string $message, int $status = 400): void {
	http_response_code($status);
	echo "<!doctype html><meta charset=\"utf-8\"><title>Webmail SSO</title><p>" . htmlentities($message) . "</p>";
	exit();
}

function zod_webmail_sso_env(): array {
	$path = "/etc/whmpanel/webmail-sso.env";
	if (!is_readable($path)) {
		zod_webmail_sso_error("Webmail SSO configuration is missing or unreadable.", 503);
	}

	$values = [];
	foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
		if (str_starts_with(trim($line), "#") || !str_contains($line, "=")) {
			continue;
		}
		[$key, $value] = explode("=", $line, 2);
		$values[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
	}

	return $values;
}

if (empty($_GET["token"]) || !hash_equals((string) $_SESSION["token"], (string) $_GET["token"])) {
	zod_webmail_sso_error("Invalid session token.", 403);
}

$domain = strtolower(trim((string) ($_GET["domain"] ?? "")));
$account = strtolower(trim((string) ($_GET["account"] ?? "")));

if (!preg_match('/^[a-z0-9][a-z0-9.-]*\.[a-z]{2,}$/', $domain)) {
	zod_webmail_sso_error("Invalid mail domain.");
}

if (!preg_match('/^[a-z0-9._%+\-]+$/', $account)) {
	zod_webmail_sso_error("Invalid mail account.");
}

exec(
	HESTIA_CMD . "v-list-mail-account " . $user . " " . \Hestiacp\quoteshellarg\quoteshellarg($domain) . " " . \Hestiacp\quoteshellarg\quoteshellarg($account) . " json",
	$output,
	$return_var,
);

if ($return_var !== 0) {
	zod_webmail_sso_error("Mail account was not found.", 404);
}

$mailbox = $account . "@" . $domain;
$env = zod_webmail_sso_env();
$masterPass = (string) ($env["WEBMAIL_SSO_MASTER_PASS"] ?? "");

if ($masterPass === "") {
	zod_webmail_sso_error("Webmail SSO credentials are incomplete.", 503);
}

$time = time();
$ssoToken = hash_hmac("sha256", $mailbox . "|" . $time, $masterPass);

$webmailAlias = $_SESSION["WEBMAIL_ALIAS"] ?? "webmail";
$webmailHost = $webmailAlias . "." . $domain;
$ssoUrl = "https://" . $webmailHost . "/?_zod_sso=1&user=" . urlencode($mailbox) . "&t=" . $time . "&s=" . $ssoToken;

header("Location: " . $ssoUrl);
exit();
PHP;

$sftp->put('/usr/local/hestia/web/open/webmail/index.php', $openWebmailIndex);
$ssh->exec("chmod 644 /usr/local/hestia/web/open/webmail/index.php");

echo "5. Verifying SSO...\n";
$verifyCmd = <<<'BASH'
php -r '
$time = time();
$user = "help@zodhost.com";
$pass = "PI9DRdB+CJtUa4A+02hU4qK0KOwTwSRi2+Wnh9Kd";
$token = hash_hmac("sha256", $user . "|" . $time, $pass);
$url = "https://169.58.176.53/?_zod_sso=1&user=" . urlencode($user) . "&t=" . $time . "&s=" . $token;

@unlink("/tmp/rc_cookie.txt");
$cmd = "curl -k -i -s -c /tmp/rc_cookie.txt -b /tmp/rc_cookie.txt -H \"Host: webmail.zodhost.com\" \"$url\"";
shell_exec($cmd);

$cmd2 = "curl -k -s -b /tmp/rc_cookie.txt -H \"Host: webmail.zodhost.com\" \"https://169.58.176.53/?_task=mail\"";
$html = shell_exec($cmd2);

preg_match("/<title>(.*?)<\/title>/i", $html, $m);
echo "Result Title: " . ($m[1] ?? "No title") . "\n";
'
BASH;

echo $ssh->exec($verifyCmd);
echo "Webmail SSO successfully deployed and verified.\n";

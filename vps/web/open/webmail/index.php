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
<?php
use Filegator\Services\Storage\Filesystem as Storage;
use function Hestiacp\quoteshellarg\quoteshellarg;

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
	require_once __DIR__ . '/vendor/autoload.php';
}

if (!defined('APP_PUBLIC_PATH')) define('APP_PUBLIC_PATH', 'dist/');
if (!defined('APP_PUBLIC_DIR')) define('APP_PUBLIC_DIR', __DIR__ . '/dist');
if (!defined('APP_VERSION')) define('APP_VERSION', '7.15.1');
if (!defined('APP_ENV')) define('APP_ENV', 'production');

if (session_status() !== PHP_SESSION_ACTIVE) {
	@session_start();
}

$requested_fm_domain = strtolower(trim((string) ($_GET["domain"] ?? "")));
if ($requested_fm_domain !== "" && preg_match('/^(?!-)[a-z0-9.-]+(?<!-)$/', $requested_fm_domain)) {
	$_SESSION["FM_DOMAIN"] = $requested_fm_domain;
}

$dist_config = require __DIR__ . "/configuration_sample.php";

if (!class_exists("ZodPanelSameDirectoryZipArchiver", false)) {
	class ZodPanelSameDirectoryZipArchiver extends \Filegator\Services\Archiver\Adapters\HestiaZipArchiver
	{
		public function uncompress(string $source, string $destination, Storage $storage)
		{
			$auth = $this->container->get("Filegator\Services\Auth\AuthInterface");
			$v_user = basename($auth->user()->getUsername());
			if ($v_user === "") {
				return;
			}

			$base = "/home/" . $v_user;
			$root = $this->getFileManagerRoot($v_user, $base);
			$source_path = $this->resolveArchivePath($source, $root, $base);
			$real_source = realpath($source_path);

			if ($real_source === false || !str_starts_with($real_source, $base . "/")) {
				return;
			}

			$real_dest = realpath(dirname($real_source));
			if ($real_dest === false || !str_starts_with($real_dest, $base . "/")) {
				throw new \RuntimeException("Invalid extraction destination");
			}

			exec(
				"sudo /usr/local/hestia/bin/v-extract-fs-archive " .
					quoteshellarg($v_user) .
					" " .
					quoteshellarg($real_source) .
					" " .
					quoteshellarg($real_dest),
				$output,
				$return_var,
			);

			if ($return_var !== 0) {
				throw new \RuntimeException(implode("\n", $output) ?: "Archive extraction failed");
			}
		}

		private function getFileManagerRoot(string $v_user, string $base): string
		{
			foreach ($this->getRequestedDomains() as $requested_domain) {
				$domain_root = $base . "/web/" . $requested_domain . "/public_html";
				$real_domain_root = realpath($domain_root);
				if (
					$real_domain_root !== false &&
					is_dir($real_domain_root) &&
					str_starts_with($real_domain_root, $base . "/web/")
				) {
					return $real_domain_root;
				}
			}

			return $base;
		}

		private function resolveArchivePath(string $source, string $root, string $base): string
		{
			$source = trim($source);
			if (str_starts_with($source, "/home/")) {
				return $source;
			}

			$relative_source = ltrim($source, "/");
			if (str_starts_with($relative_source, "web/")) {
				return $base . "/" . $relative_source;
			}

			return rtrim($root, "/") . "/" . $relative_source;
		}

		private function getRequestedDomains(): array
		{
			$domains = [];
			foreach ([($_GET["domain"] ?? ""), ($_SESSION["FM_DOMAIN"] ?? "")] as $domain) {
				$domain = strtolower(trim((string) $domain));
				if ($domain !== "" && preg_match('/^(?!-)[a-z0-9.-]+(?<!-)$/', $domain)) {
					$domains[] = basename($domain);
				}
			}

			return array_values(array_unique($domains));
		}
	}
}

if (!class_exists("\Filegator\Services\Archiver\Adapters\ZodPanelSameDirectoryZipArchiver", false)) {
	class_alias(
		"ZodPanelSameDirectoryZipArchiver",
		"Filegator\Services\Archiver\Adapters\ZodPanelSameDirectoryZipArchiver",
	);
}

$dist_config["public_path"] = "/fm/";
$dist_config["frontend_config"]["app_name"] = "File Manager - ZodPanel";
$dist_config["frontend_config"]["logo"] = "../images/logo.svg";
$dist_config["frontend_config"]["editable_and_previewable_extensions"] = [
	"bsh", "c", "cc", "cpp", "cs", "csh", "css", "cyc", "cv", "htm", "html",
	"java", "js", "m", "mxml", "perl", "php", "pl", "pm", "py", "rb", "sh",
	"sql", "xhtml", "xml", "xsl", "txt", "conf", "env", "json", "md", "log",
	".example", ".htaccess", ".twig", ".tpl", ".yaml", ".yml", "blade.php"
];
$dist_config["frontend_config"]["date_format"] = "YY/MM/DD H:mm:ss";
$dist_config["frontend_config"]["guest_redirection"] = "/login/";
$dist_config["frontend_config"]["upload_max_size"] = 2048 * 1024 * 1024;
$dist_config["frontend_config"]["pagination"] = [100, 50, 25];

$dist_config["services"]["Filegator\Services\Storage\Filesystem"]["config"]["adapter"] = function () {
	if (session_status() !== PHP_SESSION_ACTIVE) {
		@session_start();
	}

	if (!empty($_SESSION["INACTIVE_SESSION_TIMEOUT"])) {
		if ($_SESSION["INACTIVE_SESSION_TIMEOUT"] * 60 + ($_SESSION["LAST_ACTIVITY"] ?? time()) < time()) {
			$v_user = quoteshellarg($_SESSION["user"] ?? "admin");
			$v_session_id = quoteshellarg($_SESSION["token"] ?? "");
			exec("/usr/local/hestia/bin/v-log-user-logout " . $v_user . " " . $v_session_id);
			unset($_SESSION);
			@session_destroy();
			@session_start();
			echo '<meta http-equiv="refresh" content="0; url=/">';
			exit();
		} else {
			$_SESSION["LAST_ACTIVITY"] = time();
		}
	}

	$logged_user = $_SESSION["user"] ?? $_SESSION["USER"] ?? "admin";
	$v_user = $logged_user;

	if (!empty($_SESSION["look"]) && ($_SESSION["userContext"] ?? "") === "admin") {
		$v_user = $_SESSION["look"];
	}

	// 1. Resolve domain owner dynamically if domain parameter is requested
	$requested_domain = $_GET["domain"] ?? $_SESSION["FM_DOMAIN"] ?? "";
	$requested_domain = strtolower(trim((string) $requested_domain));
	$root = "/home/" . $v_user;

	if ($requested_domain !== "" && preg_match('/^(?!-)[a-z0-9.-]+(?<!-)$/', $requested_domain)) {
		// Look up domain owner across system
		$owner = $v_user;
		$found_dirs = glob("/home/*/web/" . basename($requested_domain) . "/public_html");
		if (!empty($found_dirs)) {
			$parts = explode("/", $found_dirs[0]);
			if (!empty($parts[2])) {
				$owner = $parts[2];
			}
		}

		// If admin is browsing or if current user is owner, allow seamless access
		if ($logged_user === "admin" || $logged_user === $owner) {
			$v_user = $owner;
			$_SESSION["look"] = $owner;
			$_SESSION["userContext"] = "admin";
			$_SESSION["FM_DOMAIN"] = $requested_domain;
			$domain_root = "/home/" . $owner . "/web/" . basename($requested_domain) . "/public_html";
			if (is_dir($domain_root)) {
				$root = $domain_root;
			}
		}
	} elseif (isset($_GET["dir"])) {
		$requested_dir = trim((string) $_GET["dir"]);
		if (preg_match('#^/home/([^/]+)#', $requested_dir, $m)) {
			$dir_owner = $m[1];
			if ($logged_user === "admin" || $logged_user === $dir_owner) {
				$v_user = $dir_owner;
				$_SESSION["look"] = $dir_owner;
				$_SESSION["userContext"] = "admin";
				if (is_dir($requested_dir)) {
					$root = $requested_dir;
				}
				unset($_SESSION["FM_DOMAIN"]);
			}
		}
	}

	// Ensure SFTP key exists and has proper permissions
	$ssh_dir = "/home/" . basename($v_user) . "/.ssh";
	$key_file = $ssh_dir . "/hst-filemanager-key";
	if (!file_exists($key_file)) {
		exec("sudo /usr/local/hestia/bin/v-add-user-sftp-key " . quoteshellarg(basename($v_user)) . " 60", $output, $return_var);
		@shell_exec("sudo chmod 0755 " . quoteshellarg($ssh_dir));
		@shell_exec("sudo chmod 0600 " . quoteshellarg($key_file));
	}

	$sftp_port = $_SESSION["SFTP_PORT"] ?? 22;

	return new \League\Flysystem\Sftp\SftpAdapter([
		"host" => "127.0.0.1",
		"port" => intval($sftp_port),
		"username" => basename($v_user),
		"privateKey" => $key_file,
		"root" => $root,
		"timeout" => 15,
		"directoryPerm" => 0755,
	]);
};

$dist_config["services"]["Filegator\Services\Archiver\ArchiverInterface"] = [
	"handler" => "\Filegator\Services\Archiver\Adapters\ZodPanelSameDirectoryZipArchiver",
	"config" => [],
];

$dist_config["services"]["Filegator\Services\Auth\AuthInterface"] = [
	"handler" => "\Filegator\Services\Auth\Adapters\HestiaAuth",
	"config" => [
		"permissions" => ["read", "write", "upload", "download", "batchdownload", "zip", "chmod"],
		"private_repos" => false,
	],
];

$dist_config["services"]["Filegator\Services\View\ViewInterface"]["config"] = [
	"add_to_head" => '
    <style>
        .logo { width: 46px; }
    </style>
    ',
	"add_to_body" => '
<script>
    var checkVueLoaded = setInterval(function() {
        if (document.getElementsByClassName("container").length) {
            clearInterval(checkVueLoaded);
            var navProfile = document.getElementsByClassName("navbar-item profile")[0]; 
            if (navProfile) navProfile.replaceWith(navProfile.cloneNode(true));
            var logoutItem = document.getElementsByClassName("navbar-item logout")[0];
            if (logoutItem) logoutItem.text="Exit to Control Panel \u00BB";
            var div = document.getElementsByClassName("container")[0];
            var callback = function(){
                var item = document.getElementsByClassName("navbar-item logout")[0];
                if (item && item.text != "Exit to Control Panel \u00BB") {
                    var p = document.getElementsByClassName("navbar-item profile")[0]; 
                    if (p) p.replaceWith(p.cloneNode(true));
                    item.text="Exit to Control Panel \u00BB";
                }
            };
            var observer = new MutationObserver(callback);
            observer.observe(div, { childList: true, subtree: true });
        }
    }, 200);
</script>',
];

return $dist_config;
<?php
$TAB = "DB";

// Main include
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";

$panel_host = $_SERVER["HTTP_HOST"] ?? "zodpanel.zodserver.cloud:8083";
[$http_host, $port] = explode(":", $panel_host . ":");
$pma_path = "/phpmyadmin/";

if (!empty($_SESSION["DB_PMA_ALIAS"])) {
	$pma_path = "/" . trim($_SESSION["DB_PMA_ALIAS"], "/") . "/";
}

$pma_url = "https://" . $http_host . $pma_path;

if (empty($user_plain)) {
	$user_plain = $_SESSION["user"] ?? "admin";
}

// Ensure MariaDB user permissions are provisioned using HESTIA_CMD (sudo)
exec(HESTIA_CMD . "v-add-user-pma-temp-user " . escapeshellarg($user_plain) . " 2>/dev/null", $output, $ret);

$time = time();
$temp_pass = "ZodHostPass_" . $user_plain . "_2026!";
$token = md5($user_plain . $temp_pass . $time . "ZODPANEL_SECRET");

$params = [
	"user" => $user_plain,
	"pma_pass" => base64_encode($temp_pass),
	"exp" => $time,
	"token" => $token,
	"zod_all" => 1,
];

if (!empty($_GET["database"])) {
	$params["database"] = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET["database"]);
}

header("Location: " . $pma_url . "hestia-sso.php?" . http_build_query($params));
exit();

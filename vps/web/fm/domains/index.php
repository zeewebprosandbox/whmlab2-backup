<?php
$TAB = "FM";

// Main include
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";

$user = !empty($_SESSION["user"]) ? $_SESSION["user"] : "admin";
$fm_user = $user;
if (!empty($_SESSION["look"]) && $_SESSION["userContext"] === "admin") {
	$fm_user = $_SESSION["look"];
}

$is_global_admin = ($user === "admin" && empty($_SESSION["look"]));
$data = [];

if ($is_global_admin) {
	// List all domains across all users on server
	exec(HESTIA_CMD . "v-list-users json", $user_list_output, $user_list_var);
	$all_users = json_decode(implode("", $user_list_output), true) ?: [];

	foreach (array_keys($all_users) as $u) {
		$dom_output = [];
		exec(HESTIA_CMD . "v-list-web-domains " . escapeshellarg($u) . " json", $dom_output, $dom_var);
		$u_doms = json_decode(implode("", $dom_output), true) ?: [];
		foreach ($u_doms as $d_name => $d_info) {
			$d_info["_USER"] = $u;
			$data[$d_name] = $d_info;
		}
	}
} else {
	exec(HESTIA_CMD . "v-list-web-domains " . escapeshellarg($fm_user) . " json", $output, $return_var);
	$data = json_decode(implode("", $output), true) ?: [];
	foreach ($data as $d_name => &$d_info) {
		$d_info["_USER"] = $fm_user;
	}
	unset($d_info);
}

// User details
exec(HESTIA_CMD . "v-list-user " . escapeshellarg($fm_user) . " json", $user_output, $user_return_var);
$user_data = json_decode(implode("", $user_output), true)[$fm_user] ?? [];

// Export to GLOBALS for render_page
$GLOBALS["data"] = $data;
$GLOBALS["fm_user"] = $fm_user;
$GLOBALS["is_global_admin"] = $is_global_admin;
$GLOBALS["user_data"] = $user_data;

// Render page
render_page($user, $TAB, "list_fm_domains");

// Back uri
$_SESSION["back"] = $_SERVER["REQUEST_URI"];
<?php
$TAB = "WEB";
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";
exec(HESTIA_CMD . "v-list-web-domains " . escapeshellarg($user) . " 'json'", $output, $return_var);
$data = json_decode(implode("", $output), true);
render_page($user, $TAB, "list_apps_dashboard");
$_SESSION["back"] = $_SERVER["REQUEST_URI"];

path = "../zodpanel-hestia-custom-backup/usr/local/hestia/web/api/whmlab/index.php"
with open(path, "r") as f:
    code = f.read()

node_routes = """
if ($method === "GET" && $path === "nodejs/apps") {
	$user = (string) ($input["username"] ?? $_GET["username"] ?? "admin");
	$apps = whmpanel_run_soft(["v-zodpanel-node-app", "list", $user], true);
	whmpanel_json($apps["data"] ?? $apps);
}

if ($method === "POST" && $path === "nodejs/create") {
	$user = (string) ($input["username"] ?? "admin");
	$domain = (string) ($input["domain"] ?? "");
	$entry = (string) ($input["entry"] ?? "app.js");
	$port = (string) ($input["port"] ?? "");
	whmpanel_json(whmpanel_run_soft(["v-zodpanel-node-app", "create", $user, $domain, $entry, $port], true));
}

if ($method === "POST" && $path === "nodejs/action") {
	$action = (string) ($input["action"] ?? "restart");
	$user = (string) ($input["username"] ?? "admin");
	$domain = (string) ($input["domain"] ?? "");
	whmpanel_json(whmpanel_run_soft(["v-zodpanel-node-app", $action, $user, $domain], true));
}
"""

if "nodejs/apps" not in code:
    code = code.replace("if ($method === \"GET\" && $path === \"server/info\") {", node_routes + "\nif ($method === \"GET\" && $path === \"server/info\") {")

code = code.replace('"nodejs" => false,', '"nodejs" => true,')

with open(path, "w") as f:
    f.write(code)

print("INDEX_PHP_NODEJS_ROUTES_ADDED")

path = "../zodpanel-hestia-custom-backup/usr/local/hestia/web/api/whmlab/index.php"
with open(path, "r") as f:
    code = f.read()

terminal_route = """if ($method === "POST" && $path === "terminal/exec") {
	$user = (string) ($input["username"] ?? "admin");
	$cmd = (string) ($input["command"] ?? "pwd");
	$userHome = "/home/" . preg_replace("/[^a-zA-Z0-9_-]/", "", $user);
	if (!is_dir($userHome)) {
		whmpanel_error("User directory does not exist", 400);
	}
	$escapedCmd = escapeshellarg("cd " . escapeshellarg($userHome) . " && " . $cmd);
	$out = shell_exec("bash -c " . $escapedCmd . " 2>&1");
	whmpanel_json(["success" => true, "output" => (string) $out]);
}"""

import re
code = re.sub(r"if \(\$method === \"POST\" && \$path === \"terminal/exec\"\) \{.*?\n\}", terminal_route, code, flags=re.DOTALL)

with open(path, "w") as f:
    f.write(code)

print("TERMINAL_EXEC_UPDATED")

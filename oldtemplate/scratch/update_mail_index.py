import re

with open('./scratch/add_mail_index.php', 'r') as f:
    content = f.read()

# We want to inject auto-provisioning right before // Add Mail Account
injection = """	// Auto-provision Mail Domain if it does not exist
	if (empty($_SESSION["error_msg"])) {
		exec(HESTIA_CMD . "v-list-mail-domain " . escapeshellarg($user) . " " . $v_domain . " 'json'", $output_check, $return_var_check);
		if ($return_var_check != 0) {
			exec(HESTIA_CMD . "v-add-mail-domain " . escapeshellarg($user) . " " . $v_domain . " 'yes' 'yes'", $output_add, $return_var_add);
		}
	}

	// Add Mail Account"""

content = content.replace("// Add Mail Account", injection, 1)

with open('./scratch/add_mail_index.php', 'w') as f:
    f.write(content)

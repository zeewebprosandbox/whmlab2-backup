import re

with open('./scratch/add_mail_acc.php', 'r') as f:
    content = f.read()

# Replace the v-list-mail-domains call with v-list-web-domains
old_php = """					<?php
					exec(HESTIA_CMD . "v-list-mail-domains " . escapeshellarg($user) . " 'json'", $output_domains);
					$mail_domains = json_decode(implode("", $output_domains), true);
					unset($output_domains);
					?>"""
                    
new_php = """					<?php
					exec(HESTIA_CMD . "v-list-web-domains " . escapeshellarg($user) . " 'json'", $output_domains);
					$mail_domains = json_decode(implode("", $output_domains), true);
					unset($output_domains);
					?>"""

content = content.replace(old_php, new_php)

with open('./scratch/add_mail_acc.php', 'w') as f:
    f.write(content)

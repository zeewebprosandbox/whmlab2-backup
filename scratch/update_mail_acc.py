import re

with open('./scratch/add_mail_acc.php', 'r') as f:
    content = f.read()

# The block to replace
old_block = """					<div class="u-mb10">
						<label for="v_domain" class="form-label"><?= tohtml( _("Domain")) ?></label>
						<input type="text" class="form-control" name="v_domain" id="v_domain" value="<?= tohtml(trim($v_domain, "'")) ?>" disabled>
						<input type="hidden" name="v_domain" value="<?= tohtml(trim($v_domain, "'")) ?>">
					</div>
					<div class="u-mb10">
						<label for="v_account" class="form-label"><?= tohtml( _("Account")) ?></label>
						<input type="text" class="form-control js-account-input" name="v_account" id="v_account" value="<?= tohtml(trim($v_account, "'")) ?>" required>
					</div>"""

new_block = """					<?php
					exec(HESTIA_CMD . "v-list-mail-domains " . escapeshellarg($user) . " 'json'", $output_domains);
					$mail_domains = json_decode(implode("", $output_domains), true);
					unset($output_domains);
					?>
					<div class="u-mb10">
						<label for="v_account" class="form-label"><?= tohtml( _("Email Address")) ?></label>
						<div style="display: flex; align-items: center; gap: 10px;">
							<input type="text" class="form-control js-account-input" name="v_account" id="v_account" value="<?= tohtml(trim($v_account, "'")) ?>" placeholder="username" required style="flex: 1;">
							<span style="font-weight: bold; color: #94a3b8;">@</span>
							<select class="form-select" name="v_domain" id="v_domain" required style="flex: 1;">
								<?php foreach($mail_domains as $domain => $data) { ?>
									<option value="<?= tohtml($domain) ?>" <?= ($domain == trim($v_domain, "'")) ? 'selected' : '' ?>><?= tohtml($domain) ?></option>
								<?php } ?>
							</select>
						</div>
					</div>"""

content = content.replace(old_block, new_block)

with open('./scratch/add_mail_acc.php', 'w') as f:
    f.write(content)

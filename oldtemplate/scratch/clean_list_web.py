import re

with open('./scratch/list_web.php', 'r') as f:
    content = f.read()

# We need to replace the content between <ul class="units-table-row-actions"> and </ul>
# using regex, but since it's a large block with nested tags, it's safer to use split/replace
# Let's find the start and end indices.

start_tag = '<ul class="units-table-row-actions">'
end_tag = '</ul>'

start_idx = content.find(start_tag)
if start_idx != -1:
    end_idx = content.find(end_tag, start_idx)
    if end_idx != -1:
        end_idx += len(end_tag)
        
        new_actions = """<ul class="units-table-row-actions" style="display: flex; gap: 12px; justify-content: flex-end; align-items: center;">
						<?php if ($read_only !== "true") { ?>
						<li class="units-table-row-action" data-key-action="href" style="margin:0;">
							<a class="button button-secondary" href="/edit/web/?<?= tohtml(http_build_query(["domain" => $key, "token" => $_SESSION["token"]])) ?>" title="<?= tohtml( _("Manage Domain")) ?>" style="padding: 6px 14px; height: auto; min-height: 0; line-height: normal; border-radius: 6px;">
								<i class="fas fa-cog"></i> <?= tohtml( _("Manage")) ?>
							</a>
						</li>
						<?php } ?>
						<li class="units-table-row-action" data-key-action="href" style="margin:0;">
							<a class="units-table-row-action-link" href="/fm/?<?= tohtml(http_build_query(["domain" => $key])) ?>" target="_blank" rel="noopener" title="<?= tohtml( _("File Manager")) ?>" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: rgba(255,255,255,0.05); border-radius: 6px; border: 1px solid rgba(255,255,255,0.1);">
								<i class="fas fa-folder-open icon-yellow" style="font-size: 14px; margin: 0;"></i>
							</a>
						</li>
					</ul>"""
        
        content = content[:start_idx] + new_actions + content[end_idx:]

with open('./scratch/list_web.php', 'w') as f:
    f.write(content)

import re

with open('./scratch/list_web.php', 'r') as f:
    content = f.read()

old_fm_block = """						<li class="units-table-row-action" data-key-action="href" style="margin:0;">
							<a class="units-table-row-action-link" href="/fm/?<?= tohtml(http_build_query(["domain" => $key])) ?>" target="_blank" rel="noopener" title="<?= tohtml( _("File Manager")) ?>" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: rgba(255,255,255,0.05); border-radius: 6px; border: 1px solid rgba(255,255,255,0.1);">
								<i class="fas fa-folder-open icon-yellow" style="font-size: 14px; margin: 0;"></i>
							</a>
						</li>
					</ul>"""

new_fm_block = """						<li class="units-table-row-action" data-key-action="href" style="margin:0;">
							<a class="units-table-row-action-link" href="/fm/?<?= tohtml(http_build_query(["domain" => $key])) ?>" target="_blank" rel="noopener" title="<?= tohtml( _("File Manager")) ?>" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: rgba(255,255,255,0.05); border-radius: 6px; border: 1px solid rgba(255,255,255,0.1);">
								<i class="fas fa-folder-open icon-yellow" style="font-size: 14px; margin: 0;"></i>
							</a>
						</li>
						<?php if ($read_only !== "true") { ?>
						<li class="units-table-row-action shortcut-delete" data-key-action="js" style="margin:0;">
							<a class="units-table-row-action-link data-controls js-confirm-action" href="/delete/web/?<?= tohtml(http_build_query(["domain" => $key, "token" => $_SESSION["token"]])) ?>" title="<?= tohtml( _("Delete")) ?>" data-confirm-title="<?= tohtml( _("Delete")) ?>" data-confirm-message="<?= tohtml(sprintf(_("Are you sure you want to delete domain %s?"), $key)) ?>" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: rgba(239,68,68,0.1); border-radius: 6px; border: 1px solid rgba(239,68,68,0.2);">
								<i class="fas fa-trash icon-red" style="font-size: 14px; margin: 0;"></i>
							</a>
						</li>
						<?php } ?>
					</ul>"""

content = content.replace(old_fm_block, new_fm_block)

with open('./scratch/list_web.php', 'w') as f:
    f.write(content)

<!-- Begin toolbar -->
<div class="toolbar">
	<div class="toolbar-inner">
		<div class="toolbar-buttons">
			<?php if ($read_only !== "true") { ?>
				<a href="/add/web/" class="button button-secondary js-button-create">
					<i class="fas fa-circle-plus icon-green"></i><?= tohtml( _("Add Web Domain")) ?>
				</a>
			<?php } ?>
		</div>
		<div class="toolbar-right">
			<div class="toolbar-sorting">
				<button class="toolbar-sorting-toggle js-toggle-sorting-menu" type="button" title="<?= tohtml( _("Sort items")) ?>">
					<?= tohtml( _("Sort by")) ?>:
					<span class="u-text-bold">
							<?php if ($_SESSION['userSortOrder'] === 'name') { $label = _('Name'); } else { $label = _('Date'); } ?>
						<?= tohtml($label) ?> <i class="fas fa-arrow-down-a-z"></i>
					</span>
				</button>
				<ul class="toolbar-sorting-menu js-sorting-menu u-hidden">
					<li data-entity="sort-bandwidth" data-sort-as-int="1">
						<span class="name"><?= tohtml( _("Bandwidth")) ?> <i class="fas fa-arrow-down-a-z"></i></span><span class="up"><i class="fas fa-arrow-up-a-z"></i></span>
					</li>
					<li data-entity="sort-date" data-sort-as-int="1">
						<span class="name <?php if ($_SESSION['userSortOrder'] === 'date') { echo 'active'; } ?>"><?= tohtml( _("Date")) ?> <i class="fas fa-arrow-down-a-z"></i></span><span class="up"><i class="fas fa-arrow-up-a-z"></i></span>
					</li>
					<li data-entity="sort-disk" data-sort-as-int="1">
						<span class="name"><?= tohtml( _("Disk")) ?> <i class="fas fa-arrow-down-a-z"></i></span><span class="up"><i class="fas fa-arrow-up-a-z"></i></span>
					</li>
					<li data-entity="sort-name">
						<span class="name <?php if ($_SESSION['userSortOrder'] === 'name') { echo 'active'; } ?>"><?= tohtml( _("Name")) ?> <i class="fas fa-arrow-down-a-z"></i></span><span class="up"><i class="fas fa-arrow-up-a-z"></i></span>
					</li>
					<li data-entity="sort-ip" data-sort-as-int="1">
						<span class="name"><?= tohtml( _("IP Address")) ?> <i class="fas fa-arrow-down-a-z"></i></span><span class="up"><i class="fas fa-arrow-up-a-z"></i></span>
					</li>
				</ul>
				<?php if ($read_only !== "true") { ?>
					<form x-data x-bind="BulkEdit" action="/bulk/web/" method="post">
						<input type="hidden" name="token" value="<?= tohtml($_SESSION["token"]) ?>">
						<select class="form-select" name="action">
							<option value=""><?= tohtml( _("Apply to selected")) ?></option>
							<?php if ($_SESSION["userContext"] === "admin") { ?>
								<option value="rebuild"><?= tohtml( _("Rebuild")) ?></option>
							<?php } ?>
							<option value="suspend"><?= tohtml( _("Suspend")) ?></option>
							<option value="unsuspend"><?= tohtml( _("Unsuspend")) ?></option>
							<?php if ($_SESSION["PROXY_SYSTEM"] == "nginx" || $_SESSION["WEB_SYSTEM"] == "nginx") { ?>
								<option value="purge"><?= tohtml( _("Purge Nginx Cache")) ?></option>
							<?php } ?>
							<option value="delete"><?= tohtml( _("Delete")) ?></option>
						</select>
						<button type="submit" class="toolbar-input-submit" title="<?= tohtml( _("Apply to selected")) ?>">
							<i class="fas fa-arrow-right"></i>
						</button>
					</form>
				<?php } ?>
			</div>
			<div class="toolbar-search">
				<form action="/search/" method="get">
					<input type="hidden" name="token" value="<?= tohtml($_SESSION["token"]) ?>">
					<input type="search" class="form-control js-search-input" name="q" value="<?= tohtml($_GET['q'] ?? '') ?>" title="<?= tohtml( _("Search")) ?>">
					<button type="submit" class="toolbar-input-submit" title="<?= tohtml( _("Search")) ?>">
						<i class="fas fa-magnifying-glass"></i>
					</button>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- End toolbar -->

	<div class="container">

		<h1 class="u-text-center u-hide-desktop u-mt20 u-pr30 u-mb20 u-pl30"><?= tohtml( _("Web Domains")) ?></h1>

		<div class="units-table js-units-container">
		<div class="units-table-header">
				<div class="units-table-cell">
					<input type="checkbox" class="js-toggle-all-checkbox" title="<?= tohtml( _("Select all")) ?>"<?= $display_mode === "disabled" ? " disabled" : "" ?>>
				</div>
			<div class="units-table-cell"><?= tohtml( _("Name")) ?></div>
			<div class="units-table-cell"></div>
			<div class="units-table-cell u-text-center"><?= tohtml( _("IP Address")) ?></div>
			<div class="units-table-cell u-text-center"><?= tohtml( _("Disk")) ?></div>
			<div class="units-table-cell u-text-center"><?= tohtml( _("Bandwidth")) ?></div>
			<div class="units-table-cell u-text-center"><?= tohtml( _("SSL")) ?></div>
			<div class="units-table-cell u-text-center"><?= tohtml( _("Statistics")) ?></div>
		</div>

		<!-- Begin web domain list item loop -->
		<?php
			foreach ($data as $key => $value) {
				++$i;
				if ($data[$key]['SUSPENDED'] == 'yes') {
					$status = 'suspended';
					$spnd_action = 'unsuspend';
					$spnd_action_title = _('Unsuspend');
					$spnd_icon = 'fa-play';
					$spnd_icon_class = 'icon-green';
					$spnd_confirmation = _('Are you sure you want to unsuspend domain %s?');
				} else {
					$status = 'active';
					$spnd_action = 'suspend';
					$spnd_action_title = _('Suspend');
					$spnd_icon = 'fa-pause';
					$spnd_icon_class = 'icon-highlight';
					$spnd_confirmation = _('Are you sure you want to suspend domain %s?');
				}
				if (!empty($data[$key]['SSL_HOME'])) {
					if ($data[$key]['SSL_HOME'] == 'same') {
						$ssl_home = 'public_html';
					} else {
						$ssl_home = 'public_shtml';
					}
				} else {
					$ssl_home = '';
				}
				$web_stats='no';
				if (!empty($data[$key]['STATS'])) {
					$web_stats=$data[$key]['STATS'];
				}
				$ftp_user='no';
				if (!empty($data[$key]['FTP_USER'])) {
					$ftp_user=$data[$key]['FTP_USER'];
				}
				if (strlen($ftp_user) > 24 ) {
					$ftp_user = str_replace(':', ', ', $ftp_user);
					$ftp_user = substr($ftp_user, 0, 24);
					$ftp_user = trim($ftp_user, ":");
					$ftp_user = str_replace(':', ', ', $ftp_user);
					$ftp_user = $ftp_user.", ...";
				} else {
					$ftp_user = str_replace(':', ', ', $ftp_user);
				}

				$backend_support='no';
				if (!empty($data[$key]['BACKEND'])) {
					$backend_support='yes';
				}

				$proxy_support='no';
				if (!empty($data[$key]['PROXY'])) {
					$proxy_support='yes';
				}
				if (strlen($data[$key]['PROXY_EXT']) > 24 ) {
					$proxy_ext_title = str_replace(',', ', ', $data[$key]['PROXY_EXT']);
					$proxy_ext = substr($data[$key]['PROXY_EXT'], 0, 24);
					$proxy_ext = trim($proxy_ext, ",");
					$proxy_ext = str_replace(',', ', ', $proxy_ext);
					$proxy_ext = $proxy_ext.", ...";
				} else {
					$proxy_ext_title = '';
					$proxy_ext = str_replace(',', ', ', $data[$key]['PROXY_EXT']);
				}
				if ($data[$key]['SUSPENDED'] === 'yes') {
					if ($data[$key]['SSL'] == 'no') {
						$icon_ssl = 'fas fa-circle-xmark';
						$title_ssl = _('Disabled');
					}
					if ($data[$key]['SSL'] == 'yes') {
						$icon_ssl = 'fas fa-circle-check';
						$title_ssl = _('Enabled');
					}
					if ($web_stats == 'no') {
						$icon_webstats = 'fas fa-circle-xmark';
						$title_webstats = _('Disabled');
					} else {
						$icon_webstats = 'fas fa-circle-check';
						$title_webstats = _('Enabled');
					}
				} else {
					if ($data[$key]['SSL'] == 'no') {
						$icon_ssl = 'fas fa-circle-xmark icon-red';
						$title_ssl = _('Disabled');
					}
					if ($data[$key]['SSL'] == 'yes') {
						$icon_ssl = 'fas fa-circle-check icon-green';
						$title_ssl = _('Enabled');
					}
					if ($web_stats == 'no') {
						$icon_webstats = 'fas fa-circle-xmark icon-red';
						$title_webstats = _('Disabled');
					} else {
						$icon_webstats = 'fas fa-circle-check icon-green';
						$title_webstats = _('Enabled');
					}
				}
				$has_ssl = filter_var($data[$key]['SSL'], FILTER_VALIDATE_BOOL);
				$vstats_scheme = $has_ssl ? 'https' : 'http';
			?>
			<div class="units-table-row <?php if ($data[$key]['SUSPENDED'] == 'yes') echo 'disabled'; ?> js-unit"
				data-sort-ip="<?= tohtml(str_replace(".", "", $data[$key]["IP"])) ?>"
				data-sort-date="<?= tohtml(strtotime($data[$key]["DATE"] . " " . $data[$key]["TIME"])) ?>"
				data-sort-name="<?= tohtml($key) ?>"
				data-sort-bandwidth="<?= tohtml($data[$key]["U_BANDWIDTH"]) ?>"
				data-sort-disk="<?= tohtml($data[$key]["U_DISK"]) ?>">
				<div class="units-table-cell">
					<div>
						<input id="check<?= tohtml($i) ?>" class="js-unit-checkbox" type="checkbox" title="<?= tohtml( _("Select")) ?>" name="domain[]" value="<?= tohtml($key) ?>"<?= $display_mode === "disabled" ? " disabled" : "" ?>>
						<label for="check<?= tohtml($i) ?>" class="u-hide-desktop"><?= tohtml( _("Select")) ?></label>
					</div>
				</div>
				<div class="units-table-cell units-table-heading-cell u-text-bold">
					<span class="u-hide-desktop"><?= tohtml( _("Name")) ?>:</span>
					<?php if ($read_only === "true") { ?>
						<?= tohtml($key) ?>
					<?php } else {
						$aliases = explode(',', $data[$key]['ALIAS']);
						$alias_new = array();
						foreach($aliases as $alias){
							if ($alias != 'www.'.$key) {
								$alias_new[] = trim($alias);
							}
						}
						?>
						<a href="/edit/web/?<?= tohtml(http_build_query(["domain" => $key, "token" => $_SESSION['token']])) ?>" title="<?= tohtml( _("Edit Domain")) ?>: <?= tohtml($key) ?>">
							<?= tohtml($key) ?>
								<?php
									if (!empty($alias_new) && !empty($data[$key]['ALIAS'])) {
										$aliases = implode(', ', $alias_new);
										echo "<p class='hint u-max-width300 u-text-truncate'>(" . tohtml($aliases) . ")</p>";
									}
								?>
							</a>
						<?php } ?>
				</div>
				<div class="units-table-cell">
					<ul class="units-table-row-actions" style="display: flex; gap: 12px; justify-content: flex-end; align-items: center;">
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
						<?php if ($read_only !== "true") { ?>
						<li class="units-table-row-action shortcut-delete" data-key-action="js" style="margin:0;">
							<a class="units-table-row-action-link data-controls js-confirm-action" href="/delete/web/?<?= tohtml(http_build_query(["domain" => $key, "token" => $_SESSION["token"]])) ?>" title="<?= tohtml( _("Delete")) ?>" data-confirm-title="<?= tohtml( _("Delete")) ?>" data-confirm-message="<?= tohtml(sprintf(_("Are you sure you want to delete domain %s?"), $key)) ?>" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: rgba(239,68,68,0.1); border-radius: 6px; border: 1px solid rgba(239,68,68,0.2);">
								<i class="fas fa-trash icon-red" style="font-size: 14px; margin: 0;"></i>
							</a>
						</li>
						<?php } ?>
					</ul>
				</div>
				<div class="units-table-cell u-text-center-desktop">
					<span class="u-hide-desktop u-text-bold"><?= tohtml( _("IP Address")) ?>:</span>
					<?= tohtml(empty($ips[$data[$key]["IP"]]["NAT"]) ? $data[$key]["IP"] : "{$ips[$data[$key]["IP"]]["NAT"]}") ?>
				</div>
				<div class="units-table-cell u-text-center-desktop">
					<span class="u-hide-desktop u-text-bold"><?= tohtml( _("Disk")) ?>:</span>
					<span class="u-text-bold">
						<?= tohtml(humanize_usage_size($data[$key]["U_DISK"])) ?>
					</span>
					<span class="u-text-small">
						<?= tohtml(humanize_usage_measure($data[$key]["U_DISK"])) ?>
					</span>
				</div>
				<div class="units-table-cell u-text-center-desktop">
					<span class="u-hide-desktop u-text-bold"><?= tohtml( _("Bandwidth")) ?>:</span>
					<span class="u-text-bold">
						<?= tohtml(humanize_usage_size($data[$key]["U_BANDWIDTH"])) ?>
					</span>
					<span class="u-text-small">
						<?= tohtml(humanize_usage_measure($data[$key]["U_BANDWIDTH"])) ?>
					</span>
				</div>
				<div class="units-table-cell u-text-center-desktop">
					<span class="u-hide-desktop u-text-bold"><?= tohtml( _("SSL")) ?>:</span>
					<i class="fas <?= tohtml($icon_ssl) ?>" title="<?= tohtml($title_ssl) ?>"></i>
				</div>
				<div class="units-table-cell u-text-center-desktop">
					<span class="u-hide-desktop u-text-bold"><?= tohtml( _("Statistics")) ?>:</span>
					<i class="fas <?= tohtml($icon_webstats) ?>" title="<?= tohtml($title_webstats) ?>"></i>
				</div>
			</div>
		<?php } ?>
	</div>

	<div class="units-table-footer">
		<p>
			<?php printf(ngettext("%d web domain", "%d web domains", $i), $i); ?>
		</p>
	</div>

</div>

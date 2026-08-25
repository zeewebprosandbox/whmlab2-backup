<?php

require __DIR__ . '/../vendor/autoload.php';

$sftp = new \phpseclib3\Net\SFTP('169.58.176.53', 22);
if (!$sftp->login('root', 'Remixbrown99@')) {
    die("SSH login failed\n");
}

echo "1. Deploying /usr/local/hestia/web/fm/domains/index.php...\n";

$indexPhp = <<<'PHP'
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
PHP;

$sftp->put('/usr/local/hestia/web/fm/domains/index.php', $indexPhp);

echo "2. Deploying /usr/local/hestia/web/templates/pages/list_fm_domains.php...\n";

$listFmDomains = <<<'PHP'
<!-- Begin toolbar -->
<div class="toolbar">
	<div class="toolbar-inner">
		<div class="toolbar-buttons">
			<a href="/fm/?dir=<?= urlencode('/home/' . $fm_user) ?>" class="button button-primary" style="display: inline-flex; align-items: center; gap: 8px;">
				<i class="fas fa-folder-tree"></i> <?= tohtml(_("Explore Home (/home/" . $fm_user . ")")) ?>
			</a>
			<a href="/list/web/" class="button button-secondary" style="display: inline-flex; align-items: center; gap: 8px;">
				<i class="fas fa-arrow-left icon-blue"></i> <?= tohtml(_("Web Domains")) ?>
			</a>
		</div>
		<div class="toolbar-right">
			<div class="toolbar-search">
				<input type="search" id="fm-domain-search" class="form-control" placeholder="<?= tohtml(_("Filter websites...")) ?>" onkeyup="filterFmDomains(this.value)">
			</div>
		</div>
	</div>
</div>
<!-- End toolbar -->

<div class="container zod-fm-domains-wrapper" style="padding: 24px 20px 60px 20px; max-width: 1320px; margin: 0 auto;">

	<!-- Top Hero Header -->
	<div class="zod-fm-header-card" style="background: linear-gradient(135deg, rgba(30, 27, 75, 0.7) 0%, rgba(15, 23, 42, 0.9) 100%); border: 1px solid rgba(99, 102, 241, 0.25); border-radius: 16px; padding: 26px 32px; margin-bottom: 28px; position: relative; overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.25);">
		<div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 20px; position: relative; z-index: 2;">
			<div>
				<div style="display: inline-flex; align-items: center; gap: 8px; padding: 4px 12px; border-radius: 20px; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); color: #a5b4fc; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 10px;">
					<i class="fas fa-folder-open"></i> <?= tohtml(_("File Manager Direct Navigator")) ?>
				</div>
				<h1 style="font-size: 24px; font-weight: 800; color: #ffffff; margin: 0 0 6px 0; letter-spacing: -0.02em;">
					<?= tohtml(_("Website Directories & Document Roots")) ?>
				</h1>
				<p style="font-size: 13.5px; color: #94a3b8; margin: 0;">
					<?php if ($is_global_admin) { ?>
						<?= tohtml(_("Managing all websites across the server")) ?> • <strong style="color: #38bdf8;"><?= count($data) ?> <?= tohtml(_("total websites online")) ?></strong>
					<?php } else { ?>
						<?= tohtml(_("Browsing account")) ?>: <strong style="color: #e2e8f0; font-family: monospace; font-size: 13px;"><?= tohtml($fm_user) ?></strong> • <?= tohtml(_("Root")) ?>: <code style="background: rgba(0,0,0,0.3); padding: 2px 6px; border-radius: 4px; color: #38bdf8;">/home/<?= tohtml($fm_user) ?>/</code>
					<?php } ?>
				</p>
			</div>
			<div style="display: flex; gap: 10px; align-items: center;">
				<a href="/fm/?dir=<?= urlencode('/home/' . $fm_user) ?>" class="button button-primary" style="padding: 10px 18px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; border-radius: 10px; box-shadow: 0 4px 14px rgba(99,102,241,0.35);">
					<i class="fas fa-folder-tree"></i> <?= tohtml(_("Open Root Filesystem")) ?>
				</a>
			</div>
		</div>
	</div>

	<!-- Section Header -->
	<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
		<h2 style="font-size: 17px; font-weight: 700; color: #f8fafc; margin: 0; display: flex; align-items: center; gap: 10px;">
			<i class="fas fa-globe" style="color: #6366f1;"></i> <?= tohtml(_("Available Websites")) ?> <span style="background: rgba(99,102,241,0.2); color: #818cf8; font-size: 12px; padding: 2px 8px; border-radius: 10px; font-weight: 700;"><?= count($data) ?></span>
		</h2>
		<span style="font-size: 12.5px; color: #64748b;"><?= tohtml(_("Directly opens public_html in File Manager")) ?></span>
	</div>

	<!-- Domains Grid -->
	<?php if (empty($data)) { ?>
		<div style="background: #13141f; border: 1px dashed rgba(255,255,255,0.15); border-radius: 16px; padding: 56px 24px; text-align: center;">
			<i class="fas fa-folder-open" style="font-size: 52px; color: #6366f1; margin-bottom: 16px; opacity: 0.8;"></i>
			<h3 style="font-size: 19px; color: #ffffff; font-weight: 700; margin-bottom: 8px;"><?= tohtml(_("No Website Domains Found")) ?></h3>
			<p style="color: #94a3b8; font-size: 13.5px; max-width: 440px; margin: 0 auto 24px auto;"><?= tohtml(_("Add your first domain in the Web section to automatically generate its document root and web directories.")) ?></p>
			<a href="/add/web/" class="button button-primary" style="padding: 10px 22px; font-size: 13px; font-weight: 600;"><i class="fas fa-plus"></i> <?= tohtml(_("Add Web Domain")) ?></a>
		</div>
	<?php } else { ?>
		<div id="fm-domains-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 18px;">
			<?php
			$idx = 0;
			foreach ($data as $domain => $d) {
				$idx++;
				$isDefault = ($idx === 1);
				$disk = $d['U_DISK'] ?? 0;
				$bw = $d['U_BANDWIDTH'] ?? 0;
				$ssl = ($d['SSL'] ?? 'no') === 'yes';
				$domainOwner = $d['_USER'] ?? $fm_user;
				$docRoot = "/home/{$domainOwner}/web/{$domain}/public_html";
			?>
				<div class="fm-domain-item" data-domain="<?= strtolower(htmlspecialchars($domain)) ?>" style="background: #13141f; border: 1px solid <?= $isDefault ? 'rgba(99,102,241,0.35)' : 'rgba(255,255,255,0.08)' ?>; border-radius: 14px; padding: 22px; display: flex; flex-direction: column; justify-content: space-between; gap: 16px; transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease; box-shadow: 0 4px 16px rgba(0,0,0,0.15);">
					<div>
						<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
							<div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(99, 102, 241, 0.12); border: 1px solid rgba(99, 102, 241, 0.25); display: flex; align-items: center; justify-content: center; color: #818cf8; font-size: 19px;">
								<i class="fas fa-globe"></i>
							</div>
							<div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
								<span style="font-size: 10.5px; font-weight: 700; color: #a5b4fc; background: rgba(99, 102, 241, 0.12); border: 1px solid rgba(99, 102, 241, 0.25); padding: 2px 8px; border-radius: 6px;">
									@<?= tohtml($domainOwner) ?>
								</span>
								<?php if ($ssl) { ?>
									<span style="font-size: 10px; font-weight: 700; color: #10b981; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25); padding: 2px 6px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;">
										<i class="fas fa-lock" style="font-size: 9px;"></i> SSL
									</span>
								<?php } ?>
							</div>
						</div>

						<h3 style="font-size: 16px; font-weight: 800; color: #f8fafc; margin: 0 0 5px 0; letter-spacing: -0.01em; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
							<?= tohtml($domain) ?>
						</h3>
						<div style="font-size: 11.5px; font-family: monospace; color: #38bdf8; margin-bottom: 10px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
							/web/<?= tohtml($domain) ?>/public_html
						</div>
						<div style="font-size: 11.5px; color: #64748b; display: flex; gap: 12px; align-items: center;">
							<span><strong style="color: #94a3b8;"><?= tohtml(humanize_usage_size($disk)) ?></strong> <?= tohtml(humanize_usage_measure($disk)) ?></span>
							<span>•</span>
							<span><strong style="color: #94a3b8;"><?= tohtml(humanize_usage_size($bw)) ?></strong> <?= tohtml(humanize_usage_measure($bw)) ?> <?= tohtml(_("BW")) ?></span>
						</div>
					</div>

					<div style="padding-top: 14px; border-top: 1px solid rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: space-between; gap: 8px;">
						<a href="/fm/?domain=<?= urlencode($domain) ?>" class="button button-primary" style="flex: 1; text-align: center; justify-content: center; font-size: 12.5px; font-weight: 600; padding: 9px 14px; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px;">
							<i class="fas fa-folder-open"></i> <?= tohtml(_("Open public_html")) ?>
						</a>
						<a href="/fm/?dir=<?= urlencode('/home/' . $domainOwner . '/web/' . $domain) ?>" class="button button-secondary" title="<?= tohtml(_("Open Domain Root")) ?>" style="padding: 9px 12px; border-radius: 8px;">
							<i class="fas fa-ellipsis-h"></i>
						</a>
					</div>
				</div>
			<?php } ?>
		</div>
	<?php } ?>
</div>

<script>
function filterFmDomains(query) {
	query = query.toLowerCase().trim();
	const items = document.querySelectorAll('.fm-domain-item');
	items.forEach(el => {
		const domain = el.getAttribute('data-domain') || '';
		if (!query || domain.includes(query)) {
			el.style.display = 'flex';
		} else {
			el.style.display = 'none';
		}
	});
}
</script>
PHP;

$sftp->put('/usr/local/hestia/web/templates/pages/list_fm_domains.php', $listFmDomains);

echo "3. Updating /usr/local/hestia/web/fm/dist/css/hst-custom.css with clean padding and space fitting...\n";

$customCss = <<<'CSS'
/* Clean file manager fit with spacious padding */
html, body {
	background-color: #0b0c10 !important;
	color: #e2e8f0 !important;
	margin: 0 !important;
	padding: 0 !important;
	height: 100% !important;
	box-sizing: border-box !important;
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
}

#app {
	width: 100% !important;
	max-width: 100% !important;
	min-height: 100vh !important;
	box-sizing: border-box !important;
	padding: 16px 24px 40px 24px !important;
	margin: 0 auto !important;
}

.container {
	max-width: 100% !important;
	width: 100% !important;
	padding: 0 8px !important;
	margin: 0 auto !important;
}

#browser {
	margin: 12px auto 40px auto !important;
	background: #13141f !important;
	border: 1px solid rgba(255, 255, 255, 0.08) !important;
	border-radius: 14px !important;
	padding: 24px !important;
	box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35) !important;
}

.navbar {
	background: transparent !important;
	padding: 8px 0 16px 0 !important;
	border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
	margin-bottom: 18px !important;
}

.b-table .table {
	border-radius: 10px !important;
	overflow: hidden !important;
	border: 1px solid rgba(255, 255, 255, 0.06) !important;
}

.table.is-hoverable tbody tr td {
	padding: 11px 16px !important;
	vertical-align: middle !important;
	font-size: 13.5px !important;
}

#multi-actions {
	min-height: 48px !important;
	margin-bottom: 20px !important;
	display: flex !important;
	flex-wrap: wrap !important;
	gap: 10px !important;
	align-items: center !important;
}

#multi-actions a,
#multi-actions .upload a {
	margin: 0 !important;
	padding: 8px 16px !important;
	border-radius: 8px !important;
	font-size: 13px !important;
	font-weight: 600 !important;
	background: #1c1d29 !important;
	border: 1px solid rgba(255, 255, 255, 0.12) !important;
	color: #f8fafc !important;
	display: inline-flex !important;
	align-items: center !important;
	gap: 6px !important;
	transition: all 0.15s ease !important;
}

#multi-actions a:hover,
#multi-actions .upload a:hover {
	background: #6366f1 !important;
	border-color: #6366f1 !important;
	color: #ffffff !important;
}

.breadcrumb {
	margin-bottom: 18px !important;
	padding: 10px 16px !important;
	background: rgba(255, 255, 255, 0.03) !important;
	border-radius: 10px !important;
	border: 1px solid rgba(255, 255, 255, 0.06) !important;
}

.breadcrumb a {
	color: #38bdf8 !important;
	font-weight: 600 !important;
	padding: 2px 6px !important;
	border-radius: 4px !important;
}

.breadcrumb a:hover {
	background: rgba(56, 189, 248, 0.1) !important;
}
CSS;

$sftp->put('/usr/local/hestia/web/fm/dist/css/hst-custom.css', $customCss);

echo "✓ Successfully deployed updated /fm/domains/ and spacious padding File Manager!\n";

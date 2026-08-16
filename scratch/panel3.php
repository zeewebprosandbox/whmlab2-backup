<div id="token" token="<?= $_SESSION["token"] ?>"></div>

<header class="app-header">

	<div class="top-bar">
		<div class="container top-bar-inner">

			<!-- Logo / Usage Statistics wrapper -->
			<div class="top-bar-left">

				<!-- Logo / Home Button -->
				<a href="/" class="top-bar-logo" title="<?= htmlentities($_SESSION["APP_NAME"]) ?>">
					<img src="/images/logo-header.svg" alt="<?= htmlentities($_SESSION["APP_NAME"]) ?>" width="54" height="29">
				</a>

				<!-- Usage Statistics -->
				<div class="top-bar-usage">

					<button id="theme-toggle" style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 50px; width: 50px; height: 26px; position: relative; cursor: pointer; display: inline-flex; align-items: center; padding: 2px; margin-right: 15px; transition: 0.3s; vertical-align: middle;">
						<div id="theme-toggle-knob" style="background: #fff; border-radius: 50%; width: 20px; height: 20px; transition: 0.3s; display: flex; justify-content: center; align-items: center;">
							<i id="theme-icon" class="fas fa-sun" style="color: #f59e0b; font-size: 10px;"></i>
						</div>
					</button>
					<script>
						document.addEventListener("DOMContentLoaded", () => {
							const btn = document.getElementById('theme-toggle');
							const knob = document.getElementById('theme-toggle-knob');
							const icon = document.getElementById('theme-icon');
							let isDark = document.body.classList.contains('theme-dark') || localStorage.getItem('theme') === 'dark';
							
							function updateToggleUI() {
								if(isDark) {
									btn.style.background = '#3b82f6';
									btn.style.borderColor = '#3b82f6';
									knob.style.transform = 'translateX(24px)';
									icon.className = 'fas fa-moon';
									icon.style.color = '#1e293b';
									document.body.classList.add('theme-dark');
									document.body.classList.remove('theme-light');
								} else {
									btn.style.background = 'rgba(0,0,0,0.2)';
									btn.style.borderColor = 'rgba(255,255,255,0.1)';
									knob.style.transform = 'translateX(0px)';
									icon.className = 'fas fa-sun';
									icon.style.color = '#f59e0b';
									document.body.classList.add('theme-light');
									document.body.classList.remove('theme-dark');
								}
							}
							
							updateToggleUI();
							
							btn.addEventListener('click', () => {
								isDark = !isDark;
								localStorage.setItem('theme', isDark ? 'dark' : 'light');
								updateToggleUI();
							});
						});
					</script>

					<?php if ($_SESSION["look"] !== "") {
     	$user_icon = "fa-binoculars";
     } elseif ($_SESSION["userContext"] === "admin") {
     	$user_icon = "fa-user-tie";
     } else {
     	$user_icon = "fa-user";
     } ?>
					<div class="top-bar-usage-inner">
						<span class="top-bar-usage-item">
							<i class="fas <?= $user_icon ?>" title="<?= _("Logged in as") ?>: <?= htmlspecialchars($panel[$user]["NAME"]) ?>"></i>
							<span class="u-text-bold">
								<?= htmlspecialchars($user) ?>
							</span>
						</span>
						<span class="top-bar-usage-item">
							<i class="fas fa-hard-drive" title="<?= _("Disk") ?>: <?= humanize_usage_size($panel[$user]["U_DISK"]) ?> <?= humanize_usage_measure($panel[$user]["U_DISK"]) ?>"></i>
							<span class="u-text-bold">
								<?= humanize_usage_size($panel[$user]["U_DISK"]) ?>
							</span>
							<?= humanize_usage_measure($panel[$user]["U_DISK"]) ?>
							/
							<span class="u-text-bold">
							<?= humanize_usage_size($panel[$user]["DISK_QUOTA"]) ?>
							</span>
							<?= humanize_usage_measure($panel[$user]["DISK_QUOTA"]) ?>
						</span>
						<span class="top-bar-usage-item">
							<i class="fas fa-right-left" title="<?= _("Bandwidth") ?>: <?= humanize_usage_size($panel[$user]["U_BANDWIDTH"]) ?> <?= humanize_usage_measure($panel[$user]["U_BANDWIDTH"]) ?>"></i>
							<span class="u-text-bold">
								<?= humanize_usage_size($panel[$user]["U_BANDWIDTH"]) ?>
							</span>
							<?= humanize_usage_measure($panel[$user]["U_BANDWIDTH"]) ?>
							/
							<span class="u-text-bold">
								<?= humanize_usage_size($panel[$user]["BANDWIDTH"]) ?>
							</span>
							<?= humanize_usage_measure($panel[$user]["BANDWIDTH"]) ?>
						</span>
					</div>
				</div>

			</div>

			<!-- Notifications / Menu wrapper -->
			<div class="top-bar-right">

				<!-- Notifications -->
				<?php
    $impersonatingAdmin = $_SESSION["userContext"] === "admin" && ($_SESSION["look"] !== "" && $user == "admin");
    // Do not show notifications panel when impersonating 'admin' user
    if (!$impersonatingAdmin) { ?>
					<div x-data="notifications" class="top-bar-notifications">
						<button
							x-on:click="toggle()"
							x-bind:class="open && 'active'"
							class="top-bar-menu-link"
							type="button"
							title="<?= _("Notifications") ?>"
						>
							<i
								x-bind:class="{
									'animate__animated animate__swing icon-orange': (!initialized && <?= $panel[$user]["NOTIFICATIONS"] == "yes" ? "true" : "false" ?>) || notifications.length != 0,
									'fas fa-bell': true
								}"
							></i>
							<span class="u-hidden"><?= _("Notifications") ?></span>
						</button>
						<div
							x-cloak
							x-show="open"
							x-on:click.outside="open = false"
							class="top-bar-notifications-panel"
						>
							<template x-if="!initialized">
								<div class="top-bar-notifications-empty">
									<i class="fas fa-circle-notch fa-spin icon-dim"></i>
									<p><?= _("Loading...") ?></p>
								</div>
							</template>
							<template x-if="initialized && notifications.length == 0">
								<div class="top-bar-notifications-empty">
									<i class="fas fa-bell-slash icon-dim"></i>
									<p><?= _("No notifications") ?></p>
								</div>
							</template>
							<template x-if="initialized && notifications.length > 0">
								<ul>
									<template x-for="notification in notifications" :key="notification.ID">
										<li
											x-bind:id="`notification-${notification.ID}`"
											x-bind:class="notification.ACK && 'unseen'"
											class="top-bar-notification-item"
											x-data="{ open: true }"
											x-show="open"
											x-collapse
										>
											<div class="top-bar-notification-inner">
												<div class="top-bar-notification-header">
													<p x-text="notification.TOPIC" class="top-bar-notification-title"></p>
													<button
														x-on:click="open = false; setTimeout(() => remove(notification.ID), 300);"
														type="button"
														class="top-bar-notification-delete"
														title="<?= _("Delete notification") ?>"
													>
														<i class="fas fa-xmark"></i>
														<span class="u-hidden-visually"><?= _("Delete notification") ?></span>
													</button>
												</div>
												<div class="top-bar-notification-content" x-html="notification.NOTICE"></div>
												<p class="top-bar-notification-timestamp">
													<time
														:datetime="`${notification.TIMESTAMP_ISO}`"
														x-bind:title="`${notification.TIMESTAMP_TITLE}`"
														x-text="`${notification.TIMESTAMP_TEXT}`"
													></time>
												</p>
											</div>
										</li>
									</template>
								</ul>
							</template>
							<template x-if="initialized && notifications.length > 2">
								<button
									x-on:click="removeAll()"
									type="button"
									class="top-bar-notifications-delete-all"
								>
									<i class="fas fa-check"></i>
									<?= _("Delete all notifications") ?>
								</button>
							</template>
						</div>
					</div>
				<?php }
    ?>

				<!-- Menu -->
				<nav x-data="{ open: false }" class="top-bar-menu">

					<button
						type="button"
						class="top-bar-menu-link u-hide-tablet"
						x-on:click="open = !open">
						<i class="fas fa-bars"></i>
						<span class="u-hidden" x-text="open ? '<?= _("Close menu") ?>' : '<?= _("Open menu") ?>'">
							<?= _("Open menu") ?>
						</span>
					</button>

					<div x-cloak x-show="open" x-on:click.outside="open = false" class="top-bar-menu-panel">
						<ul class="top-bar-menu-list">

							<!-- File Manager -->
							<?php if (isset($_SESSION["FILE_MANAGER"]) && !empty($_SESSION["FILE_MANAGER"]) && $_SESSION["FILE_MANAGER"] == "true") { ?>
								<?php if ($_SESSION["userContext"] === "admin" && $_SESSION["look"] === "admin" && $_SESSION["POLICY_SYSTEM_PROTECTED_ADMIN"] == "yes") { ?>
									<!-- Hide file manager when impersonating admin-->
								<?php } else { ?>
									<li class="top-bar-menu-item">
										<a title="<?= _("File manager") ?>" class="top-bar-menu-link <?php if ($TAB == "FM") {
	echo "active";
} ?>" href="/fm/">
											<i class="fas fa-folder-open"></i>
											<span class="top-bar-menu-link-label u-hide-desktop"><?= _("File manager") ?></span>
										</a>
									</li>
								<?php } ?>
							<?php } ?>

							<!-- Web Terminal -->
							<?php if (isset($_SESSION["WEB_TERMINAL"]) && !empty($_SESSION["WEB_TERMINAL"]) && $_SESSION["WEB_TERMINAL"] == "true") { ?>
								<?php if ($_SESSION["userContext"] === "admin" && $_SESSION["look"] === "admin" && $_SESSION["POLICY_SYSTEM_PROTECTED_ADMIN"] == "yes") { ?>
									<!-- Hide web terminal when impersonating admin -->
								<?php } elseif ($_SESSION["login_shell"] != "nologin") { ?>
									<li class="top-bar-menu-item">
										<a title="<?= _("Web terminal") ?>" class="top-bar-menu-link <?php if ($TAB == "TERMINAL") {
	echo "active";
} ?>" href="/list/terminal/">
											<i class="fas fa-terminal"></i>
											<span class="top-bar-menu-link-label u-hide-desktop"><?= _("Web terminal") ?></span>
										</a>
									</li>
								<?php } ?>
							<?php } ?>

							<!-- Server Settings -->
							<?php if (($_SESSION["userContext"] === "admin" && $_SESSION["POLICY_SYSTEM_HIDE_SERVICES"] !== "yes") || $_SESSION["user"] === $_SESSION['ROOT_USER']) { ?>
								<?php if ($_SESSION["userContext"] === "admin" && $_SESSION["look"] !== "") { ?>
									<!-- Hide 'Server Settings' button when impersonating 'admin' or other users -->
								<?php } else { ?>
									<li class="top-bar-menu-item">
										<a title="<?= _("Server settings") ?>" class="top-bar-menu-link <?php if (in_array($TAB, ["SERVER", "IP", "RRD", "FIREWALL"])) {
	echo "active";
} ?>" href="/list/server/">
											<i class="fas fa-gear"></i>
											<span class="top-bar-menu-link-label u-hide-desktop"><?= _("Server settings") ?></span>
										</a>
									</li>
								<?php } ?>
							<?php } ?>

							<!-- Edit User -->
							<?php if ($_SESSION["userContext"] === "admin" && ($_SESSION["look"] !== "" && $user == "admin")) { ?>
								<!-- Hide 'edit user' entry point from other administrators for default 'admin' account-->
								<li class="top-bar-menu-item">
									<a title="<?= _("Logs") ?>" class="top-bar-menu-link <?php if ($TAB == "LOG") {
	echo "active";
} ?>" href="/list/log/">
										<i class="fas fa-clock-rotate-left"></i>
										<span class="top-bar-menu-link-label u-hide-desktop"><?= _("Logs") ?></span>
									</a>
								</li>
							<?php } else { ?>
								<?php if ($panel[$user]["SUSPENDED"] === "no") { ?>
									<li class="top-bar-menu-item">
										<a title="<?= htmlspecialchars($user) ?> (<?= htmlspecialchars($panel[$user]["NAME"]) ?>)" class="top-bar-menu-link" href="/edit/user/?user=<?= $user ?>&token=<?= $_SESSION["token"] ?>">
											<i class="fas fa-circle-user"></i>
											<span class="top-bar-menu-link-label u-hide-desktop"><?= htmlspecialchars($user) ?> (<?= htmlspecialchars($panel[$user]["NAME"]) ?>)</span>
										</a>
									</li>
								<?php } ?>
							<?php } ?>

							<!-- Statistics -->
							<li class="top-bar-menu-item">
								<a title="<?= _("Statistics") ?>" class="top-bar-menu-link <?php if ($TAB == "STATS") {
	echo "active";
} ?>" href="/list/stats/">
									<i class="fas fa-chart-line"></i>
									<span class="top-bar-menu-link-label u-hide-desktop"><?= _("Statistics") ?></span>
								</a>
							</li>
							<?php if ($_SESSION["HIDE_DOCS"] !== "yes") { ?>
								<!-- Help / Documentation -->
								<li class="top-bar-menu-item">
									<a title="<?= _("Help") ?>" class="top-bar-menu-link" href="https://hestiacp.com/docs/" target="_blank" rel="noopener">
										<i class="fas fa-circle-question"></i>
										<span class="top-bar-menu-link-label u-hide-desktop"><?= _("Help") ?></span>
									</a>
								</li>
							<?php } ?>
							<!-- Logout -->
							<?php if (isset($_SESSION["look"]) && !empty($_SESSION["look"])) { ?>
								<li class="top-bar-menu-item">
									<a title="<?= _("Log out") ?> (<?= $user ?>)" class="top-bar-menu-link top-bar-menu-link-logout" href="/logout/?token=<?= $_SESSION["token"] ?>">
										<i class="fas fa-circle-up"></i>
										<span class="top-bar-menu-link-label u-hide-desktop"><?= _("Log out") ?> (<?= $user ?>)</span>
									</a>
								</li>
							<?php } else { ?>
								<li class="top-bar-menu-item">
									<a title="<?= _("Log out") ?>" class="top-bar-menu-link top-bar-menu-link-logout" href="/logout/?token=<?= $_SESSION["token"] ?>">
										<i class="fas fa-right-from-bracket"></i>
										<span class="top-bar-menu-link-label u-hide-desktop"><?= _("Log out") ?></span>
									</a>
								</li>
							<?php } ?>

						</ul>
					</div>
				</nav>

			</div>

		</div>
	</div>

	<nav x-data="{ open: false }" class="main-menu">
		<div class="container">
			<button x-on:click="open = !open" type="button" class="main-menu-toggle">
				<i class="fas fa-bars"></i>
				<span
					x-text="open ? '<?= _("Collapse main menu") ?>' : '<?= _("Expand main menu") ?>'"
					class="main-menu-toggle-label"
				>
					<?= _("Expand main menu") ?>
				</span>
			</button>
			<ul x-cloak x-show="open" class="main-menu-list">

				<!-- Users tab -->
				<?php if ($_SESSION["userContext"] == "admin" && $_SESSION["look"] === "") { ?>
					<?php if ($_SESSION["user"] !== "admin" && $_SESSION["POLICY_SYSTEM_HIDE_ADMIN"] === "yes") {
     	$user_count = $panel[$user]["U_USERS"] - 1;
     } else {
     	$user_count = $panel[$user]["U_USERS"];
     } ?>
					<li class="main-menu-item">
						<a class="main-menu-item-link <?php if (in_array($TAB, ["USER", "LOG"])) {
      	echo "active";
      } ?>" href="/list/user/" title="<?= _("Users") ?>: <?= $user_count ?>&#13;<?= _("Suspended") ?>: <?= $panel[$user]["SUSPENDED_USERS"] ?>">
							<p class="main-menu-item-label"><?= _("USER") ?><i class="fas fa-users"></i></p>
							<ul class="main-menu-stats">
								<li>
									<?= _("Users") ?>: <?= htmlspecialchars($user_count) ?>
								</li>
								<li>
									<?= _("Suspended") ?>: <?= $panel[$user]["SUSPENDED_USERS"] ?>
								</li>
							</ul>
						</a>
					</li>
				<?php } ?>

				<!-- Web tab -->
				<?php if (isset($_SESSION["WEB_SYSTEM"]) && !empty($_SESSION["WEB_SYSTEM"])) { ?>
					<?php if ($panel[$user]["WEB_DOMAINS"] != "0") { ?>
						<li class="main-menu-item">
							<a class="main-menu-item-link <?php if ($TAB == "WEB") {
       	echo "active";
       } ?>" href="/list/web/" title="<?= _("Domains") ?>: <?= $panel[$user]["U_WEB_DOMAINS"] ?>&#13;<?= _("Aliases") ?>: <?= $panel[$user]["U_WEB_ALIASES"] ?>&#13;<?= _("Limit") ?>: <?= $panel[
	$user
]["WEB_DOMAINS"] == "unlimited"
	? "∞"
	: $panel[$user]["WEB_DOMAINS"] ?>&#13;<?= _("Suspended") ?>: <?= $panel[$user]["SUSPENDED_WEB"] ?>">
								<p class="main-menu-item-label"><?= _("WEB") ?><i class="fas fa-earth-americas"></i></p>
								<ul class="main-menu-stats">
									<li>
										<?= _("Domains") ?>: <?= $panel[$user]["U_WEB_DOMAINS"] ?> / <?= $panel[$user]["WEB_DOMAINS"] == "unlimited" ? "<span class=\"u-text-bold\">∞</span>" : $panel[$user]["WEB_DOMAINS"] ?> (<?= $panel[
 	$user
 ]["SUSPENDED_WEB"] ?>)
									</li>
									<li>
										<?= _("Aliases") ?>: <?= $panel[$user]["U_WEB_ALIASES"] ?> / <?= $panel[$user]["WEB_ALIASES"] == "unlimited" || $panel[$user]["WEB_DOMAINS"] == "unlimited"
 	? "<span class=\"u-text-bold\">∞</span>"
 	: $panel[$user]["WEB_ALIASES"] * $panel[$user]["WEB_DOMAINS"] ?>
									</li>
								</ul>
							</a>
						</li>
					<?php } ?>
				<?php } ?>

				<!-- DNS tab -->
				<?php if (isset($_SESSION["DNS_SYSTEM"]) && !empty($_SESSION["DNS_SYSTEM"])) { ?>
					<?php if ($panel[$user]["DNS_DOMAINS"] != "0") { ?>
						<li class="main-menu-item">
							<a class="main-menu-item-link <?php if ($TAB == "DNS") {
       	echo "active";
       } ?>" href="/list/dns/" title="<?= _("Domains") ?>: <?= $panel[$user]["U_DNS_DOMAINS"] ?>&#13;<?= _("Limit") ?>: <?= $panel[$user]["DNS_DOMAINS"] == "unlimited"
	? "∞"
	: $panel[$user]["DNS_DOMAINS"] ?>&#13;<?= _("Suspended") ?>: <?= $panel[$user]["SUSPENDED_DNS"] ?>">
								<p class="main-menu-item-label"><?= _("DNS") ?><i class="fas fa-book-atlas"></i></p>
								<ul class="main-menu-stats">
									<li>
										<?= _("Zones") ?>: <?= $panel[$user]["U_DNS_DOMAINS"] ?> / <?= $panel[$user]["DNS_DOMAINS"] == "unlimited" ? "<span class=\"u-text-bold\">∞</span>" : $panel[$user]["DNS_DOMAINS"] ?> (<?= $panel[
 	$user
 ]["SUSPENDED_DNS"] ?>)
									</li>
									<li>
										<?= _("Records") ?>: <?= $panel[$user]["U_DNS_RECORDS"] ?> / <?= $panel[$user]["DNS_RECORDS"] == "unlimited" || $panel[$user]["DNS_DOMAINS"] == "unlimited"
 	? "<span class=\"u-text-bold\">∞</span>"
 	: $panel[$user]["DNS_RECORDS"] * $panel[$user]["DNS_DOMAINS"] ?>
									</li>
								</ul>
							</a>
						</li>
					<?php } ?>
				<?php } ?>

				<!-- Mail tab -->
				<?php if (isset($_SESSION["MAIL_SYSTEM"]) && !empty($_SESSION["MAIL_SYSTEM"])) { ?>
					<?php if ($panel[$user]["MAIL_DOMAINS"] != "0") { ?>
						<li class="main-menu-item">
							<a class="main-menu-item-link <?php if ($TAB == "MAIL") {
       	echo "active";
       } ?>" href="/list/mail/" title="<?= _("Domains") ?>: <?= $panel[$user]["U_MAIL_DOMAINS"] ?>&#13;<?= _("Limit") ?>: <?= $panel[$user]["MAIL_DOMAINS"] == "unlimited"
	? "∞"
	: $panel[$user]["MAIL_DOMAINS"] ?>&#13;<?= _("Suspended") ?>: <?= $panel[$user]["SUSPENDED_MAIL"] ?>">
								<p class="main-menu-item-label"><?= _("MAIL") ?><i class="fas fa-envelopes-bulk"></i></p>
								<ul class="main-menu-stats">
									<li>
										<?= _("Domains") ?>: <?= $panel[$user]["U_MAIL_DOMAINS"] ?> / <?= $panel[$user]["MAIL_DOMAINS"] == "unlimited" ? "<span class=\"u-text-bold\">∞</span>" : $panel[$user]["MAIL_DOMAINS"] ?> (<?= $panel[
 	$user
 ]["SUSPENDED_MAIL"] ?>)
									</li>
									<li>
										<?= _("Accounts") ?>: <?= $panel[$user]["U_MAIL_ACCOUNTS"] ?> / <?= $panel[$user]["MAIL_ACCOUNTS"] == "unlimited" || $panel[$user]["MAIL_DOMAINS"] == "unlimited"
 	? "<span class=\"u-text-bold\">∞</span>"
 	: $panel[$user]["MAIL_ACCOUNTS"] * $panel[$user]["MAIL_DOMAINS"] ?>
									</li>
								</ul>
							</a>
						</li>
					<?php } ?>
				<?php } ?>

				<!-- Databases tab -->
				<?php if (isset($_SESSION["DB_SYSTEM"]) && !empty($_SESSION["DB_SYSTEM"])) { ?>
					<?php if ($panel[$user]["DATABASES"] != "0") { ?>
						<li class="main-menu-item">
							<a class="main-menu-item-link <?php if ($TAB == "DB") {
       	echo "active";
       } ?>" href="/list/db/" title="<?= _("Databases") ?>: <?= $panel[$user]["U_DATABASES"] ?>&#13;<?= _("Limit") ?>: <?= $panel[$user]["DATABASES"] == "unlimited"
	? "∞"
	: $panel[$user]["DATABASES"] ?>&#13;<?= _("Suspended") ?>: <?= $panel[$user]["SUSPENDED_DB"] ?>">
								<p class="main-menu-item-label"><?= _("DB") ?><i class="fas fa-database"></i></p>
								<ul class="main-menu-stats">
									<li>
										<?= _("Databases") ?>: <?= $panel[$user]["U_DATABASES"] ?> / <?= $panel[$user]["DATABASES"] == "unlimited" ? "<span class=\"u-text-bold\">∞</span>" : $panel[$user]["DATABASES"] ?> (<?= $panel[$user][
 	"SUSPENDED_DB"
 ] ?>)
									</li>
								</ul>
							</a>
						</li>
					<?php } ?>
				<?php } ?>

				<!-- Cron tab -->
				<?php if (isset($_SESSION["CRON_SYSTEM"]) && !empty($_SESSION["CRON_SYSTEM"])) { ?>
					<?php if ($panel[$user]["CRON_JOBS"] != "0") { ?>
						<li class="main-menu-item">
							<a class="main-menu-item-link <?php if ($TAB == "CRON") {
       	echo "active";
       } ?>" href="/list/cron/" title="<?= _("Jobs") ?>: <?= $panel[$user]["U_WEB_DOMAINS"] ?>&#13;<?= _("Limit") ?>: <?= $panel[$user]["CRON_JOBS"] == "unlimited"
	? "∞"
	: $panel[$user]["CRON_JOBS"] ?>&#13;<?= _("Suspended") ?>: <?= $panel[$user]["SUSPENDED_CRON"] ?>">
								<p class="main-menu-item-label"><?= _("CRON") ?><i class="fas fa-clock"></i></p>
								<ul class="main-menu-stats">
									<li>
										<?= _("Jobs") ?>: <?= $panel[$user]["U_CRON_JOBS"] ?> / <?= $panel[$user]["CRON_JOBS"] == "unlimited" ? "<span class=\"u-text-bold\">∞</span>" : $panel[$user]["CRON_JOBS"] ?> (<?= $panel[$user][
 	"SUSPENDED_CRON"
 ] ?>)
									</li>
								</ul>
							</a>
						</li>
					<?php } ?>
				<?php } ?>

				<!-- Backups tab -->
				<?php if (isset($_SESSION["BACKUP_SYSTEM"]) && !empty($_SESSION["BACKUP_SYSTEM"])) { ?>
					<?php if ($panel[$user]["BACKUPS"] != "0" || $panel[$user]["U_BACKUPS"] != "0" || $panel[$user]["BACKUPS_INCREMENTAL"] == "yes") { ?>
						<li class="main-menu-item">
							<a class="main-menu-item-link <?php if ($TAB == "BACKUP") {
       	echo "active";
       } ?>" href="/list/backup/" title="<?= _("Backups") ?>: <?= $panel[$user]["U_BACKUPS"] ?>&#13;<?= _("Limit") ?>: <?= $panel[$user]["BACKUPS"] == "unlimited" ? "∞" : $panel[$user]["BACKUPS"] ?>">
								<p class="main-menu-item-label"><?= _("BACKUP") ?><i class="fas fa-file-zipper"></i></p>
								<ul class="main-menu-stats">
									<li>
										<?= _("Backups") ?>: <?= $panel[$user]["U_BACKUPS"] ?> / <?= $panel[$user]["BACKUPS"] == "unlimited" ? "<span class=\"u-text-bold\">∞</span>" : $panel[$user]["BACKUPS"] ?>
									</li>
								</ul>
							</a>
						</li>
					<?php } ?>
				<?php } ?>

			</ul>
		</div>
	</nav>

</header>

<div class="container" style="margin-top: 20px; margin-bottom: 20px;">
    <style>
.quick-cards-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
}
@media (max-width: 768px) {
    .quick-cards-grid {
        grid-template-columns: 1fr;
    }
}
</style>
<div class="quick-cards-grid">
        <!-- Card 1 -->
        <a href="/list/web/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 14px 16px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-globe" style="font-size: 18px; color: #3b82f6;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 13px; letter-spacing: 0.5px;">Web Domains</span>
                <span style="font-size: 10px; color: #94a3b8; margin-top: 2px;">Manage websites</span>
            </div>
        </a>
        <!-- Card 2 -->
        <a href="/list/dns/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 14px 16px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-book-atlas" style="font-size: 18px; color: #10b981;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 13px; letter-spacing: 0.5px;">DNS Records</span>
                <span style="font-size: 10px; color: #94a3b8; margin-top: 2px;">Zone management</span>
            </div>
        </a>
        <!-- Card 3 -->
        <a href="/list/mail/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 14px 16px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-envelope" style="font-size: 18px; color: #f59e0b;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 13px; letter-spacing: 0.5px;">Mail Accounts</span>
                <span style="font-size: 10px; color: #94a3b8; margin-top: 2px;">Email & Webmail</span>
            </div>
        </a>
        <!-- Card 4 -->
        <a href="/list/db/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 14px 16px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-database" style="font-size: 18px; color: #8b5cf6;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 13px; letter-spacing: 0.5px;">Databases</span>
                <span style="font-size: 10px; color: #94a3b8; margin-top: 2px;">MySQL / PgSQL</span>
            </div>
        </a>
        
        <!-- Card 5 -->
        <a href="/list/cron/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 14px 16px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-clock" style="font-size: 18px; color: #64748b;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 13px; letter-spacing: 0.5px;">Cron Jobs</span>
                <span style="font-size: 10px; color: #94a3b8; margin-top: 2px;">Scheduled tasks</span>
            </div>
        </a>
        <!-- Card 6 -->
        <a href="/list/backup/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 14px 16px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-file-zipper" style="font-size: 18px; color: #0ea5e9;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 13px; letter-spacing: 0.5px;">Backups</span>
                <span style="font-size: 10px; color: #94a3b8; margin-top: 2px;">Restore & Archive</span>
            </div>
        </a>
        <!-- Card 7 -->
        <a href="/list/ssl/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 14px 16px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-lock" style="font-size: 18px; color: #ef4444;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 13px; letter-spacing: 0.5px;">SSL Certificates</span>
                <span style="font-size: 10px; color: #94a3b8; margin-top: 2px;">Let's Encrypt</span>
            </div>
        </a>
        <!-- Card 8 -->
        <a href="/list/apps/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 14px 16px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-bolt" style="font-size: 18px; color: #eab308;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 13px; letter-spacing: 0.5px;">Quick Apps</span>
                <span style="font-size: 10px; color: #94a3b8; margin-top: 2px;">WP, Laravel, etc</span>
            </div>
        </a>
        
        <!-- Card 9 -->
        <a href="/list/terminal/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 14px 16px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-terminal" style="font-size: 18px; color: #a3e635;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 13px; letter-spacing: 0.5px;">Terminal</span>
                <span style="font-size: 10px; color: #94a3b8; margin-top: 2px;">SSH Access</span>
            </div>
        </a>
        <!-- Card 10 -->
        <a href="/list/nodejs/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 14px 16px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fab fa-node-js" style="font-size: 18px; color: #16a34a;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 13px; letter-spacing: 0.5px;">Node.js</span>
                <span style="font-size: 10px; color: #94a3b8; margin-top: 2px;">Manage Node apps</span>
            </div>
        </a>


    </div>
</div>


<main class="app-content">

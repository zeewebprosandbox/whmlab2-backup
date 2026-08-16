import os

# Controllers
ssl_index = """<?php
$TAB = "WEB";
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";
exec(HESTIA_CMD . "v-list-web-domains " . escapeshellarg($user) . " 'json'", $output, $return_var);
$data = json_decode(implode("", $output), true);
render_page($user, $TAB, "list_ssl_dashboard");
$_SESSION["back"] = $_SERVER["REQUEST_URI"];
"""

apps_index = """<?php
$TAB = "WEB";
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";
exec(HESTIA_CMD . "v-list-web-domains " . escapeshellarg($user) . " 'json'", $output, $return_var);
$data = json_decode(implode("", $output), true);
render_page($user, $TAB, "list_apps_dashboard");
$_SESSION["back"] = $_SERVER["REQUEST_URI"];
"""

nodejs_index = """<?php
$TAB = "WEB";
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";
exec(HESTIA_CMD . "v-list-web-domains " . escapeshellarg($user) . " 'json'", $output, $return_var);
$data = json_decode(implode("", $output), true);
render_page($user, $TAB, "list_nodejs_dashboard");
$_SESSION["back"] = $_SERVER["REQUEST_URI"];
"""

# Templates
ssl_template = """<div class="container">
    <h1 class="u-mt20 u-mb20"><?= tohtml( _("SSL Certificates Manager")) ?></h1>
    
    <div class="units-table js-units-container">
        <div class="units-table-header">
            <div class="units-table-cell"><?= tohtml( _("Domain")) ?></div>
            <div class="units-table-cell u-text-center"><?= tohtml( _("SSL Status")) ?></div>
            <div class="units-table-cell u-text-center"><?= tohtml( _("Let's Encrypt")) ?></div>
            <div class="units-table-cell u-text-right"><?= tohtml( _("Actions")) ?></div>
        </div>

        <?php
            foreach ($data as $key => $value) {
                $has_ssl = filter_var($data[$key]['SSL'], FILTER_VALIDATE_BOOL);
                $has_le = filter_var($data[$key]['LETSENCRYPT'], FILTER_VALIDATE_BOOL);
                
                $ssl_status = $has_ssl ? "<span style='color: #10b981;'><i class='fas fa-lock'></i> Active</span>" : "<span style='color: #ef4444;'><i class='fas fa-unlock'></i> Inactive</span>";
                $le_status = $has_le ? "<span style='color: #3b82f6;'>Yes</span>" : "<span style='color: #64748b;'>No</span>";
        ?>
            <div class="units-table-row">
                <div class="units-table-cell u-text-bold">
                    <?= tohtml($key) ?>
                </div>
                <div class="units-table-cell u-text-center">
                    <?= $ssl_status ?>
                </div>
                <div class="units-table-cell u-text-center">
                    <?= $le_status ?>
                </div>
                <div class="units-table-cell u-text-right">
                    <a class="button button-secondary" href="/edit/web/?domain=<?= tohtml($key) ?>&token=<?= $_SESSION['token'] ?>#v_ssl" style="padding: 6px 14px; border-radius: 6px;">
                        <i class="fas fa-cog"></i> <?= tohtml( _("Manage SSL")) ?>
                    </a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
"""

apps_template = """<div class="container">
    <h1 class="u-mt20 u-mb20"><?= tohtml( _("Quick App Installer")) ?></h1>
    <p class="u-mb20">Select a domain below to launch the one-click app installer (WordPress, Laravel, etc).</p>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <?php foreach ($data as $key => $value) { ?>
            <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); padding: 20px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-weight: bold; font-size: 16px; margin-bottom: 4px;"><?= tohtml($key) ?></div>
                    <div style="font-size: 12px; color: #94a3b8;"><i class="fas fa-bolt" style="color: #eab308;"></i> Ready for installation</div>
                </div>
                <a class="button" href="/add/webapp/?domain=<?= tohtml($key) ?>&token=<?= $_SESSION['token'] ?>" style="background: #3b82f6; color: white; border: none; border-radius: 6px; padding: 8px 16px;">
                    Install App
                </a>
            </div>
        <?php } ?>
    </div>
</div>
"""

nodejs_template = """<div class="container">
    <h1 class="u-mt20 u-mb20"><?= tohtml( _("Node.js Applications")) ?></h1>
    
    <div class="units-table js-units-container">
        <div class="units-table-header">
            <div class="units-table-cell"><?= tohtml( _("Domain")) ?></div>
            <div class="units-table-cell u-text-center"><?= tohtml( _("Backend Template")) ?></div>
            <div class="units-table-cell u-text-right"><?= tohtml( _("Actions")) ?></div>
        </div>

        <?php
            $has_node = false;
            foreach ($data as $key => $value) {
                if ($value['TPL'] == 'nodejs' || strpos($value['TPL'], 'node') !== false) {
                    $has_node = true;
        ?>
            <div class="units-table-row">
                <div class="units-table-cell u-text-bold">
                    <?= tohtml($key) ?>
                </div>
                <div class="units-table-cell u-text-center">
                    <span style="background: #16a34a; color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px;"><i class="fab fa-node-js"></i> <?= tohtml($value['TPL']) ?></span>
                </div>
                <div class="units-table-cell u-text-right">
                    <a class="button button-secondary" href="/edit/web/?domain=<?= tohtml($key) ?>&token=<?= $_SESSION['token'] ?>" style="padding: 6px 14px; border-radius: 6px;">
                        <i class="fas fa-cog"></i> <?= tohtml( _("Manage App")) ?>
                    </a>
                </div>
            </div>
        <?php 
                }
            }
            if (!$has_node) {
                echo "<div class='units-table-row'><div class='units-table-cell u-text-center' style='width: 100%; color: #94a3b8; padding: 20px;'>No Node.js domains configured yet. Edit a web domain and set its template to Node.js.</div></div>";
            }
        ?>
    </div>
</div>
"""

with open('./scratch/dashboards/ssl_index.php', 'w') as f: f.write(ssl_index)
with open('./scratch/dashboards/apps_index.php', 'w') as f: f.write(apps_index)
with open('./scratch/dashboards/nodejs_index.php', 'w') as f: f.write(nodejs_index)
with open('./scratch/dashboards/list_ssl_dashboard.php', 'w') as f: f.write(ssl_template)
with open('./scratch/dashboards/list_apps_dashboard.php', 'w') as f: f.write(apps_template)
with open('./scratch/dashboards/list_nodejs_dashboard.php', 'w') as f: f.write(nodejs_template)

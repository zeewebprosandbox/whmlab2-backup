<div class="container">
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

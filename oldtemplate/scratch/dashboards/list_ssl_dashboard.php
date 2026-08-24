<div class="container">
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

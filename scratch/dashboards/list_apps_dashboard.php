<div class="container">
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

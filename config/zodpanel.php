<?php

return [
    'custom_source_path' => env('ZODPANEL_CUSTOM_SOURCE_PATH', base_path('../zodpanel-hestia-custom-backup/usr/local/hestia')),
    'hestia_install_url' => env('ZODPANEL_HESTIA_INSTALL_URL', 'https://raw.githubusercontent.com/hestiacp/hestiacp/release/install/hst-install.sh'),
    'backup_repo' => env('ZODPANEL_BACKUP_REPO', 'https://github.com/zeewebprosandbox/zodpanel-hestia-custom-backup'),
    'whmlab_repo' => env('WHMLAB_BACKUP_REPO', 'https://github.com/zeewebprosandbox/whmlab2-backup'),
];

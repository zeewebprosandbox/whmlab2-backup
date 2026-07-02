<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use phpseclib3\Net\SSH2;

class PullLiveZodPanelCustom extends Command
{
    protected $signature = 'zodpanel:pull-live
        {--host= : Live ZodPanel SSH host}
        {--port= : Live ZodPanel SSH port}
        {--user= : Live ZodPanel SSH username}
        {--password= : Live ZodPanel SSH password}
        {--target= : Local zodpanel-hestia-custom-backup repo path}';

    protected $description = 'Pull custom Hestia/ZodPanel files from the live ZodPanel server into the local backup repository';

    public function handle(): int
    {
        $host = $this->option('host') ?: config('zodpanel.live.host');
        $port = (int) ($this->option('port') ?: config('zodpanel.live.ssh_port', 22));
        $user = $this->option('user') ?: config('zodpanel.live.ssh_username', 'root');
        $password = $this->option('password') ?: config('zodpanel.live.ssh_password');
        $target = rtrim($this->option('target') ?: config('zodpanel.custom_backup_path'), '/');

        if (!$host || !$user || !$password) {
            $this->error('Live ZodPanel host, user, and password are required. Pass --host/--user/--password or set ZODPANEL_LIVE_* env values.');
            return self::FAILURE;
        }

        if (!is_dir($target)) {
            File::makeDirectory($target, 0755, true);
        }

        $ssh = new SSH2($host, $port, 20);
        if (!$ssh->login($user, $password)) {
            $this->error('Unable to connect to live ZodPanel over SSH.');
            return self::FAILURE;
        }

        $this->line("Pulling live custom ZodPanel files from {$host}:{$port}");
        $pulled = 0;

        foreach (config('zodpanel.custom_file_paths', []) as $remote) {
            $local = $target.'/'.ltrim($remote, '/');
            $encoded = trim((string) $ssh->exec('test -f '.escapeshellarg($remote).' && base64 -w 0 '.escapeshellarg($remote).' || true'));

            if ($encoded === '') {
                $this->warn("Missing or unreadable on live node: {$remote}");
                continue;
            }

            $contents = base64_decode($encoded, true);
            if ($contents === false) {
                $this->warn("Could not decode live file: {$remote}");
                continue;
            }

            File::ensureDirectoryExists(dirname($local));
            File::put($local, $contents);
            @chmod($local, str_contains($remote, '/bin/') ? 0755 : 0644);
            $this->line("Pulled {$remote}");
            $pulled++;
        }

        $this->info("Pulled {$pulled} live ZodPanel custom file(s).");
        return self::SUCCESS;
    }
}

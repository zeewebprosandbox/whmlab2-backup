<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class PushZodPanelVersion extends Command
{
    protected $signature = 'zodpanel:version-push
        {description : Human-readable version description}
        {--whmlab-only : Push only the WHMLab Laravel repository}
        {--zodpanel-only : Push only the ZodPanel custom Hestia repository}';

    protected $description = 'Commit and push WHMLab/ZodPanel custom repositories with one version description';

    public function handle(): int
    {
        $description = trim($this->argument('description'));
        if ($description === '') {
            $this->error('A version description is required.');
            return self::FAILURE;
        }

        $repos = [];
        if (!$this->option('zodpanel-only')) {
            $repos['WHMLab'] = base_path();
        }

        if (!$this->option('whmlab-only')) {
            $repos['ZodPanel custom Hestia'] = dirname(base_path()).'/zodpanel-hestia-custom-backup';
        }

        foreach ($repos as $label => $path) {
            if (!is_dir($path.'/.git')) {
                $this->warn("Skipping {$label}: {$path} is not a git repository.");
                continue;
            }

            $this->line("Versioning {$label}...");
            $this->runGit($path, ['add', '-A']);

            $status = trim($this->runGit($path, ['status', '--short'], false));
            if ($status === '') {
                $this->line("No changes in {$label}; pushing current branch.");
            } else {
                $this->runGit($path, ['commit', '-m', $description]);
            }

            $branch = trim($this->runGit($path, ['branch', '--show-current'], false)) ?: 'main';
            $this->runGit($path, ['push', '-u', 'origin', $branch]);
        }

        $this->info('Version push completed.');
        return self::SUCCESS;
    }

    private function runGit(string $path, array $arguments, bool $throw = true): string
    {
        $process = new Process(array_merge(['git'], $arguments), $path);
        $process->setTimeout(300);
        $process->run();

        $output = trim($process->getOutput()."\n".$process->getErrorOutput());
        if ($output !== '') {
            $this->line($output);
        }

        if ($throw && !$process->isSuccessful()) {
            throw new \RuntimeException($output ?: 'Git command failed.');
        }

        return $output;
    }
}

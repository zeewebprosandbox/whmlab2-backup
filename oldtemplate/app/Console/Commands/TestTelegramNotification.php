<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class TestTelegramNotification extends Command
{
    protected $signature = 'zodpanel:test-telegram {message? : Optional custom test message}';
    protected $description = 'Send a test operational notification to the configured Telegram Channel';

    public function handle(): int
    {
        $this->info('Checking Telegram Bot credentials...');

        $token = gs('telegram_bot_token') ?: env('TELEGRAM_BOT_TOKEN');
        $chatId = gs('telegram_chat_id') ?: env('TELEGRAM_CHAT_ID');

        if (empty($token) || empty($chatId)) {
            $this->error('Telegram Bot Token or Channel/Chat ID is not configured!');
            $this->line('You can set it in Admin Settings > General Settings or in .env (TELEGRAM_BOT_TOKEN, TELEGRAM_CHAT_ID).');
            return 1;
        }

        $this->info("Sending test notification to Chat/Channel ID: {$chatId}...");

        $custom = $this->argument('message');
        if ($custom) {
            $success = TelegramService::sendMessage("🔔 <b>ZodHost Test Alert</b>\n\n" . htmlspecialchars($custom));
        } else {
            $success = TelegramService::sendTestAlert();
        }

        if ($success) {
            $this->info('✓ Telegram test notification dispatched successfully!');
            return 0;
        } else {
            $this->error('✗ Failed to send Telegram test notification. Check your Bot Token and Channel ID.');
            return 1;
        }
    }
}

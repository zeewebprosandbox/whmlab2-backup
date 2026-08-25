<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    /**
     * Send a raw HTML formatted message to the configured Telegram Channel/Chat
     *
     * @param string $message
     * @param array $extra
     * @return bool
     */
    public static function sendMessage(string $message, array $extra = []): bool
    {
        try {
            $token = gs('telegram_bot_token') ?: env('TELEGRAM_BOT_TOKEN');
            $chatId = gs('telegram_chat_id') ?: env('TELEGRAM_CHAT_ID');

            if (empty($token) || empty($chatId)) {
                return false;
            }

            if (gs('telegram_notification') === 0) {
                return false;
            }

            $payload = array_merge([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ], $extra);

            $response = Http::timeout(8)->post("https://api.telegram.org/bot{$token}/sendMessage", $payload);

            if ($response->successful()) {
                return true;
            }

            Log::warning('Telegram Notification Error: ' . $response->body());
            return false;
        } catch (Exception $e) {
            Log::error('Telegram Service Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Notify on New User Registration
     */
    public static function notifyNewUser($user): bool
    {
        $siteName = gs('site_name') ?? 'ZodHost';
        $time = now()->format('Y-m-d H:i:s T');
        
        $text = "👤 <b>NEW CUSTOMER REGISTRATION</b>\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "• <b>Name:</b> " . htmlspecialchars($user->fullname ?? ($user->firstname . ' ' . $user->lastname)) . "\n";
        $text .= "• <b>Username:</b> @" . htmlspecialchars($user->username) . "\n";
        $text .= "• <b>Email:</b> " . htmlspecialchars($user->email) . "\n";
        $text .= "• <b>Country:</b> " . htmlspecialchars($user->country_name ?? $user->country_code ?? 'N/A') . "\n";
        $text .= "• <b>Registered At:</b> {$time}\n";
        $text .= "• <b>Platform:</b> {$siteName}\n";

        return self::sendMessage($text);
    }

    /**
     * Notify on New Order Placed
     */
    public static function notifyNewOrder($order, array $items = []): bool
    {
        $siteName = gs('site_name') ?? 'ZodHost';
        $curSym = gs('cur_sym') ?? '$';
        $time = now()->format('Y-m-d H:i:s T');

        $text = "🛒 <b>NEW ORDER PLACED</b>\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "• <b>Order ID:</b> #" . htmlspecialchars($order->order_number ?? $order->id) . "\n";
        $text .= "• <b>Customer:</b> " . htmlspecialchars(@$order->user->username ?? 'Guest/User') . " (" . htmlspecialchars(@$order->user->email ?? 'N/A') . ")\n";
        $text .= "• <b>Amount:</b> {$curSym}" . showAmount($order->amount ?? $order->total_amount ?? 0) . "\n";
        $text .= "• <b>Status:</b> " . strtoupper($order->status ?? 'Pending') . "\n";
        
        if (!empty($items)) {
            $text .= "• <b>Items:</b>\n";
            foreach ($items as $item) {
                $text .= "  ▫️ " . htmlspecialchars($item) . "\n";
            }
        }

        $text .= "• <b>Time:</b> {$time}\n";

        return self::sendMessage($text);
    }

    /**
     * Notify on Payment Received / Invoice Paid
     */
    public static function notifyPaymentReceived($invoice, $amount, $gateway = 'Online Gateway', $trx = null): bool
    {
        $curSym = gs('cur_sym') ?? '$';
        $time = now()->format('Y-m-d H:i:s T');

        $text = "💰 <b>PAYMENT RECEIVED / INVOICE PAID</b>\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "• <b>Invoice #:</b> " . htmlspecialchars($invoice->invoice_number ?? $invoice->id) . "\n";
        $text .= "• <b>Customer:</b> " . htmlspecialchars(@$invoice->user->username ?? 'User') . "\n";
        $text .= "• <b>Amount Paid:</b> {$curSym}" . showAmount($amount) . "\n";
        $text .= "• <b>Gateway:</b> " . htmlspecialchars($gateway) . "\n";
        if ($trx) {
            $text .= "• <b>TRX ID:</b> <code>" . htmlspecialchars($trx) . "</code>\n";
        }
        $text .= "• <b>Time:</b> {$time}\n";

        return self::sendMessage($text);
    }

    /**
     * Notify on Hosting Service Provisioned
     */
    public static function notifyServiceProvisioned($service, string $panel = 'ZodPanel'): bool
    {
        $time = now()->format('Y-m-d H:i:s T');

        $text = "⚡ <b>SERVICE PROVISIONED & ACTIVE</b>\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "• <b>Service ID:</b> #" . $service->id . "\n";
        $text .= "• <b>Product:</b> " . htmlspecialchars(@$service->product->name ?? 'Hosting Service') . "\n";
        $text .= "• <b>Domain:</b> <code>" . htmlspecialchars($service->domain ?? 'N/A') . "</code>\n";
        $text .= "• <b>Customer:</b> " . htmlspecialchars(@$service->user->username ?? 'User') . "\n";
        $text .= "• <b>Panel:</b> {$panel} (zodpanel.zodserver.cloud:8083)\n";
        $text .= "• <b>Time:</b> {$time}\n";

        return self::sendMessage($text);
    }

    /**
     * Notify on Support Ticket Opened or Replied
     */
    public static function notifySupportTicket($ticket, string $action = 'Opened'): bool
    {
        $time = now()->format('Y-m-d H:i:s T');

        $text = "🎫 <b>SUPPORT TICKET " . strtoupper($action) . "</b>\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "• <b>Ticket:</b> #" . htmlspecialchars($ticket->ticket) . "\n";
        $text .= "• <b>Subject:</b> " . htmlspecialchars($ticket->subject) . "\n";
        $text .= "• <b>Customer:</b> " . htmlspecialchars($ticket->name ?? @$ticket->user->username) . " (" . htmlspecialchars($ticket->email ?? @$ticket->user->email) . ")\n";
        $text .= "• <b>Priority:</b> " . strtoupper($ticket->priority == 3 ? 'High' : ($ticket->priority == 2 ? 'Medium' : 'Low')) . "\n";
        $text .= "• <b>Time:</b> {$time}\n";

        return self::sendMessage($text);
    }

    /**
     * Notify on Contact Form Inquiries
     */
    public static function notifyContactMessage($name, $email, $subject, $message): bool
    {
        $time = now()->format('Y-m-d H:i:s T');

        $text = "📬 <b>NEW CONTACT FORM INQUIRY</b>\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "• <b>From:</b> " . htmlspecialchars($name) . " (" . htmlspecialchars($email) . ")\n";
        $text .= "• <b>Subject:</b> " . htmlspecialchars($subject) . "\n";
        $text .= "• <b>Message:</b>\n" . htmlspecialchars(substr($message, 0, 300)) . (strlen($message) > 300 ? '...' : '') . "\n";
        $text .= "• <b>Time:</b> {$time}\n";

        return self::sendMessage($text);
    }

    /**
     * Send Test Alert
     */
    public static function sendTestAlert(): bool
    {
        $siteName = gs('site_name') ?? 'ZodHost';
        $time = now()->format('Y-m-d H:i:s T');

        $text = "🔔 <b>{$siteName} TELEGRAM BOT NOTIFICATIONS ACTIVE</b>\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "• <b>Status:</b> Connected & Operational 100%\n";
        $text .= "• <b>Server:</b> zodpanel.zodserver.cloud\n";
        $text .= "• <b>Time:</b> {$time}\n";
        $text .= "• <i>All real-time customer registrations, orders, payments, provisioning, tickets, and alerts will be delivered to this channel.</i>\n";

        return self::sendMessage($text);
    }
}

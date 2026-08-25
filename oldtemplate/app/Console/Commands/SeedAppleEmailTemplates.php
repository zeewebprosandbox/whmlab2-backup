<?php

namespace App\Console\Commands;

use App\Models\GeneralSetting;
use App\Models\NotificationTemplate;
use Illuminate\Console\Command;

class SeedAppleEmailTemplates extends Command
{
    protected $signature = 'zodpanel:seed-apple-emails';
    protected $description = 'Overhaul global email wrapper and all 28 notification templates with Apple Mail-inspired design';

    public function handle(): int
    {
        $this->info('Starting Apple Mail Email System Overhaul...');

        // 1. Update Global Email Template in General Settings
        $globalHtml = $this->getAppleGlobalEmailTemplate();
        $gs = GeneralSetting::first();
        if ($gs) {
            $gs->email_template = $globalHtml;
            $gs->save();
            $this->info('✓ Updated GeneralSetting global email template.');
        }

        // 2. Update All 28 Notification Templates
        $templates = $this->getNotificationTemplatesData();
        $updated = 0;

        foreach ($templates as $act => $data) {
            $tpl = NotificationTemplate::where('act', $act)->first();
            if ($tpl) {
                $tpl->subject = $data['subject'];
                $tpl->email_body = $data['email_body'];
                $tpl->email_status = 1;
                $tpl->save();
                $updated++;
            }
        }

        $this->info("✓ Successfully updated {$updated} notification templates in database with Apple Mail design!");

        return 0;
    }

    private function getAppleGlobalEmailTemplate(): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{site_name}} Notification</title>
    <!--[if mso]>
    <style type="text/css">
    body, table, td {font-family: Arial, Helvetica, sans-serif !important;}
    </style>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f5f5f7; font-family: -apple-system, BlinkMacSystemFont, \'SF Pro Text\', \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; color: #1d1d1f; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: 100%;">
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f5f5f7; padding: 40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 580px; background-color: #ffffff; border-radius: 14px; border: 1px solid #e5e5ea; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04); overflow: hidden;">
                    <!-- Apple Header -->
                    <tr>
                        <td style="padding: 28px 36px; border-bottom: 1px solid #f2f2f7; text-align: left;">
                            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>
                                        <div style="font-size: 19px; font-weight: 700; color: #000000; letter-spacing: -0.02em; display: inline-flex; align-items: center; gap: 6px;">
                                            {{site_name}}
                                        </div>
                                    </td>
                                    <td align="right">
                                        <span style="font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: #86868b; background: #f5f5f7; padding: 5px 10px; border-radius: 20px;">
                                            Official Notice
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Email Main Body Content -->
                    <tr>
                        <td style="padding: 36px; font-size: 14.5px; line-height: 1.65; color: #333336;">
                            {{message}}
                        </td>
                    </tr>

                    <!-- Apple Footer -->
                    <tr>
                        <td style="background-color: #fbfbfd; padding: 24px 36px; border-top: 1px solid #f2f2f7; text-align: center; font-size: 12px; color: #86868b; line-height: 1.6;">
                            <p style="margin: 0 0 6px 0;">This email was sent to you as a registered customer of <strong>{{site_name}}</strong>.</p>
                            <p style="margin: 0 0 10px 0;">Need assistance? Access the <a href="https://zodpanel.zodserver.cloud" style="color: #0071e3; text-decoration: none; font-weight: 500;">Support Desk & Client Portal</a>.</p>
                            <p style="margin: 0; font-size: 11px; color: #a1a1a6;">© {{site_name}} Infrastructure • All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    private function getNotificationTemplatesData(): array
    {
        return [
            'HOSTING_ACCOUNT' => [
                'subject' => 'Your Hosting Account is Ready — Account & Panel Credentials',
                'email_body' => '<h2 style="font-size: 20px; font-weight: 600; color: #000000; margin: 0 0 12px 0;">Welcome to {{site_name}} Hosting</h2>
<p style="margin: 0 0 20px 0; color: #424245;">Your new hosting service has been provisioned and is ready for use on our high-speed NVMe cluster.</p>

<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #fbfbfd; border: 1px solid #e5e5ea; border-radius: 10px; margin-bottom: 24px; padding: 16px 20px;">
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Domain / Host:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;"><code>{{domain}}</code></td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Server IP:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;"><code>{{server_ip}}</code></td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Username:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;"><code>{{username}}</code></td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Password:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;"><code>{{password}}</code></td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Control Panel URL:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #0071e3; text-align: right;"><a href="https://zodpanel.zodserver.cloud:8083" style="color: #0071e3; text-decoration: none;">zodpanel.zodserver.cloud:8083</a></td></tr>
</table>

<h3 style="font-size: 14px; font-weight: 600; color: #1d1d1f; margin: 0 0 8px 0;">Nameserver Configuration</h3>
<p style="margin: 0 0 16px 0; font-size: 13px; color: #6e6e73;">Point your domain registrar nameservers to:</p>
<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #f5f5f7; border-radius: 8px; padding: 12px 16px; margin-bottom: 24px;">
    <tr><td style="font-size: 12.5px; font-family: ui-monospace, Menlo, Consolas, monospace; color: #1d1d1f;">{{nameserver_first}}</td></tr>
    <tr><td style="font-size: 12.5px; font-family: ui-monospace, Menlo, Consolas, monospace; color: #1d1d1f; padding-top: 4px;">{{nameserver_second}}</td></tr>
</table>

<div style="text-align: center; margin: 28px 0 10px 0;">
    <a href="https://zodpanel.zodserver.cloud:8083" style="background: #000000; color: #ffffff; padding: 12px 28px; border-radius: 980px; font-size: 14px; font-weight: 500; text-decoration: none; display: inline-block;">Log in to ZodPanel</a>
</div>'
            ],

            'ORDER_NOTIFICATION' => [
                'subject' => 'Order Confirmation #{{order_number}} — {{site_name}}',
                'email_body' => '<h2 style="font-size: 20px; font-weight: 600; color: #000000; margin: 0 0 8px 0;">Thank you for your order</h2>
<p style="margin: 0 0 20px 0; color: #424245;">We have received your order <strong>#{{order_number}}</strong>. Below is your summary receipt.</p>

<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #fbfbfd; border: 1px solid #e5e5ea; border-radius: 10px; margin-bottom: 24px; padding: 18px 20px;">
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Order Number:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;">#{{order_number}}</td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Total Amount:</td><td style="padding: 6px 0; font-size: 15px; font-weight: 700; color: #000000; text-align: right;">{{currency_symbol}}{{order_amount}}</td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Payment Status:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #34c759; text-align: right;">Completed</td></tr>
</table>

<div style="text-align: center; margin: 28px 0 10px 0;">
    <a href="{{order_link}}" style="background: #000000; color: #ffffff; padding: 12px 28px; border-radius: 980px; font-size: 14px; font-weight: 500; text-decoration: none; display: inline-block;">View Order & Invoice</a>
</div>'
            ],

            'INVOICE_PAYMENT_REMINDER' => [
                'subject' => 'Invoice Payment Reminder #{{invoice_number}}',
                'email_body' => '<h2 style="font-size: 20px; font-weight: 600; color: #000000; margin: 0 0 8px 0;">Invoice Payment Notice</h2>
<p style="margin: 0 0 20px 0; color: #424245;">This is a friendly reminder that Invoice <strong>#{{invoice_number}}</strong> is due for payment on <strong>{{due_date}}</strong>.</p>

<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #fbfbfd; border: 1px solid #e5e5ea; border-radius: 10px; margin-bottom: 24px; padding: 18px 20px;">
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Invoice Number:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;">#{{invoice_number}}</td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Amount Due:</td><td style="padding: 6px 0; font-size: 15px; font-weight: 700; color: #000000; text-align: right;">{{currency_symbol}}{{amount}}</td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Due Date:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #ff9500; text-align: right;">{{due_date}}</td></tr>
</table>

<div style="text-align: center; margin: 28px 0 10px 0;">
    <a href="{{invoice_link}}" style="background: #0071e3; color: #ffffff; padding: 12px 28px; border-radius: 980px; font-size: 14px; font-weight: 500; text-decoration: none; display: inline-block;">Pay Invoice Online</a>
</div>'
            ],

            'DEPOSIT_COMPLETE' => [
                'subject' => 'Payment Received — {{currency_symbol}}{{amount}}',
                'email_body' => '<h2 style="font-size: 20px; font-weight: 600; color: #000000; margin: 0 0 8px 0;">Payment Confirmation</h2>
<p style="margin: 0 0 20px 0; color: #424245;">Your payment has been successfully processed and credited to your account.</p>

<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #fbfbfd; border: 1px solid #e5e5ea; border-radius: 10px; margin-bottom: 24px; padding: 18px 20px;">
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Amount Credited:</td><td style="padding: 6px 0; font-size: 15px; font-weight: 700; color: #34c759; text-align: right;">+{{currency_symbol}}{{amount}}</td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Transaction ID:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;"><code>{{trx}}</code></td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Payment Method:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;">{{gateway_name}}</td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Updated Balance:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;">{{currency_symbol}}{{post_balance}}</td></tr>
</table>'
            ],

            'PASS_RESET_CODE' => [
                'subject' => 'Password Reset Code: {{code}}',
                'email_body' => '<h2 style="font-size: 20px; font-weight: 600; color: #000000; margin: 0 0 8px 0;">Reset Your Password</h2>
<p style="margin: 0 0 20px 0; color: #424245;">We received a request to reset your password for your {{site_name}} account. Use the secure authorization code below:</p>

<div style="background: #f5f5f7; border: 1px solid #e5e5ea; border-radius: 12px; padding: 20px; text-align: center; margin: 24px 0;">
    <span style="font-size: 32px; font-weight: 700; letter-spacing: 6px; color: #000000; font-family: ui-monospace, Menlo, Consolas, monospace;">{{code}}</span>
</div>

<p style="font-size: 12.5px; color: #86868b; margin: 0;">If you did not request this password reset, please ignore this email or contact support immediately.</p>'
            ],

            'EVER_CODE' => [
                'subject' => 'Verify Your Email Address — Code: {{code}}',
                'email_body' => '<h2 style="font-size: 20px; font-weight: 600; color: #000000; margin: 0 0 8px 0;">Confirm Your Email Address</h2>
<p style="margin: 0 0 20px 0; color: #424245;">Welcome to {{site_name}}! Please enter the 6-digit confirmation code below to verify your email address and activate your account:</p>

<div style="background: #f5f5f7; border: 1px solid #e5e5ea; border-radius: 12px; padding: 20px; text-align: center; margin: 24px 0;">
    <span style="font-size: 32px; font-weight: 700; letter-spacing: 6px; color: #000000; font-family: ui-monospace, Menlo, Consolas, monospace;">{{code}}</span>
</div>'
            ],

            'ADMIN_SUPPORT_REPLY' => [
                'subject' => 'Support Ticket #{{ticket_id}} Update',
                'email_body' => '<h2 style="font-size: 20px; font-weight: 600; color: #000000; margin: 0 0 8px 0;">New Support Reply</h2>
<p style="margin: 0 0 16px 0; color: #424245;">A support specialist has responded to ticket <strong>#{{ticket_id}}</strong> ({{ticket_subject}}):</p>

<div style="background: #fbfbfd; border-left: 3px solid #0071e3; padding: 16px 20px; border-radius: 0 8px 8px 0; margin-bottom: 24px; color: #1d1d1f; font-size: 14px; line-height: 1.6;">
    {{message}}
</div>

<div style="text-align: center; margin: 24px 0 10px 0;">
    <a href="{{link}}" style="background: #000000; color: #ffffff; padding: 12px 28px; border-radius: 980px; font-size: 14px; font-weight: 500; text-decoration: none; display: inline-block;">View Ticket in Portal</a>
</div>'
            ],

            'VPS_SERVER' => [
                'subject' => 'Your Cloud VPS / Dedicated Server Credentials',
                'email_body' => '<h2 style="font-size: 20px; font-weight: 600; color: #000000; margin: 0 0 12px 0;">Your Server is Provisioned & Active</h2>
<p style="margin: 0 0 20px 0; color: #424245;">Your high-performance server instance is online and ready for deployment.</p>

<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #fbfbfd; border: 1px solid #e5e5ea; border-radius: 10px; margin-bottom: 24px; padding: 16px 20px;">
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Server IP:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;"><code>{{ip}}</code></td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Root Username:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;"><code>{{username}}</code></td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Root Password:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;"><code>{{password}}</code></td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Control Panel:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #0071e3; text-align: right;"><a href="https://zodpanel.zodserver.cloud:8083" style="color: #0071e3; text-decoration: none;">zodpanel.zodserver.cloud:8083</a></td></tr>
</table>

<div style="text-align: center; margin: 28px 0 10px 0;">
    <a href="https://zodpanel.zodserver.cloud:8083" style="background: #000000; color: #ffffff; padding: 12px 28px; border-radius: 980px; font-size: 14px; font-weight: 500; text-decoration: none; display: inline-block;">Connect to Server Console</a>
</div>'
            ],

            'DOMAIN_REGISTER' => [
                'subject' => 'Domain Registration Completed — {{domain}}',
                'email_body' => '<h2 style="font-size: 20px; font-weight: 600; color: #000000; margin: 0 0 12px 0;">Domain Registered Successfully</h2>
<p style="margin: 0 0 20px 0; color: #424245;">Your domain name <strong>{{domain}}</strong> has been registered and assigned to your account.</p>

<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #fbfbfd; border: 1px solid #e5e5ea; border-radius: 10px; margin-bottom: 24px; padding: 16px 20px;">
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Domain:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;"><code>{{domain}}</code></td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Registration Period:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;">{{reg_period}} Year(s)</td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Renewal Date:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;">{{expiry_date}}</td></tr>
</table>'
            ],

            'SERVICE_SUSPEND' => [
                'subject' => 'Service Suspended — {{service_name}}',
                'email_body' => '<h2 style="font-size: 20px; font-weight: 600; color: #ff3b30; margin: 0 0 12px 0;">Service Suspension Notice</h2>
<p style="margin: 0 0 20px 0; color: #424245;">Your service <strong>{{service_name}}</strong> has been suspended due to: <em>{{service_suspension_reason}}</em>.</p>

<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #fff5f5; border: 1px solid #ffccd0; border-radius: 10px; margin-bottom: 24px; padding: 16px 20px;">
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Service:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;">{{service_name}}</td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Status:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #ff3b30; text-align: right;">Suspended</td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Due Date:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;">{{service_next_due_date}}</td></tr>
</table>

<p style="font-size: 13px; color: #424245; margin: 0 0 24px 0;">To immediately unsuspend and restore your service, please settle your outstanding invoice.</p>

<div style="text-align: center; margin: 28px 0 10px 0;">
    <a href="{{invoice_link}}" style="background: #000000; color: #ffffff; padding: 12px 28px; border-radius: 980px; font-size: 14px; font-weight: 500; text-decoration: none; display: inline-block;">Pay Invoice & Restore Service</a>
</div>'
            ],

            'SERVICE_UNSUSPEND' => [
                'subject' => 'Service Restored & Active — {{service_name}}',
                'email_body' => '<h2 style="font-size: 20px; font-weight: 600; color: #34c759; margin: 0 0 12px 0;">Service Successfully Restored</h2>
<p style="margin: 0 0 20px 0; color: #424245;">Your service <strong>{{service_name}}</strong> has been reactivated and full access is now restored.</p>

<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #fbfbfd; border: 1px solid #e5e5ea; border-radius: 10px; margin-bottom: 24px; padding: 16px 20px;">
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Service:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;">{{service_name}}</td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Status:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #34c759; text-align: right;">Active</td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Next Due Date:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;">{{service_next_due_date}}</td></tr>
</table>

<div style="text-align: center; margin: 28px 0 10px 0;">
    <a href="https://zodpanel.zodserver.cloud:8083" style="background: #000000; color: #ffffff; padding: 12px 28px; border-radius: 980px; font-size: 14px; font-weight: 500; text-decoration: none; display: inline-block;">Open Control Panel</a>
</div>'
            ],

            'FIRST_INVOICE_OVERDUE_NOTICE' => [
                'subject' => 'Invoice Overdue Notice (1st Warning) — #{{invoice_number}}',
                'email_body' => '<h2 style="font-size: 20px; font-weight: 600; color: #ff9500; margin: 0 0 12px 0;">Invoice Payment Overdue</h2>
<p style="margin: 0 0 20px 0; color: #424245;">Invoice <strong>#{{invoice_number}}</strong> was due on <strong>{{invoice_due_date}}</strong> and is now overdue.</p>

<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #fbfbfd; border: 1px solid #e5e5ea; border-radius: 10px; margin-bottom: 24px; padding: 16px 20px;">
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Invoice #:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;">#{{invoice_number}}</td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Due Date:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #ff3b30; text-align: right;">{{invoice_due_date}}</td></tr>
</table>

<div style="text-align: center; margin: 28px 0 10px 0;">
    <a href="{{invoice_link}}" style="background: #000000; color: #ffffff; padding: 12px 28px; border-radius: 980px; font-size: 14px; font-weight: 500; text-decoration: none; display: inline-block;">Pay Overdue Invoice</a>
</div>'
            ],

            'SECOND_INVOICE_OVERDUE_NOTICE' => [
                'subject' => 'Urgent: Invoice Overdue (2nd Notice) — #{{invoice_number}}',
                'email_body' => '<h2 style="font-size: 20px; font-weight: 600; color: #ff3b30; margin: 0 0 12px 0;">Urgent Overdue Notice</h2>
<p style="margin: 0 0 20px 0; color: #424245;">Invoice <strong>#{{invoice_number}}</strong> remains unpaid. To prevent automated service suspension, please submit payment promptly.</p>

<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #fbfbfd; border: 1px solid #e5e5ea; border-radius: 10px; margin-bottom: 24px; padding: 16px 20px;">
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Invoice #:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;">#{{invoice_number}}</td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Due Date:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #ff3b30; text-align: right;">{{invoice_due_date}}</td></tr>
</table>

<div style="text-align: center; margin: 28px 0 10px 0;">
    <a href="{{invoice_link}}" style="background: #ff3b30; color: #ffffff; padding: 12px 28px; border-radius: 980px; font-size: 14px; font-weight: 500; text-decoration: none; display: inline-block;">Pay Now to Prevent Suspension</a>
</div>'
            ],

            'THIRD_INVOICE_OVERDUE_NOTICE' => [
                'subject' => 'Final Notice Before Suspension — #{{invoice_number}}',
                'email_body' => '<h2 style="font-size: 20px; font-weight: 600; color: #ff3b30; margin: 0 0 12px 0;">Final Notice: Immediate Action Required</h2>
<p style="margin: 0 0 20px 0; color: #424245;">This is your final notice regarding unpaid Invoice <strong>#{{invoice_number}}</strong>. Your service will be automatically suspended shortly unless payment is settled.</p>

<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #fbfbfd; border: 1px solid #e5e5ea; border-radius: 10px; margin-bottom: 24px; padding: 16px 20px;">
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Invoice #:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #1d1d1f; text-align: right;">#{{invoice_number}}</td></tr>
    <tr><td style="padding: 6px 0; font-size: 13px; color: #86868b;">Status:</td><td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #ff3b30; text-align: right;">Pending Suspension</td></tr>
</table>

<div style="text-align: center; margin: 28px 0 10px 0;">
    <a href="{{invoice_link}}" style="background: #ff3b30; color: #ffffff; padding: 12px 28px; border-radius: 980px; font-size: 14px; font-weight: 600; text-decoration: none; display: inline-block;">Pay Immediately</a>
</div>'
            ],
        ];
    }
}

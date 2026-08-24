<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ZodHostContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedPages();
        $this->seedCoreSections();
        $this->seedPolicies();
        $this->seedFaqs();
        $this->seedAnnouncements();
    }

    private function seedSettings(): void
    {
        DB::table('general_settings')->where('id', 1)->update([
            'site_name' => 'ZodHost',
            'email_from' => 'support@zodhost.com',
            'email_from_name' => 'ZodHost Support',
            'sms_from' => 'ZodHost',
            'push_title' => 'ZodHost',
            'base_color' => '2563EB',
            'cur_text' => 'USD',
            'cur_sym' => '$',
            'active_template' => 'basic',
            'updated_at' => now(),
        ]);

        Cache::forget('GeneralSetting');
    }

    private function seedPages(): void
    {
        $template = activeTemplate();
        $pages = [
            ['name' => 'Home', 'slug' => '/', 'is_default' => 1, 'secs' => ['domain', 'service']],
            ['name' => 'Contact', 'slug' => 'contact', 'is_default' => 1, 'secs' => null],
            ['name' => 'Faq', 'slug' => 'faq', 'is_default' => 0, 'secs' => ['faq']],
            ['name' => 'Announcement', 'slug' => 'announcements', 'is_default' => 1, 'secs' => null],
            ['name' => 'About ZodHost', 'slug' => 'about-zodhost', 'is_default' => 0, 'secs' => ['about']],
        ];

        DB::table('pages')
            ->where('tempname', activeTemplateName())
            ->whereIn('slug', array_column($pages, 'slug'))
            ->delete();

        foreach ($pages as $page) {
            DB::table('pages')->updateOrInsert(
                ['slug' => $page['slug'], 'tempname' => $template],
                [
                    'name' => $page['name'],
                    'secs' => $page['secs'] ? json_encode($page['secs']) : null,
                    'seo_content' => json_encode([
                        'keywords' => ['ZodHost', 'hosting', 'VPS', 'domains', 'WHMPanel'],
                        'description' => 'ZodHost provides clean hosting, VPS, domains, remote desktop, radio streaming, and support workflows for growing online businesses.',
                    ]),
                    'is_default' => $page['is_default'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    private function seedCoreSections(): void
    {
        $this->dedupeSingletonSections([
            'seo.data',
            'about.content',
            'blog.content',
            'contact_us.content',
            'counter.content',
            'footer.content',
            'domain.content',
            'subscribe.content',
            'invoice_address.content',
            'banned.content',
            'kyc.content',
            'cookie.data',
        ]);

        $sections = [
            'seo.data' => [
                'image' => null,
                'keywords' => ['ZodHost', 'web hosting', 'VPS hosting', 'domain registration', 'WHMPanel', 'remote desktop', 'Shoutcast hosting'],
                'description' => 'ZodHost delivers shared hosting, VPS, dedicated servers, remote desktop, radio streaming, domains, billing, and support in one clean client platform.',
                'social_title' => 'ZodHost - Hosting, VPS, Domains and Support',
                'social_description' => 'Launch and manage hosting services with transparent pricing, clean billing, account-aware support, and WHMPanel integration.',
            ],
            'about.content' => [
                'has_image' => '1',
                'heading' => 'Built for hosting teams that care about clarity',
                'sub_heading' => 'ZodHost keeps infrastructure, billing, domains, and support connected.',
                'description' => 'ZodHost was built for customers who want reliable hosting without confusing dashboards. From starter shared hosting to VPS, dedicated servers, remote desktop, and streaming plans, every service is presented clearly with renewal dates, support access, and account details in one place.',
                'about_icon' => '<i class="las la-server"></i>',
            ],
            'blog.content' => [
                'heading' => 'ZodHost Announcements',
                'subheading' => 'Product updates, infrastructure notes, and practical hosting guidance from the ZodHost team.',
            ],
            'contact_us.content' => [
                'heading' => 'Talk to ZodHost',
                'description' => 'Need help choosing a plan, moving a site, configuring DNS, or resolving a service issue? Our team can review the account and guide the next step.',
                'email' => 'support@zodhost.com',
                'phone' => '+1 302 555 0198',
                'address' => 'ZodHost Operations, 1209 Orange Street, Wilmington, DE 19801, United States',
            ],
            'counter.content' => [
                'heading' => 'Reliable hosting, measured clearly',
                'sub_heading' => 'ZodHost tracks services, invoices, support, and panel access from one workspace.',
            ],
            'footer.content' => [
                'description' => 'ZodHost provides shared hosting, VPS, dedicated servers, RDP, radio streaming, domains, billing, and support tools with a clean client experience.',
            ],
            'domain.content' => [
                'heading' => 'Find the right domain for your next project',
                'subheading' => 'Search, register, and connect domains to ZodHost hosting plans without extra steps.',
                'text' => 'Check availability, compare popular TLDs, register the name, and point it to your active hosting service from the same account.',
            ],
            'subscribe.content' => [
                'heading' => 'Get ZodHost service updates',
                'subheading' => 'Receive infrastructure notices, product updates, domain reminders, and hosting tips. No filler, only useful service information.',
            ],
            'invoice_address.content' => [
                'address' => "ZodHost LLC\n1209 Orange Street\nWilmington, DE 19801\nUnited States\nsupport@zodhost.com",
            ],
            'banned.content' => [
                'heading' => 'This account is currently restricted',
                'has_image' => '1',
                'description' => 'Please contact ZodHost support if you believe this restriction was applied in error or if you need help resolving an account issue.',
            ],
            'kyc.content' => [
                'kyc_required' => 'Identity verification is required before this action can be completed. Please submit the requested account documents.',
                'kyc_pending' => 'Your verification documents are under review. ZodHost will notify you when the review is complete.',
                'kyc_reject' => 'Your verification submission was not approved. Please review the reason, update the documents, and submit again.',
            ],
        ];

        foreach ($sections as $key => $values) {
            $query = DB::table('frontends')->where('data_keys', $key);

            if ($query->exists()) {
                $query->update([
                    'data_values' => json_encode($values),
                    'tempname' => $key === 'seo.data' ? null : 'basic',
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('frontends')->insert([
                    'data_keys' => $key,
                    'data_values' => json_encode($values),
                    'tempname' => $key === 'seo.data' ? null : 'basic',
                    'slug' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        DB::table('frontends')->where('data_keys', 'cookie.data')->update([
            'data_values' => json_encode([
                'status' => 1,
                'short_desc' => 'ZodHost uses essential cookies for login sessions, cart activity, fraud prevention, support workflows, and service preferences.',
                'description' => $this->html([
                    ['How ZodHost uses cookies', 'We use cookies to keep you signed in, remember cart choices, protect forms, process checkout, and improve support. Some cookies are required for the platform to work correctly.'],
                    ['Analytics and preferences', 'When enabled, analytics help us understand which pages and workflows need improvement. Preference cookies may remember language, currency, and interface choices.'],
                    ['Your choices', 'You can disable non-essential cookies in your browser. Required session and security cookies are needed for account, billing, and support features.'],
                ]),
            ]),
            'updated_at' => now(),
        ]);
    }

    private function dedupeSingletonSections(array $keys): void
    {
        foreach ($keys as $key) {
            $ids = DB::table('frontends')->where('data_keys', $key)->orderBy('id')->pluck('id');

            if ($ids->count() > 1) {
                DB::table('frontends')->where('data_keys', $key)->whereNot('id', $ids->first())->delete();
            }
        }
    }

    private function seedPolicies(): void
    {
        $policies = [
            'privacy-policy' => [
                'title' => 'Privacy Policy',
                'details' => $this->html([
                    ['Information we collect', 'ZodHost collects the information needed to create accounts, process payments, register domains, provide support, prevent abuse, and operate hosting services. This may include name, email, phone, billing details, service records, IP addresses, login history, and support messages.'],
                    ['How we use information', 'We use account data to provision services, send invoices and renewal reminders, respond to tickets, secure the platform, and meet legal or registrar requirements. We do not sell customer data.'],
                    ['Payment and domain data', 'Payment details are handled by supported payment providers. Domain registration may require contact details to be shared with registrars or registry operators according to domain rules.'],
                    ['Security and retention', 'We use access controls, audit logs, encryption where appropriate, and limited staff access. Records are kept only as long as needed for service, compliance, fraud prevention, or accounting.'],
                ]),
            ],
            'terms-of-service' => [
                'title' => 'Terms of Service',
                'details' => $this->html([
                    ['Service use', 'ZodHost services must be used for lawful websites, applications, communication, and business operations. Customers are responsible for the content, files, scripts, and traffic generated by their services.'],
                    ['Billing and renewals', 'Services renew according to the selected billing cycle. Unpaid invoices may lead to reminders, suspension, and eventual termination according to the service grace period shown in the client area.'],
                    ['Acceptable use', 'Spam, phishing, malware, credential theft, abusive automation, illegal content, and activity that harms network stability are not allowed. ZodHost may suspend services to protect customers and infrastructure.'],
                    ['Backups and responsibility', 'ZodHost may provide backup features for selected plans, but customers should keep independent backups of important data. Managed recovery depends on the service type and backup availability.'],
                    ['Support', 'Support is provided through tickets, live chat where available, and account-aware tools such as support PINs. Staff may request verification before discussing account-specific services.'],
                ]),
            ],
        ];

        foreach ($policies as $slug => $policy) {
            DB::table('frontends')->updateOrInsert(
                ['data_keys' => 'policy_pages.element', 'slug' => $slug, 'tempname' => 'basic'],
                [
                    'data_values' => json_encode($policy),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    private function seedFaqs(): void
    {
        DB::table('frontends')->where('data_keys', 'faq.element')->delete();

        $faqs = [
            ['Which ZodHost plan should I start with?', 'Start with Basic Shared Hosting for small sites, Premium Shared Hosting for busier WordPress or business sites, Budget VPS for lightweight apps, and KVM NVMe VPS for stronger isolation and performance.'],
            ['Can I register a domain during checkout?', 'Yes. You can register a new domain, use an existing domain, or update nameservers after checkout. Domain pricing and renewal terms are shown before purchase.'],
            ['How does WHMPanel fit into ZodHost?', 'WHMPanel is the ZodHost control panel layer for hosting services. It connects billing, provisioning, account access, support, and service management without sending customers through disconnected tools.'],
            ['What is a support PIN?', 'A support PIN is a temporary verification code shown in the client dashboard. It helps staff locate and verify the correct account or service without exposing passwords.'],
            ['Can I upgrade a hosting or VPS plan later?', 'Yes. Eligible services can be upgraded from the client area or through support. Billing adjustments depend on the active plan, renewal date, and selected target package.'],
            ['Do you provide backups?', 'Backup availability depends on the plan. Shared and premium hosting plans include backup options, while VPS and dedicated customers should also maintain their own off-server backup strategy.'],
            ['What happens when an invoice is overdue?', 'ZodHost sends reminders before suspension. If payment remains overdue after the grace period, services may be suspended until the invoice is paid.'],
            ['Can I manage multiple services from one account?', 'Yes. The client area shows hosting, VPS, dedicated servers, remote desktop, radio streaming, domains, invoices, tickets, and support tools in one workspace.'],
        ];

        foreach ($faqs as [$question, $answer]) {
            DB::table('frontends')->insert([
                'data_keys' => 'faq.element',
                'data_values' => json_encode(compact('question', 'answer')),
                'tempname' => 'basic',
                'slug' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedAnnouncements(): void
    {
        DB::table('frontends')->where('data_keys', 'blog.element')->delete();

        $posts = [
            [
                'title' => 'ZodHost launches a cleaner hosting client experience',
                'description' => 'ZodHost now brings hosting, VPS, domains, invoices, support tickets, and service access into a cleaner client area. The goal is simple: customers should see what they own, when it renews, where it points, and how to get help without hunting through unrelated screens.',
            ],
            [
                'title' => 'WHMPanel integration prepares ZodHost for faster provisioning',
                'description' => 'The WHMPanel foundation connects ZodHost billing to hosting nodes, service records, account summaries, support PIN workflows, and one-click panel access. This makes provisioning and support more consistent across shared hosting, VPS, dedicated, RDP, and streaming services.',
            ],
            [
                'title' => 'Choosing between shared hosting, VPS, and dedicated servers',
                'description' => 'Shared hosting is best for websites that need a simple managed environment. VPS plans are better for applications that need dedicated resources and root-level control. Dedicated servers fit high-traffic, virtualization, streaming, and business platforms that need full hardware allocation.',
            ],
            [
                'title' => 'Domain and DNS basics for new ZodHost customers',
                'description' => 'A domain points visitors to your hosting service through nameservers and DNS records. ZodHost shows assigned provider nameservers inside the service details page, making it easier to connect existing domains or manage newly registered names.',
            ],
        ];

        foreach ($posts as $post) {
            DB::table('frontends')->insert([
                'data_keys' => 'blog.element',
                'data_values' => json_encode([
                    'title' => $post['title'],
                    'description' => '<p>' . e($post['description']) . '</p>',
                ]),
                'tempname' => 'basic',
                'slug' => Str::slug($post['title']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function html(array $sections): string
    {
        return collect($sections)->map(function ($section) {
            return '<div class="mb-4"><h3 class="mb-2">' . e($section[0]) . '</h3><p>' . e($section[1]) . '</p></div>';
        })->implode('');
    }
}

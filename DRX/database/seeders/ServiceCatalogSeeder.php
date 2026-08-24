<?php

namespace Database\Seeders;

use App\Models\Pricing;
use App\Models\Product;
use App\Models\DomainPricing;
use App\Models\DomainSetup;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedDomainTlds();

        foreach ($this->catalog() as $categoryData) {
            $category = ServiceCategory::updateOrCreate(
                ['slug' => $categoryData['slug']],
                [
                    'name' => $categoryData['name'],
                    'short_description' => $categoryData['description'],
                    'status' => 1,
                ]
            );

            foreach ($categoryData['products'] as $productData) {
                $product = Product::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'slug' => $productData['slug'],
                    ],
                    [
                        'server_group_id' => 0,
                        'server_id' => 0,
                        'package_name' => $productData['package_name'],
                        'product_type' => $productData['product_type'],
                        'payment_type' => 2,
                        'module_option' => 3,
                        'name' => $productData['name'],
                        'description' => $productData['description'],
                        'welcome_email' => $productData['welcome_email'],
                        'domain_register' => $productData['domain_register'],
                        'stock_control' => 0,
                        'stock_quantity' => 0,
                        'status' => 1,
                    ]
                );

                Pricing::updateOrCreate(
                    [
                        'type' => 'product',
                        'product_id' => $product->id,
                    ],
                    $this->pricing($productData['monthly'], $productData['setup_fee'] ?? 0)
                );
            }
        }
    }

    private function seedDomainTlds(): void
    {
        foreach ($this->domainTlds() as $extension => $price) {
            $domain = DomainSetup::updateOrCreate(
                ['extension' => $extension],
                [
                    'id_protection' => 1,
                    'status' => 1,
                ]
            );

            DomainPricing::updateOrCreate(
                ['domain_id' => $domain->id],
                $this->domainPricing($price)
            );
        }
    }

    private function domainPricing(float $oneYearPrice): array
    {
        $idProtection = 3.99;

        return [
            'one_year_price' => $oneYearPrice,
            'one_year_id_protection' => $idProtection,
            'one_year_renew' => round($oneYearPrice * 1.08, 2),
            'two_year_price' => round($oneYearPrice * 2 * 0.97, 2),
            'two_year_id_protection' => round($idProtection * 2, 2),
            'two_year_renew' => round($oneYearPrice * 2 * 1.05, 2),
            'three_year_price' => round($oneYearPrice * 3 * 0.95, 2),
            'three_year_id_protection' => round($idProtection * 3, 2),
            'three_year_renew' => round($oneYearPrice * 3 * 1.03, 2),
            'four_year_price' => round($oneYearPrice * 4 * 0.93, 2),
            'four_year_id_protection' => round($idProtection * 4, 2),
            'four_year_renew' => round($oneYearPrice * 4 * 1.02, 2),
            'five_year_price' => round($oneYearPrice * 5 * 0.90, 2),
            'five_year_id_protection' => round($idProtection * 5, 2),
            'five_year_renew' => round($oneYearPrice * 5, 2),
            'six_year_price' => round($oneYearPrice * 6 * 0.88, 2),
            'six_year_id_protection' => round($idProtection * 6, 2),
            'six_year_renew' => round($oneYearPrice * 6, 2),
        ];
    }

    private function pricing(float $monthly, float $setupFee = 0): array
    {
        return [
            'configurable_group_sub_option_id' => 0,
            'monthly_setup_fee' => $setupFee,
            'quarterly_setup_fee' => 0,
            'semi_annually_setup_fee' => 0,
            'annually_setup_fee' => 0,
            'biennially_setup_fee' => 0,
            'triennially_setup_fee' => 0,
            'monthly' => $monthly,
            'quarterly' => round($monthly * 3 * 0.97, 2),
            'semi_annually' => round($monthly * 6 * 0.94, 2),
            'annually' => round($monthly * 12 * 0.90, 2),
            'biennially' => round($monthly * 24 * 0.85, 2),
            'triennially' => round($monthly * 36 * 0.80, 2),
        ];
    }

    private function plan(
        string $name,
        float $monthly,
        array $features,
        int $productType,
        int $welcomeEmail,
        bool $domainRegister = false,
        float $setupFee = 0
    ): array {
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'package_name' => Str::slug($name),
            'monthly' => $monthly,
            'setup_fee' => $setupFee,
            'product_type' => $productType,
            'welcome_email' => $welcomeEmail,
            'domain_register' => $domainRegister ? 1 : 0,
            'description' => implode("\n", $features),
        ];
    }

    private function catalog(): array
    {
        return [
            [
                'name' => 'Basic Shared Hosting',
                'slug' => 'basic-shared-hosting',
                'description' => 'Affordable cPanel-style shared hosting for personal sites, landing pages, and small projects.',
                'products' => [
                    $this->plan('Starter Shared', 2.99, ['1 website', '10 GB SSD storage', '100 GB bandwidth', '5 email accounts', 'Free SSL'], 1, 1, true),
                    $this->plan('Basic Shared', 4.99, ['3 websites', '25 GB SSD storage', '250 GB bandwidth', '25 email accounts', 'Free SSL'], 1, 1, true),
                    $this->plan('Plus Shared', 7.99, ['10 websites', '50 GB SSD storage', '500 GB bandwidth', 'Unlimited email accounts', 'Daily backups'], 1, 1, true),
                    $this->plan('Business Shared', 11.99, ['25 websites', '100 GB SSD storage', '1 TB bandwidth', 'Priority support', 'Daily backups'], 1, 1, true),
                ],
            ],
            [
                'name' => 'Premium Shared Hosting',
                'slug' => 'premium-shared-hosting',
                'description' => 'Higher resource shared hosting for busier WordPress, ecommerce, and business websites.',
                'products' => [
                    $this->plan('Premium Starter', 8.99, ['5 websites', '50 GB NVMe storage', '1 TB bandwidth', '2 CPU cores', 'Daily malware scan'], 1, 1, true),
                    $this->plan('Premium Growth', 14.99, ['15 websites', '100 GB NVMe storage', '2 TB bandwidth', '4 CPU cores', 'Staging support'], 1, 1, true),
                    $this->plan('Premium Business', 24.99, ['50 websites', '200 GB NVMe storage', '4 TB bandwidth', '6 CPU cores', 'Priority support'], 1, 1, true),
                    $this->plan('Premium Enterprise', 39.99, ['Unlimited websites', '300 GB NVMe storage', '6 TB bandwidth', '8 CPU cores', 'Advanced backups'], 1, 1, true),
                ],
            ],
            [
                'name' => 'Budget VPS',
                'slug' => 'budget-vps',
                'description' => 'Low-cost virtual servers for development, lightweight apps, VPNs, and small production workloads.',
                'products' => [
                    $this->plan('Budget VPS 1', 5.99, ['1 vCPU', '1 GB RAM', '25 GB SSD storage', '1 TB transfer', '1 IPv4 address'], 3, 3),
                    $this->plan('Budget VPS 2', 9.99, ['2 vCPU', '2 GB RAM', '50 GB SSD storage', '2 TB transfer', '1 IPv4 address'], 3, 3),
                    $this->plan('Budget VPS 4', 17.99, ['4 vCPU', '4 GB RAM', '80 GB SSD storage', '3 TB transfer', '1 IPv4 address'], 3, 3),
                    $this->plan('Budget VPS 8', 29.99, ['6 vCPU', '8 GB RAM', '160 GB SSD storage', '5 TB transfer', '1 IPv4 address'], 3, 3),
                ],
            ],
            [
                'name' => 'KVM NVMe VPS',
                'slug' => 'kvm-nvme-vps',
                'description' => 'Fast KVM virtual servers with NVMe storage for apps, databases, and high traffic services.',
                'products' => [
                    $this->plan('KVM NVMe Starter', 12.99, ['2 vCPU', '4 GB RAM', '60 GB NVMe storage', '3 TB transfer', '1 IPv4 address'], 3, 3),
                    $this->plan('KVM NVMe Pro', 24.99, ['4 vCPU', '8 GB RAM', '120 GB NVMe storage', '5 TB transfer', '1 IPv4 address'], 3, 3),
                    $this->plan('KVM NVMe Business', 49.99, ['8 vCPU', '16 GB RAM', '240 GB NVMe storage', '8 TB transfer', '1 IPv4 address'], 3, 3),
                    $this->plan('KVM NVMe Elite', 89.99, ['12 vCPU', '32 GB RAM', '480 GB NVMe storage', '12 TB transfer', '1 IPv4 address'], 3, 3),
                ],
            ],
            [
                'name' => 'Dedicated Server',
                'slug' => 'dedicated-server',
                'description' => 'Bare metal servers for demanding workloads, virtualization, streaming, and business platforms.',
                'products' => [
                    $this->plan('Dedicated Entry', 79.99, ['Intel Xeon E3 or equivalent', '16 GB RAM', '2 x 1 TB HDD', '10 TB transfer', '5 usable IPv4 addresses'], 3, 3, false, 25),
                    $this->plan('Dedicated Power', 139.99, ['Intel Xeon E5 or equivalent', '32 GB RAM', '2 x 1 TB SSD', '20 TB transfer', '5 usable IPv4 addresses'], 3, 3, false, 35),
                    $this->plan('Dedicated Pro', 229.99, ['Dual Xeon or equivalent', '64 GB RAM', '2 x 2 TB NVMe', '30 TB transfer', '10 usable IPv4 addresses'], 3, 3, false, 49),
                    $this->plan('Dedicated Enterprise', 349.99, ['High core-count CPU', '128 GB RAM', '4 x 2 TB NVMe', '50 TB transfer', '10 usable IPv4 addresses'], 3, 3, false, 75),
                ],
            ],
            [
                'name' => 'Remote Desktop',
                'slug' => 'remote-desktop',
                'description' => 'Windows remote desktop plans for browser work, automation, trading tools, and office applications.',
                'products' => [
                    $this->plan('RDP Starter', 14.99, ['2 vCPU', '4 GB RAM', '60 GB SSD storage', 'Windows Server', '1 user account'], 4, 4),
                    $this->plan('RDP Standard', 24.99, ['4 vCPU', '8 GB RAM', '100 GB SSD storage', 'Windows Server', '2 user accounts'], 4, 4),
                    $this->plan('RDP Business', 44.99, ['6 vCPU', '16 GB RAM', '200 GB SSD storage', 'Windows Server', '5 user accounts'], 4, 4),
                    $this->plan('RDP Performance', 79.99, ['8 vCPU', '32 GB RAM', '400 GB SSD storage', 'Windows Server', '10 user accounts'], 4, 4),
                ],
            ],
            [
                'name' => 'Radio Shoutcast',
                'slug' => 'radio-shoutcast',
                'description' => 'Shoutcast streaming packages for internet radio stations, DJs, churches, and live broadcasts.',
                'products' => [
                    $this->plan('Radio Starter', 4.99, ['50 listeners', '64 kbps bitrate', '2 GB AutoDJ storage', 'Live streaming', 'Basic statistics'], 4, 4),
                    $this->plan('Radio Plus', 9.99, ['100 listeners', '128 kbps bitrate', '5 GB AutoDJ storage', 'Live streaming', 'Listener statistics'], 4, 4),
                    $this->plan('Radio Pro', 19.99, ['250 listeners', '192 kbps bitrate', '15 GB AutoDJ storage', 'Live streaming', 'Advanced statistics'], 4, 4),
                    $this->plan('Radio Max', 34.99, ['500 listeners', '320 kbps bitrate', '30 GB AutoDJ storage', 'Live streaming', 'Priority support'], 4, 4),
                ],
            ],
        ];
    }

    private function domainTlds(): array
    {
        return [
            '.com' => 12.99,
            '.net' => 14.99,
            '.org' => 13.99,
            '.co' => 29.99,
            '.info' => 9.99,
            '.biz' => 11.99,
            '.online' => 19.99,
            '.site' => 17.99,
            '.com.ng' => 8.99,
            '.ng' => 24.99,
        ];
    }
}

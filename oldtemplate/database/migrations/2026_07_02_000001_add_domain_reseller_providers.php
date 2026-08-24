<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        foreach ($this->providers($now) as $provider) {
            $exists = DB::table('domain_registers')->where('alias', $provider['alias'])->exists();
            if (!$exists) {
                DB::table('domain_registers')->insert($provider);
            }
        }
    }

    public function down(): void
    {
        DB::table('domain_registers')
            ->whereIn('alias', ['NameSilo', 'ResellerClub', 'NetEarthOne', 'LogicBoxes', 'Porkbun'])
            ->delete();
    }

    private function providers($now): array
    {
        $logicBoxesParams = json_encode([
            'auth_user_id' => [
                'title' => 'Auth User Id (Reseller ID)',
                'required' => true,
                'value' => '',
            ],
            'api_key' => [
                'title' => 'Api Key',
                'required' => true,
                'value' => '',
            ],
        ]);

        return [
            [
                'name' => 'NameSilo',
                'alias' => 'NameSilo',
                'ns1' => 'ns1.dnsowl.com',
                'ns2' => 'ns2.dnsowl.com',
                'ns3' => 'ns3.dnsowl.com',
                'ns4' => null,
                'params' => json_encode([
                    'api_key' => [
                        'title' => 'API Key',
                        'required' => true,
                        'value' => '',
                    ],
                ]),
                'test_mode' => 1,
                'default' => 0,
                'status' => 0,
                'setup_done' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'ResellerClub',
                'alias' => 'ResellerClub',
                'ns1' => 'ns1.resellerclub.com',
                'ns2' => 'ns2.resellerclub.com',
                'ns3' => null,
                'ns4' => null,
                'params' => $logicBoxesParams,
                'test_mode' => 1,
                'default' => 0,
                'status' => 0,
                'setup_done' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'NetEarthOne',
                'alias' => 'NetEarthOne',
                'ns1' => 'ns1.netearthone.com',
                'ns2' => 'ns2.netearthone.com',
                'ns3' => null,
                'ns4' => null,
                'params' => $logicBoxesParams,
                'test_mode' => 1,
                'default' => 0,
                'status' => 0,
                'setup_done' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'LogicBoxes',
                'alias' => 'LogicBoxes',
                'ns1' => 'ns1.logicboxes.com',
                'ns2' => 'ns2.logicboxes.com',
                'ns3' => null,
                'ns4' => null,
                'params' => $logicBoxesParams,
                'test_mode' => 1,
                'default' => 0,
                'status' => 0,
                'setup_done' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Porkbun',
                'alias' => 'Porkbun',
                'ns1' => 'curitiba.ns.porkbun.com',
                'ns2' => 'fortaleza.ns.porkbun.com',
                'ns3' => 'maceio.ns.porkbun.com',
                'ns4' => 'salvador.ns.porkbun.com',
                'params' => json_encode([
                    'api_key' => [
                        'title' => 'API Key',
                        'required' => true,
                        'value' => '',
                    ],
                    'secret_api_key' => [
                        'title' => 'Secret API Key',
                        'required' => true,
                        'value' => '',
                    ],
                ]),
                'test_mode' => 0,
                'default' => 0,
                'status' => 0,
                'setup_done' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
    }
};

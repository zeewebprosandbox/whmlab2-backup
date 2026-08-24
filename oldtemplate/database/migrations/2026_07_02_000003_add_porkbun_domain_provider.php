<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('domain_registers')->where('alias', 'Porkbun')->exists();
        if ($exists) {
            return;
        }

        DB::table('domain_registers')->insert([
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
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('domain_registers')->where('alias', 'Porkbun')->delete();
    }
};

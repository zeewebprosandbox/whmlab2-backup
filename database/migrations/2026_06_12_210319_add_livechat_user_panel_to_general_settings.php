<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('general_settings', 'show_livechat_user_panel')) {
                $table->boolean('show_livechat_user_panel')->default(true)->after('registration');
            }
        });

        DB::table('general_settings')->update([
            'site_name' => 'ZolaHost',
            'base_color' => '2563EB',
            'show_livechat_user_panel' => true,
        ]);
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (Schema::hasColumn('general_settings', 'show_livechat_user_panel')) {
                $table->dropColumn('show_livechat_user_panel');
            }
        });
    }
};

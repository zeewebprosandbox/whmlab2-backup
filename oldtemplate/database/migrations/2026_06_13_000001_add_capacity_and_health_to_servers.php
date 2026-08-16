<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            if (!Schema::hasColumn('servers', 'service_role')) {
                $table->string('service_role', 40)->default('any')->after('type');
            }

            if (!Schema::hasColumn('servers', 'location')) {
                $table->string('location', 120)->nullable()->after('service_role');
            }

            if (!Schema::hasColumn('servers', 'max_accounts')) {
                $table->unsignedInteger('max_accounts')->default(0)->after('ip_address');
            }

            if (!Schema::hasColumn('servers', 'current_accounts')) {
                $table->unsignedInteger('current_accounts')->default(0)->after('max_accounts');
            }

            if (!Schema::hasColumn('servers', 'health_status')) {
                $table->string('health_status', 30)->default('unknown')->after('current_accounts');
            }

            if (!Schema::hasColumn('servers', 'health_message')) {
                $table->string('health_message', 255)->nullable()->after('health_status');
            }

            if (!Schema::hasColumn('servers', 'health_checked_at')) {
                $table->timestamp('health_checked_at')->nullable()->after('health_message');
            }
        });
    }

    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            foreach ([
                'service_role',
                'location',
                'max_accounts',
                'current_accounts',
                'health_status',
                'health_message',
                'health_checked_at',
            ] as $column) {
                if (Schema::hasColumn('servers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

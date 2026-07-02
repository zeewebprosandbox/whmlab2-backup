<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            if (!Schema::hasColumn('servers', 'deployment_status')) {
                $table->string('deployment_status', 40)->default('manual')->after('health_checked_at');
            }

            if (!Schema::hasColumn('servers', 'deployment_version')) {
                $table->string('deployment_version', 120)->nullable()->after('deployment_status');
            }

            if (!Schema::hasColumn('servers', 'deployment_log')) {
                $table->longText('deployment_log')->nullable()->after('deployment_version');
            }

            if (!Schema::hasColumn('servers', 'last_deployed_at')) {
                $table->timestamp('last_deployed_at')->nullable()->after('deployment_log');
            }
        });
    }

    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            foreach (['deployment_status', 'deployment_version', 'deployment_log', 'last_deployed_at'] as $column) {
                if (Schema::hasColumn('servers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

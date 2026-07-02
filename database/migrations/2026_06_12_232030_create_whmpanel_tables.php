<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whm_panel_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('hostname')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('api_token', 80)->unique();
            $table->string('status')->default('online');
            $table->unsignedInteger('total_disk_mb')->default(102400);
            $table->unsignedInteger('used_disk_mb')->default(0);
            $table->unsignedInteger('total_bandwidth_mb')->default(1024000);
            $table->unsignedInteger('used_bandwidth_mb')->default(0);
            $table->unsignedTinyInteger('cpu_percent')->default(4);
            $table->unsignedTinyInteger('memory_percent')->default(18);
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();
        });

        Schema::create('whm_panel_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('node_id')->constrained('whm_panel_nodes')->cascadeOnDelete();
            $table->foreignId('hosting_id')->nullable()->constrained('hostings')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('username')->index();
            $table->string('email')->nullable();
            $table->string('package')->nullable();
            $table->string('primary_domain')->nullable();
            $table->string('status')->default('active')->index();
            $table->unsignedInteger('disk_limit_mb')->default(1024);
            $table->unsignedInteger('disk_used_mb')->default(96);
            $table->unsignedInteger('bandwidth_limit_mb')->default(10240);
            $table->unsignedInteger('bandwidth_used_mb')->default(480);
            $table->unsignedTinyInteger('cpu_percent')->default(2);
            $table->unsignedTinyInteger('memory_percent')->default(12);
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('terminated_at')->nullable();
            $table->timestamps();
            $table->unique(['node_id', 'username']);
        });

        Schema::create('whm_panel_websites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('whm_panel_accounts')->cascadeOnDelete();
            $table->string('domain')->index();
            $table->string('document_root')->nullable();
            $table->string('php_version')->default('8.3');
            $table->boolean('ssl_enabled')->default(true);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('whm_panel_dns_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('whm_panel_websites')->cascadeOnDelete();
            $table->string('name');
            $table->string('type', 10);
            $table->text('value');
            $table->unsignedInteger('ttl')->default(3600);
            $table->unsignedInteger('priority')->nullable();
            $table->timestamps();
        });

        Schema::create('whm_panel_usage_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('node_id')->constrained('whm_panel_nodes')->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained('whm_panel_accounts')->cascadeOnDelete();
            $table->unsignedInteger('disk_used_mb')->default(0);
            $table->unsignedInteger('bandwidth_used_mb')->default(0);
            $table->unsignedTinyInteger('cpu_percent')->default(0);
            $table->unsignedTinyInteger('memory_percent')->default(0);
            $table->timestamp('recorded_at')->useCurrent();
        });

        Schema::create('whm_panel_sso_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('whm_panel_accounts')->cascadeOnDelete();
            $table->string('token_hash');
            $table->timestamp('expires_at')->index();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whm_panel_sso_tokens');
        Schema::dropIfExists('whm_panel_usage_stats');
        Schema::dropIfExists('whm_panel_dns_records');
        Schema::dropIfExists('whm_panel_websites');
        Schema::dropIfExists('whm_panel_accounts');
        Schema::dropIfExists('whm_panel_nodes');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whm_panel_service_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->nullable()->constrained('whm_panel_accounts')->cascadeOnDelete();
            $table->foreignId('website_id')->nullable()->constrained('whm_panel_websites')->cascadeOnDelete();
            $table->string('module', 40)->index();
            $table->string('type', 60)->nullable()->index();
            $table->string('name')->index();
            $table->string('status', 40)->default('active')->index();
            $table->json('config')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whm_panel_service_items');
    }
};

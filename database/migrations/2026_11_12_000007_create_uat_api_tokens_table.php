<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config()->string('uat.table_prefix').'api_tokens', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('token', 64)->unique();
            $table->json('abilities');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config()->string('uat.table_prefix').'api_tokens');
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config()->string('uat.table_prefix').'scenarios', function (Blueprint $table): void {
            $table->id();
            $table->string('status')->index();
            $table->string('start_url', 2048)->nullable();
            $table->timestamp('ready_for_retest_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config()->string('uat.table_prefix').'scenarios');
    }
};

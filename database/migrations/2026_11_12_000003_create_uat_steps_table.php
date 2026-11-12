<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $prefix = config()->string('uat.table_prefix');

        Schema::create($prefix.'steps', function (Blueprint $table) use ($prefix): void {
            $table->id();
            $table->foreignId('scenario_id')->constrained($prefix.'scenarios')->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->string('url', 2048)->nullable();
            $table->timestamps();

            $table->index(['scenario_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config()->string('uat.table_prefix').'steps');
    }
};

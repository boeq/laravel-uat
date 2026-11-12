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

        Schema::create($prefix.'scenario_translations', function (Blueprint $table) use ($prefix): void {
            $table->id();
            $table->foreignId('scenario_id')->constrained($prefix.'scenarios')->cascadeOnDelete();
            $table->string('locale');
            $table->string('title');
            $table->text('description');
            $table->text('preconditions')->nullable();
            $table->string('start_url', 2048)->nullable();
            $table->timestamps();

            $table->unique(['scenario_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config()->string('uat.table_prefix').'scenario_translations');
    }
};

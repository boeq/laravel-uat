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

        Schema::create($prefix.'step_translations', function (Blueprint $table) use ($prefix): void {
            $table->id();
            $table->foreignId('step_id')->constrained($prefix.'steps')->cascadeOnDelete();
            $table->string('locale');
            $table->string('title');
            $table->text('instruction');
            $table->text('expected_result');
            $table->string('link_label')->nullable();
            $table->string('url', 2048)->nullable();
            $table->timestamps();

            $table->unique(['step_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config()->string('uat.table_prefix').'step_translations');
    }
};

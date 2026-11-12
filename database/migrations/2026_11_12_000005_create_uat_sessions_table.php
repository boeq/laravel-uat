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

        Schema::create($prefix.'sessions', function (Blueprint $table) use ($prefix): void {
            $table->id();
            $table->foreignId('scenario_id')->constrained($prefix.'scenarios')->restrictOnDelete();
            $table->morphs('tester');
            $table->string('locale');
            $table->string('status')->index();
            $table->text('closing_comment')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config()->string('uat.table_prefix').'sessions');
    }
};

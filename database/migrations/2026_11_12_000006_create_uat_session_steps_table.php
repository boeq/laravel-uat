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

        Schema::create($prefix.'session_steps', function (Blueprint $table) use ($prefix): void {
            $table->id();
            $table->foreignId('session_id')->constrained($prefix.'sessions')->cascadeOnDelete();
            $table->foreignId('step_id')->nullable()->constrained($prefix.'steps')->nullOnDelete();
            $table->unsignedInteger('position');
            $table->string('title');
            $table->text('instruction');
            $table->text('expected_result');
            $table->string('link_label')->nullable();
            $table->string('url', 2048)->nullable();
            $table->string('verdict')->nullable();
            $table->text('feedback')->nullable();
            $table->string('feedback_status')->nullable()->index();
            $table->text('resolution_note')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->string('page_url', 2048)->nullable();
            $table->text('user_agent')->nullable();
            $table->unsignedInteger('viewport_width')->nullable();
            $table->unsignedInteger('viewport_height')->nullable();
            $table->string('app_locale')->nullable();
            $table->timestamp('judged_at')->nullable();
            $table->timestamps();

            $table->index(['session_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config()->string('uat.table_prefix').'session_steps');
    }
};

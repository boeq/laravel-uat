<?php

declare(strict_types=1);

use Boeq\Uat\Http\Controllers\Api\ConfigurationController;
use Boeq\Uat\Http\Controllers\Api\FeedbackController;
use Boeq\Uat\Http\Controllers\Api\ResolveFeedbackController;
use Boeq\Uat\Http\Controllers\Api\ScenarioController;
use Boeq\Uat\Http\Controllers\Api\ScenarioRetestController;
use Boeq\Uat\Http\Controllers\Api\ScenarioStatusController;
use Boeq\Uat\Http\Controllers\Api\ScenarioTranslationController;
use Boeq\Uat\Http\Controllers\Api\SessionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('uat.api.')->group(function (): void {
    Route::get('configuration', ConfigurationController::class)->name('configuration');

    Route::apiResource('scenarios', ScenarioController::class);
    Route::put('scenarios/{scenario}/translations/{locale}', ScenarioTranslationController::class)->name('scenarios.translations.update');
    Route::put('scenarios/{scenario}/status', ScenarioStatusController::class)->name('scenarios.status.update');
    Route::post('scenarios/{scenario}/retest', ScenarioRetestController::class)->name('scenarios.retest');

    Route::get('scenarios/{scenario}/sessions', [SessionController::class, 'index'])->name('scenarios.sessions.index');
    Route::get('sessions/{session}', [SessionController::class, 'show'])->name('sessions.show');

    Route::get('feedback', FeedbackController::class)->name('feedback.index');
    Route::post('feedback/{step}/resolve', ResolveFeedbackController::class)->name('feedback.resolve');
});

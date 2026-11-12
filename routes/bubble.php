<?php

declare(strict_types=1);

use Boeq\Uat\Http\Controllers\Bubble\AbandonSessionController;
use Boeq\Uat\Http\Controllers\Bubble\AcceptSessionController;
use Boeq\Uat\Http\Controllers\Bubble\BubbleController;
use Boeq\Uat\Http\Controllers\Bubble\CompleteSessionController;
use Boeq\Uat\Http\Controllers\Bubble\ScenarioController;
use Boeq\Uat\Http\Controllers\Bubble\SessionController;
use Boeq\Uat\Http\Controllers\Bubble\SessionStepController;
use Illuminate\Support\Facades\Route;

Route::name('uat.')->group(function (): void {
    Route::get('/', BubbleController::class)->name('bubble.show');

    Route::get('scenarios', ScenarioController::class)->name('scenarios.index');
    Route::post('scenarios/{scenario}/session', [SessionController::class, 'store'])->name('sessions.store')->can('test', 'scenario');

    Route::get('sessions/{session}', [SessionController::class, 'show'])->name('sessions.show')->can('update', 'session');
    Route::put('sessions/{session}/steps/{step}', SessionStepController::class)->name('sessions.steps.update')->can('update', 'session')->scopeBindings();
    Route::post('sessions/{session}/accept', AcceptSessionController::class)->name('sessions.accept')->can('update', 'session');
    Route::post('sessions/{session}/complete', CompleteSessionController::class)->name('sessions.complete')->can('complete', 'session');
    Route::post('sessions/{session}/abandon', AbandonSessionController::class)->name('sessions.abandon')->can('update', 'session');
});

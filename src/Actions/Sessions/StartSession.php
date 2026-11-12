<?php

declare(strict_types=1);

namespace Boeq\Uat\Actions\Sessions;

use Boeq\Uat\Contracts\Sessions\StartsSession;
use Boeq\Uat\Contracts\TestsFeatures;
use Boeq\Uat\Enums\SessionStatus;
use Boeq\Uat\Events\SessionStartedEvent;
use Boeq\Uat\Models\Scenario;
use Boeq\Uat\Models\Session;
use Boeq\Uat\Models\Step;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StartSession implements StartsSession
{
    public function start(TestsFeatures&Model $tester, Scenario $scenario): Session
    {
        return DB::transaction(function () use ($tester, $scenario): Session {
            Session::query()
                ->inProgressBy($tester)
                ->update(['status' => SessionStatus::Paused]);

            $session = Session::query()
                ->whereMorphedTo('tester', $tester)
                ->whereBelongsTo($scenario)
                ->unfinished()
                ->first();

            if ($session instanceof Session) {
                $session->update(['status' => SessionStatus::InProgress]);

                return $session;
            }

            return $this->create($tester, $scenario);
        });
    }

    public static function bind(): void
    {
        app()->singleton(StartsSession::class, static::class);
    }

    protected function create(TestsFeatures&Model $tester, Scenario $scenario): Session
    {
        $scenario->loadMissing(['translations', 'steps.translations']);

        $session = new Session([
            'locale' => $scenario->translation(app()->getLocale())->locale,
            'status' => SessionStatus::InProgress,
        ]);
        $session->scenario()->associate($scenario);
        $session->tester()->associate($tester);
        $session->save();

        $session->steps()->createMany(
            $scenario->steps->map(fn (Step $step): array => $this->snapshot($step, $session->locale)),
        );

        SessionStartedEvent::dispatch($session);

        return $session;
    }

    protected function snapshot(Step $step, string $locale): array
    {
        $translation = $step->translation($locale);

        return [
            'step_id' => $step->id,
            'position' => $step->position,
            'title' => $translation->title,
            'instruction' => $translation->instruction,
            'expected_result' => $translation->expected_result,
            'link_label' => $translation->link_label,
            'url' => $translation->url ?? $step->url,
        ];
    }
}

@use('Boeq\Uat\Enums\SessionResult')

@if ($progress->needsRetest())
    <span class="font-semibold text-amber-600 dark:text-amber-400">{{ __('uat::bubble.ready_for_retest') }}</span>
@elseif ($progress->session === null)
    {{ __('uat::bubble.not_started') }}
@elseif ($progress->session->status->isUnfinished())
    {{ __('uat::bubble.in_progress', [
        'judged' => $progress->session->steps->whereNotNull('verdict')->count(),
        'steps' => $progress->session->steps->count(),
    ]) }}
@elseif ($progress->session->result() === SessionResult::AcceptedWithFeedback)
    {{ __('uat::bubble.completed_with_feedback') }}
@else
    {{ __('uat::bubble.completed') }}
@endif

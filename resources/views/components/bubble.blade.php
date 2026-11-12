@use('Boeq\Uat\Enums\Position')

<div
    id="uat-bubble"
    data-uat-url="{{ route('uat.bubble.show') }}"
    data-uat-error="{{ __('uat::bubble.error') }}"
    @class([
        'fixed m-4 flex gap-3',
        'bottom-0 right-0 flex-col items-end' => $position === Position::BottomRight,
        'bottom-0 left-0 flex-col items-start' => $position === Position::BottomLeft,
        'right-0 top-0 flex-col-reverse items-end' => $position === Position::TopRight,
        'left-0 top-0 flex-col-reverse items-start' => $position === Position::TopLeft,
        config()->string('uat.bubble.classes.container'),
    ])
>
    {!! $screen !!}
</div>

@once
    @include('uat::partials.script')
@endonce

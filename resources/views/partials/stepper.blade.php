@php $steps = ['Project', 'Contract', 'Progress']; @endphp
<div class="stepper">
    @foreach ($steps as $i => $label)
        @php $n = $i + 1; $state = $n < $current ? 'done' : ($n === $current ? 'active' : ''); @endphp
        <div class="step {{ $state }}">
            <span>@if ($state === 'done')<i class="bi bi-check-lg"></i>@else{{ $n }}@endif</span>
            <em>{{ $label }}</em>
        </div>
        @unless ($loop->last)<hr>@endunless
    @endforeach
</div>
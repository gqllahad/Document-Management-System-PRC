@php $notices = $deliveries->whereIn('state', ['overdue', 'soon'])->values(); @endphp

<div class="notice-panel" data-notices>
    <div class="notice-head">
        <div>
            <h3 class="stages-title"><i class="bi bi-bell"></i> Needs attention</h3>
            <p class="stages-sub">Due and coming due · within 7 days</p>
        </div>
        @if ($notices->isNotEmpty())
           <span class="deliv-badge is-overdue notice-count" @if ($notices->isEmpty()) hidden @endif>{{ $notices->count() }}</span>
        @endif
    </div>

    <ul class="notice-list">
        @foreach ($notices as $n)
            <li class="notice-li" data-division="{{ $n->division_id }}">
                <a href="#" class="notice-item">
                    <div>
                        <strong>{{ $n->name }}</strong>
                        <small>{{ $n->division }} · Due {{ $n->deadline->format('M j, Y') }}</small>
                    </div>
                    <span class="deliv-badge is-{{ $n->state }}">{{ $n->label }}</span>
                </a>
            </li>
        @endforeach
        <li class="notice-empty" @if ($notices->isNotEmpty()) hidden @endif><i class="bi bi-check-circle"></i> Nothing due. All on track.</li>
    </ul>
</div>
@php
    $full    = ($mode ?? 'dashboard') === 'full';
    $overdue = $deliveries->where('state', 'overdue')->count();
    $soon    = $deliveries->where('state', 'soon')->count();
    $attn    = $overdue + $soon;
    $icons   = ['delivered' => 'check2', 'overdue' => 'exclamation-triangle', 'soon' => 'clock', 'ongoing' => 'clock'];
    $rows    = $full ? $deliveries : $deliveries->whereIn('state', ['overdue', 'soon']);
@endphp

<div class="deliv-panel" data-delivery>
    <div class="deliv-head">
        <h3 class="stages-title">Track monitoring</h3>
        <p class="stages-sub">Notice-to-proceed timelines and actual delivery</p>
    </div>

   <div class="deliv-bar">
        @if ($full)
            <div class="deliv-tabs">
                <button type="button" class="is-active" data-tab="all">All scheduled <em>{{ $deliveries->count() }}</em></button>
                <button type="button" data-tab="overdue">Overdue <em>{{ $overdue }}</em></button>
                <button type="button" data-tab="soon">Due soon <em>{{ $soon }}</em></button>
            </div>
        @else
            <a href="{{ route('admin.tracking') }}" class="deliv-link">View all deliveries <i class="bi bi-arrow-right"></i></a>
        @endif
    </div>

    <div class="deliv-table">
        <div class="deliv-row deliv-th">
            <span>Project / Division</span>
            <span>Delivery timeline</span>
            <button type="button" class="deliv-sort">Deadline <i class="bi bi-arrow-down-up"></i></button>
            <span>Status</span>
            <span></span>
        </div>

        @foreach ($rows as $d)
            @php
                $late = $d->actual && $d->actual->gt($d->deadline);
                [$sub, $subClass] = match ($d->state) {
                    'overdue'   => ['Past deadline', 'is-overdue'],
                    'soon'      => ['Due this week', 'is-soon'],
                    'delivered' => [$late ? 'Delivered late' : 'Delivered on time', $late ? 'is-overdue' : 'is-ok'],
                    default     => ['Within timeline', 'is-ok'],
                };
            @endphp

            <div class="deliv-row" data-state="{{ $d->state }}" data-division="{{ $d->division_id }}" data-ts="{{ $d->deadline->timestamp }}">
                <div class="deliv-proj">
                    <!-- <span class="deliv-ico"><i class="bi bi-box-seam"></i></span> -->
                    <div>
                        <strong>{{ $d->name }}</strong>
                        <small>{{ $d->division }} <span>/</span> {{ $d->contract_no }}</small>
                    </div>
                </div>

                <div class="deliv-timeline is-{{ $d->state }}">
                    <div class="deliv-meta"><span>Time elapsed</span><b>{{ $d->pct }}%</b></div>
                    <div class="deliv-track">
                        <span class="deliv-fill" @style(['width: ' . $d->pct . '%'])></span>
                    </div>
                    <small>{{ $d->period }} calendar days</small>
                </div>

                <div class="deliv-dates">
                    <div class="deliv-main-date">{{ $d->deadline->format('M j, Y') }}</div>
                    <div class="deliv-sub-date">NTP - {{ $d->ntp->format('M j, Y') }}</div>
                </div>

                <div>
                    <span class="deliv-badge is-{{ $d->state }}">
                        <i class="bi bi-{{ $icons[$d->state] }}"></i> {{ $d->label }}
                    </span>
                    <small class="deliv-sub {{ $subClass }}">{{ $sub }}</small>
                </div>

                <!-- <a href="#" class="deliv-go"><i class="bi bi-arrow-right"></i></a> -->
            </div>
        @endforeach

        <div class="deliv-empty" @if ($rows->isNotEmpty()) hidden @endif>
            {{ $full ? 'No deliveries in this view' : 'Nothing needs attention' }}
        </div>
    </div>

    <div class="deliv-foot">
        <span>Showing <b class="deliv-shown">{{ $rows->count() }}</b> of <span class="deliv-total">{{ $deliveries->count() }}</span> deliveries</span>
        <span><i class="bi bi-calendar-event"></i> Due soon: within 14 calendar days</span>
    </div>

    <div class="deliv-note {{ $attn ? 'is-overdue' : 'is-delivered' }}">
        <i class="bi bi-{{ $attn ? 'exclamation-circle' : 'check2' }}"></i>
        {{ $attn
            ? $attn . ' ' . Str::plural('delivery', $attn) . ($attn === 1 ? ' needs' : ' need') . ' attention'
            : 'No overdue or upcoming deadlines need attention.' }}
    </div>
</div>
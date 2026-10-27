<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Procurement Units / Contracts</h5>
    </div>

    <div class="card-body">
        @forelse ($project->procurementUnits as $unit)
            <div class="border rounded p-3 mb-3">
                <h6>{{ $unit->unit_name }}</h6>

                <div class="row g-3">
                    <div class="col-md-4">
                        <strong>Contract Number</strong>
                        <div>{{ $unit->contract_number ?? 'Not recorded' }}</div>
                    </div>

                    <div class="col-md-4">
                        <strong>Contract Price</strong>
                        <div>
                            {{ $unit->contract_price !== null
                                ? '₱' . number_format((float) $unit->contract_price, 2)
                                : 'Not recorded' }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <strong>Winning Bidder</strong>
                        <div>{{ $unit->winning_bidder ?? 'Not recorded' }}</div>
                    </div>

                    <div class="col-md-4">
                        <strong>Contract Date</strong>
                        <div>
                            {{ $unit->contract_date?->format('M d, Y') ?? '—' }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <strong>NTP Date</strong>
                        <div>
                            {{ $unit->ntp_date?->format('M d, Y') ?? '—' }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <strong>Delivery Period</strong>
                        <div>
                            {{ $unit->delivery_period !== null
                                ? $unit->delivery_period . ' days'
                                : '—' }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <strong>Actual Delivery</strong>
                        <div>
                            {{ $unit->actual_date_of_delivery?->format('M d, Y') ?? '—' }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <strong>Status</strong>
                        <div>{{ $unit->status?->name ?? 'Not recorded' }}</div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted mb-0">
                No procurement units have been recorded for this project yet.
            </p>
        @endforelse
    </div>
</div>
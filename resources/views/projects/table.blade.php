<thead>
                <tr>
                    <th><button type="button" class="prj-sort">Project <i class="bi bi-arrow-down-up"></i></button></th>
                <!-- @if ($isAdmin) <th>Division</th> @endif -->
                    <th class="num">Budget & Contract</th>

                <!-- @if (auth()->user()->role === 'admin')
                    <th class="num">Contract P.</th>
                    <th class="num">Savings</th> 
                @endif -->
                    <th class="col-status"><button type="button" class="current-sort">Current Status <i class="bi bi-arrow-down-up"></i></button></th>
                <!-- @if (auth()->user()->role === 'admin')
                    <th>Contract No.</th>
                @else 
                    <th>Project Type</th>
                @endif -->
                    <th>Quarter</th>
                    <th>Update</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($projects as $project)
                    
                    @php
                        $code = 'PRJ-' . $project->created_at->year . '-' . str_pad($project->id, 3, '0', STR_PAD_LEFT);
                        $u = $project->latestUpdate;

                         $units = $project->procurementUnits;

                        $latestUnitUpdate = $units
                            ->flatMap(fn ($unit) => $unit->updates->map(fn ($update) => [
                                'update' => $update,
                                'unit_name' => $unit->unit_name,
                            ]))
                            ->sortByDesc(fn ($item) => $item['update']->created_at)
                            ->first();
                        
                    @endphp

                    @php
                        $filterUnits = $project->procurementUnits;

                        $filterQuarters = $filterUnits
                            ->map(fn ($unit) => $unit->quarter ?? $project->quarter)
                            ->filter()
                            ->unique()
                            ->values();

                        $filterYears = $filterUnits
                            ->map(fn ($unit) =>
                                $project->fiscal_year
                                ?? $unit->contract_date?->year
                                ?? $project->contract_date?->year
                            )
                            ->filter()
                            ->unique()
                            ->values();
                    @endphp

                    <tr data-contract="{{ $project->contract_number ? 'recorded' : 'pending' }}"
                        data-division="{{ $project->division_id }}"
                        data-type="{{ $project->source_funds }}"
                        data-quarter="{{ $filterQuarters->implode(',') }}"
                        data-fiscal-year="{{ $filterYears->implode(',') }}" data-updated="{{ data_get($latestUnitUpdate, 'update.status_date')?->format('Y-m-d') }}">

                        <td>
                            <div class="p-name">{{ $project->project_name }}</div>
                            <div class="p-meta">
                                <span>{{ $code }}</span>

                                @php
                                    $units = $project->procurementUnits;

                                    $totalUnits = $units->count();
                                    $contractedUnits = $units->filter(
                                        fn ($unit) => filled($unit->contract_number)
                                    )->count();
                                    $pendingUnits = $totalUnits - $contractedUnits;
                                @endphp

                                @if ($totalUnits > 1)
                                    <span class="pill pill-green">
                                        <i class="bi bi-layers"></i>
                                        {{ $totalUnits }} Units
                                    </span>

                                    <span class="pill pill-green">
                                        <i class="bi bi-file-earmark-check"></i>
                                        {{ $contractedUnits }} Contracted
                                    </span>

                                    @if ($pendingUnits > 0)
                                        <span class="pill pill-amber">
                                            <i class="bi bi-clock"></i>
                                            {{ $pendingUnits }} Pending
                                        </span>
                                    @endif
                                @elseif ($totalUnits === 1)
                                    @if ($contractedUnits === 1)
                                        <span class="pill pill-green">
                                            <i class="bi bi-file-earmark"></i>
                                            {{ $units->first()->contract_number }}
                                        </span>
                                    @else
                                        <span class="pill pill-amber">
                                            <i class="bi bi-clock"></i>
                                            No contract yet
                                        </span>
                                    @endif
                                @else
                                    <span class="pill pill-amber">
                                        <i class="bi bi-clock"></i>
                                        No units recorded
                                    </span>
                                @endif
                            </div>
                            
                            <div class="p-meta">
                                @if ($isAdmin)
                                    <span class="pill pill-div">{{ Str::upper($project->division->name) }}</span>
                                @endif
                                @if ($project->source_funds)
                                    <span>{{ $project->source_funds }}</span>
                                @endif
                            </div>
                            <!-- <small class="tb_small">PRJ-{{ $project->created_at->year }}-{{ str_pad($project->id, 3, '0', STR_PAD_LEFT) }}</small> -->
                        </td>
                    <!-- @if (auth()->user()->role === 'admin')
                            <td><span class="pill pill-div">{{ Str::upper($project->division->name) }}</span></td>
                    @endif -->

                        <td>
                            <div class="amt"><span>ABC</span><strong>₱{{ number_format($project->approved_budget, 2) }}</strong></div>
                            <div class="amt">
                                <span>Contract</span>
                                @if ($project->contract_price !== null)
                                    <strong>₱{{ number_format($project->contract_price, 2) }}</strong>
                                @else
                                    <em>Pending</em>
                                @endif
                            </div>
                            @if ($project->savings !== null)
                                <div class="amt savings"><span>Savings</span><strong>₱{{ number_format($project->savings, 2) }}</strong></div>
                            @endif
                        </td>
                        <!-- <td class="num">₱{{ number_format($project->approved_budget, 2) }}</td>
                        <td class="num {{ $project->contract_price === null ? 'text-muted' : '' }}">{{ $project->contract_price !== null ? '₱' . number_format($project->contract_price, 2) : 'Pending' }}</td>
                        <td class="num">{{ $project->savings !== null ? '₱' . number_format($project->savings, 2) : '—' }}</td> -->


                       <!-- <td class="update-cell col-status">
                            @php $u = $project->latestUpdate; @endphp
                            @if ($u)
                                @if ($u->created_at->gt(now()->subDays(2)))
                                    <span class="badge-new">New</span>
                                @endif
                                <span class="update-text" title="{{ $u->update }}">{{ $u->update }}</span>
                                <small class="update-date">{{ $u->status_date->format('m/d/Y') }}</small>
                            @else
                                <span class="text-muted">No updates yet</span>
                            @endif
                        </td> -->

                        <td class="update-cell col-status">
                            @if ($latestUnitUpdate)
                                @if ($latestUnitUpdate['update']->created_at->gt(now()->subDays(2)))
                                    <span class="badge-new">New  &nbsp;
                                        @if ($units->count() > 1)
                                            <small class="update-date">
                                                {{ $latestUnitUpdate['unit_name'] }}
                                            </small>
                                        @endif
                                    </span>
                                @endif

                                

                                <span
                                    class="update-text"
                                    title="{{ $latestUnitUpdate['update']->update }}"
                                >
                                    {{ $latestUnitUpdate['update']->update }}
                                </span>

                                <small class="update-date">
                                    {{ $latestUnitUpdate['update']->status_date?->format('m/d/Y') ?? '—' }}
                                </small>
                            @else
                                <span class="text-muted">No updates yet</span>
                            @endif
                        </td>


                    <!-- @if (auth()->user()->role === 'admin')
                        <td>{{ $project->contract_number? '' . $project->contract_number : '—' }}</td>
                        
                    @else
                        <td>{{ $project->contract_date?->format('M d, Y') ?? '—' }}</td>
                        <td>{{ $project->project_type? '' . $project->project_type : '—' }}</td>
                    @endif -->

                        <td>
                        @php
                            $units = $project->procurementUnits;

                            $quarters = $units
                                ->map(function ($unit) use ($project) {
                                    $quarter = $unit->quarter ?? $project->quarter;

                                    $year = $project->fiscal_year
                                        ?? $unit->contract_date?->year
                                        ?? $project->contract_date?->year;

                                    return collect([$quarter, $year])
                                        ->filter(fn ($value) => filled($value))
                                        ->implode(' · ');
                                })
                                ->filter()
                                ->unique()
                                ->values();

                            if ($quarters->isEmpty()) {
                                $fallback = collect([
                                    $project->quarter,
                                    $project->fiscal_year ?? $project->contract_date?->year,
                                ])->filter()->implode(' · ');

                                if ($fallback !== '') {
                                    $quarters->push($fallback);
                                }
                            }
                        @endphp

                        @forelse ($quarters as $quarter)
                            <div>{{ $quarter }}</div>
                        @empty
                            —
                        @endforelse
                    </td>

                        <td><span class="pill pill-role">{{ $project->status->name ?? '—' }}</span></td>
                       
                        <td class="actions">
                            <div class="actions-row">
                            <button type="button" class="btn-sm" data-view
                                data-id="{{ $project->id }}"
                                data-name="{{ $project->project_name }}"
                                data-code="PRJ-{{ $project->created_at->year }}-{{ str_pad($project->id, 3, '0', STR_PAD_LEFT) }}"
                                data-created="{{ $project->created_at->format('M d, Y') }}"
                                data-division="{{ $project->division->name ?? '' }}"
                                data-division-id="{{ $project->division_id }}"
                                data-quarter="{{ $project->quarter }}"
                                data-status="{{ $project->status->name ?? '' }}"
                                data-abc="{{ $project->approved_budget }}"
                                data-price="{{ $project->contract_price }}"
                                data-contract-number="{{ $project->contract_number }}"
                                data-contract-date="{{ $project->contract_date?->format('M d, Y') }}"
                                data-ntp="{{ $project->ntp_date?->format('M d, Y') }}"
                                data-delivered="{{ $project->actual_date_of_delivery?->format('M d, Y') }}"
                                data-delivery-period= "{{  $project->delivery_period  }}"
                                data-period="{{ collect([$project->quarter, $project->fiscal_year])->filter()->implode(' · ') }}"
                                data-update="{{ $project->latestUpdate->update ?? '' }}"
                                data-winning-bidder="{{ $project->winning_bidder }}"
                                data-status-date="{{ $project->latestUpdate?->status_date?->format('M d, Y') }}"
                                data-fiscal-year="{{ $project->fiscal_year }}"
                                data-docs="{{ $project->documents->map(fn ($d) => [
                                            'id'   => $d->id,
                                            'name' => $d->file_name ?? basename($d->file_path ?? ''),
                                            'url' => $d->file_path ? url(Storage::url($d->file_path)) : null,
                                            'size' => $d->file_path && Storage::disk('public')->exists($d->file_path)
                                                ? Storage::disk('public')->size($d->file_path)
                                                : null,
                                        ])->toJson() }}"
                                        data-history="{{ $project->procurementUnits
                                        ->flatMap(fn ($unit) => $unit->updates->map(fn ($h) => [
                                            '_sort'  => $h->created_at?->timestamp ?? 0,
                                            'unit'   => $unit->unit_name,
                                            'status' => $h->status->name ?? '—',
                                            'date'   => $h->status_date?->format('M d, Y'),
                                            'note'   => $h->update,
                                            'at'     => $h->created_at?->diffForHumans(),
                                            'created_at' => $h->created_at?->toISOString(),
                                        ]))
                                        ->sortByDesc('_sort')
                                        ->map(fn ($h) => collect($h)->except('_sort'))
                                        ->values()
                                        ->toJson() }}"
                                        data-units="{{ $project->procurementUnits->map(fn ($unit) => [
                                        'name' => $unit->unit_name,
                                        'contract_number' => $unit->contract_number,
                                        'contract_price' => $unit->contract_price,
                                        'contract_date' => $unit->contract_date?->format('M d, Y'),
                                        'winning_bidder' => $unit->winning_bidder,
                                        'ntp_date' => $unit->ntp_date?->format('M d, Y'),
                                        'delivery_period' => $unit->delivery_period,
                                        'actual_delivery' => $unit->actual_date_of_delivery?->format('M d, Y'),
                                        'status' => $unit->status?->name,
                                        'status_date' => $unit->status_date?->format('Y-m-d'),
                                        'items' => $unit->items->map(fn ($item) => [
                                            'name' => $item->item_name,
                                            'description' => $item->description,
                                            'quantity' => $item->quantity,
                                            'unit' => $item->unit,
                                            'unit_price' => $item->unit_price,
                                        ])->values(),
                                    ])->values()->toJson() }}">
                                 View
                            </button>
                        @if (!$isAdmin)
                            <button type="button" class="btn-sm" data-continue
                                data-action="{{ route('projects.update', $project) }}"
                                data-id="{{ $project->id }}"
                                data-name="{{ $project->project_name }}"
                                data-abc="{{ number_format($project->approved_budget, 2) }}"
                                data-code="PRJ-{{ $project->created_at->year }}-{{ str_pad($project->id, 3, '0', STR_PAD_LEFT) }}"
                                data-created="{{ $project->created_at->format('M d, Y') }}"
                                data-status="{{ $project->status_id }}"
                                data-ntp="{{ $project->ntp_date?->format('Y-m-d') }}"
                                data-delivered="{{ $project->actual_date_of_delivery?->format('Y-m-d') }}"
                                data-contract-number="{{ $project->contract_number }}"
                                data-price="{{ $project->contract_price }}"
                                data-source="{{ $project->source_funds }}"
                                data-contract-date="{{ $project->contract_date?->format('Y-m-d') }}"
                                data-winning-bidder="{{ $project->winning_bidder }}"

                                
                                data-units="{{ $project->procurementUnits->map(fn ($unit) => [
                                    'id' => $unit->id,
                                    'name' => $unit->unit_name,
                                    'contractNumber' => $unit->contract_number,
                                    'price' => $unit->contract_price,
                                    'contractDate' => $unit->contract_date?->format('Y-m-d'),
                                    'winningBidder' => $unit->winning_bidder,
                                    'status' => $unit->status->name ?? '—',
                                    'status_date' => $unit->status_date?->format('Y-m-d'),
                                ])->toJson() }}">
                                <i class="bi bi-pencil-square"></i> Update
                            </button>
                        
                                @php $scheduled = $project->procurementUnits->filter(fn ($u) => $u->contract_number); @endphp
                                 @if ($scheduled->isNotEmpty())
                                    <div class="menu">
                                        <button type="button" class="btn-sm menu-toggle">
                                            <i class="bi bi-calendar-check"></i> Schedule
                                            @if ($scheduled->count() > 1)
                                                <span class="menu-count">{{ $scheduled->count() }}</span>
                                            @endif
                                            <i class="bi bi-chevron-down"></i>
                                        </button>

                                        <div class="menu-list" hidden>
                                            @foreach ($scheduled as $unit)
                                                <button type="button" class="menu-item" data-schedule
                                                    data-id="{{ $project->id }}"
                                                    data-unit-id="{{ $unit->id }}"
                                                    data-name="{{ $project->project_name }}"
                                                    data-code="{{ $code }}"
                                                    data-source="{{ $project->source_funds }}"
                                                    data-abc="{{ number_format($project->approved_budget, 2) }}"
                                                    data-contract-number="{{ $unit->contract_number }}"
                                                    data-contract-date="{{ $unit->contract_date?->format('Y-m-d') }}"
                                                    data-quarter="{{ $unit->quarter }}"
                                                    data-price="{{ $unit->contract_price }}"
                                                    data-ntp="{{ $unit->ntp_date?->format('Y-m-d') }}"
                                                    data-winning-bidder="{{ $unit->winning_bidder }}"
                                                    data-delivery-period="{{ $unit->delivery_period }}"
                                                    data-status-id="{{ $unit->status_id }}">
                                                    <strong>{{ $unit->unit_name }}</strong>
                                                    <small>{{ $unit->contract_number }}</small>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                        @endif
                            
                            <!-- <a href="{{ route('projects.show', $project) }}" class="btn-sm"><i class="bi bi-eye"></i> View</a> -->
                            </div>
                         </td>
                    </tr>
                @empty
                    <tr class="empty"><td colspan="10">No projects yet.</td></tr>
                @endforelse
                @if ($projects->isNotEmpty())
                    <tr class="empty no-results" hidden><td colspan="10">No matching projects.</td></tr>
                @endif
            </tbody>
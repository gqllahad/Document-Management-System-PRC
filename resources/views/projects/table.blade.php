<thead>
                <tr>
                    <th><button type="button" class="prj-sort">Project <i class="bi bi-arrow-down-up"></i></button></th>
                <!-- @if ($isAdmin) <th>Division</th> @endif -->
                    <th class="num">Budget & Contract</th>

                <!-- @if (auth()->user()->role === 'admin')
                    <th class="num">Contract P.</th>
                    <th class="num">Savings</th> 
                @endif -->
                    <th class="col-status">Current Status</th>
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
                    @endphp

                    <tr data-contract="{{ $project->contract_number ? 'recorded' : 'pending' }}" 
                    data-division="{{ $project->division_id }}"
                    data-type="{{ $project->source_funds }}" data-quarter="{{ $project->quarter }}">

                        <td>
                            <div class="p-name">{{ $project->project_name }}</div>
                            <div class="p-meta">
                                <span>{{ $code }}</span>
                                @if ($project->contract_number)
                                    <span class="pill pill-green"><i class="bi bi-file-earmark"></i> {{ $project->contract_number }}</span>
                                @else
                                    <span class="pill pill-amber"><i class="bi bi-clock"></i> No contract yet</span>
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


                       <td class="update-cell col-status">
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
                        </td>


                    <!-- @if (auth()->user()->role === 'admin')
                        <td>{{ $project->contract_number? '' . $project->contract_number : '—' }}</td>
                        
                    @else
                        <td>{{ $project->contract_date?->format('M d, Y') ?? '—' }}</td>
                        <td>{{ $project->project_type? '' . $project->project_type : '—' }}</td>
                    @endif -->

                        <td>{{ collect([$project->quarter, $project->fiscal_year ?? $project->contract_date?->year])->filter()->implode(' · ') ?: '—' }}</td>

                        <td><span class="pill pill-role">{{ $project->status->name ?? '—' }}</span></td>
                       
                        <td class="actions">
                            <button type="button" class="btn-sm" data-view
                                data-name="{{ $project->project_name }}"
                                data-code="PRJ-{{ $project->created_at->year }}-{{ str_pad($project->id, 3, '0', STR_PAD_LEFT) }}"
                                data-created="{{ $project->created_at->format('M d, Y') }}"
                                data-division="{{ $project->division->name ?? '' }}"
                                data-status="{{ $project->status->name ?? '' }}"
                                data-abc="{{ $project->approved_budget }}"
                                data-price="{{ $project->contract_price }}"
                                data-contract-number="{{ $project->contract_number }}"
                                data-contract-date="{{ $project->contract_date?->format('M d, Y') }}"
                                data-ntp="{{ $project->ntp_date?->format('M d, Y') }}"
                                data-delivered="{{ $project->actual_date_of_delivery?->format('M d, Y') }}"
                                data-delivery-period= "{{  $project->delivery_period  }}"
                                data-period="{{ $project->quarter ? $project->quarter . ($project->fiscal_year ? ' · ' . $project->fiscal_year : '') : '' }}"
                                data-update="{{ $project->latestUpdate->update ?? '' }}"
                                data-winning-bidder="{{ $project->winning_bidder }}"
                                data-status-date="{{ $project->latestUpdate?->status_date?->format('M d, Y') }}"
                                data-docs="{{ $project->documents->map(fn ($d) => [
                                            'id'   => $d->id,
                                            'name' => $d->file_name ?? basename($d->file_path ?? ''),
                                            'url' => $d->file_path ? url(Storage::url($d->file_path)) : null,
                                            'size' => $d->file_path && Storage::disk('public')->exists($d->file_path)
                                                ? Storage::disk('public')->size($d->file_path)
                                                : null,
                                        ])->toJson() }}"
                                data-history="{{ $project->updates->sortByDesc('created_at')->map(fn ($l) => [
                                            'status' => $l->status->name ?? '—',
                                            'date'   => $l->status_date?->format('M d, Y'),
                                            'note'   => $l->update,
                                            'at'     => $l->created_at?->diffForHumans(),
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
                                data-quarter="{{ $project->quarter }}">
                                <i class="bi bi-pencil-square"></i> Update
                            </button>
                        @if (!$isAdmin && $project->contract_number)
                            <button type="button" class="btn-sm" data-schedule
                                data-id="{{ $project->id }}"
                                data-name="{{ $project->project_name }}"
                                data-code="PRJ-{{ $project->created_at->year }}-{{ str_pad($project->id, 3, '0', STR_PAD_LEFT) }}"
                                data-source="{{ $project->source_funds }}"
                                data-abc="{{ number_format($project->approved_budget, 2) }}"
                                data-contract-number="{{ $project->contract_number }}"
                                data-contract-date="{{ $project->contract_date?->format('Y-m-d') }}"
                                data-quarter="{{ $project->quarter }}"
                                data-price="{{ $project->contract_price }}"
                                data-ntp="{{ $project->ntp_date?->format('Y-m-d') }}"
                                data-winning-bidder="{{ $project->winning_bidder }}"
                                data-delivery-period="{{ $project->delivery_period }}"
                                data-status-id="{{ $project->status_id }}">
                                <i class="bi bi-calendar-check"></i> Schedule
                            </button>
                            @endif
                            
                            <!-- <a href="{{ route('projects.show', $project) }}" class="btn-sm"><i class="bi bi-eye"></i> View</a> -->
                        
                         @endif
                         </td>
                    </tr>
                @empty
                    <tr class="empty"><td colspan="10">No projects yet.</td></tr>
                @endforelse
                @if ($projects->isNotEmpty())
                    <tr class="empty no-results" hidden><td colspan="10">No matching projects.</td></tr>
                @endif
            </tbody>
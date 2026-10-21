<aside class="drawer {{ $stage1Err ? 'open' : '' }}" id="projectDrawer" aria-hidden="true">
    <form method="POST" action="{{ route('projects.store') }}" class="drawer-form" id="createForm">
        @csrf

        <div class="drawer-head">
            <div>
                <h3>Create project</h3>
                <p>Start a procurement project</p>
            </div>
            <button type="button" class="drawer-close" data-drawer-close aria-label="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="drawer-body">
            <span class="drawer-badge">{{ Str::upper(auth()->user()->division->name ?? '') }} workspace</span>
            @include('projects.partials.stepper', ['current' => 1])

            <div class="field">
                <label for="project_name">Project name<b class="required">*</b></label>
                <input type="text" id="project_name" name="project_name" value="{{ old('project_name') }}" placeholder="Enter project name" required>
                @error('project_name') <small class="err">{{ $message }}</small> @enderror
            </div>

            <div class="field">
                <label for="approved_budget">Approved Budget for the Contract (ABC) <b class="required">*</b></label>
                <div class="input-prefix">
                    <span>₱</span>
                    <input type="text" id="approved_budget" name="approved_budget" value="{{ old('approved_budget') }}" placeholder="0.00" inputmode="decimal" required>
                </div>
                @error('approved_budget') <small class="err">{{ $message }}</small> @enderror
            </div>


            <div class="field">
                    <label for="source_funds">Source of funds <b class="required">*</b></label>
                    <select id="source_funds" name="source_funds" required>
                        <option value="" disabled {{ old('source_funds') ? '' : 'selected' }}>Select source</option>
                        @foreach (\App\Models\Project::SOURCE_FUNDS as $v)
                            <option value="{{ $v }}" @selected(old('source_funds') === $v)>{{ $v }}</option>
                        @endforeach
                    </select>
                    @error('source_funds') <small class="err">{{ $message }}</small> @enderror
            </div> 
            <!-- project type -->
            <!-- <div class="row-2">
                

                
                 <div class="field">
                    <label for="project_type">Project type <b>*</b></label>
                    <select id="project_type" name="project_type" required>
                        <option value="" disabled {{ old('project_type') ? '' : 'selected' }}>Select type</option>
                        @foreach (\App\Models\Project::PROJECT_TYPES as $v)
                            <option value="{{ $v }}" @selected(old('project_type') === $v)>{{ $v }}</option>
                        @endforeach
                    </select>
                    @error('project_type') <small class="err">{{ $message }}</small> @enderror
                </div>

            </div> -->
            
            <div class="drawer-note">
                <i class="bi bi-info-circle"></i>
                Contract details, documents and status updates can be added when you return to this project.
            </div>
        </div>

        <div class="drawer-foot">
            <button type="button" class="btn-sm" data-drawer-close>Cancel</button>
            <button type="submit" class="btn-primary"><i class="bi bi-check-lg"></i> Create project</button>
        </div>
    </form>
</aside>
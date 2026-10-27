<aside class="drawer" id="continueDrawer" aria-hidden="true">
    <form method="POST" id="continueForm" enctype="multipart/form-data" class="drawer-form">
        @csrf @method('PUT')

        <div class="drawer-head">
            <div>
                <h3>Project continuation</h3>
                <p>Add contract details to your existing project</p>
            </div>
            <button type="button" class="drawer-close" data-drawer-close aria-label="Close"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="drawer-body">
            <span class="drawer-badge">{{ Str::upper(auth()->user()->division->name ?? '') }} workspace</span>
            @include('projects.partials.stepper', ['current' => 2])

            <div class="sec-title">Recorded project <small><i class="bi bi-lock"></i> Read-only</small></div>
            <div class="recorded">
                <small>PROJECT NAME</small>
                <strong id="cName"></strong>
                <span><span id="cCode"></span> · Created <span id="cCreated"></span></span>
                <div class="recorded-abc">
                    <div><small>Approved budget (ABC)</small><small>Approved Budget for the Contract</small></div>
                    <b>₱<span id="cAbc"></span></b>
                </div>
            </div>

            <hr>

             <div class="field">
                <label for="cUnit">Procurement unit <b class="required">*</b></label>
                <select id="cUnit" name="procurement_unit_id" required>
                    <option value="">Select procurement unit</option>
                </select>
                <small class="muted" style="padding: 6px 0;">
                    Choose the contract or lot you want to update.
                </small>
            </div>

            <div class="sec-title">Contract details <small class="tag-warn" id="contractTag">To be completed</small>
                <button type="button" class="link-toggle" id="unlockBtn" hidden>Edit</button>
            </div>

            <div class="field">
                <label for="cContract">Contract number </label>
                <input type="text" id="cContract" name="contract_number" placeholder="Enter contract number..">
                <small class="err" id="contractErr" hidden></small>
            </div>

            <div class="field">
                <label for="contract_price">Contract price </label>
                <div class="input-prefix">
                    <span>₱</span>
                    <input type="text" id="contract_price" name="contract_price" placeholder="0.00" inputmode="decimal">
                </div>
                <small id="abcWarning" class="err" hidden></small>
                @error('contract_price') <small class="err">{{ $message }}</small> @enderror
            </div>

            <div class="field">
                <label for="winning_bidder">Winning bidder </label>
                <input type="text" id="winning_bidder" name="winning_bidder" placeholder="Enter winning bidder.." >
            </div>

            <div class="field">
                <label for="contract_date">Contract date </label>
                <input type="date" id="contract_date" name="contract_date" >
            </div>

            <hr>

            <div class="sec-title">Progress</div>
            <p class="muted">Keep the project status current for your team.</p>

            <div class="field">
                <label for="status_id">Update status <b class="required">*</b></label>
                <select id="status_id" name="status_id" required>
                    @foreach ($statuses as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- <div class="row-2">
                <div class="field">
                    <label for="delivery_period">Delivery period (Days)</label>
                    <input type="number" id="delivery_period" name="delivery_period" value="{{ old('delivery_period', $project->delivery_period ?? '') }}" min="1" max="120"step="1" placeholder="Enter calendar days (1–120)">
                </div>

                <div class="field">
                    <label for="actual_delivery">Actual delivery</label>
                    <input type="date" id="actual_delivery" name="actual_delivery" value="{{ old('actual_date_of_delivery', $project->actual_date_of_delivery ?? '') }}">
                </div>
            </div> -->
            
            <div class="field">
                <label for="update">Current status <b class="required">*</b></label>
                <textarea id="update" name="update" rows="3" placeholder="e.g. Notice to proceed issued, delivery scheduled" required></textarea>
                <input type="date" id="update_date" name="status_date" required>
            </div>

            <hr>
            <div class="sec-title">Documents <small id="fileCount">0 files</small></div>
            <p class="muted">Notice of award, signed contract, notice to proceed, etc.</p>

            <label class="dropzone" for="documents">
                <i class="bi bi-upload"></i>
                <b>Click to upload files</b>
                <small>PDF, DOCX or images</small>
                <input type="file" id="documents" name="documents[]" multiple accept=".pdf,.doc,.docx,image/*" hidden>
            </label>
            <span class="drawer-note"><i class="bi bi-shield-check"></i>The original project name and approved budget remain unchanged.</span>
        </div>

        <div class="drawer-foot">
             <button type="button" class="btn-ghost" data-drawer-close>Cancel</button>
            <button type="submit" class="btn-primary btn-save">
                <i class="bi bi-check2-circle"></i> Save contract details
            </button>
            <button type="button" class="btn-ghost" id="toStage3" hidden>Continue to stage 3 <i class="bi bi-arrow-right"></i></button>
        </div>
    </form>
</aside>
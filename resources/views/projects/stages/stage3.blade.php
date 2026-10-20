<aside class="drawer" id="ntpDrawer" aria-hidden="true">
    <form method="POST" id="ntpForm" class="drawer-form">
        @csrf @method('PUT')

        <div class="drawer-head">
            <div>
                <h3>Notice to proceed</h3>
                <p>Set the NTP date and delivery period</p>
            </div>
            <button type="button" class="drawer-close" data-drawer-close aria-label="Close"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="drawer-body">
            <span class="drawer-badge">{{ Str::upper(auth()->user()->division->name ?? '') }} workspace</span>
            @include('partials.stepper', ['current' => 3])

            <div class="sec-title">Project & contract <small><i class="bi bi-lock"></i> Read-only</small>
                <button type="button" class="link-toggle" id="nEditBtn">Edit</button>
            </div>

            <div class="recorded">
                <small>PROJECT INFORMATION</small>
                <strong id="nName"></strong>
                <span><span id="nCode"></span> · <span id="nSource"></span></span>
                <div class="recorded-abc">
                    <div><small>Approved budget (ABC)</small></div>
                    <b>₱<span id="nAbc"></span></b>
                </div>
            </div>

            <div class="recorded">
                <small>CONTRACT INFORMATION</small>
                <strong id="nWinningBidder"></strong>
                
                <strong id="nContract"></strong>
                <span><span id="nContractDate"></span> · <span id="nQuarter"></span></span>
                <div class="recorded-abc">
                    <div><small>Contract price</small></div>
                    <b>₱<span id="nPrice"></span></b>
                </div>
            </div>

            <hr>

            <div class="sec-title">Delivery schedule <small class="tag-warn" id="ntpTag">To be completed</small>
                <button type="button" class="link-toggle" id="ntpUnlockBtn" hidden>Edit</button>
            </div>

            <div class="row-2">
                <div class="field">
                    <label for="ntp_date">NTP date <b>*</b></label>
                    <input type="date" id="ntp_date" name="ntp_date" required>
                    @error('ntp_date') <small class="err">{{ $message }}</small> @enderror
                </div>

                <div class="field">
                    <label for="delivery_period">Delivery period (days) <b>*</b></label>
                    <input type="number" id="delivery_period" name="delivery_period" min="1" max="120" step="1" placeholder="1–120" required>
                    @error('delivery_period') <small class="err">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="recorded">
                <small>EXPECTED DELIVERY</small>
                <strong id="expectedDate">—</strong>
            </div>

            <hr>

            <div class="sec-title">Progress</div>
            <p class="muted">Keep the project status current for your team.</p>

            <div class="field">
                <label for="status_id3">Update status</label>
                <select id="status_id3" name="status_id" required>
                    @foreach ($statuses as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="update3">Current status<b>*</b> </label>
                <textarea id="update3" name="update" rows="3" placeholder="e.g. Notice to proceed issued, delivery scheduled" required></textarea>
                <input type="date" id="update_date3" name="status_date" required>
            </div>

            <hr>
            <div class="sec-title">Documents <small id="fileCount3">0 files</small></div>
            <p class="muted">Notice to proceed, etc.</p>

            <label class="dropzone" for="documents3">
                <i class="bi bi-upload"></i>
                <b>Click to upload files</b>
                <small>PDF, DOCX or images</small>
                <input type="file" id="documents3" name="documents[]" multiple accept=".pdf,.doc,.docx,image/*" hidden>
            </label>
            <span class="drawer-note"><i class="bi bi-shield-check"></i>Contract details remain unchanged.</span>
        </div>

        <div class="drawer-foot">
            <button type="button" class="btn-ghost" data-drawer-close>Cancel</button>
            <button type="submit" class="btn-primary btn-save">
                <i class="bi bi-check2-circle"></i> Save schedule
            </button>
        </div>
    </form>
</aside>
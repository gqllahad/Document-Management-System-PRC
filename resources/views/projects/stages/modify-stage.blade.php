<aside class="drawer" id="modifyDrawer" aria-hidden="true">
    <form method="POST" id="modifyForm" class="drawer-form">
        @csrf @method('PUT')
        <input type="hidden" name="save_mode" id="saveMode" value="final">

        <div class="drawer-head">
            <div>
                <small class="eyebrow">PROJECT MODIFICATION</small>
                <h3>Modify: <span id="mTitle"></span></h3>
                <p><span id="mCode"></span> · Originally created <span id="mCreated"></span></p>
                <div class="chips">
                    <span class="chip" id="mDivChip"></span>
                    <span class="chip" id="mQtrChip"></span>
                </div>
            </div>
            <span class="status-pill"><span id="mStatus"></span> <em>(Editing)</em></span>
            <button type="button" class="drawer-close" data-drawer-close aria-label="Close"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="drawer-body">
            <div class="audit-note">
                <i class="bi bi-exclamation-triangle"></i>
                <div>
                    <b>Audit trail active</b>
                    <p>Changes to contract price, ABC, or timeline require documentation and trigger a procurement revision log.</p>
                </div>
            </div>

            <section class="card">
                <div class="sec-title">Project identification <small id="mId"></small></div>

                <div class="field">
                    <label for="m_name">Project name <b class="required">*</b></label>
                    <input type="text" id="m_name" name="name" required>
                    @error('name') <small class="err">{{ $message }}</small> @enderror
                </div>

                <div class="row-2">
                    <div class="field">
                        <label for="m_division">Requesting division / unit</label>
                        <select id="m_division" name="division_id" required>
                            @foreach ($divisions as $dv)
                                <option value="{{ $dv->id }}">{{ $dv->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="m_quarter">Implementation period</label>
                        <select id="m_quarter" name="quarter" required>
                            @foreach ($quarters as $q)
                                <option value="{{ $q }}">{{ $q }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </section>

            <section class="card card-fin">
                <div class="sec-title">Financial overview &amp; budgeting <small class="tag-info">Auto-calculated</small></div>

                <div class="row-2">
                    <div class="field">
                        <label for="m_abc">Approved budget <span class="mini-tag">ABC</span></label>
                        <div class="money"><span>₱</span><input type="number" id="m_abc" name="abc" min="0" step="0.01" required></div>
                    </div>
                    <div class="field">
                        <label for="m_price">Contract price / bid</label>
                        <div class="money"><span>₱</span><input type="number" id="m_price" name="contract_price" min="0" step="0.01" required></div>
                    </div>
                </div>

                <div class="util">
                    <div class="util-row"><span>Budget utilization</span><b><span id="mUtilPct">0</span>% allocated</b></div>
                    <div class="bar"><i id="mUtilBar"></i></div>
                </div>

                <div class="savings">
                    <span>Estimated savings</span>
                    <div><b id="mSavings">₱0.00</b> <small id="mSavingsPct"></small></div>
                </div>
            </section>

            <section class="card">
                <div class="sec-title">Contract &amp; supplier info <small class="tag-ok">Recorded</small></div>

                <div class="bidder">
                    <i class="bi bi-stars"></i>
                    <div>
                        <small>WINNING BIDDER</small>
                        <strong id="mBidder"></strong>
                        <input type="text" id="m_bidder" name="winning_bidder" hidden>
                    </div>
                    <button type="button" class="link-toggle" id="mBidderBtn">Change</button>
                </div>
            </section>
        </div>

        <div class="drawer-foot">
            <button type="button" class="btn-ghost" data-drawer-close>Cancel</button>
            <button type="submit" class="btn-ghost" id="mDraftBtn"><i class="bi bi-save"></i> Save draft</button>
            <button type="submit" class="btn-primary btn-save"><i class="bi bi-check2"></i> Review &amp; save changes</button>
        </div>
    </form>
</aside>
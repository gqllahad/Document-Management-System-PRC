<aside class="drawer" id="viewDrawer" aria-hidden="true">
    <div class="drawer-form">
        <div class="drawer-head">
            <div>
                <small class="eyebrow">Project overview</small>
                <h3 id="vName"></h3>
                <p><span id="vCode"></span> · Created <span id="vCreated"></span></p>
            </div>
            <button type="button" class="drawer-close" data-drawer-close aria-label="Close"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="drawer-body">
            <div class="v-top">
                <span class="drawer-badge" id="vDivision"></span>
                <span class="v-pill" id="vStatusPill"><i class="bi bi-circle"></i> <span id="vStatus"></span></span>
            </div>

            <div class="v-fin" id="vFin">
                <small>Financial overview</small>
                <span class="v-lbl">Contract price</span>
                <strong id="vPrice"></strong>
                <div class="v-abc"><span>Approved budget <em>ABC</em></span><b id="vAbc"></b></div>
                <div class="v-bar"><i id="vBar"></i></div>
                <div class="v-diff" id="vDiff"></div>
            </div>

           <div class="v-sec"><i class="bi bi-file-earmark-text"></i> Contract details <small class="v-rec" id="vRecTag"><i class="bi bi-check2"></i> Recorded</small></div>

            <div class="v-bidder">
                <span class="v-ico"><i class="bi bi-trophy"></i></span>
                <div><small>Winning bidder</small><strong id="vWinningBidder"></strong></div>
            </div>

            <div class="v-contract">
                <small>Contract number</small>
                <strong id="vContractNumber"></strong>
            </div>

            <div class="row-2 v-meta">
                <div><small><i class="bi bi-calendar3"></i> Contract date</small><b id="vContractDate"></b></div>
                <div><small>Procurement period</small><b id="vPeriodQ"></b><span id="vPeriodY"></span></div>
            </div>

            <hr>

            <div id="vDelivery" hidden>
                <div class="sec-title"><span><i class="bi bi-check-circle-fill"></i> Project completion</span><small id="vDays"></small></div>

                <div class="v-remaining" id="vRemaining">
                    <i class="bi bi-clock"></i>
                    <div><b id="vRemainText"></b><small id="vDeadlineText"></small></div>
                </div>

                <div class="v-track"><i id="vTrackFill"></i><span class="v-pin"><i class="bi bi-geo-alt-fill"></i></span></div>
                <div class="v-track-lbl"><span>NTP start</span><span>Delivery deadline</span></div>

                <div class="row-2 v-meta">
                    <div><small><i class="bi bi-calendar3"></i> NTP start date</small><b id="vNtp"></b></div>
                    <div><small><i class="bi bi-geo-alt"></i> Delivery deadline</small><b id="vDeadline"></b></div>
                </div>
            </div>

            <div class="v-actual">
                <small><i class="bi bi-truck"></i> Actual delivery</small>
                <b id="vDelivered"></b>
            </div>

            <div class="v-sec"><i class="bi bi-arrow-repeat"></i> Progress</div>
            <div class="v-progress">
                <small>Current Update</small>
                <strong id="vStatus2"></strong>
                <span class="v-date"><i class="bi bi-calendar3"></i> <span id="vStatusDate"></span></span>
                <div class="v-note" id="vNote">
                    <!-- <div class="v-note-head">
                        <span><i class="bi bi-chat-left-text"></i> Latest status</span>
                        <time id="vNoteDate"></time>
                    </div> -->
                    <div class="sec-title">Status history <small id="vHistCount">0</small></div>
                    <ol class="timeline" id="vHistory"></ol>
                    <p class="muted" id="vHistEmpty" hidden>No updates recorded yet.</p>
                    <!-- <p id="vUpdate"></p> -->
                </div>
                <button type="button" class="link-toggle" id="vHistoryBtn" hidden></button>
                <ul class="timeline" id="vHistory" hidden></ul>
            </div>

            <div class="v-sec"><i class="bi bi-paperclip"></i> Documents <small class="v-count" id="vDocCount">0</small></div>
            <ul class="v-docs" id="vDocs"></ul>
            <div class="v-none" id="vNone" hidden><i class="bi bi-folder-x"></i> No documents uploaded</div>
        </div>

        <div class="drawer-foot">
            <button type="button" class="btn-sm v-remarks"><i class="bi bi-download"></i> Download</button>
            @if(auth()->user()->role !== 'admin')
                <button type="button" class="btn-primary btn-save" id="vUpdateBtn"><i class="bi bi-pencil"></i> Update project</button>
            @else
            <button type="button" class="btn-sm v-remarks"><i class="bi bi-pencil"></i> Add remarks</button>
            @endif
        </div>
    </div>
</aside>
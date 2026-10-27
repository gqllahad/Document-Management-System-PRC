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
                <div class="v-tags">
                    <span class="drawer-badge" id="vDivision"></span>
                    <span class="drawer-badge" id="vPeriod" hidden><i class="bi bi-calendar-event"></i> <span id="vPeriodText"></span></span>
                </div>
                <span class="v-pill" id="vStatusPill" data-tone="info">
                    <i class="v-dot"></i>
                    <span id="vStatus"></span>
                    <small id="vStatusSub" hidden></small>
                </span>
            </div>

            <div class="v-fin" id="vFin">
                <small>Financial overview</small>

                <div class="v-line"><span>Approved budget <em>ABC</em></span><b id="vAbc"></b></div>
                <div class="v-line" id="vPriceRow"><span id="vPriceLbl">Contract price</span><strong id="vPrice"></strong></div>

                <div class="v-bar"><i id="vBar"></i></div>

                <div class="v-save" id="vSave">
                    <span id="vSaveLbl">Savings</span>
                    <b id="vSaveAmt"></b>
                    <small id="vSavePct"></small>
                </div>
            </div>

            <div id="vSingle">
                <div class="v-sec"><i class="bi bi-file-earmark-text"></i> Contract details <small class="v-rec" id="vRecTag"><i class="bi bi-check2"></i> Recorded</small></div>

                    <div class="v-bidder" id="vBidderSection">
                        <span class="v-ico"><i class="bi bi-trophy"></i></span>
                        <div>
                            <small>Winning bidder</small>
                            <strong id="vWinningBidder"></strong>
                    </div>
                </div>

       
                <div class="v-contract" id="vContractSection">
                    <small>Contract number</small>
                    <strong id="vContractNumber"></strong>
                </div>

                <div class="row-2 v-meta" id="vContractMeta">
                    <div><small><i class="bi bi-calendar3"></i> Contract date</small><b id="vContractDate"></b></div>
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
        </div>


        <!-- TODO: units -->
        <div id="vUnitsWrap" hidden>
            <div class="v-sec">
                <i class="bi bi-box-seam"></i>
                Procurement Units
                <small class="v-count" id="vUnitCount">0</small>
            </div>

            <div id="vUnits">
                <p class="text-muted">No procurement units recorded.</p>
            </div>
        </div>

        <div class="v-sec"><i class="bi bi-arrow-repeat"></i> Progress</div>
            <div class="v-progress">
                <small>Current Update</small>
                <strong id="vStatus2"></strong>

                <span class="v-date">
                    <i class="bi bi-calendar3"></i>
                    <span id="vStatusDate"></span>
                </span>

                <!-- <p id="vUpdate"></p> -->

                <div class="v-note" id="vNote">
                    

                    <div class="sec-title">
                        Status history  <small id="vHistCount">0</small>
                    </div>
                    <br>

                    <ol class="timeline" id="vHistory"></ol>

                    <p class="muted" id="vHistEmpty" hidden>
                        No updates recorded yet.
                    </p>
                </div>
            </div>

            <div class="v-sec"><i class="bi bi-paperclip"></i> Documents <small class="v-count" id="vDocCount">0</small></div>
            <ul class="v-docs" id="vDocs"></ul>
            <div class="v-none" id="vNone" hidden><i class="bi bi-folder-x"></i> No documents uploaded</div>
        
    </div>
    

        <div class="drawer-foot">
            <button type="button" class="btn-sm v-remarks"><i class="bi bi-download"></i> Download</button>
            <button type="button" id="vRemarks" class="btn-sm v-remarks"><i class="bi bi-pencil"></i> Add remarks</button>
            @if(auth()->user()->role !== 'admin')
                <button type="button" class="btn-primary btn-save" id="vUpdateBtn"><i class="bi bi-pencil"></i> Update project</button>
            @endif
        </div>
    </div>
</aside>
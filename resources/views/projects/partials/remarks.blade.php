<aside class="drawer" id="remarksDrawer" aria-hidden="true">
    <div class="drawer-form">
        <div class="drawer-head">
            <div>
                <small class="eyebrow">Remarks</small>
                <h3 id="rName"></h3>
                <p><span id="rCode"></span> · Created <span id="rCreated"></span></p>
            </div>
            <button type="button" class="drawer-close" data-drawer-close aria-label="Close"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="drawer-body chat" id="rList"></div>

        @php $isAdmin = auth()->user()->role === 'admin'; @endphp

        <form class="drawer-foot chat-compose" id="rForm">
            <div class="cc-head">
                <span class="cc-av">{{ Str::upper(Str::substr(auth()->user()->name, 0, 2)) }}</span>
                <strong>Add remark as {{ $isAdmin ? 'administrator' : 'Fund Controller' }} to {{ $isAdmin ? 'Fund Controller' : 'administrator' }}</strong>
                <span class="cc-to"><i></i> Recipient: {{ $isAdmin ? 'Fund Controller' : 'administrator' }}</span>
            </div>

            <div class="cc-box">
                <textarea name="body" rows="3" maxlength="1000" required
                    placeholder="Add a comment regarding to concerns..."></textarea>
            </div>

            <div class="cc-foot">
                <small>Press Enter to send..</small>
                <div>
                    <button type="button" class="btn-sm" data-drawer-close>Cancel</button>
                    <button type="submit" class="btn-primary">Send remarks</button>
                </div>
            </div>
        </form>
    </div>
</aside>
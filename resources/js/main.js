// partials
import './toast.js';
import './partials/projectFilter.js';

import './stringToNumber.js';

// project
import './partials/review.js';

// user management
import './partials/manageUser.js';

// panels for dashboard
import './panels/stagePanel.js';
import './panels/deliveryPanel.js';
import './panels/budgetPanel.js';
import './panels/cards.js';
import './panels/attentionPanel.js';

const toggle = document.getElementById('sidebarToggle');
const app = document.querySelector('.app');

window.addEventListener('pageshow', (e) => {
    if (e.persisted) location.reload();
}); //reloads to prevent the back cahceh data after logout

// toggles
if (localStorage.getItem('sidebar') === 'collapsed') app.classList.add('collapsed');

toggle?.addEventListener('click', () => {
    app.classList.toggle('collapsed');
    localStorage.setItem('sidebar', app.classList.contains('collapsed') ? 'collapsed' : 'open');
});

// projecct
(() => {
    const backdrop = document.getElementById('projectDrawerBackdrop');
    const drawers = document.querySelectorAll('.drawer');   

    drawers.forEach(d => { if (!d.classList.contains('open')) d.inert = true; });
    if (document.querySelector('.drawer.open')) document.body.classList.add('no-scroll');

    const open = (el) => {
        if (!el ) return; //|| !backdrop

        el.inert = false; //inigo
        el.classList.add('open');
        backdrop.classList.add('open');
        el.setAttribute('aria-hidden', 'false');
        document.body.classList.add('no-scroll');
    };

    const closeAll = () => {
        document.activeElement?.blur(); // inigo

        drawers.forEach(d => {
            d.classList.remove('open');
            d.setAttribute('aria-hidden', 'true');
            d.inert = true; //inigo
        });

        if (backdrop) {
            backdrop.classList.remove('open');
        }
        document.body.classList.remove('no-scroll');
    };

    document.querySelectorAll('[data-drawer-open]').forEach(b => {
        b.onclick = () => {
            const d = document.getElementById(b.dataset.drawerOpen);

            if (!d) return;

            open(d);
            d.querySelector('input')?.focus();
        };
    });

    document.querySelectorAll('[data-drawer-close]').forEach(b => {
        b.onclick = closeAll;
    });

    if (backdrop) {
        backdrop.onclick = closeAll;
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeAll();
    });

    // Stage 2
    const form = document.getElementById('continueForm');
    const set = (id, v) => {
        const el = document.getElementById(id);
        if (el) el.textContent = v;
    };

    if (form) {
        let maxPrice = Infinity;
        const q = form.elements.quarter;
        const contractDate = form.elements.contract_date;

        document.querySelectorAll('[data-continue]').forEach(b => {
            b.onclick = () => {
                const d = b.dataset;

                form.reset();
                form.dataset.id = d.id;
                setContractErr('');
                // period();

                const toStage3 = document.getElementById('toStage3');
                const contractEls = ['contract_number', 'contract_price', 'contract_date', 'quarter', 'winning_bidder'].map(n => form.elements[n]);
                const progressEls = ['status_id', 'update', 'status_date'].map(n => form.elements[n]);
                const setReq = (contract, progress) => {
                    contractEls.forEach(el => el.required = contract);
                    progressEls.forEach(el => el.required = progress);
                };

                const unlock = document.getElementById('unlockBtn');
                const tag = document.getElementById('contractTag');
                const locks = [
                    ['contract_number', d.contractNumber],
                    ['contract_price', d.price ? Number(d.price).toLocaleString('en-PH', { minimumFractionDigits: 2 }) : ''],
                    ['contract_date', d.contractDate],
                    ['winning_bidder', d.winningBidder],
                ];

                const setTag = (text, ok) => {
                    tag.textContent = text;
                    tag.classList.toggle('tag-ok', ok);
                    tag.classList.toggle('tag-warn', !ok);
                };

                let editing = false;

                setTag(!!(d.contractNumber && d.price && d.contractDate && d.quarter && d.winningBidder));

                const lock = () => {
                editing = false;

                locks.forEach(([name, val]) => {
                    const el = form.elements[name];
                    el.value = val || '';
                    el.readOnly = !!val;
                    el.classList.toggle('locked', !!val);
                });

                q.value = d.quarter || '';
                q.dataset.touched = d.quarter ? '1' : '';
                q.classList.toggle('locked', !!d.quarter);
                q.tabIndex = d.quarter ? -1 : 0;

                const done = !!(d.contractNumber && d.price && d.contractDate && d.quarter && d.winningBidder);
                setTag(done ? 'Recorded' : 'To be completed', done);

                unlock.textContent = 'Edit';
                unlock.hidden = !(locks.some(([, v]) => v) || d.quarter);
            };

            const edit = () => {
                editing = true;

                locks.forEach(([name]) => {
                    const el = form.elements[name];
                    el.readOnly = false;
                    el.classList.remove('locked');
                });
                q.classList.remove('locked');
                q.tabIndex = 0;

                setTag('Editing', false);
                unlock.textContent = 'Cancel';
            };

            unlock.onclick = () => (editing ? lock() : edit());
             toStage3.onclick = () => openStage3(d, b);
            lock();

                form.action = d.action;
                set('cName', d.name);
                set('cCode', d.code);
                set('cCreated', d.created);
                set('cAbc', d.abc);

                maxPrice = parseFloat(d.abc.replace(/,/g, ''));

                if (form.status_id && d.status) {
                    form.status_id.value = d.status;
                }

                open(document.getElementById('continueDrawer'));
            };   
            });

        // contract num Check
        const cNum = document.getElementById('cContract');
        const cErr = document.getElementById('contractErr');
        let t;

        const setContractErr = msg => {
            cNum.setCustomValidity(msg);
            cErr.textContent = msg;
            cErr.hidden = !msg;
            cNum.classList.toggle('invalid', !!msg);
        };

        cNum.addEventListener('input', () => {
            clearTimeout(t);
            setContractErr('');

            const v = cNum.value.trim().replace(/^\.+/, '');
            if (!v) return;

            t = setTimeout(async () => {
                try {
                    const url = `/projects/check-contract?number=${encodeURIComponent(v)}&ignore=${form.dataset.id || ''}`;
                    const res = await fetch(url, { headers: { Accept: 'application/json' } });
                    const { taken } = await res.json();
                    if (cNum.value.trim().replace(/^\.+/, '') === v) {
                        setContractErr(taken ? 'This contract number is already taken.' : '');
                    }
                } catch {}
            }, 400);
        });

        const priceInput = form.querySelector('[name="contract_price"]');

            const checkPrice = () => {
                const price = parseFloat(priceInput.value.replace(/,/g, '')) || 0;
                const over = price > maxPrice;

                priceInput.setCustomValidity(over ? 'Contract price cannot exceed the ABC.' : '');
                priceInput.closest('.input-prefix')?.classList.toggle('invalid', over);
            };

            priceInput.addEventListener('input', checkPrice);

        form.onsubmit = e => {

            if (!form.checkValidity()) { // price > maxPrice
                e.preventDefault();
                toast('Contract price cannot exceed the ABC.', 'warning');
            }
        };

        q.onchange = () => (q.dataset.touched = '1');

        contractDate.onchange = () => {
            if (!contractDate.value || q.dataset.touched || q.classList.contains('locked')) return;
            q.value = ['1ST', '2ND', '3RD', '4TH'][Math.floor(new Date(contractDate.value).getMonth() / 3)];
        };
    }

    // Document file count
    const documentsInput = document.getElementById('documents');

    if (documentsInput) {
        documentsInput.onchange = e => {
            const n = e.target.files.length;
            set('fileCount', `${n} file${n === 1 ? '' : 's'}`);
        };
    }

    // stage 3 
    const s3 = document.getElementById('ntpForm');

    if (s3) {
        const ntp = s3.elements.ntp_date;
        const days = s3.elements.delivery_period;
        const out = document.getElementById('expectedDate');
        let cont = null;

        const expected = () => {
            if (!ntp.value || !days.value) return out.textContent = '—';
            const dt = new Date(ntp.value);
            dt.setDate(dt.getDate() + Number(days.value));
            out.textContent = dt.toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' });
        };

        [ntp, days].forEach(el => el.addEventListener('input', expected));


        // lock
        const unlock = document.getElementById('ntpUnlockBtn');
        const tag = document.getElementById('ntpTag');
        let saved = { ntp: '', days: '' };
        let editing = false;

        const setTag = (text, ok) => {
            tag.textContent = text;
            tag.classList.toggle('tag-ok', ok);
            tag.classList.toggle('tag-warn', !ok);
        };

        const lock = () => {
            editing = false;
            ntp.value = saved.ntp;
            days.value = saved.days;
            expected();

            const done = !!(saved.ntp && saved.days);
            [ntp, days].forEach(el => {
                el.readOnly = done;
                el.classList.toggle('locked', done);
            });

            setTag(done ? 'Recorded' : 'To be completed', done);
            unlock.textContent = 'Edit';
            unlock.hidden = !done;
        };

        const edit = () => {
            editing = true;
            [ntp, days].forEach(el => {
                el.readOnly = false;
                el.classList.remove('locked');
            });
            setTag('Editing', false);
            unlock.textContent = 'Cancel';
        };

        unlock.onclick = () => (editing ? lock() : edit());

        window.openStage3 = (d, btn) => {
            cont = btn || null;
            s3.action = `/projects/${d.id}/schedule`;

            set('nName', d.name);
            set('nCode', d.code);
            set('nSource', d.source);
            set('nAbc', d.abc);
            set('nContract', d.contractNumber);
            set('nContractDate', d.contractDate);
            set('nQuarter', d.quarter);
            set('nWinningBidder', d.winningBidder);
            set('nPrice', d.price ? Number(d.price).toLocaleString('en-PH', { minimumFractionDigits: 2 }) : '');

            s3.reset();
            s3.elements.status_id.value = d.statusId || d.status || '';
            set('fileCount3', '0 files');

            ntp.min = d.contractDate || '';
            saved = { ntp: d.ntp || '', days: d.deliveryPeriod || '' };
            lock();

            closeAll();
            open(document.getElementById('ntpDrawer'));
        };

        document.getElementById('nEditBtn').onclick = () => { closeAll(); cont?.click(); };

        document.querySelectorAll('[data-schedule]').forEach(b => {
            b.onclick = () => openStage3(b.dataset, b.closest('tr')?.querySelector('[data-continue]'));
        });

        document.getElementById('documents3').onchange = e => {
            const n = e.target.files.length;
            set('fileCount3', `${n} file${n === 1 ? '' : 's'}`);
        };
    }

    // view
    const show = (id, v) => {
        const el = document.getElementById(id);
        if (!el) return;

        el.textContent = v || '—';
        el.classList.toggle('empty', !v);
    };


    const peso = n => '₱' + Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2 });
    const kb = b => b == null ? '' : b < 1048576 ? Math.round(b / 1024) + ' KB' : (b / 1048576).toFixed(1) + ' MB';

    document.querySelectorAll('[data-view]').forEach(b => {
        b.onclick = () => {
            const d = b.dataset;
            const abc = parseFloat(d.abc) || 0;
            const price = d.price === '' ? null : parseFloat(d.price);

            show('vName', d.name);
            show('vCode', d.code);
            show('vCreated', d.created);
            show('vDivision', d.division);
            show('vStatus', d.status);
            show('vStatus2', d.status);
            show('vAbc', peso(abc));
            show('vPrice', price == null ? '' : peso(price));
            show('vContractNumber', d.contractNumber || 'Not recorded');
            show('vContractDate', d.contractDate);
            show('vWinningBidder', d.winningBidder || 'Not yet recorded');

            const [pq, py] = (d.period || '').split(' · ');
            const ord = { '1ST': '1st quarter', '2ND': '2nd quarter', '3RD': '3rd quarter', '4TH': '4th quarter', EPA: 'EPA' };
            show('vPeriodQ', ord[pq] || pq);
            show('vPeriodY', py || '');

            show('vDelivered', d.delivered || 'Not yet delivered');
            show('vNtp', d.ntp);

            const box = document.getElementById('vDelivery');
            const start = d.ntp ? new Date(d.ntp) : null;
            const days = parseInt(d.deliveryPeriod) || 0;
            box.hidden = !(start && days);

            if (!box.hidden) {
                const DAY = 864e5;
                const fmt = x => x.toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });
                const end = new Date(start);
                end.setDate(end.getDate() + days);

                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const delivered = d.delivered ? new Date(d.delivered) : null;
                const left = Math.round((end - today) / DAY);

                let text, state;
                if (delivered) {
                    const late = Math.round((delivered - end) / DAY);
                    state = late > 0 ? 'late' : 'ok';
                    text = late > 0 ? `Delivered ${late} day${late > 1 ? 's' : ''} late` : 'Delivered on time';
                } else if (left < 0) {
                    state = 'late';
                    text = `${-left} day${left < -1 ? 's' : ''} overdue`;
                } else {
                    state = left <= 7 ? 'warn' : 'ok';
                    text = `${left} day${left === 1 ? '' : 's'} remaining`;
                }

                const pct = Math.min(Math.max(((delivered || today) - start) / (end - start) * 100, 0), 100);

                set('vDays', `${days} calendar days`);
                set('vRemainText', text);
                set('vDeadlineText', `Deadline · ${fmt(end)}`);
                set('vDeadline', fmt(end));
                document.getElementById('vTrackFill').style.width = pct + '%';
                document.getElementById('vRemaining').dataset.state = state;
            }

            const done = !!(d.contractNumber && d.contractDate && d.period && d.winningBidder);
            const tag = document.getElementById('vRecTag');
            tag.classList.toggle('pending', !done);
            tag.lastChild.textContent = done ? ' Recorded' : 'Pending';
            show('vStatusDate', d.statusDate || 'Status date not recorded');
            // show('vUpdate', d.update);
            show('vUpdate', d.update || 'No update recorded yet');
            show('vNoteDate', d.statusDate || '');

            document.getElementById('vNote').classList.toggle('is-empty', !d.update);

            // documents
            const fin = document.getElementById('vFin');
            const diff = document.getElementById('vDiff');
            const over = price != null && price > abc;
            fin.classList.toggle('over', over);
            document.getElementById('vBar').style.width = (price && abc ? Math.min(price / abc * 100, 100) : 0) + '%';

            if (price == null || !abc) {
                diff.textContent = 'No contract price recorded';
            } else {
                const gap = Math.abs(abc - price);
                diff.innerHTML = `<i class="bi bi-graph-${over ? 'up' : 'down'}-arrow"></i> `;
                diff.append(`${peso(gap)} ${over ? 'over' : 'below'} budget · ${(gap / abc * 100).toFixed(1)}%`);
            }

            const docs = JSON.parse(d.docs || '[]');
            const ul = document.getElementById('vDocs');
            ul.innerHTML = '';

            // proto
            const syncDocs = () => {
                const n = ul.children.length;
                document.getElementById('vDocCount').textContent = n;
                document.getElementById('vNone').hidden = n > 0;
                ul.hidden = !n;
            };

            // docs.forEach(f => {
            //     const li = document.createElement('li');
            //     const a = document.createElement('a');
            //     a.href = f.url; a.target = '_blank'; a.rel = 'noopener';
            //     a.innerHTML = '<i class="bi bi-file-earmark-text"></i><span><b></b><small></small></span>';
            //     a.querySelector('b').textContent = f.name;
            //     a.querySelector('small').textContent = kb(f.size);
            //     li.append(a);
            //     ul.append(li);
            // });


            docs.forEach(f => {
                const li = document.createElement('li');

                const a = document.createElement('a');
                a.href = f.url;
                a.target = '_blank';
                a.rel = 'noopener';
                a.innerHTML = '<i class="bi bi-file-earmark-text"></i><span><b></b><small></small></span>';
                a.querySelector('b').textContent = f.name;
                a.querySelector('small').textContent = kb(f.size);

                li.append(a);

                const actions = document.createElement('div');
                actions.className = 'doc-actions';
                actions.style.display = 'inline';

                actions.innerHTML = `
                    <button type="button" class="doc-delete" title="Delete document">
                        <i class="bi bi-trash"></i>
                    </button>
                    <div class="doc-confirm">
                        <span>Delete this file?</span>
                        <button type="button" class="doc-cancel">Cancel</button>
                        <button type="button" class="doc-confirm-btn">Delete</button>
                    </div>`;

                actions.querySelector('.doc-delete').onclick = () => li.classList.add('confirming');
                actions.querySelector('.doc-cancel').onclick = () => li.classList.remove('confirming');

                actions.querySelector('.doc-confirm-btn').onclick = async () => {
                    const res = await fetch(`/documents/${f.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (res.ok){
                        li.remove();
                        syncDocs();
                        toast('Successfully deleted!', 'success');
                    }
                    else{
                        toast('Failed to delete document!', 'warning');
                    } 
                };
                
                li.append(actions);
                ul.append(li);
                syncDocs();
                
            });

            const cont = b.closest('tr')?.querySelector('[data-continue]');
            const upd = document.getElementById('vUpdateBtn');
            if(upd){
                upd.hidden = !cont;
                upd.onclick = () => { closeAll(); cont?.click(); };
            }
            open(document.getElementById('viewDrawer'));
        };
    });
})();

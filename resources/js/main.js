// partials
import './toast.js';
import './partials/projectFilter.js';

import './stringToNumber.js';

// project
import './partials/review.js';
// import './partials/remarks.js';

// user management
import './partials/manageUser.js';
import './partials/stage1.js';
import './partials/table.js';

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

                const toStage3 = document.getElementById('toStage3');
                const contractEls = ['contract_number', 'contract_price', 'contract_date', 'winning_bidder'].map(n => form.elements[n]);
                const progressEls = ['status_id', 'update', 'status_date'].map(n => form.elements[n]);
                const setReq = (contract, progress) => {
                    contractEls.forEach(el => el.required = contract);
                    progressEls.forEach(el => el.required = progress);
                };

            const unitSelect = document.getElementById('cUnit');
            const units = JSON.parse(d.units || '[]');

            const contractFields = [
                'contract_number',
                'contract_price',
                'contract_date',
                'winning_bidder'
            ];

            const unlock = document.getElementById('unlockBtn');
            const tag = document.getElementById('contractTag');

            let editing = false;
            let selectedUnit = null;

            unitSelect.innerHTML = '<option value="">Select procurement unit</option>';

            units.forEach(unit => {
                const option = document.createElement('option');
                option.value = unit.id;
                option.textContent = unit.name;
                unitSelect.appendChild(option);
            });

            const setTag = (text, ok) => {
                tag.textContent = text;
                tag.classList.toggle('tag-ok', ok);
                tag.classList.toggle('tag-warn', !ok);
            };

            const loadUnit = () => {
                selectedUnit = units.find(
                    unit => String(unit.id) === unitSelect.value
                );

                form.dataset.unitId = selectedUnit?.id || '';
                form.action = selectedUnit
                    ? `/procurement-units/${selectedUnit.id}`
                    : '';

                contractFields.forEach(name => {
                    const el = form.elements[name];
                    el.value = '';
                    el.readOnly = false;
                    el.classList.remove('locked');
                });

                if (!selectedUnit) {
                    setTag('Select a unit', false);
                    unlock.hidden = true;
                    return;
                }

                const values = {
                    contract_number: selectedUnit.contractNumber,
                    contract_price: selectedUnit.price,
                    contract_date: selectedUnit.contractDate,
                    winning_bidder: selectedUnit.winningBidder
                };

                contractFields.forEach(name => {
                    const el = form.elements[name];
                    const value = values[name] ?? '';

                    el.value = name === 'contract_price' && value !== ''
                        ? Number(value).toLocaleString('en-PH', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })
                        : value;

                    el.readOnly = !!value;
                    el.classList.toggle('locked', !!value);
                });

                if (selectedUnit.status) {
                    form.elements.status_id.value = selectedUnit.status;
                }

                editing = false;

               const done = contractFields.every(name => !!values[name]);
                setTag(done ? 'Recorded' : 'To be completed', done);

                unlock.hidden = !contractFields.some(name => !!values[name]);
                unlock.textContent = 'Edit';
            };

            unitSelect.onchange = loadUnit;

            const priceInput = form.querySelector('[name="contract_price"]');
            const abcWarning = document.getElementById('abcWarning');

            // const checkPrice = () => {
            //     const price = parseFloat(priceInput.value.replace(/,/g, '')) || 0;

            //     const otherUnitsTotal = units.reduce((total, unit) => {
            //         if (String(unit.id) === String(selectedUnit?.id)) {
            //             return total;
            //         }

            //         return total + (parseFloat(unit.price) || 0);
            //     }, 0);

            //     const remainingABC = maxPrice - otherUnitsTotal;
            //     const over = price > remainingABC;
            //     const zero = priceInput.value.trim() !== '' && price <= 0;

            //     abcWarning.textContent = over
            //     ? `Contract price cannot exceed the remaining ABC of ₱${remainingABC.toLocaleString('en-PH', {
            //         minimumFractionDigits: 2,
            //         maximumFractionDigits: 2
            //     })}.`
            //     : zero
            //         ? 'Contract price must be greater than ₱0.'
            //         : '';

            //     abcWarning.hidden = !(over || zero);

            //     priceInput.closest('.input-prefix')?.classList.toggle('invalid', over || zero);
            // };

            const checkPrice = () => {
                const rawPrice = priceInput.value.replace(/,/g, '').trim();
                const price = parseFloat(rawPrice);

                const otherUnitsTotal = units.reduce((total, unit) => {
                    if (String(unit.id) === String(selectedUnit?.id)) {
                        return total;
                    }

                    return total + (parseFloat(unit.price) || 0);
                }, 0);

                const remainingABC = Math.max(0, maxPrice - otherUnitsTotal);

                const over = rawPrice !== '' && Number.isFinite(price) && price > remainingABC;
                const zero = rawPrice !== '' && (!Number.isFinite(price) || price <= 0);

                abcWarning.textContent = over
                    ? `Contract price cannot exceed the remaining ABC of ₱${remainingABC.toLocaleString('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })}.`
                    : zero
                        ? 'Contract price must be greater than ₱0.'
                        : '';

                abcWarning.hidden = !(over || zero);
                priceInput.closest('.input-prefix')?.classList.toggle('invalid', over || zero);
            };

            priceInput.addEventListener('input', checkPrice);

            unitSelect.addEventListener('change', checkPrice);

            unlock.onclick = () => {
                if (!selectedUnit) return;

                if (editing) {
                    loadUnit();
                    return;
                }

                editing = true;

                contractFields.forEach(name => {
                    const el = form.elements[name];
                    el.readOnly = false;
                    el.classList.remove('locked');
                });

                setTag('Editing', false);
                unlock.textContent = 'Cancel';
            };

            // unitSelect.value = '';
            // loadUnit();
            const single = units.length === 1;
            unitSelect.value = single ? String(units[0].id) : '';
            unitSelect.closest('.field').style.display = single ? 'none' : '';
            loadUnit();

            toStage3.onclick = () => {
            if (!selectedUnit) {
                toast('Please select a procurement unit first.', 'warning');
                return;
            }

            openStage3({
                    id: d.id,
                    unitId: selectedUnit.id,
                    name: d.name,
                    code: d.code,
                    source: d.source,
                    abc: d.abc,
                    contractNumber: selectedUnit.contractNumber,
                    contractDate: selectedUnit.contractDate,
                    quarter: d.quarter,
                    winningBidder: selectedUnit.winningBidder,
                    price: selectedUnit.price,
                    statusId: selectedUnit.status,
                    ntp: selectedUnit.ntpDate,
                    deliveryPeriod: selectedUnit.deliveryPeriod
                }, b);
            };

            // lock();

            // form.elements.quarter.value = d.quarter || '';
            set('cName', d.name);
            set('cCode', d.code);
            set('cCreated', d.created);
            set('cAbc', d.abc);

            maxPrice = parseFloat(String(d.abc).replace(/,/g, ''));

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

            const v = cNum.value.trim().replace(/^\.+/, '');

            t = setTimeout(async () => {
                try {
                    const url = `/projects/check-contract?number=${encodeURIComponent(v)}&project=${form.dataset.id || ''}&ignore=${form.dataset.unitId || ''}`;
                    const res = await fetch(url, { headers: { Accept: 'application/json' } });
                    const { taken } = await res.json();
                    if (cNum.value.trim().replace(/^\.+/, '') === v) {
                        setContractErr(taken ? 'This contract number is already taken.' : '');
                    }
                } catch {}
            }, 400);
        });

        form.onsubmit = e => {

            if (!form.checkValidity()) { // price > maxPrice
                e.preventDefault();
                toast('Contract price cannot exceed the ABC.', 'warning');
            }
        };

        // q.onchange = () => (q.dataset.touched = '1');

        // contractDate.onchange = () => {
        //     if (!contractDate.value || q.dataset.touched || q.classList.contains('locked')) return;
        //     q.value = ['1ST', '2ND', '3RD', '4TH'][Math.floor(new Date(contractDate.value).getMonth() / 3)];
        // };
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
        const statusSel = s3.elements.status_id;
        const delBox = document.getElementById('deliveredBox');
        const delInput = document.getElementById('actual_date_of_delivery');

        const syncDelivered = () => {
            const isDelivered = /delivered/i.test(statusSel.options[statusSel.selectedIndex]?.text || '');
            delBox.hidden = !isDelivered;
            delInput.disabled = !isDelivered;
            delInput.required = isDelivered;
            if (!isDelivered) delInput.value = '';
            else delInput.min = ntp.value || '';
        };

        statusSel.addEventListener('change', syncDelivered);
        ntp.addEventListener('input', syncDelivered);

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
             s3.reset();

            cont = btn || null;

            const unitId = d.unitId || s3.dataset.unitId;

            if (!unitId) {
                toast('Please select a procurement unit first.', 'warning');
                return;
            }

            s3.dataset.unitId = unitId;
            s3.action = `/procurement-units/${unitId}/schedule`;

            set('nName', d.name);
            set('nCode', d.code);
            set('nSource', d.source);
            set('nAbc', d.abc);
            set('nContract', d.contractNumber);
            set('nContractDate', d.contractDate);
            set('nQuarter', d.quarter);
            set('nWinningBidder', d.winningBidder);
            set('nPrice', d.price ? Number(d.price).toLocaleString('en-PH', { minimumFractionDigits: 2 }) : '');

            s3.elements.status_id.value = d.statusId || d.status || '';

            ntp.min = d.contractDate || '';
            saved = {
                ntp: d.ntp || '',
                days: d.deliveryPeriod || ''
            };

            lock();

            closeAll();
            open(document.getElementById('ntpDrawer'));
        };

        document.querySelectorAll('[data-schedule]').forEach(b => {
            b.onclick = () => {
                const d = b.dataset;

                if (!d.unitId) {
                    toast('Procurement unit not found.', 'warning');
                    return;
                }

                openStage3(d, b);
            };
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

    const esc = s => String(s ?? '').replace(/[&<>"']/g, c =>
        ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    const fmtDate = x => x.toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });

    function deadlineInfo(u) {
        if (!u.ntp_date || !u.delivery_period) return null;
        const end = new Date(u.ntp_date);
        end.setDate(end.getDate() + u.delivery_period);

        if (u.actual_delivery) {
            const late = Math.round((new Date(u.actual_delivery) - end) / 864e5);
            return { end, state: late > 0 ? 'late' : 'ok', text: late > 0 ? `Delivered ${late}d late` : 'Delivered on time' };
        }
        const today = new Date(); today.setHours(0, 0, 0, 0);
        const left = Math.round((end - today) / 864e5);
        if (left < 0) return { end, state: 'late', text: `${-left}d overdue` };
        return { end, state: left <= 7 ? 'warn' : 'ok', text: `${left}d remaining` };
    }

    function renderUnits(units) {
        const wrap = document.getElementById('vUnits');
        wrap.innerHTML = units.map((u, i) => {
            const dl = deadlineInfo(u);
            const cell = (label, value) => `<div><small>${label}</small><b>${esc(value) || '—'}</b></div>`;

            return `
            <article class="u-card">
                <header class="u-head">
                    <span class="u-no">${i + 1}</span>
                    <h4>${esc(u.name || 'Procurement unit')}</h4>
                    ${u.status ? `<span class="pill pill-role">${esc(u.status)}</span>` : ''}
                </header>

                <div class="u-contract">
                    <div>
                        <small>Contract number</small>
                        <strong>${esc(u.contract_number) || 'Not recorded'}</strong>
                    </div>
                    <div class="u-price">
                        <small>Contract price</small>
                        <strong>${u.contract_price == null ? '—' : peso(u.contract_price)}</strong>
                    </div>
                </div>

                <div class="u-bidder">
                    <i class="bi bi-trophy"></i>
                    <span>${esc(u.winning_bidder) || 'Winning bidder not recorded'}</span>
                </div>

                <div class="u-grid">
                    ${cell('Contract date', u.contract_date)}
                    ${cell('NTP date', u.ntp_date)}
                    ${cell('Delivery period', u.delivery_period == null ? '' : u.delivery_period + ' days')}
                    ${cell('Actual delivery', u.actual_delivery)}
                </div>

                ${dl ? `
                <div class="u-deadline" data-state="${dl.state}">
                    <i class="bi bi-clock"></i>
                    <span>${dl.text}</span>
                    <small>Deadline · ${fmtDate(dl.end)}</small>
                </div>` : ''}

                ${u.items?.length ? `
                <details class="u-items">
                    <summary>${u.items.length} item${u.items.length > 1 ? 's' : ''}</summary>
                    <ul>${u.items.map(it => `
                        <li>
                            <span>${esc(it.name)}</span>
                            <small>${it.quantity ?? 1} ${esc(it.unit || '')}${it.unit_price == null ? '' : ' · ' + peso(it.unit_price)}</small>
                        </li>`).join('')}
                    </ul>
                </details>` : ''}
            </article>`;
        }).join('');
    }

    document.querySelectorAll('[data-view]').forEach(b => {
        b.onclick = () => {
            const d = b.dataset;
            const abc = parseFloat(d.abc) || 0;

            const units = JSON.parse(d.units || '[]');
            const unitsContainer = document.getElementById('vUnits');

            const one = units.length === 1 ? units[0] : null;

            const src = one ? {
                contractNumber: one.contract_number,
                contractDate:   one.contract_date,
                winningBidder:  one.winning_bidder,
                price:          one.contract_price,
                ntp:            one.ntp_date,
                deliveryPeriod: one.delivery_period,
                delivered:      one.actual_delivery,
            } : d;

            const price = src.price == null || src.price === '' ? null : parseFloat(src.price);

            const toneOf = s =>
                /deliver|complete/i.test(s) ? 'ok' :
                /cancel|fail/i.test(s)      ? 'bad' :
                /pending|ongoing|bidding|pre-?proc/i.test(s) ? 'warn' : 'info';

            const isDone = s => toneOf(s || '') === 'ok';

            let pillText, pillSub = '', pillTone;

            if (units.length > 1) {
                const done = units.filter(u => isDone(u.status)).length;
                pillText = `${units.length} units`;
                pillSub  = `${done} delivered`;
                pillTone = done === units.length ? 'ok' : done ? 'info' : toneOf(units[0].status);
            } else {
                pillText = one?.status || d.status || 'No status';
                pillTone = toneOf(pillText);
            }

            const pill = document.getElementById('vStatusPill');
            pill.dataset.tone = pillTone;
            pill.title = units.length > 1
                ? units.map(u => `${u.name}: ${u.status || '—'}`).join('\n')
                : pillText;

            document.getElementById('vStatusSub').hidden = !pillSub;

            const latestUnit = units.filter(unit => unit.status && unit.status_date).sort((a, b) => new Date(b.status_date) - new Date(a.status_date))[0];

            const currentStatus = latestUnit? `${latestUnit.name} — ${latestUnit.status}` : d.status;

            show('vName', d.name);
            show('vCode', d.code);
            show('vCreated', d.created);
            show('vDivision', d.division);
            set('vStatus', pillText);
            set('vStatusSub', pillSub);
            show('vStatus2', currentStatus);
            show('vAbc', peso(abc));
            show('vPrice', price == null ? '' : peso(price));
            show('vContractNumber', src.contractNumber || 'Not recorded');
            show('vContractDate', src.contractDate);
            show('vWinningBidder', src.winningBidder || 'Not yet recorded');

            const bidderSection = document.getElementById('vBidderSection');
            const bidder = document.getElementById('vWinningBidder');

            const unitCount = document.getElementById('vUnitCount');

            const multi = units.length > 1;

            // document.getElementById('vFin').classList.toggle('multi', multi);
            document.getElementById('vSingle').hidden = multi;
            document.getElementById('vUnitsWrap').hidden = !multi;
            document.getElementById('vUnitCount').textContent = units.length;

            if (multi) renderUnits(units);

            const [pq, py] = (d.period || '').split(' · ');
            const ord = { '1ST': '1st quarter', '2ND': '2nd quarter', '3RD': '3rd quarter', '4TH': '4th quarter', EPA: 'EPA' };
            const period = [ord[pq] || pq, py].filter(Boolean).join(' · ');

            document.getElementById('vPeriod').hidden = !period;
            set('vPeriodText', period);

            show('vDelivered', src.delivered || 'Not yet delivered');
            show('vNtp', src.ntp);

            const box = document.getElementById('vDelivery');
            const start = src.ntp ? new Date(src.ntp) : null;
            const days = parseInt(src.deliveryPeriod) || 0;

            const delivered = src.delivered ? new Date(src.delivered) : null;

            box.hidden = !(start && days);

            if (!box.hidden) {
                const DAY = 864e5;
                const fmt = x => x.toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });
                const end = new Date(start);
                end.setDate(end.getDate() + days);

                const today = new Date();
                today.setHours(0, 0, 0, 0);
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

            // const done = !!(d.contractNumber && d.contractDate && d.period && d.winningBidder);
            const done = !!(src.contractNumber && src.contractDate && src.winningBidder);
            const tag = document.getElementById('vRecTag');
            tag.classList.toggle('pending', !done);
            tag.lastChild.textContent = done ? ' Recorded' : 'Pending';
            show('vStatusDate', d.statusDate || 'Status date not recorded');
            // show('vUpdate', d.update);
            // show('vUpdate', d.update || 'No update recorded yet');
            // show('vNoteDate', d.statusDate || '');

            // document.getElementById('vNote').classList.toggle('is-empty', !d.update);

            // const hist = JSON.parse(d.history || '[]');
            // const tl = document.getElementById('vHistory');
            // tl.innerHTML = '';

            const hist = JSON.parse(d.history || '[]').sort((a, b) => {
                const dateA = new Date(a.created_at || a.at || 0).getTime();
                const dateB = new Date(b.created_at || b.at || 0).getTime();
                return dateB - dateA;
            });

            const tl = document.getElementById('vHistory');
            tl.innerHTML = '';

            const latest = hist[0];

            show(
                'vStatus2',
                latest ? `${latest.unit ? latest.unit + ' — ' : ''}${latest.status}` : 'No updates yet'
            );

            show('vStatusDate', latest?.date || 'Status date not recorded');
            show('vUpdate', latest?.note || 'No update recorded yet');

            hist.forEach((h, i) => {
                const li = document.createElement('li');
                li.classList.toggle('current', i === 0);

                li.innerHTML =
                    '<div class="tl-top"><b class="tl-status"></b><time></time></div><p></p><small></small>';

                li.querySelector('.tl-status').textContent =
                    `${h.unit ? h.unit + ' — ' : ''}${h.status}`;

                li.querySelector('time').textContent = h.date || '';
                li.querySelector('p').textContent = h.note || 'No remarks';
                li.querySelector('small').textContent = h.at || '';

                tl.append(li);
            });

            set('vHistCount', hist.length);
            tl.hidden = !hist.length;
            document.getElementById('vHistEmpty').hidden = hist.length > 0;

            // financial
            const fin = document.getElementById('vFin');
            const priced = units.filter(u => u.contract_price != null);
            const hasPrice = units.length ? priced.length > 0 : price != null;
            const total = units.length
                ? priced.reduce((s, u) => s + Number(u.contract_price), 0)
                : (price ?? 0);
            const allPriced = units.length ? priced.length === units.length : price != null;

            const gap = abc - total;
            const over = hasPrice && gap < 0;

            fin.classList.toggle('over', over);
            fin.classList.toggle('has-price', hasPrice);

            set('vPriceLbl', units.length > 1
                ? `Total contracted · ${priced.length}/${units.length} units`
                : 'Contract price');
            show('vPrice', hasPrice ? peso(total) : 'Not recorded');

            document.getElementById('vBar').style.width =
                (hasPrice && abc ? Math.min(total / abc * 100, 100) : 0) + '%';

            set('vSaveLbl', over ? 'Over budget' : allPriced ? 'Estimated savings' : 'Unallocated');
            set('vSaveAmt', hasPrice ? peso(Math.abs(gap)) : '—');
            set('vSavePct', hasPrice && abc ? `${(Math.abs(gap) / abc * 100).toFixed(1)}% of ABC` : '')


            // dovuments
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

            // update
            const cont = b.closest('tr')?.querySelector('[data-continue]');
            const upd = document.getElementById('vUpdateBtn');
            if(upd){
                upd.hidden = !cont;
                upd.onclick = () => { closeAll(); cont?.click(); };
            }

            // remakrs

            const rList = document.getElementById('rList');
            const rForm = document.getElementById('rForm');
            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            const labels = { admin: 'Administrator', fund_controller: 'Fund Controller', head: 'Head' };

            const bubble = r => {
                const el = document.createElement('div');
                el.className = `msg ${r.role === 'admin' ? 'out' : 'in'} role-${r.role}`;
                el.innerHTML = '<div class="msg-top"><span class="role-pill"></span><b class="msg-name"></b></div><p></p><time></time>';
                el.querySelector('.role-pill').textContent = labels[r.role] ?? r.role;
                el.querySelector('.msg-name').textContent = r.user;
                el.querySelector('p').textContent = r.body;
                el.querySelector('time').textContent = r.at;
                return el;
            };

            const scrollDown = () => rList.scrollTop = rList.scrollHeight;

            const loadRemarks = async id => {
                rList.innerHTML = '';
                const res = await fetch(`/projects/${id}/remarks`, { headers: { Accept: 'application/json' } });
                if (!res.ok) return;
                (await res.json()).forEach(r => rList.append(bubble(r)));
                scrollDown();
            };

            rForm.onsubmit = async e => {
                e.preventDefault();
                rForm.body.addEventListener('keydown', e => {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        rForm.requestSubmit();
                    }
                });

                const body = rForm.body.value.trim();
                if (!body) return;

                const res = await fetch(`/projects/${rForm.dataset.id}/remarks`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ body })
                });

                if (res.ok) {
                    rList.append(bubble(await res.json()));
                    rForm.reset();
                    scrollDown();
                } else {
                    toast('Failed to send remark!', 'warning');
                }
            };

            const rem = document.getElementById('vRemarks');
            
            if(rem){
                
                rem.onclick = () => {
                    closeAll();
                    show('rName', d.name);
                    show('rCode', d.code);
                    show('rCreated', d.created);
                    rForm.dataset.id = d.id;
                    loadRemarks(d.id);
                    open(document.getElementById('remarksDrawer'));
                };
            }



            // modify
            const vm = document.getElementById('vModify');
            if (vm) {
                vm.onclick = () => openModify({
                    projectId: d.id,
                    code: d.code,
                    name: d.name,
                    created: d.created,
                    divisionId: d.divisionId,
                    divisionName: d.division,
                    quarter: d.quarter,
                    abc: d.abc,
                    price: d.price,
                    winningBidder: d.winningBidder,
                    statusName: d.status
                });
            }

            open(document.getElementById('viewDrawer'));
        };
    });


    const mf = document.getElementById('modifyForm');

        if (mf) {
            const abc = mf.elements.abc;
            const price = mf.elements.contract_price;
            const bidderIn = mf.elements.winning_bidder;
            const bidderTxt = document.getElementById('mBidder');
            const bidderBtn = document.getElementById('mBidderBtn');

            const peso = n => '₱' + n.toLocaleString('en-PH', { minimumFractionDigits: 2 });

            const calc = () => {
                const a = parseFloat(abc.value) || 0;
                const p = parseFloat(price.value) || 0;
                const pct = a ? (p / a) * 100 : 0;
                const save = a - p;

                set('mUtilPct', pct.toFixed(1));
                document.getElementById('mUtilBar').style.width = Math.min(pct, 100) + '%';

                const sv = document.getElementById('mSavings');
                sv.textContent = peso(save);
                sv.classList.toggle('neg', save < 0);
                set('mSavingsPct', a ? `${((save / a) * 100).toFixed(1)}% OF ABC` : '');
            };

            [abc, price].forEach(el => el.addEventListener('input', calc));

            bidderBtn.onclick = () => {
                const editing = bidderIn.hidden;
                bidderIn.hidden = !editing;
                bidderTxt.hidden = editing;
                bidderBtn.textContent = editing ? 'Done' : 'Change';
                if (editing) bidderIn.focus();
                else bidderTxt.textContent = bidderIn.value;
            };

            document.getElementById('mDraftBtn').onclick = () => { mf.elements.save_mode.value = 'draft'; };
            mf.querySelector('.btn-save').onclick = () => { mf.elements.save_mode.value = 'final'; };

            window.openModify = (d, btn) => {
                mf.reset();

                if (!d.projectId) {
                    toast('Project not found.', 'warning');
                    return;
                }

                mf.action = `/projects/${d.projectId}`;

                set('mTitle', d.name);
                set('mCode', d.code);
                set('mId', `ID: ${d.code}`);
                set('mCreated', d.created);
                set('mStatus', d.statusName);
                set('mDivChip', d.divisionName);
                set('mQtrChip', d.quarter);
                set('mBidder', d.winningBidder);

                mf.elements.name.value = d.name || '';
                mf.elements.division_id.value = d.divisionId || '';
                mf.elements.quarter.value = d.quarter || '';
                abc.value = d.abc || '';
                price.value = d.price || '';
                bidderIn.value = d.winningBidder || '';
                bidderIn.hidden = true;
                bidderTxt.hidden = false;
                bidderBtn.textContent = 'Change';

                calc();
                closeAll();
                open(document.getElementById('modifyDrawer'));
            };
        };
})();

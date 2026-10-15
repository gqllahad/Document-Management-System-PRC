import './toast.js';
import './projectFilter.js';
import './stringToNumber.js';

const toggle = document.getElementById('sidebarToggle');
const app = document.querySelector('.app');

// user management
const ms_modal = document.getElementById('roleModal');
const ms_form = document.getElementById('roleForm');
const ms_save = document.getElementById('mSave');
const ms_warn = document.getElementById('mWarn');

let ms_current = {};

const ms_pick = (name) => ms_form.querySelector(`input[name="${name}"]:checked`)?.value;

window.addEventListener('pageshow', (e) => {
    if (e.persisted) location.reload();
}); //reloads to prevent the back cahceh data after logout

// toggles
if (localStorage.getItem('sidebar') === 'collapsed') app.classList.add('collapsed');

toggle?.addEventListener('click', () => {
    app.classList.toggle('collapsed');
    localStorage.setItem('sidebar', app.classList.contains('collapsed') ? 'collapsed' : 'open');
});

// manage users (searchss)
document.getElementById('userSearch')?.addEventListener('input', (e) => {
    const q = e.target.value.toLowerCase();
    document.querySelectorAll('#userTable tbody tr:not(.empty)').forEach((row) => {
        row.hidden = !row.textContent.toLowerCase().includes(q);
    });
});

document.querySelectorAll('.manage-user').forEach((btn) => {
    btn.addEventListener('click', () => {
        const { action, name, email, role, status } = btn.dataset;
        ms_current = { role, status };

        ms_form.action = action;
        document.getElementById('mName').textContent = name;
        document.getElementById('mEmail').textContent = email;
        document.getElementById('mAvatar').textContent = name.charAt(0).toUpperCase();

        ms_form.querySelectorAll('input[name="role"]').forEach((r) => (r.checked = r.value === role));
        ms_form.querySelectorAll('input[name="status"]').forEach((r) => (r.checked = r.value === status));

        ms_warn.hidden = status !== 'inactive';
        ms_save.disabled = true;
        ms_modal.showModal();
    });
});

ms_form?.addEventListener('change', (e) => {
    if (e.target.name === 'role' && ms_current.status === 'pending') {
        ms_form.querySelector('input[name="status"][value="active"]').checked = true;
    }

    const role = ms_pick('role');
    const status = ms_pick('status');

    ms_warn.hidden = status !== 'inactive';
    ms_save.disabled = !role || !status || (role === ms_current.role && status === ms_current.status);
});

ms_modal?.addEventListener('click', (e) => {
    if (e.target === ms_modal || e.target.closest('[data-close]')) ms_modal.close();
});

// projecct

(() => {
    const backdrop = document.getElementById('projectDrawerBackdrop');
    const drawers = document.querySelectorAll('.drawer');

    // onigo
   

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

        // const out = document.getElementById('periodOut');

        // const period = () => {
        //     if (!out) return;

        //     const v = form.contract_date.value;

        //     if (!v) {
        //         out.textContent = '—';
        //         return;
        //     }

        //     const dt = new Date(v);
        //     const y = dt.getFullYear();

        //     out.textContent = form.period_type.value === 'epa'
        //         ? `EPA · ${y}`
        //         : `Q${Math.ceil((dt.getMonth() + 1) / 3)} · ${y}`;
        // };

        document.querySelectorAll('[data-continue]').forEach(b => {
            b.onclick = () => {
                const d = b.dataset;

                form.reset();
                form.dataset.id = d.id;
                setContractErr('');
                // period();

                const unlock = document.getElementById('unlockBtn');
                const locks = [
                    ['contract_number', d.contractNumber],
                    ['contract_price', d.price ? Number(d.price).toLocaleString('en-PH', { minimumFractionDigits: 2 }) : ''],
                    ['contract_date', d.contractDate],
                ];

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

                // if (d.contractDate) form.querySelector(`[name="period_type"][value="${d.periodType}"]`).checked = true;
                // seg.classList.toggle('locked', !!d.contractDate);
                // period();

                const tag = document.getElementById('contractTag');
                const setTag = done => {
                    tag.textContent = done ? 'Recorded' : 'To be completed';
                    tag.classList.toggle('tag-ok', done);
                    tag.classList.toggle('tag-warn', !done);
                };

                setTag(!!(d.contractNumber && d.price && d.contractDate && d.quarter));

                unlock.hidden = !(locks.some(([, v]) => v) || d.quarter);
                unlock.onclick = () => {
                    locks.forEach(([name]) => {
                        const el = form.elements[name];
                        el.readOnly = false;
                        el.classList.remove('locked');
                    });
                    q.classList.remove('locked');
                    q.tabIndex = 0;
                    unlock.hidden = true;

                    tag.textContent = 'Editing';
                    tag.classList.remove('tag-ok');
                    tag.classList.add('tag-warn');
                };

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

        // form.querySelectorAll('[name="period_type"]').forEach(r => {
        //     r.onchange = period;
        // });
    }

    // Document file count
    const documentsInput = document.getElementById('documents');

    if (documentsInput) {
        documentsInput.onchange = e => {
            const n = e.target.files.length;
            set('fileCount', `${n} file${n === 1 ? '' : 's'}`);
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

            const [pq, py] = (d.period || '').split(' · ');
            const ord = { '1ST': '1st quarter', '2ND': '2nd quarter', '3RD': '3rd quarter', '4TH': '4th quarter', EPA: 'EPA' };
            show('vPeriodQ', ord[pq] || pq);
            show('vPeriodY', py || '');

            show('vDelivered', d.delivered || 'Not recorded');

            const done = !!(d.contractNumber && d.contractDate && d.period);
            const tag = document.getElementById('vRecTag');
            tag.classList.toggle('pending', !done);
            tag.lastChild.textContent = done ? ' Recorded' : ' Awaiting contract';
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
            document.getElementById('vDocCount').textContent = docs.length;

            // if (!docs.length) {
            //     const li = document.createElement('li');
            //     li.className = 'v-none';
            //     li.innerHTML = '<i class="bi bi-folder-x"></i> No documents uploaded';
            //     ul.append(li);
            // }
            document.getElementById('vNone').hidden = docs.length > 0;
            ul.hidden = !docs.length;

            docs.forEach(f => {
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.href = f.url; a.target = '_blank'; a.rel = 'noopener';
                a.innerHTML = '<i class="bi bi-file-earmark-text"></i><span><b></b><small></small></span>';
                a.querySelector('b').textContent = f.name;
                a.querySelector('small').textContent = kb(f.size);
                li.append(a);
                ul.append(li);
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

    // document.querySelectorAll('[data-view]').forEach(b => {
    //     b.onclick = () => {
    //         const d = b.dataset;

    //         show('vName', d.name);
    //         show('vCode', d.code);
    //         show('vCreated', d.created);
    //         show('vStatus', d.status);
    //         show('vContractNumber', d.contractNumber);
    //         show('vContractDate', d.contractDate);
    //         show('vAbc', d.abc);
    //         show('vContractPrice', d.contractPrice);
    //         show('vPeriod', d.period);
    //         show('vDelivered', d.delivered);
    //         show('vUpdate', d.update);
    //         show('vStatusDate', d.statusDate);
    //         show('vRemarks', d.remarks);

    //         open(document.getElementById('viewDrawer'));
    //     };
    // });
})();


// review (are u sure naba? )
(() => {
  const dlg  = document.getElementById('reviewModal');
  const list = document.getElementById('rvList');
  const warn = document.getElementById('rvWarn');
  const $ = id => document.getElementById(id);
  const num = v => parseFloat(String(v).replace(/[^\d.]/g, '')) || 0;
  const peso = n => '₱' + n.toLocaleString('en-PH', { minimumFractionDigits: 2 });
  let pending = null;

  const openReview = (form, rows, warning = '') => {
    list.innerHTML = '';
    rows.forEach(([label, value, wide]) => {
      const d = document.createElement('div');
      if (wide) d.className = 'wide';
      const dt = document.createElement('dt'); dt.textContent = label;
      const dd = document.createElement('dd'); dd.textContent = value || '—';
      d.append(dt, dd);
      list.append(d);
    });

    warn.hidden = !warning;
    warn.querySelector('span').textContent = warning;
    pending = form;
    dlg.showModal();
  };

  // Stage 1
  const create = $('createForm');
  create?.addEventListener('submit', e => {
    e.preventDefault();
    if (!create.reportValidity()) return;

    openReview(create, [
      ['Project name', create.project_name.value, true],
    ['Approved budget (ABC)', peso(num(create.approved_budget.value))],
    ['Project type', create.project_type.value],
    ['Source of funds', create.source_funds.value],
    ['Expense class', create.expenses_classes.value],
    ]);
  });

  // Stage 2
  const cont = $('continueForm');
  cont?.addEventListener('submit', e => {
    e.preventDefault();
    if (!cont.reportValidity()) return;

    const price = num($('contract_price').value);
    const abc   = num($('cAbc').textContent);
    const files = [...$('documents').files].map(f => f.name);
    const status = $('status_id');

    openReview(cont, [
      ['Project', $('cName').textContent, true],
      ['Contract number', $('cContract').value],
      ['Contract price', peso(price)],
      ['Contract date', $('contract_date').value],
    //   ['Period', $('periodOut').textContent],
      ['Status', status.options[status.selectedIndex].text],
      ['Status date', $('update_date').value],
      ['Current status', $('update').value, true],
      ['Documents', files.length ? files.join(', ') : 'No files attached', true],
    ], abc && price > abc ? `Contract price exceeds the ABC (${peso(abc)}).` : '');
  });

  dlg?.addEventListener('click', e => {
    if (e.target === dlg || e.target.closest('[data-review-back]')) dlg.close();
    if (e.target.closest('[data-review-confirm]')) {
      dlg.close();
      pending?.submit();
    }
  });
})();


// (() => {
//   const form = document.getElementById('continueForm');
//   const dlg  = document.getElementById('reviewModal');
//   const list = document.getElementById('rvList');
//   const warn = document.getElementById('rvWarn');
//   const $ = id => document.getElementById(id);
//   const num = v => parseFloat(String(v).replace(/[^\d.]/g, '')) || 0;
//   const peso = n => '₱' + n.toLocaleString('en-PH', { minimumFractionDigits: 2 });

//   const row = (label, value, wide) => {
//     const d = document.createElement('div');
//     if (wide) d.className = 'wide';
//     const dt = document.createElement('dt'); dt.textContent = label;
//     const dd = document.createElement('dd'); dd.textContent = value || '—';
//     d.append(dt, dd);
//     list.append(d);
//   };

//   form.addEventListener('submit', e => {
//     e.preventDefault();
//     if (!form.reportValidity()) return;

//     const price = num($('contract_price').value);
//     const abc   = num($('cAbc').textContent);
//     const files = [...$('documents').files].map(f => f.name);
//     const status = $('status_id');

//     list.innerHTML = '';
//     row('Project', $('cName').textContent, true);
//     row('Contract number', $('cContract').value);
//     row('Contract price', peso(price));
//     row('Contract date', $('contract_date').value);
//     row('Period', $('periodOut').textContent);
//     row('Status', status.options[status.selectedIndex].text);
//     row('Status date', $('update_date').value);
//     row('Current status', $('update').value, true);
//     row('Documents', files.length ? files.join(', ') : 'No files attached', true);

//     // warn.hidden = !(abc && price > abc);
//     // warn.querySelector('span').textContent = `Contract price exceeds the ABC (${peso(abc)}).`;

//     // dlg.showModal();
//   });

//   dlg.addEventListener('click', e => {
//     if (e.target === dlg || e.target.closest('[data-review-back]')) dlg.close();
//     if (e.target.closest('[data-review-confirm]')) form.submit();
//   });
// })();

// project search

// const search = document.getElementById('projectSearch');
// const buttons = document.querySelectorAll('.filter-buttons button');
// let filter = 'all';

// document.getElementById('projectSearch')?.addEventListener('input', (e) => {
//     const q = e.target.value.toLowerCase();
//     document.querySelectorAll('#projectTable tbody tr:not(.empty)').forEach((row) => {
//         row.hidden = !row.textContent.toLowerCase().includes(q);
//     });
// });




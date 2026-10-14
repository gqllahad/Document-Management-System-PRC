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
    const open = (el) => { el.classList.add('open'); backdrop.classList.add('open'); el.setAttribute('aria-hidden', 'false'); };
    const closeAll = () => { drawers.forEach(d => { d.classList.remove('open'); d.setAttribute('aria-hidden', 'true'); }); backdrop.classList.remove('open'); };

    document.querySelectorAll('[data-drawer-open]').forEach(b => b.onclick = () => {
        const d = document.getElementById(b.dataset.drawerOpen); open(d); d.querySelector('input')?.focus();
    });
    document.querySelectorAll('[data-drawer-close]').forEach(b => b.onclick = closeAll);
    backdrop.onclick = closeAll;
    document.addEventListener('keydown', e => e.key === 'Escape' && closeAll());

    // Stage 2
    const form = document.getElementById('continueForm');
    const set = (id, v) => document.getElementById(id).textContent = v;
    let maxPrice = Infinity;

    document.querySelectorAll('[data-continue]').forEach(b => b.onclick = () => {
        const d = b.dataset;
        form.reset();
        period();
        form.action = d.action;
        set('cName', d.name); set('cCode', d.code); set('cCreated', d.created); set('cAbc', d.abc);
        maxPrice = parseFloat(d.abc.replace(/,/g, ''));

        if (d.status) form.status_id.value = d.status;
        open(document.getElementById('continueDrawer'));
    });

    const out = document.getElementById('periodOut');
    const period = () => {
        const v = form.contract_date.value;
        if (!v) return out.textContent = '—';
        const dt = new Date(v), y = dt.getFullYear();
        out.textContent = form.period_type.value === 'epa' ? `EPA · ${y}` : `Q${Math.ceil((dt.getMonth() + 1) / 3)} · ${y}`;
    };

    form.onsubmit = (e) => {
        const price = parseFloat(form.contract_price.value.replace(/,/g, ''));
        if (price > maxPrice) {
            e.preventDefault();
            alert('Contract price cannot exceed the ABC.');
        }
    };

    form.contract_date.onchange = period;
    form.querySelectorAll('[name=period_type]').forEach(r => r.onchange = period);

    document.getElementById('documents').onchange = e => {
        const n = e.target.files.length; set('fileCount', `${n} file${n === 1 ? '' : 's'}`);
    };

})();


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




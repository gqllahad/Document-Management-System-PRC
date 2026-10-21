// user management
const ms_modal = document.getElementById('roleModal');
const ms_form = document.getElementById('roleForm');
const ms_save = document.getElementById('mSave');
const ms_warn = document.getElementById('mWarn');

let ms_current = {};

const ms_pick = (name) => ms_form.querySelector(`input[name="${name}"]:checked`)?.value;

// manage users (searchss)
document.getElementById('userSearch')?.addEventListener('input', (e) => {
    const q = e.target.value.toLowerCase();
    let visible = 0;

    document.querySelectorAll('#userTable tbody tr:not(.empty)').forEach((row) => {
        const show = row.textContent.toLowerCase().includes(q);
        row.hidden = !show;
        if (show) visible++;
    });

    document.querySelector('#userTable .empty').hidden = visible > 0;
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
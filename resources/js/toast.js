const icons = { success: 'bi-check-circle-fill', error: 'bi-x-circle-fill', warning: 'bi-exclamation-triangle-fill' };

window.toast = (msg, type = 'success', ms = 4000) => {
    const stack = document.getElementById('toastStack');
    if (!stack) return;

    const el = document.createElement('div');
    el.className = `toast ${type}`;
    el.style.setProperty('--t', ms + 'ms');
    el.setAttribute('role', type === 'error' ? 'alert' : 'status');
    el.innerHTML = `<i class="bi ${icons[type] ?? icons.success}"></i><p></p><button type="button" aria-label="Close">&times;</button>`;
    el.querySelector('p').textContent = msg;

    const close = () => {
        el.classList.add('out');
        el.addEventListener('animationend', () => el.remove(), { once: true });
    };

    el.querySelector('button').addEventListener('click', close);
    stack.appendChild(el);
    setTimeout(close, ms);
};

(window.__toasts ?? []).forEach((t, i) =>
    setTimeout(() => toast(t.msg, t.type, t.type === 'error' ? 6000 : 4000), i * 150)
);


// confirmation toast
window.confirmToast = (message, onConfirm) => {
    const stack = document.getElementById('toastStack');
    if (!stack) return;

    const el = document.createElement('div');
    el.className = 'toast warning confirm-toast';
    el.setAttribute('role', 'alert');

    const icon = document.createElement('i');
    icon.className = 'bi bi-exclamation-triangle-fill';

    const text = document.createElement('p');
    text.textContent = message;

    const actions = document.createElement('div');
    actions.className = 'toast-actions';

    const cancel = document.createElement('button');
    cancel.type = 'button';
    cancel.textContent = 'Cancel';
    cancel.className = 'toast-cancel';

    const confirm = document.createElement('button');
    confirm.type = 'button';
    confirm.textContent = 'Delete';
    confirm.className = 'toast-confirm';

    actions.append(cancel, confirm);
    el.append(icon, text, actions);
    stack.appendChild(el);

    const close = () => el.remove();

    cancel.addEventListener('click', close);

    confirm.addEventListener('click', () => {
        close();
        onConfirm();
    });
};

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

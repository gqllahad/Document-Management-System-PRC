document.addEventListener('click', e => {
    const toggle = e.target.closest('.menu-toggle');
    document.querySelectorAll('.menu-list:not([hidden])').forEach(m => {
        if (!toggle || m !== toggle.nextElementSibling) m.hidden = true;
    });
    if (!toggle) return;

    const menu = toggle.nextElementSibling;
    menu.hidden = !menu.hidden;
    if (menu.hidden) return;

    const r = toggle.getBoundingClientRect();
    const up = window.innerHeight - r.bottom < menu.offsetHeight + 12;
    menu.style.top = up ? `${r.top - menu.offsetHeight - 4}px` : `${r.bottom + 4}px`;
    menu.style.left = `${Math.max(8, r.right - menu.offsetWidth)}px`;
});

document.querySelector('.table-wrap')?.addEventListener('scroll', () =>
    document.querySelectorAll('.menu-list').forEach(m => m.hidden = true));
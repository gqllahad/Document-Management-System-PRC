(() => {
    const box = document.querySelector('.cards-container');
    const sel = document.getElementById('divisionFilter');
    if (!box) return;

    const data = JSON.parse(box.dataset.cards);
    const pad = n => String(n).padStart(2, '0');
    const fmt = n => n >= 1e6 ? '₱' + (n / 1e6).toFixed(2) + 'M'
                   : n >= 1e3 ? '₱' + (n / 1e3).toFixed(2) + 'K'
                   : '₱' + n.toFixed(2);
    const set = (k, v) => box.querySelector(`[data-stat="${k}"]`).textContent = v;

    const render = () => {
        const div = sel?.value ?? 'all';
        const rows = div === 'all' ? Object.values(data) : [data[div] ?? {}];
        const s = k => rows.reduce((a, r) => a + (r[k] ?? 0), 0);

        set('total', pad(s('total')));
        set('total2', s('total'));
        set('delivered', s('delivered'));
        set('ongoing', pad(s('total') - s('delivered')));
        set('abc', fmt(s('abc')));
        set('contract', fmt(s('contract')));
        set('contracted', s('contracted'));
        set('pending', s('total') - s('contracted'));
        set('savings', fmt(s('savings')));
    };

    sel?.addEventListener('change', render);
})();
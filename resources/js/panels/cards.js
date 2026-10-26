(() => {
    const box = document.querySelector('.cards-container');
    const sel = document.getElementById('divisionFilter');
    const yearSel = document.getElementById('fiscalFilter');
    if (!box) return;

    const data = JSON.parse(box.dataset.cards);
    const pad = n => String(n).padStart(2, '0');
    const fmt = n => n >= 1e6 ? '₱' + (n / 1e6).toFixed(2) + 'M'
                   : n >= 1e3 ? '₱' + (n / 1e3).toFixed(2) + 'K'
                   : '₱' + n.toFixed(2);
    const set = (k, v) => box.querySelector(`[data-stat="${k}"]`).textContent = v;

    const render = () => {
        const div  = sel?.value ?? 'all';
        const year = yearSel?.value ?? 'all';

        const rows = data.filter(r =>
            (div  === 'all' || String(r.d) === div) &&
            (year === 'all' || String(r.y) === year)
        );
        const s = k => rows.reduce((a, r) => a + r[k], 0);
        const total = rows.length;

        set('total', pad(total));
        set('total2', total);
        set('delivered', s('delivered'));
        set('ongoing', pad(total - s('delivered')));
        set('abc', fmt(s('abc')));
        set('contract', fmt(s('contract')));
        set('contracted', s('contracted'));
        set('pending', total - s('contracted'));
        set('savings', fmt(s('savings')));
    };

    sel?.addEventListener('change', render);
    yearSel?.addEventListener('change', render);
})();
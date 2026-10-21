document.querySelectorAll('[data-delivery]').forEach(panel => {
    const sel = document.getElementById('divisionFilter');
    const rows = [...panel.querySelectorAll('.deliv-row[data-state]')];
    const tabs = [...panel.querySelectorAll('.deliv-tabs button')];
    const empty = panel.querySelector('.deliv-empty');
    const shown = panel.querySelector('.deliv-shown');
    const total = panel.querySelector('.deliv-total');
    const note = panel.querySelector('.deliv-note');
    let tab = 'all';

    const render = () => {
        const div = sel?.value ?? 'all';
        const inDiv = rows.filter(r => div === 'all' || r.dataset.division === div);
        const count = s => inDiv.filter(r => s === 'all' || r.dataset.state === s).length;

        tabs.forEach(b => b.querySelector('em').textContent = count(b.dataset.tab));

        let visible = 0;
        rows.forEach(r => {
            const show = inDiv.includes(r) && (tab === 'all' || r.dataset.state === tab);
            r.hidden = !show;
            if (show) visible++;
        });

        empty.hidden = visible > 0;
        shown.textContent = visible;
        total.textContent = inDiv.length;

        const attn = count('overdue') + count('soon');
        note.className = 'deliv-note ' + (attn ? 'is-overdue' : 'is-delivered');
        note.innerHTML = attn
            ? `<i class="bi bi-exclamation-circle"></i> ${attn} ${attn === 1 ? 'delivery needs' : 'deliveries need'} attention`
            : '<i class="bi bi-check2"></i> No overdue or upcoming deadlines need attention.';
    };

    tabs.forEach(b => b.addEventListener('click', () => {
        tabs.forEach(t => t.classList.toggle('is-active', t === b));
        tab = b.dataset.tab;
        render();

    const sortBtn = panel.querySelector('.deliv-sort');
    let asc = true;

    sortBtn?.addEventListener('click', () => {
        rows.sort((a, b) => asc ? a.dataset.ts - b.dataset.ts : b.dataset.ts - a.dataset.ts)
            .forEach(r => empty.before(r));
        asc = !asc;
        });
    }));

    sel?.addEventListener('change', render);
});
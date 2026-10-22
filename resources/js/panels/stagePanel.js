document.querySelectorAll('[data-donut]').forEach(panel => {
    const num = panel.querySelector('.stages-num');
    const lbl = panel.querySelector('.stages-lbl');
    const pie = panel.querySelector('.stages-pie');
    if (!num || !lbl || !pie) return;

    const items = [...panel.querySelectorAll('.stages-list li')];
    const data = JSON.parse(panel.dataset.stages);
    const divisionSel = document.getElementById('divisionFilter');
    const yearSel = document.getElementById('fiscalFilter');
    const quarterSel = document.getElementById('quarterFilter');
    let total = 0;
    let active = null;

    const show = li => {
        num.textContent = li ? li.dataset.count : total;
        lbl.textContent = li ? li.dataset.label : 'All projects';
    };

    const render = () => {
        const div = divisionSel?.value || 'all';
        const year = yearSel?.value || 'all';
        const quarter = quarterSel?.value || 'all';
        const rows = data.filter(r =>
                                    (div === 'all'     || String(r.d) === div) &&
                                    (year === 'all'    || String(r.y) === year) &&
                                    (quarter === 'all' || r.q === quarter)
                                );
        total = 0;
        items.forEach(li => {
            // const c = rows.reduce((sum, r) => sum + (r[li.dataset.id] ?? 0), 0);
            const c = rows.filter(r => String(r.s) === li.dataset.id).length;
            li.dataset.count = c;
            li.querySelector('.stages-count').textContent = c;
            total += c;
        });

        let acc = 0;
        const parts = [];
        items.forEach(li => {
            const c = +li.dataset.count;
            if (!c) return;
            const start = acc / total * 100;
            acc += c;
            parts.push(`${li.dataset.color} ${start}% ${acc / total * 100}%`);
        });

        pie.style.background = `conic-gradient(${total ? parts.join(', ') : '#e5e7eb 0 100%'})`;
        show(active);
    };

    items.forEach(li => {
        li.addEventListener('mouseenter', () => show(li));
        li.addEventListener('mouseleave', () => show(active));
        li.addEventListener('click', () => {
            active = active === li ? null : li;
            items.forEach(i => i.classList.toggle('is-active', i === active));
            show(active);
        });
    });

    divisionSel?.addEventListener('change', render);
    yearSel?.addEventListener('change', render);
    quarterSel?.addEventListener('change', render);
    render();
});
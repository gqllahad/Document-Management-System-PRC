document.querySelectorAll('[data-notices]').forEach(panel => {
    const sel = document.getElementById('divisionFilter');
    const yearSel = document.getElementById('fiscalFilter');
    const quarterSel = document.getElementById('quarterFilter');
    const items = [...panel.querySelectorAll('.notice-li')];
    const empty = panel.querySelector('.notice-empty');
    const count = panel.querySelector('.notice-count');

    const render = () => {
        const div = sel?.value || 'all';
        const year = yearSel?.value || 'all';
        const quarter = quarterSel?.value || 'all';
        let n = 0;

        items.forEach(li => {
            const show = (div === 'all' || li.dataset.division === div)  && (year === 'all' || li.dataset.year === year) && (quarter ===  'all' || li.dataset.quarter === quarter);
            li.hidden = !show;
            if (show) n++;
        });

        empty.hidden = n > 0;
        count.hidden = n === 0;
        count.textContent = n;
    };

    sel?.addEventListener('change', render);
    yearSel?.addEventListener('change', render);
    quarterSel?.addEventListener('change', render);
});
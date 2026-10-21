document.querySelectorAll('[data-notices]').forEach(panel => {
    const sel = document.getElementById('divisionFilter');
    const items = [...panel.querySelectorAll('.notice-li')];
    const empty = panel.querySelector('.notice-empty');
    const count = panel.querySelector('.notice-count');

    const render = () => {
        const div = sel?.value ?? 'all';
        let n = 0;

        items.forEach(li => {
            const show = div === 'all' || li.dataset.division === div;
            li.hidden = !show;
            if (show) n++;
        });

        empty.hidden = n > 0;
        count.hidden = n === 0;
        count.textContent = n;
    };

    sel?.addEventListener('change', render);
});
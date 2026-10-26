document.querySelectorAll('.bars-panel').forEach(panel => {
    const data = JSON.parse(panel.dataset.bars);
    const sel = document.getElementById('divisionFilter');
    const yearSel = document.getElementById('fiscalFilter');
    const axis = [...panel.querySelectorAll('.bars-axis span')];
    const pairs = [...panel.querySelectorAll('.bars-pair')];
    const sub = panel.querySelector('.stages-sub');
    const m = v => '₱' + (v / 1e6).toFixed(1) + 'M';

    const render = () => {
        const div  = sel?.value ?? 'all';
        const year = yearSel?.value ?? 'all';

        const rows = data.filter(r =>
            (div  === 'all' || String(r.d) === div) &&
            (year === 'all' || String(r.y) === year)
        );
        const sum = (q, k) => rows.reduce((s, r) => s + (r.q === q ? r[k] : 0), 0);

        const vals = pairs.map(p => ({ abc: sum(p.dataset.q, 'abc'), con: sum(p.dataset.q, 'contract') }));
        const max = Math.max(1, ...vals.flatMap(v => [v.abc, v.con]));
        const step = 10 ** (String(Math.floor(max)).length - 1);
        const top = Math.ceil(max / step) * step;

        axis.forEach((s, i) => s.textContent = m(top * (4 - i) / 4));

        pairs.forEach((p, i) => {
            const [a, c] = p.querySelectorAll('i');
            a.style.height = (vals[i].abc / top * 100) + '%';
            c.style.height = (vals[i].con / top * 100) + '%';
            a.title = 'ABC ' + m(vals[i].abc);
            c.title = 'Contract ' + m(vals[i].con);
        });

        const label = sel?.selectedOptions[0]?.text ?? 'Your division';
        const yr = year === 'all' ? '' : ` · FY ${year}`;
        sub.textContent = `${label}${yr} · by quarter`;
    };

    sel?.addEventListener('change', render);
    yearSel?.addEventListener('change', render);
    render();
});
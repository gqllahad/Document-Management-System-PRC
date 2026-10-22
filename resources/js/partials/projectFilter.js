const search = document.getElementById('projectSearch');
const buttons = document.querySelectorAll('.filter-buttons button');
let filter = 'all';
const divisionSel = document.getElementById('divisionFilter');
const typeSel = document.getElementById('typeFilter');

const quarterSel = document.getElementById('quarterFilter');

const messages = {
    all: 'No matching projects.',
    pending: 'No projects pending contract.',
    recorded: 'No projects with recorded contract.',
};

function applyFilters() {
    const q = (search?.value ?? '').toLowerCase();
    const counts = { all: 0, pending: 0, recorded: 0 };
    let visible = 0;

    document.querySelectorAll('#projectTable tbody tr:not(.empty)').forEach((row) => {
        const matchSearch   = row.textContent.toLowerCase().includes(q);
        const matchDivision = !divisionSel || divisionSel.value === 'all' || row.dataset.division === divisionSel.value;
        const matchType     = !typeSel || typeSel.value === 'all' || row.dataset.type === typeSel.value;
        const matchQuarter  = !quarterSel || quarterSel.value === 'all' || row.dataset.quarter === quarterSel.value;

        const base = matchSearch && matchDivision && matchType && matchQuarter;

        if (base) {
            counts.all++;
            if (row.dataset.contract in counts) counts[row.dataset.contract]++;
        }

        const show = base && (filter === 'all' || row.dataset.contract === filter);
        row.hidden = !show;
        if (show) visible++;
    });

    buttons.forEach((b) => {
        b.querySelector('span').textContent = counts[b.dataset.filter];
    });

    const noResults = document.querySelector('#projectTable .no-results');
    if (!noResults) return;
    noResults.hidden = visible > 0;
    noResults.firstElementChild.textContent = messages[filter];
}

search?.addEventListener('input', applyFilters);
divisionSel?.addEventListener('change', applyFilters);
typeSel?.addEventListener('change', applyFilters);
quarterSel?.addEventListener('change', applyFilters);

buttons.forEach((btn) => {
    btn.addEventListener('click', () => {
        buttons.forEach((b) => b.classList.remove('active'));
        btn.classList.add('active');
        filter = btn.dataset.filter;
        applyFilters();
    });
});

const sortBtn = document.querySelector('#projectTable .prj-sort');
const tbody = document.querySelector('#projectTable tbody');
let asc = true;

sortBtn?.addEventListener('click', () => {
    const rows = [...tbody.querySelectorAll('tr:not(.empty)')];

    rows.sort((a, b) => {
        const x = a.querySelector('.p-name').textContent.trim();
        const y = b.querySelector('.p-name').textContent.trim();
        return asc ? x.localeCompare(y) : y.localeCompare(x);
    }).forEach(r => tbody.append(r));

    tbody.append(tbody.querySelector('.no-results') ?? '');
    asc = !asc;
});
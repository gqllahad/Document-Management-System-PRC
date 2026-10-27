const search = document.getElementById('projectSearch');
const buttons = document.querySelectorAll('.filter-buttons button');
let filter = 'all';
const divisionSel = document.getElementById('divisionFilter');
const typeSel = document.getElementById('typeFilter');
const yearSel = document.getElementById('fiscalFilter');

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
        const matchSearch = row.textContent.toLowerCase().includes(q);

        const matchDivision =
            !divisionSel ||
            divisionSel.value === 'all' ||
            row.dataset.division === divisionSel.value;

        const matchType =
            !typeSel ||
            typeSel.value === 'all' ||
            row.dataset.type === typeSel.value;

        const rowQuarters = (row.dataset.quarter || '')
            .split(',')
            .map(value => value.trim())
            .filter(Boolean);

        const rowYears = (row.dataset.fiscalYear || '')
            .split(',')
            .map(value => value.trim())
            .filter(Boolean);

        const matchQuarter =
            !quarterSel ||
            quarterSel.value === 'all' ||
            rowQuarters.includes(quarterSel.value);

        const matchFiscal =
            !yearSel ||
            yearSel.value === 'all' ||
            rowYears.includes(yearSel.value);

        const base =
            matchSearch &&
            matchDivision &&
            matchType &&
            matchQuarter &&
            matchFiscal;

        if (base) {
            counts.all++;

            if (row.dataset.contract in counts) {
                counts[row.dataset.contract]++;
            }
        }

        const show =
            base &&
            (filter === 'all' || row.dataset.contract === filter);

        row.hidden = !show;

        if (show) visible++;
    });

    buttons.forEach((b) => {
        const count = b.querySelector('span');

        if (count) {
            count.textContent = counts[b.dataset.filter] ?? 0;
        }
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
yearSel?.addEventListener('change', applyFilters);

buttons.forEach((btn) => {
    btn.addEventListener('click', () => {
        buttons.forEach((b) => b.classList.remove('active'));
        btn.classList.add('active');
        filter = btn.dataset.filter;
        applyFilters();
    });
});


const table = document?.querySelector('#projectTable');
const tbody = table?.querySelector('tbody');
const sortBtn = table?.querySelector('.prj-sort');
const currentBtn = table?.querySelector('.current-sort');

function sortRows(getValue, asc) {
    const rows = [...tbody.querySelectorAll('tr:not(.empty):not(.no-results)')];
    rows.sort((a, b) => {
        const x = getValue(a), y = getValue(b);
        return asc ? (x > y ? 1 : x < y ? -1 : 0) : (x < y ? 1 : x > y ? -1 : 0);
    }).forEach(r => tbody.append(r));

    const noResults = tbody.querySelector('.no-results');
    if (noResults) tbody.append(noResults);
}

let nameAsc = true;
sortBtn?.addEventListener('click', () => {
    sortRows(r => r.querySelector('.p-name').textContent.trim().toLowerCase(), nameAsc);
    nameAsc = !nameAsc;
});

let dateAsc = false; //true 2 clicl
currentBtn?.addEventListener('click', () => {
    sortRows(r => new Date(r.dataset.updated).getTime() || 0, dateAsc);
    dateAsc = !dateAsc;
});
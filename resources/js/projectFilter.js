const search = document.getElementById('projectSearch');
const buttons = document.querySelectorAll('.filter-buttons button');
let filter = 'all';

const messages = {
    all: 'No matching projects.',
    awaiting: 'No projects awaiting contract.',
    recorded: 'No projects with recorded contract.',
};


function applyFilters() {
    const q = (search?.value ?? '').toLowerCase();
    let visible = 0;

    document.querySelectorAll('#projectTable tbody tr:not(.empty)').forEach((row) => {
        const matchSearch = row.textContent.toLowerCase().includes(q);
        const matchFilter = filter === 'all' || row.dataset.contract === filter;
        const show = matchSearch && matchFilter;
        row.hidden = !show;
        if (show) visible++;
    });

    const noResults = document.querySelector('#projectTable .no-results');
    if (!noResults) return;

    if (noResults) noResults.hidden = visible > 0;
    noResults.firstElementChild.textContent = messages[filter];
}

search?.addEventListener('input', applyFilters);

buttons.forEach((btn) => {
    btn.addEventListener('click', () => {
        buttons.forEach((b) => b.classList.remove('active'));
        btn.classList.add('active');
        filter = btn.dataset.filter;
        applyFilters();
    });
});
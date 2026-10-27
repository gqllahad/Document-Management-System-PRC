const section = document.getElementById('unitsSection');

if (section) {
    const oldUnits = JSON.parse(section.dataset.oldUnits || '[]').map(
        unit => typeof unit === 'string' ? unit : unit?.unit_name || ''
    );

    const MIN_UNITS = 2;

const list      = document.getElementById('unitList');
const count     = document.getElementById('unitCount');
const removeBtn = document.getElementById('removeUnit');
const radios    = document.querySelectorAll('input[name="procurement_structure"]');

function render() {
    const n = list.children.length;
    count.textContent = `${n} unit${n > 1 ? 's' : ''}`;
    removeBtn.disabled = n <= MIN_UNITS;
}

function addUnit(value = '') {
    const n = list.children.length + 1;
    list.insertAdjacentHTML('beforeend', `
    <div class="unit">
        <span class="unit-no">${n}</span>
        <div class="unit-body">
            <span class="unit-label">Unit ${n} name</span>
            <input type="text" name="units[]" placeholder="e.g. Supply and Delivery of Computers" required>
            <small>List the items included in this unit.</small>
        </div>
    </div>`);
    list.lastElementChild.querySelector('input').value = value;
    render();
}

function toggleUnits() {
    const multiple = document.querySelector('input[name="procurement_structure"]:checked')?.value === 'multiple';
    section.hidden = !multiple;
    list.querySelectorAll('input').forEach(i => i.disabled = !multiple);

    if (multiple && !list.children.length) {
        if (oldUnits.length) oldUnits.forEach(v => addUnit(v));
        else for (let i = 0; i < MIN_UNITS; i++) addUnit();
    }
}

document.getElementById('addUnit').addEventListener('click', () => addUnit());
removeBtn.addEventListener('click', () => { list.lastElementChild?.remove(); render(); });
radios.forEach(r => r.addEventListener('change', toggleUnits));

toggleUnits();
};
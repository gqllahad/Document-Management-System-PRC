const budgetInput = document.getElementById('approved_budget');
const contractInput = document.getElementById('contract_price');

budgetInput.addEventListener('input', function () {
    let value = this.value.replace(/,/g, '');

    value = value.replace(/[^\d.]/g, '');

    const parts = value.split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    this.value = parts.join('.');
});

contractInput.addEventListener('input', function () {
    let value = this.value.replace(/,/g, '');

    value = value.replace(/[^\d.]/g, '');

    const parts = value.split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    this.value = parts.join('.');
});
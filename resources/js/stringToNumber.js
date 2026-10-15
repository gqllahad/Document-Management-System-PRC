const budgetInput = document.getElementById('approved_budget');
const contractInput = document.getElementById('contract_price');

if (budgetInput) {
    budgetInput.addEventListener('input', function () {
        let value = this.value.replace(/,/g, '');

        value = value.replace(/[^\d.]/g, '');

        const parts = value.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

        this.value = parts.join('.');
    });
}

if (contractInput) {
    contractInput.addEventListener('input', function () {
        let value = this.value.replace(/,/g, '');

        value = value.replace(/[^\d.]/g, '');

        const parts = value.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

        this.value = parts.join('.');
    });
}
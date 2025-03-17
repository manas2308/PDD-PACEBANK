// Chart for Accounts Per Acc Types
const accountsCtx = document.getElementById('accountsChart').getContext('2d');
new Chart(accountsCtx, {
    type: 'pie',
    data: {
        labels: ['Savings Acc', 'Fixed Deposit Acc', 'Current Acc'],
        datasets: [{
            data: [50, 25, 25],
            backgroundColor: ['#3498db', '#2ecc71', '#e74c3c']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Chart for Transactions
const transactionsCtx = document.getElementById('transactionsChart').getContext('2d');
new Chart(transactionsCtx, {
    type: 'doughnut',
    data: {
        labels: ['Withdrawals', 'Deposits', 'Transfers'],
        datasets: [{
            data: [1, 50, 49],
            backgroundColor: ['#e74c3c', '#3498db', '#f1c40f']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

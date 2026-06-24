<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let stockChart;
let categoryChart;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTables
    const lowStockTable = $('#lowStockTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/api/low-stock',
        columns: [
            { data: 'name' },
            { data: 'category.name' },
            { data: 'current_quantity' },
            { data: 'min_quantity' },
            { 
                data: null,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-primary" onclick="addStock(${row.id})">
                            Add Stock
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="editProduct(${row.id})">
                            Edit
                        </button>
                    `;
                }
            }
        ]
    });

    const transactionsTable = $('#transactionsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/api/transactions',
        columns: [
            { data: 'date' },
            { data: 'product.name' },
            { data: 'type' },
            { data: 'quantity' },
            { data: 'user.name' }
        ]
    });

    // Initialize Charts
    const ctxStock = document.getElementById('stockChart').getContext('2d');
    stockChart = new Chart(ctxStock, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Stock Value',
                data: [],
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const ctxCategory = document.getElementById('categoryChart').getContext('2d');
    categoryChart = new Chart(ctxCategory, {
        type: 'pie',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)'
                ]
            }]
        }
    });

    // Load charts on page load
    updateStockChart(30);
    updateCategoryChart();

    // Update dashboard stats
    axios.get('/api/dashboard-stats')
        .then(response => {
            document.getElementById('totalProducts').textContent = response.data.totalProducts;
            document.getElementById('totalStockValue').textContent = `$${response.data.totalStockValue.toFixed(2)}`;
            document.getElementById('recentTransactions').textContent = response.data.recentTransactions;
            document.getElementById('lowStockItems').textContent = response.data.lowStockItems;
        });

    // Dropdown period change
    document.querySelectorAll('.dropdown-menu a').forEach(item => {
        item.addEventListener('click', e => {
            e.preventDefault();
            const period = parseInt(item.getAttribute('data-period'));
            document.getElementById('chartPeriod').textContent = item.textContent;
            updateStockChart(period);
        });
    });
});

function updateStockChart(period) {
    axios.get(`/api/stock-history?period=${period}`)
        .then(response => {
            console.log('Stock History Data:', response.data); // Debug log
            stockChart.data.labels = response.data.dates;
            stockChart.data.datasets[0].data = response.data.values;
            stockChart.update();
        })
        .catch(error => {
            console.error('Failed to load stock history:', error);
        });
}

function updateCategoryChart() {
    axios.get('/api/category-distribution')
        .then(response => {
            categoryChart.data.labels = response.data.labels;
            categoryChart.data.datasets[0].data = response.data.values;
            categoryChart.update();
        })
        .catch(error => {
            console.error('Failed to load category data:', error);
        });
}

function addStock(productId) {
    Swal.fire({
        title: 'Add Stock',
        input: 'number',
        inputAttributes: {
            min: 1
        },
        inputLabel: 'Quantity to add',
        showCancelButton: true,
        confirmButtonText: 'Add',
        preConfirm: quantity => {
            if (!quantity || quantity <= 0) {
                Swal.showValidationMessage('Please enter a valid quantity');
            }
            return quantity;
        }
    }).then(result => {
        if (result.isConfirmed) {
            axios.post(`/api/products/${productId}/add-stock`, { quantity: result.value })
                .then(() => {
                    Swal.fire('Success', 'Stock added successfully', 'success');
                    $('#lowStockTable').DataTable().ajax.reload();
                    updateStockChart(parseInt(document.getElementById('chartPeriod').textContent.match(/\d+/)[0]));
                })
                .catch(() => {
                    Swal.fire('Error', 'Failed to add stock', 'error');
                });
        }
    });
}

function editProduct(productId) {
    window.location.href = `/products/${productId}/edit`;
}
</script>

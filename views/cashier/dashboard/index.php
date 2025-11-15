<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Dashboard - School Management</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .summary-card { transition: transform 0.2s; }
        .summary-card:hover { transform: translateY(-5px); }
        .chart-container { position: relative; height: 400px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="/cashier/dashboard">School Management Cashier</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="/cashier/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/cashier/fees">Fees</a></li>
                    <li class="nav-item"><a class="nav-link" href="/cashier/outstanding">Outstanding</a></li>
                    <li class="nav-item"><a class="nav-link" href="/cashier/reports">Reports</a></li>
                    <li class="nav-item"><a class="nav-link" href="/cashier/expenses">Expenses</a></li>
                    <li class="nav-item"><a class="nav-link" href="/cashier/documents">Documents</a></li>
                    <li class="nav-item"><a class="nav-link" href="/cashier/expense-categories">Categories</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <h1 class="mb-4">Cashier Dashboard</h1>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card summary-card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Monthly Collection</h5>
                        <h2 id="monthly-collection">$<?php echo number_format($stats['monthly_collection'], 2); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card summary-card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Outstanding Payments</h5>
                        <h2 id="outstanding-payments">$<?php echo number_format($stats['outstanding_payments'], 2); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card summary-card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">Monthly Expenses</h5>
                        <h2 id="monthly-expenses">$<?php echo number_format($stats['monthly_expenses'], 2); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card summary-card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Net Profit</h5>
                        <h2 id="net-profit">$<?php echo number_format($stats['net_profit'], 2); ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Financial Overview (Last 12 Months)</div>
                    <div class="card-body">
                        <canvas id="financialChart" class="chart-container"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Payment Status Distribution</div>
                    <div class="card-body">
                        <canvas id="paymentStatusChart" class="chart-container"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions and Recent Activity -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Quick Actions</div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/cashier/fees" class="btn btn-primary">Process Payment</a>
                            <a href="/cashier/expenses" class="btn btn-success">Add Expense</a>
                            <a href="/cashier/outstanding" class="btn btn-warning">View Outstanding</a>
                            <a href="/cashier/reports" class="btn btn-info">Generate Report</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Recent Payments</div>
                    <div class="card-body">
                        <ul id="recent-payments" class="list-group list-group-flush">
                            <!-- Recent payments will be loaded via AJAX -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load dynamic data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadStats();
            loadFinancialChart();
            loadPaymentStatusChart();
            loadRecentPayments();
        });

        function loadStats() {
            fetch('/cashier/api/dashboard-stats')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('monthly-collection').textContent = '$' + parseFloat(data.monthly_collection).toFixed(2);
                    document.getElementById('outstanding-payments').textContent = '$' + parseFloat(data.outstanding_payments).toFixed(2);
                    document.getElementById('monthly-expenses').textContent = '$' + parseFloat(data.monthly_expenses).toFixed(2);
                    document.getElementById('net-profit').textContent = '$' + parseFloat(data.net_profit).toFixed(2);
                })
                .catch(error => console.error('Error loading stats:', error));
        }

        function loadFinancialChart() {
            fetch('/cashier/api/reports/financial')
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('financialChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.map(item => item.period),
                            datasets: [{
                                label: 'Collection',
                                data: data.map(item => item.collection),
                                borderColor: 'rgba(54, 162, 235, 1)',
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            }, {
                                label: 'Expenses',
                                data: data.map(item => item.expenses),
                                borderColor: 'rgba(255, 99, 132, 1)',
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            }, {
                                label: 'Profit',
                                data: data.map(item => item.profit),
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error loading financial chart:', error));
        }

        function loadPaymentStatusChart() {
            // This would need an API endpoint for payment status distribution
            // For now, placeholder
            const ctx = document.getElementById('paymentStatusChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Paid', 'Pending', 'Overdue'],
                    datasets: [{
                        data: [65, 25, 10],
                        backgroundColor: ['#28a745', '#ffc107', '#dc3545']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        function loadRecentPayments() {
            // This would need an API endpoint for recent payments
            // For now, placeholder
            const list = document.getElementById('recent-payments');
            list.innerHTML = '<li class="list-group-item">Recent payments will be loaded here</li>';
        }

        // Auto-refresh stats every 5 minutes
        setInterval(loadStats, 300000);
    </script>
</body>
</html>
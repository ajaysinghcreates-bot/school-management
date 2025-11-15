<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Reports - School Management</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container { position: relative; height: 400px; margin-bottom: 2rem; }
        .report-card { margin-bottom: 2rem; }
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
                    <li class="nav-item"><a class="nav-link" href="/cashier/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/cashier/fees">Fees</a></li>
                    <li class="nav-item"><a class="nav-link" href="/cashier/outstanding">Outstanding</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/cashier/reports">Reports</a></li>
                    <li class="nav-item"><a class="nav-link" href="/cashier/expenses">Expenses</a></li>
                    <li class="nav-item"><a class="nav-link" href="/cashier/expense-categories">Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="/cashier/documents">Documents</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Financial Reports</h1>
            <div>
                <select id="report-period" class="form-select d-inline-block w-auto me-2">
                    <option value="monthly">Monthly</option>
                    <option value="quarterly">Quarterly</option>
                    <option value="yearly">Yearly</option>
                </select>
                <button class="btn btn-primary" id="generate-report">Generate Report</button>
                <button class="btn btn-secondary" id="export-pdf">Export PDF</button>
                <button class="btn btn-success" id="export-excel">Export Excel</button>
                <button class="btn btn-info" id="show-analytics">Advanced Analytics</button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card report-card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Revenue</h5>
                        <h3 id="total-revenue">$0.00</h3>
                        <small id="revenue-change" class="text-white-50">+0% from last period</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card report-card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Expenses</h5>
                        <h3 id="total-expenses">$0.00</h3>
                        <small id="expenses-change" class="text-white-50">+0% from last period</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card report-card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Net Profit</h5>
                        <h3 id="net-profit">$0.00</h3>
                        <small id="profit-change" class="text-white-50">+0% from last period</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card report-card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Outstanding Fees</h5>
                        <h3 id="outstanding-fees">$0.00</h3>
                        <small id="outstanding-change" class="text-white-50">+0% from last period</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Revenue vs Expenses Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueExpensesChart" class="chart-container"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Payment Methods</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="paymentMethodsChart" class="chart-container"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Charts -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Monthly Profit Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="profitTrendChart" class="chart-container"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Fee Collection Status</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="feeStatusChart" class="chart-container"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Analytics Section (Hidden by default) -->
        <div id="analytics-section" style="display: none;">
            <h2 class="mb-4">Advanced Financial Analytics & Forecasting</h2>

            <!-- Forecasting Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card report-card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Revenue Forecast (6 months)</h5>
                            <h4 id="revenue-forecast">$0.00</h4>
                            <small id="revenue-trend" class="text-white-50">Trend analysis</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card report-card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Expense Forecast</h5>
                            <h4 id="expense-forecast">$0.00</h4>
                            <small id="expense-trend" class="text-white-50">Category analysis</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card report-card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Seasonal Peak</h5>
                            <h4 id="seasonal-peak">N/A</h4>
                            <small id="seasonal-insight" class="text-white-50">Monthly patterns</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card report-card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Growth Rate</h5>
                            <h4 id="growth-rate">0.00%</h4>
                            <small id="growth-period" class="text-white-50">Year over year</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Forecasting Charts -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Revenue Forecasting (Next 6 Months)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="forecastingChart" class="chart-container"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Expense Trends by Category</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="expenseTrendsChart" class="chart-container"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seasonal Analysis -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Seasonal Revenue Patterns</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="seasonalChart" class="chart-container"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Payment Method Distribution</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="paymentMethodsChart" class="chart-container"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Insights -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Financial Insights & Recommendations</h5>
                        </div>
                        <div class="card-body">
                            <div id="insights-content">
                                <div class="alert alert-info">
                                    <i class="fas fa-chart-line"></i> Click "Advanced Analytics" to load forecasting data and insights.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Report Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Detailed Financial Report</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="report-table">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Revenue</th>
                                <th>Expenses</th>
                                <th>Profit</th>
                                <th>Profit Margin</th>
                            </tr>
                        </thead>
                        <tbody id="report-tbody">
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPeriod = 'monthly';
        let reportData = [];

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('report-period').addEventListener('change', function() {
                currentPeriod = this.value;
                loadReport();
            });
            document.getElementById('generate-report').addEventListener('click', loadReport);
            document.getElementById('export-pdf').addEventListener('click', exportPDF);
            document.getElementById('export-excel').addEventListener('click', exportExcel);
            document.getElementById('show-analytics').addEventListener('click', toggleAnalytics);

            loadReport();
        });

        function loadReport() {
            const params = new URLSearchParams({ period: currentPeriod });

            fetch('/cashier/api/reports/financial?' + params)
                .then(response => response.json())
                .then(data => {
                    reportData = data;
                    updateSummaryCards(data);
                    renderCharts(data);
                    renderReportTable(data);
                })
                .catch(error => console.error('Error loading report:', error));
        }

        function updateSummaryCards(data) {
            if (data.length === 0) return;

            const latest = data[data.length - 1];
            const previous = data.length > 1 ? data[data.length - 2] : null;

            const totalRevenue = data.reduce((sum, item) => sum + item.collection, 0);
            const totalExpenses = data.reduce((sum, item) => sum + item.expenses, 0);
            const totalProfit = data.reduce((sum, item) => sum + item.profit, 0);

            document.getElementById('total-revenue').textContent = '$' + totalRevenue.toFixed(2);
            document.getElementById('total-expenses').textContent = '$' + totalExpenses.toFixed(2);
            document.getElementById('net-profit').textContent = '$' + totalProfit.toFixed(2);

            // Calculate changes (simplified)
            if (previous) {
                const revenueChange = ((latest.collection - previous.collection) / previous.collection * 100).toFixed(1);
                const expensesChange = ((latest.expenses - previous.expenses) / previous.expenses * 100).toFixed(1);
                const profitChange = ((latest.profit - previous.profit) / Math.abs(previous.profit) * 100).toFixed(1);

                document.getElementById('revenue-change').textContent = `${revenueChange > 0 ? '+' : ''}${revenueChange}% from last period`;
                document.getElementById('expenses-change').textContent = `${expensesChange > 0 ? '+' : ''}${expensesChange}% from last period`;
                document.getElementById('profit-change').textContent = `${profitChange > 0 ? '+' : ''}${profitChange}% from last period`;
            }

            // Outstanding fees (placeholder - would need separate API call)
            document.getElementById('outstanding-fees').textContent = '$0.00';
        }

        function renderCharts(data) {
            // Revenue vs Expenses Trend
            const ctx1 = document.getElementById('revenueExpensesChart').getContext('2d');
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: data.map(item => item.period),
                    datasets: [{
                        label: 'Revenue',
                        data: data.map(item => item.collection),
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    }, {
                        label: 'Expenses',
                        data: data.map(item => item.expenses),
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true } }
                }
            });

            // Payment Methods (placeholder data)
            const ctx2 = document.getElementById('paymentMethodsChart').getContext('2d');
            new Chart(ctx2, {
                type: 'pie',
                data: {
                    labels: ['Cash', 'Card', 'Bank Transfer', 'Check'],
                    datasets: [{
                        data: [45, 30, 15, 10],
                        backgroundColor: ['#28a745', '#007bff', '#ffc107', '#dc3545']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Profit Trend
            const ctx3 = document.getElementById('profitTrendChart').getContext('2d');
            new Chart(ctx3, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.period),
                    datasets: [{
                        label: 'Profit',
                        data: data.map(item => item.profit),
                        backgroundColor: data.map(item => item.profit >= 0 ? 'rgba(40, 167, 69, 0.5)' : 'rgba(220, 53, 69, 0.5)'),
                        borderColor: data.map(item => item.profit >= 0 ? 'rgba(40, 167, 69, 1)' : 'rgba(220, 53, 69, 1)'),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true } }
                }
            });

            // Fee Status (placeholder data)
            const ctx4 = document.getElementById('feeStatusChart').getContext('2d');
            new Chart(ctx4, {
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

        function renderReportTable(data) {
            const tbody = document.getElementById('report-tbody');
            tbody.innerHTML = '';

            data.forEach(item => {
                const margin = item.collection > 0 ? (item.profit / item.collection * 100).toFixed(1) : 0;
                tbody.innerHTML += `
                    <tr>
                        <td>${item.period}</td>
                        <td>$${item.collection.toFixed(2)}</td>
                        <td>$${item.expenses.toFixed(2)}</td>
                        <td>$${item.profit.toFixed(2)}</td>
                        <td>${margin}%</td>
                    </tr>
                `;
            });
        }

        function exportPDF() {
            const period = document.getElementById('report-period').value;
            const url = '/cashier/api/reports/export?period=' + period;
            window.open(url, '_blank');
        }

        function exportExcel() {
            const period = document.getElementById('report-period').value;
            const url = '/cashier/api/reports/export-excel?period=' + period + '&format=csv';
            window.open(url, '_blank');
        }

        function toggleAnalytics() {
            const analyticsSection = document.getElementById('analytics-section');
            const button = document.getElementById('show-analytics');

            if (analyticsSection.style.display === 'none') {
                analyticsSection.style.display = 'block';
                button.textContent = 'Hide Analytics';
                button.classList.remove('btn-info');
                button.classList.add('btn-secondary');
                loadAnalytics();
            } else {
                analyticsSection.style.display = 'none';
                button.textContent = 'Advanced Analytics';
                button.classList.remove('btn-secondary');
                button.classList.add('btn-info');
            }
        }

        function loadAnalytics() {
            fetch('/cashier/api/analytics')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        document.getElementById('insights-content').innerHTML = '<div class="alert alert-warning">Insufficient data for advanced analytics.</div>';
                        return;
                    }

                    updateAnalyticsCards(data);
                    renderAnalyticsCharts(data);
                    generateInsights(data);
                })
                .catch(error => {
                    console.error('Error loading analytics:', error);
                    document.getElementById('insights-content').innerHTML = '<div class="alert alert-danger">Error loading analytics data.</div>';
                });
        }

        function updateAnalyticsCards(data) {
            // Revenue forecast
            if (data.revenue_forecast && data.revenue_forecast.length > 0) {
                const latestForecast = data.revenue_forecast[data.revenue_forecast.length - 1];
                document.getElementById('revenue-forecast').textContent = '$' + latestForecast.predicted_revenue.toFixed(2);
                document.getElementById('revenue-trend').textContent = 'Confidence: ' + latestForecast.confidence;
            }

            // Expense forecast
            if (data.expense_forecast && data.expense_forecast.length > 0) {
                const totalForecast = data.expense_forecast.reduce((sum, item) => sum + item.forecasted_amount, 0);
                document.getElementById('expense-forecast').textContent = '$' + totalForecast.toFixed(2);
                document.getElementById('expense-trend').textContent = data.expense_forecast.length + ' categories analyzed';
            }

            // Seasonal patterns
            if (data.seasonal_patterns && data.seasonal_patterns.length > 0) {
                const peakMonth = data.seasonal_patterns.reduce((max, month) =>
                    month.monthly_revenue > max.monthly_revenue ? month : max
                );
                document.getElementById('seasonal-peak').textContent = peakMonth.month_name;
                document.getElementById('seasonal-insight').textContent = '$' + peakMonth.monthly_revenue.toFixed(2) + ' average';
            }

            // Growth rate calculation
            if (data.revenue_trends && data.revenue_trends.length >= 2) {
                const first = data.revenue_trends[0].revenue;
                const last = data.revenue_trends[data.revenue_trends.length - 1].revenue;
                const growthRate = ((last - first) / first * 100).toFixed(2);
                document.getElementById('growth-rate').textContent = growthRate + '%';
                document.getElementById('growth-period').textContent = data.revenue_trends.length + ' months analyzed';
            }
        }

        function renderAnalyticsCharts(data) {
            // Forecasting Chart
            if (data.revenue_forecast && data.revenue_forecast.length > 0) {
                const ctx1 = document.getElementById('forecastingChart').getContext('2d');
                const historicalData = data.revenue_trends.slice(-6); // Last 6 months
                const forecastData = data.revenue_forecast;

                new Chart(ctx1, {
                    type: 'line',
                    data: {
                        labels: [
                            ...historicalData.map(item => item.month),
                            ...forecastData.map(item => item.month)
                        ],
                        datasets: [{
                            label: 'Historical Revenue',
                            data: [
                                ...historicalData.map(item => item.revenue),
                                ...Array(forecastData.length).fill(null)
                            ],
                            borderColor: 'rgba(54, 162, 235, 1)',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        }, {
                            label: 'Forecasted Revenue',
                            data: [
                                ...Array(historicalData.length).fill(null),
                                ...forecastData.map(item => item.predicted_revenue)
                            ],
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderDash: [5, 5]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }

            // Expense Trends Chart
            if (data.expense_analysis && data.expense_analysis.length > 0) {
                const ctx2 = document.getElementById('expenseTrendsChart').getContext('2d');
                const categories = [...new Set(data.expense_analysis.map(item => item.category))];

                const datasets = categories.map((category, index) => {
                    const categoryData = data.expense_analysis.filter(item => item.category === category);
                    return {
                        label: category,
                        data: categoryData.map(item => item.total_expenses),
                        backgroundColor: `hsl(${index * 360 / categories.length}, 70%, 50%)`,
                    };
                });

                new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: [...new Set(data.expense_analysis.map(item => item.month))],
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }

            // Seasonal Chart
            if (data.seasonal_patterns && data.seasonal_patterns.length > 0) {
                const ctx3 = document.getElementById('seasonalChart').getContext('2d');
                new Chart(ctx3, {
                    type: 'bar',
                    data: {
                        labels: data.seasonal_patterns.map(item => item.month_name),
                        datasets: [{
                            label: 'Monthly Revenue',
                            data: data.seasonal_patterns.map(item => item.monthly_revenue),
                            backgroundColor: 'rgba(75, 192, 192, 0.5)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }

            // Payment Methods Distribution (from analytics data)
            if (data.payment_methods && data.payment_methods.length > 0) {
                const ctx4 = document.getElementById('paymentMethodsChart').getContext('2d');
                new Chart(ctx4, {
                    type: 'doughnut',
                    data: {
                        labels: data.payment_methods.map(item => item.payment_method),
                        datasets: [{
                            data: data.payment_methods.map(item => item.total_amount),
                            backgroundColor: ['#28a745', '#007bff', '#ffc107', '#dc3545', '#6f42c1']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }
        }

        function generateInsights(data) {
            let insights = '<div class="row">';

            // Revenue insights
            if (data.revenue_trends && data.revenue_trends.length >= 2) {
                const first = data.revenue_trends[0].revenue;
                const last = data.revenue_trends[data.revenue_trends.length - 1].revenue;
                const growth = ((last - first) / first * 100).toFixed(1);

                insights += `
                    <div class="col-md-6">
                        <div class="alert alert-${growth > 0 ? 'success' : 'warning'}">
                            <h6><i class="fas fa-chart-line"></i> Revenue Trend</h6>
                            <p>Revenue has ${growth > 0 ? 'grown' : 'declined'} by ${Math.abs(growth)}% over the analyzed period.</p>
                        </div>
                    </div>
                `;
            }

            // Expense insights
            if (data.expense_forecast && data.expense_forecast.length > 0) {
                const increasingCategories = data.expense_forecast.filter(item => item.trend === 'increasing');
                if (increasingCategories.length > 0) {
                    insights += `
                        <div class="col-md-6">
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-triangle"></i> Expense Alert</h6>
                                <p>${increasingCategories.length} expense categorie(s) showing increasing trends. Consider cost optimization.</p>
                            </div>
                        </div>
                    `;
                }
            }

            // Seasonal insights
            if (data.seasonal_patterns && data.seasonal_patterns.length > 0) {
                const peakMonth = data.seasonal_patterns.reduce((max, month) =>
                    month.monthly_revenue > max.monthly_revenue ? month : max
                );
                insights += `
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-calendar-alt"></i> Seasonal Pattern</h6>
                            <p>${peakMonth.month_name} shows the highest revenue (${peakMonth.monthly_revenue.toFixed(2)}). Consider seasonal promotions.</p>
                        </div>
                    </div>
                `;
            }

            // Payment method insights
            if (data.payment_methods && data.payment_methods.length > 0) {
                const topMethod = data.payment_methods[0];
                insights += `
                    <div class="col-md-6">
                        <div class="alert alert-primary">
                            <h6><i class="fas fa-credit-card"></i> Payment Preferences</h6>
                            <p>${topMethod.payment_method} is the most popular payment method (${topMethod.percentage.toFixed(1)}% of transactions).</p>
                        </div>
                    </div>
                `;
            }

            insights += '</div>';
            document.getElementById('insights-content').innerHTML = insights;
        }
    </script>
</body>
</html>
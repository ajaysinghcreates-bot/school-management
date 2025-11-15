<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fees Management - School Management</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-paid { color: #28a745; }
        .status-pending { color: #ffc107; }
        .status-overdue { color: #dc3545; }
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
                    <li class="nav-item"><a class="nav-link active" href="/cashier/fees">Fees</a></li>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Fees Management</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#paymentModal">Process Payment</button>
            <button class="btn btn-info ms-2" data-bs-toggle="modal" data-bs-target="#bulkImportModal">Bulk Import</button>
        </div>

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="search" class="form-control" placeholder="Search by student name...">
            </div>
            <div class="col-md-3">
                <select id="status-filter" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
            <div class="col-md-2">
                <button id="search-btn" class="btn btn-secondary">Search</button>
            </div>
        </div>

        <!-- Fees Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="fees-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="fees-tbody">
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
                <nav aria-label="Fees pagination">
                    <ul class="pagination justify-content-center" id="pagination">
                        <!-- Pagination will be generated via JS -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Process Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="payment-form">
                        <input type="hidden" id="fee-id" name="fee_id">
                        <div class="mb-3">
                            <label for="student-name" class="form-label">Student</label>
                            <input type="text" class="form-control" id="student-name" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="payment-method" class="form-label">Payment Method</label>
                            <select class="form-select" id="payment-method" name="payment_method" required>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="check">Check</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="payment-date" class="form-label">Payment Date</label>
                            <input type="date" class="form-control" id="payment-date" name="payment_date" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="process-payment-btn">Process Payment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Import Modal -->
    <div class="modal fade" id="bulkImportModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Payment Import</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>CSV Format:</strong> Upload a CSV file with the following columns:<br>
                        <code>fee_id, amount, payment_date, payment_method</code><br><br>
                        <strong>Example:</strong><br>
                        <code>1, 500.00, 2024-01-15, cash</code><br>
                        <code>2, 750.50, 2024-01-15, card</code><br><br>
                        <strong>Valid payment methods:</strong> cash, card, bank_transfer, check
                    </div>
                    <form id="bulk-import-form" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="csv-file" class="form-label">Select CSV File</label>
                            <input type="file" class="form-control" id="csv-file" name="csv_file" accept=".csv" required>
                        </div>
                    </form>
                    <div id="import-progress" class="d-none">
                        <div class="progress mb-3">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div id="import-status"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="start-import-btn">Import Payments</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPage = 1;
        let currentSearch = '';
        let currentStatus = '';

        document.addEventListener('DOMContentLoaded', function() {
            loadFees();
            document.getElementById('search-btn').addEventListener('click', function() {
                currentSearch = document.getElementById('search').value;
                currentStatus = document.getElementById('status-filter').value;
                currentPage = 1;
                loadFees();
            });
            document.getElementById('process-payment-btn').addEventListener('click', processPayment);
            document.getElementById('start-import-btn').addEventListener('click', startBulkImport);
        });

        function loadFees() {
            const params = new URLSearchParams({
                search: currentSearch,
                status: currentStatus,
                page: currentPage
            });

            fetch('/cashier/api/fees?' + params)
                .then(response => response.json())
                .then(data => {
                    renderFeesTable(data.data);
                    renderPagination(data.total, data.page, data.limit);
                })
                .catch(error => console.error('Error loading fees:', error));
        }

        function renderFeesTable(fees) {
            const tbody = document.getElementById('fees-tbody');
            tbody.innerHTML = '';

            if (fees.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No fees found</td></tr>';
                return;
            }

            fees.forEach(fee => {
                const statusClass = fee.status === 'paid' ? 'status-paid' : 'status-pending';
                const statusText = fee.status.charAt(0).toUpperCase() + fee.status.slice(1);
                const actions = fee.status === 'pending' ?
                    `<button class="btn btn-sm btn-success" onclick="openPaymentModal(${fee.id}, '${fee.student_name}', ${fee.amount})">Pay</button>` :
                    '<span class="text-muted">Paid</span>';

                tbody.innerHTML += `
                    <tr>
                        <td>${fee.student_name}</td>
                        <td>${fee.description}</td>
                        <td>$${parseFloat(fee.amount).toFixed(2)}</td>
                        <td>${new Date(fee.due_date).toLocaleDateString()}</td>
                        <td><span class="${statusClass}">${statusText}</span></td>
                        <td>${actions}</td>
                    </tr>
                `;
            });
        }

        function renderPagination(total, page, limit) {
            const totalPages = Math.ceil(total / limit);
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';

            if (totalPages <= 1) return;

            for (let i = 1; i <= totalPages; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === page ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i})">${i}</a>`;
                pagination.appendChild(li);
            }
        }

        function changePage(page) {
            currentPage = page;
            loadFees();
        }

        function openPaymentModal(feeId, studentName, amount) {
            document.getElementById('fee-id').value = feeId;
            document.getElementById('student-name').value = studentName;
            document.getElementById('amount').value = amount;
            document.getElementById('payment-date').value = new Date().toISOString().split('T')[0];
            new bootstrap.Modal(document.getElementById('paymentModal')).show();
        }

        function processPayment() {
            const form = document.getElementById('payment-form');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            fetch('/cashier/api/fees/process-payment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                    // Open PDF receipt in new tab
                    window.open('/cashier/receipt?payment_id=' + result.payment_id, '_blank');
                    loadFees(); // Reload the fees table
                } else {
                    alert('Error processing payment: ' + result.error);
                }
            })
            .catch(error => console.error('Error processing payment:', error));
        }

        function startBulkImport() {
            const fileInput = document.getElementById('csv-file');
            const file = fileInput.files[0];

            if (!file) {
                alert('Please select a CSV file to import.');
                return;
            }

            const formData = new FormData();
            formData.append('csv_file', file);

            const progressDiv = document.getElementById('import-progress');
            const statusDiv = document.getElementById('import-status');
            const progressBar = progressDiv.querySelector('.progress-bar');

            // Show progress
            progressDiv.classList.remove('d-none');
            progressBar.style.width = '0%';
            statusDiv.textContent = 'Processing payments...';

            fetch('/cashier/api/fees/bulk-import', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                progressBar.style.width = '100%';
                statusDiv.innerHTML = `
                    <strong>Import completed!</strong><br>
                    Successfully processed: ${result.success} payments<br>
                    Errors: ${result.errors.length}<br>
                    Total rows processed: ${result.total_processed}
                `;

                if (result.errors.length > 0) {
                    statusDiv.innerHTML += '<br><strong>Errors:</strong><ul>';
                    result.errors.forEach(error => {
                        statusDiv.innerHTML += `<li>${error}</li>`;
                    });
                    statusDiv.innerHTML += '</ul>';
                }

                // Reload fees table
                loadFees();

                // Reset form after a delay
                setTimeout(() => {
                    fileInput.value = '';
                    progressDiv.classList.add('d-none');
                    bootstrap.Modal.getInstance(document.getElementById('bulkImportModal')).hide();
                }, 5000);
            })
            .catch(error => {
                console.error('Error importing payments:', error);
                statusDiv.innerHTML = '<strong class="text-danger">Import failed. Please check the file format and try again.</strong>';
            });
        }

    </script>
</body>
</html>
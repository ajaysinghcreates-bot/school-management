<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outstanding Fees - School Management</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .overdue { background-color: #f8d7da; }
        .due-soon { background-color: #fff3cd; }
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
                    <li class="nav-item"><a class="nav-link active" href="/cashier/outstanding">Outstanding</a></li>
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
            <h1>Outstanding Fees</h1>
            <div>
                <button class="btn btn-warning me-2" id="send-bulk-reminders">Send Bulk Reminders</button>
                <button class="btn btn-info me-2" id="send-automated-reminders">Send Automated Reminders</button>
                <button class="btn btn-primary" onclick="location.reload()">Refresh</button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Outstanding</h5>
                        <h3 id="total-outstanding">$0.00</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">Overdue</h5>
                        <h3 id="overdue-count">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Due This Week</h5>
                        <h3 id="due-this-week">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search -->
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" id="search" class="form-control" placeholder="Search by student name...">
            </div>
            <div class="col-md-3">
                <button id="search-btn" class="btn btn-secondary">Search</button>
            </div>
        </div>

        <!-- Outstanding Fees Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="outstanding-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>Student</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Days Overdue</th>
                                <th>Last Reminder</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="outstanding-tbody">
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
                <nav aria-label="Outstanding pagination">
                    <ul class="pagination justify-content-center" id="pagination">
                        <!-- Pagination will be generated via JS -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Reminder Modal -->
    <div class="modal fade" id="reminderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send Reminder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Send payment reminder to <strong id="reminder-student"></strong>?</p>
                    <p>Outstanding amount: <strong id="reminder-amount"></strong></p>
                    <p>Due date: <strong id="reminder-due-date"></strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="send-reminder-btn">Send Reminder</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPage = 1;
        let currentSearch = '';
        let selectedFeeId = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadOutstanding();
            document.getElementById('search-btn').addEventListener('click', function() {
                currentSearch = document.getElementById('search').value;
                currentPage = 1;
                loadOutstanding();
            });
            document.getElementById('select-all').addEventListener('change', toggleSelectAll);
            document.getElementById('send-bulk-reminders').addEventListener('click', sendBulkReminders);
            document.getElementById('send-automated-reminders').addEventListener('click', sendAutomatedReminders);
            document.getElementById('send-reminder-btn').addEventListener('click', sendReminder);
        });

        function loadOutstanding() {
            const params = new URLSearchParams({
                search: currentSearch,
                page: currentPage
            });

            fetch('/cashier/api/outstanding?' + params)
                .then(response => response.json())
                .then(data => {
                    renderOutstandingTable(data.data);
                    renderPagination(data.total, data.page, data.limit);
                    updateSummary(data.data);
                })
                .catch(error => console.error('Error loading outstanding fees:', error));
        }

        function renderOutstandingTable(fees) {
            const tbody = document.getElementById('outstanding-tbody');
            tbody.innerHTML = '';

            if (fees.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center">No outstanding fees found</td></tr>';
                return;
            }

            const today = new Date();
            fees.forEach(fee => {
                const dueDate = new Date(fee.due_date);
                const daysOverdue = Math.floor((today - dueDate) / (1000 * 60 * 60 * 24));
                const isOverdue = daysOverdue > 0;
                const isDueSoon = daysOverdue > -7 && daysOverdue <= 0;
                const rowClass = isOverdue ? 'overdue' : (isDueSoon ? 'due-soon' : '');

                const lastReminder = fee.last_reminder ? new Date(fee.last_reminder).toLocaleDateString() : 'Never';
                const reminderBadge = fee.last_reminder ?
                    (new Date(fee.last_reminder) > new Date(Date.now() - 7 * 24 * 60 * 60 * 1000) ? 'badge bg-success' : 'badge bg-secondary') : 'badge bg-warning';

                tbody.innerHTML += `
                    <tr class="${rowClass}">
                        <td><input type="checkbox" class="fee-checkbox" value="${fee.id}"></td>
                        <td>${fee.student_name}</td>
                        <td>${fee.description}</td>
                        <td>$${parseFloat(fee.amount).toFixed(2)}</td>
                        <td>${dueDate.toLocaleDateString()}</td>
                        <td>${isOverdue ? daysOverdue : '-'}</td>
                        <td><span class="${reminderBadge}">${lastReminder}</span></td>
                        <td>
                            <button class="btn btn-sm btn-warning me-2" onclick="openReminderModal(${fee.id}, '${fee.student_name}', ${fee.amount}, '${fee.due_date}')">Send Reminder</button>
                            <a href="/cashier/fees" class="btn btn-sm btn-success">Process Payment</a>
                        </td>
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
            loadOutstanding();
        }

        function updateSummary(fees) {
            let totalOutstanding = 0;
            let overdueCount = 0;
            let dueThisWeek = 0;
            const today = new Date();
            const weekFromNow = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000);

            fees.forEach(fee => {
                totalOutstanding += parseFloat(fee.amount);
                const dueDate = new Date(fee.due_date);
                if (dueDate < today) overdueCount++;
                if (dueDate >= today && dueDate <= weekFromNow) dueThisWeek++;
            });

            document.getElementById('total-outstanding').textContent = '$' + totalOutstanding.toFixed(2);
            document.getElementById('overdue-count').textContent = overdueCount;
            document.getElementById('due-this-week').textContent = dueThisWeek;
        }

        function toggleSelectAll() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.fee-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        }

        function openReminderModal(feeId, studentName, amount, dueDate) {
            selectedFeeId = feeId;
            document.getElementById('reminder-student').textContent = studentName;
            document.getElementById('reminder-amount').textContent = '$' + parseFloat(amount).toFixed(2);
            document.getElementById('reminder-due-date').textContent = new Date(dueDate).toLocaleDateString();
            new bootstrap.Modal(document.getElementById('reminderModal')).show();
        }

        function sendReminder() {
            if (!selectedFeeId) return;

            fetch('/cashier/api/outstanding/send-reminder?fee_id=' + selectedFeeId, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('reminderModal')).hide();
                    alert('Reminder sent successfully!');
                } else {
                    alert('Error sending reminder: ' + result.error);
                }
            })
            .catch(error => console.error('Error sending reminder:', error));
        }

        function sendBulkReminders() {
            const selectedFees = Array.from(document.querySelectorAll('.fee-checkbox:checked')).map(cb => cb.value);
            if (selectedFees.length === 0) {
                alert('Please select fees to send reminders for.');
                return;
            }

            if (confirm(`Send reminders for ${selectedFees.length} outstanding fees?`)) {
                // Send reminders for each selected fee
                let promises = selectedFees.map(feeId =>
                    fetch('/cashier/api/outstanding/send-reminder?fee_id=' + feeId, { method: 'POST' })
                    .then(response => response.json())
                );

                Promise.all(promises)
                    .then(results => {
                        const successCount = results.filter(r => r.success).length;
                        alert(`Reminders sent for ${successCount} out of ${selectedFees.length} fees.`);
                        loadOutstanding(); // Refresh the table to show updated reminder dates
                    })
                    .catch(error => console.error('Error sending bulk reminders:', error));
            }
        }

        function sendAutomatedReminders() {
            if (confirm('Send automated reminders to all overdue students who haven\'t been reminded in the last 7 days?')) {
                const button = document.getElementById('send-automated-reminders');
                const originalText = button.textContent;
                button.disabled = true;
                button.textContent = 'Sending...';

                fetch('/cashier/api/outstanding/send-automated-reminders', {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert(`Automated reminders sent successfully!\n${result.sent} reminders sent out of ${result.total} overdue fees.`);
                        loadOutstanding(); // Refresh the table
                    } else {
                        alert('Error sending automated reminders: ' + (result.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error sending automated reminders:', error);
                    alert('Error sending automated reminders. Please try again.');
                })
                .finally(() => {
                    button.disabled = false;
                    button.textContent = originalText;
                });
            }
        }
    </script>
</body>
</html>
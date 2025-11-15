<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fees Management - School Management</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-responsive { max-height: 70vh; overflow-y: auto; }
        .pagination { justify-content: center; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/admin/dashboard">School Management Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="/admin/students">Students</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/teachers">Teachers</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/classes">Classes</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/attendance">Attendance</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/exams">Exams</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/admin/fees">Fees</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/reports">Reports</a></li>
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
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">Add Fee</button>
        </div>

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="search" class="form-control" placeholder="Search student">
            </div>
            <div class="col-md-3">
                <select id="statusFilter" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="overdue">Overdue</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary" onclick="loadFees()">Filter</button>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="mb-3" id="bulkActions" style="display: none;">
            <select id="bulkStatus" class="form-select d-inline-block w-auto me-2">
                <option value="paid">Mark as Paid</option>
                <option value="pending">Mark as Pending</option>
                <option value="overdue">Mark as Overdue</option>
            </select>
            <button class="btn btn-warning" onclick="bulkUpdateFees()">Update Selected</button>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="feesTable">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Student</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="feesBody"></tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav aria-label="Fees pagination">
            <ul class="pagination" id="pagination"></ul>
        </nav>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Fee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="feeForm">
                        <input type="hidden" id="feeId">
                        <div class="mb-3">
                            <label for="student_id" class="form-label">Student</label>
                            <select class="form-select" id="student_id" required>
                                <!-- Students loaded -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" class="form-control" id="due_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status">
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="overdue">Overdue</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveFee()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPage = 1;

        document.addEventListener('DOMContentLoaded', function() {
            loadStudents();
            loadFees();
        });

        function loadStudents() {
            fetch('/admin/api/students')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('student_id');
                    data.data.forEach(student => {
                        const option = document.createElement('option');
                        option.value = student.id;
                        option.textContent = student.name;
                        select.appendChild(option);
                    });
                });
        }

        function loadFees(page = 1) {
            currentPage = page;
            const search = document.getElementById('search').value;
            const status = document.getElementById('statusFilter').value;

            fetch(`/admin/api/fees?search=${encodeURIComponent(search)}&status=${status}&page=${page}`)
                .then(response => response.json())
                .then(data => {
                    renderTable(data.data);
                    renderPagination(data.total, data.page, data.limit);
                });
        }

        function renderTable(fees) {
            const tbody = document.getElementById('feesBody');
            tbody.innerHTML = '';
            fees.forEach(fee => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><input type="checkbox" class="fee-checkbox" value="${fee.id}"></td>
                    <td>${fee.student_name}</td>
                    <td>${fee.amount}</td>
                    <td>${fee.description || ''}</td>
                    <td>${fee.due_date}</td>
                    <td><span class="badge bg-${fee.status === 'paid' ? 'success' : fee.status === 'pending' ? 'warning' : 'danger'}">${fee.status}</span></td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editFee(${fee.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteFee(${fee.id})">Delete</button>
                    </td>
                `;
                tbody.appendChild(row);
            });

            document.getElementById('selectAll').checked = false;
            updateBulkActions();
        }

        function renderPagination(total, page, limit) {
            const totalPages = Math.ceil(total / limit);
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';

            if (totalPages <= 1) return;

            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${page === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `<a class="page-link" href="#" onclick="loadFees(${page - 1})">Previous</a>`;
            pagination.appendChild(prevLi);

            for (let i = 1; i <= totalPages; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === page ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="loadFees(${i})">${i}</a>`;
                pagination.appendChild(li);
            }

            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${page === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `<a class="page-link" href="#" onclick="loadFees(${page + 1})">Next</a>`;
            pagination.appendChild(nextLi);
        }

        function editFee(id) {
            fetch('/admin/api/fees')
                .then(response => response.json())
                .then(data => {
                    const fee = data.data.find(f => f.id == id);
                    if (fee) {
                        document.getElementById('feeId').value = fee.id;
                        document.getElementById('student_id').value = fee.student_id;
                        document.getElementById('amount').value = fee.amount;
                        document.getElementById('description').value = fee.description;
                        document.getElementById('due_date').value = fee.due_date;
                        document.getElementById('status').value = fee.status;
                        document.getElementById('modalTitle').textContent = 'Edit Fee';
                        new bootstrap.Modal(document.getElementById('addModal')).show();
                    }
                });
        }

        function saveFee() {
            const id = document.getElementById('feeId').value;
            const data = {
                student_id: document.getElementById('student_id').value,
                amount: document.getElementById('amount').value,
                description: document.getElementById('description').value,
                due_date: document.getElementById('due_date').value,
                status: document.getElementById('status').value
            };

            const url = id ? '/admin/api/fees/update?id=' + id : '/admin/api/fees/create';
            const method = id ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addModal')).hide();
                    loadFees(currentPage);
                } else {
                    alert('Error: ' + result.error);
                }
            });
        }

        function deleteFee(id) {
            if (confirm('Are you sure you want to delete this fee record?')) {
                fetch('/admin/api/fees/delete?id=' + id, { method: 'DELETE' })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            loadFees(currentPage);
                        } else {
                            alert('Error: ' + result.error);
                        }
                    });
            }
        }

        function bulkUpdateFees() {
            const ids = Array.from(document.querySelectorAll('.fee-checkbox:checked')).map(cb => cb.value);
            const status = document.getElementById('bulkStatus').value;
            if (ids.length === 0) return;

            fetch('/admin/api/fees/bulk-update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ids, status })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    loadFees(currentPage);
                } else {
                    alert('Error: ' + result.error);
                }
            });
        }

        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.fee-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkActions();
        });

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('fee-checkbox')) {
                updateBulkActions();
            }
        });

        function updateBulkActions() {
            const checked = document.querySelectorAll('.fee-checkbox:checked');
            document.getElementById('bulkActions').style.display = checked.length > 0 ? 'block' : 'none';
        }
    </script>
</body>
</html>
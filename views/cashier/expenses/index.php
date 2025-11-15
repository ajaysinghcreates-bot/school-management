<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses Management - School Management</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .expense-row:hover { background-color: #f8f9fa; }
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
                    <li class="nav-item"><a class="nav-link" href="/cashier/reports">Reports</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/cashier/expenses">Expenses</a></li>
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
            <h1>Expenses Management</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#expenseModal">Add New Expense</button>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total This Month</h5>
                        <h3 id="monthly-total">$0.00</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total This Year</h5>
                        <h3 id="yearly-total">$0.00</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Average Monthly</h5>
                        <h3 id="average-monthly">$0.00</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="search" class="form-control" placeholder="Search expenses...">
            </div>
            <div class="col-md-3">
                <select id="category-filter" class="form-select">
                    <option value="">All Categories</option>
                    <option value="utilities">Utilities</option>
                    <option value="supplies">Supplies</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="salaries">Salaries</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="col-md-2">
                <button id="search-btn" class="btn btn-secondary">Search</button>
            </div>
        </div>

        <!-- Expenses Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="expenses-table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Category</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="expenses-tbody">
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
                <nav aria-label="Expenses pagination">
                    <ul class="pagination justify-content-center" id="pagination">
                        <!-- Pagination will be generated via JS -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Expense Modal -->
    <div class="modal fade" id="expenseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Add New Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="expense-form">
                        <input type="hidden" id="expense-id" name="id">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" name="description" required>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="utilities">Utilities</option>
                                <option value="supplies">Supplies</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="salaries">Salaries</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="expense-date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="expense-date" name="expense_date" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-expense-btn">Save Expense</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this expense?</p>
                    <p><strong id="delete-description"></strong></p>
                    <p>Amount: <strong id="delete-amount"></strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete-btn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPage = 1;
        let currentSearch = '';
        let currentCategory = '';
        let expenseToDelete = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadExpenses();
            document.getElementById('search-btn').addEventListener('click', function() {
                currentSearch = document.getElementById('search').value;
                currentCategory = document.getElementById('category-filter').value;
                currentPage = 1;
                loadExpenses();
            });
            document.getElementById('save-expense-btn').addEventListener('click', saveExpense);
            document.getElementById('confirm-delete-btn').addEventListener('click', confirmDelete);
        });

        function loadExpenses() {
            const params = new URLSearchParams({
                search: currentSearch,
                category: currentCategory,
                page: currentPage
            });

            fetch('/cashier/api/expenses?' + params)
                .then(response => response.json())
                .then(data => {
                    renderExpensesTable(data.data);
                    renderPagination(data.total, data.page, data.limit);
                    updateSummary(data.data);
                })
                .catch(error => console.error('Error loading expenses:', error));
        }

        function renderExpensesTable(expenses) {
            const tbody = document.getElementById('expenses-tbody');
            tbody.innerHTML = '';

            if (expenses.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">No expenses found</td></tr>';
                return;
            }

            expenses.forEach(expense => {
                tbody.innerHTML += `
                    <tr class="expense-row">
                        <td>${expense.description}</td>
                        <td>$${parseFloat(expense.amount).toFixed(2)}</td>
                        <td><span class="badge bg-secondary">${expense.category}</span></td>
                        <td>${new Date(expense.expense_date).toLocaleDateString()}</td>
                        <td>
                            <button class="btn btn-sm btn-warning me-2" onclick="editExpense(${expense.id}, '${expense.description}', ${expense.amount}, '${expense.category}', '${expense.expense_date}')">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteExpense(${expense.id}, '${expense.description}', ${expense.amount})">Delete</button>
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
            loadExpenses();
        }

        function updateSummary(expenses) {
            // This is a simplified calculation - in reality, you'd want to fetch aggregated data from the server
            const currentMonth = new Date().getMonth();
            const currentYear = new Date().getFullYear();

            let monthlyTotal = 0;
            let yearlyTotal = 0;

            expenses.forEach(expense => {
                const expenseDate = new Date(expense.expense_date);
                const amount = parseFloat(expense.amount);

                if (expenseDate.getFullYear() === currentYear) {
                    yearlyTotal += amount;
                    if (expenseDate.getMonth() === currentMonth) {
                        monthlyTotal += amount;
                    }
                }
            });

            const averageMonthly = yearlyTotal / 12;

            document.getElementById('monthly-total').textContent = '$' + monthlyTotal.toFixed(2);
            document.getElementById('yearly-total').textContent = '$' + yearlyTotal.toFixed(2);
            document.getElementById('average-monthly').textContent = '$' + averageMonthly.toFixed(2);
        }

        function editExpense(id, description, amount, category, date) {
            document.getElementById('modal-title').textContent = 'Edit Expense';
            document.getElementById('expense-id').value = id;
            document.getElementById('description').value = description;
            document.getElementById('amount').value = amount;
            document.getElementById('category').value = category;
            document.getElementById('expense-date').value = date.split('T')[0];
            new bootstrap.Modal(document.getElementById('expenseModal')).show();
        }

        function saveExpense() {
            const form = document.getElementById('expense-form');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            const isEdit = data.id ? true : false;

            const url = isEdit ? '/cashier/api/expenses/update?id=' + data.id : '/cashier/api/expenses/create';
            const method = isEdit ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('expenseModal')).hide();
                    form.reset();
                    document.getElementById('expense-id').value = '';
                    document.getElementById('modal-title').textContent = 'Add New Expense';
                    loadExpenses(); // Reload the expenses table
                } else {
                    alert('Error saving expense: ' + result.error);
                }
            })
            .catch(error => console.error('Error saving expense:', error));
        }

        function deleteExpense(id, description, amount) {
            expenseToDelete = id;
            document.getElementById('delete-description').textContent = description;
            document.getElementById('delete-amount').textContent = '$' + parseFloat(amount).toFixed(2);
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        function confirmDelete() {
            if (!expenseToDelete) return;

            fetch('/cashier/api/expenses/delete?id=' + expenseToDelete, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                    expenseToDelete = null;
                    loadExpenses(); // Reload the expenses table
                } else {
                    alert('Error deleting expense: ' + result.error);
                }
            })
            .catch(error => console.error('Error deleting expense:', error));
        }
    </script>
</body>
</html>
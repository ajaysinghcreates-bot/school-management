<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Categories - School Management</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .category-card { transition: transform 0.2s; cursor: pointer; }
        .category-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
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
                    <li class="nav-item"><a class="nav-link" href="/cashier/expenses">Expenses</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/cashier/expense-categories">Categories</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Expense Categories</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">Add Category</button>
        </div>

        <!-- Categories Grid -->
        <div class="row" id="categories-grid">
            <!-- Categories will be loaded via AJAX -->
        </div>

        <!-- Statistics -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Categories</h5>
                        <h2 id="total-categories">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Most Used</h5>
                        <h4 id="most-used-category">-</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Expenses</h5>
                        <h2 id="total-expenses">$0.00</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="category-form">
                        <input type="hidden" id="category-id">
                        <div class="mb-3">
                            <label for="category-name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="category-name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="category-description" class="form-label">Description (Optional)</label>
                            <textarea class="form-control" id="category-description" name="description" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-category-btn">Save Category</button>
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
                    <p>Are you sure you want to delete the category "<strong id="delete-category-name"></strong>"?</p>
                    <div class="alert alert-warning">
                        <strong>Warning:</strong> This action cannot be undone. Make sure no expenses are using this category.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete-btn">Delete Category</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        let categoryToDelete = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadCategories();
            document.getElementById('save-category-btn').addEventListener('click', saveCategory);
            document.getElementById('confirm-delete-btn').addEventListener('click', confirmDelete);
        });

        function loadCategories() {
            fetch('/cashier/api/expense-categories')
                .then(response => response.json())
                .then(data => {
                    renderCategories(data.data);
                    updateStatistics(data.data);
                })
                .catch(error => console.error('Error loading categories:', error));
        }

        function renderCategories(categories) {
            const grid = document.getElementById('categories-grid');
            grid.innerHTML = '';

            if (categories.length === 0) {
                grid.innerHTML = '<div class="col-12"><div class="alert alert-info text-center">No expense categories found. Create your first category!</div></div>';
                return;
            }

            categories.forEach(category => {
                const col = document.createElement('div');
                col.className = 'col-md-4 mb-3';
                col.innerHTML = `
                    <div class="card category-card h-100" onclick="editCategory(${category.id}, '${category.name}', '${category.description || ''}')">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">${category.name}</h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" onclick="event.stopPropagation(); toggleDropdown(${category.id})">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu" id="dropdown-${category.id}" style="display: none;">
                                        <li><a class="dropdown-item" href="#" onclick="event.stopPropagation(); editCategory(${category.id}, '${category.name}', '${category.description || ''}')">Edit</a></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="event.stopPropagation(); deleteCategory(${category.id}, '${category.name}')">Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                            <p class="card-text flex-grow-1">${category.description || 'No description'}</p>
                            <div class="mt-auto">
                                <small class="text-muted">ID: ${category.id}</small>
                            </div>
                        </div>
                    </div>
                `;
                grid.appendChild(col);
            });
        }

        function updateStatistics(categories) {
            document.getElementById('total-categories').textContent = categories.length;

            // This would ideally fetch usage statistics from the server
            // For now, just show basic stats
            document.getElementById('most-used-category').textContent = categories.length > 0 ? categories[0].name : '-';
            document.getElementById('total-expenses').textContent = '$0.00'; // Would need API call
        }

        function editCategory(id, name, description) {
            document.getElementById('modal-title').textContent = 'Edit Category';
            document.getElementById('category-id').value = id;
            document.getElementById('category-name').value = name;
            document.getElementById('category-description').value = description;
            new bootstrap.Modal(document.getElementById('categoryModal')).show();
        }

        function saveCategory() {
            const form = document.getElementById('category-form');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            const isEdit = data.id ? true : false;

            const url = isEdit ? '/cashier/api/expense-categories/update?id=' + data.id : '/cashier/api/expense-categories/create';
            const method = isEdit ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
                    form.reset();
                    document.getElementById('category-id').value = '';
                    document.getElementById('modal-title').textContent = 'Add Category';
                    loadCategories();
                } else {
                    alert('Error saving category: ' + result.error);
                }
            })
            .catch(error => console.error('Error saving category:', error));
        }

        function deleteCategory(id, name) {
            categoryToDelete = id;
            document.getElementById('delete-category-name').textContent = name;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        function confirmDelete() {
            if (!categoryToDelete) return;

            fetch('/cashier/api/expense-categories/delete?id=' + categoryToDelete, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                    categoryToDelete = null;
                    loadCategories();
                } else {
                    alert('Error deleting category: ' + result.error);
                }
            })
            .catch(error => console.error('Error deleting category:', error));
        }

        function toggleDropdown(id) {
            const dropdown = document.getElementById('dropdown-' + id);
            // Hide all other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu.id !== 'dropdown-' + id) menu.style.display = 'none';
            });
            // Toggle current dropdown
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.style.display = 'none';
                });
            }
        });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Management - School Management</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-responsive { max-height: 70vh; overflow-y: auto; }
        .pagination { justify-content: center; }
        .gallery-img { width: 50px; height: 50px; object-fit: cover; }
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
                    <li class="nav-item"><a class="nav-link" href="/admin/fees">Fees</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/events">Events</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/admin/gallery">Gallery</a></li>
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
            <h1>Gallery Management</h1>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">Add Image</button>
        </div>

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="search" class="form-control" placeholder="Search gallery">
            </div>
            <div class="col-md-3">
                <select id="categoryFilter" class="form-select">
                    <option value="">All Categories</option>
                    <option value="events">Events</option>
                    <option value="students">Students</option>
                    <option value="teachers">Teachers</option>
                    <option value="facilities">Facilities</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary" onclick="loadGallery()">Filter</button>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="galleryTable">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="galleryBody"></tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav aria-label="Gallery pagination">
            <ul class="pagination" id="pagination"></ul>
        </nav>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="galleryForm">
                        <input type="hidden" id="galleryId">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image_path" class="form-label">Image Path</label>
                            <input type="text" class="form-control" id="image_path" placeholder="/uploads/image.jpg" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category">
                                <option value="events">Events</option>
                                <option value="students">Students</option>
                                <option value="teachers">Teachers</option>
                                <option value="facilities">Facilities</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveGallery()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPage = 1;

        document.addEventListener('DOMContentLoaded', function() {
            loadGallery();
        });

        function loadGallery(page = 1) {
            currentPage = page;
            const search = document.getElementById('search').value;
            const category = document.getElementById('categoryFilter').value;

            fetch(`/admin/api/gallery?search=${encodeURIComponent(search)}&category=${category}&page=${page}`)
                .then(response => response.json())
                .then(data => {
                    renderTable(data.data);
                    renderPagination(data.total, data.page, data.limit);
                });
        }

        function renderTable(gallery) {
            const tbody = document.getElementById('galleryBody');
            tbody.innerHTML = '';
            gallery.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><img src="${item.image_path}" alt="${item.title}" class="gallery-img"></td>
                    <td>${item.title}</td>
                    <td><span class="badge bg-secondary">${item.category}</span></td>
                    <td>${item.created_at}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editGallery(${item.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteGallery(${item.id})">Delete</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function renderPagination(total, page, limit) {
            const totalPages = Math.ceil(total / limit);
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';

            if (totalPages <= 1) return;

            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${page === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `<a class="page-link" href="#" onclick="loadGallery(${page - 1})">Previous</a>`;
            pagination.appendChild(prevLi);

            for (let i = 1; i <= totalPages; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === page ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="loadGallery(${i})">${i}</a>`;
                pagination.appendChild(li);
            }

            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${page === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `<a class="page-link" href="#" onclick="loadGallery(${page + 1})">Next</a>`;
            pagination.appendChild(nextLi);
        }

        function editGallery(id) {
            fetch('/admin/api/gallery')
                .then(response => response.json())
                .then(data => {
                    const item = data.data.find(g => g.id == id);
                    if (item) {
                        document.getElementById('galleryId').value = item.id;
                        document.getElementById('title').value = item.title;
                        document.getElementById('description').value = item.description;
                        document.getElementById('image_path').value = item.image_path;
                        document.getElementById('category').value = item.category;
                        document.getElementById('modalTitle').textContent = 'Edit Image';
                        new bootstrap.Modal(document.getElementById('addModal')).show();
                    }
                });
        }

        function saveGallery() {
            const id = document.getElementById('galleryId').value;
            const data = {
                title: document.getElementById('title').value,
                description: document.getElementById('description').value,
                image_path: document.getElementById('image_path').value,
                category: document.getElementById('category').value
            };

            const url = id ? '/admin/api/gallery/update?id=' + id : '/admin/api/gallery/create';
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
                    loadGallery(currentPage);
                } else {
                    alert('Error: ' + result.error);
                }
            });
        }

        function deleteGallery(id) {
            if (confirm('Are you sure you want to delete this image?')) {
                fetch('/admin/api/gallery/delete?id=' + id, { method: 'DELETE' })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            loadGallery(currentPage);
                        } else {
                            alert('Error: ' + result.error);
                        }
                    });
            }
        }
    </script>
</body>
</html>
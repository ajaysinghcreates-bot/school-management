<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage Management - Admin Panel</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/admin/dashboard">School Admin</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/admin/dashboard">Dashboard</a>
                <a class="nav-link active" href="/admin/homepage">Homepage</a>
                <a class="nav-link" href="/admin/students">Students</a>
                <a class="nav-link" href="/admin/teachers">Teachers</a>
                <a class="nav-link" href="/logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Homepage Content Management</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addContentModal">
                <i class="fas fa-plus"></i> Add Content
            </button>
        </div>

        <!-- Section Tabs -->
        <ul class="nav nav-tabs mb-4" id="sectionTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">All Content</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="hero-tab" data-bs-toggle="tab" data-bs-target="#hero" type="button" role="tab">Hero</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="about-tab" data-bs-toggle="tab" data-bs-target="#about" type="button" role="tab">About</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses" type="button" role="tab">Courses</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events" type="button" role="tab">Events</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="achievements-tab" data-bs-toggle="tab" data-bs-target="#achievements" type="button" role="tab">Achievements</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="gallery-tab" data-bs-toggle="tab" data-bs-target="#gallery" type="button" role="tab">Gallery</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="testimonials-tab" data-bs-toggle="tab" data-bs-target="#testimonials" type="button" role="tab">Testimonials</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cta-tab" data-bs-toggle="tab" data-bs-target="#cta" type="button" role="tab">Call to Action</button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="sectionTabsContent">
            <div class="tab-pane fade show active" id="all" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5>All Homepage Content</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="allContentTable">
                                <thead>
                                    <tr>
                                        <th>Section</th>
                                        <th>Title</th>
                                        <th>Content</th>
                                        <th>Order</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Individual section tabs will be populated dynamically -->
            <div class="tab-pane fade" id="hero" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5>Hero Section Content</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="heroTable">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Content</th>
                                        <th>Order</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Similar structure for other sections -->
            <div class="tab-pane fade" id="about" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5>About Section Content</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="aboutTable">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Content</th>
                                        <th>Order</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="courses" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5>Courses Section Content</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="coursesTable">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Content</th>
                                        <th>Order</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="events" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5>Events Section Content</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="eventsTable">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Content</th>
                                        <th>Order</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="achievements" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5>Achievements Section Content</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="achievementsTable">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Content</th>
                                        <th>Order</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="gallery" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5>Gallery Section Content</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="galleryTable">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Content</th>
                                        <th>Order</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="testimonials" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5>Testimonials Section Content</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="testimonialsTable">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="cta" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5>Call to Action Section Content</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="ctaTable">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Content</th>
                                        <th>Order</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Content Modal -->
    <div class="modal fade" id="addContentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Homepage Content</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addContentForm">
                        <div class="mb-3">
                            <label for="section" class="form-label">Section</label>
                            <select class="form-select" id="section" name="section" required>
                                <option value="hero">Hero</option>
                                <option value="about">About</option>
                                <option value="courses">Courses</option>
                                <option value="events">Events</option>
                                <option value="achievements">Achievements</option>
                                <option value="gallery">Gallery</option>
                                <option value="testimonials">Testimonials</option>
                                <option value="cta">Call to Action</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control" id="content" name="content" rows="4"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image_path" class="form-label">Image Path</label>
                            <input type="text" class="form-control" id="image_path" name="image_path" placeholder="/uploads/image.jpg">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="link_url" class="form-label">Link URL</label>
                                    <input type="text" class="form-control" id="link_url" name="link_url" placeholder="/page">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="link_text" class="form-label">Link Text</label>
                                    <input type="text" class="form-control" id="link_text" name="link_text" placeholder="Learn More">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="display_order" class="form-label">Display Order</label>
                                    <input type="number" class="form-control" id="display_order" name="display_order" value="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="is_active" class="form-label">Status</label>
                                    <select class="form-select" id="is_active" name="is_active">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveContentBtn">Save Content</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Content Modal -->
    <div class="modal fade" id="editContentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Homepage Content</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editContentForm">
                        <input type="hidden" id="edit_content_id" name="id">
                        <div class="mb-3">
                            <label for="edit_section" class="form-label">Section</label>
                            <select class="form-select" id="edit_section" name="section" required>
                                <option value="hero">Hero</option>
                                <option value="about">About</option>
                                <option value="courses">Courses</option>
                                <option value="events">Events</option>
                                <option value="achievements">Achievements</option>
                                <option value="gallery">Gallery</option>
                                <option value="testimonials">Testimonials</option>
                                <option value="cta">Call to Action</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_content" class="form-label">Content</label>
                            <textarea class="form-control" id="edit_content" name="content" rows="4"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_image_path" class="form-label">Image Path</label>
                            <input type="text" class="form-control" id="edit_image_path" name="image_path" placeholder="/uploads/image.jpg">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_link_url" class="form-label">Link URL</label>
                                    <input type="text" class="form-control" id="edit_link_url" name="link_url" placeholder="/page">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_link_text" class="form-label">Link Text</label>
                                    <input type="text" class="form-control" id="edit_link_text" name="link_text" placeholder="Learn More">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_display_order" class="form-label">Display Order</label>
                                    <input type="number" class="form-control" id="edit_display_order" name="display_order" value="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_is_active" class="form-label">Status</label>
                                    <select class="form-select" id="edit_is_active" name="is_active">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="updateContentBtn">Update Content</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentSection = 'all';

        document.addEventListener('DOMContentLoaded', function() {
            loadContent('all');

            // Tab change event
            document.querySelectorAll('#sectionTabs .nav-link').forEach(tab => {
                tab.addEventListener('click', function() {
                    currentSection = this.id.replace('-tab', '');
                    loadContent(currentSection);
                });
            });

            // Save new content
            document.getElementById('saveContentBtn').addEventListener('click', saveContent);

            // Update content
            document.getElementById('updateContentBtn').addEventListener('click', updateContent);
        });

        function loadContent(section) {
            const url = section === 'all' ? '/admin/api/homepage' : `/admin/api/homepage?section=${section}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (section === 'all') {
                        populateTable('allContentTable', data.data);
                    } else {
                        populateTable(`${section}Table`, data.data);
                    }
                })
                .catch(error => console.error('Error loading content:', error));
        }

        function populateTable(tableId, data) {
            const tbody = document.querySelector(`#${tableId} tbody`);
            tbody.innerHTML = '';

            data.forEach(item => {
                const row = document.createElement('tr');
                if (tableId === 'allContentTable') {
                    row.innerHTML = `
                        <td>${item.section}</td>
                        <td>${item.title}</td>
                        <td>${item.content ? item.content.substring(0, 50) + '...' : ''}</td>
                        <td>${item.display_order}</td>
                        <td><span class="badge bg-${item.is_active ? 'success' : 'secondary'}">${item.is_active ? 'Active' : 'Inactive'}</span></td>
                        <td>
                            <button class="btn btn-sm btn-primary me-1" onclick="editContent(${item.id})">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteContent(${item.id})">Delete</button>
                        </td>
                    `;
                } else {
                    row.innerHTML = `
                        <td>${item.title}</td>
                        <td>${item.content ? item.content.substring(0, 50) + '...' : ''}</td>
                        <td>${item.display_order}</td>
                        <td><span class="badge bg-${item.is_active ? 'success' : 'secondary'}">${item.is_active ? 'Active' : 'Inactive'}</span></td>
                        <td>
                            <button class="btn btn-sm btn-primary me-1" onclick="editContent(${item.id})">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteContent(${item.id})">Delete</button>
                        </td>
                    `;
                }
                tbody.appendChild(row);
            });
        }

        function saveContent() {
            const form = document.getElementById('addContentForm');
            const formData = new FormData(form);

            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }

            fetch('/admin/api/homepage/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addContentModal')).hide();
                    form.reset();
                    loadContent(currentSection);
                } else {
                    alert('Error: ' + result.error);
                }
            })
            .catch(error => console.error('Error saving content:', error));
        }

        function editContent(id) {
            fetch(`/admin/api/homepage`)
                .then(response => response.json())
                .then(data => {
                    const content = data.data.find(item => item.id == id);
                    if (content) {
                        document.getElementById('edit_content_id').value = content.id;
                        document.getElementById('edit_section').value = content.section;
                        document.getElementById('edit_title').value = content.title;
                        document.getElementById('edit_content').value = content.content || '';
                        document.getElementById('edit_image_path').value = content.image_path || '';
                        document.getElementById('edit_link_url').value = content.link_url || '';
                        document.getElementById('edit_link_text').value = content.link_text || '';
                        document.getElementById('edit_display_order').value = content.display_order;
                        document.getElementById('edit_is_active').value = content.is_active;

                        new bootstrap.Modal(document.getElementById('editContentModal')).show();
                    }
                })
                .catch(error => console.error('Error loading content for edit:', error));
        }

        function updateContent() {
            const form = document.getElementById('editContentForm');
            const formData = new FormData(form);
            const id = document.getElementById('edit_content_id').value;

            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }

            fetch(`/admin/api/homepage/update?id=${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('editContentModal')).hide();
                    loadContent(currentSection);
                } else {
                    alert('Error: ' + result.error);
                }
            })
            .catch(error => console.error('Error updating content:', error));
        }

        function deleteContent(id) {
            if (confirm('Are you sure you want to delete this content?')) {
                fetch(`/admin/api/homepage/delete?id=${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        loadContent(currentSection);
                    } else {
                        alert('Error: ' + result.error);
                    }
                })
                .catch(error => console.error('Error deleting content:', error));
            }
        }
    </script>
</body>
</html>
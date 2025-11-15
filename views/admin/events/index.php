<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events & Announcements Management - School Management</title>
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
                    <li class="nav-item"><a class="nav-link" href="/admin/fees">Fees</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/admin/events">Events</a></li>
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
            <h1>Events & Announcements Management</h1>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">Add Event</button>
        </div>

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="search" class="form-control" placeholder="Search events">
            </div>
            <div class="col-md-3">
                <select id="typeFilter" class="form-select">
                    <option value="">All Types</option>
                    <option value="event">Event</option>
                    <option value="announcement">Announcement</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary" onclick="loadEvents()">Filter</button>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="eventsTable">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Event Date</th>
                        <th>Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="eventsBody"></tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav aria-label="Events pagination">
            <ul class="pagination" id="pagination"></ul>
        </nav>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="eventForm">
                        <input type="hidden" id="eventId">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="event_date" class="form-label">Event Date</label>
                            <input type="date" class="form-control" id="event_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" id="type">
                                <option value="event">Event</option>
                                <option value="announcement">Announcement</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveEvent()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPage = 1;

        document.addEventListener('DOMContentLoaded', function() {
            loadEvents();
        });

        function loadEvents(page = 1) {
            currentPage = page;
            const search = document.getElementById('search').value;
            const type = document.getElementById('typeFilter').value;

            fetch(`/admin/api/events?search=${encodeURIComponent(search)}&type=${type}&page=${page}`)
                .then(response => response.json())
                .then(data => {
                    renderTable(data.data);
                    renderPagination(data.total, data.page, data.limit);
                });
        }

        function renderTable(events) {
            const tbody = document.getElementById('eventsBody');
            tbody.innerHTML = '';
            events.forEach(event => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${event.title}</td>
                    <td><span class="badge bg-${event.type === 'event' ? 'primary' : 'info'}">${event.type}</span></td>
                    <td>${event.event_date}</td>
                    <td>${event.location || ''}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editEvent(${event.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteEvent(${event.id})">Delete</button>
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
            prevLi.innerHTML = `<a class="page-link" href="#" onclick="loadEvents(${page - 1})">Previous</a>`;
            pagination.appendChild(prevLi);

            for (let i = 1; i <= totalPages; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === page ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="loadEvents(${i})">${i}</a>`;
                pagination.appendChild(li);
            }

            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${page === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `<a class="page-link" href="#" onclick="loadEvents(${page + 1})">Next</a>`;
            pagination.appendChild(nextLi);
        }

        function editEvent(id) {
            fetch('/admin/api/events')
                .then(response => response.json())
                .then(data => {
                    const event = data.data.find(e => e.id == id);
                    if (event) {
                        document.getElementById('eventId').value = event.id;
                        document.getElementById('title').value = event.title;
                        document.getElementById('description').value = event.description;
                        document.getElementById('event_date').value = event.event_date;
                        document.getElementById('type').value = event.type;
                        document.getElementById('location').value = event.location;
                        document.getElementById('modalTitle').textContent = 'Edit Event';
                        new bootstrap.Modal(document.getElementById('addModal')).show();
                    }
                });
        }

        function saveEvent() {
            const id = document.getElementById('eventId').value;
            const data = {
                title: document.getElementById('title').value,
                description: document.getElementById('description').value,
                event_date: document.getElementById('event_date').value,
                type: document.getElementById('type').value,
                location: document.getElementById('location').value
            };

            const url = id ? '/admin/api/events/update?id=' + id : '/admin/api/events/create';
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
                    loadEvents(currentPage);
                } else {
                    alert('Error: ' + result.error);
                }
            });
        }

        function deleteEvent(id) {
            if (confirm('Are you sure you want to delete this event?')) {
                fetch('/admin/api/events/delete?id=' + id, { method: 'DELETE' })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            loadEvents(currentPage);
                        } else {
                            alert('Error: ' + result.error);
                        }
                    });
            }
        }
    </script>
</body>
</html>
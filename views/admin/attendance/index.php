<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management - School Management</title>
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
                    <li class="nav-item"><a class="nav-link active" href="/admin/attendance">Attendance</a></li>
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
            <h1>Attendance Management</h1>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">Mark Attendance</button>
        </div>

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-3">
                <input type="text" id="search" class="form-control" placeholder="Search student">
            </div>
            <div class="col-md-2">
                <input type="date" id="dateFilter" class="form-control">
            </div>
            <div class="col-md-2">
                <select id="classFilter" class="form-select">
                    <option value="">All Classes</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary" onclick="loadAttendance()">Filter</button>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="attendanceTable">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="attendanceBody"></tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav aria-label="Attendance pagination">
            <ul class="pagination" id="pagination"></ul>
        </nav>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Mark Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="attendanceForm">
                        <input type="hidden" id="attendanceId">
                        <div class="mb-3">
                            <label for="student_id" class="form-label">Student</label>
                            <select class="form-select" id="student_id" required>
                                <!-- Students loaded -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status">
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="late">Late</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveAttendance()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPage = 1;

        document.addEventListener('DOMContentLoaded', function() {
            loadClasses();
            loadStudents();
            loadAttendance();
        });

        function loadClasses() {
            fetch('/admin/api/classes')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('classFilter');
                    data.data.forEach(cls => {
                        const option = document.createElement('option');
                        option.value = cls.id;
                        option.textContent = cls.name;
                        select.appendChild(option);
                    });
                });
        }

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

        function loadAttendance(page = 1) {
            currentPage = page;
            const search = document.getElementById('search').value;
            const date = document.getElementById('dateFilter').value;
            const classId = document.getElementById('classFilter').value;

            fetch(`/admin/api/attendance?search=${encodeURIComponent(search)}&date=${date}&class_id=${classId}&page=${page}`)
                .then(response => response.json())
                .then(data => {
                    renderTable(data.data);
                    renderPagination(data.total, data.page, data.limit);
                });
        }

        function renderTable(attendance) {
            const tbody = document.getElementById('attendanceBody');
            tbody.innerHTML = '';
            attendance.forEach(att => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${att.student_name}</td>
                    <td>${att.class_name || ''}</td>
                    <td>${att.date}</td>
                    <td><span class="badge bg-${att.status === 'present' ? 'success' : att.status === 'absent' ? 'danger' : 'warning'}">${att.status}</span></td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editAttendance(${att.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteAttendance(${att.id})">Delete</button>
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
            prevLi.innerHTML = `<a class="page-link" href="#" onclick="loadAttendance(${page - 1})">Previous</a>`;
            pagination.appendChild(prevLi);

            for (let i = 1; i <= totalPages; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === page ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="loadAttendance(${i})">${i}</a>`;
                pagination.appendChild(li);
            }

            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${page === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `<a class="page-link" href="#" onclick="loadAttendance(${page + 1})">Next</a>`;
            pagination.appendChild(nextLi);
        }

        function editAttendance(id) {
            fetch('/admin/api/attendance')
                .then(response => response.json())
                .then(data => {
                    const att = data.data.find(a => a.id == id);
                    if (att) {
                        document.getElementById('attendanceId').value = att.id;
                        document.getElementById('student_id').value = att.student_id;
                        document.getElementById('date').value = att.date;
                        document.getElementById('status').value = att.status;
                        document.getElementById('modalTitle').textContent = 'Edit Attendance';
                        new bootstrap.Modal(document.getElementById('addModal')).show();
                    }
                });
        }

        function saveAttendance() {
            const id = document.getElementById('attendanceId').value;
            const data = {
                student_id: document.getElementById('student_id').value,
                date: document.getElementById('date').value,
                status: document.getElementById('status').value
            };

            const url = id ? '/admin/api/attendance/update?id=' + id : '/admin/api/attendance/create';
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
                    loadAttendance(currentPage);
                } else {
                    alert('Error: ' + result.error);
                }
            });
        }

        function deleteAttendance(id) {
            if (confirm('Are you sure you want to delete this attendance record?')) {
                fetch('/admin/api/attendance/delete?id=' + id, { method: 'DELETE' })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            loadAttendance(currentPage);
                        } else {
                            alert('Error: ' + result.error);
                        }
                    });
            }
        }
    </script>
</body>
</html>
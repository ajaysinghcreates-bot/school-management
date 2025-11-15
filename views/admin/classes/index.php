<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classes & Subjects Management - School Management</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-responsive { max-height: 60vh; overflow-y: auto; }
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
                    <li class="nav-item"><a class="nav-link active" href="/admin/classes">Classes</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/attendance">Attendance</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/reports">Reports</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <h1>Classes & Subjects Management</h1>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="classes-tab" data-bs-toggle="tab" data-bs-target="#classes" type="button" role="tab">Classes</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="subjects-tab" data-bs-toggle="tab" data-bs-target="#subjects" type="button" role="tab">Subjects</button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- Classes Tab -->
            <div class="tab-pane fade show active" id="classes" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center my-4">
                    <h2>Classes</h2>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#classModal">Add Class</button>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" id="classSearch" class="form-control" placeholder="Search classes">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary" onclick="loadClasses()">Filter</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="classesBody"></tbody>
                    </table>
                </div>

                <nav aria-label="Classes pagination">
                    <ul class="pagination" id="classesPagination"></ul>
                </nav>
            </div>

            <!-- Subjects Tab -->
            <div class="tab-pane fade" id="subjects" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center my-4">
                    <h2>Subjects</h2>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#subjectModal">Add Subject</button>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" id="subjectSearch" class="form-control" placeholder="Search subjects">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary" onclick="loadSubjects()">Filter</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Class</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="subjectsBody"></tbody>
                    </table>
                </div>

                <nav aria-label="Subjects pagination">
                    <ul class="pagination" id="subjectsPagination"></ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Class Modal -->
    <div class="modal fade" id="classModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="classId">
                    <div class="mb-3">
                        <label for="className" class="form-label">Name</label>
                        <input type="text" class="form-control" id="className" required>
                    </div>
                    <div class="mb-3">
                        <label for="classDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="classDescription"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveClass()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject Modal -->
    <div class="modal fade" id="subjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="subjectId">
                    <div class="mb-3">
                        <label for="subjectName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="subjectName" required>
                    </div>
                    <div class="mb-3">
                        <label for="subjectDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="subjectDescription"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="subjectClassId" class="form-label">Class</label>
                        <select class="form-select" id="subjectClassId" required>
                            <!-- Classes loaded -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveSubject()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadClasses();
            loadSubjects();
            loadClassesForSelect();
        });

        function loadClasses(page = 1) {
            const search = document.getElementById('classSearch').value;
            fetch(`/admin/api/classes?search=${encodeURIComponent(search)}&page=${page}`)
                .then(response => response.json())
                .then(data => {
                    renderClasses(data.data);
                    renderPagination('classes', data.total, data.page, data.limit);
                });
        }

        function loadSubjects(page = 1) {
            const search = document.getElementById('subjectSearch').value;
            fetch(`/admin/api/subjects?search=${encodeURIComponent(search)}&page=${page}`)
                .then(response => response.json())
                .then(data => {
                    renderSubjects(data.data);
                    renderPagination('subjects', data.total, data.page, data.limit);
                });
        }

        function loadClassesForSelect() {
            fetch('/admin/api/classes')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('subjectClassId');
                    data.data.forEach(cls => {
                        const option = document.createElement('option');
                        option.value = cls.id;
                        option.textContent = cls.name;
                        select.appendChild(option);
                    });
                });
        }

        function renderClasses(classes) {
            const tbody = document.getElementById('classesBody');
            tbody.innerHTML = '';
            classes.forEach(cls => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${cls.name}</td>
                    <td>${cls.description || ''}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editClass(${cls.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteClass(${cls.id})">Delete</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function renderSubjects(subjects) {
            const tbody = document.getElementById('subjectsBody');
            tbody.innerHTML = '';
            subjects.forEach(sub => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${sub.name}</td>
                    <td>${sub.description || ''}</td>
                    <td>${sub.class_name || ''}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editSubject(${sub.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteSubject(${sub.id})">Delete</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function renderPagination(type, total, page, limit) {
            const totalPages = Math.ceil(total / limit);
            const pagination = document.getElementById(type + 'Pagination');
            pagination.innerHTML = '';

            if (totalPages <= 1) return;

            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${page === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `<a class="page-link" href="#" onclick="load${type.charAt(0).toUpperCase() + type.slice(1)}(${page - 1})">Previous</a>`;
            pagination.appendChild(prevLi);

            for (let i = 1; i <= totalPages; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === page ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="load${type.charAt(0).toUpperCase() + type.slice(1)}(${i})">${i}</a>`;
                pagination.appendChild(li);
            }

            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${page === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `<a class="page-link" href="#" onclick="load${type.charAt(0).toUpperCase() + type.slice(1)}(${page + 1})">Next</a>`;
            pagination.appendChild(nextLi);
        }

        function editClass(id) {
            fetch('/admin/api/classes')
                .then(response => response.json())
                .then(data => {
                    const cls = data.data.find(c => c.id == id);
                    if (cls) {
                        document.getElementById('classId').value = cls.id;
                        document.getElementById('className').value = cls.name;
                        document.getElementById('classDescription').value = cls.description;
                        new bootstrap.Modal(document.getElementById('classModal')).show();
                    }
                });
        }

        function saveClass() {
            const id = document.getElementById('classId').value;
            const data = {
                name: document.getElementById('className').value,
                description: document.getElementById('classDescription').value
            };

            const url = id ? '/admin/api/classes/update?id=' + id : '/admin/api/classes/create';
            const method = id ? 'PUT' : 'POST';

            fetch(url, { method, headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        bootstrap.Modal.getInstance(document.getElementById('classModal')).hide();
                        loadClasses();
                        loadClassesForSelect();
                    } else {
                        alert('Error: ' + result.error);
                    }
                });
        }

        function deleteClass(id) {
            if (confirm('Delete this class?')) {
                fetch('/admin/api/classes/delete?id=' + id, { method: 'DELETE' })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            loadClasses();
                            loadClassesForSelect();
                        } else {
                            alert('Error: ' + result.error);
                        }
                    });
            }
        }

        function editSubject(id) {
            fetch('/admin/api/subjects')
                .then(response => response.json())
                .then(data => {
                    const sub = data.data.find(s => s.id == id);
                    if (sub) {
                        document.getElementById('subjectId').value = sub.id;
                        document.getElementById('subjectName').value = sub.name;
                        document.getElementById('subjectDescription').value = sub.description;
                        document.getElementById('subjectClassId').value = sub.class_id;
                        new bootstrap.Modal(document.getElementById('subjectModal')).show();
                    }
                });
        }

        function saveSubject() {
            const id = document.getElementById('subjectId').value;
            const data = {
                name: document.getElementById('subjectName').value,
                description: document.getElementById('subjectDescription').value,
                class_id: document.getElementById('subjectClassId').value
            };

            const url = id ? '/admin/api/subjects/update?id=' + id : '/admin/api/subjects/create';
            const method = id ? 'PUT' : 'POST';

            fetch(url, { method, headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        bootstrap.Modal.getInstance(document.getElementById('subjectModal')).hide();
                        loadSubjects();
                    } else {
                        alert('Error: ' + result.error);
                    }
                });
        }

        function deleteSubject(id) {
            if (confirm('Delete this subject?')) {
                fetch('/admin/api/subjects/delete?id=' + id, { method: 'DELETE' })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            loadSubjects();
                        } else {
                            alert('Error: ' + result.error);
                        }
                    });
            }
        }
    </script>
</body>
</html>
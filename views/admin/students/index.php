<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students Management - School Management</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .table-responsive { max-height: 70vh; overflow-y: auto; }
        .pagination { justify-content: center; }
        .badge { font-size: 0.75em; }
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
            <h1>Students Management</h1>
            <div>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">Add Student</button>
                <button class="btn btn-info" onclick="exportStudents()">Export CSV</button>
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#importModal">Bulk Import</button>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="search" class="form-control" placeholder="Search by name, email, phone">
            </div>
            <div class="col-md-3">
                <select id="classFilter" class="form-select">
                    <option value="">All Classes</option>
                    <!-- Classes will be loaded dynamically -->
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary" onclick="loadStudents()">Filter</button>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="mb-3" id="bulkActions" style="display: none;">
            <button class="btn btn-danger" onclick="bulkDelete()">Delete Selected</button>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="studentsTable">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Name</th>
                        <th>Admission No</th>
                        <th>Roll No</th>
                        <th>Class</th>
                        <th>Phone</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="studentsBody">
                    <!-- Data will be loaded here -->
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav aria-label="Students pagination">
            <ul class="pagination" id="pagination">
                <!-- Pagination will be loaded here -->
            </ul>
        </nav>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="studentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">Personal Details</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="family-tab" data-bs-toggle="tab" data-bs-target="#family" type="button" role="tab">Family Information</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="medical-tab" data-bs-toggle="tab" data-bs-target="#medical" type="button" role="tab">Medical Information</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="academic-tab" data-bs-toggle="tab" data-bs-target="#academic" type="button" role="tab">Academic Details</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="banking-tab" data-bs-toggle="tab" data-bs-target="#banking" type="button" role="tab">Banking & Documents</button>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content mt-3" id="studentTabContent">
                        <!-- Personal Details Tab -->
                        <div class="tab-pane fade show active" id="personal" role="tabpanel">
                            <form id="studentForm">
                                <input type="hidden" id="studentId">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name *</label>
                                            <input type="text" class="form-control" id="name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email *</label>
                                            <input type="email" class="form-control" id="email" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="text" class="form-control" id="phone">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="dob" class="form-label">Date of Birth</label>
                                            <input type="date" class="form-control" id="dob">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="gender" class="form-label">Gender</label>
                                            <select class="form-select" id="gender">
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="blood_group" class="form-label">Blood Group</label>
                                            <select class="form-select" id="blood_group">
                                                <option value="">Select Blood Group</option>
                                                <option value="A+">A+</option>
                                                <option value="A-">A-</option>
                                                <option value="B+">B+</option>
                                                <option value="B-">B-</option>
                                                <option value="AB+">AB+</option>
                                                <option value="AB-">AB-</option>
                                                <option value="O+">O+</option>
                                                <option value="O-">O-</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="category" class="form-label">Category</label>
                                            <select class="form-select" id="category">
                                                <option value="General">General</option>
                                                <option value="SC">SC</option>
                                                <option value="ST">ST</option>
                                                <option value="OBC">OBC</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="religion" class="form-label">Religion</label>
                                            <input type="text" class="form-control" id="religion">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nationality" class="form-label">Nationality</label>
                                            <input type="text" class="form-control" id="nationality" value="Indian">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="aadhar_number" class="form-label">Aadhar Number</label>
                                            <input type="text" class="form-control" id="aadhar_number">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="correspondence_address" class="form-label">Correspondence Address</label>
                                    <textarea class="form-control" id="correspondence_address" rows="3"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="permanent_address" class="form-label">Permanent Address</label>
                                    <textarea class="form-control" id="permanent_address" rows="3"></textarea>
                                </div>
                            </form>
                        </div>

                        <!-- Family Information Tab -->
                        <div class="tab-pane fade" id="family" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="father_name" class="form-label">Father's Name</label>
                                        <input type="text" class="form-control" id="father_name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="mother_name" class="form-label">Mother's Name</label>
                                        <input type="text" class="form-control" id="mother_name">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="guardian_name" class="form-label">Guardian Name</label>
                                        <input type="text" class="form-control" id="guardian_name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="guardian_phone" class="form-label">Guardian Phone</label>
                                        <input type="text" class="form-control" id="guardian_phone">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="guardian_relation" class="form-label">Guardian Relation</label>
                                <select class="form-select" id="guardian_relation">
                                    <option value="">Select Relation</option>
                                    <option value="Father">Father</option>
                                    <option value="Mother">Mother</option>
                                    <option value="Uncle">Uncle</option>
                                    <option value="Aunt">Aunt</option>
                                    <option value="Grandfather">Grandfather</option>
                                    <option value="Grandmother">Grandmother</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>

                        <!-- Medical Information Tab -->
                        <div class="tab-pane fade" id="medical" role="tabpanel">
                            <div class="mb-3">
                                <label for="allergies" class="form-label">Allergies</label>
                                <textarea class="form-control" id="allergies" rows="3" placeholder="List any allergies (food, medicine, environmental, etc.)"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="medical_conditions" class="form-label">Medical Conditions</label>
                                <textarea class="form-control" id="medical_conditions" rows="3" placeholder="List any medical conditions, disabilities, or special needs"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                                        <input type="text" class="form-control" id="emergency_contact_name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone</label>
                                        <input type="text" class="form-control" id="emergency_contact_phone">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Details Tab -->
                        <div class="tab-pane fade" id="academic" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="class_id" class="form-label">Current Class *</label>
                                        <select class="form-select" id="class_id" required>
                                            <!-- Classes will be loaded -->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="roll_number" class="form-label">Roll Number</label>
                                        <input type="text" class="form-control" id="roll_number">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="admission_number" class="form-label">Admission Number</label>
                                        <input type="text" class="form-control" id="admission_number">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="enrollment_date" class="form-label">Enrollment Date *</label>
                                        <input type="date" class="form-control" id="enrollment_date" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="previous_school" class="form-label">Previous School</label>
                                <input type="text" class="form-control" id="previous_school">
                            </div>
                        </div>

                        <!-- Banking & Documents Tab -->
                        <div class="tab-pane fade" id="banking" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="bank_account_number" class="form-label">Bank Account Number</label>
                                        <input type="text" class="form-control" id="bank_account_number">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="ifsc_code" class="form-label">IFSC Code</label>
                                        <input type="text" class="form-control" id="ifsc_code">
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Document Upload:</strong> Document upload functionality can be added here for certificates, photos, and other documents.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveStudent()">Save Student</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Import Students</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Enter JSON array of students:</p>
                    <textarea class="form-control" id="importData" rows="10" placeholder='[{"name":"John Doe","email":"john@example.com",...}]'></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="importStudents()">Import</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPage = 1;
        let totalPages = 1;

        document.addEventListener('DOMContentLoaded', function() {
            loadClasses();
            loadStudents();
        });

        function loadClasses() {
            fetch('/admin/api/classes')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('classFilter');
                    const modalSelect = document.getElementById('class_id');
                    data.data.forEach(cls => {
                        const option = document.createElement('option');
                        option.value = cls.id;
                        option.textContent = cls.name;
                        select.appendChild(option.cloneNode(true));
                        modalSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading classes:', error));
        }

        function loadStudents(page = 1) {
            currentPage = page;
            const search = document.getElementById('search').value;
            const classId = document.getElementById('classFilter').value;

            fetch(`/admin/api/students?search=${encodeURIComponent(search)}&class_id=${classId}&page=${page}`)
                .then(response => response.json())
                .then(data => {
                    renderTable(data.data);
                    renderPagination(data.total, data.page, data.limit);
                })
                .catch(error => console.error('Error loading students:', error));
        }

        function renderTable(students) {
            const tbody = document.getElementById('studentsBody');
            tbody.innerHTML = '';
            students.forEach(student => {
                const categoryBadge = student.category && student.category !== 'General' ?
                    `<span class="badge bg-info">${student.category}</span>` : '';
                const statusBadge = `<span class="badge bg-success">Active</span>`;

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><input type="checkbox" class="student-checkbox" value="${student.id}"></td>
                    <td>${student.name}</td>
                    <td>${student.admission_number || '-'}</td>
                    <td>${student.roll_number || '-'}</td>
                    <td>${student.class_name || ''}</td>
                    <td>${student.phone || '-'}</td>
                    <td>${categoryBadge}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button class="btn btn-sm btn-warning me-1" onclick="editStudent(${student.id})" title="Edit Student">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-info me-1" onclick="viewStudent(${student.id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteStudent(${student.id})" title="Delete Student">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });

            // Update select all
            document.getElementById('selectAll').checked = false;
            updateBulkActions();
        }

        function renderPagination(total, page, limit) {
            totalPages = Math.ceil(total / limit);
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';

            if (totalPages <= 1) return;

            // Previous
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${page === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `<a class="page-link" href="#" onclick="loadStudents(${page - 1})">Previous</a>`;
            pagination.appendChild(prevLi);

            // Pages
            for (let i = 1; i <= totalPages; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === page ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="loadStudents(${i})">${i}</a>`;
                pagination.appendChild(li);
            }

            // Next
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${page === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `<a class="page-link" href="#" onclick="loadStudents(${page + 1})">Next</a>`;
            pagination.appendChild(nextLi);
        }

        function editStudent(id) {
            fetch(`/admin/api/students`)
                .then(response => response.json())
                .then(data => {
                    const student = data.data.find(s => s.id == id);
                    if (student) {
                        // Personal Details
                        document.getElementById('studentId').value = student.id;
                        document.getElementById('name').value = student.name || '';
                        document.getElementById('email').value = student.email || '';
                        document.getElementById('phone').value = student.phone || '';
                        document.getElementById('dob').value = student.dob || '';
                        document.getElementById('gender').value = student.gender || 'Male';
                        document.getElementById('blood_group').value = student.blood_group || '';
                        document.getElementById('category').value = student.category || 'General';
                        document.getElementById('religion').value = student.religion || '';
                        document.getElementById('nationality').value = student.nationality || 'Indian';
                        document.getElementById('aadhar_number').value = student.aadhar_number || '';
                        document.getElementById('correspondence_address').value = student.correspondence_address || '';
                        document.getElementById('permanent_address').value = student.permanent_address || '';

                        // Family Information
                        document.getElementById('father_name').value = student.father_name || '';
                        document.getElementById('mother_name').value = student.mother_name || '';
                        document.getElementById('guardian_name').value = student.guardian_name || '';
                        document.getElementById('guardian_phone').value = student.guardian_phone || '';
                        document.getElementById('guardian_relation').value = student.guardian_relation || '';

                        // Medical Information
                        document.getElementById('allergies').value = student.allergies || '';
                        document.getElementById('medical_conditions').value = student.medical_conditions || '';
                        document.getElementById('emergency_contact_name').value = student.emergency_contact_name || '';
                        document.getElementById('emergency_contact_phone').value = student.emergency_contact_phone || '';

                        // Academic Details
                        document.getElementById('class_id').value = student.class_id || '';
                        document.getElementById('roll_number').value = student.roll_number || '';
                        document.getElementById('admission_number').value = student.admission_number || '';
                        document.getElementById('enrollment_date').value = student.enrollment_date || '';
                        document.getElementById('previous_school').value = student.previous_school || '';

                        // Banking & Documents
                        document.getElementById('bank_account_number').value = student.bank_account_number || '';
                        document.getElementById('ifsc_code').value = student.ifsc_code || '';

                        document.getElementById('modalTitle').textContent = 'Edit Student';
                        new bootstrap.Modal(document.getElementById('addModal')).show();
                    }
                });
        }

        function saveStudent() {
            const id = document.getElementById('studentId').value;
            const data = {
                // Personal Details
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                dob: document.getElementById('dob').value,
                gender: document.getElementById('gender').value,
                blood_group: document.getElementById('blood_group').value,
                category: document.getElementById('category').value,
                religion: document.getElementById('religion').value,
                nationality: document.getElementById('nationality').value,
                aadhar_number: document.getElementById('aadhar_number').value,
                correspondence_address: document.getElementById('correspondence_address').value,
                permanent_address: document.getElementById('permanent_address').value,

                // Family Information
                father_name: document.getElementById('father_name').value,
                mother_name: document.getElementById('mother_name').value,
                guardian_name: document.getElementById('guardian_name').value,
                guardian_phone: document.getElementById('guardian_phone').value,
                guardian_relation: document.getElementById('guardian_relation').value,

                // Medical Information
                allergies: document.getElementById('allergies').value,
                medical_conditions: document.getElementById('medical_conditions').value,
                emergency_contact_name: document.getElementById('emergency_contact_name').value,
                emergency_contact_phone: document.getElementById('emergency_contact_phone').value,

                // Academic Details
                class_id: document.getElementById('class_id').value,
                roll_number: document.getElementById('roll_number').value,
                admission_number: document.getElementById('admission_number').value,
                enrollment_date: document.getElementById('enrollment_date').value,
                previous_school: document.getElementById('previous_school').value,

                // Banking & Documents
                bank_account_number: document.getElementById('bank_account_number').value,
                ifsc_code: document.getElementById('ifsc_code').value
            };

            const url = id ? '/admin/api/students/update?id=' + id : '/admin/api/students/create';
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
                    loadStudents(currentPage);
                    // Reset form
                    document.getElementById('studentForm').reset();
                    document.getElementById('studentId').value = '';
                    document.getElementById('modalTitle').textContent = 'Add Student';
                } else {
                    alert('Error: ' + result.error);
                }
            })
            .catch(error => {
                console.error('Error saving student:', error);
                alert('Error saving student. Please try again.');
            });
        }

        function deleteStudent(id) {
            if (confirm('Are you sure you want to delete this student?')) {
                fetch('/admin/api/students/delete?id=' + id, { method: 'DELETE' })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            loadStudents(currentPage);
                        } else {
                            alert('Error: ' + result.error);
                        }
                    });
            }
        }

        function viewStudent(id) {
            fetch(`/admin/api/students`)
                .then(response => response.json())
                .then(data => {
                    const student = data.data.find(s => s.id == id);
                    if (student) {
                        let details = `Student Details:\n\n`;
                        details += `Name: ${student.name}\n`;
                        details += `Admission No: ${student.admission_number || 'N/A'}\n`;
                        details += `Roll No: ${student.roll_number || 'N/A'}\n`;
                        details += `Class: ${student.class_name || 'N/A'}\n`;
                        details += `Email: ${student.email || 'N/A'}\n`;
                        details += `Phone: ${student.phone || 'N/A'}\n`;
                        details += `Date of Birth: ${student.dob || 'N/A'}\n`;
                        details += `Gender: ${student.gender || 'N/A'}\n`;
                        details += `Category: ${student.category || 'General'}\n`;
                        details += `Blood Group: ${student.blood_group || 'N/A'}\n`;
                        details += `Father: ${student.father_name || 'N/A'}\n`;
                        details += `Mother: ${student.mother_name || 'N/A'}\n`;
                        details += `Guardian: ${student.guardian_name || 'N/A'}\n`;
                        details += `Enrollment Date: ${student.enrollment_date || 'N/A'}\n`;

                        if (student.allergies || student.medical_conditions) {
                            details += `\nMedical Information:\n`;
                            if (student.allergies) details += `Allergies: ${student.allergies}\n`;
                            if (student.medical_conditions) details += `Medical Conditions: ${student.medical_conditions}\n`;
                        }

                        alert(details);
                    }
                });
        }

        function exportStudents() {
            fetch('/admin/api/students')
                .then(response => response.json())
                .then(data => {
                    const headers = 'Name,Admission No,Roll No,Email,Phone,Class,DOB,Gender,Category,Father,Mother,Guardian,Enrollment Date\n';
                    const csv = headers + data.data.map(s =>
                        `"${s.name}","${s.admission_number || ''}","${s.roll_number || ''}","${s.email || ''}","${s.phone || ''}","${s.class_name || ''}","${s.dob || ''}","${s.gender || ''}","${s.category || 'General'}","${s.father_name || ''}","${s.mother_name || ''}","${s.guardian_name || ''}","${s.enrollment_date || ''}"`
                    ).join('\n');
                    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'students_comprehensive.csv';
                    a.click();
                });
        }

        function importStudents() {
            const data = JSON.parse(document.getElementById('importData').value);
            fetch('/admin/api/students/bulk-import', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                alert(`Imported ${result.success} students. Errors: ${result.errors.length}`);
                bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
                loadStudents();
            });
        }

        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkActions();
        });

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('student-checkbox')) {
                updateBulkActions();
            }
        });

        function updateBulkActions() {
            const checked = document.querySelectorAll('.student-checkbox:checked');
            document.getElementById('bulkActions').style.display = checked.length > 0 ? 'block' : 'none';
        }

        function bulkDelete() {
            const ids = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);
            if (confirm(`Delete ${ids.length} students?`)) {
                ids.forEach(id => deleteStudent(id));
            }
        }
    </script>
</body>
</html>
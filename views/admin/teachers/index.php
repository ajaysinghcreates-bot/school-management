<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teachers Management - School Management</title>
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
                    <li class="nav-item"><a class="nav-link active" href="/admin/teachers">Teachers</a></li>
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
            <h1>Teachers Management</h1>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">Add Teacher</button>
        </div>

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="search" class="form-control" placeholder="Search by name, email, phone">
            </div>
            <div class="col-md-3">
                <select id="subjectFilter" class="form-select">
                    <option value="">All Subjects</option>
                    <!-- Subjects will be loaded dynamically -->
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary" onclick="loadTeachers()">Filter</button>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="teachersTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Employee ID</th>
                        <th>Subject</th>
                        <th>Qualification</th>
                        <th>Experience</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="teachersBody">
                    <!-- Data will be loaded here -->
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav aria-label="Teachers pagination">
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
                    <h5 class="modal-title" id="modalTitle">Add Teacher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="teacherTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">Personal Details</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="professional-tab" data-bs-toggle="tab" data-bs-target="#professional" type="button" role="tab">Professional Info</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="emergency-tab" data-bs-toggle="tab" data-bs-target="#emergency" type="button" role="tab">Emergency & Medical</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="banking-tab" data-bs-toggle="tab" data-bs-target="#banking" type="button" role="tab">Banking & Documents</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button" role="tab">Weekly Schedule</button>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content mt-3" id="teacherTabContent">
                        <!-- Personal Details Tab -->
                        <div class="tab-pane fade show active" id="personal" role="tabpanel">
                            <form id="teacherForm">
                                <input type="hidden" id="teacherId">
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
                                            <label for="marital_status" class="form-label">Marital Status</label>
                                            <select class="form-select" id="marital_status">
                                                <option value="Single">Single</option>
                                                <option value="Married">Married</option>
                                                <option value="Divorced">Divorced</option>
                                                <option value="Widowed">Widowed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
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
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="aadhar_number" class="form-label">Aadhar Number</label>
                                            <input type="text" class="form-control" id="aadhar_number">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" rows="3"></textarea>
                                </div>
                            </form>
                        </div>

                        <!-- Professional Information Tab -->
                        <div class="tab-pane fade" id="professional" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="employee_id" class="form-label">Employee ID</label>
                                        <input type="text" class="form-control" id="employee_id">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="subject_id" class="form-label">Primary Subject *</label>
                                        <select class="form-select" id="subject_id" required>
                                            <!-- Subjects will be loaded -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="qualification" class="form-label">Qualification</label>
                                        <input type="text" class="form-control" id="qualification" placeholder="e.g., M.Sc. B.Ed.">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="experience_years" class="form-label">Experience (Years)</label>
                                        <input type="number" class="form-control" id="experience_years" min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="specialization" class="form-label">Specialization</label>
                                        <input type="text" class="form-control" id="specialization">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="hire_date" class="form-label">Hire Date *</label>
                                        <input type="date" class="form-control" id="hire_date" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="salary" class="form-label">Monthly Salary</label>
                                        <input type="number" class="form-control" id="salary" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="previous_school" class="form-label">Previous School</label>
                                        <input type="text" class="form-control" id="previous_school">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="teaching_subjects" class="form-label">Teaching Subjects</label>
                                <textarea class="form-control" id="teaching_subjects" rows="2" placeholder="List all subjects taught, separated by commas"></textarea>
                            </div>
                        </div>

                        <!-- Emergency & Medical Tab -->
                        <div class="tab-pane fade" id="emergency" role="tabpanel">
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
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Medical Information:</strong> Detailed medical records can be maintained separately for privacy and compliance.
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
                            <div class="mb-3">
                                <label for="pan_number" class="form-label">PAN Number</label>
                                <input type="text" class="form-control" id="pan_number">
                            </div>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Document Upload:</strong> Certificates, ID proofs, and other documents can be uploaded here.
                            </div>
                        </div>

                        <!-- Weekly Schedule Tab -->
                        <div class="tab-pane fade" id="schedule" role="tabpanel">
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-calendar-alt"></i>
                                <strong>Weekly Teaching Schedule:</strong> Enter class timings for each day (e.g., "9:00 AM - 10:00 AM: Class 5A, 10:30 AM - 11:30 AM: Class 6B")
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="monday_schedule" class="form-label">Monday</label>
                                        <textarea class="form-control" id="monday_schedule" rows="2"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="tuesday_schedule" class="form-label">Tuesday</label>
                                        <textarea class="form-control" id="tuesday_schedule" rows="2"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="wednesday_schedule" class="form-label">Wednesday</label>
                                        <textarea class="form-control" id="wednesday_schedule" rows="2"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="thursday_schedule" class="form-label">Thursday</label>
                                        <textarea class="form-control" id="thursday_schedule" rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="friday_schedule" class="form-label">Friday</label>
                                        <textarea class="form-control" id="friday_schedule" rows="2"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="saturday_schedule" class="form-label">Saturday</label>
                                        <textarea class="form-control" id="saturday_schedule" rows="2"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="sunday_schedule" class="form-label">Sunday</label>
                                        <textarea class="form-control" id="sunday_schedule" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveTeacher()">Save Teacher</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPage = 1;

        document.addEventListener('DOMContentLoaded', function() {
            loadSubjects();
            loadTeachers();
        });

        function loadSubjects() {
            fetch('/admin/api/subjects')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('subjectFilter');
                    const modalSelect = document.getElementById('subject_id');
                    data.data.forEach(sub => {
                        const option = document.createElement('option');
                        option.value = sub.id;
                        option.textContent = sub.name;
                        select.appendChild(option.cloneNode(true));
                        modalSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading subjects:', error));
        }

        function loadTeachers(page = 1) {
            currentPage = page;
            const search = document.getElementById('search').value;
            const subjectId = document.getElementById('subjectFilter').value;

            fetch(`/admin/api/teachers?search=${encodeURIComponent(search)}&subject_id=${subjectId}&page=${page}`)
                .then(response => response.json())
                .then(data => {
                    renderTable(data.data);
                    renderPagination(data.total, data.page, data.limit);
                })
                .catch(error => console.error('Error loading teachers:', error));
        }

        function renderTable(teachers) {
            const tbody = document.getElementById('teachersBody');
            tbody.innerHTML = '';
            teachers.forEach(teacher => {
                const experienceText = teacher.experience_years ? `${teacher.experience_years} years` : 'N/A';
                const statusBadge = `<span class="badge bg-success">Active</span>`;

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${teacher.name}</td>
                    <td>${teacher.employee_id || '-'}</td>
                    <td>${teacher.subject_name || ''}</td>
                    <td>${teacher.qualification || '-'}</td>
                    <td>${experienceText}</td>
                    <td>${teacher.phone || '-'}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button class="btn btn-sm btn-warning me-1" onclick="editTeacher(${teacher.id})" title="Edit Teacher">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-info me-1" onclick="viewTeacher(${teacher.id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteTeacher(${teacher.id})" title="Delete Teacher">
                            <i class="fas fa-trash"></i>
                        </button>
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

            // Previous
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${page === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `<a class="page-link" href="#" onclick="loadTeachers(${page - 1})">Previous</a>`;
            pagination.appendChild(prevLi);

            // Pages
            for (let i = 1; i <= totalPages; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === page ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="loadTeachers(${i})">${i}</a>`;
                pagination.appendChild(li);
            }

            // Next
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${page === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `<a class="page-link" href="#" onclick="loadTeachers(${page + 1})">Next</a>`;
            pagination.appendChild(nextLi);
        }

        function viewTeacher(id) {
            fetch(`/admin/api/teachers`)
                .then(response => response.json())
                .then(data => {
                    const teacher = data.data.find(t => t.id == id);
                    if (teacher) {
                        let details = `Teacher Details:\n\n`;
                        details += `Name: ${teacher.name}\n`;
                        details += `Employee ID: ${teacher.employee_id || 'N/A'}\n`;
                        details += `Email: ${teacher.email || 'N/A'}\n`;
                        details += `Phone: ${teacher.phone || 'N/A'}\n`;
                        details += `Subject: ${teacher.subject_name || 'N/A'}\n`;
                        details += `Qualification: ${teacher.qualification || 'N/A'}\n`;
                        details += `Experience: ${teacher.experience_years ? teacher.experience_years + ' years' : 'N/A'}\n`;
                        details += `Specialization: ${teacher.specialization || 'N/A'}\n`;
                        details += `Hire Date: ${teacher.hire_date || 'N/A'}\n`;
                        details += `Salary: ${teacher.salary ? '$' + teacher.salary : 'N/A'}\n`;

                        if (teacher.teaching_subjects) {
                            details += `\nTeaching Subjects: ${teacher.teaching_subjects}\n`;
                        }

                        alert(details);
                    }
                });
        }

        function editTeacher(id) {
            fetch(`/admin/api/teachers`)
                .then(response => response.json())
                .then(data => {
                    const teacher = data.data.find(t => t.id == id);
                    if (teacher) {
                        // Personal Details
                        document.getElementById('teacherId').value = teacher.id;
                        document.getElementById('name').value = teacher.name || '';
                        document.getElementById('email').value = teacher.email || '';
                        document.getElementById('phone').value = teacher.phone || '';
                        document.getElementById('address').value = teacher.address || '';
                        document.getElementById('dob').value = teacher.dob || '';
                        document.getElementById('gender').value = teacher.gender || 'Male';
                        document.getElementById('marital_status').value = teacher.marital_status || 'Single';
                        document.getElementById('blood_group').value = teacher.blood_group || '';
                        document.getElementById('aadhar_number').value = teacher.aadhar_number || '';

                        // Professional Information
                        document.getElementById('employee_id').value = teacher.employee_id || '';
                        document.getElementById('subject_id').value = teacher.subject_id || '';
                        document.getElementById('qualification').value = teacher.qualification || '';
                        document.getElementById('experience_years').value = teacher.experience_years || '';
                        document.getElementById('specialization').value = teacher.specialization || '';
                        document.getElementById('hire_date').value = teacher.hire_date || '';
                        document.getElementById('salary').value = teacher.salary || '';
                        document.getElementById('previous_school').value = teacher.previous_school || '';
                        document.getElementById('teaching_subjects').value = teacher.teaching_subjects || '';

                        // Emergency & Medical
                        document.getElementById('emergency_contact_name').value = teacher.emergency_contact_name || '';
                        document.getElementById('emergency_contact_phone').value = teacher.emergency_contact_phone || '';

                        // Banking & Documents
                        document.getElementById('bank_account_number').value = teacher.bank_account_number || '';
                        document.getElementById('ifsc_code').value = teacher.ifsc_code || '';
                        document.getElementById('pan_number').value = teacher.pan_number || '';

                        // Weekly Schedule
                        document.getElementById('monday_schedule').value = teacher.monday_schedule || '';
                        document.getElementById('tuesday_schedule').value = teacher.tuesday_schedule || '';
                        document.getElementById('wednesday_schedule').value = teacher.wednesday_schedule || '';
                        document.getElementById('thursday_schedule').value = teacher.thursday_schedule || '';
                        document.getElementById('friday_schedule').value = teacher.friday_schedule || '';
                        document.getElementById('saturday_schedule').value = teacher.saturday_schedule || '';
                        document.getElementById('sunday_schedule').value = teacher.sunday_schedule || '';

                        document.getElementById('modalTitle').textContent = 'Edit Teacher';
                        new bootstrap.Modal(document.getElementById('addModal')).show();
                    }
                });
        }

        function saveTeacher() {
            const id = document.getElementById('teacherId').value;
            const data = {
                // Personal Details
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                address: document.getElementById('address').value,
                dob: document.getElementById('dob').value,
                gender: document.getElementById('gender').value,
                marital_status: document.getElementById('marital_status').value,
                blood_group: document.getElementById('blood_group').value,
                aadhar_number: document.getElementById('aadhar_number').value,

                // Professional Information
                employee_id: document.getElementById('employee_id').value,
                subject_id: document.getElementById('subject_id').value,
                qualification: document.getElementById('qualification').value,
                experience_years: document.getElementById('experience_years').value,
                specialization: document.getElementById('specialization').value,
                hire_date: document.getElementById('hire_date').value,
                salary: document.getElementById('salary').value,
                previous_school: document.getElementById('previous_school').value,
                teaching_subjects: document.getElementById('teaching_subjects').value,

                // Emergency & Medical
                emergency_contact_name: document.getElementById('emergency_contact_name').value,
                emergency_contact_phone: document.getElementById('emergency_contact_phone').value,

                // Banking & Documents
                bank_account_number: document.getElementById('bank_account_number').value,
                ifsc_code: document.getElementById('ifsc_code').value,
                pan_number: document.getElementById('pan_number').value,

                // Weekly Schedule
                monday_schedule: document.getElementById('monday_schedule').value,
                tuesday_schedule: document.getElementById('tuesday_schedule').value,
                wednesday_schedule: document.getElementById('wednesday_schedule').value,
                thursday_schedule: document.getElementById('thursday_schedule').value,
                friday_schedule: document.getElementById('friday_schedule').value,
                saturday_schedule: document.getElementById('saturday_schedule').value,
                sunday_schedule: document.getElementById('sunday_schedule').value
            };

            const url = id ? '/admin/api/teachers/update?id=' + id : '/admin/api/teachers/create';
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
                    loadTeachers(currentPage);
                    // Reset form
                    document.getElementById('teacherForm').reset();
                    document.getElementById('teacherId').value = '';
                    document.getElementById('modalTitle').textContent = 'Add Teacher';
                } else {
                    alert('Error: ' + result.error);
                }
            })
            .catch(error => {
                console.error('Error saving teacher:', error);
                alert('Error saving teacher. Please try again.');
            });
        }

        function deleteTeacher(id) {
            if (confirm('Are you sure you want to delete this teacher?')) {
                fetch('/admin/api/teachers/delete?id=' + id, { method: 'DELETE' })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            loadTeachers(currentPage);
                        } else {
                            alert('Error: ' + result.error);
                        }
                    });
            }
        }
    </script>
</body>
</html>
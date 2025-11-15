<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management - School Management</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/teacher/dashboard">School Management Teacher</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="/teacher/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/teacher/attendance">Attendance</a></li>
                    <li class="nav-item"><a class="nav-link" href="/teacher/classes">Classes</a></li>
                    <li class="nav-item"><a class="nav-link" href="/teacher/exams">Exams</a></li>
                    <li class="nav-item"><a class="nav-link" href="/teacher/profile">Profile</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <h1 class="mb-4">Attendance Management</h1>

        <div class="card">
            <div class="card-header">
                <h5>Mark Daily Attendance</h5>
            </div>
            <div class="card-body">
                <form id="attendance-form">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="attendance-date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="attendance-date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="class-select" class="form-label">Class</label>
                            <select class="form-select" id="class-select" required>
                                <option value="">Select Class</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-primary" id="load-students">Load Students</button>
                        </div>
                    </div>
                </form>

                <div id="students-list" style="display: none;">
                    <h6>Students</h6>
                    <form id="mark-attendance-form">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Class</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="students-tbody">
                                    <!-- Students will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-success" id="save-attendance">Save Attendance</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadClasses();

            document.getElementById('load-students').addEventListener('click', loadStudents);
            document.getElementById('save-attendance').addEventListener('click', saveAttendance);
        });

        function loadClasses() {
            // Assuming we have an API to get assigned classes
            fetch('/teacher/api/classes')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('class-select');
                    data.data.forEach(cls => {
                        const option = document.createElement('option');
                        option.value = cls.id;
                        option.textContent = cls.name;
                        select.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading classes:', error));
        }

        function loadStudents() {
            const date = document.getElementById('attendance-date').value;
            const classId = document.getElementById('class-select').value;

            if (!date || !classId) {
                alert('Please select date and class');
                return;
            }

            fetch(`/teacher/api/attendance?date=${date}&class_id=${classId}`)
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('students-tbody');
                    tbody.innerHTML = '';

                    data.data.forEach(student => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${student.name}</td>
                            <td>${student.class_name}</td>
                            <td>
                                <select class="form-select status-select" data-student-id="${student.id}">
                                    <option value="present" ${student.status === 'present' ? 'selected' : ''}>Present</option>
                                    <option value="absent" ${student.status === 'absent' ? 'selected' : ''}>Absent</option>
                                    <option value="late" ${student.status === 'late' ? 'selected' : ''}>Late</option>
                                </select>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });

                    document.getElementById('students-list').style.display = 'block';
                })
                .catch(error => console.error('Error loading students:', error));
        }

        function saveAttendance() {
            const date = document.getElementById('attendance-date').value;
            const attendances = [];

            document.querySelectorAll('.status-select').forEach(select => {
                attendances.push({
                    student_id: select.dataset.studentId,
                    status: select.value
                });
            });

            fetch('/teacher/api/attendance/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ date, attendances })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Attendance saved successfully');
                } else {
                    alert('Error saving attendance: ' + data.error);
                }
            })
            .catch(error => console.error('Error saving attendance:', error));
        }
    </script>
</body>
</html>
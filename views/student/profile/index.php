<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Student Portal</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/student/dashboard">Student Portal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="/student/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/student/attendance">Attendance</a></li>
                    <li class="nav-item"><a class="nav-link" href="/student/results">Results</a></li>
                    <li class="nav-item"><a class="nav-link" href="/student/fees">Fees</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/student/profile">Profile</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <h1 class="mb-4">Student Profile</h1>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Personal Information</div>
                    <div class="card-body">
                        <form id="profile-form">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="text" class="form-control" id="date_of_birth" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Gender</label>
                                    <input type="text" class="form-control" id="gender" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" id="address" rows="2" readonly></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Class</label>
                                    <input type="text" class="form-control" id="class_name" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Admission Date</label>
                                    <input type="text" class="form-control" id="admission_date" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Parent Name</label>
                                    <input type="text" class="form-control" id="parent_name" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Parent Phone</label>
                                    <input type="text" class="form-control" id="parent_phone" readonly>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Academic Information</div>
                    <div class="card-body">
                        <p><strong>Roll Number:</strong> <span id="roll_number">-</span></p>
                        <p><strong>Enrollment Number:</strong> <span id="enrollment_number">-</span></p>
                        <p><strong>Current Semester:</strong> <span id="current_semester">-</span></p>
                        <p><strong>Academic Year:</strong> <span id="academic_year">-</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadProfile();
        });

        function loadProfile() {
            fetch('/student/api/profile')
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        document.getElementById('name').value = data.name || '';
                        document.getElementById('email').value = data.email || '';
                        document.getElementById('date_of_birth').value = data.date_of_birth ? new Date(data.date_of_birth).toLocaleDateString() : '';
                        document.getElementById('gender').value = data.gender || '';
                        document.getElementById('phone').value = data.phone || '';
                        document.getElementById('address').value = data.address || '';
                        document.getElementById('class_name').value = data.class_name || '';
                        document.getElementById('admission_date').value = data.admission_date ? new Date(data.admission_date).toLocaleDateString() : '';
                        document.getElementById('parent_name').value = data.parent_name || '';
                        document.getElementById('parent_phone').value = data.parent_phone || '';
                        document.getElementById('roll_number').textContent = data.roll_number || '-';
                        document.getElementById('enrollment_number').textContent = data.enrollment_number || '-';
                        document.getElementById('current_semester').textContent = data.current_semester || '-';
                        document.getElementById('academic_year').textContent = data.academic_year || '-';
                    }
                })
                .catch(error => {
                    console.error('Error loading profile:', error);
                });
        }
    </script>
</body>
</html>
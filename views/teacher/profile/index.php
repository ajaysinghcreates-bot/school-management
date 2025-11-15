<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - School Management</title>
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
                    <li class="nav-item"><a class="nav-link" href="/teacher/attendance">Attendance</a></li>
                    <li class="nav-item"><a class="nav-link" href="/teacher/classes">Classes</a></li>
                    <li class="nav-item"><a class="nav-link" href="/teacher/exams">Exams</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/teacher/profile">Profile</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <h1 class="mb-4">My Profile</h1>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Personal Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4"><strong>Name:</strong></div>
                            <div class="col-sm-8" id="profile-name"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4"><strong>Email:</strong></div>
                            <div class="col-sm-8" id="profile-email"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4"><strong>Phone:</strong></div>
                            <div class="col-sm-8" id="profile-phone"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4"><strong>Address:</strong></div>
                            <div class="col-sm-8" id="profile-address"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4"><strong>Date of Birth:</strong></div>
                            <div class="col-sm-8" id="profile-dob"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4"><strong>Gender:</strong></div>
                            <div class="col-sm-8" id="profile-gender"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4"><strong>Hire Date:</strong></div>
                            <div class="col-sm-8" id="profile-hire-date"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4"><strong>Salary:</strong></div>
                            <div class="col-sm-8" id="profile-salary"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Assigned Subjects & Classes</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group" id="subjects-list">
                            <!-- Subjects will be loaded here -->
                        </ul>
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
            fetch('/teacher/api/profile')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('profile-name').textContent = data.name;
                    document.getElementById('profile-email').textContent = data.email;
                    document.getElementById('profile-phone').textContent = data.phone;
                    document.getElementById('profile-address').textContent = data.address;
                    document.getElementById('profile-dob').textContent = data.dob;
                    document.getElementById('profile-gender').textContent = data.gender;
                    document.getElementById('profile-hire-date').textContent = data.hire_date;
                    document.getElementById('profile-salary').textContent = data.salary;

                    const list = document.getElementById('subjects-list');
                    list.innerHTML = '';
                    data.subjects.forEach(subject => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item';
                        li.textContent = `${subject.name} (${subject.class_name})`;
                        list.appendChild(li);
                    });
                })
                .catch(error => console.error('Error loading profile:', error));
        }
    </script>
</body>
</html>
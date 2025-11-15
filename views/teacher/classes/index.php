<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigned Classes - School Management</title>
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
                    <li class="nav-item"><a class="nav-link active" href="/teacher/classes">Classes</a></li>
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
        <h1 class="mb-4">Assigned Classes & Subjects</h1>

        <div class="card">
            <div class="card-header">
                <h5>My Classes</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Class Name</th>
                                <th>Description</th>
                                <th>Subjects</th>
                            </tr>
                        </thead>
                        <tbody id="classes-tbody">
                            <!-- Classes will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadClasses();
        });

        function loadClasses() {
            fetch('/teacher/api/classes')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('classes-tbody');
                    tbody.innerHTML = '';

                    if (data.data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="3" class="text-center">No assigned classes</td></tr>';
                        return;
                    }

                    data.data.forEach(cls => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${cls.name}</td>
                            <td>${cls.description || 'N/A'}</td>
                            <td>${cls.subjects}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(error => console.error('Error loading classes:', error));
        }
    </script>
</body>
</html>
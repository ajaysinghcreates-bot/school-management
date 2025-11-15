<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - School Management</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .summary-card { transition: transform 0.2s; }
        .summary-card:hover { transform: translateY(-5px); }
    </style>
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
                    <li class="nav-item"><a class="nav-link active" href="/teacher/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/teacher/attendance">Attendance</a></li>
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
        <h1 class="mb-4">Teacher Dashboard</h1>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card summary-card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Assigned Classes</h5>
                        <h2 id="total-classes">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card summary-card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Assigned Subjects</h5>
                        <h2 id="total-subjects">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card summary-card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Students</h5>
                        <h2 id="total-students">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card summary-card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Attendance Rate</h5>
                        <h2 id="attendance-rate">0%</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card summary-card bg-secondary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Upcoming Exams</h5>
                        <h2 id="upcoming-exams">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card summary-card bg-dark text-white">
                    <div class="card-body">
                        <h5 class="card-title">Avg Performance</h5>
                        <h2 id="avg-performance">0%</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Quick Actions</div>
                    <div class="card-body">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                            <a href="/teacher/attendance" class="btn btn-primary">Mark Attendance</a>
                            <a href="/teacher/exams" class="btn btn-success">Manage Exams</a>
                            <a href="/teacher/classes" class="btn btn-warning">View Classes</a>
                            <a href="/teacher/profile" class="btn btn-info">View Profile</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load dynamic data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadStats();
        });

        function loadStats() {
            fetch('/teacher/api/dashboard-stats')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('total-classes').textContent = data.total_classes;
                    document.getElementById('total-subjects').textContent = data.total_subjects;
                    document.getElementById('total-students').textContent = data.total_students;
                    document.getElementById('attendance-rate').textContent = data.attendance_rate + '%';
                    document.getElementById('upcoming-exams').textContent = data.upcoming_exams;
                    document.getElementById('avg-performance').textContent = data.avg_performance + '%';
                })
                .catch(error => console.error('Error loading stats:', error));
        }

        // Auto-refresh stats every 5 minutes
        setInterval(loadStats, 300000);
    </script>
</body>
</html>
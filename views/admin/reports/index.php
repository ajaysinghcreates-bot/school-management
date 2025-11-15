<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - School Management</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container { position: relative; height: 400px; margin-bottom: 2rem; }
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
                    <li class="nav-item"><a class="nav-link" href="/admin/events">Events</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/gallery">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/admin/reports">Reports</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <h1>Reports</h1>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Student Enrollment by Class</div>
                    <div class="card-body">
                        <canvas id="studentReportChart" class="chart-container"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Fee Collection Status</div>
                    <div class="card-body">
                        <canvas id="feeReportChart" class="chart-container"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Quick Stats</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5>Total Students</h5>
                                        <h2 id="totalStudents">0</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5>Total Teachers</h5>
                                        <h2 id="totalTeachers">0</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h5>Total Classes</h5>
                                        <h2 id="totalClasses">0</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h5>Pending Fees</h5>
                                        <h2 id="pendingFees">0</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadStudentReport();
            loadFeeReport();
            loadStats();
        });

        function loadStudentReport() {
            fetch('/admin/api/reports/students')
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('studentReportChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.map(item => item.class),
                            datasets: [{
                                label: 'Number of Students',
                                data: data.map(item => item.count),
                                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true }
                            }
                        }
                    });
                });
        }

        function loadFeeReport() {
            fetch('/admin/api/reports/fees')
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('feeReportChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: data.map(item => item.status),
                            datasets: [{
                                label: 'Fee Amount',
                                data: data.map(item => item.total),
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.5)',
                                    'rgba(54, 162, 235, 0.5)',
                                    'rgba(255, 205, 86, 0.5)'
                                ],
                                borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 205, 86, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                });
        }

        function loadStats() {
            fetch('/admin/api/stats')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalStudents').textContent = data.total_students;
                    document.getElementById('totalTeachers').textContent = data.total_teachers;
                    document.getElementById('totalClasses').textContent = data.total_classes;
                    document.getElementById('pendingFees').textContent = data.total_fees_pending;
                });
        }
    </script>
</body>
</html>
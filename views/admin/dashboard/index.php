<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - School Management</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .summary-card { transition: transform 0.2s; }
        .summary-card:hover { transform: translateY(-5px); }
        .chart-container { position: relative; height: 400px; }
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
                    <li class="nav-item"><a class="nav-link" href="/admin/homepage">Homepage</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/reports">Reports</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <h1 class="mb-4">Admin Dashboard</h1>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card summary-card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Students</h5>
                        <h2 id="total-students"><?php echo $stats['total_students']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card summary-card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Teachers</h5>
                        <h2 id="total-teachers"><?php echo $stats['total_teachers']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card summary-card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Classes</h5>
                        <h2 id="total-classes"><?php echo $stats['total_classes']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card summary-card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Attendance Rate</h5>
                        <h2 id="attendance-rate"><?php echo $stats['attendance_rate']; ?>%</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Monthly Student Enrollment</div>
                    <div class="card-body">
                        <canvas id="enrollmentChart" class="chart-container"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Attendance Trend (Last 30 Days)</div>
                    <div class="card-body">
                        <canvas id="attendanceChart" class="chart-container"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions and Notifications -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Quick Actions</div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/admin/students?action=add" class="btn btn-primary">Add New Student</a>
                            <a href="/admin/teachers?action=add" class="btn btn-success">Add New Teacher</a>
                            <a href="/admin/classes?action=add" class="btn btn-warning">Add New Class</a>
                            <a href="/admin/events?action=add" class="btn btn-info">Add New Event</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Recent Notifications</div>
                    <div class="card-body">
                        <ul id="notifications-list" class="list-group list-group-flush">
                            <!-- Notifications will be loaded via AJAX -->
                        </ul>
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
            loadChartData();
            loadAttendanceTrend();
            loadNotifications();
        });

        function loadStats() {
            fetch('/admin/api/stats')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('total-students').textContent = data.total_students;
                    document.getElementById('total-teachers').textContent = data.total_teachers;
                    document.getElementById('total-classes').textContent = data.total_classes;
                    document.getElementById('attendance-rate').textContent = data.attendance_rate + '%';
                })
                .catch(error => console.error('Error loading stats:', error));
        }

        function loadChartData() {
            fetch('/admin/api/chart-data')
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('enrollmentChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.map(item => item.month),
                            datasets: [{
                                label: 'New Students',
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
                })
                .catch(error => console.error('Error loading chart data:', error));
        }

        function loadAttendanceTrend() {
            fetch('/admin/api/attendance-trend')
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('attendanceChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.map(item => item.date),
                            datasets: [{
                                label: 'Attendance Rate (%)',
                                data: data.map(item => item.rate),
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true, max: 100 }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error loading attendance trend:', error));
        }

        function loadNotifications() {
            fetch('/admin/api/notifications')
                .then(response => response.json())
                .then(data => {
                    const list = document.getElementById('notifications-list');
                    list.innerHTML = '';
                    if (data.length === 0) {
                        list.innerHTML = '<li class="list-group-item">No recent notifications</li>';
                    } else {
                        data.forEach(notification => {
                            const li = document.createElement('li');
                            li.className = 'list-group-item';
                            li.textContent = notification.message + ' - ' + new Date(notification.created_at).toLocaleDateString();
                            list.appendChild(li);
                        });
                    }
                })
                .catch(error => console.error('Error loading notifications:', error));
        }

        // Auto-refresh stats every 5 minutes
        setInterval(loadStats, 300000);
    </script>
</body>
</html>
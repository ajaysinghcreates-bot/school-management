<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - School Management</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .summary-card { transition: transform 0.2s; }
        .summary-card:hover { transform: translateY(-5px); }
    </style>
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
                    <li class="nav-item"><a class="nav-link active" href="/student/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/student/attendance">Attendance</a></li>
                    <li class="nav-item"><a class="nav-link" href="/student/results">Results</a></li>
                    <li class="nav-item"><a class="nav-link" href="/student/fees">Fees</a></li>
                    <li class="nav-item"><a class="nav-link" href="/student/profile">Profile</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <h1 class="mb-4">Student Dashboard</h1>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card summary-card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Attendance Percentage</h5>
                        <h2 id="attendance-percentage">0%</h2>
                        <small>Last 30 days</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card summary-card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Fee Status</h5>
                        <h2 id="fee-outstanding">$0</h2>
                        <small>Outstanding</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Exam Results -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Recent Exam Results</div>
                    <div class="card-body">
                        <div id="recent-results" class="list-group">
                            <div class="list-group-item">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Announcements -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Announcements</div>
                    <div class="card-body">
                        <div id="announcements" class="list-group">
                            <div class="list-group-item">Loading...</div>
                        </div>
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
                            <a href="/student/attendance" class="btn btn-primary">View Attendance</a>
                            <a href="/student/results" class="btn btn-success">View Results</a>
                            <a href="/student/fees" class="btn btn-warning">Pay Fees</a>
                            <a href="/student/profile" class="btn btn-info">View Profile</a>
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
            fetch('/student/api/dashboard-stats')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('attendance-percentage').textContent = data.attendance_percentage + '%';
                    document.getElementById('fee-outstanding').textContent = '$' + data.fee_status.outstanding;

                    // Recent results
                    const resultsHtml = data.recent_results.length > 0
                        ? data.recent_results.map(result =>
                            `<div class="list-group-item">
                                <strong>${result.title}</strong> - ${result.marks_obtained}/${result.total_marks || 'N/A'} (${result.grade})<br>
                                <small>${new Date(result.exam_date).toLocaleDateString()}</small>
                            </div>`
                        ).join('')
                        : '<div class="list-group-item">No recent results</div>';
                    document.getElementById('recent-results').innerHTML = resultsHtml;

                    // Announcements
                    const announcementsHtml = data.announcements.length > 0
                        ? data.announcements.map(ann =>
                            `<div class="list-group-item">
                                <strong>${ann.title}</strong><br>
                                ${ann.content}<br>
                                <small>${new Date(ann.created_at).toLocaleDateString()}</small>
                            </div>`
                        ).join('')
                        : '<div class="list-group-item">No announcements</div>';
                    document.getElementById('announcements').innerHTML = announcementsHtml;
                })
                .catch(error => console.error('Error loading stats:', error));
        }

        // Auto-refresh stats every 5 minutes
        setInterval(loadStats, 300000);
    </script>
</body>
</html>
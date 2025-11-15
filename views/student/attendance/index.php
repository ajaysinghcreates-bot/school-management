<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance History - Student Portal</title>
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
                    <li class="nav-item"><a class="nav-link active" href="/student/attendance">Attendance</a></li>
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
        <h1 class="mb-4">Attendance History</h1>

        <!-- Month Selector -->
        <div class="row mb-4">
            <div class="col-md-4">
                <label for="month-select" class="form-label">Select Month</label>
                <input type="month" class="form-control" id="month-select" value="<?php echo date('Y-m'); ?>">
            </div>
        </div>

        <!-- Attendance Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Attendance Records</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="attendance-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="attendance-body">
                                    <tr>
                                        <td colspan="2" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadAttendance();
            document.getElementById('month-select').addEventListener('change', loadAttendance);
        });

        function loadAttendance() {
            const month = document.getElementById('month-select').value;
            fetch(`/student/api/attendance?month=${month}`)
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('attendance-body');
                    if (data.data && data.data.length > 0) {
                        const rows = data.data.map(record => `
                            <tr>
                                <td>${new Date(record.date).toLocaleDateString()}</td>
                                <td>
                                    <span class="badge ${record.status === 'present' ? 'bg-success' : record.status === 'absent' ? 'bg-danger' : 'bg-warning'}">
                                        ${record.status.charAt(0).toUpperCase() + record.status.slice(1)}
                                    </span>
                                </td>
                            </tr>
                        `).join('');
                        tbody.innerHTML = rows;
                    } else {
                        tbody.innerHTML = '<tr><td colspan="2" class="text-center">No attendance records found</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error loading attendance:', error);
                    document.getElementById('attendance-body').innerHTML = '<tr><td colspan="2" class="text-center text-danger">Error loading data</td></tr>';
                });
        }
    </script>
</body>
</html>
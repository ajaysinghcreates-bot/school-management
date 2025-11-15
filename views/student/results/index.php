<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Results - Student Portal</title>
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
                    <li class="nav-item"><a class="nav-link active" href="/student/results">Results</a></li>
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
        <h1 class="mb-4">Exam Results</h1>

        <!-- Results Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Your Exam Results</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="results-table">
                                <thead>
                                    <tr>
                                        <th>Exam Title</th>
                                        <th>Subject</th>
                                        <th>Exam Date</th>
                                        <th>Marks Obtained</th>
                                        <th>Total Marks</th>
                                        <th>Grade</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody id="results-body">
                                    <tr>
                                        <td colspan="7" class="text-center">Loading...</td>
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
            loadResults();
        });

        function loadResults() {
            fetch('/student/api/results')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('results-body');
                    if (data.data && data.data.length > 0) {
                        const rows = data.data.map(result => `
                            <tr>
                                <td>${result.title}</td>
                                <td>${result.subject_name}</td>
                                <td>${new Date(result.exam_date).toLocaleDateString()}</td>
                                <td>${result.marks_obtained}</td>
                                <td>${result.total_marks || 'N/A'}</td>
                                <td>
                                    <span class="badge ${getGradeColor(result.grade)}">${result.grade}</span>
                                </td>
                                <td>${result.remarks || '-'}</td>
                            </tr>
                        `).join('');
                        tbody.innerHTML = rows;
                    } else {
                        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No results found</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error loading results:', error);
                    document.getElementById('results-body').innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>';
                });
        }

        function getGradeColor(grade) {
            if (!grade) return 'bg-secondary';
            const g = grade.toUpperCase();
            if (g === 'A' || g === 'A+') return 'bg-success';
            if (g === 'B' || g === 'B+') return 'bg-primary';
            if (g === 'C' || g === 'C+') return 'bg-warning';
            if (g === 'D' || g === 'F') return 'bg-danger';
            return 'bg-secondary';
        }
    </script>
</body>
</html>
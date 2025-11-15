<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance - Parent Portal</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/parent/dashboard">Parent Portal</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/parent/dashboard">Dashboard</a>
                <a class="nav-link active" href="/parent/attendance">Attendance</a>
                <a class="nav-link" href="/logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Child Attendance</h1>

        <?php if (isset($child)): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5><?php echo htmlspecialchars($child['first_name'] . ' ' . $child['last_name']); ?> - <?php echo htmlspecialchars($child['class_name']); ?></h5>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Attendance Records</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendance as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['formatted_date']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $record['status'] === 'present' ? 'success' : ($record['status'] === 'absent' ? 'danger' : 'warning'); ?>">
                                        <?php echo ucfirst($record['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($record['remarks'] ?? ''); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            Please select a child to view attendance records.
        </div>
        <?php endif; ?>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
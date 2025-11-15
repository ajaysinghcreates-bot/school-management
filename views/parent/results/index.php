<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results - Parent Portal</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/parent/dashboard">Parent Portal</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/parent/dashboard">Dashboard</a>
                <a class="nav-link active" href="/parent/results">Results</a>
                <a class="nav-link" href="/logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Child Results</h1>

        <?php if (isset($child)): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5><?php echo htmlspecialchars($child['first_name'] . ' ' . $child['last_name']); ?> - <?php echo htmlspecialchars($child['class_name']); ?></h5>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Exam Results</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Exam</th>
                                <th>Subject</th>
                                <th>Marks Obtained</th>
                                <th>Total Marks</th>
                                <th>Grade</th>
                                <th>Percentage</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $result): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($result['exam_title']); ?></td>
                                <td><?php echo htmlspecialchars($result['subject_name']); ?></td>
                                <td><?php echo htmlspecialchars($result['marks_obtained']); ?></td>
                                <td><?php echo htmlspecialchars($result['total_marks']); ?></td>
                                <td><?php echo htmlspecialchars($result['grade']); ?></td>
                                <td><?php echo htmlspecialchars($result['percentage']); ?>%</td>
                                <td><?php echo htmlspecialchars($result['exam_date']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            Please select a child to view results.
        </div>
        <?php endif; ?>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
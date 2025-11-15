<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Children - Parent Portal</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/parent/dashboard">Parent Portal</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/parent/dashboard">Dashboard</a>
                <a class="nav-link active" href="/parent/children">My Children</a>
                <a class="nav-link" href="/logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>My Children</h1>
        <div class="row">
            <?php foreach ($children as $child): ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($child['first_name'] . ' ' . $child['last_name']); ?></h5>
                        <p class="card-text">
                            <strong>Scholar Number:</strong> <?php echo htmlspecialchars($child['scholar_number']); ?><br>
                            <strong>Class:</strong> <?php echo htmlspecialchars($child['class_name']); ?><br>
                            <strong>Roll Number:</strong> <?php echo htmlspecialchars($child['roll_number']); ?><br>
                            <strong>Date of Birth:</strong> <?php echo htmlspecialchars($child['dob']); ?><br>
                            <strong>Status:</strong> <?php echo ucfirst($child['status']); ?>
                        </p>
                        <a href="/parent/attendance?child_id=<?php echo $child['id']; ?>" class="btn btn-primary">View Attendance</a>
                        <a href="/parent/results?child_id=<?php echo $child['id']; ?>" class="btn btn-success">View Results</a>
                        <a href="/parent/fees?child_id=<?php echo $child['id']; ?>" class="btn btn-warning">View Fees</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fees - Parent Portal</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/parent/dashboard">Parent Portal</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/parent/dashboard">Dashboard</a>
                <a class="nav-link active" href="/parent/fees">Fees</a>
                <a class="nav-link" href="/logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Child Fees</h1>

        <?php if (isset($child)): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5><?php echo htmlspecialchars($child['first_name'] . ' ' . $child['last_name']); ?> - <?php echo htmlspecialchars($child['class_name']); ?></h5>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Fee Records</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fee Type</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Discount</th>
                                <th>Scholarship</th>
                                <th>Fine</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fees as $fee): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fee['fee_type']); ?> (<?php echo htmlspecialchars($fee['frequency']); ?>)</td>
                                <td>₹<?php echo htmlspecialchars($fee['amount']); ?></td>
                                <td><?php echo htmlspecialchars($fee['due_date']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $fee['status'] === 'paid' ? 'success' : ($fee['status'] === 'pending' ? 'warning' : 'danger'); ?>">
                                        <?php echo ucfirst($fee['status']); ?>
                                    </span>
                                </td>
                                <td>₹<?php echo htmlspecialchars($fee['discount_amount']); ?></td>
                                <td>₹<?php echo htmlspecialchars($fee['scholarship_amount']); ?></td>
                                <td>₹<?php echo htmlspecialchars($fee['fine_amount']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            Please select a child to view fee records.
        </div>
        <?php endif; ?>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
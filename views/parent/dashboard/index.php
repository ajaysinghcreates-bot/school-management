<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard - School Management</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .summary-card { transition: transform 0.2s; }
        .summary-card:hover { transform: translateY(-5px); }
        .child-card { margin-bottom: 1rem; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/parent/dashboard">School Management - Parent Portal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="/parent/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/parent/children">My Children</a></li>
                    <li class="nav-item"><a class="nav-link" href="/parent/attendance">Attendance</a></li>
                    <li class="nav-item"><a class="nav-link" href="/parent/results">Results</a></li>
                    <li class="nav-item"><a class="nav-link" href="/parent/fees">Fees</a></li>
                    <li class="nav-item"><a class="nav-link" href="/parent/events">Events</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="/parent/profile">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <h1 class="mb-4">Parent Dashboard</h1>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card summary-card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">My Children</h5>
                        <h2 id="total-children"><?php echo count($children); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card summary-card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Pending Fees</h5>
                        <h2 id="pending-fees">₹0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card summary-card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Upcoming Events</h5>
                        <h2 id="upcoming-events"><?php echo count($this->getUpcomingEvents()); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card summary-card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Notifications</h5>
                        <h2 id="unread-notifications"><?php echo count($notifications); ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Children Overview -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>My Children</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($children as $child): ?>
                            <div class="col-md-4 mb-3">
                                <div class="card child-card">
                                    <div class="card-body">
                                        <h6 class="card-title"><?php echo htmlspecialchars($child['first_name'] . ' ' . $child['last_name']); ?></h6>
                                        <p class="card-text">
                                            <strong>Class:</strong> <?php echo htmlspecialchars($child['class_name']); ?><br>
                                            <strong>Roll No:</strong> <?php echo htmlspecialchars($child['roll_number']); ?><br>
                                            <strong>Status:</strong> <?php echo ucfirst($child['status']); ?>
                                        </p>
                                        <div class="btn-group btn-group-sm">
                                            <a href="/parent/attendance?child_id=<?php echo $child['id']; ?>" class="btn btn-outline-primary">Attendance</a>
                                            <a href="/parent/results?child_id=<?php echo $child['id']; ?>" class="btn btn-outline-success">Results</a>
                                            <a href="/parent/fees?child_id=<?php echo $child['id']; ?>" class="btn btn-outline-warning">Fees</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Notifications -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Notifications</h5>
                    </div>
                    <div class="card-body">
                        <div id="notifications-list">
                            <?php if (empty($notifications)): ?>
                            <p class="text-muted">No recent notifications</p>
                            <?php else: ?>
                                <?php foreach ($notifications as $notification): ?>
                                <div class="alert alert-<?php echo $notification['type'] === 'error' ? 'danger' : $notification['type']; ?> alert-dismissible fade show">
                                    <strong><?php echo htmlspecialchars($notification['title']); ?></strong><br>
                                    <?php echo htmlspecialchars($notification['message']); ?>
                                    <small class="text-muted d-block mt-1">
                                        <?php echo date('d M Y, H:i', strtotime($notification['created_at'])); ?>
                                    </small>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/parent/children" class="btn btn-primary">View All Children</a>
                            <a href="/parent/events" class="btn btn-success">School Events</a>
                            <a href="/parent/profile" class="btn btn-info">Update Profile</a>
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
            fetch('/parent/api/dashboard-stats')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('total-children').textContent = data.total_children;
                    document.getElementById('pending-fees').textContent = '₹' + data.pending_fees;
                    document.getElementById('upcoming-events').textContent = data.upcoming_events;
                    document.getElementById('unread-notifications').textContent = data.unread_notifications;
                })
                .catch(error => console.error('Error loading stats:', error));
        }
    </script>
</body>
</html>
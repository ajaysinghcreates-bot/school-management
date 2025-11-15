<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - Parent Portal</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/parent/dashboard">Parent Portal</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/parent/dashboard">Dashboard</a>
                <a class="nav-link active" href="/parent/events">Events</a>
                <a class="nav-link" href="/logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>School Events</h1>

        <div class="row">
            <?php foreach ($events as $event): ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($event['description']); ?></p>
                        <p class="card-text">
                            <small class="text-muted">
                                <strong>Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?><br>
                                <strong>Time:</strong> <?php echo htmlspecialchars($event['start_time'] . ' - ' . $event['end_time']); ?><br>
                                <strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?><br>
                                <strong>Type:</strong> <?php echo ucfirst($event['event_type']); ?>
                            </small>
                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($events)): ?>
        <div class="alert alert-info">
            No upcoming events at this time.
        </div>
        <?php endif; ?>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
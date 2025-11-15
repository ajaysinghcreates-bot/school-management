<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - School Management</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
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
                    <li class="nav-item"><a class="nav-link" href="/admin/exams">Exams</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/fees">Fees</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/events">Events</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/gallery">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/reports">Reports</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/admin/settings">Settings</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <h1>System Settings</h1>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">General Settings</div>
                    <div class="card-body">
                        <form id="settingsForm">
                            <div class="mb-3">
                                <label for="school_name" class="form-label">School Name</label>
                                <input type="text" class="form-control" id="school_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="school_address" class="form-label">School Address</label>
                                <textarea class="form-control" id="school_address" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="school_phone" class="form-label">School Phone</label>
                                <input type="text" class="form-control" id="school_phone">
                            </div>
                            <div class="mb-3">
                                <label for="school_email" class="form-label">School Email</label>
                                <input type="email" class="form-control" id="school_email">
                            </div>
                            <div class="mb-3">
                                <label for="academic_year" class="form-label">Current Academic Year</label>
                                <input type="text" class="form-control" id="academic_year" placeholder="2024-2025">
                            </div>
                            <div class="mb-3">
                                <label for="fee_reminder_days" class="form-label">Fee Reminder Days Before Due</label>
                                <input type="number" class="form-control" id="fee_reminder_days" min="1" max="30">
                            </div>
                            <button type="button" class="btn btn-primary" onclick="saveSettings()">Save Settings</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">System Information</div>
                    <div class="card-body">
                        <p><strong>Version:</strong> 1.0.0</p>
                        <p><strong>Last Updated:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
                        <p><strong>Database:</strong> MySQL</p>
                        <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadSettings();
        });

        function loadSettings() {
            fetch('/admin/api/settings')
                .then(response => response.json())
                .then(data => {
                    data.forEach(setting => {
                        const element = document.getElementById(setting.key);
                        if (element) {
                            element.value = setting.value;
                        }
                    });
                })
                .catch(error => console.error('Error loading settings:', error));
        }

        function saveSettings() {
            const settings = {};
            const form = document.getElementById('settingsForm');
            const inputs = form.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                settings[input.id] = input.value;
            });

            fetch('/admin/api/settings/update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(settings)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Settings saved successfully!');
                } else {
                    alert('Error saving settings: ' + result.error);
                }
            })
            .catch(error => console.error('Error saving settings:', error));
        }
    </script>
</body>
</html>
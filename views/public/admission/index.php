<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission - School Management System</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .section-padding {
            padding: 80px 0;
        }
        .admission-step {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .step-number {
            width: 40px;
            height: 40px;
            background: #007bff;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <nav class="navbar navbar-expand-lg navbar-light container">
            <a class="navbar-brand" href="/">
                <img src="assets/images/logo.png" alt="School Logo" height="50" class="me-2">
                <span class="fw-bold">School Management</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="/about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="/courses">Courses</a></li>
                    <li class="nav-item"><a class="nav-link" href="/events">Events</a></li>
                    <li class="nav-item"><a class="nav-link" href="/gallery">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="/contact">Contact</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/admission">Admission</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Application Process Section -->
    <section class="section-padding">
        <div class="container">
            <h2 class="text-center mb-5">Admission Process</h2>
            <div id="process-content">
                <!-- Process content will be loaded dynamically -->
                <p>Loading...</p>
            </div>
        </div>
    </section>

    <!-- Requirements Section -->
    <section class="section-padding bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Admission Requirements</h2>
            <div id="requirements-content">
                <!-- Requirements will be loaded dynamically -->
                <p>Loading...</p>
            </div>
        </div>
    </section>

    <!-- Fee Structure Section -->
    <section class="section-padding">
        <div class="container">
            <h2 class="text-center mb-5">Fee Structure</h2>
            <div id="fees-content">
                <!-- Fees will be loaded dynamically -->
                <p>Loading...</p>
            </div>
        </div>
    </section>

    <!-- Apply Now Section -->
    <section class="section-padding bg-primary text-white">
        <div class="container text-center">
            <h2>Ready to Join Us?</h2>
            <p>Start your application process today!</p>
            <a href="/contact" class="btn btn-light btn-lg">Contact Us for Application</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <img src="assets/images/logo.png" alt="School Logo" height="50" class="mb-3">
                    <p id="footer-about">Loading school information...</p>
                </div>
                <div class="col-lg-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-white">Home</a></li>
                        <li><a href="/about" class="text-white">About</a></li>
                        <li><a href="/courses" class="text-white">Courses</a></li>
                        <li><a href="/events" class="text-white">Events</a></li>
                        <li><a href="/gallery" class="text-white">Gallery</a></li>
                        <li><a href="/contact" class="text-white">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h5>Contact Info</h5>
                    <div id="contact-info">
                        <!-- Contact info will be loaded dynamically -->
                        <p>Loading...</p>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; 2024 School Management System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load admission data
        fetch('/api/admission')
            .then(response => response.json())
            .then(data => {
                document.getElementById('process-content').innerHTML = data.process || '<p>Application process details...</p>';
                document.getElementById('requirements-content').innerHTML = data.requirements || '<p>Admission requirements...</p>';
                document.getElementById('fees-content').innerHTML = data.fee_structure || '<p>Fee structure details...</p>';
            });

        // Load contact info
        fetch('/api/contact')
            .then(response => response.json())
            .then(data => {
                document.getElementById('contact-info').innerHTML = `
                    <p><i class="fas fa-map-marker-alt me-2"></i>${data.address || 'School Address'}</p>
                    <p><i class="fas fa-phone me-2"></i>${data.phone || 'Phone Number'}</p>
                    <p><i class="fas fa-envelope me-2"></i>${data.email || 'Email'}</p>
                `;
                document.getElementById('footer-about').innerHTML = data.about || 'School description...';
            });
    </script>
</body>
</html>
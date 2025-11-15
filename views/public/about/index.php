<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - School Management System</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .section-padding {
            padding: 80px 0;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }
        .mission-vision {
            background: #f8f9fa;
            padding: 40px;
            border-radius: 10px;
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
                    <li class="nav-item"><a class="nav-link active" href="/about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="/courses">Courses</a></li>
                    <li class="nav-item"><a class="nav-link" href="/events">Events</a></li>
                    <li class="nav-item"><a class="nav-link" href="/gallery">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="/contact">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admission">Admission</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- History Section -->
    <section class="section-padding">
        <div class="container">
            <h2 class="text-center mb-5">Our History</h2>
            <div id="history-content">
                <!-- History content will be loaded dynamically -->
                <p>Loading...</p>
            </div>
        </div>
    </section>

    <!-- Mission and Vision Section -->
    <section class="section-padding bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="mission-vision">
                        <h3>Our Mission</h3>
                        <div id="mission-content">
                            <!-- Mission content will be loaded dynamically -->
                            <p>Loading...</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mission-vision">
                        <h3>Our Vision</h3>
                        <div id="vision-content">
                            <!-- Vision content will be loaded dynamically -->
                            <p>Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Faculty Section -->
    <section class="section-padding">
        <div class="container">
            <h2 class="text-center mb-5">Our Faculty</h2>
            <div class="row" id="faculty-container">
                <!-- Faculty will be loaded dynamically -->
            </div>
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
        // Load about content
        fetch('/api/about')
            .then(response => response.json())
            .then(data => {
                document.getElementById('history-content').innerHTML = data.history || '<p>School history...</p>';
                document.getElementById('mission-content').innerHTML = data.mission || '<p>School mission...</p>';
                document.getElementById('vision-content').innerHTML = data.vision || '<p>School vision...</p>';
            });

        // Load faculty
        fetch('/api/faculty')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('faculty-container');
                container.innerHTML = data.map(faculty => `
                    <div class="col-md-4 mb-4">
                        <div class="card card-hover h-100">
                            <img src="${faculty.image || 'assets/images/faculty-default.jpg'}" class="card-img-top" alt="${faculty.name}">
                            <div class="card-body">
                                <h5 class="card-title">${faculty.name}</h5>
                                <p class="card-text">${faculty.position}</p>
                                <p class="card-text">${faculty.bio}</p>
                            </div>
                        </div>
                    </div>
                `).join('');
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
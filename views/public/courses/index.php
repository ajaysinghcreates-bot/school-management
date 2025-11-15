<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses - School Management System</title>
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
                    <li class="nav-item"><a class="nav-link active" href="/courses">Courses</a></li>
                    <li class="nav-item"><a class="nav-link" href="/events">Events</a></li>
                    <li class="nav-item"><a class="nav-link" href="/gallery">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="/contact">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admission">Admission</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Courses Section -->
    <section class="section-padding">
        <div class="container">
            <h2 class="text-center mb-5">Our Academic Programs</h2>
            <div class="row" id="courses-container">
                <!-- Courses will be loaded dynamically -->
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
        // Load courses
        fetch('/api/courses')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('courses-container');
                container.innerHTML = data.map(course => `
                    <div class="col-md-6 mb-4">
                        <div class="card card-hover h-100">
                            <img src="${course.image || 'assets/images/course-default.jpg'}" class="card-img-top" alt="${course.name}">
                            <div class="card-body">
                                <h5 class="card-title">${course.name}</h5>
                                <p class="card-text">${course.description}</p>
                                <h6>Curriculum:</h6>
                                <p>${course.curriculum || 'Details coming soon...'}</p>
                                <h6>Admission Requirements:</h6>
                                <p>${course.requirements || 'Details coming soon...'}</p>
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
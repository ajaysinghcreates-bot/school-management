<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-carousel {
            height: 70vh;
            min-height: 500px;
        }
        .hero-carousel img {
            object-fit: cover;
            height: 100%;
        }
        .section-padding {
            padding: 80px 0;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }
        .testimonial-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }
        .achievement-counter {
            font-size: 2.5rem;
            font-weight: bold;
            color: #007bff;
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
                    <li class="nav-item"><a class="nav-link" href="/about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="/courses">Courses</a></li>
                    <li class="nav-item"><a class="nav-link" href="/events">Events</a></li>
                    <li class="nav-item"><a class="nav-link" href="/gallery">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="/contact">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admission">Admission</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <?php if (!empty($heroContent)): ?>
    <section class="hero-carousel bg-primary text-white d-flex align-items-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4"><?php echo htmlspecialchars($heroContent[0]['title']); ?></h1>
                    <p class="lead mb-4"><?php echo htmlspecialchars($heroContent[0]['content']); ?></p>
                    <?php if (!empty($heroContent[0]['link_url']) && !empty($heroContent[0]['link_text'])): ?>
                    <a href="<?php echo htmlspecialchars($heroContent[0]['link_url']); ?>" class="btn btn-light btn-lg"><?php echo htmlspecialchars($heroContent[0]['link_text']); ?></a>
                    <?php endif; ?>
                </div>
                <div class="col-lg-6">
                    <?php if (!empty($heroContent[0]['image_path'])): ?>
                    <img src="<?php echo htmlspecialchars($heroContent[0]['image_path']); ?>" alt="Hero Image" class="img-fluid rounded">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- About Section -->
    <?php if (!empty($aboutContent)): ?>
    <section class="section-padding bg-light" id="about-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h2><?php echo htmlspecialchars($aboutContent[0]['title']); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars($aboutContent[0]['content'])); ?></p>
                    <?php if (!empty($aboutContent[0]['link_url']) && !empty($aboutContent[0]['link_text'])): ?>
                    <a href="<?php echo htmlspecialchars($aboutContent[0]['link_url']); ?>" class="btn btn-primary"><?php echo htmlspecialchars($aboutContent[0]['link_text']); ?></a>
                    <?php endif; ?>
                </div>
                <div class="col-lg-6">
                    <img src="<?php echo htmlspecialchars($aboutContent[0]['image_path'] ?? 'assets/images/about.jpg'); ?>" alt="About School" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Courses Section -->
    <?php if (!empty($coursesContent)): ?>
    <section class="section-padding" id="courses-section">
        <div class="container">
            <h2 class="text-center mb-5"><?php echo htmlspecialchars($coursesContent[0]['title']); ?></h2>
            <p class="text-center mb-5"><?php echo htmlspecialchars($coursesContent[0]['content']); ?></p>
            <div class="text-center">
                <?php if (!empty($coursesContent[0]['link_url']) && !empty($coursesContent[0]['link_text'])): ?>
                <a href="<?php echo htmlspecialchars($coursesContent[0]['link_url']); ?>" class="btn btn-primary btn-lg"><?php echo htmlspecialchars($coursesContent[0]['link_text']); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Events Section -->
    <?php if (!empty($eventsContent)): ?>
    <section class="section-padding bg-light" id="events-section">
        <div class="container">
            <h2 class="text-center mb-5"><?php echo htmlspecialchars($eventsContent[0]['title']); ?></h2>
            <p class="text-center mb-5"><?php echo htmlspecialchars($eventsContent[0]['content']); ?></p>
            <div class="text-center">
                <?php if (!empty($eventsContent[0]['link_url']) && !empty($eventsContent[0]['link_text'])): ?>
                <a href="<?php echo htmlspecialchars($eventsContent[0]['link_url']); ?>" class="btn btn-primary btn-lg"><?php echo htmlspecialchars($eventsContent[0]['link_text']); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Achievements Section -->
    <?php if (!empty($achievementsContent)): ?>
    <section class="section-padding" id="achievements-section">
        <div class="container">
            <h2 class="text-center mb-5"><?php echo htmlspecialchars($achievementsContent[0]['title']); ?></h2>
            <p class="text-center mb-5"><?php echo htmlspecialchars($achievementsContent[0]['content']); ?></p>
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="achievement-counter">500+</div>
                    <h5>Students</h5>
                </div>
                <div class="col-md-3">
                    <div class="achievement-counter">50+</div>
                    <h5>Teachers</h5>
                </div>
                <div class="col-md-3">
                    <div class="achievement-counter">95%</div>
                    <h5>Pass Rate</h5>
                </div>
                <div class="col-md-3">
                    <div class="achievement-counter">10+</div>
                    <h5>Years</h5>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Gallery Section -->
    <?php if (!empty($galleryContent)): ?>
    <section class="section-padding bg-light" id="gallery-section">
        <div class="container">
            <h2 class="text-center mb-5"><?php echo htmlspecialchars($galleryContent[0]['title']); ?></h2>
            <p class="text-center mb-5"><?php echo htmlspecialchars($galleryContent[0]['content']); ?></p>
            <div class="text-center">
                <?php if (!empty($galleryContent[0]['link_url']) && !empty($galleryContent[0]['link_text'])): ?>
                <a href="<?php echo htmlspecialchars($galleryContent[0]['link_url']); ?>" class="btn btn-primary btn-lg"><?php echo htmlspecialchars($galleryContent[0]['link_text']); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Testimonials Section -->
    <?php if (!empty($testimonialsContent)): ?>
    <section class="section-padding" id="testimonials-section">
        <div class="container">
            <h2 class="text-center mb-5"><?php echo htmlspecialchars($testimonialsContent[0]['title']); ?></h2>
            <p class="text-center mb-5"><?php echo htmlspecialchars($testimonialsContent[0]['content']); ?></p>
            <div class="row">
                <div class="col-md-4">
                    <div class="testimonial-card text-center">
                        <i class="fas fa-quote-left fa-2x text-primary mb-3"></i>
                        <p>"This school has provided an excellent learning environment for my child."</p>
                        <h6>Parent Name</h6>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card text-center">
                        <i class="fas fa-quote-left fa-2x text-primary mb-3"></i>
                        <p>"The teachers are dedicated and the facilities are world-class."</p>
                        <h6>Student Name</h6>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card text-center">
                        <i class="fas fa-quote-left fa-2x text-primary mb-3"></i>
                        <p>"Outstanding academic programs and extracurricular activities."</p>
                        <h6>Alumni Name</h6>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Call-to-Action Section -->
    <?php if (!empty($ctaContent)): ?>
    <section class="section-padding bg-primary text-white">
        <div class="container text-center">
            <h2><?php echo htmlspecialchars($ctaContent[0]['title']); ?></h2>
            <p><?php echo htmlspecialchars($ctaContent[0]['content']); ?></p>
            <?php if (!empty($ctaContent[0]['link_url']) && !empty($ctaContent[0]['link_text'])): ?>
            <a href="<?php echo htmlspecialchars($ctaContent[0]['link_url']); ?>" class="btn btn-light btn-lg"><?php echo htmlspecialchars($ctaContent[0]['link_text']); ?></a>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

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
        // Load carousel images
        fetch('/api/carousel')
            .then(response => response.json())
            .then(data => {
                const carouselInner = document.getElementById('carousel-inner');
                if (data.length > 0) {
                    data.forEach((image, index) => {
                        const item = document.createElement('div');
                        item.className = 'carousel-item' + (index === 0 ? ' active' : '');
                        item.innerHTML = `
                            <img src="${image.image_path}" class="d-block w-100" alt="${image.title}">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>${image.title}</h5>
                                <p>${image.description || ''}</p>
                            </div>
                        `;
                        carouselInner.appendChild(item);
                    });
                } else {
                    carouselInner.innerHTML = '<div class="carousel-item active"><img src="assets/images/default-carousel.jpg" class="d-block w-100" alt="Default"><div class="carousel-caption d-none d-md-block"><h5>Welcome to Our School</h5></div></div>';
                }
            });

        // Load about content
        fetch('/api/about')
            .then(response => response.json())
            .then(data => {
                document.getElementById('about-content').innerHTML = data.content || '<p>Welcome to our school...</p>';
            });

        // Load courses
        fetch('/api/courses')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('courses-container');
                container.innerHTML = data.map(course => `
                    <div class="col-md-4 mb-4">
                        <div class="card card-hover h-100">
                            <img src="${course.image || 'assets/images/course-default.jpg'}" class="card-img-top" alt="${course.name}">
                            <div class="card-body">
                                <h5 class="card-title">${course.name}</h5>
                                <p class="card-text">${course.description}</p>
                            </div>
                        </div>
                    </div>
                `).join('');
            });

        // Load events
        fetch('/api/events')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('events-container');
                container.innerHTML = data.map(event => `
                    <div class="col-md-4 mb-4">
                        <div class="card card-hover h-100">
                            <img src="${event.image || 'assets/images/event-default.jpg'}" class="card-img-top" alt="${event.title}">
                            <div class="card-body">
                                <h5 class="card-title">${event.title}</h5>
                                <p class="card-text">${new Date(event.date).toLocaleDateString()}</p>
                                <p class="card-text">${event.description}</p>
                            </div>
                        </div>
                    </div>
                `).join('');
            });

        // Load achievements
        fetch('/api/achievements')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('achievements-container');
                container.innerHTML = data.map(achievement => `
                    <div class="col-md-3 mb-4 text-center">
                        <div class="achievement-counter">${achievement.count || '100+'}</div>
                        <h5>${achievement.title}</h5>
                        <p>${achievement.description}</p>
                    </div>
                `).join('');
            });

        // Load gallery
        fetch('/api/gallery')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('gallery-container');
                container.innerHTML = data.map(item => `
                    <div class="col-md-3 mb-4">
                        <img src="${item.image_path}" class="img-fluid rounded" alt="${item.title}">
                    </div>
                `).join('');
            });

        // Load testimonials
        fetch('/api/testimonials')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('testimonials-container');
                container.innerHTML = data.map(testimonial => `
                    <div class="col-md-4 mb-4">
                        <div class="testimonial-card text-center">
                            <img src="${testimonial.image || 'assets/images/testimonial-default.jpg'}" class="rounded-circle mb-3" width="80" height="80" alt="${testimonial.name}">
                            <p>"${testimonial.message}"</p>
                            <h6>${testimonial.name}</h6>
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
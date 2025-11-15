<?php
class PublicController {
    private $db;
    private $homepageContent;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->homepageContent = new HomepageContent();
    }

    public function home() {
        // Load dynamic homepage content
        $heroContent = $this->homepageContent->getBySection('hero');
        $aboutContent = $this->homepageContent->getBySection('about');
        $coursesContent = $this->homepageContent->getBySection('courses');
        $eventsContent = $this->homepageContent->getBySection('events');
        $achievementsContent = $this->homepageContent->getBySection('achievements');
        $galleryContent = $this->homepageContent->getBySection('gallery');
        $testimonialsContent = $this->homepageContent->getBySection('testimonials');
        $ctaContent = $this->homepageContent->getBySection('cta');

        // Render the homepage view with dynamic content
        include 'views/public/homepage/index.php';
    }

    // API methods for AJAX
    public function getCarouselImages() {
        header('Content-Type: application/json');
        try {
            $stmt = $this->db->query("SELECT * FROM carousel_images ORDER BY id DESC");
            $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($images);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getEvents() {
        header('Content-Type: application/json');
        try {
            $stmt = $this->db->query("SELECT * FROM events ORDER BY date DESC LIMIT 6");
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($events);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getCourses() {
        header('Content-Type: application/json');
        try {
            $stmt = $this->db->query("SELECT * FROM courses ORDER BY id DESC LIMIT 6");
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($courses);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getGallery() {
        header('Content-Type: application/json');
        try {
            $stmt = $this->db->query("SELECT * FROM gallery ORDER BY id DESC LIMIT 8");
            $gallery = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($gallery);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getTestimonials() {
        header('Content-Type: application/json');
        try {
            $stmt = $this->db->query("SELECT * FROM testimonials ORDER BY id DESC LIMIT 3");
            $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($testimonials);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getAchievements() {
        header('Content-Type: application/json');
        try {
            $stmt = $this->db->query("SELECT * FROM achievements ORDER BY id DESC LIMIT 4");
            $achievements = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($achievements);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getAbout() {
        header('Content-Type: application/json');
        try {
            $stmt = $this->db->query("SELECT * FROM about LIMIT 1");
            $about = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($about);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getContact() {
        header('Content-Type: application/json');
        try {
            $stmt = $this->db->query("SELECT * FROM contact LIMIT 1");
            $contact = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($contact);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function about() {
        include 'views/public/about/index.php';
    }

    public function courses() {
        include 'views/public/courses/index.php';
    }

    public function events() {
        include 'views/public/events/index.php';
    }

    public function gallery() {
        include 'views/public/gallery/index.php';
    }

    public function contact() {
        include 'views/public/contact/index.php';
    }

    public function admission() {
        include 'views/public/admission/index.php';
    }

    public function getAdmission() {
        header('Content-Type: application/json');
        try {
            $stmt = $this->db->query("SELECT * FROM admission LIMIT 1");
            $admission = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($admission);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getFaculty() {
        header('Content-Type: application/json');
        try {
            $stmt = $this->db->query("SELECT * FROM faculty ORDER BY id DESC");
            $faculty = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($faculty);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
?>
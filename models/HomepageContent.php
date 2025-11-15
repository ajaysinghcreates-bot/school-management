<?php
class HomepageContent {
    protected $db;
    protected $table = 'homepage_content';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllActive() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY display_order ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBySection($section) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE section = ? AND is_active = 1 ORDER BY display_order ASC");
        $stmt->execute([$section]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSection($section) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE section = ? ORDER BY display_order ASC");
        $stmt->execute([$section]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update($this->table, $data, 'id = ?', [$id]);
    }

    public function delete($id) {
        return $this->db->delete($this->table, 'id = ?', [$id]);
    }

    public function getAllSections() {
        $stmt = $this->db->query("SELECT DISTINCT section FROM {$this->table} ORDER BY section");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getContentById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Predefined sections for the homepage
    public static function getDefaultSections() {
        return [
            'hero' => [
                'title' => 'Hero Section',
                'description' => 'Main banner/hero section of the homepage'
            ],
            'about' => [
                'title' => 'About Section',
                'description' => 'School introduction and overview'
            ],
            'courses' => [
                'title' => 'Courses Section',
                'description' => 'Academic programs and courses offered'
            ],
            'events' => [
                'title' => 'Events Section',
                'description' => 'Upcoming school events and activities'
            ],
            'achievements' => [
                'title' => 'Achievements Section',
                'description' => 'School achievements and awards'
            ],
            'gallery' => [
                'title' => 'Gallery Section',
                'description' => 'Photo gallery showcasing school activities'
            ],
            'testimonials' => [
                'title' => 'Testimonials Section',
                'description' => 'Student and parent testimonials'
            ],
            'cta' => [
                'title' => 'Call to Action',
                'description' => 'Contact/admission call to action section'
            ],
            'footer' => [
                'title' => 'Footer Section',
                'description' => 'Footer content and links'
            ]
        ];
    }

    // Initialize default homepage content
    public function initializeDefaultContent() {
        $defaultContent = [
            [
                'section' => 'hero',
                'title' => 'Welcome to Our School',
                'content' => 'Providing quality education and shaping future leaders since 1990. Join our community of learners and achievers.',
                'image_path' => '/assets/images/hero-bg.jpg',
                'link_url' => '/admission',
                'link_text' => 'Apply Now',
                'display_order' => 1,
                'is_active' => 1
            ],
            [
                'section' => 'about',
                'title' => 'About Our School',
                'content' => 'Our school is committed to providing a nurturing environment where students can develop academically, socially, and emotionally. With experienced faculty and modern facilities, we prepare students for success in an ever-changing world.',
                'display_order' => 2,
                'is_active' => 1
            ],
            [
                'section' => 'courses',
                'title' => 'Academic Programs',
                'content' => 'We offer comprehensive academic programs from kindergarten through high school, including STEM, arts, and vocational training.',
                'link_url' => '/courses',
                'link_text' => 'View All Courses',
                'display_order' => 3,
                'is_active' => 1
            ],
            [
                'section' => 'events',
                'title' => 'Upcoming Events',
                'content' => 'Stay connected with our school community through various events and activities throughout the year.',
                'link_url' => '/events',
                'link_text' => 'View Events',
                'display_order' => 4,
                'is_active' => 1
            ],
            [
                'section' => 'achievements',
                'title' => 'Our Achievements',
                'content' => 'Proud of our students\' accomplishments in academics, sports, and extracurricular activities.',
                'display_order' => 5,
                'is_active' => 1
            ],
            [
                'section' => 'gallery',
                'title' => 'School Gallery',
                'content' => 'Explore our photo gallery showcasing school events, activities, and memorable moments.',
                'link_url' => '/gallery',
                'link_text' => 'View Gallery',
                'display_order' => 6,
                'is_active' => 1
            ],
            [
                'section' => 'testimonials',
                'title' => 'What Parents Say',
                'content' => 'Hear from our satisfied parents about their experience with our school.',
                'display_order' => 7,
                'is_active' => 1
            ],
            [
                'section' => 'cta',
                'title' => 'Ready to Join Our Community?',
                'content' => 'Take the first step towards your child\'s bright future. Contact us today to learn more about our admission process.',
                'link_url' => '/contact',
                'link_text' => 'Contact Us',
                'display_order' => 8,
                'is_active' => 1
            ]
        ];

        foreach ($defaultContent as $content) {
            $this->create($content);
        }
    }
}
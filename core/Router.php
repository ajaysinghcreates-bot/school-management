<?php
class Router {
    private $routes = [
        // Public routes
        '' => ['controller' => 'PublicController', 'method' => 'home'],
        'login' => ['controller' => 'AuthController', 'method' => 'login'],
        'about' => ['controller' => 'PublicController', 'method' => 'about'],
        'courses' => ['controller' => 'PublicController', 'method' => 'courses'],
        'events' => ['controller' => 'PublicController', 'method' => 'events'],
        'gallery' => ['controller' => 'PublicController', 'method' => 'gallery'],
        'contact' => ['controller' => 'PublicController', 'method' => 'contact'],
        'admission' => ['controller' => 'PublicController', 'method' => 'admission'],

        // API routes
        'api/carousel' => ['controller' => 'PublicController', 'method' => 'getCarouselImages'],
        'api/events' => ['controller' => 'PublicController', 'method' => 'getEvents'],
        'api/courses' => ['controller' => 'PublicController', 'method' => 'getCourses'],
        'api/gallery' => ['controller' => 'PublicController', 'method' => 'getGallery'],
        'api/testimonials' => ['controller' => 'PublicController', 'method' => 'getTestimonials'],
        'api/achievements' => ['controller' => 'PublicController', 'method' => 'getAchievements'],
        'api/about' => ['controller' => 'PublicController', 'method' => 'getAbout'],
        'api/contact' => ['controller' => 'PublicController', 'method' => 'getContact'],
        'api/admission' => ['controller' => 'PublicController', 'method' => 'getAdmission'],
        'api/faculty' => ['controller' => 'PublicController', 'method' => 'getFaculty'],

        // Admin routes
        'admin/dashboard' => ['controller' => 'AdminController', 'method' => 'dashboard'],
        'admin/students' => ['controller' => 'AdminController', 'method' => 'students'],
        'admin/teachers' => ['controller' => 'AdminController', 'method' => 'teachers'],
        'admin/classes' => ['controller' => 'AdminController', 'method' => 'classes'],
        'admin/attendance' => ['controller' => 'AdminController', 'method' => 'attendance'],
        'admin/exams' => ['controller' => 'AdminController', 'method' => 'exams'],
        'admin/fees' => ['controller' => 'AdminController', 'method' => 'fees'],
        'admin/events' => ['controller' => 'AdminController', 'method' => 'events'],
        'admin/gallery' => ['controller' => 'AdminController', 'method' => 'gallery'],
        'admin/homepage' => ['controller' => 'AdminController', 'method' => 'homepage'],
        'admin/reports' => ['controller' => 'AdminController', 'method' => 'reports'],
        'admin/settings' => ['controller' => 'AdminController', 'method' => 'settings'],

        // Admin API routes
        'admin/api/stats' => ['controller' => 'AdminController', 'method' => 'getStats'],
        'admin/api/chart-data' => ['controller' => 'AdminController', 'method' => 'getChartData'],
        'admin/api/attendance-trend' => ['controller' => 'AdminController', 'method' => 'getAttendanceTrend'],
        'admin/api/notifications' => ['controller' => 'AdminController', 'method' => 'getNotifications'],

        // Students API
        'admin/api/students' => ['controller' => 'AdminController', 'method' => 'getStudentsData'],
        'admin/api/students/create' => ['controller' => 'AdminController', 'method' => 'createStudent'],
        'admin/api/students/update' => ['controller' => 'AdminController', 'method' => 'updateStudent'],
        'admin/api/students/delete' => ['controller' => 'AdminController', 'method' => 'deleteStudent'],
        'admin/api/students/bulk-import' => ['controller' => 'AdminController', 'method' => 'bulkImportStudents'],

        // Teachers API
        'admin/api/teachers' => ['controller' => 'AdminController', 'method' => 'getTeachersData'],
        'admin/api/teachers/create' => ['controller' => 'AdminController', 'method' => 'createTeacher'],
        'admin/api/teachers/update' => ['controller' => 'AdminController', 'method' => 'updateTeacher'],
        'admin/api/teachers/delete' => ['controller' => 'AdminController', 'method' => 'deleteTeacher'],

        // Classes API
        'admin/api/classes' => ['controller' => 'AdminController', 'method' => 'getClassesData'],
        'admin/api/classes/create' => ['controller' => 'AdminController', 'method' => 'createClass'],
        'admin/api/classes/update' => ['controller' => 'AdminController', 'method' => 'updateClass'],
        'admin/api/classes/delete' => ['controller' => 'AdminController', 'method' => 'deleteClass'],

        // Subjects API
        'admin/api/subjects' => ['controller' => 'AdminController', 'method' => 'getSubjectsData'],
        'admin/api/subjects/create' => ['controller' => 'AdminController', 'method' => 'createSubject'],
        'admin/api/subjects/update' => ['controller' => 'AdminController', 'method' => 'updateSubject'],
        'admin/api/subjects/delete' => ['controller' => 'AdminController', 'method' => 'deleteSubject'],

        // Attendance API
        'admin/api/attendance' => ['controller' => 'AdminController', 'method' => 'getAttendanceData'],
        'admin/api/attendance/create' => ['controller' => 'AdminController', 'method' => 'createAttendance'],
        'admin/api/attendance/update' => ['controller' => 'AdminController', 'method' => 'updateAttendance'],
        'admin/api/attendance/delete' => ['controller' => 'AdminController', 'method' => 'deleteAttendance'],

        // Exams API
        'admin/api/exams' => ['controller' => 'AdminController', 'method' => 'getExamsData'],
        'admin/api/exams/create' => ['controller' => 'AdminController', 'method' => 'createExam'],
        'admin/api/exams/update' => ['controller' => 'AdminController', 'method' => 'updateExam'],
        'admin/api/exams/delete' => ['controller' => 'AdminController', 'method' => 'deleteExam'],

        // Results API
        'admin/api/results' => ['controller' => 'AdminController', 'method' => 'getResultsData'],
        'admin/api/results/create' => ['controller' => 'AdminController', 'method' => 'createResult'],
        'admin/api/results/update' => ['controller' => 'AdminController', 'method' => 'updateResult'],
        'admin/api/results/delete' => ['controller' => 'AdminController', 'method' => 'deleteResult'],

        // Fees API
        'admin/api/fees' => ['controller' => 'AdminController', 'method' => 'getFeesData'],
        'admin/api/fees/create' => ['controller' => 'AdminController', 'method' => 'createFee'],
        'admin/api/fees/update' => ['controller' => 'AdminController', 'method' => 'updateFee'],
        'admin/api/fees/delete' => ['controller' => 'AdminController', 'method' => 'deleteFee'],
        'admin/api/fees/bulk-update' => ['controller' => 'AdminController', 'method' => 'bulkUpdateFees'],

        // Events API
        'admin/api/events' => ['controller' => 'AdminController', 'method' => 'getEventsData'],
        'admin/api/events/create' => ['controller' => 'AdminController', 'method' => 'createEvent'],
        'admin/api/events/update' => ['controller' => 'AdminController', 'method' => 'updateEvent'],
        'admin/api/events/delete' => ['controller' => 'AdminController', 'method' => 'deleteEvent'],

        // Gallery API
        'admin/api/gallery' => ['controller' => 'AdminController', 'method' => 'getGalleryData'],
        'admin/api/gallery/create' => ['controller' => 'AdminController', 'method' => 'createGallery'],
        'admin/api/gallery/update' => ['controller' => 'AdminController', 'method' => 'updateGallery'],
        'admin/api/gallery/delete' => ['controller' => 'AdminController', 'method' => 'deleteGallery'],

        // Homepage Content API
        'admin/api/homepage' => ['controller' => 'AdminController', 'method' => 'getHomepageData'],
        'admin/api/homepage/create' => ['controller' => 'AdminController', 'method' => 'createHomepageContent'],
        'admin/api/homepage/update' => ['controller' => 'AdminController', 'method' => 'updateHomepageContent'],
        'admin/api/homepage/delete' => ['controller' => 'AdminController', 'method' => 'deleteHomepageContent'],

        // Reports API
        'admin/api/reports/students' => ['controller' => 'AdminController', 'method' => 'getStudentReport'],
        'admin/api/reports/fees' => ['controller' => 'AdminController', 'method' => 'getFeeReport'],

        // Settings API
        'admin/api/settings' => ['controller' => 'AdminController', 'method' => 'getSettings'],
        'admin/api/settings/update' => ['controller' => 'AdminController', 'method' => 'updateSettings'],

        // Teacher routes
        'teacher/dashboard' => ['controller' => 'TeacherController', 'method' => 'dashboard'],
        'teacher/attendance' => ['controller' => 'TeacherController', 'method' => 'attendance'],
        'teacher/classes' => ['controller' => 'TeacherController', 'method' => 'classes'],
        'teacher/exams' => ['controller' => 'TeacherController', 'method' => 'exams'],
        'teacher/profile' => ['controller' => 'TeacherController', 'method' => 'profile'],

        // Teacher API routes
        'teacher/api/dashboard-stats' => ['controller' => 'TeacherController', 'method' => 'getDashboardStats'],
        'teacher/api/attendance' => ['controller' => 'TeacherController', 'method' => 'getAttendanceData'],
        'teacher/api/attendance/create' => ['controller' => 'TeacherController', 'method' => 'createAttendance'],
        'teacher/api/attendance/update' => ['controller' => 'TeacherController', 'method' => 'updateAttendance'],
        'teacher/api/classes' => ['controller' => 'TeacherController', 'method' => 'getClassesData'],
        'teacher/api/exams' => ['controller' => 'TeacherController', 'method' => 'getExamsData'],
        'teacher/api/exams/create' => ['controller' => 'TeacherController', 'method' => 'createExam'],
        'teacher/api/exams/update' => ['controller' => 'TeacherController', 'method' => 'updateExam'],
        'teacher/api/results' => ['controller' => 'TeacherController', 'method' => 'getResultsData'],
        'teacher/api/results/create' => ['controller' => 'TeacherController', 'method' => 'createResult'],
        'teacher/api/results/update' => ['controller' => 'TeacherController', 'method' => 'updateResult'],
        'teacher/api/profile' => ['controller' => 'TeacherController', 'method' => 'getProfile'],

        // Student routes
        'student/dashboard' => ['controller' => 'StudentController', 'method' => 'dashboard'],
        'student/attendance' => ['controller' => 'StudentController', 'method' => 'attendance'],
        'student/results' => ['controller' => 'StudentController', 'method' => 'results'],
        'student/fees' => ['controller' => 'StudentController', 'method' => 'fees'],
        'student/profile' => ['controller' => 'StudentController', 'method' => 'profile'],

        // Student API routes
        'student/api/dashboard-stats' => ['controller' => 'StudentController', 'method' => 'getDashboardStats'],
        'student/api/attendance' => ['controller' => 'StudentController', 'method' => 'getAttendanceData'],
        'student/api/results' => ['controller' => 'StudentController', 'method' => 'getResultsData'],
        'student/api/fees' => ['controller' => 'StudentController', 'method' => 'getFeesData'],
        'student/api/profile' => ['controller' => 'StudentController', 'method' => 'getProfile'],

        // Cashier routes
        'cashier/dashboard' => ['controller' => 'CashierController', 'method' => 'dashboard'],
        'cashier/fees' => ['controller' => 'CashierController', 'method' => 'fees'],
        'cashier/outstanding' => ['controller' => 'CashierController', 'method' => 'outstanding'],
        'cashier/reports' => ['controller' => 'CashierController', 'method' => 'reports'],
        'cashier/expenses' => ['controller' => 'CashierController', 'method' => 'expenses'],

        // Parent routes
        'parent/dashboard' => ['controller' => 'ParentController', 'method' => 'dashboard'],
        'parent/children' => ['controller' => 'ParentController', 'method' => 'children'],
        'parent/attendance' => ['controller' => 'ParentController', 'method' => 'attendance'],
        'parent/results' => ['controller' => 'ParentController', 'method' => 'results'],
        'parent/fees' => ['controller' => 'ParentController', 'method' => 'fees'],
        'parent/events' => ['controller' => 'ParentController', 'method' => 'events'],
        'parent/profile' => ['controller' => 'ParentController', 'method' => 'profile'],

        // Cashier API routes
        'cashier/api/dashboard-stats' => ['controller' => 'CashierController', 'method' => 'getDashboardStats'],
        'cashier/api/fees' => ['controller' => 'CashierController', 'method' => 'getFeesData'],
        'cashier/api/fees/process-payment' => ['controller' => 'CashierController', 'method' => 'processPayment'],
        'cashier/api/fees/bulk-import' => ['controller' => 'CashierController', 'method' => 'bulkImportPayments'],
        'cashier/receipt' => ['controller' => 'CashierController', 'method' => 'generateReceipt'],
        'cashier/api/outstanding' => ['controller' => 'CashierController', 'method' => 'getOutstandingData'],
        'cashier/api/outstanding/send-reminder' => ['controller' => 'CashierController', 'method' => 'sendReminder'],
        'cashier/api/outstanding/send-automated-reminders' => ['controller' => 'CashierController', 'method' => 'sendAutomatedReminders'],
        'cashier/api/reports/financial' => ['controller' => 'CashierController', 'method' => 'getFinancialReport'],
        'cashier/api/reports/export' => ['controller' => 'CashierController', 'method' => 'exportFinancialReport'],
        'cashier/api/analytics' => ['controller' => 'CashierController', 'method' => 'getFinancialAnalytics'],
        'cashier/payment-gateway' => ['controller' => 'CashierController', 'method' => 'paymentGateway'],
        'cashier/api/payment/initiate' => ['controller' => 'CashierController', 'method' => 'initiatePayment'],
        'cashier/api/payment/process' => ['controller' => 'CashierController', 'method' => 'processGatewayPayment'],

        // Academic Documents routes
        'cashier/documents' => ['controller' => 'CashierController', 'method' => 'documents'],
        'cashier/api/documents/marksheet' => ['controller' => 'CashierController', 'method' => 'generateMarksheet'],
        'cashier/api/documents/admit-card' => ['controller' => 'CashierController', 'method' => 'generateAdmitCard'],
        'cashier/api/documents/transfer-certificate' => ['controller' => 'CashierController', 'method' => 'generateTransferCertificate'],
        'cashier/api/documents/bulk-marksheet' => ['controller' => 'CashierController', 'method' => 'generateBulkMarksheet'],
        'cashier/api/documents/bulk-admit-card' => ['controller' => 'CashierController', 'method' => 'generateBulkAdmitCard'],
        'cashier/api/reports/export-excel' => ['controller' => 'CashierController', 'method' => 'exportFinancialReportExcel'],
        'cashier/api/expenses' => ['controller' => 'CashierController', 'method' => 'getExpensesData'],
        'cashier/api/expenses/create' => ['controller' => 'CashierController', 'method' => 'createExpense'],
        'cashier/api/expenses/update' => ['controller' => 'CashierController', 'method' => 'updateExpense'],
        'cashier/api/expenses/delete' => ['controller' => 'CashierController', 'method' => 'deleteExpense'],
        'cashier/expense-categories' => ['controller' => 'CashierController', 'method' => 'expenseCategories'],
        'cashier/api/expense-categories' => ['controller' => 'CashierController', 'method' => 'getExpenseCategories'],
        'cashier/api/expense-categories/create' => ['controller' => 'CashierController', 'method' => 'createExpenseCategory'],
        'cashier/api/expense-categories/update' => ['controller' => 'CashierController', 'method' => 'updateExpenseCategory'],
        'cashier/api/expense-categories/delete' => ['controller' => 'CashierController', 'method' => 'deleteExpenseCategory'],
    ];

    public function dispatch($path) {
        try {
            // Set security headers
            Security::setSecurityHeaders();

            if (array_key_exists($path, $this->routes)) {
                $route = $this->routes[$path];
                $controllerName = $route['controller'];
                $methodName = $route['method'];

                // Check if controller exists
                $controllerFile = 'controllers/' . $controllerName . '.php';
                if (!file_exists($controllerFile)) {
                    $this->handleError(404, "Controller $controllerName not found");
                    return;
                }

                require_once $controllerFile;

                if (!class_exists($controllerName)) {
                    $this->handleError(404, "Class $controllerName not found");
                    return;
                }

                $controller = new $controllerName();

                if (!method_exists($controller, $methodName)) {
                    $this->handleError(404, "Method $methodName not found in $controllerName");
                    return;
                }

                // Check authentication and authorization
                $this->checkAccess($route);

                // Execute controller method
                $controller->$methodName();
            } else {
                $this->handleError(404, "Route not found: $path");
            }
        } catch (Exception $e) {
            $this->handleError(500, $e->getMessage());
        }
    }

    private function checkAccess($route) {
        $auth = new Auth();

        // Routes that don't require authentication
        $publicRoutes = ['login', 'about', 'courses', 'events', 'gallery', 'contact', 'admission'];

        // API routes that might have different auth requirements
        $isApiRoute = strpos($route['controller'], 'api') !== false;

        if (!in_array($route['controller'], $publicRoutes) && !$isApiRoute) {
            if (!$auth->can('access_system')) {
                header('Location: /login');
                exit;
            }
        }

        // Role-based access control with permissions
        if (strpos($route['controller'], 'AdminController') === 0) {
            $auth->permissionCheck('admin.access');
        } elseif (strpos($route['controller'], 'TeacherController') === 0) {
            $auth->permissionCheck('teacher.access');
        } elseif (strpos($route['controller'], 'CashierController') === 0) {
            $auth->permissionCheck('cashier.access');
        } elseif (strpos($route['controller'], 'StudentController') === 0) {
            $auth->permissionCheck('student.access');
        } elseif (strpos($route['controller'], 'ParentController') === 0) {
            $auth->permissionCheck('parent.access');
        }
    }

    private function handleError($code, $message) {
        http_response_code($code);

        if ($code === 404) {
            echo "404 Not Found: $message";
        } elseif ($code === 403) {
            echo "403 Forbidden: $message";
        } elseif ($code === 500) {
            $config = require 'config/app.php';
            if ($config['debug']) {
                echo "500 Internal Server Error: $message";
            } else {
                echo "500 Internal Server Error";
            }
        }
    }
}
?>
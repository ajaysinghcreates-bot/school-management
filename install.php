<?php
// Installation script for School Management System

session_start();

// Include required files
require_once 'core/Database.php';
require_once 'core/Security.php';
require_once 'core/Session.php';
require_once 'core/Validator.php';

class Installer {
    private $db;
    private $errors = [];
    private $step = 1;

    public function __construct() {
        $this->db = null;
        $this->step = $_GET['step'] ?? 1;
    }

    public function run() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processStep();
        } else {
            $this->showStep();
        }
    }

    private function processStep() {
        switch ($this->step) {
            case 1:
                $this->processRequirements();
                break;
            case 2:
                $this->processDatabaseConfig();
                break;
            case 3:
                $this->processAdminAccount();
                break;
            case 4:
                $this->processInstallation();
                break;
        }
    }

    private function showStep() {
        $this->renderHeader();
        switch ($this->step) {
            case 1:
                $this->showRequirements();
                break;
            case 2:
                $this->showDatabaseConfig();
                break;
            case 3:
                $this->showAdminAccount();
                break;
            case 4:
                $this->showInstallation();
                break;
            case 5:
                $this->showComplete();
                break;
        }
        $this->renderFooter();
    }

    private function processRequirements() {
        // Check PHP version
        if (version_compare(PHP_VERSION, '8.1.0', '<')) {
            $this->errors[] = 'PHP 8.1 or higher is required. Current version: ' . PHP_VERSION;
        }

        // Check extensions
        $requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'session'];
        foreach ($requiredExtensions as $ext) {
            if (!extension_loaded($ext)) {
                $this->errors[] = "PHP extension '{$ext}' is required but not loaded.";
            }
        }

        // Check file permissions
        $writablePaths = ['config/', 'logs/', 'uploads/'];
        foreach ($writablePaths as $path) {
            if (!is_writable($path)) {
                $this->errors[] = "Directory '{$path}' is not writable.";
            }
        }

        if (empty($this->errors)) {
            header('Location: install.php?step=2');
            exit;
        } else {
            $this->step = 1;
            $this->showStep();
        }
    }

    private function processDatabaseConfig() {
        $host = $_POST['db_host'] ?? '';
        $database = $_POST['db_name'] ?? '';
        $username = $_POST['db_user'] ?? '';
        $password = $_POST['db_pass'] ?? '';

        if (empty($host) || empty($database) || empty($username)) {
            $this->errors[] = 'All database fields are required.';
        } else {
            try {
                $pdo = new PDO("mysql:host={$host};dbname={$database};charset=utf8mb4", $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Save config
                $config = [
                    'host' => $host,
                    'database' => $database,
                    'username' => $username,
                    'password' => $password,
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'driver' => 'mysql',
                    'port' => 3306,
                    'options' => [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ],
                    'pool' => [
                        'min_connections' => 1,
                        'max_connections' => 10,
                        'idle_timeout' => 60,
                    ],
                ];

                file_put_contents('config/database.php', "<?php\nreturn " . var_export($config, true) . ";\n");

                header('Location: install.php?step=3');
                exit;
            } catch (PDOException $e) {
                $this->errors[] = 'Database connection failed: ' . $e->getMessage();
            }
        }

        $this->step = 2;
        $this->showStep();
    }

    private function processAdminAccount() {
        $name = trim($_POST['admin_name'] ?? '');
        $email = trim($_POST['admin_email'] ?? '');
        $password = $_POST['admin_password'] ?? '';
        $confirmPassword = $_POST['admin_confirm_password'] ?? '';

        $validator = new Validator([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'confirm_password' => $confirmPassword
        ]);

        $validator->setRules([
            'name' => 'required|min:2|max:100',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'confirm_password' => 'required'
        ]);

        if (!$validator->validate()) {
            $this->errors = array_values($validator->getErrors());
        } elseif ($password !== $confirmPassword) {
            $this->errors[] = 'Passwords do not match.';
        } elseif (!Security::validatePassword($password)) {
            $this->errors[] = 'Password must be at least 8 characters and contain uppercase, lowercase, and numbers.';
        } else {
            // Save admin data to session for next step
            $_SESSION['admin_data'] = [
                'name' => $name,
                'email' => $email,
                'password' => Security::hashPassword($password)
            ];

            header('Location: install.php?step=4');
            exit;
        }

        $this->step = 3;
        $this->showStep();
    }

    private function processInstallation() {
        try {
            // Import database schema
            $schema = file_get_contents('database/schema.sql');
            $this->db = Database::getInstance()->getConnection();

            // Split schema into individual statements
            $statements = array_filter(array_map('trim', explode(';', $schema)));

            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    $this->db->exec($statement);
                }
            }

            // Create admin user
            $adminData = $_SESSION['admin_data'];
            $adminData['role'] = 'admin';
            $adminData['is_active'] = true;
            $adminData['created_at'] = date('Y-m-d H:i:s');
            $adminData['updated_at'] = date('Y-m-d H:i:s');

            $this->db->insert('users', $adminData);

            // Initialize default permissions
            require_once 'models/Permission.php';
            $permission = new Permission();
            $permission->initializeDefaultPermissions();

            // Initialize default homepage content
            require_once 'models/HomepageContent.php';
            $homepageContent = new HomepageContent();
            $homepageContent->initializeDefaultContent();

            // Create installation flag
            file_put_contents('config/installed', date('Y-m-d H:i:s'));

            header('Location: install.php?step=5');
            exit;
        } catch (Exception $e) {
            $this->errors[] = 'Installation failed: ' . $e->getMessage();
            $this->step = 4;
            $this->showStep();
        }
    }

    private function showRequirements() {
        ?>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3>Step 1: System Requirements Check</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($this->errors)): ?>
                                <div class="alert alert-danger">
                                    <h5>Please fix the following issues:</h5>
                                    <ul>
                                        <?php foreach ($this->errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <h5>Checking system requirements...</h5>
                            <div class="mb-3">
                                <strong>PHP Version:</strong>
                                <span class="badge bg-<?php echo version_compare(PHP_VERSION, '8.1.0', '>=') ? 'success' : 'danger'; ?>">
                                    <?php echo PHP_VERSION; ?> (Required: 8.1+)
                                </span>
                            </div>

                            <div class="mb-3">
                                <strong>PHP Extensions:</strong>
                                <?php
                                $extensions = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'session'];
                                foreach ($extensions as $ext) {
                                    $status = extension_loaded($ext);
                                    echo "<span class='badge bg-" . ($status ? 'success' : 'danger') . " me-1'>{$ext}</span>";
                                }
                                ?>
                            </div>

                            <div class="mb-3">
                                <strong>File Permissions:</strong>
                                <?php
                                $paths = ['config/', 'logs/', 'uploads/'];
                                foreach ($paths as $path) {
                                    $writable = is_writable($path);
                                    echo "<span class='badge bg-" . ($writable ? 'success' : 'danger') . " me-1'>{$path}</span>";
                                }
                                ?>
                            </div>

                            <form method="post">
                                <button type="submit" class="btn btn-primary">Continue</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    private function showDatabaseConfig() {
        ?>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3>Step 2: Database Configuration</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($this->errors)): ?>
                                <div class="alert alert-danger">
                                    <ul>
                                        <?php foreach ($this->errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form method="post">
                                <div class="mb-3">
                                    <label for="db_host" class="form-label">Database Host</label>
                                    <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                                </div>
                                <div class="mb-3">
                                    <label for="db_name" class="form-label">Database Name</label>
                                    <input type="text" class="form-control" id="db_name" name="db_name" value="school_management" required>
                                </div>
                                <div class="mb-3">
                                    <label for="db_user" class="form-label">Database Username</label>
                                    <input type="text" class="form-control" id="db_user" name="db_user" value="root" required>
                                </div>
                                <div class="mb-3">
                                    <label for="db_pass" class="form-label">Database Password</label>
                                    <input type="password" class="form-control" id="db_pass" name="db_pass">
                                </div>
                                <button type="submit" class="btn btn-primary">Test Connection & Continue</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    private function showAdminAccount() {
        ?>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3>Step 3: Administrator Account</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($this->errors)): ?>
                                <div class="alert alert-danger">
                                    <ul>
                                        <?php foreach ($this->errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form method="post">
                                <div class="mb-3">
                                    <label for="admin_name" class="form-label">Administrator Name</label>
                                    <input type="text" class="form-control" id="admin_name" name="admin_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="admin_email" class="form-label">Administrator Email</label>
                                    <input type="email" class="form-control" id="admin_email" name="admin_email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="admin_password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                                    <div class="form-text">Must be at least 8 characters with uppercase, lowercase, and numbers.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="admin_confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="admin_confirm_password" name="admin_confirm_password" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Create Account & Continue</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    private function showInstallation() {
        ?>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3>Step 4: Installation</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($this->errors)): ?>
                                <div class="alert alert-danger">
                                    <ul>
                                        <?php foreach ($this->errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <p>The system will now:</p>
                            <ul>
                                <li>Create database tables</li>
                                <li>Insert default data</li>
                                <li>Create administrator account</li>
                                <li>Set up system configuration</li>
                            </ul>

                            <form method="post">
                                <button type="submit" class="btn btn-primary">Start Installation</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    private function showComplete() {
        ?>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3>Installation Complete!</h3>
                        </div>
                        <div class="card-body text-center">
                            <div class="alert alert-success">
                                <h4>School Management System has been successfully installed!</h4>
                            </div>

                            <p><strong>Administrator Login:</strong></p>
                            <p>Email: <?php echo htmlspecialchars($_SESSION['admin_data']['email']); ?></p>
                            <p>Password: (The password you set during installation)</p>

                            <div class="mt-4">
                                <a href="/" class="btn btn-primary">Go to Homepage</a>
                                <a href="/login" class="btn btn-secondary">Login to Admin Panel</a>
                            </div>

                            <div class="mt-4">
                                <p class="text-muted">Please change the default password after first login.</p>
                                <p class="text-muted">Remove the install.php file for security.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    private function renderHeader() {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>School Management System - Installation</title>
            <link href="assets/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
        <?php
    }

    private function renderFooter() {
        ?>
            <script src="assets/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
    }
}

// Check if already installed
if (file_exists('config/installed')) {
    die('System is already installed. Remove config/installed to re-run installation.');
}

$installer = new Installer();
$installer->run();
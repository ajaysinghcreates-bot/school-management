<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Documents - School Management</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .document-card { transition: transform 0.3s; cursor: pointer; }
        .document-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
        .document-icon { font-size: 3rem; margin-bottom: 1rem; }
        .bulk-actions { background-color: #f8f9fa; padding: 1rem; border-radius: 0.5rem; margin-top: 1rem; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="/cashier/dashboard">School Management Cashier</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="/cashier/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/cashier/fees">Fees</a></li>
                    <li class="nav-item"><a class="nav-link" href="/cashier/outstanding">Outstanding</a></li>
                    <li class="nav-item"><a class="nav-link" href="/cashier/reports">Reports</a></li>
                    <li class="nav-item"><a class="nav-link" href="/cashier/expenses">Expenses</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/cashier/documents">Documents</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Academic Documents Management</h1>
                <p class="text-muted">Generate and print academic documents for students</p>
            </div>
        </div>

        <!-- Document Types -->
        <div class="row mb-5">
            <div class="col-md-4 mb-4">
                <div class="card document-card h-100" onclick="showDocumentOptions('marksheet')">
                    <div class="card-body text-center">
                        <div class="document-icon text-primary">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h5 class="card-title">Marksheets</h5>
                        <p class="card-text">Generate individual or bulk marksheets for exams</p>
                        <span class="badge bg-primary">Single & Bulk</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card document-card h-100" onclick="showDocumentOptions('admit-card')">
                    <div class="card-body text-center">
                        <div class="document-icon text-success">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <h5 class="card-title">Admit Cards</h5>
                        <p class="card-text">Generate admit cards for examinations</p>
                        <span class="badge bg-success">Single & Bulk</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card document-card h-100" onclick="showDocumentOptions('transfer-certificate')">
                    <div class="card-body text-center">
                        <div class="document-icon text-warning">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <h5 class="card-title">Transfer Certificates</h5>
                        <p class="card-text">Generate transfer certificates for students</p>
                        <span class="badge bg-warning">Individual</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Generation Options -->
        <div id="document-options" class="row" style="display: none;">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 id="document-title" class="mb-0">Document Options</h5>
                    </div>
                    <div class="card-body">
                        <!-- Marksheet Options -->
                        <div id="marksheet-options" class="document-type-options" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Single Marksheet</h6>
                                    <div class="mb-3">
                                        <label for="student-select" class="form-label">Select Student</label>
                                        <select class="form-select" id="student-select">
                                            <option value="">Choose a student...</option>
                                            <!-- Students will be loaded dynamically -->
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="exam-select" class="form-label">Select Exam</label>
                                        <select class="form-select" id="exam-select">
                                            <option value="">Choose an exam...</option>
                                            <!-- Exams will be loaded dynamically -->
                                        </select>
                                    </div>
                                    <button class="btn btn-primary" onclick="generateSingleMarksheet()">
                                        <i class="fas fa-download"></i> Generate Marksheet
                                    </button>
                                </div>

                                <div class="col-md-6">
                                    <h6>Bulk Marksheets (Class-wise)</h6>
                                    <div class="mb-3">
                                        <label for="class-select-marksheet" class="form-label">Select Class</label>
                                        <select class="form-select" id="class-select-marksheet">
                                            <option value="">Choose a class...</option>
                                            <!-- Classes will be loaded dynamically -->
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="exam-select-bulk" class="form-label">Select Exam</label>
                                        <select class="form-select" id="exam-select-bulk">
                                            <option value="">Choose an exam...</option>
                                            <!-- Exams will be loaded dynamically -->
                                        </select>
                                    </div>
                                    <button class="btn btn-success" onclick="generateBulkMarksheet()">
                                        <i class="fas fa-download"></i> Generate Bulk Marksheets
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Admit Card Options -->
                        <div id="admit-card-options" class="document-type-options" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Single Admit Card</h6>
                                    <div class="mb-3">
                                        <label for="student-admit-select" class="form-label">Select Student</label>
                                        <select class="form-select" id="student-admit-select">
                                            <option value="">Choose a student...</option>
                                            <!-- Students will be loaded dynamically -->
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="exam-admit-select" class="form-label">Select Exam</label>
                                        <select class="form-select" id="exam-admit-select">
                                            <option value="">Choose an exam...</option>
                                            <!-- Exams will be loaded dynamically -->
                                        </select>
                                    </div>
                                    <button class="btn btn-primary" onclick="generateSingleAdmitCard()">
                                        <i class="fas fa-download"></i> Generate Admit Card
                                    </button>
                                </div>

                                <div class="col-md-6">
                                    <h6>Bulk Admit Cards (Class-wise)</h6>
                                    <div class="mb-3">
                                        <label for="class-select-admit" class="form-label">Select Class</label>
                                        <select class="form-select" id="class-select-admit">
                                            <option value="">Choose a class...</option>
                                            <!-- Classes will be loaded dynamically -->
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="exam-admit-bulk-select" class="form-label">Select Exam</label>
                                        <select class="form-select" id="exam-admit-bulk-select">
                                            <option value="">Choose an exam...</option>
                                            <!-- Exams will be loaded dynamically -->
                                        </select>
                                    </div>
                                    <button class="btn btn-success" onclick="generateBulkAdmitCard()">
                                        <i class="fas fa-download"></i> Generate Bulk Admit Cards
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Transfer Certificate Options -->
                        <div id="transfer-certificate-options" class="document-type-options" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Transfer Certificate</h6>
                                    <div class="mb-3">
                                        <label for="student-tc-select" class="form-label">Select Student</label>
                                        <select class="form-select" id="student-tc-select">
                                            <option value="">Choose a student...</option>
                                            <!-- Students will be loaded dynamically -->
                                        </select>
                                    </div>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Note:</strong> Transfer certificates include academic performance summary and are issued for students leaving the institution.
                                    </div>
                                    <button class="btn btn-warning" onclick="generateTransferCertificate()">
                                        <i class="fas fa-download"></i> Generate Transfer Certificate
                                    </button>
                                </div>

                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6>TC Information</h6>
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success"></i> Student details and academic record</li>
                                                <li><i class="fas fa-check text-success"></i> Date of admission and leaving</li>
                                                <li><i class="fas fa-check text-success"></i> Conduct and character certificate</li>
                                                <li><i class="fas fa-check text-success"></i> Academic performance summary</li>
                                                <li><i class="fas fa-check text-success"></i> Official school seal and signatures</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Documents -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Document Generations</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Document Type</th>
                                        <th>Student/Class</th>
                                        <th>Generated By</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="recent-documents">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No recent documents</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentDocumentType = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadStudents();
            loadClasses();
            loadExams();
        });

        function showDocumentOptions(type) {
            currentDocumentType = type;

            // Hide all options
            document.querySelectorAll('.document-type-options').forEach(el => {
                el.style.display = 'none';
            });

            // Show selected options
            document.getElementById(type + '-options').style.display = 'block';
            document.getElementById('document-options').style.display = 'block';
            document.getElementById('document-title').textContent = type.charAt(0).toUpperCase() + type.slice(1).replace('-', ' ') + ' Options';

            // Scroll to options
            document.getElementById('document-options').scrollIntoView({ behavior: 'smooth' });
        }

        function loadStudents() {
            // This would typically load from API
            // For demo, we'll add some sample options
            const studentSelects = ['student-select', 'student-admit-select', 'student-tc-select'];
            studentSelects.forEach(selectId => {
                const select = document.getElementById(selectId);
                // Sample students - in real implementation, load from API
                select.innerHTML = '<option value="">Choose a student...</option>' +
                    '<option value="1">John Doe (Class 10-A)</option>' +
                    '<option value="2">Jane Smith (Class 9-B)</option>' +
                    '<option value="3">Bob Johnson (Class 8-A)</option>';
            });
        }

        function loadClasses() {
            const classSelects = ['class-select-marksheet', 'class-select-admit'];
            classSelects.forEach(selectId => {
                const select = document.getElementById(selectId);
                select.innerHTML = '<option value="">Choose a class...</option>' +
                    '<option value="1">Class 8-A</option>' +
                    '<option value="2">Class 9-B</option>' +
                    '<option value="3">Class 10-A</option>';
            });
        }

        function loadExams() {
            const examSelects = ['exam-select', 'exam-select-bulk', 'exam-admit-select', 'exam-admit-bulk-select'];
            examSelects.forEach(selectId => {
                const select = document.getElementById(selectId);
                select.innerHTML = '<option value="">Choose an exam...</option>' +
                    '<option value="1">Mid-Term Examination 2024</option>' +
                    '<option value="2">Final Examination 2024</option>' +
                    '<option value="3">Unit Test - Mathematics</option>';
            });
        }

        function generateSingleMarksheet() {
            const studentId = document.getElementById('student-select').value;
            const examId = document.getElementById('exam-select').value;

            if (!studentId || !examId) {
                alert('Please select both student and exam');
                return;
            }

            window.open('/cashier/api/documents/marksheet?student_id=' + studentId + '&exam_id=' + examId, '_blank');
        }

        function generateBulkMarksheet() {
            const classId = document.getElementById('class-select-marksheet').value;
            const examId = document.getElementById('exam-select-bulk').value;

            if (!classId || !examId) {
                alert('Please select both class and exam');
                return;
            }

            window.open('/cashier/api/documents/bulk-marksheet?class_id=' + classId + '&exam_id=' + examId, '_blank');
        }

        function generateSingleAdmitCard() {
            const studentId = document.getElementById('student-admit-select').value;
            const examId = document.getElementById('exam-admit-select').value;

            if (!studentId || !examId) {
                alert('Please select both student and exam');
                return;
            }

            window.open('/cashier/api/documents/admit-card?student_id=' + studentId + '&exam_id=' + examId, '_blank');
        }

        function generateBulkAdmitCard() {
            const classId = document.getElementById('class-select-admit').value;
            const examId = document.getElementById('exam-admit-bulk-select').value;

            if (!classId || !examId) {
                alert('Please select both class and exam');
                return;
            }

            window.open('/cashier/api/documents/bulk-admit-card?class_id=' + classId + '&exam_id=' + examId, '_blank');
        }

        function generateTransferCertificate() {
            const studentId = document.getElementById('student-tc-select').value;

            if (!studentId) {
                alert('Please select a student');
                return;
            }

            window.open('/cashier/api/documents/transfer-certificate?student_id=' + studentId, '_blank');
        }
    </script>
</body>
</html>
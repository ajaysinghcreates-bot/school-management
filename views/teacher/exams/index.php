<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Management - School Management</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/teacher/dashboard">School Management Teacher</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="/teacher/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/teacher/attendance">Attendance</a></li>
                    <li class="nav-item"><a class="nav-link" href="/teacher/classes">Classes</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/teacher/exams">Exams</a></li>
                    <li class="nav-item"><a class="nav-link" href="/teacher/profile">Profile</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <h1 class="mb-4">Exam Management</h1>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>My Exams</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExamModal">Add Exam</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Subject</th>
                                <th>Class</th>
                                <th>Exam Date</th>
                                <th>Total Marks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="exams-tbody">
                            <!-- Exams will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Exam Modal -->
    <div class="modal fade" id="addExamModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="examModalTitle">Add Exam</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="exam-form">
                        <input type="hidden" id="exam-id">
                        <div class="mb-3">
                            <label for="exam-title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="exam-title" required>
                        </div>
                        <div class="mb-3">
                            <label for="exam-subject" class="form-label">Subject</label>
                            <select class="form-select" id="exam-subject" required>
                                <!-- Subjects will be loaded -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="exam-date" class="form-label">Exam Date</label>
                            <input type="date" class="form-control" id="exam-date" required>
                        </div>
                        <div class="mb-3">
                            <label for="exam-marks" class="form-label">Total Marks</label>
                            <input type="number" class="form-control" id="exam-marks" required>
                        </div>
                        <div class="mb-3">
                            <label for="exam-description" class="form-label">Description</label>
                            <textarea class="form-control" id="exam-description" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-exam">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Modal -->
    <div class="modal fade" id="resultsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Exam Results</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <button class="btn btn-success mb-3" id="add-result-btn">Add Result</button>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Marks Obtained</th>
                                    <th>Grade</th>
                                    <th>Remarks</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="results-tbody">
                                <!-- Results will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Result Modal -->
    <div class="modal fade" id="addResultModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Result</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="result-form">
                        <input type="hidden" id="result-exam-id">
                        <div class="mb-3">
                            <label for="result-student" class="form-label">Student</label>
                            <select class="form-select" id="result-student" required>
                                <!-- Students will be loaded -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="result-marks" class="form-label">Marks Obtained</label>
                            <input type="number" class="form-control" id="result-marks" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="result-grade" class="form-label">Grade</label>
                            <input type="text" class="form-control" id="result-grade" required>
                        </div>
                        <div class="mb-3">
                            <label for="result-remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="result-remarks" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-result">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentExamId = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadExams();
            loadSubjects();

            document.getElementById('save-exam').addEventListener('click', saveExam);
            document.getElementById('add-result-btn').addEventListener('click', () => {
                loadStudentsForResult();
                new bootstrap.Modal(document.getElementById('addResultModal')).show();
            });
            document.getElementById('save-result').addEventListener('click', saveResult);
        });

        function loadExams() {
            fetch('/teacher/api/exams')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('exams-tbody');
                    tbody.innerHTML = '';

                    data.data.forEach(exam => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${exam.title}</td>
                            <td>${exam.subject_name}</td>
                            <td>${exam.class_name}</td>
                            <td>${exam.exam_date}</td>
                            <td>${exam.total_marks}</td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="editExam(${exam.id})">Edit</button>
                                <button class="btn btn-sm btn-info" onclick="viewResults(${exam.id})">Results</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(error => console.error('Error loading exams:', error));
        }

        function loadSubjects() {
            fetch('/teacher/api/classes')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('exam-subject');
                    select.innerHTML = '<option value="">Select Subject</option>';
                    // Assuming subjects are in the classes data, but actually need to get subjects
                    // For simplicity, let's assume we need another API or parse from classes
                    // Actually, in controller, getExams already has subjects, but for add, need subjects list
                    // Let's add a subjects API or use existing
                    // For now, placeholder
                });
        }

        function saveExam() {
            const formData = {
                title: document.getElementById('exam-title').value,
                subject_id: document.getElementById('exam-subject').value,
                exam_date: document.getElementById('exam-date').value,
                total_marks: document.getElementById('exam-marks').value,
                description: document.getElementById('exam-description').value
            };

            const id = document.getElementById('exam-id').value;
            const url = id ? '/teacher/api/exams/update' : '/teacher/api/exams/create';
            const method = id ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(id ? { ...formData, id } : formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addExamModal')).hide();
                    loadExams();
                } else {
                    alert('Error: ' + data.error);
                }
            });
        }

        function editExam(id) {
            // Load exam data and populate form
            fetch('/teacher/api/exams')
                .then(response => response.json())
                .then(data => {
                    const exam = data.data.find(e => e.id == id);
                    if (exam) {
                        document.getElementById('exam-id').value = exam.id;
                        document.getElementById('exam-title').value = exam.title;
                        document.getElementById('exam-subject').value = exam.subject_id;
                        document.getElementById('exam-date').value = exam.exam_date;
                        document.getElementById('exam-marks').value = exam.total_marks;
                        document.getElementById('exam-description').value = exam.description;
                        document.getElementById('examModalTitle').textContent = 'Edit Exam';
                        new bootstrap.Modal(document.getElementById('addExamModal')).show();
                    }
                });
        }

        function viewResults(examId) {
            currentExamId = examId;
            fetch(`/teacher/api/results?exam_id=${examId}`)
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('results-tbody');
                    tbody.innerHTML = '';
                    data.data.forEach(result => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${result.student_name}</td>
                            <td>${result.marks_obtained}</td>
                            <td>${result.grade}</td>
                            <td>${result.remarks || ''}</td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="editResult(${result.id})">Edit</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                    new bootstrap.Modal(document.getElementById('resultsModal')).show();
                });
        }

        function loadStudentsForResult() {
            // Load students for the current exam's class
            // Need to get class from exam
            fetch(`/teacher/api/exams`)
                .then(response => response.json())
                .then(data => {
                    const exam = data.data.find(e => e.id == currentExamId);
                    if (exam) {
                        // Assume we have a way to get students by class
                        // For simplicity, placeholder
                        const select = document.getElementById('result-student');
                        select.innerHTML = '<option value="">Select Student</option>';
                        // Would need another API for students in class
                    }
                });
        }

        function saveResult() {
            const formData = {
                exam_id: currentExamId,
                student_id: document.getElementById('result-student').value,
                marks_obtained: document.getElementById('result-marks').value,
                grade: document.getElementById('result-grade').value,
                remarks: document.getElementById('result-remarks').value
            };

            fetch('/teacher/api/results/create', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addResultModal')).hide();
                    viewResults(currentExamId);
                } else {
                    alert('Error: ' + data.error);
                }
            });
        }

        function editResult(id) {
            // Similar to edit exam
        }
    </script>
</body>
</html>
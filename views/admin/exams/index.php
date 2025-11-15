<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exams & Results Management - School Management</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-responsive { max-height: 60vh; overflow-y: auto; }
        .pagination { justify-content: center; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/admin/dashboard">School Management Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="/admin/students">Students</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/teachers">Teachers</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/classes">Classes</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/attendance">Attendance</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/admin/exams">Exams</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/reports">Reports</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <h1>Exams & Results Management</h1>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="exams-tab" data-bs-toggle="tab" data-bs-target="#exams" type="button" role="tab">Exams</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="results-tab" data-bs-toggle="tab" data-bs-target="#results" type="button" role="tab">Results</button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- Exams Tab -->
            <div class="tab-pane fade show active" id="exams" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center my-4">
                    <h2>Exams</h2>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#examModal">Add Exam</button>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" id="examSearch" class="form-control" placeholder="Search exams">
                    </div>
                    <div class="col-md-3">
                        <select id="subjectFilter" class="form-select">
                            <option value="">All Subjects</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary" onclick="loadExams()">Filter</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Subject</th>
                                <th>Exam Date</th>
                                <th>Total Marks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="examsBody"></tbody>
                    </table>
                </div>

                <nav aria-label="Exams pagination">
                    <ul class="pagination" id="examsPagination"></ul>
                </nav>
            </div>

            <!-- Results Tab -->
            <div class="tab-pane fade" id="results" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center my-4">
                    <h2>Results</h2>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#resultModal">Add Result</button>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <select id="examFilter" class="form-select">
                            <option value="">All Exams</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="studentFilter" class="form-select">
                            <option value="">All Students</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary" onclick="loadResults()">Filter</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Exam</th>
                                <th>Subject</th>
                                <th>Marks Obtained</th>
                                <th>Grade</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="resultsBody"></tbody>
                    </table>
                </div>

                <nav aria-label="Results pagination">
                    <ul class="pagination" id="resultsPagination"></ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Exam Modal -->
    <div class="modal fade" id="examModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Exam</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="examId">
                    <div class="mb-3">
                        <label for="examTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="examTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="examSubjectId" class="form-label">Subject</label>
                        <select class="form-select" id="examSubjectId" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="examDate" class="form-label">Exam Date</label>
                        <input type="date" class="form-control" id="examDate" required>
                    </div>
                    <div class="mb-3">
                        <label for="totalMarks" class="form-label">Total Marks</label>
                        <input type="number" class="form-control" id="totalMarks" required>
                    </div>
                    <div class="mb-3">
                        <label for="examDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="examDescription"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveExam()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Result Modal -->
    <div class="modal fade" id="resultModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Result</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="resultId">
                    <div class="mb-3">
                        <label for="resultExamId" class="form-label">Exam</label>
                        <select class="form-select" id="resultExamId" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="resultStudentId" class="form-label">Student</label>
                        <select class="form-select" id="resultStudentId" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="marksObtained" class="form-label">Marks Obtained</label>
                        <input type="number" class="form-control" id="marksObtained" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="grade" class="form-label">Grade</label>
                        <input type="text" class="form-control" id="grade">
                    </div>
                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveResult()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadSubjects();
            loadExams();
            loadExamsForSelect();
            loadStudents();
            loadResults();
        });

        function loadSubjects() {
            fetch('/admin/api/subjects')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('subjectFilter');
                    const examSelect = document.getElementById('examSubjectId');
                    data.data.forEach(sub => {
                        const option = document.createElement('option');
                        option.value = sub.id;
                        option.textContent = sub.name;
                        select.appendChild(option.cloneNode(true));
                        examSelect.appendChild(option);
                    });
                });
        }

        function loadExams(page = 1) {
            const search = document.getElementById('examSearch').value;
            const subjectId = document.getElementById('subjectFilter').value;
            fetch(`/admin/api/exams?search=${encodeURIComponent(search)}&subject_id=${subjectId}&page=${page}`)
                .then(response => response.json())
                .then(data => {
                    renderExams(data.data);
                    renderPagination('exams', data.total, data.page, data.limit);
                });
        }

        function loadResults(page = 1) {
            const examId = document.getElementById('examFilter').value;
            const studentId = document.getElementById('studentFilter').value;
            fetch(`/admin/api/results?exam_id=${examId}&student_id=${studentId}&page=${page}`)
                .then(response => response.json())
                .then(data => {
                    renderResults(data.data);
                    renderPagination('results', data.total, data.page, data.limit);
                });
        }

        function loadExamsForSelect() {
            fetch('/admin/api/exams')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('examFilter');
                    const resultSelect = document.getElementById('resultExamId');
                    data.data.forEach(exam => {
                        const option = document.createElement('option');
                        option.value = exam.id;
                        option.textContent = exam.title;
                        select.appendChild(option.cloneNode(true));
                        resultSelect.appendChild(option);
                    });
                });
        }

        function loadStudents() {
            fetch('/admin/api/students')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('studentFilter');
                    const resultSelect = document.getElementById('resultStudentId');
                    data.data.forEach(student => {
                        const option = document.createElement('option');
                        option.value = student.id;
                        option.textContent = student.name;
                        select.appendChild(option.cloneNode(true));
                        resultSelect.appendChild(option);
                    });
                });
        }

        function renderExams(exams) {
            const tbody = document.getElementById('examsBody');
            tbody.innerHTML = '';
            exams.forEach(exam => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${exam.title}</td>
                    <td>${exam.subject_name || ''}</td>
                    <td>${exam.exam_date}</td>
                    <td>${exam.total_marks}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editExam(${exam.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteExam(${exam.id})">Delete</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function renderResults(results) {
            const tbody = document.getElementById('resultsBody');
            tbody.innerHTML = '';
            results.forEach(result => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${result.student_name}</td>
                    <td>${result.exam_title}</td>
                    <td>${result.subject_name || ''}</td>
                    <td>${result.marks_obtained}</td>
                    <td>${result.grade || ''}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editResult(${result.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteResult(${result.id})">Delete</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function renderPagination(type, total, page, limit) {
            const totalPages = Math.ceil(total / limit);
            const pagination = document.getElementById(type + 'Pagination');
            pagination.innerHTML = '';

            if (totalPages <= 1) return;

            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${page === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `<a class="page-link" href="#" onclick="load${type.charAt(0).toUpperCase() + type.slice(1)}(${page - 1})">Previous</a>`;
            pagination.appendChild(prevLi);

            for (let i = 1; i <= totalPages; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === page ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="load${type.charAt(0).toUpperCase() + type.slice(1)}(${i})">${i}</a>`;
                pagination.appendChild(li);
            }

            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${page === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `<a class="page-link" href="#" onclick="load${type.charAt(0).toUpperCase() + type.slice(1)}(${page + 1})">Next</a>`;
            pagination.appendChild(nextLi);
        }

        function editExam(id) {
            fetch('/admin/api/exams')
                .then(response => response.json())
                .then(data => {
                    const exam = data.data.find(e => e.id == id);
                    if (exam) {
                        document.getElementById('examId').value = exam.id;
                        document.getElementById('examTitle').value = exam.title;
                        document.getElementById('examSubjectId').value = exam.subject_id;
                        document.getElementById('examDate').value = exam.exam_date;
                        document.getElementById('totalMarks').value = exam.total_marks;
                        document.getElementById('examDescription').value = exam.description;
                        new bootstrap.Modal(document.getElementById('examModal')).show();
                    }
                });
        }

        function saveExam() {
            const id = document.getElementById('examId').value;
            const data = {
                title: document.getElementById('examTitle').value,
                subject_id: document.getElementById('examSubjectId').value,
                exam_date: document.getElementById('examDate').value,
                total_marks: document.getElementById('totalMarks').value,
                description: document.getElementById('examDescription').value
            };

            const url = id ? '/admin/api/exams/update?id=' + id : '/admin/api/exams/create';
            const method = id ? 'PUT' : 'POST';

            fetch(url, { method, headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        bootstrap.Modal.getInstance(document.getElementById('examModal')).hide();
                        loadExams();
                        loadExamsForSelect();
                    } else {
                        alert('Error: ' + result.error);
                    }
                });
        }

        function deleteExam(id) {
            if (confirm('Delete this exam?')) {
                fetch('/admin/api/exams/delete?id=' + id, { method: 'DELETE' })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            loadExams();
                            loadExamsForSelect();
                        } else {
                            alert('Error: ' + result.error);
                        }
                    });
            }
        }

        function editResult(id) {
            fetch('/admin/api/results')
                .then(response => response.json())
                .then(data => {
                    const result = data.data.find(r => r.id == id);
                    if (result) {
                        document.getElementById('resultId').value = result.id;
                        document.getElementById('resultExamId').value = result.exam_id;
                        document.getElementById('resultStudentId').value = result.student_id;
                        document.getElementById('marksObtained').value = result.marks_obtained;
                        document.getElementById('grade').value = result.grade;
                        document.getElementById('remarks').value = result.remarks;
                        new bootstrap.Modal(document.getElementById('resultModal')).show();
                    }
                });
        }

        function saveResult() {
            const id = document.getElementById('resultId').value;
            const data = {
                exam_id: document.getElementById('resultExamId').value,
                student_id: document.getElementById('resultStudentId').value,
                marks_obtained: document.getElementById('marksObtained').value,
                grade: document.getElementById('grade').value,
                remarks: document.getElementById('remarks').value
            };

            const url = id ? '/admin/api/results/update?id=' + id : '/admin/api/results/create';
            const method = id ? 'PUT' : 'POST';

            fetch(url, { method, headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        bootstrap.Modal.getInstance(document.getElementById('resultModal')).hide();
                        loadResults();
                    } else {
                        alert('Error: ' + result.error);
                    }
                });
        }

        function deleteResult(id) {
            if (confirm('Delete this result?')) {
                fetch('/admin/api/results/delete?id=' + id, { method: 'DELETE' })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            loadResults();
                        } else {
                            alert('Error: ' + result.error);
                        }
                    });
            }
        }
    </script>
</body>
</html>
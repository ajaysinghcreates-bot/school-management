<?php
class PDFGenerator {
    private $pdf;

    public function __construct() {
        // Initialize PDF with basic settings
        $this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $this->pdf->SetCreator('School Management System');
        $this->pdf->SetAuthor('School Management System');
        $this->pdf->SetTitle('School Document');

        // Set default header and footer
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);

        // Set margins
        $this->pdf->SetMargins(15, 15, 15);

        // Set auto page breaks
        $this->pdf->SetAutoPageBreak(TRUE, 15);

        // Set font
        $this->pdf->SetFont('helvetica', '', 10);
    }

    public function generateMarksheet($studentData, $examData, $resultsData) {
        $this->pdf->AddPage();

        // School Header
        $this->addSchoolHeader();

        // Title
        $this->pdf->SetFont('helvetica', 'B', 16);
        $this->pdf->Cell(0, 15, 'MARK SHEET', 0, 1, 'C');
        $this->pdf->Ln(5);

        // Student Information
        $this->pdf->SetFont('helvetica', 'B', 12);
        $this->pdf->Cell(0, 10, 'Student Information', 0, 1, 'L');
        $this->pdf->SetFont('helvetica', '', 10);

        $this->pdf->Cell(50, 8, 'Name:', 0, 0);
        $this->pdf->Cell(0, 8, $studentData['name'], 0, 1);

        $this->pdf->Cell(50, 8, 'Scholar Number:', 0, 0);
        $this->pdf->Cell(0, 8, $studentData['scholar_number'], 0, 1);

        $this->pdf->Cell(50, 8, 'Class:', 0, 0);
        $this->pdf->Cell(0, 8, $studentData['class_name'], 0, 1);

        $this->pdf->Cell(50, 8, 'Roll Number:', 0, 0);
        $this->pdf->Cell(0, 8, $studentData['roll_number'], 0, 1);

        $this->pdf->Ln(5);

        // Exam Information
        $this->pdf->SetFont('helvetica', 'B', 12);
        $this->pdf->Cell(0, 10, 'Examination Details', 0, 1, 'L');
        $this->pdf->SetFont('helvetica', '', 10);

        $this->pdf->Cell(50, 8, 'Exam Name:', 0, 0);
        $this->pdf->Cell(0, 8, $examData['title'], 0, 1);

        $this->pdf->Cell(50, 8, 'Exam Date:', 0, 0);
        $this->pdf->Cell(0, 8, date('d-m-Y', strtotime($examData['exam_date'])), 0, 1);

        $this->pdf->Ln(5);

        // Results Table
        $this->pdf->SetFont('helvetica', 'B', 10);
        $this->pdf->SetFillColor(240, 240, 240);

        // Table Header
        $this->pdf->Cell(80, 10, 'Subject', 1, 0, 'C', true);
        $this->pdf->Cell(30, 10, 'Total Marks', 1, 0, 'C', true);
        $this->pdf->Cell(30, 10, 'Obtained', 1, 0, 'C', true);
        $this->pdf->Cell(25, 10, 'Grade', 1, 0, 'C', true);
        $this->pdf->Cell(25, 10, 'Percentage', 1, 1, 'C', true);

        // Table Data
        $this->pdf->SetFont('helvetica', '', 10);
        $totalMarks = 0;
        $obtainedMarks = 0;

        foreach ($resultsData as $result) {
            $this->pdf->Cell(80, 8, $result['subject_name'], 1, 0, 'L');
            $this->pdf->Cell(30, 8, $result['total_marks'], 1, 0, 'C');
            $this->pdf->Cell(30, 8, $result['marks_obtained'], 1, 0, 'C');
            $this->pdf->Cell(25, 8, $result['grade'], 1, 0, 'C');
            $this->pdf->Cell(25, 8, number_format($result['percentage'], 2) . '%', 1, 1, 'C');

            $totalMarks += $result['total_marks'];
            $obtainedMarks += $result['marks_obtained'];
        }

        // Total Row
        $this->pdf->SetFont('helvetica', 'B', 10);
        $this->pdf->Cell(80, 10, 'TOTAL', 1, 0, 'R');
        $this->pdf->Cell(30, 10, $totalMarks, 1, 0, 'C');
        $this->pdf->Cell(30, 10, $obtainedMarks, 1, 0, 'C');
        $this->pdf->Cell(25, 10, $this->calculateGrade($obtainedMarks, $totalMarks), 1, 0, 'C');
        $this->pdf->Cell(25, 10, number_format(($obtainedMarks / $totalMarks) * 100, 2) . '%', 1, 1, 'C');

        $this->pdf->Ln(10);

        // Remarks
        $this->pdf->SetFont('helvetica', 'B', 10);
        $this->pdf->Cell(30, 8, 'Remarks:', 0, 0);
        $this->pdf->SetFont('helvetica', '', 10);
        $this->pdf->Cell(0, 8, $this->getRemarks($obtainedMarks, $totalMarks), 0, 1);

        // Signatures
        $this->pdf->Ln(20);
        $this->addSignatures();

        return $this->pdf->Output('marksheet_' . $studentData['scholar_number'] . '.pdf', 'S');
    }

    public function generateAdmitCard($studentData, $examData, $subjectsData) {
        $this->pdf->AddPage();

        // School Header
        $this->addSchoolHeader();

        // Title
        $this->pdf->SetFont('helvetica', 'B', 16);
        $this->pdf->Cell(0, 15, 'ADMIT CARD', 0, 1, 'C');
        $this->pdf->Ln(5);

        // Student Information
        $this->pdf->SetFont('helvetica', 'B', 12);
        $this->pdf->Cell(0, 10, 'Student Details', 0, 1, 'L');
        $this->pdf->SetFont('helvetica', '', 10);

        $this->pdf->Cell(50, 8, 'Name:', 0, 0);
        $this->pdf->Cell(0, 8, $studentData['name'], 0, 1);

        $this->pdf->Cell(50, 8, 'Scholar Number:', 0, 0);
        $this->pdf->Cell(0, 8, $studentData['scholar_number'], 0, 1);

        $this->pdf->Cell(50, 8, 'Class:', 0, 0);
        $this->pdf->Cell(0, 8, $studentData['class_name'], 0, 1);

        $this->pdf->Cell(50, 8, 'Roll Number:', 0, 0);
        $this->pdf->Cell(0, 8, $studentData['roll_number'], 0, 1);

        $this->pdf->Ln(5);

        // Exam Information
        $this->pdf->SetFont('helvetica', 'B', 12);
        $this->pdf->Cell(0, 10, 'Examination Details', 0, 1, 'L');
        $this->pdf->SetFont('helvetica', '', 10);

        $this->pdf->Cell(50, 8, 'Exam Name:', 0, 0);
        $this->pdf->Cell(0, 8, $examData['title'], 0, 1);

        $this->pdf->Cell(50, 8, 'Exam Date:', 0, 0);
        $this->pdf->Cell(0, 8, date('d-m-Y', strtotime($examData['exam_date'])), 0, 1);

        if ($examData['start_time']) {
            $this->pdf->Cell(50, 8, 'Time:', 0, 0);
            $this->pdf->Cell(0, 8, date('H:i', strtotime($examData['start_time'])) . ' - ' . date('H:i', strtotime($examData['end_time'])), 0, 1);
        }

        $this->pdf->Ln(5);

        // Subject Schedule
        $this->pdf->SetFont('helvetica', 'B', 12);
        $this->pdf->Cell(0, 10, 'Subject Schedule', 0, 1, 'L');

        $this->pdf->SetFont('helvetica', 'B', 10);
        $this->pdf->SetFillColor(240, 240, 240);

        // Table Header
        $this->pdf->Cell(60, 10, 'Subject', 1, 0, 'C', true);
        $this->pdf->Cell(30, 10, 'Date', 1, 0, 'C', true);
        $this->pdf->Cell(25, 10, 'Time', 1, 0, 'C', true);
        $this->pdf->Cell(25, 10, 'Room', 1, 0, 'C', true);
        $this->pdf->Cell(50, 10, 'Instructions', 1, 1, 'C', true);

        // Table Data
        $this->pdf->SetFont('helvetica', '', 9);
        foreach ($subjectsData as $subject) {
            $this->pdf->Cell(60, 8, $subject['name'], 1, 0, 'L');
            $this->pdf->Cell(30, 8, date('d-m-Y', strtotime($subject['exam_date'])), 1, 0, 'C');
            $this->pdf->Cell(25, 8, $subject['time_slot'], 1, 0, 'C');
            $this->pdf->Cell(25, 8, $subject['room'] ?? 'TBA', 1, 0, 'C');
            $this->pdf->Cell(50, 8, 'Bring your own stationery', 1, 1, 'L');
        }

        $this->pdf->Ln(10);

        // Instructions
        $this->pdf->SetFont('helvetica', 'B', 10);
        $this->pdf->Cell(0, 8, 'Important Instructions:', 0, 1, 'L');
        $this->pdf->SetFont('helvetica', '', 9);
        $instructions = [
            '1. Reach the examination center 30 minutes before the exam time.',
            '2. Bring this admit card and valid ID proof.',
            '3. Electronic devices are strictly prohibited.',
            '4. Maintain discipline during the examination.',
            '5. Report any discrepancy immediately to the invigilator.'
        ];

        foreach ($instructions as $instruction) {
            $this->pdf->Cell(0, 6, $instruction, 0, 1, 'L');
        }

        // Signatures
        $this->pdf->Ln(20);
        $this->addSignatures();

        return $this->pdf->Output('admit_card_' . $studentData['scholar_number'] . '.pdf', 'S');
    }

    public function generateTransferCertificate($studentData, $transferData) {
        $this->pdf->AddPage();

        // School Header
        $this->addSchoolHeader();

        // Title
        $this->pdf->SetFont('helvetica', 'B', 16);
        $this->pdf->Cell(0, 15, 'TRANSFER CERTIFICATE', 0, 1, 'C');
        $this->pdf->Ln(5);

        // Certificate Number
        $this->pdf->SetFont('helvetica', '', 10);
        $this->pdf->Cell(0, 8, 'Certificate No: ' . $transferData['certificate_number'], 0, 1, 'R');
        $this->pdf->Cell(0, 8, 'Date: ' . date('d-m-Y'), 0, 1, 'R');
        $this->pdf->Ln(5);

        // Student Information
        $this->pdf->SetFont('helvetica', '', 12);
        $this->pdf->MultiCell(0, 10, "This is to certify that " . $studentData['name'] . ", son/daughter of " . $studentData['father_name'] . " and " . $studentData['mother_name'] . ", resident of " . $studentData['address'] . ", was a student of this school from " . date('d-m-Y', strtotime($studentData['admission_date'])) . " to " . date('d-m-Y', strtotime($transferData['leaving_date'])) . ".", 0, 'J');

        $this->pdf->Ln(5);

        $this->pdf->MultiCell(0, 10, "During the period of study, the conduct and character of the student was " . ($transferData['conduct'] ?? 'good') . ". The student has passed " . $transferData['last_class'] . " examination.", 0, 'J');

        $this->pdf->Ln(5);

        $this->pdf->MultiCell(0, 10, "The student is leaving the school to join " . $transferData['new_school'] . " and this Transfer Certificate is issued at the request of the parent/guardian.", 0, 'J');

        $this->pdf->Ln(10);

        // Academic Record
        $this->pdf->SetFont('helvetica', 'B', 12);
        $this->pdf->Cell(0, 10, 'Academic Record', 0, 1, 'L');

        $this->pdf->SetFont('helvetica', 'B', 10);
        $this->pdf->SetFillColor(240, 240, 240);

        // Table Header
        $this->pdf->Cell(40, 10, 'Class', 1, 0, 'C', true);
        $this->pdf->Cell(30, 10, 'Year', 1, 0, 'C', true);
        $this->pdf->Cell(40, 10, 'Grade/Percentage', 1, 0, 'C', true);
        $this->pdf->Cell(80, 10, 'Remarks', 1, 1, 'C', true);

        // Table Data
        $this->pdf->SetFont('helvetica', '', 10);
        if (isset($transferData['academic_record']) && is_array($transferData['academic_record'])) {
            foreach ($transferData['academic_record'] as $record) {
                $this->pdf->Cell(40, 8, $record['class'], 1, 0, 'C');
                $this->pdf->Cell(30, 8, $record['year'], 1, 0, 'C');
                $this->pdf->Cell(40, 8, $record['grade'], 1, 0, 'C');
                $this->pdf->Cell(80, 8, $record['remarks'] ?? '', 1, 1, 'L');
            }
        }

        // Signatures
        $this->pdf->Ln(30);
        $this->addSignatures();

        return $this->pdf->Output('transfer_certificate_' . $studentData['scholar_number'] . '.pdf', 'S');
    }

    private function addSchoolHeader() {
        // School Logo and Name
        $this->pdf->SetFont('helvetica', 'B', 18);
        $this->pdf->Cell(0, 20, 'SCHOOL MANAGEMENT SYSTEM', 0, 1, 'C');

        $this->pdf->SetFont('helvetica', '', 12);
        $this->pdf->Cell(0, 8, 'School Address, City - PIN Code', 0, 1, 'C');
        $this->pdf->Cell(0, 8, 'Phone: +91-1234567890 | Email: info@school.com', 0, 1, 'C');

        $this->pdf->Ln(10);

        // Add a line
        $this->pdf->Line(15, $this->pdf->GetY(), $this->pdf->GetPageWidth() - 15, $this->pdf->GetY());
        $this->pdf->Ln(5);
    }

    private function addSignatures() {
        $this->pdf->SetFont('helvetica', '', 10);

        // Principal Signature
        $this->pdf->Cell(60, 8, 'Principal', 0, 0, 'C');
        $this->pdf->Cell(60, 8, 'Class Teacher', 0, 0, 'C');
        $this->pdf->Cell(60, 8, 'Exam Controller', 0, 1, 'C');

        $this->pdf->Ln(15);

        $this->pdf->Cell(60, 8, '(Signature)', 0, 0, 'C');
        $this->pdf->Cell(60, 8, '(Signature)', 0, 0, 'C');
        $this->pdf->Cell(60, 8, '(Signature)', 0, 1, 'C');
    }

    private function calculateGrade($obtained, $total) {
        $percentage = ($obtained / $total) * 100;

        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 40) return 'C';
        return 'F';
    }

    private function getRemarks($obtained, $total) {
        $percentage = ($obtained / $total) * 100;

        if ($percentage >= 90) return 'Outstanding performance. Keep it up!';
        if ($percentage >= 80) return 'Excellent performance.';
        if ($percentage >= 70) return 'Good performance.';
        if ($percentage >= 60) return 'Satisfactory performance.';
        if ($percentage >= 50) return 'Needs improvement.';
        return 'Poor performance. Extra attention required.';
    }

    public function output($name = '', $dest = '') {
        return $this->pdf->Output($name, $dest);
    }
}

// Simple TCPDF fallback if TCPDF is not available
if (!class_exists('TCPDF')) {
    class TCPDF {
        public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false) {}
        public function SetCreator($creator) {}
        public function SetAuthor($author) {}
        public function SetTitle($title) {}
        public function setPrintHeader($header = false) {}
        public function setPrintFooter($footer = false) {}
        public function SetMargins($left, $top, $right = -1) {}
        public function SetAutoPageBreak($auto = true, $margin = 0) {}
        public function SetFont($family, $style = '', $size = 0) {}
        public function AddPage() {}
        public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '') {}
        public function MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false) {}
        public function Ln($h = '') {}
        public function Line($x1, $y1, $x2, $y2) {}
        public function GetY() { return 0; }
        public function GetPageWidth() { return 210; }
        public function Output($name = '', $dest = '') {
            // Return error message if TCPDF is not installed
            return 'TCPDF library is not installed. Please install TCPDF to generate PDF documents.';
        }
    }
}
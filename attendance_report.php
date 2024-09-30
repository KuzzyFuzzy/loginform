<?php
require('C:/xampp/htdocs/66/fpdf/fpdf.php'); // Adjust the path to fpdf.php

// Database connection
include 'database.php'; // Include your database connection file

// Fetch attendance summary
$sql = "SELECT name, last_name, attendance FROM students";
$result = $conn->query($sql);

// Create instance of FPDF
$pdf = new FPDF();
$pdf->AddPage();

// Header
$pdf->SetFont('Arial', 'B', 16); // Bold font for school name


// Add spacing with 1.5 line height
$pdf->Cell(0, 10, 'South East Asian Institute of Technology, Inc.', 0, 1, 'C');
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(0, 5, 'College of Information and Communication Technology', 0, 1, 'C');
$pdf->Ln(15); // Adjust line space to 1.5

// Subject and Record Type
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'Subject: IT ELEC3: Web Systems and Technology', 0, 0); // Set more vertical space (8)
$pdf->Cell(0, 5, 'Instructor: Hernan E. Trillano', 0, 1, 'R');
$pdf->Ln(2); // Minimized line spacing
$pdf->Cell(0, 8, 'Schedule: Mon-Tue | CL7', 0, 0);
$pdf->Cell(0, 5, 'Date: ' . date('Y-m-d'), 0, 1, 'R');
$pdf->Ln(5); // Add extra space

// Attendance Report Title
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Attendance Report', 0, 1, 'C');
$pdf->Ln(5);

// Centering the Table
$pdf->SetX((210 - 130) / 2); // Center table on an A4 page (width: 210mm)

// Table Header
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(80, 10, 'Student Name', 1);
$pdf->Cell(50, 10, 'Attendance', 1);
$pdf->Ln();

// Table Content
$pdf->SetFont('Arial', '', 10);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->SetX((210 - 130) / 2); // Center table content
        $pdf->Cell(80, 10, $row['name'] . ' ' . $row['last_name'], 1);
        $pdf->Cell(50, 10, $row['attendance'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->SetX((210 - 130) / 2); // Center 'No attendance data' cell
    $pdf->Cell(130, 10, 'No attendance data found', 1, 0, 'C');
}

// Add "Nothing Follows" after the table
$pdf->Ln(10); // Add space before the "Nothing Follows" text
$pdf->SetFont('Arial', 'I', 10); // Italic font
$pdf->Cell(0, 10, '*************Nothing Follows*************', 0, 1, 'C');

// Signatory Section
$pdf->Ln(5); // Minimized line spacing
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 10, 'Approved By:', 0, 1);
$pdf->SetFont('Arial', 'U', 10);
$pdf->Cell(0, 10, 'Hernan E. Trillano', 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 1, 'CICT Faculty', 0, 1);

// Output PDF to browser
$pdf->Output('I', 'Attendance_Report.pdf'); // 'I' option will open in a new tab
?>

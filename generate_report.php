<?php
require('C:\xampp\htdocs\PROJECT\fpdf\fpdf.php'); // Adjust the path to fpdf.php

// Database connection
include 'database.php'; // Include your database connection file

// Fetch student records
$sql = "SELECT * FROM students";
$result = $conn->query($sql);

// Create instance of FPDF
$pdf = new FPDF();
$pdf->AddPage();

// Add watermark (logo) centered on the page
$pdf->Image('C:\xampp\htdocs\PROJECT\cict.png', 50, 80, 110, 110); // Logo centered as watermark

// Header
$pdf->SetFont('Arial', 'B', 16); // Bold font for school name
$pdf->Image('C:\xampp\htdocs\PROJECT\logo.png', 10, 10, 25); // Logo aligned to left

// Adding space with 1.5 line height
$pdf->Cell(0, 10, 'South East Asian Institute of Technology, Inc.', 0, 1, 'C');

// Set font to normal (non-bold) and size to 14 for the address
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(0, 5, 'Crossing Rubber, Tupi, South Cotabato 9505', 0, 1, 'C');
$pdf->Ln(15); // Adjust line space to 1.5

// Subject and Record Type
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'Subject: IT ELEC3: Web Systems and Technology', 0, 0); // Set more vertical space (8)
$pdf->Cell(0, 5, 'Instructor: Hernan E. Trillano', 0, 1, 'R');
$pdf->Ln(2); // Minimized line spacing
$pdf->Cell(0, 8, 'Record Type: Student Master List', 0, 0);
$pdf->Cell(0, 5, 'Date: ' . date('Y-m-d'), 0, 1, 'R');
$pdf->Ln(5); // Add extra space

// Table Header
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(35, 10, 'ID No.', 1); // Adjusted column size
$pdf->Cell(50, 10, 'First Name', 1); // Adjusted column size
$pdf->Cell(50, 10, 'Middle Name', 1); // Adjusted column size
$pdf->Cell(55, 10, 'Last Name', 1); // Adjusted column size
$pdf->Ln();

// Table Content
$pdf->SetFont('Arial', '', 10);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(35, 10, $row['student_ID'], 1);
        $pdf->Cell(50, 10, $row['first_name'], 1);
        $pdf->Cell(50, 10, $row['middle_name'], 1);
        $pdf->Cell(55, 10, $row['last_name'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(190, 10, 'No records found', 1, 0, 'C');
}

// Add "Nothing Follows" after the table
$pdf->Ln(10); // Add space before the "Nothing Follows" text
$pdf->SetFont('Arial', 'I', 10); // Italic font
$pdf->Cell(0, 10, '*************Nothing Follows*************', 0, 1, 'C');


// Signatory Section
$pdf->Ln(5); // Minimized line spacing
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 10, 'Reviewed By:', 0, 1);
$pdf->SetFont('Arial', 'U', 10);
$pdf->Cell(0, 10, 'Hernan E. Trillano', 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 1, 'CICT Faculty', 0, 1);

// Set headers to force PDF to open in browser
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="Student_Master_List.pdf"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . strlen($pdf->Output('S')));

// Output PDF to browser
$pdf->Output('I', 'Student_Master_List.pdf'); // 'I' option will open in browser
?>




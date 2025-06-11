<?php
include 'koneksi.php';
/*
$paths = ["C:/xampp/htdocs/nanonets/files/PO - PV", 
"C:/xampp/htdocs/nanonets/files/PO - P2", 
"C:/xampp/htdocs/nanonets/files/Non PO - PV", 
"C:/xampp/htdocs/nanonets/files/Non PO - P2"];

$url = "https://app.nanonets.com/api/v2/OCR/Model/297f234f-d498-4d93-8b63-e53fad148326/LabelFile/";

foreach ($paths as $path) {
$processFiles = array_values(array_filter(scandir($path), function($file) use ($path) { 
return !is_dir($path . '/' . $file);
}));

echo "path $path <br/>";

//Looping File
foreach($processFiles as $processFile){
echo $processFile;

$file = curl_file_create($path."/".$processFile);
$upload_date = date("YmdHis");

$sourceDirectory = $path.'/'.$processFile;
$destinationDirectory = 'C:/xampp/htdocs/nanonets/files/ExtractINV/'.$upload_date.'_'.$processFile;
echo "source directory $sourceDirectory <br/>";

if ($path == "C:/xampp/htdocs/nanonets/files/PO - PV"){
    echo "type 1 </br>";
} elseif ($path == "C:/xampp/htdocs/nanonets/files/PO - P2") {
    echo "type 2 </br>";
} elseif ($path == "C:/xampp/htdocs/nanonets/files/Non PO - PV") {
    echo "type 3 </br>";
} elseif ($path == "C:/xampp/htdocs/nanonets/files/Non PO - P2") {
    echo "type 4 </br>";
}
}
}
*/
/*
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;



$BankName = "";

if ($Norek == "0001982651"){ //DANAMON
    $BankName ="DANAMON";
}else if ($Norek == "1150094006345"){ //MANDIRI
    $BankName ="MANDIRI";
}else{ //BCA
    $BankName ="BCA";
}


// Create spreadsheet and sheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set column widths
$sheet->getColumnDimension('A')->setWidth(15);
$sheet->getColumnDimension('B')->setWidth(20);
$sheet->getColumnDimension('C')->setWidth(30);
$sheet->getColumnDimension('D')->setWidth(15);
$sheet->getColumnDimension('E')->setWidth(15);

// Header: PT BERCA HARDAYAPERKASA
$sheet->mergeCells('A1:D1');
$sheet->setCellValue('A1', 'PT BERCA HARDAYAPERKASA');
$sheet->getStyle('A1')->getFont()->setBold(true);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


// Header: REKONSILIASI MANDIRI
$sheet->mergeCells('A2:D2');
$sheet->setCellValue('A2', 'REKONSILIASI MANDIRI');
$sheet->getStyle('A2')->getFont()->setBold(true);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Header: A/C (115-0094006345)
$sheet->mergeCells('A3:D3');
$sheet->setCellValue('A3', 'A/C (115-0094006345)');
$sheet->getStyle('A3')->getFont()->setBold(true);
$sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Header: A/C (115-0094006345)
$sheet->mergeCells('A4:D4');
$sheet->setCellValue('A4', '30/06/2024');
$sheet->getStyle('A4')->getFont()->setBold(true);
$sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);





// SALDO ACCOUNTING PER 30 JUNI 2024
$sheet->setCellValue('A6', 'SALDO ACCOUNTING PER 30 JUNI 2024');
$sheet->getStyle('A6')->getFont()->setBold(true);

$sheet->setCellValue('E6', 'Rp');
$sheet->setCellValue('F6', '1.675.333.108,18');
$sheet->getStyle('F6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

// Section 1: PENERIMAAN YANG BELUM DICATAT ACCOUNTING
$sheet->setCellValue('A8', 'PENERIMAAN YANG BELUM DICATAT ACCOUNTING');
$sheet->getStyle('A8')->getFont()->setBold(true);

$sheet->setCellValue('A9', 'DATE');
$sheet->setCellValue('B9', 'REFF');
$sheet->setCellValue('C9', 'DESCRIPTIONS');
$sheet->setCellValue('D9', 'Rp.');
$sheet->getStyle('A9:D9')->getFont()->setBold(true);
$sheet->getStyle('A9:D9')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Example Data
$data1 = [
    ['06-May-24', 'COINS BG', '', '700.000,00'],
    ['23-May-24', 'SHEPTYN', 'PERTAMINA TRANS KONTINENTAL', '4.000.000,00'],
    ['23-May-24', 'SHEPTYN', 'PERTAMINA TRANS KONTINENTAL', '4.000.000,00'],
    ['23-May-24', 'SHEPTYN', 'PERTAMINA TRANS KONTINENTAL', '4.000.000,00'],
    ['23-May-24', 'SHEPTYN', 'PERTAMINA TRANS KONTINENTAL', '4.000.000,00'],
    ['23-May-24', 'SHEPTYN', 'PERTAMINA TRANS KONTINENTAL', '4.000.000,00'],
    ['23-May-24', 'SHEPTYN', 'PERTAMINA TRANS KONTINENTAL', '4.000.000,00'],
    ['23-May-24', 'SHEPTYN', 'PERTAMINA TRANS KONTINENTAL', '4.000.000,00']
];

$row = 10; // START ROW - HEADER SECTION 1
foreach ($data1 as $data) {
    $sheet->setCellValue('A' . $row, $data[0]);
    $sheet->setCellValue('B' . $row, $data[1]);
    $sheet->setCellValue('C' . $row, $data[2]);
    $sheet->setCellValue('D' . $row, $data[3]);
    $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $row++;
}

// Section 2: PENGELUARAN YANG BELUM DICATAT ACCOUNTING
$row += 2; // Add space
$sheet->setCellValue('A' . $row, 'PENGELUARAN YANG BELUM DICATAT ACCOUNTING');
$sheet->getStyle('A' . $row)->getFont()->setBold(true);

$row++;
$sheet->setCellValue('A' . $row, 'DATE');
$sheet->setCellValue('B' . $row, 'REFF');
$sheet->setCellValue('C' . $row, 'DESCRIPTIONS');
$sheet->setCellValue('D' . $row, 'Rp.');
$sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
$sheet->getStyle('A' . $row . ':D' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Example Data
$data2 = [
    ['02-Apr-24', 'BG12124271012', '', '-58.368.326,00'],
    ['26-Apr-24', 'BG12124275009', '', '-130.197.640,00'],
];

$row++;
foreach ($data2 as $data) {
    $sheet->setCellValue('A' . $row, $data[0]);
    $sheet->setCellValue('B' . $row, $data[1]);
    $sheet->setCellValue('C' . $row, $data[2]);
    $sheet->setCellValue('D' . $row, $data[3]);
    $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $row++;
}

// Section 3: PENGELUARAN YANG BELUM DICATAT BANK
$row += 2; // Add space
$sheet->setCellValue('A' . $row, 'PENGELUARAN YANG BELUM DICATAT BANK');
$sheet->getStyle('A' . $row)->getFont()->setBold(true);

$row++;
$sheet->setCellValue('A' . $row, 'DATE');
$sheet->setCellValue('B' . $row, 'REFF');
$sheet->setCellValue('C' . $row, 'DESCRIPTIONS');
$sheet->setCellValue('D' . $row, 'Rp.');
$sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
$sheet->getStyle('A' . $row . ':D' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Example Data
$data3 = [
    ['02-Apr-24', 'BG12124271012', '', '-58.368.326,00'],
    ['26-Apr-24', 'BG12124275009', '', '-130.197.640,00'],
];

$row++;
foreach ($data3 as $data) {
    $sheet->setCellValue('A' . $row, $data[0]);
    $sheet->setCellValue('B' . $row, $data[1]);
    $sheet->setCellValue('C' . $row, $data[2]);
    $sheet->setCellValue('D' . $row, $data[3]);
    $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $row++;
}


// Section 4: PENERIMAAN YANG BELUM DICATAT BANK
$row += 2; // Add space
$sheet->setCellValue('A' . $row, 'PENERIMAAN YANG BELUM DICATAT BANK');
$sheet->getStyle('A' . $row)->getFont()->setBold(true);

$row++;
$sheet->setCellValue('A' . $row, 'DATE');
$sheet->setCellValue('B' . $row, 'REFF');
$sheet->setCellValue('C' . $row, 'DESCRIPTIONS');
$sheet->setCellValue('D' . $row, 'Rp.');
$sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
$sheet->getStyle('A' . $row . ':D' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Example Data
$data4 = [
    ['02-Apr-24', 'BG12124271012', '', '-58.368.326,00'],
    ['26-Apr-24', 'BG12124275009', '', '-130.197.640,00'],
];

$row++;
foreach ($data4 as $data) {
    $sheet->setCellValue('A' . $row, $data[0]);
    $sheet->setCellValue('B' . $row, $data[1]);
    $sheet->setCellValue('C' . $row, $data[2]);
    $sheet->setCellValue('D' . $row, $data[3]);
    $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $row++;
}

// Section 5: SELISIH PENCATATAN ACCT DAN BANK
$row += 2; // Add space
$sheet->setCellValue('A' . $row, 'SELISIH PENCATATAN ACCT DAN BANK');
$sheet->getStyle('A' . $row)->getFont()->setBold(true);

$row++;
$sheet->setCellValue('A' . $row, 'DATE');
$sheet->setCellValue('B' . $row, 'REFF');
$sheet->setCellValue('C' . $row, 'DESCRIPTIONS');
$sheet->setCellValue('D' . $row, 'Rp.');
$sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
$sheet->getStyle('A' . $row . ':D' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Example Data
$data5 = [
    ['02-Apr-24', 'BG12124271012', '', '-58.368.326,00'],
    ['26-Apr-24', 'BG12124275009', '', '-130.197.640,00'],
];

$row++;
foreach ($data5 as $data) {
    $sheet->setCellValue('A' . $row, $data[0]);
    $sheet->setCellValue('B' . $row, $data[1]);
    $sheet->setCellValue('C' . $row, $data[2]);
    $sheet->setCellValue('D' . $row, $data[3]);
    $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $row++;
}

// Section 6: SALDO BANK PER 30 JUNI 2024
$row += 2; // Add space
$sheet->setCellValue('A' . $row, 'SALDO BANK PER 30 JUNI 2024');
$sheet->getStyle('A' . $row)->getFont()->setBold(true);

$row++;
$sheet->setCellValue('A' . $row, 'DATE');
$sheet->setCellValue('B' . $row, 'REFF');
$sheet->setCellValue('C' . $row, 'DESCRIPTIONS');
$sheet->setCellValue('D' . $row, 'Rp.');
$sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
$sheet->getStyle('A' . $row . ':D' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


// Save to file
$writer = new Xlsx($spreadsheet);
$writer->save('.xlsx');

echo "Excel file created successfully!"; */
/*

$sqlDetail3 = "select b.GLDGJ, b.GLDOC, b.GLDCT, b.GLEXA, b.GLEXR, b.GLPN, b.GLFY, b.GLAA from bankgl b";
  
$stid = oci_parse($conn, $sqlDetail3);
oci_execute($stid);

// Fetch data
while ($row = oci_fetch_assoc($stid)) {
    echo "ID: " . $row['GLDOC'] . " - Name: " . $row['GLDGJ'] . "<br>";
}

// Free resources
oci_free_statement($stid);
oci_close($conn);




$oracle_conn = oci_connect("JDE", "B1t24680", "10.0.2.56:1521/jdeorcl");
if (!$oracle_conn) {
    $e = oci_error();
    die("Oracle Connection failed: " . $e['message']);
}

// Fetch data from Oracle Table (via DBLINK)
$query = "SELECT * FROM table_name@your_dblink";
$stid = oci_parse($oracle_conn, $query);
oci_execute($stid);

// Store fetched data in an array
$data = [];
while ($row = oci_fetch_assoc($stid)) {
    $data[] = $row;
}

// Close Oracle connection
oci_free_statement($stid);
oci_close($oracle_conn); */

$Remark = "BI - FAST DB BIF TRANSFER KE011ECS INDO JAYA`PTKBB";

$cleanRemark = str_replace('`', '', $Remark);

echo $cleanRemark;
<?php

require 'vendor/autoload.php';


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

function generateExcel() { 
    
include 'koneksi.php';

$sql = "select id, bank_name, bank_account, fiscal, period, balance_amount from bs_header a where is_generated = 0 and id = 2";
$result = $connect->query($sql);
while($rowHead = $result->fetch_assoc()) {
   // echo "<br> id: ". $row["id"]. " - bank_name: ". $row["bank_name"]. " " . $row["bank_account"] . " " . $row["fiscal"] . " " . $row["period"] . " " . $row["balance_amount"] . "<br>";

            // Create a DateTime object for the first day of the next month
            $date = new DateTime("{$rowHead["fiscal"]}-{$rowHead["period"]}-01");
            $date->modify('first day of next month');

            // Subtract one day to get the last day of the given month
            $date->modify('-1 day');

            // Format the date as "31 May 2024"
            $formattedDate = $date->format('d F Y');

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

            // Header: REKONSILIASI BANK
            $sheet->mergeCells('A2:D2');
            $sheet->setCellValue('A2', 'REKONSILIASI '.$rowHead["bank_name"]);
            $sheet->getStyle('A2')->getFont()->setBold(true);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Header: A/C (115-0094006345)
            $sheet->mergeCells('A3:D3');
            $sheet->setCellValue('A3', 'A/C ('.$rowHead["bank_account"].')');
            $sheet->getStyle('A3')->getFont()->setBold(true);
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Header: A/C (115-0094006345)
            $sheet->mergeCells('A4:D4');
            $sheet->setCellValue('A4', $formattedDate);
            $sheet->getStyle('A4')->getFont()->setBold(true);
            $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // SALDO ACCOUNTING PER 30 JUNI 2024
            $sheet->setCellValue('A6', 'SALDO ACCOUNTING PER '.$formattedDate);
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


$sqlDetail = "select c.id, c.fiscal, c.period, c.posting_date, c.code, c.remark, c.amount_kredit from (
select a.id, a.fiscal, a.period, b.posting_date, b.code, b.remark, b.amount_kredit, b.amount_debit  from bs_header a INNER JOIN bs_detail b ON a.id = b.header_id 
where a.id = ".$rowHead["id"]." and b.code = 'CR' and a.fiscal = ".$rowHead["fiscal"]." and a.period=".$rowHead["period"]." and a.account_number =  '1.11501.MAN01IDR'
) c LEFT JOIN bankgl d ON c.amount_kredit = d.GLAA and d.GLAID = '00000317' and d.GLPN = ".$rowHead["period"]." and d.GLFY = ".$rowHead["fiscal"]."
where d.GLAA IS NULL ";

$result1 = $connect->query($sqlDetail);
$row = 10; 
foreach ($result1 as $data) {
    $sheet->setCellValue('A' . $row, $data['posting_date']);
    $sheet->setCellValue('B' . $row, $data['remark']);
    $sheet->setCellValue('C' . $row, $data['amount_kredit']);
    $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $row++;
}

/*
						echo "<pre><br/>";
						echo "index ALL Cells <br/>";
						//	print_r(json_encode($recordsq));
					 	//print_r(json_encode($dataDetails,JSON_PRETTY_PRINT));
							print_r($dataRow1);
						echo "</pre>";   */


/*
            // Example Data
            $data1 = [
                ['06-May-24', 'COINS BG', 'PERTAMINA TRANS KONTINENTAL', '700.000,00'],
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
            }*/
/*
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
            $sheet->setCellValue('A' . $row, 'SALDO BANK PER '.$formattedDate);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);

            $row++;
            $sheet->setCellValue('A' . $row, 'DATE');
            $sheet->setCellValue('B' . $row, 'REFF');
            $sheet->setCellValue('C' . $row, 'DESCRIPTIONS');
            $sheet->setCellValue('D' . $row, 'Rp.');
            $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':D' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
*/
            $filename = $rowHead["bank_name"].'_'.$rowHead["fiscal"].'_'. $rowHead["period"].'.xlsx';

            // Save to file
       
            $writer = new Xlsx($spreadsheet);
            $writer->save($filename); 
            

            echo "Excel file created successfully generated excel! OK<br/>";
        }
}


generateExcel();
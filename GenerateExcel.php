<?php

require 'vendor/autoload.php';


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;



if (isset($_GET['id'])) {
    // Get the value of 'id' from the query string


    $idas_value = $_GET['id'];
    $nik = $_GET['nik'] ?? null;

    include 'koneksi.php';
    // Output the value
   // echo "The value of idas is: " . htmlspecialchars($idas_value);





   

$sql = "select id, bank_name, bank_account, fiscal, period, REPLACE(REPLACE(REPLACE(FORMAT(beginning_balance / 100, 2), ',', 'X'),'.', ','),'X', '.') AS  beginning_balance, account_number from bs_header a where is_generated = 0 and id=".$idas_value;


$result = $connect->query($sql);
while($rowHead = $result->fetch_assoc()) {
   // echo "<br> id: ". $row["id"]. " - bank_name: ". $row["bank_name"]. " " . $row["bank_account"] . " " . $row["fiscal"] . " " . $row["period"] . " " . $row["balance_amount"] . "<br>";
   
   
    if ($rowHead["bank_name"] == "Mandiri"){
        $AccountID = "00000317"; 
    }else  if ($rowHead["bank_name"] == "Danamon"){
        $AccountID = "00000315"; 
    }else{ //BCA
        $AccountID = "00000311"; 
    } 
   
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
            $sheet->setCellValue('F6',$rowHead["beginning_balance"] );
            $sheet->getStyle('F6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Section 1: PENERIMAAN YANG BELUM DICATAT ACCOUNTING
            $sheet->setCellValue('A8', 'PENERIMAAN YANG BELUM DICATAT ACCOUNTING');
            $sheet->getStyle('A8')->getFont()->setBold(true);

            $sheet->setCellValue('A9', 'DATE');
           // $sheet->setCellValue('B9', 'REFF');
            $sheet->setCellValue('B9', 'DESCRIPTIONS');
            $sheet->setCellValue('C9', 'Rp.');
            $sheet->getStyle('A9:C9')->getFont()->setBold(true);
            $sheet->getStyle('A9:C9')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            /*
            $sqlDetail1 = "select c.id, c.fiscal, c.period, c.posting_date, c.code, c.remark, REPLACE(REPLACE(REPLACE(FORMAT(amount_kredit / 100, 2), ',', 'X'),'.', ','),'X', '.') AS amount_kredit, c.amount_kredit/100 as forsum  from (
            select a.id, a.fiscal, a.period, b.posting_date, b.code, b.remark, b.amount_kredit, b.amount_debit  from bs_header a INNER JOIN bs_detail b ON a.id = b.header_id 
            where  b.code = 'CR' and a.fiscal = ".$rowHead["fiscal"]." and a.period <= ".$rowHead["period"]." and a.account_number =  '".$rowHead["account_number"]."' AND a.user_id = '".$nik."'
            ) c LEFT JOIN bankgl d ON c.amount_kredit = d.GLAA and d.GLAID = '".$AccountID."' and d.GLPN <= ".$rowHead["period"]." and d.GLFY = ".$rowHead["fiscal"]."
            where d.GLAA IS NULL "; */


$sqlDetail1 = "
    SELECT 
        c.id, 
        c.fiscal, 
        c.period, 
        c.posting_date, 
        c.code, 
        c.remark, 
        REPLACE(
            REPLACE(
                REPLACE(FORMAT(amount_kredit / 100, 2), ',', 'X'),
            '.', ','), 
        'X', '.') AS amount_kredit, 
        c.amount_kredit / 100 AS forsum  
    FROM (
        SELECT 
            a.id, 
            a.fiscal, 
            a.period, 
            b.posting_date, 
            b.code, 
            b.remark, 
            b.amount_kredit, 
            b.amount_debit  
        FROM 
            bs_header a 
            INNER JOIN bs_detail b ON a.id = b.header_id 
        WHERE  
            b.code = 'CR' 
            AND a.fiscal = ".$rowHead["fiscal"]." 
            AND a.period <= ".$rowHead["period"]." 
            AND a.account_number = '".$rowHead["account_number"]."' 
            AND a.user_id = '".$nik."'
    ) c 
    LEFT JOIN bankgl d 
        ON ROUND(c.amount_kredit * 100) = d.GLAA 
        AND d.GLAID = '".$AccountID."' 
        AND d.GLPN <= ".$rowHead["period"]." 
        AND d.GLFY = ".$rowHead["fiscal"]."
    WHERE 
        d.GLAA IS NULL
";



            $result1 = $connect->query($sqlDetail1);
            $row = 10; 
            $sum1 = 0;
            foreach ($result1 as $data) {
                $sheet->setCellValue('A' . $row, $data['posting_date']);
                $sheet->setCellValue('B' . $row, $data['remark']);
                $sheet->setCellValue('C' . $row, $data['amount_kredit']);
                //$sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sum1 += $data['forsum'];
                $row++;
            }

            $sheet->setCellValue('E'. $row, 'Rp');
            $sheet->setCellValue('F'. $row, number_format($sum1, 0, ',', '.'));
            $sheet->getStyle('F'. $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);



            // Section 2: PENGELUARAN YANG BELUM DICATAT ACCOUNTING
            $row += 2; // Add space
            $sheet->setCellValue('A' . $row, 'PENGELUARAN YANG BELUM DICATAT ACCOUNTING');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);

            $row++;
            $sheet->setCellValue('A' . $row, 'DATE');
           // $sheet->setCellValue('B' . $row, 'REFF');
            $sheet->setCellValue('B' . $row, 'DESCRIPTIONS');
            $sheet->setCellValue('C' . $row, 'Rp.');
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':C' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            
             /*
            $sqlDetail2 = "select c.id, c.fiscal, c.period, c.posting_date, c.code, c.remark, REPLACE ( REPLACE ( REPLACE ( FORMAT( c.amount_debit / 100,2 ), ',', 'X' ), '.', ',' ), 'X', '.' ) AS amount_debit, c.amount_debit/100 as forsum from (
                select a.id, a.fiscal, a.period, b.posting_date, b.code, b.remark, b.amount_kredit, b.amount_debit  from bs_header a INNER JOIN bs_detail b ON a.id = b.header_id 
                where  b.code = 'DB' and a.fiscal = ".$rowHead["fiscal"]." and a.period <= ".$rowHead["period"]." and a.account_number ='".$rowHead["account_number"]."' AND a.user_id = '".$nik."'
                ) c LEFT JOIN bankgl d ON CONCAT('-', CAST(c.amount_debit AS CHAR)) = d.GLAA and d.GLAID ='".$AccountID."' and d.GLPN <= ".$rowHead["period"]." and d.GLFY = ".$rowHead["fiscal"]."
                where d.GLAA IS NULL "; */
				$sqlDetail2 ="
        SELECT 
        c.id, 
        c.fiscal, 
        c.period, 
        c.posting_date, 
        c.code, 
        c.remark, 
        REPLACE(
            REPLACE(
                REPLACE(FORMAT(c.amount_debit / 100, 2), ',', 'X'), 
            '.', ','), 
        'X', '.') AS amount_debit, 
        c.amount_debit / 100 AS forsum 
    FROM (
        SELECT 
            a.id, 
            a.fiscal, 
            a.period, 
            b.posting_date, 
            b.code, 
            b.remark, 
            b.amount_kredit, 
            b.amount_debit 
        FROM 
            bs_header a 
            INNER JOIN bs_detail b ON a.id = b.header_id 
        WHERE 
            b.code = 'DB' 
            AND a.fiscal = " . $rowHead["fiscal"] . " 
            AND a.period <= " . $rowHead["period"] . " 
            AND a.account_number = '" . $rowHead["account_number"] . "' 
            AND a.user_id = '" . $nik . "'
    ) c 
    LEFT JOIN bankgl d 
        ON CONCAT('-', CAST(ROUND(c.amount_debit * 100) AS CHAR)) = d.GLAA 
        AND d.GLAID = '" . $AccountID . "' 
        AND d.GLPN <= " . $rowHead["period"] . " 
        AND d.GLFY = " . $rowHead["fiscal"] . " 
    WHERE 
        d.GLAA IS NULL";

file_put_contents('debug.log', $sqlDetail2 . PHP_EOL, FILE_APPEND);
				
                $result2 = $connect->query($sqlDetail2);
                $row++;
                $sum2 = 0;
                foreach ($result2 as $data) {
                    $sheet->setCellValue('A' . $row, $data['posting_date']);
                    $sheet->setCellValue('B' . $row, $data['remark']);
                    $sheet->setCellValue('C' . $row, $data['amount_debit']);
                   // $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sum2 += $data['forsum'];
                    $row++;
                }

                $sheet->setCellValue('E'. $row, 'Rp');
                $sheet->setCellValue('F'. $row, number_format($sum2, 0, ',', '.'));
                $sheet->getStyle('F'. $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Section 3: PENGELUARAN YANG BELUM DICATAT BANK
            $row += 2; // Add space
            $sheet->setCellValue('A' . $row, 'PENGELUARAN YANG BELUM DICATAT BANK');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);

            $row++;
            $sheet->setCellValue('A' . $row, 'DATE');
           // $sheet->setCellValue('B' . $row, 'REFF');
            $sheet->setCellValue('B' . $row, 'DESCRIPTIONS');
            $sheet->setCellValue('C' . $row, 'Rp.');
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':C' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


            /*
            $sqlDetail3 = "select c.id, c.fiscal, c.period, c.posting_date, c.code, c.remark, CONCAT('-', CAST(c.amount_debit AS CHAR)) as amount_debit from (
                select a.id, a.fiscal, a.period, b.posting_date, b.code, b.remark, b.amount_kredit, b.amount_debit  from bs_header a INNER JOIN bs_detail b ON a.id = b.header_id 
                where a.id = ".$rowHead["id"]." and b.code = 'DB' and a.fiscal = ".$rowHead["fiscal"]." and a.period=".$rowHead["period"]." and a.account_number = '".$rowHead["account_number"]."'
                ) c LEFT JOIN bankgl d ON CONCAT('-', CAST(c.amount_debit AS CHAR)) = d.GLAA and d.GLAID ='".$AccountID."' and d.GLPN = ".$rowHead["period"]." and d.GLFY = ".$rowHead["fiscal"]."
                where c.amount_debit IS NULL  "; */

                
  /*              
$sqlDetail3 = " SELECT GLDGJ as posting_date, GLDOC, GLDCT, GLEXA as remark, GLEXR, GLPN, GLFY, REPLACE(REPLACE(REPLACE(FORMAT(GLAA / 100, 2), ',', 'X'),'.', ','),'X', '.') as amount_debit, GLAA/100 as forsum FROM bankgl b WHERE b.GLAID = '".$AccountID."'AND b.GLPN <= ".$rowHead["period"]." AND b.GLFY = ".$rowHead["fiscal"]." AND b.GLAA < 0 AND b.GLAA NOT IN (SELECT -ABS(b.amount_debit) FROM bs_header a INNER JOIN bs_detail b ON a.id = b.header_id WHERE  b.CODE = 'DB' AND a.fiscal = ".$rowHead["fiscal"]." AND a.period <= ".$rowHead["period"]." AND a.account_number = '".$rowHead["account_number"]." AND a.user_id = '".$nik."')";
*/
$sqlDetail3 = "
    SELECT 
        GLDGJ AS posting_date, 
        GLDOC, 
        GLDCT, 
        GLEXA AS remark, 
        GLEXR, 
        GLPN, 
        GLFY, 
        REPLACE(
            REPLACE(
                REPLACE(FORMAT(GLAA / 100, 2), ',', 'X'), 
            '.', ','), 
        'X', '.') AS amount_debit, 
        GLAA / 100 AS forsum 
    FROM 
        bankgl b 
    WHERE 
        b.GLAID = '" . $AccountID . "' 
        AND b.GLPN <= " . $rowHead["period"] . " 
        AND b.GLFY = " . $rowHead["fiscal"] . " 
        AND b.GLAA < 0 
        AND b.GLAA NOT IN (
            SELECT 
                -ABS(ROUND(b.amount_debit*100)) 
            FROM 
                bs_header a 
                INNER JOIN bs_detail b ON a.id = b.header_id 
            WHERE  
                b.CODE = 'DB' 
                AND a.fiscal = " . $rowHead["fiscal"] . " 
                AND a.period <= " . $rowHead["period"] . " 
                AND a.account_number = '" . $rowHead["account_number"] . "' 
                AND a.user_id = '" . $nik . "'
        )";

                
                $result3 = $connect->query($sqlDetail3);
                $row++;
                $sum3 = 0;
             
                while ($datas = $result3->fetch_assoc()) {  
                    $sheet->setCellValue('A' . $row, $datas['posting_date']);
                    $sheet->setCellValue('B' . $row, $datas['remark']);
                    $sheet->setCellValue('C' . $row, $datas['amount_debit']);
                    //$sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sum3 += $datas['forsum'];
                    $row++;
                }

                $sheet->setCellValue('E'. $row, 'Rp');
                $sheet->setCellValue('F'. $row, number_format($sum3, 0, ',', '.'));
                $sheet->getStyle('F'. $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Section 4: PENERIMAAN YANG BELUM DICATAT BANK
            $row += 2; // Add space
            $sheet->setCellValue('A' . $row, 'PENERIMAAN YANG BELUM DICATAT BANK');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);

            $row++;
            $sheet->setCellValue('A' . $row, 'DATE');
           // $sheet->setCellValue('B' . $row, 'REFF');
            $sheet->setCellValue('B' . $row, 'DESCRIPTIONS');
            $sheet->setCellValue('C' . $row, 'Rp.');
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':C' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
/*
            $sqlDetail4 = "select c.id, c.fiscal, c.period, c.posting_date, c.code, c.remark, c.amount_kredit from (
                select a.id, a.fiscal, a.period, b.posting_date, b.code, b.remark, b.amount_kredit, b.amount_debit  from bs_header a INNER JOIN bs_detail b ON a.id = b.header_id 
                where a.id = ".$rowHead["id"]." and b.code = 'CR' and a.fiscal = ".$rowHead["fiscal"]." and a.period=".$rowHead["period"]." and a.account_number ='".$rowHead["account_number"]."'
                ) c LEFT JOIN bankgl d ON c.amount_kredit = d.GLAA and d.GLAID ='".$AccountID."' and d.GLPN = ".$rowHead["period"]." and d.GLFY = ".$rowHead["fiscal"]."
                where c.amount_kredit IS NULL  "; */
                /*
                $sqlDetail4 = " SELECT GLDGJ as posting_date, GLDOC, GLDCT, GLEXA as remark, GLEXR, GLPN, GLFY, REPLACE(REPLACE(REPLACE(FORMAT(GLAA / 100, 2), ',', 'X'),'.', ','),'X', '.') AS  amount_kredit, GLAA/100 as forsum FROM bankgl b WHERE b.GLAID = '".$AccountID."'AND b.GLPN <= ".$rowHead["period"]." AND b.GLFY = ".$rowHead["fiscal"]." AND b.GLAA > 0 AND b.GLAA NOT IN (SELECT b.amount_kredit FROM bs_header a INNER JOIN bs_detail b ON a.id = b.header_id WHERE  b.CODE = 'DB' AND a.fiscal = ".$rowHead["fiscal"]." AND a.period <= ".$rowHead["period"]." AND a.account_number = '".$rowHead["account_number"]." AND a.user_id = " . $nik . "')";*/
$sqlDetail4 = "
     SELECT 
        GLDGJ AS posting_date, 
        GLDOC, 
        GLDCT, 
        GLEXA AS remark, 
        GLEXR, 
        GLPN, 
        GLFY, 
        REPLACE(
            REPLACE(
                REPLACE(FORMAT(GLAA / 100, 2), ',', 'X'), 
            '.', ','), 
        'X', '.') AS amount_kredit, 
        GLAA / 100 AS forsum 
    FROM 
        bankgl b 
    WHERE 
        b.GLAID = '" . $AccountID . "' 
        AND b.GLPN <= " . $rowHead["period"] . " 
        AND b.GLFY = " . $rowHead["fiscal"] . " 
        AND b.GLAA > 0 
        AND b.GLAA NOT IN (
            SELECT 
                ROUND(b.amount_kredit * 100) 
            FROM  
                bs_header a 
                INNER JOIN bs_detail b ON a.id = b.header_id 
            WHERE 
                b.CODE = 'CR' 
                AND a.fiscal = " . $rowHead["fiscal"] . " 
                AND a.period <= " . $rowHead["period"] . " 
                AND a.account_number = '" . $rowHead["account_number"] . "' 
                AND a.user_id = '" . $nik . "'
        )";

       // file_put_contents('debug.log', $sqlDetail4  . PHP_EOL, FILE_APPEND);

                $result4 = $connect->query($sqlDetail4);
                $row++;
                $sum4=0;
                foreach ($result4 as $data) {
                    $sheet->setCellValue('A' . $row, $data['posting_date']);
                    $sheet->setCellValue('B' . $row, $data['remark']);
                    $sheet->setCellValue('C' . $row, $data['amount_kredit']);
                   // $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                   $sum4 += $data['forsum'];
                    $row++;
                }
                $sheet->setCellValue('E'. $row, 'Rp');
                $sheet->setCellValue('F'. $row, number_format($sum4, 0, ',', '.'));
                $sheet->getStyle('F'. $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Section 5: SELISIH PENCATATAN ACCT DAN BANK
            $row += 2; // Add space
            $sheet->setCellValue('A' . $row, 'SELISIH PENCATATAN ACCT DAN BANK');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);

            $row++;
            $sheet->setCellValue('A' . $row, 'DATE');
         //   $sheet->setCellValue('B' . $row, 'REFF');
            $sheet->setCellValue('B' . $row, 'DESCRIPTIONS');
            $sheet->setCellValue('C' . $row, 'Rp.');
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':C' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


            // Section 6: SALDO BANK PER 30 JUNI 2024
            $row += 2; // Add space
            $sheet->setCellValue('A' . $row, 'SALDO BANK PER '.$formattedDate);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);

            $sheet->setCellValue('E'. $row, 'Rp');
            $sheet->setCellValue('F'. $row, number_format($rowHead["beginning_balance"]+$sum1+$sum2+$sum3+$sum4, 0, ',', '.'));
            $sheet->getStyle('F'. $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);


            $filename = $rowHead["bank_name"].'_'.$rowHead["fiscal"].'_'. $rowHead["period"].'.xlsx';

            $writer = new Xlsx($spreadsheet);
            //$writer->save($temp_file); 
            // Clear output buffer before sending file
            if (ob_get_length()) {
                ob_end_clean();
            }
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');


            $writer->save('php://output');
            exit;

            echo "Excel file created successfully generated excel! OK<br/>";
        }
    } 
?>
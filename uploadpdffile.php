<?php
//$start_time = microtime(true);
require_once('vendor/autoload.php');
include 'koneksi.php';
include 'normalizeAmount.php';
include 'generateQueryGL.php';
date_default_timezone_set('Asia/Jakarta');
$dateNow = date("Y-m-d H:i:s");
$userid = $_POST['useridhidden'];


$target_dir_bca = "C:/nginx/html/bankstatement/files/ScanBank/BCA/";
$target_dir_danamon = "C:/nginx/html/bankstatement/files/ScanBank/Danamon/";
$target_dir_mandiri = "C:/nginx/html/bankstatement/files/ScanBank/Mandiri/";

$dest_dir_bca = "C:/nginx/html/bankstatement/files/ExtractBank/BCA/";
$dest_dir_danamon = "C:/nginx/html/bankstatement/files/ExtractBank/Danamon/";  
$dest_dir_mandiri = "C:/nginx/html/bankstatement/files/ExtractBank/Mandiri/";


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["fileToUpload"])) {
 
    
// Prepare the statement
$stmt = $connect->prepare("SELECT nik, userid, email, fullname FROM username WHERE userid = ?");

// Bind the parameter (s = string)
$stmt->bind_param("s", $userid);

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    $useridrow = $row['nik'];
}

// Close the statement
$stmt->close();



    $original_filename = basename($_FILES["fileToUpload"]["name"]); //MANDIRI.PDF

    $extractedText = pathinfo($original_filename, PATHINFO_FILENAME);

    $date_time = date('Ymd_His'); 

    $file_ext = pathinfo($original_filename, PATHINFO_EXTENSION); // pdf

    $new_filename = $useridrow . "_" . $extractedText . '_' . $date_time . '.' . $file_ext;
   
                                                if ($extractedText == "DANAMON"){

                                                    $target_file = $target_dir_danamon . $new_filename;
                                                    $filepath = $target_dir_danamon . DIRECTORY_SEPARATOR . $new_filename;
                                                    $destpath =  $dest_dir_danamon . DIRECTORY_SEPARATOR . $new_filename;

                                                }else if ($extractedText == "BCA"){
                                                    
                                                    $target_file = $target_dir_bca . $new_filename;
                                                    $filepath = $target_dir_bca . DIRECTORY_SEPARATOR . $new_filename;
                                                    $destpath =  $dest_dir_bca . DIRECTORY_SEPARATOR . $new_filename;
                                                }else{
                                                    
                                                    $target_file = $target_dir_mandiri . $new_filename;
                                                    $filepath = $target_dir_mandiri . DIRECTORY_SEPARATOR . $new_filename;

                                                    $destpath =  $dest_dir_mandiri . DIRECTORY_SEPARATOR . $new_filename;
                                                }

    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    if ($_FILES["fileToUpload"]["size"] > 104857600) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    if ($fileType != "pdf") {
        $uploadOk = 0;
    }

  
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {

    
            $nanonetsApiUrl = 'https://app.nanonets.com/api/v2/OCR/Model/7d4b3832-190b-4ced-9cfa-af0eadc24361/LabelFile/';
            $nanonetsAuth = ['3afff200-73d5-11ee-aaae-221e96dee932', '']; // Basic Auth

            $client = new \GuzzleHttp\Client();
           try {
            $response = $client->request('POST', $nanonetsApiUrl, [
                'timeout' => 1800, // 30 minutes
                'auth' => $nanonetsAuth,
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($filepath, 'r'), // Open the file stream
                        'filename' => $original_filename, // Include the original file name
                    ],
                ],
            ]);

            $json_data =  $response->getBody()->getContents();
        //echo $json_data;
            $jsonArrayResponse = json_decode($json_data, true);


            if($jsonArrayResponse['message'] == "Success") {

                $results = $jsonArrayResponse['result'];
                $indexPage = 0;
                $Bank_Name = "";
                $codetransaction="";
                $Norek = "";
                $Branch = "";
                $Period  = "";
                $Currency = "";
                $index = 0;
                $indexRem = 0;
                $indexPostDate = 0;
                $indexDebit = 0;
                $indexKredit = 0;
                $indexSaldo = 0;
                $indexRef = 0;
                $indexDesc = 0;
                $indexArray = 0;
                $Customer_Name = "BERCA HARDAYAPERKASA";
        
                $cells = [];
                
                foreach ($results as $result) {
                    $predictions = $result['prediction'];
                    if($indexPage == 0) {
                        foreach ($predictions as $prediction) {
                            if($prediction['label'] == "Bank_Name") {
                                $Bank = ($prediction['ocr_text']);
                            }					
                            if($prediction['label'] == "Account_Number") {
                                $Norek = ($prediction['ocr_text']);
                            }
                            if($prediction['label'] == "Currency") {
                                $Currency = ($prediction['ocr_text']);
                            }
                            if($prediction['label'] == "Branch") {
                                $Branch = ($prediction['ocr_text']);
                            }
                            if($prediction['label'] == "Period" || $prediction['label'] == "Start_Period") {
                                $Period = ($prediction['ocr_text']);
                            }	
                            if ($Norek == "1150094006345"){	
                                if($prediction['label'] == "Beginning_Balance") {
                                    $Beginning_Balance = ($prediction['ocr_text']);
                                }	
                            }else{
                                $Beginning_Balance = 0;
                            }
                        }
        
        
                        if (isset($predictions)) {
                            foreach ($predictions as $predictionx) {
                                if ($predictionx['label'] === 'table' && isset($predictionx['cells'])) {
                                    foreach ($predictionx['cells'] as $cell) {
                                        $cell['page_no'] = $predictionx['page_no']; // Add page_no to the cell
                                        $cells[] = $cell;
                                    }
                                } 
                            } 
                        } 
                    } else {
                        if (isset($predictions)) {
                            foreach ($predictions as $predictionx) {
                                if ($predictionx['label'] === 'table' && isset($predictionx['cells'])) {
                                    foreach ($predictionx['cells'] as $cell) {
                                        $cell['page_no'] = $predictionx['page_no']; // Add page_no to the cell
                                        $cells[] = $cell;
                                    }
                                } 
                            } 
                        } 
                    } 
        
                    $indexPage++;
                }
                $user_id = 455;
        
        
                //DANAMON
                if ($Norek == "0001982651"){
                    $Bank_Name = "Danamon";
                    $Account_number = '1.11501.DAN01IDR';
      
                    if (strpos($Period, '-') !== false) {

                       preg_match('/[A-Za-z]+/', $Period, $matches);
                       preg_match('/\d{4}/', $Period, $yearMatches);
                       $monthName = $matches[0]; // This will give "Juli"

                      
                       $fiscalYear = substr($yearMatches[0], -2);
                       
                       $indonesianMonths = [
                           'Januari' => 1,
                           'Februari' => 2,
                           'Maret' => 3,
                           'April' => 4,
                           'Mei' => 5, 
                           'Juni' => 6,
                           'Juli' => 7,
                           'Agustus' => 8,
                           'September' => 9,
                           'Oktober' => 10,
                           'November' => 11,
                           'Desember' => 12,
                       ];
                       
                       $PeriodNumber = $indonesianMonths[$monthName];

                        $Beginning_Balance = generateQueryGL("00000315",$fiscalYear,$PeriodNumber);
                    } else {
                        $dateTime = DateTime::createFromFormat('d m F Y', $Period);
        
                        $yearTwoDigit = $dateTime->format('y');
        
                        $fiscalYear = $yearTwoDigit; // Output: 24
        
                        $monthMapping = [
                            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
                        ];
                    
                        $parts = explode(' ', ltrim(rtrim($Period)));
                    
                        $monthName = $parts[2];
                    
                        $PeriodNumber =  $monthMapping[$monthName] ?? null; // Return null if the month is not found
                        $Beginning_Balance = generateQueryGL("00000315",$fiscalYear,$PeriodNumber);
                    }

                //MANDIRI
                }else if ($Norek == "1150094006345"){
        
        
                    $dates = explode(" ", $Period);
                    
                    $startDate = implode(" ", array_slice($dates, 0, 3));
                    
                    $date = DateTime::createFromFormat('d M Y', $startDate);
                    $yearTwoDigit = $date->format('y'); // 'y' gives the two-digit year
                    $monthint = (int)$date->format('m'); // Get the month as an integer
        
                    $PeriodNumber = $monthint;
                    $fiscalYear = $yearTwoDigit;
        
                    $Account_number = '1.11501.MAN01IDR';
        
        
                    $Bank_Name = "Mandiri";  
                    $Beginning_Balance = generateQueryGL("00000317",$fiscalYear,$PeriodNumber);

                //BCA
                }else{
                    $Bank_Name = "BCA";
                    $Account_number = '1.11501.BCA01IDR';
                   /* $SplitPeriod = explode(" S / D ",$Period);
        
                    $date = DateTime::createFromFormat('d-m-y', $SplitPeriod[1]);
                    $month = (int) $date->format('m');
                    $yearTwoDigit = $date->format('y');
                    $PeriodNumber = $month; // Output: 4
                    $fiscalYear = $yearTwoDigit; */

                    $months = [
                        "JANUARI" => 1,
                        "FEBRUARI" => 2,
                        "MARET" => 3,
                        "APRIL" => 4,
                        "MEI" => 5,
                        "JUNI" => 6,
                        "JULI" => 7,
                        "AGUSTUS" => 8,
                        "SEPTEMBER" => 9,
                        "OKTOBER" => 10,
                        "NOVEMBER" => 11,
                        "DESEMBER" => 12
                    ];
                    
                    // Split the string into month and year
                    list($monthName, $yearFull) = explode(" ", strtoupper($Period));
                    
                    // Convert month name to number
                    $month = $months[$monthName] ?? 0; // Use 0 if the month is not found
                    
                    // Get last two digits of the year
                    $yearTwoDigit = substr($yearFull, -2);
                    
                    $PeriodNumber = $month;
                    $fiscalYear = $yearTwoDigit;

                    $Beginning_Balance = generateQueryGL("00000311",$fiscalYear,$PeriodNumber);
                }
        

                $bank_nameDel = $connect->real_escape_string($Bank_Name);
                $currencyDel = $connect->real_escape_string($Currency);
                $fiscalDel = $connect->real_escape_string($fiscalYear);
                $periodDel = $connect->real_escape_string($PeriodNumber);
                $useridrowDel = (int)$useridrow; // Assuming user_id is integer
                
                $header_ids = [];
                $sqlSelectDel = "
                    SELECT id FROM bs_header
                    WHERE bank_name = '$bank_nameDel'
                      AND currency = '$currencyDel'
                      AND fiscal = '$fiscalDel'
                      AND period = '$periodDel'
                      AND user_id = $useridrowDel
                ";
                $resSelectDel = $connect->query($sqlSelectDel);
                while ($rowtoDel = $resSelectDel->fetch_assoc()) {
                    $header_ids[] = $rowtoDel['id'];
                }
                if (count($header_ids) > 0) {
                    $id_list = implode(',', array_map('intval', $header_ids));
                    $connect->query("DELETE FROM bs_detail WHERE header_id IN ($id_list)");

                    $connect->query("DELETE FROM bs_header WHERE id IN ($id_list)");
                }



                $sqlInsertHeader = "INSERT INTO bs_header(
                            company, 
                            bank_name, 
                            bank_account, 
                            branch, 
                            currency, 
                            account_number,
                            fiscal, 
                            period, 
                            beginning_balance,
                            total_debit,
                            total_credit,
                            closing_balance,
                            user_id,
                            date_updated,
                            is_generated
                            ) VALUES 
                            (
                            '$Customer_Name', 
                            '$Bank_Name', 
                            '$Norek', 
                            '$Branch', 
                            '$Currency', 
                            '$Account_number',
                            '$fiscalYear', 
                            '$PeriodNumber', 
                            '$Beginning_Balance',
                            '10',
                            '10',
                            '10',
                            '$useridrow',
                            '$dateNow',
                            '0')";
                            
                            if ($connect->query($sqlInsertHeader) === TRUE) {
                                $BsHeaderId = mysqli_insert_id($connect);
        
                                $filteredData = array_map(function ($item) {
                                    return [
                                        'row' 		=> $item['page_no']." - ".$item['row'],
                                        'row_no' 	=> $item['row'],
                                        'page_no' 	=> $item['page_no'],
                                        'label' 	=> $item['label'],
                                        'text'		=> $item['text']
                                    ];
                                }, $cells); 
        
        
                                $records = [];
                                
                                foreach ($filteredData as $cell) {
                                    $row = $cell['row'];
                                    $records[$row][] = [
                                        'label' 	=> $cell['label'],
                                        'text' 		=> $cell['text'],
                                        'row' 		=> $cell['row'],
                                        'page_no'	=> $cell['page_no'],
                                        'row_no'	=> $cell['row_no']
                                    ];
                                }
                
                                $indexdet = 0;
                                $jsob ="";
                                foreach ($records as $key => $rows) {
                                    foreach ($rows as $rowss) {
                                        $indexdet++;
                                        
                                        if ($indexdet == 1){
                                            $jsob = "[";
                                        }
                                        $jsob = $jsob.json_encode($rowss).",";
                                    } 
                                }
                                $json_dat = substr($jsob, 0, -1) . "]";
                                
                                $dataxz = json_decode($json_dat, true); 
                            
                                $dataDetails = [];
                                foreach ($dataxz as $item) {
                                    $row = $item['row'];
                                    $label = $item['label'];
                                    $text = $item['text'];
                                
                                    $dataDetails[$row][$label] = $text;
                                    $dataDetails[$row]['page_no'] = $item['page_no'];
                                    $dataDetails[$row]['row_no'] = $item['row_no'];
        
                                } 
        
                                if ($Norek == "1150094006345" ){
                                    $defaultKeys = [
                                        "page_no" => "0",
                                        "row_no" => "0",
                                        "Saldo" => "0",
                                        "Posting_Date" => "",
                                        "Remark" => "",
                                        "Debit" => "0",
                                        "Kredit" => "0"
                                    ];
                                }else if ($Norek == "0001982651" ){
                                    $defaultKeys = [
                                        "page_no" => "0",
                                        "row_no" => "0",
                                        "Saldo" => "0",
                                        "Posting_Date" => "",
                                        "Desc" => "",
                                        "Debit" => "0",
                                        "Kredit" => "0"
                                    ];
                                }else{
                                    $defaultKeys = [
                                        "page_no" => "0",
                                        "row_no" => "0",
                                        "Saldo" => "0",
                                        "Posting_Date" => "",
                                        "Desc" => "",
                                        "Debit_Kredit" => "0",
                                        "Kredit" => "0"
                                    ];
        
                                }
        
                                foreach ($dataDetails as &$itemsx) {
                                    foreach ($defaultKeys as $key => $defaultValue) {
                                        if (!array_key_exists($key, $itemsx)) {
                                            $itemsx[$key] = $defaultValue; 
                                        }
                                    }
                                }
        
                                foreach ($dataDetails as $values) {
 
                                    if ($Norek == "1150094006345"){//MANDIRI
										$RemarkRaw 		= str_replace("'", "", $values['Remark']);
										$Remark = preg_replace("/[^a-zA-Z0-9\s.,-]/", "", $RemarkRaw);
										
                                       // $Debit 			= str_replace([',', '.'], '', $values['Debit']);
                                      //  $Kredit 		= str_replace([',', '.'], '', $values['Kredit']);
										
										$Debit          = normalizeAmount($values['Debit'], 'us');  
                                        $Kredit         = normalizeAmount($values['Kredit'], 'us'); 
                                     
                                        $Saldo          = normalizeAmount($values['Saldo'], 'us'); 
										
										
                                        if ($Debit == "000"){
                                            $Debit = "0";
                                        }
        
                                        if ($Kredit == "000"){
                                            $Kredit = "0";
                                        }
                                        if ($Debit == "0"){
                                            $codetransaction = "CR";
                                        }else{
                                            $codetransaction = "DB";
                                        }
                                    }else if ($Norek == "0001982651"){ //DANAMON
                                        $RemarkRaw 		= str_replace("'", "", $values['Desc']);
										$Remark = preg_replace("/[^a-zA-Z0-9\s.,-]/", "", $RemarkRaw);
										$Debit          = normalizeAmount($values['Debit'], 'euro');  
                                        $Kredit         = normalizeAmount($values['Kredit'], 'euro'); 
                                        $Saldo          = normalizeAmount($values['Saldo'], 'euro'); 
                                        if ($Debit == "0"){
                                            $codetransaction = "CR";
                                        }else{
                                            $codetransaction = "DB";
                                        }
                                    }else {  //BCA
										$RemarkRaw 		= $values['Desc'];
										$Remark = preg_replace("/[^a-zA-Z0-9\s.,-]/", "", $RemarkRaw);
                                       // $Debit 			= str_replace([',', '.'], '', $values['Debit_Kredit']);
                                        //$Kredit 		= str_replace([',', '.'], '', $values['Debit_Kredit']);

                                        $rawValue = '';
                                        if (!empty($values['Debit_Kredit']) && $values['Debit_Kredit'] != "0") {
                                            $rawValue = $values['Debit_Kredit'];
                                        } elseif (!empty($values['Kredit']) && $values['Kredit'] != "0") {
                                            $rawValue = $values['Kredit'];
                                        } elseif (!empty($values['Saldo']) && $values['Saldo'] != "0") {
                                            $rawValue = $values['Saldo'];
                                        }
  
                                        $AmountBCA = $rawValue;

                                        if(substr($AmountBCA, -2)=="DB"){  
                                            //$Debit 			= preg_replace('/[a-zA-Z]/','',str_replace([',', '.'], '', $values['Debit_Kredit']));
											$DebitBCA 	= preg_replace('/[A-Za-z\s]+$/', '', $AmountBCA);
											$Debit          = normalizeAmount($DebitBCA, 'us');  
											//echo $Debit;
                                            $Kredit 	= "0";
                                            
                                            $codetransaction = "DB";
                                        }else{ 
                                   
											$KreditBCA 		= preg_replace('/[A-Za-z\s]+$/', '', $AmountBCA);
											$Kredit          = normalizeAmount($KreditBCA, 'us');  
                                            $Debit 	= "0";
                                            $codetransaction = "CR";
                                        }
										 $Saldo          = normalizeAmount($values['Saldo'], 'us'); 
                                    }
        
                                    $page_no		= $values['page_no']+1;
                                    $row_no			= $values['row_no'];
                                    $Posting_Date 	= $values['Posting_Date'];
        
                                  //  $Saldo 			= str_replace([',', '.'], '', $values['Saldo']);
        
                                  //  $cleanRemark = str_replace('`', '', $Remark);
                                    //echo $cleanRemark;
                                    $sqlInsertDetail = "INSERT INTO bs_detail(
                                        header_id, 
                                        page_no,
                                        row_no,
                                        remark,
                                        posting_date,
                                        code,
                                        amount_debit,
                                        amount_kredit, 
                                        saldo,
                                        date_updated) VALUES 
                                        (
                                        '$BsHeaderId', 
                                        '$page_no',
                                        '$row_no',
                                        '$Remark',
                                        '$Posting_Date',
                                        '$codetransaction',
                                        '$Debit',
                                        '$Kredit',
                                        '$Saldo',
                                        '$dateNow')";
        
                                                if ($connect->query($sqlInsertDetail) === true) {
                                         
                                                } else  {
                                                    echo "Error: " .
                                                        $sqlInsertDetail .
                                                        "<br>" .
                                                        $connect->error;
                                                }
                                }   						
        
                            }
                            $connect->close();
            }
                    if (rename($filepath, $destpath)) {
                        echo json_encode(["status" => "success", "message" => "File uploaded successfully."]);
                      
                    } else {
                        echo "Failed to move the file.";
                        
                    }
                /////////////////////////////////////////////CATCH/////////////////////////////////////////////////////
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                
                echo "Request error for $original_filename: " . $e->getMessage();
                if ($e->hasResponse()) {
                    echo "Response: " . nl2br(htmlspecialchars($e->getResponse()->getBody()->getContents()));
                }
            } 
        
        
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
    
   // $end_time = microtime(true);
   // $execution_time = ($end_time - $start_time) * 1000; 
   
}else{
    echo json_encode(["status" => "error", "message" => "Failed to upload file."]);
} 



?>
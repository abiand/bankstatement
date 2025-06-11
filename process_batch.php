<?php
include 'koneksi.php';
include 'generateQueryGL.php';
date_default_timezone_set('Asia/Jakarta');
$dateNow = date("Y-m-d H:i:s");

function isValidDate($date) {
    $d = DateTime::createFromFormat('d/m', $date);
    return $d && $d->format('d/m') === $date;
}

//$json_data = file_get_contents('C:\nginx\html\bankstatement\files\ScanBank\Danamon\danamon.json');
//$json_data = file_get_contents('C:\nginx\html\bankstatement\files\ScanBank\Mandiri\mandiri.json');
$json_data = file_get_contents('C:\nginx\html\bankstatement\files\ScanBank\BCA\bca.json');
$jsonArrayResponse = json_decode($json_data, true);
/*
	echo "<pre>";
	print_r($jsonArrayResponse);
	echo "</pre>"; */
		
	if($jsonArrayResponse['message'] == "Success") {

		$results = $jsonArrayResponse['result'];
		$indexPage = 0;
		$Bank_Name = "";
		$codetransaction="";
		$Norek = "";
		$Period  = "";
		//$Currency = "";
		//$dataDetails = [];
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
		$balance_amount ="";

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
					if($prediction['label'] == "Period") {
						$Period = ($prediction['ocr_text']);
					}	
					if ($Norek == "1150094006345"){	
						if($prediction['label'] == "Beginning_Balance") {
							$Beginning_Balance = str_replace([',', '.'], '', ($prediction['ocr_text']));
							//$Debit 			= str_replace([',', '.'], '', ($prediction['ocr_text']));
						}	
					}else{
						$Beginning_Balance = 0;
					}
				}


			//if ($Norek == "1150094006345"){	
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
			//}
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
				//echo "The string contains a hyphen.";

				preg_match('/\b\d{4}\b/', $Period, $matches);

				if (!empty($matches)) {
					// Extract the 4-digit year and convert it to a 2-digit year
					$fullYear = $matches[0];
					$twoDigitYear = substr($fullYear, -2);
					$fiscalYear = $twoDigitYear; // Output: 24
				} else {
					echo "Year not found in the string.";
				}

				$months = [
					'Januari' => 1,'Februari' => 2,'Maret' => 3,'April' => 4,'Mei' => 5,'Juni' => 6,'Juli' => 7,'Agustus' => 8,'September' => 9,'Oktober' => 10,'November' => 11,'Desember' => 12
				];

				echo "conto";
				if (preg_match('/\d{2} - \d{2} ([a-zA-Z]+)/', ltrim(rtrim($Period)), $matches)) {
					
					$monthName = $matches[1];
					$PeriodNumber = $months[$monthName] ?? null; // Return the month number or null if not found
				}

			} else {
				//echo "The string does not contain a hyphen.";
				// Create a DateTime object from the string
				$dateTime = DateTime::createFromFormat('d m F Y', $Period);

				// Get the year in 2-digit format
				$yearTwoDigit = $dateTime->format('y');

				$fiscalYear = $yearTwoDigit; // Output: 24


				$monthMapping = [
					'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
				];
			
				// Split the date string into parts
				$parts = explode(' ', ltrim(rtrim($Period)));
			
				// Extract the month name
				$monthName = $parts[2];
			
				// Return the corresponding month integer
				$PeriodNumber =  $monthMapping[$monthName] ?? null; // Return null if the month is not found
			}

			$balance_amount = generateQueryGL("00000315",$fiscalYear,$PeriodNumber);


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
			$balance_amount = generateQueryGL("00000317",$fiscalYear,$PeriodNumber);
		//BCA
		}else{
			$Bank_Name = "BCA";
			$Account_number = '1.11501.BCA01IDR';
			$SplitPeriod = explode(" S/D ",$Period);


			$date = DateTime::createFromFormat('d-m-y', $SplitPeriod[1]);
			$month = (int) $date->format('m');
			$yearTwoDigit = $date->format('y');
			$PeriodNumber = $month; // Output: 4
			$fiscalYear = $yearTwoDigit;

			$balance_amount = generateQueryGL("00000311",$fiscalYear,$PeriodNumber);
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
					balance_amount,
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
					'$balance_amount',
					'$Beginning_Balance',
					'10',
					'10',
					'10',
					'$user_id',
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
								//$postingDate = ($rowss['label'] == 'Posting_Date') ? $rowss['text'];
							} 
						}
						$json_dat = substr($jsob, 0, -1) . "]";
						
						$dataxz = json_decode($json_dat, true); 
					
						$dataDetails = [];
						foreach ($dataxz as $item) {
							//$indexdetail++;
							$row = $item['row'];
							$label = $item['label'];
							$text = $item['text'];
						
							$dataDetails[$row][$label] = $text;
							$dataDetails[$row]['page_no'] = $item['page_no'];
							$dataDetails[$row]['row_no'] = $item['row_no'];

						} 

						/*
						echo "<pre><br/>";
						echo "index ALL Cells <br/>";
						//	print_r(json_encode($recordsq));
					 	print_r(json_encode($dataDetails,JSON_PRETTY_PRINT));
						//	print_r($dataDetails);
						echo "</pre>";  */



						//DEFINE DEFAULT ARRAY EACH BANK
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
								"Posting_Date" => "01/01",
								"Desc" => "",
								"Debit" => "0",
								"Kredit" => "0"
							];
						}else{
							$defaultKeys = [
								"page_no" => "0",
								"row_no" => "0",
								"Saldo" => "0",
								"Posting_Date" => "01/01",
								"Desc" => "",
								"Debit_Kredit" => "0"
							];

						}


						foreach ($dataDetails as &$itemsx) {
							foreach ($defaultKeys as $key => $defaultValue) {
								if (!array_key_exists($key, $itemsx)) {
									$itemsx[$key] = $defaultValue; // Add the missing key with default value
								}
							}
						}

						foreach ($dataDetails as $key => $values) {
							//echo $values['Desc']." ".$values['Debit_Kredit']." ".$values['Posting_Date']."<br>";

							//echo $values['Desc']." ".$values['Debit_Kredit']." ".$values['Posting_Date']."<br>";
							//echo $values['Desc']."<br>";
							if ($Norek == "1150094006345"){
								$Remark 		= $values['Remark'];
								$Debit 			= str_replace([',', '.'], '', $values['Debit']);
								$Kredit 		= str_replace([',', '.'], '', $values['Kredit']);
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

								list($date, $time) = explode(" ", $values['Posting_Date']);
								list($day, $month, $year) = explode("/", $date);
								$monthName = DateTime::createFromFormat('m', $month)->format('M');
								$yearShort = substr($year, -2);
								$formattedDate = "$day-$monthName-$yearShort";
								$Posting_Date = $formattedDate;
								
							}else if ($Norek == "0001982651"){
								$Remark 		= $values['Desc'];
								$Debit 			= str_replace([',', '.'], '', $values['Debit']);
								$Kredit 		= str_replace([',', '.'], '', $values['Kredit']);
								if ($Debit == "0"){
									$codetransaction = "CR";
								}else{
									$codetransaction = "DB";
								}
								$defaultDate = "01/01";
								//$Posting_Date = $values['Posting_Date'];
							    if (!isValidDate($values['Posting_Date'])) {
									//$dataDetails[$key] = $defaultDate;
									$Posting_Date = $defaultDate;
								}else{
									
									$Posting_Date = $values['Posting_Date'];
								}

								list($days, $months) = explode("/", $Posting_Date);
								$monthNames = DateTime::createFromFormat('m', $months)->format('M');
								$formattedDates = "$days-$monthNames-$fiscalYear";
								$Posting_Date = $formattedDates;
								
							}else { 
								//echo "masuk bca ".$values['Debit_Kredit']."<br/>";
								$Remark 		= $values['Desc'];
								$Debit 			= str_replace([',', '.'], '', $values['Debit_Kredit']);
								$Kredit 		= str_replace([',', '.'], '', $values['Debit_Kredit']);
								if(substr($Debit, -2)=="DB" || $Debit=="0"){  
									$Debit 			= preg_replace('/[a-zA-Z]/','',str_replace([',', '.'], '', $values['Debit_Kredit']));
									$Kredit 	= "0";
									
									$codetransaction = "DB";
								}else{ //echo "KRED";
									$Kredit 		= preg_replace('/[a-zA-Z]/','',str_replace([',', '.'], '', $values['Debit_Kredit']));
									
									$Debit 	= "0";
									$codetransaction = "CR";
								}






								list($days, $months) = explode("/", $values['Posting_Date']);
								$monthNames = DateTime::createFromFormat('m', $months)->format('M');
								$formattedDates = "$days-$monthNames-$fiscalYear";
								$Posting_Date = $formattedDates;

							

							}

							$page_no		= $values['page_no']+1;
							$row_no			= $values['row_no'];
							
					



							$Saldo 			= str_replace([',', '.'], '', $values['Saldo']);

							
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
											//echo "$sqlInsertDetail";
										} else  {
											echo "Error: " .
												$sqlInsertDetail .
												"<br>" .
												$connect->error;
										}
						}   						/*echo "<pre><br/>";
						echo "index ALL Cells <br/>";
						//print_r(json_encode($recordsq));
					//	print_r(json_encode($recordsq,JSON_PRETTY_PRINT));
						print_r($dataDetails);
						echo "</pre>";  
						echo "New record created successfully"; */
					}
					$connect->close();
	}
<?php
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');
$dateNow = date("Y-m-d H:i:s");

//Read From Folder
$paths = ["C:/nginx/html/bankstatement/files/ScanBank/BCA", 
		 "C:/nginx/html/bankstatement/files/ScanBank/Danamon",
		 "C:/nginx/html/bankstatement/files/ScanBank/Mandiri"];

//$url = "https://app.nanonets.com/api/v2/OCR/Model/297f234f-d498-4d93-8b63-e53fad148326/LabelFile/";
$url = "https://app.nanonets.com/api/v2/OCR/Model/9d446474-624a-4674-bc8f-487664244195/LabelFile/";


foreach ($paths as $path) {
	$processFiles = array_values(array_filter(scandir($path), function($file) use ($path) { 
		return !is_dir($path . '/' . $file);
	}));

	//Looping File
	foreach($processFiles as $processFile){
		$conn = oci_connect("JDE", "B1t24680", "10.0.2.57:1521/jdeorcl");
		//echo "$processFile </br>";

		$file = curl_file_create($path."/".$processFile);
		$upload_date = date("YmdHis");

		$sourceDirectory = $path.'/'.$processFile;
		$destinationDirectory = 'C:/xampp/htdocs/nanonets/files/ExtractINV/'.$upload_date.'_'.$processFile;
			//echo $destinationDirectory."<br/>";
			// Upload file

			$data = array('file'=> $file);
			$content = json_encode($data); // convert to json
			//print_r($content);
			
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER,
			array("Content-type: multipart/form-data"));
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERPWD, "ae81bd42-fd53-11ee-8637-0e7fc6dd2f51");
			//curl_setopt($curl, CURLOPT_POSTFIELDS, $data);


			if (curl_exec($curl) === false){
				echo 'Curl error: ' . curl_error($curl)."<br/>";
			} else {
				$curlResponse = curl_exec($curl);

				$jsonArrayResponse = json_decode($curlResponse, true);
				
				echo "<pre>";
				print_r($jsonArrayResponse);
				echo "</pre>";
				
			/*

				//Insert to Database
				if($jsonArrayResponse['message'] == "Success") {
					$document_no = 1;
					$company = "JDE";

					$invoice_no = "";
					$invoice_date = "";
					$supplier_name = "";
					$po_reference = "";
					$invoice_amount = "";
					$invoice_tax_amount = "";
					$due_date = "";
					$currency = "";
					$inv_doc_type = "";

					$item_description = "";
					$item_number = "";
					$quantity = "";
					$uom = "";
					$unit_price = "";
					$amount = "";

					$results = $jsonArrayResponse['result'];
					$indexPage = 0;

					$dataDetails = array();
					$index = 0;
					$indexArray = 0;

					foreach ($results as $result) {
					$predictions = $result['prediction'];
					if($indexPage == 0) {
						foreach ($predictions as $prediction) {
						//Header
						if($prediction['label'] == "invoice_number") {
							$invoice_no = ($prediction['ocr_text']);
						}
						if($prediction['label'] == "invoice_date") {
							$invoice_date = ($prediction['ocr_text']);
						}
						if($prediction['label'] == "nama_perusahaan") {
							$supplier_name = ($prediction['ocr_text']);
						}
						if($prediction['label'] == "po_number") {
							$po_reference = ($prediction['ocr_text']);
						}
						if($prediction['label'] == "invoice_amount") {
							$invoice_amount = ($prediction['ocr_text']);
						}
						if($prediction['label'] == "tax") {
							$invoice_tax_amount = ($prediction['ocr_text']);
						}
						if($prediction['label'] == "invoice_amount_currency") {
							$currency = ($prediction['ocr_text']);
						}
						if($prediction['label'] == "due_date") {
							$due_date = ($prediction['ocr_text']);
						}

						if ($po_reference === null){
							$document_type = 'POInvoice';
						} else {
							$document_type = 'STDInvoice';
						}
						
						if ($path == "C:/xampp/htdocs/nanonets/files/PO - PV"){
							$inv_doc_type = 0;
						} elseif ($path == "C:/xampp/htdocs/nanonets/files/PO - P2") {
							$inv_doc_type = 1;
						} elseif ($path == "C:/xampp/htdocs/nanonets/files/Non PO - PV") {
							$inv_doc_type = 3;
						} elseif ($path == "C:/xampp/htdocs/nanonets/files/Non PO - P2") {
							$inv_doc_type = 2;
						}
						

						//Detail
						if($prediction['label'] == "table") {
							$cells = ($prediction['cells']);
							foreach($cells as $cell) {
								if($cell['label'] == "nama_barang") {
									if($index > 0) {
										// var_dump("Insert : " . $item_description." index : ". $index);
										$dataDetails[$indexArray]['nama_barang'] = $item_description;
										$dataDetails[$indexArray]['qty'] = $quantity;
										$dataDetails[$indexArray]['UOM'] = $uom;
										$dataDetails[$indexArray]['harga_satuan'] = $unit_price;
										$dataDetails[$indexArray]['sub_harga'] = $amount;
										$dataDetails[$indexArray]['item_number'] = $item_number;
										$indexArray++;
										$item_description = "";
										$item_number = "";
										$quantity = "";
										$uom = "";
										$unit_price = "";
										$amount = "";
									}
									$item_description = ($cell['text']);

									$index++; 
								}

								if($cell['label'] == "qty") {
									$quantity = ($cell['text']);
								}

								if($cell['label'] == "UOM") {
									$uom = ($cell['text']);
								}

								if($cell['label'] == "harga_satuan") {
									$unit_price = ($cell['text']);
								}

								if($cell['label'] == "sub_harga") {
									$amount = ($cell['text']);
								}

								if($cell['label'] == "item_number") {
									$item_number = ($cell['text']);
								}

							}
						}
					}
					} else {
					foreach ($predictions as $prediction) {
						if($prediction['label'] == "table") {
							$cells = ($prediction['cells']);
							foreach($cells as $cell) {
								if($cell['label'] == "nama_barang") {
									if($index > 0) {
										$dataDetails[$indexArray]['nama_barang'] = $item_description;
										$dataDetails[$indexArray]['qty'] = $quantity;
										$dataDetails[$indexArray]['UOM'] = $uom;
										$dataDetails[$indexArray]['harga_satuan'] = $unit_price;
										$dataDetails[$indexArray]['sub_harga'] = $amount;
										$dataDetails[$indexArray]['item_number'] = $item_number;
										$indexArray++;
										$item_description = "";
										$item_number = "";
										$quantity = "";
										$uom = "";
										$unit_price = "";
										$amount = "";
									}
									$item_description = ($cell['text']);
								}

								if($cell['label'] == "qty") {
									$quantity = ($cell['text']);
								}

								if($cell['label'] == "UOM") {
									$uom = ($cell['text']);
								}

								if($cell['label'] == "harga_satuan") {
									$unit_price = ($cell['text']);
								}

								if($cell['label'] == "sub_harga") {
									$amount = ($cell['text']);
								}

								if($cell['label'] == "item_number") {
									$item_number = ($cell['text']);
								}

								$index++; 

							}
						}
					}
				}

				$indexPage++;

				}
					//Insert Last Data
							$dataDetails[$indexArray]['nama_barang'] = $item_description;
							$dataDetails[$indexArray]['qty'] = $quantity;
							$dataDetails[$indexArray]['UOM'] = $uom;
							$dataDetails[$indexArray]['harga_satuan'] = $unit_price;
							$dataDetails[$indexArray]['sub_harga'] = $amount;
							$dataDetails[$indexArray]['item_number'] = $item_number;
							$indexArray++;

					//Insert
					$sqlInsertHeader = "INSERT INTO invoice_header(
						id, 
						document_no, 
						company, 
						invoice_no, 
						invoice_date, 
						supplier_name, 
						po_reference, 
						invoice_amount,
						invoice_tax_amount,
						due_date,
						currency,
						document_type,
						inv_doc_type,
						user_reference,
						date_updated, 
						user_date, 
						file) VALUES 
						('', 
						$document_no, 
						'$company', 
						'$invoice_no', 
						'$invoice_date', 
						'$supplier_name', 
						'$po_reference', 
						'$invoice_amount',
						'$invoice_tax_amount',
						'$due_date',
						'$currency',
						'$document_type',
						'$inv_doc_type',
						'$destinationDirectory', 
						'$dateNow', 
						'$dateNow', 
						'".mysql_escape_string(file_get_contents($path."/".$processFile)) ."')";

					//$InsertInvoiceHeader = mysqli_query($connect, $sqlInsertHeader);
					//$invoiceHeaderId = mysqli_insert_id($connect);
						var_dump($dataDetails);
					if ($connect->query($sqlInsertHeader) === TRUE) {
						$invoiceHeaderId = mysqli_insert_id($connect);
						$sql3 = "SELECT TVLITM, TVDSC1, TVDSC2, TVDSC3 FROM testdta.f554311 where tvdoco = '$po_reference'";
						$stid = oci_parse($conn, $sql3);
						$r = oci_execute($stid);
						$dataArray = array();
						while ($row = oci_fetch_assoc($stid)) {
							$dataLitm[] = $row['TVLITM'];
							$dataDsc1[] = $row['TVDSC1'];
							$dataDsc2[] = $row['TVDSC2'];
							$dataDsc3[] = $row['TVDSC3'];
						}

						foreach($dataDetails as $dataDetail) {

							$item_status = 0;
							$item_temp = "xxxxx";

								// Cek item number di item number OCR
								$item_number = $dataDetail['item_number'];
								echo "</br> item_number = $item_number </br>";
								if($item_status == 0){
									$item_number = $dataDetail['item_number'];
									if(isset($item_number)){
									foreach ($dataLitm as $item) {
									if (strpos(trim($item_number), trim($item)) !== false) {
											$item_number = trim($item);
											$item_status = 1;
											echo "status_item 1 </br>";
											} 
										}
									}
								}

								// Cek item number di item description OCR
								if($item_status <> 1){
										foreach ($dataLitm as $item) {
										$item_number = $dataDetail['nama_barang'];
										if(isset($item_number)){
										if (strpos(trim($item_number), trim($item)) !== false) {
												$item_number = $item;
												$item_status = 2;
												echo "status_item 2 </br>";
												} else {
													$item_number = "";
												}
										}
									}
								}

								// Cek item description OCR di item description 1
								if($item_status > 2 || $item_status == 0){
										foreach ($dataDsc1 as $item) {
										$item_number = $dataDetail['nama_barang'];
										echo "item_number = $item_number </br>";
										$item_temp = "xxxxx";
										if(isset($item_number)){
										if (strpos(trim($item_number), trim($item)) !== false) {
												$item_temp = trim($item);
												$item_status = 3;
												echo "status_item 3 </br>";
												echo "$item_temp </br>";
												} else {
													$item_number = "";
												}
											}
									}
								}

								if($item_status > 3 || $item_status == 0){
										foreach ($dataDsc2 as $item) {
											$item_number = $dataDetail['nama_barang'];
											$item_temp = "xxxxx";
											if(isset($item_number)){
										if (strpos(trim($item_number), trim($item)) !== false) {
												$item_temp = trim($item);
												$item_status = 4;
												echo "status_item 4 </br>";
												} else {
													$item_number = "";
												}
											} 
									}
								}

								if($item_status > 4 || $item_status == 0){
										foreach ($dataDsc3 as $item) {
											$item_number = $dataDetail['nama_barang'];
											$item_temp = "xxxxx";
											if(isset($item_number)){
										if (strpos(trim($item_number), trim($item)) !== false) {
												$item_temp = trim($item);
												$item_status = 5;
												echo "status_item 5 </br>";
												} else {
													$item_number = "";
												}
											}
									}
								}
								
								if($item_status == 3 || $item_status == 4 || $item_status == 5){
								$sql2 = "SELECT DISTINCT TVLITM FROM testdta.f554311 where upper(TVDSC3) like upper('%$item_temp%') and tvdoco = '$po_reference'";
								$finditem = oci_parse($conn, $sql2);
								$run = oci_execute($finditem);
								if ($run) {
									while ($row = oci_fetch_assoc($finditem)) {
										$item_number = $row['TVLITM'];
									}
								}
								echo "run oracle query $sql2 </br>";
								}
							
							echo "item number insert $item_number </br>";
							$sqlInsertDetail = "INSERT INTO invoice_detail(
								id, 
								invoice_id, 
								item_description, 
								item_number, 
								quantity, 
								uom, 
								unit_price,
								amount,
								user_date) VALUES 
								('', 
								'$invoiceHeaderId', 
								'$dataDetail[nama_barang]', 
								'$item_number', 
								'$dataDetail[qty]', 
								'$dataDetail[UOM]', 
								'$dataDetail[harga_satuan]', 
								'$dataDetail[sub_harga]',
								'$dateNow')";

							if ($connect->query($sqlInsertDetail) === TRUE) {
								echo "$sqlInsertDetail";
							} else {
								echo "Error: " . $sqlInsertDetail . "<br>" . $connect->error;
							}
						}

						echo "New record created successfully";
					} else {
						echo "Error: " . $sqlInsertHeader . "<br>" . $connect->error;
					}
				}

				if (rename($sourceDirectory, $destinationDirectory)) {
					echo "File moved successfully to '{$destinationDirectory}'.\n";
				} else {
					echo "Error moving file '{$file}'.\n";
				}
*/
			}

			curl_close($curl);
			oci_close($conn);
			
	}
}
?>
<!-- <script> location.replace("document_list.php"); </script> -->
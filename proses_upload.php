<?php
	include 'koneksi.php';
    date_default_timezone_set('Asia/Jakarta');
	$dateNow = date("Y-m-d H:i:s");
	$invoiceHeaderId = 0;

	if(isset($_POST["submit"]))
	{
		$url = "https://app.nanonets.com/api/v2/OCR/Model/297f234f-d498-4d93-8b63-e53fad148326/LabelFile/";
		$file = curl_file_create($_FILES['fileToUpload']['tmp_name'],$_FILES['fileToUpload']['type'],$_FILES['fileToUpload']['name']);
		
		// Upload file

		$data = array('file'=> $file);
		$content = json_encode($data);
		
		$curl = curl_init($url);
    	curl_setopt($curl, CURLOPT_HEADER, false);
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($curl, CURLOPT_HTTPHEADER,
        array("Content-type: multipart/form-data"));
    	curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, "6ed320fc-73b7-11ee-a93f-82d8b5f93d01:");
    	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    	if (curl_exec($curl) === false){
    		echo 'Curl error: ' . curl_error($curl);
    	} else {
    		$curlResponse = curl_exec($curl);

    		$jsonArrayResponse = json_decode($curlResponse, true);
    		
    		echo "<pre>";
    		print_r($jsonArrayResponse);
    		echo "</pre>";

    		//Insert to Database
    		if($jsonArrayResponse['message'] == "Success") {
    			$document_no = 1;
    			$company = "JDE";

    			$invoice_no = "";
    			$invoice_date = "";
    			$supplier_name = "";
    			$po_reference = "";

    			$item_description = "";
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

                    if ($po_reference === null){
                        $document_type = 'POInvoice';
                    } else {
                        $document_type = 'STDInvoice';
                    }

    				//Detail
    				if($prediction['label'] == "table") {
    					$cells = ($prediction['cells']);
    					foreach($cells as $cell) {
    						if($cell['label'] == "nama_barang") {
    							if($index > 0) {
    								var_dump("Insert : " . $item_description." index : ". $index);
    								$dataDetails[$indexArray]['nama_barang'] = $item_description;
    								$dataDetails[$indexArray]['qty'] = $quantity;
    								$dataDetails[$indexArray]['UOM'] = $uom;
    								$dataDetails[$indexArray]['harga_satuan'] = $unit_price;
    								$dataDetails[$indexArray]['sub_harga'] = $amount;
    								$indexArray++;
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
    								$indexArray++;
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
    					$indexArray++;

    			//Insert
    			$sqlInsertHeader = "INSERT INTO invoice_header(id, document_no, company, invoice_no, invoice_date, supplier_name, po_reference, document_type, date_updated, user_date, file) VALUES ('', $document_no, '$company', '$invoice_no', '$invoice_date', '$supplier_name', '$po_reference', '$document_type', '$dateNow', '$dateNow', '".mysql_escape_string(file_get_contents($_FILES['fileToUpload']["tmp_name"])) ."')";

    			//$InsertInvoiceHeader = mysqli_query($connect, $sqlInsertHeader);
            	//$invoiceHeaderId = mysqli_insert_id($connect);
    				var_dump($dataDetails);
            	if ($connect->query($sqlInsertHeader) === TRUE) {
            		$invoiceHeaderId = mysqli_insert_id($connect);
            		foreach($dataDetails as $dataDetail) {
            			$sqlInsertDetail = "INSERT INTO invoice_detail(id, invoice_id, item_description, quantity, uom, unit_price, amount) VALUES ('', $invoiceHeaderId, '$dataDetail[nama_barang]', '$dataDetail[qty]', '$dataDetail[UOM]', '$dataDetail[harga_satuan]', '$dataDetail[sub_harga]')";

            			if ($connect->query($sqlInsertDetail) === TRUE) {
            			} else {
            				echo "Error: " . $sqlInsertDetail . "<br>" . $connect->error;
            			}
            		}

            		echo "New record created successfully";


            	} else {
            		echo "Error: " . $sqlInsertHeader . "<br>" . $connect->error;
            	}
    		}

    	}

    	curl_close($curl);

	}
?>
<script> location.replace("document_list.php"); </script>
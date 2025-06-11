<?php
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');
$dateNow = date("Y-m-d H:i:s");

$invoice_id = $_POST['invoice_id'];
$invoice_no = $_POST['invoice_no'];
$invoice_date = $_POST['invoice_date'];
$supplier_name = $_POST['supplier_name'];
$po_reference = $_POST['po_reference'];

$countDetail = $_POST['count_row'];

if ($po_reference === null){
    $document_type = 'POInvoice';
} else {
    $document_type = 'STDInvoice';
}

$sqlUpdateHeader = "UPDATE invoice_header SET invoice_no = '$invoice_no', invoice_date = '$invoice_date', supplier_name = '$supplier_name', po_reference = '$po_reference', document_type = '$document_type' date_updated = '$dateNow', is_validate = '1' WHERE id = $invoice_id";

if ($connect->query($sqlUpdateHeader) === TRUE) {
	for($i=0; $i<$countDetail; $i++) {
		$item_description = $_POST['item_description'][$i];
		$quantity = $_POST['quantity'][$i];
		$uom = $_POST['uom'][$i];
		$unit_price = $_POST['unit_price'][$i];
		$amount = $_POST['amount'][$i];
		$invoice_detail_id = $_POST['invoice_detail_id'][$i];

		$sqlUpdateDetail = "UPDATE invoice_detail SET item_description = '$item_description', quantity = '$quantity', uom = '$uom', unit_price = '$unit_price', amount = '$amount', date_updated = '$dateNow' WHERE id = $invoice_detail_id";

		if ($connect->query($sqlUpdateDetail) === TRUE) {
		} else {
			echo "Error: " . $sqlUpdateDetail . "<br>" . $connect->error;
		}
		
	}
    echo "New record created successfully";

	    $url = "http://192.168.9.39:9281/jderest/orchestrator/ARD_GenVoucherMatch";

		// JSON payload
		$data = array(
		    "username" => "jde",
		    "password" => "jde",
		    "InvoiceNo" => "$invoice_no"
		);

		// Initialize cURL session
		$ch = curl_init($url);

		// Set cURL options
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    'Content-Type: application/json',
		    'Content-Length: ' . strlen(json_encode($data))
		));

		// Execute cURL session and get the response
		$response = curl_exec($ch);

		// Check for errors
		if (curl_errno($ch)) {
		    echo 'Curl error: ' . curl_error($ch);
		}

		// Close cURL session
		curl_close($ch);

		// Output the response
		// echo $response;
} else {
	echo "Error: " . $sqlUpdateHeader . "<br>" . $connect->error;
}

header("location: compare.php?invoice_id=".$invoice_id);

?>
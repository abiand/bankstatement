<?php
include 'koneksi.php';

$invoice_id = $_GET['invoice_id'];

$sqlDeleteDetail = "DELETE FROM invoice_detail WHERE invoice_id = $invoice_id";

if ($connect->query($sqlDeleteDetail) === TRUE) {	
	$sqlDeleteHeader = "DELETE FROM invoice_header WHERE id = $invoice_id";

	if ($connect->query($sqlDeleteHeader) === TRUE) {
	} else {
		echo "Error: " . $sqlDeleteHeader . "<br>" . $connect->error;
	}
    echo "Delete Success";
    header("location: document_list.php");
} else {
	echo "Error: " . $sqlDeleteDetail . "<br>" . $connect->error;
}



?>
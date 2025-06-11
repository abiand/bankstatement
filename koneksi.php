<?php

	$database = "bankstatement";
	$hostname = "localhost";
	$uname 	  = "root";
	$password = "@dmin123";

	$connect = mysqli_connect($hostname, $uname, $password, $database);

$conn = oci_connect("JDE", "B1t24680", "10.0.2.56:1521/jdeorcl");
if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}




?>
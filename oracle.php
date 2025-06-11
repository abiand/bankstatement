<?php

include 'koneksi.php';

/*$conn = oci_connect("JDE", "JDE", "192.168.9.38:1521/orcl");
if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}*/

// Execute query
// $sql = "SELECT TVLITM FROM testdta.f554311 where tvdoco = '5494'";
// $stid = oci_parse($conn, $sql);
// if (!$stid) {
//     $e = oci_error($conn);
//     trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
// }

// // Execute statement
// $r = oci_execute($stid);
// if (!$r) {
//     $e = oci_error($stid);
//     trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
// }

// // Fetch data into an array
// $dataArray = array();
// while ($row = oci_fetch_assoc($stid)) {
//     $dataArray[] = $row['TVLITM'];
// }

// Close connection
// oci_free_statement($stid);
// oci_close($conn);
// print_r($dataArray);

// String to search for
// $searchString = "742258#3AR";

// // Iterate over the array and check if any element exists within the string
// foreach ($dataArray as $item) {
//     if (strpos(trim($searchString), trim($item)) !== false) {
//         echo "String '$item' found within '$searchString'.<br>";
//     }
// }
/*$index = 0;
$sql = "SELECT TVLITM, TVDSC1, TVDSC2, TVDSC3 FROM testdta.f554311 where tvdoco = '5068'";
					$stid = oci_parse($conn, $sql);
					$r = oci_execute($stid);
					// $dataArray = array();
					// while ($row = oci_fetch_assoc($stid)) {
					// 	$dataLitm[] = $row['TVLITM'];
					// 	$dataDsc1[] = $row['TVDSC1'];
					// 	$dataDsc2[] = $row['TVDSC2'];
					// 	$dataDsc3[] = $row['TVDSC3'];
					// }
                    // $resultArray = array(); // Initialize an empty array to store the results

                    if ($r) {
                        // Fetch rows and add them to the result array
                        while ($row = oci_fetch_assoc($stid)) {
                            $resultArray[] = $row;
                        }
                    }
                    */
                    
// print_r($resultArray);

// $item_status = 3;
// if($item_status > 2 || $item_status == 0){
//     echo 'true';
// } else {
//     echo 'false';
// }

// if($item_status <> 1){
//     if(isset($item_number)){
//         foreach ($resultArray as $item) {
//         $item_number = $dataDetail['nama_barang'];
//         if (strpos(trim($item_number), trim($item)) !== false) {
//                 $item_number = $item;
//                 $item_status = 2;
//                 } else {
//                     $item_number = "";
//                 }
//         }
//     }
// // }
//                             print_r($item_number);

/*
$item_status = 0;
$sql = "SELECT TVLITM, TVDSC1, TVDSC2, TVDSC3 FROM testdta.f554311 where tvdoco = '5507'";
					$stid = oci_parse($conn, $sql);
					$r = oci_execute($stid);
					$dataArray = array();
					while ($row = oci_fetch_assoc($stid)) {
						$dataLitm[] = $row['TVLITM'];
						$dataDsc1[] = $row['TVDSC1'];
						$dataDsc2[] = $row['TVDSC2'];
						$dataDsc3[] = $row['TVDSC3'];
					}
					oci_free_statement($stid);


if($item_status > 2 || $item_status == 0){
    foreach ($dataDsc1 as $item) {
    $item_number = 'KAKI STABIL 10 CM GELAS ELITE';
    echo "item_number = $item_number";
    $item_temp = "xxxxx";
    if(isset($item_number)){
    if (strpos(trim($item_number), trim($item)) !== false) {
            $item_temp = trim($item);
            $item_status = 3;
            echo "status_item 3";
            echo " item_ temp = $item_temp";
            } else {
                $item_number = "";
            }
        }
}
}

*/



if (!$conn) {
    $e = oci_error();
    die("Database connection failed: " . $e['message']);
}

// SQL query
$sql = "SELECT SUM(GBAPYC) + SUM(GBAN01) + SUM(GBAN02) + SUM(GBAN03) + SUM(GBAN04) AS TOTAL 
        FROM PRODDTA.F0902 
        WHERE GBAID = '00000311' 
          AND GBFY = 23 
          AND GBLT = 'AA'";

// Prepare the query
$stid = oci_parse($conn, $sql);
if (!$stid) {
    $e = oci_error($conn);
    die("Error parsing query: " . $e['message']);
}

// Execute the query
if (!oci_execute($stid)) {
    $e = oci_error($stid);
    die("Error executing query: " . $e['message']);
}

// Fetch the result
$row = oci_fetch_assoc($stid);
if ($row) {
    echo "Total: " . $row['TOTAL'] . "\n";
} else {
    echo "No rows returned.\n";
}

// Free the statement and close the connection
oci_free_statement($stid);
oci_close($conn);

?>


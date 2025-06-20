<?php
//include 'koneksi.php';

/*
function generateQueryGL($glaid,$twoDigitYear,$nMonth) {
    $result = [];
    $conn = oci_connect("JDE", "B1t24680", "10.0.2.56:1521/jdeorcl");
 	for ($i = 1; $i <= $nMonth; $i++) {
        $result[] = 'sum(GBAN' . str_pad($i, 2, '0', STR_PAD_LEFT).')';
    }
    
    $sql =  "select sum(GBAPYC) + ".implode(' + ', $result)." AS \"TOTAL\"  from PRODDTA.F0902 where GBAID = '".$glaid."' and GBFY = ".$twoDigitYear." and GBLT = 'AA'";
//echo $sql;
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
    return $row['TOTAL'];
} else {
    return "No rows returned";
}

// Free the statement and close the connection
oci_free_statement($stid);
oci_close($conn);


} */

/*
function generateQueryGL($glaid, $twoDigitYear, $nMonth) {
    $result = [];
    $conn = oci_connect("JDE", "B1t24680", "10.0.2.56:1521/jdeorcl");
    if (!$conn) {
        $e = oci_error();
        die("Error connecting to the database: " . $e['message']);
    }

    for ($i = 1; $i <= $nMonth; $i++) {
        $result[] = 'sum(GBAN' . str_pad($i, 2, '0', STR_PAD_LEFT) . ')';
    }

    $sql = "SELECT SUM(GBAPYC) + " . implode(' + ', $result) . " AS \"TOTAL\" " .
           "FROM PRODDTA.F0902 " .
           "WHERE GBAID = :glaid AND GBFY = :twoDigitYear AND GBLT = 'AA'";

    // Prepare the query
    $stid = oci_parse($conn, $sql);
    if (!$stid) {
        $e = oci_error($conn);
        die("Error parsing query: " . $e['message']);
    }

    // Bind parameters
    oci_bind_by_name($stid, ":glaid", $glaid);
    oci_bind_by_name($stid, ":twoDigitYear", $twoDigitYear);

    // Execute the query
    if (!oci_execute($stid)) {
        $e = oci_error($stid);
        die("Error executing query: " . $e['message']);
    }

    // Fetch the result
    $row = oci_fetch_assoc($stid);
    if ($row) {
        $total = $row['TOTAL'];
    } else {
        $total = "No rows returned";
    }

    // Free the statement and close the connection
    oci_free_statement($stid);
    oci_close($conn);

    return $total;
}
*/


function generateQueryGL($glaid, $twoDigitYear, $nMonth) {
    
    include 'koneksi.php';
    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }
    
    $result = [];
    
    for ($i = 1; $i <= $nMonth; $i++) {
        $result[] = 'SUM(GBAN' . str_pad($i, 2, '0', STR_PAD_LEFT) . ')';
    }
    
    $sql = "SELECT SUM(GBAPYC) + " . implode(' + ', $result) . " AS TOTAL FROM balance WHERE GBAID = ? AND GBFY = ? AND GBLT = 'AA'";
  //  file_put_contents('debug.log', $sql . PHP_EOL, FILE_APPEND);
    $stmt = $connect->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("ss", $glaid, $twoDigitYear);
        $stmt->execute();
        $stmt->bind_result($total);
        $stmt->fetch();
        $stmt->close();
    
        return ($total !== null ? $total : 0);
    } else {
        return 000;
    }
   
    $connect->close();
}

// echo generateQueryGL("00000317",25,6);

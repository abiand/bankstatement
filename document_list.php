<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}
?>


<!DOCTYPE html>
<html>

<style>

/*th,td{
border-color: black !important;
border: 2px solid black;
text-align: center;
}*/

thead{
text-align: center;
}

.top-right-logout {
    position: absolute;
    top: 20px;
    right: 30px;
    font-family: Arial, sans-serif;
}
.top-right-logout a {
    color: #007bff;
    text-decoration: none;
    font-weight: bold;
}
.top-right-logout a:hover {
    text-decoration: underline;
}
</style>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Batch List</title>

	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="bootstrap/css/all.css">
	<script src="bootstrap/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
	
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<img src="img/download2.png" width="50" height="50" alt="" style=";margin:0px 30px 0px 0px">
		
		<div class="collapse navbar-collapse" id="navbarNavAltMarkup">
			<div class="navbar-nav">
				<a class="nav-item nav-link" href="index.php">Upload File</a>
				<a class="nav-item nav-link active" href="document_list.php"><strong>Document List</strong><span class="sr-only">(current)</span></a>
			</div>
		</div>
	</nav>
	<br>
	<center>
		<?php 
		// Set the number of records per page
		$recordsPerPage = 10;

		// Calculate the current page
		$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

		// Calculate the starting record for the current page
		$startFrom = ($currentPage - 1) * $recordsPerPage;
		?>

		<!-- Search Bar Form -->
		<form method="GET" action="document_list.php">
			<div class="input-group mb-3 mt-4" style="width: 300px;">
				<input type="text" class="form-control" placeholder="Search by Vendor or Invoice" name="search">
				<div class="input-group-append">
					<button class="btn btn-primary" type="submit">Search</button>
				</div>
			</div>
		</form>

		<div class="container">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th style="width: 5%;" >No</th>
						<th style="width: 20%">Bank Name</th>
						<th style="width: 20%">Bank Account</th>
						<th style="width: 15%">Branch</th>
						<th style="width: 10%">Currency</th>
						<th style="width: 10%">Fiscal</th>
						<th style="width: 10%">Period</th>
						<th style="width: 10%">User</th>
						<th style="width: 10%">Upload Date</th>
						<th style="width: 15%">Action</th>
					</tr>
				</thead>
				<?php
				include 'koneksi.php';
				$no = 0;

				$username = $_SESSION["username"];
				$nik = null;
				$sql = "SELECT nik FROM username WHERE userid = ?";
				$stmt = mysqli_prepare($connect, $sql);
				if ($stmt) {
					mysqli_stmt_bind_param($stmt, "s", $username);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_bind_result($stmt, $nik);
			
					if (mysqli_stmt_fetch($stmt)) {
						echo "NIK: " . $nik;
					} 	/*else {
						echo "No NIK found for user: " . $username;
					} */
				
					mysqli_stmt_close($stmt);
				} else {
					echo "Query preparation failed: " . mysqli_error($connect);
				}


				$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
				$searchCondition = empty($searchTerm) ? '' : "AND (bank_name LIKE '%$searchTerm%' OR bank_account LIKE '%$searchTerm%')";
				
				$GetBatchUpload = mysqli_query($connect, "SELECT * FROM bs_header WHERE user_id = $nik and is_generated = 0 $searchCondition ORDER BY date_updated DESC LIMIT $startFrom, $recordsPerPage");
				
				while($a = mysqli_fetch_array($GetBatchUpload)) {
					$no++;
					?>
					<tr>
						<td style="text-align: center"><?php echo $no; ?></td>
						<td><?php echo $a['bank_name']; ?></td>
						<td><?php echo $a['bank_account']; ?></td>
						<td><?php echo $a['branch']; ?></td>
						<td><?php echo $a['currency']; ?></td>
						<td><?php echo $a['fiscal']; ?></td>
						<td><?php echo $a['period']; ?></td>
						<td><?php echo $a['user_id']; ?></td>
						<td><?php echo $a['date_updated']; ?></td>
					
						<td style="text-align: center">
						<!--<a href="GenerateExcel.php?id=<?php echo $a['id'] ?>" class="btn btn-success btn-sm" role="button">Generate Excel</a>-->
						<a href="javascript:void(0);" class="btn btn-success btn-sm generateExcelBtn" data-id="<?php echo $a['id']; ?>" data-nik="<?php echo htmlspecialchars($nik); ?>">Generate Excel</a>
							<!--<a href="compare.php?invoice_id=<?php echo $a['id'] ?>" class="btn btn-success btn-sm" role="button">Compare</a>
							<a href="process_delete.php?invoice_id=<?php echo $a['id'] ?>" class="btn btn-danger btn-sm" role="button" onclick="return confirm('Are You sure?')">Delete</a>-->
						</td>
					</tr>
					<?php
				}
				?>
			</table>
    <!-- Progress Modal -->
    <div class="modal fade" id="progressModal" tabindex="-1" aria-labelledby="progressModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Generating Excel, please wait...</p>
          </div>
        </div>
      </div>
    </div>
			<!-- Bootstrap Pagination Links -->
			<nav aria-label="Page navigation example">
				<ul class="pagination justify-content-center">
					<?php
					$sql = "SELECT COUNT(*) AS total FROM bs_header WHERE is_generated = 0 $searchCondition";
					$result = $connect->query($sql);
					$row = $result->fetch_assoc();
					$totalPages = ceil($row['total'] / $recordsPerPage);

					for ($i = 1; $i <= $totalPages; $i++) {
						$activeClass = ($i == $currentPage) ? 'active' : '';
						echo '<li class="page-item ' . $activeClass . '"><a class="page-link" href="?page=' . $i . '&search=' . $searchTerm . '">' . $i . '</a></li>';
					}
					?>
				</ul>
			</nav>
		</div>

		<div class="top-right-logout">
    Welcome, <?= htmlspecialchars($_SESSION["username"]) ?> |
    <a href="logout.php">Logout</a>
</div>

	</center>
	<script>
    $(document).ready(function() {
        $('.generateExcelBtn').on('click', function() {
            const excelId = $(this).data('id');
			const nik = $(this).data('nik');
            $('#progressModal').modal('show');
            setTimeout(() => {
                window.location.href = `GenerateExcel.php?id=${encodeURIComponent(excelId)}&nik=${encodeURIComponent(nik)}`;
                setTimeout(() => {
                    $('#progressModal').modal('hide');
                }, 3000);
            }, 500);
        });
    });
    </script>
</body>
</html>

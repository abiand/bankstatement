<!DOCTYPE html>
<html>
<style type="text/css">
	thead{
		text-align: center;
}
</style>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Compare ECS</title>

	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="bootstrap/upload.css">
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<img src="img/download2.png" width="50" height="50" alt="" style=";margin:0px 30px 0px 0px">
		
		<div class="collapse navbar-collapse" id="navbarNavAltMarkup">
			<div class="navbar-nav">
				<a class="nav-item nav-link" href="index.php">Single Upload</a>
				<a class="nav-item nav-link" href="batch.php">Mass Upload</a>
				<a class="nav-item nav-link active" href="document_list.php"><strong>Document List</strong><span class="sr-only">(current)</span></a>
			</div>
		</div>
	</nav>
	<center>
		<h2>COMPARE</h2>
		<br>
		<div class="container" style="max-width: 90%">	
			<?php 
			include 'koneksi.php';
			
			$invoiceID 		= $_GET['invoice_id'];

			$GetBatchUploadList = mysqli_query($connect, "select * FROM invoice_header WHERE id = $invoiceID");
			$GetInvoiceDetails = mysqli_query($connect, "select * FROM invoice_detail WHERE invoice_id = $invoiceID");
			if($a = mysqli_fetch_array($GetBatchUploadList)) {
			
				$myData[] 		= null;
				$countDetail	= 0;

				$comparison		= true;

				?>
				<form method="POST" action="proses_submit.php">
					<div class="card">
						<div class="card-header">
							<h4><?php echo $a['invoice_no'] ?></h4>
							<?php if ($a['is_validate'] == 0) {
							?>
							<span class="badge badge-pill badge-warning">Not Validated</span>
							<?php
						} else {
							?>
							<span class="badge badge-pill badge-info">Validated</span>
							<?php
						} ?>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col">
									<?php 
									//INPUT
									try {
										$objectInput = $a['file'];
										echo '<iframe id=frame src="data:application/pdf;base64,'.base64_encode( $objectInput ).'" height="600" width="700"></iframe>';

										$comparison = true;
									} catch (Exception $e) {
										echo "File Does Not Exist";
										$comparison = false;
									}
									?>
								</div>
								<?php
								if ($comparison) {
									?>
									<div class="col">
										<?php 
										//OUTPUT
										try {
											?>

											<table> 
											<input type="hidden" name="invoice_id" value="<?php echo $a['id'] ?>">
											
											<tr>
												<td> Invoice Number </td>
												<td> <input class="form-control" type="text" name="invoice_no" value="<?php echo $a['invoice_no'] ?>"> </td><td></td>
												<td> Invoice Date </td>
												<td> <input class="form-control" type="text" name="invoice_date" value="<?php echo $a['invoice_date'] ?>"> </td>
											</tr>

											<tr>
												<td> Supplier Name </td>
												<td> <input class="form-control" type="text" name="supplier_name" value="<?php echo $a['supplier_name'] ?>"> </td><td></td>
												<td> PO Reference </td>
												<td> <input class="form-control" type="text" name="po_reference" value="<?php echo $a['po_reference'] ?>"> </td>
											</tr>

											<tr>
												<td> Invocie Amount </td>
												<td> <input class="form-control" type="text" name="invoice_amount" value="<?php echo $a['invoice_amount'] ?>"> </td><td></td>
												<td> Invoice Tax </td>
												<td> <input class="form-control" type="text" name="invoice_tax_amount" value="<?php echo $a['invoice_tax_amount'] ?>"> </td>
											</tr>

											<tr>
												<td> Due Date </td>
												<td> <input class="form-control" type="text" name="due_date" value="<?php echo $a['due_date'] ?>"> </td><td>&nbsp &nbsp</td>
												<td> Currency </td>
												<td> <input class="form-control" type="text" name="currency" value="<?php echo $a['currency'] ?>"> </td>
											</tr>
											</table>
											<br>
											<br>
											<table class="table table-bordered table-sm" style="width: 100%">
												<thead>
													<tr class="table-info">
														<th scope="col" style="width: 5%">No</th>
														<th scope="col" style="width: 40%">Item Name</th>
														<th scope="col" style="width: 5%">Quantity</th>
														<th scope="col" style="width: 10%">UOM</th>
														<th scope="col" style="width: 15%">Unit Price</th>
														<th scope="col" style="width: 15%">Amount</th>
													</tr>
												</thead>
												<tbody>
													<?php
													$no = 0;
													while($dataDetail = mysqli_fetch_array($GetInvoiceDetails)) {
														$no++;
														?>
														<tr>
															<th style="font-size: 10px; text-align: center;" scope="row"><?php echo $no ?></th>
															<input type="hidden" name="invoice_detail_id[]" value="<?php echo $dataDetail['id'] ?>">
      														<td> <input class="form-control" type="text" name="item_description[]" value="<?php echo $dataDetail['item_description'] ?>" style="font-size: 10px"></td>
      														<td><input class="form-control" type="text" name="quantity[]" value="<?php echo $dataDetail['quantity'] ?>" style="font-size: 10px"></td>
      														<td><input class="form-control" type="text" name="uom[]" value="<?php echo $dataDetail['uom'] ?>" style="font-size: 10px"></td>
      														<td><input class="form-control" type="text" name="unit_price[]" value="<?php echo $dataDetail['unit_price'] ?>"style="font-size: 10px"></td>
      														<td><input  class="form-control" type="text" name="amount[]" value="<?php echo $dataDetail['amount'] ?>" style="font-size: 10px"></td>
														</tr>
														<?php
													}
													?>
												</tbody>
											</table>

											<?php
										} catch (Exception $e) {
											echo "<h5> No Comparison </h5>";
											$comparison = false;
										}
										?>
									</div>
									<?php 
								}
								?>
							</div>
						</div>
						<?php 
						if($comparison) {
							?>
							<div class="row">
								<div class="col">
								</div>
							</div>
							<input type="hidden" name="count_row" value="<?php echo $no ?>">
							<button type="submit" class="btn btn-primary float-right" name="submit">Validate</button>
							<?php 
						}
						?>
					</div>
				</form>
				<br>
				<?php 
			}
			?>
		</div>

	</center>

</body>
</html>
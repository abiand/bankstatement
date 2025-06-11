
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>New Upload</title>

	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<img src="img/download2.png" width="50" height="50" alt="" style="margin:0px 30px 0px 0px">
		
		<div class="collapse navbar-collapse" id="navbarNavAltMarkup">
			<div class="navbar-nav">
				<a class="nav-item nav-link" href="index.php">Single Upload</a>
				<a class="nav-item nav-link active" href="batch.php"><strong>Mass Upload </strong><span class="sr-only">(current)</span></a>
				<a class="nav-item nav-link" href="document_list.php">Document List</a>
			</div>
		</div>
	</nav>
	<br>
	<center>
		<div class="jumbotron">
			<h1 class="display-4">Process Document</h1>
			<p class="lead">This Page will be trigger process all file in foler ScanINV</p>
			<hr class="my-4">
			<p>Upload your PDF file in file sharing \\192.168.9.58\ScanINV</p>
			<p class="lead">
				<a class="btn btn-primary btn-lg" href="process_batch.php" role="button" type="submit" id="submit">Process</a>
			</p>
				<p class="d-none" id="proses">Processing...<span><img width="70" height="70" src="img/ajax-loader.gif"></span></p>
		</div>
	</center>
</body>
</html>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

    <script type="text/javascript">
    	$("#submit").on("click", function() {
    		$("#proses").removeClass("d-none");
    		$("#submit").prop("style", "pointer-events: none; background-color: grey");
    	});
    </script>
	
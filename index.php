<?php
session_start();

include 'koneksi.php';

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
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>New Upload</title>

	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <style>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const uploadButton = document.getElementById('uploadBtn');
    const fileInput = document.querySelector('input[type="file"]');
    const form = uploadButton.closest('form');

    form.addEventListener('submit', function (e) {
        if (!fileInput.files || fileInput.files.length === 0) {
            e.preventDefault();  // block form submission
            alert('Please select a file before uploading!');
            uploadButton.disabled = false;  // keep button active
        } else {
            uploadButton.disabled = true;  // prevent double submission
            // allow form to proceed naturally (including AJAX handlers if present)
        }
    });
});
</script>





</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<img src="img/download2.png" width="50" height="50" alt="" style=";margin:0px 30px 0px 0px">
		
		<div class="collapse navbar-collapse" id="navbarNavAltMarkup">
			<div class="navbar-nav">
				<a class="nav-item nav-link active" href="#"><strong>Upload File</strong><span class="sr-only">(current)</span></a>
				<a class="nav-item nav-link" href="document_list.php">Document List</a>
			</div>
		</div>
	</nav>
	<br>
	<center>
		<div class="card bg-light mb-3" style="max-width: 18rem;">
			<div class="card-header">Select file to upload</div>
			<div class="card-body">
				<form enctype="multipart/form-data" id="form-input">
					<div class="form-group text-center">
						<br><br>
                        <input type="hidden" name="useridhidden" value="<?= htmlspecialchars($_SESSION["username"]) ?>">
						<input type="file" class="form-control-file" name="fileToUpload" id="fileToUpload" multiple>
						<br><br>
					</div>
					<button type="submit" id="uploadBtn" class="btn btn-primary mb-2" value="Upload Image" name="submit">Upload</button>
					<p class="d-none" id="proses">Processing...<span><img width="70" height="70" src="img/ajax-loader.gif"></span></p>
				</form>
			</div>
		</div>
	</center>

<!-- Bootstrap Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                File uploaded successfully!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="top-right-logout">
    Welcome, <?= htmlspecialchars($_SESSION["username"]) ?> |
    <a href="logout.php">Logout</a>
</div>


</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!--<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

    <script type="text/javascript">
    	$("#submit").on("click", function() {
    		$('#form-input').submit();
    		$("#proses").removeClass("d-none");
    		$("#submit").prop("style", "pointer-events: none; background-color: grey");
    	});
    </script>

<script>
$(document).ready(function () {
    $("#form-input").submit(function (e) {
        e.preventDefault(); // Prevent default form submission

        var formData = new FormData(this);

		$("#submit").prop("disabled", true); 
        $("#proses").removeClass("d-none"); // Show loading

        $.ajax({
            url: "uploadpdffile.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
				$("#submit").prop("disabled", false); // Re-enable button
                $("#proses").addClass("d-none"); // Hide loading

				console.log("Server Response:", response); 
				try {
                    var res = JSON.parse(response);
                    if (res.status === "success") {
                        $("#successModal").modal("show"); // Show modal on success
                    } else {
                        alert("Upload failed: " + res.message);
                    }
                } catch (e) {
                    alert("Unexpected response from server.");
                }
            },
            error: function () {
				$("#submit").prop("disabled", false); // Re-enable button
                $("#proses").addClass("d-none"); // Hide loading
                alert("File upload failed!");
            }
        });
    });

	$(document).off("submit", "#form-input").on("submit", "#form-input", function (e) {
        e.preventDefault();
    });
});
</script>

</html>



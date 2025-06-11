<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>PDF Viewer</title>
<body>	
	<div style="margin: 0 auto;">
		<iframe id="framePdf" width="100%" height="750px" style="top: 50%; bottom: 50%;"></iframe>
	</div>
	<script>
		const params = new URLSearchParams(window.location.search);
		const data = params.get('data');
		const dataAll = data.split('/');
		const linkSrc =  dataAll[3]+ "/" + dataAll[4]+ "/" + dataAll[5] + "/" + dataAll[6];
		const iframe = document.getElementById("framePdf")
		iframe.src="http://192.168.9.58/" + linkSrc;
	</script>

</body>
</html>
<!DOCTYPE html>


<html>
	<head>
		<link rel="stylesheet" href="/Web/css/uploadr.css" style="text/css" />
		<link href='http://fonts.googleapis.com/css?family=Oxygen' rel='stylesheet' type='text/css' />
		<link href='http://fonts.googleapis.com/css?family=Telex' rel='stylesheet' type='text/css' />
		<meta charset="UTF-8">
		</head>
	<body>
		<script type="text/javascript">
		
	
		function getFile()
		{
			document.getElementById("file").click();

			var file = document.getElementById("file").value;
							
			document.getElementById("uploads").innerHTML = file+" selected";
		}
		
		</script>
		<div id="maincontent">


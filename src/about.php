<?php
include_once("global.php");
include_once("protected/config.php");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>About HSDLinks</title>
		<meta charset="utf-8">
		<meta name="author" content="Dylan Hansch">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<link rel="shortcut icon" content="none">
		
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
	</head>
	<body>
		<?php include("navbar.php"); ?>
		
		<div class="container">
			<div class="row">
				<div class="col-lg-12" style="padding-top:50px;">
					<h1>About HSDLinks</h1>
					<p>HSDLinks is a private URL shortener made by Dylan Hansch. It's open source, so feel free to view the code <a href="https://github.com/dylanhansch/hsdlinks">here</a>.</p>
					
					<h2>Bug Reports and Feature Requests</h2>
					<p>If you have any bug reports or feature requests please email Dylan Hansch at <a href="mailto:dylan@dylanhansch.net">dylan@dylanhansch.net</a>. Or, if you happen to have a Github account, feel free to open an Issue on the <a href="https://github.com/dylanhansch/hsdlinks/issues">Github Project Page</a>.</p>
				</div>
			</div>
		</div>
		
		<?php include("footer.php"); ?>
	</body>
</html>
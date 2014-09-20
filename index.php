<?php
include_once("protected/config.php");

$id = $_GET['id'];

$stmt = $mysqli->prepare("SELECT `url` FROM `links` WHERE `short` = ?");
$stmt->bind_param('s', $id);
$stmt->execute();
$stmt->bind_result($url);

if($stmt->fetch()){
    header("Location: ". $url);
	exit;
}else{
	header('404 Not Found');
}

$stmt->free_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>HSD Links</title>
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
					<h1>HSDLinks <small>Alpha</small></h1>
				</div>
			</div>
		</div>
		
		<?php include("footer.php"); ?>
	</body>
</html>
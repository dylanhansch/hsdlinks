<?php
include_once('protected/config.php');

if(isset($_GET['pop'])){
	$stmt = $mysqli->prepare("
	CREATE TABLE `links` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`short` varchar(255) NOT NULL,
		`url` varchar(255) NOT NULL,
		`privacy` varchar(255) NOT NULL,
		PRIMARY KEY (`id`)
	)
	");
	echo($mysqli->error);
	$stmt->execute();
	$stmt->close();
	
	$stmt = $mysqli->prepare("
	CREATE TABLE `privileges` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`user_id` int(11) NOT NULL,
		`link_id` int(11) NOT NULL,
		`role` varchar(255) NOT NULL,
		PRIMARY KEY (`id`)
	)
	");
	echo($mysqli->error);
	$stmt->execute();
	$stmt->close();
	
	$stmt = $mysqli->prepare("
	CREATE TABLE `users` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`username` varchar(24) NOT NULL,
		`email` varchar(255) NOT NULL,
		`firstname` varchar(255) NOT NULL,
		`lastname` varchar(255) NOT NULL,
		`password` varchar(255) NOT NULL,
		`ip_address` varchar(255) NOT NULL,
		`sign_up_date` date NOT NULL,
		`role` varchar(255) NOT NULL,
		PRIMARY KEY (`id`)
	)
	");
	echo($mysqli->error);
	$stmt->execute();
	$stmt->close();
	
	header("Location: install.php?success");
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Install <?php echo($app); ?></title>
		
		<?php include_once('lib/header.php'); ?>
	</head>
	<body>
		<?php include_once('lib/navbar.php'); ?>
		
		<div class="container">
			<div class="row">
				<div class="col-lg-12" style="padding-top:50px;">
					<?php if(isset($_GET['success'])){ ?>
					
					<h1 style="color:green">Congrats!</h1>
					<p><?php echo($app); ?> is successfully installed, you can now shorten links under your own domain, with meaningful URLs!</p>
					
					<h2 style="color:red">ALERT!</h2>
					<p>You <strong>MUST</strong> remove install.php if you want this application to be secure! Failing to do so, <strong>WILL</strong> result in a loss of data once a malicious user comes along.</p>
					
					<?php }else{ ?>
					
					<h1>Install Links</h1>
					<hr>
					<h3>Step 1.</h3>
					<p>Create a database. For example a database called, "hsdlinks".
					
					<h3>Step 2.</h3>
					<p>Fill out the config file in "protected/config.php" with the relevant information.</p>
					
					<h3>Step 3.</h3>
					<p>Populate the database with necessary tables.</p>
					<button class="btn btn-warning" onclick="populate_confirmation()">Populate Database</button>
					
					<?php } ?>
				</div>
			</div>
		</div>
		
		<?php include_once('lib/footer.php'); ?>
	</body>
</html>
<?php
include_once('protected/config.php');
include_once('lib/global.php');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>About <?php echo($app); ?></title>
		
		<?php include_once('lib/header.php'); ?>
	</head>
	<body>
		<?php include_once('lib/navbar.php'); ?>
		
		<div class="container">
			<div class="row">
				<div class="col-lg-12" style="padding-top:50px;">
					<h1>About <?php echo($app); ?></h1>
					<p>HSDLinks is a custom URL shortener made by Dylan Hansch. It's open source, so feel free to view the code <a href="https://github.com/dylanhansch/hsdlinks">here</a>.</p>
					
					<h2>Bug Reports and Feature Requests</h2>
					<p>If you have any bug reports or feature requests please email Dylan Hansch at <a href="mailto:dylan@dylanhansch.net">dylan@dylanhansch.net</a>. Or, if you happen to have a Github account, feel free to open an Issue on the <a href="https://github.com/dylanhansch/hsdlinks/issues">Github Project Page</a>.</p>
				</div>
			</div>
		</div>
		
		<?php include_once('lib/footer.php'); ?>
	</body>
</html>
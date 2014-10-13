<?php
include_once("global.php");
include_once("protected/config.php");

if($logged == 0){
	header("Location: " . $basedir);
}

$message = "";
if(isset($_POST['original'])){

	$form_orig = $_POST['original'];
	$form_short = $_POST['short'];
	$form_privacy = $_POST['privacy'];
	$owner = $_SESSION["id"];
	
	//error handling
	if( (!$form_orig) || (!$form_short) || (!$form_privacy) ){
		$message = "Please complete all the fields below!";
	}else{
		if($form_short == $links["short"]){
			$message = "That is already a shortened URL!";
		}else{
			if(substr($form_orig, 0, 7) != "http://"){
				$message = "Original URL must be HTTP or HTTPS protocol.";
			}elseif(substr($form_orig, 0, 8) != "https://"){
				$message = "Original URL must be HTTP or HTTPS protocol.";
			}else{
				$stmt = $mysqli->prepare("INSERT INTO links (short, url, privacy, owner) VALUES (?, ?, ?, ?)");
				$stmt->bind_param('sssi', $form_short, $form_orig, $form_privacy, $owner);
				$stmt->execute();
				
				$message = "HSDLink created.";
			}
			
			/*if(substr($form_orig, 0, 7) != "http://"){
				$message = "Original URL must be HTTP or HTTPS protocol.";
			}else{
			$stmt = $mysqli->prepare("INSERT INTO links (short, url, privacy, owner) VALUES (?, ?, ?, ?)");
			$stmt->bind_param('sssi', $form_short, $form_orig, $form_privacy, $owner);
			$stmt->execute();
			
			$message = "HSDLink created.";	
			}
			if(substr($form_orig, 0, 8) != "https://"){
				$message = "Original URL must be HTTP or HTTPS protocol.";
			}else{
			$stmt = $mysqli->prepare("INSERT INTO links (short, url, privacy, owner) VALUES (?, ?, ?, ?)");
			$stmt->bind_param('sssi', $form_short, $form_orig, $form_privacy, $owner);
			$stmt->execute();
			
			$message = "HSDLink created.";
			}*/
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Create HSDLink</title>
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
					
					<h1>Create HSDLink</h1>
					
					<form action="create.php" method="post" role="form">
						<p><?php echo($message); ?></p>
						<input type="text" class="form-control" name="original" placeholder="Original Link" required autofocus><br>
						<input type="text" class="form-control" name="short" placeholder="Shortened Link" required><br>
						<select name="privacy" class="form-control" required>
							<option value="public" selected="selected">Public</option>
							<option value="unlisted">Unlisted</option>
						</select>
						<br>
						<button class="btn btn-primary" type="submit" name="submit">Submit</button>
					</form>
					
				</div>
			</div>
		</div>
		
		<?php include("footer.php"); ?>
	</body>
</html>
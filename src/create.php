<?php
include_once('protected/config.php');
include_once('lib/global.php');

if($logged == 0){
	header("Location: ./");
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
			if(substr($form_orig, 0, 7) === "http://" || substr($form_orig, 0, 8) === "https://"){
				$stmt = $mysqli->prepare("INSERT INTO links (short, url, privacy) VALUES (?, ?, ?)");
				echo($mysqli->error);
				$stmt->bind_param('sss', $form_short, $form_orig, $form_privacy);
				$stmt->execute();
				$stmt->close();
				
				$stmt = $mysqli->prepare("SELECT id FROM links WHERE short = ?");
				echo($mysqli->error);
				$stmt->bind_param('s', $form_short);
				$stmt->execute();
				$stmt->bind_result($link_id);
				$stmt->fetch();
				$stmt->close();
				
				$stmt = $mysqli->prepare("INSERT INTO privileges (user_id, link_id, role) VALUES (?, ?, 'owner')");
				echo($mysqli->error);
				$stmt->bind_param('ii', $owner, $link_id);
				$stmt->execute();
				$stmt->close();
				
				$message = "Link created.";
				header('Refresh: 2; URL= ./');
				
			}else{
				$message = "Origional URL must be HTTP or HTTPS protocol.";
			}
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Create Link</title>
		
		<?php include_once('lib/header.php'); ?>
	</head>
	<body>
		<?php include_once('lib/navbar.php'); ?>
		
		<div class="container">
			<div class="row">
				<div class="col-lg-12" style="padding-top:50px;">
					
					<h1>Create Link</h1>
					
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
		
		<?php include_once('lib/footer.php'); ?>
	</body>
</html>
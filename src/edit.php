<?php
include_once('protected/config.php');
include_once('lib/global.php');

if($logged == 0){
	header("Location: ./");
}

$session_id = $_SESSION['id'];

$stmt = $mysqli->prepare("SELECT role FROM users WHERE id = ? ORDER BY id");
echo($mysqli->error);
$stmt->bind_param('i', $session_id);
$stmt->execute();
$stmt->bind_result($group);
$stmt->fetch();
$stmt->close();

if(isset($_GET['id'])){
	$id = $_GET['id'];
	$stmt = $mysqli->prepare("SELECT short FROM links WHERE id = ?"); // Checking to see if link with ID exists
	echo($mysqli->error);
	$stmt->bind_param('i', $id);
	$stmt->execute();
	if($stmt->fetch()){ // If the link exists
		$stmt->close();
		
		function users(){
			global $mysqli;
			
			$stmt = $mysqli->prepare("SELECT id,username FROM users");
			echo($mysqli->error);
			$stmt->execute();
			$stmt->bind_result($out_id,$out_username);
			$users = array();
			
			while($stmt->fetch()){
				$users[] = array('id' => $out_id, 'username' => $out_username);
			}
			$stmt->close();
			
			return $users;
		}
		
		$stmt = $mysqli->prepare("SELECT user_id FROM privileges WHERE link_id = ? AND role = 'owner'");
		echo($mysqli->error);
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$stmt->bind_result($link_owner);
		$stmt->fetch();
		$stmt->close();
		
		$permission = False;
		if($group == "admin"){
			$permission = True;
		}else{
			$permission = False;
			$stmt = $mysqli->prepare("SELECT role FROM privileges WHERE user_id = ? AND link_id = ?"); // Determine if user should be editing this link
			echo($mysqli->error);
			$stmt->bind_param('ii', $session_id, $id);
			$stmt->execute();
			$stmt->bind_result($role);
			if($stmt->fetch()){
				$stmt->close();
				
				if($role == "owner" || $role == "edit"){
					$permission = True;
				}else{
					die("No permission.");
				}
				
			}else{
				die("No permission.");
			}
		}
		
		if($permission == True){
			$stmt = $mysqli->prepare("SELECT short,url,privacy FROM links WHERE id = ?");
			echo($mysqli->error);
			$stmt->bind_param('i', $id);
			$stmt->execute();
			$stmt->bind_result($short,$url,$privacy);
			$stmt->fetch();
			$stmt->close();
			
			$message = ""; 
			$short_exists = False;
			
			if(isset($_POST['original'])){
				$form_orig = $_POST['original'];
				$form_short = $_POST['short'];
				$form_privacy = $_POST['privacy'];
				$owner = $_POST['owner'];
				
				$stmt = $mysqli->prepare("SELECT short FROM links WHERE id <> ? AND short = ?");
				echo($mysqli->error);
				$stmt->bind_param('is', $id,$form_short);
				$stmt->execute();
				if($stmt->fetch()){
					$short_exists = True;
				}
				$stmt->close();
				
				//error handling
				if( (!$form_orig) || (!$form_short) || (!$form_privacy) || (!$owner) ){
					$message = "Please complete all the fields below!";
				}else{
					if($short_exists == True){
						$message = "That is already a shortened URL!";
					}else{
						if(substr($form_orig, 0, 7) === "http://" || substr($form_orig, 0, 8) === "https://"){
							$stmt = $mysqli->prepare("UPDATE links SET short = ?, url = ?, privacy = ? WHERE id = ?");
							echo($mysqli->error);
							$stmt->bind_param('sssi', $form_short, $form_orig, $form_privacy, $id);
							$stmt->execute();
							$stmt->close();
							
							$stmt = $mysqli->prepare("SELECT id FROM links WHERE short = ?");
							echo($mysqli->error);
							$stmt->bind_param('s', $form_short);
							$stmt->execute();
							$stmt->bind_result($link_id);
							$stmt->fetch();
							$stmt->close();
							
							$stmt = $mysqli->prepare("UPDATE privileges SET user_id = ? WHERE link_id = ? AND role = 'owner'");
							echo($mysqli->error);
							$stmt->bind_param('ii', $owner, $id);
							$stmt->execute();
							$stmt->close();
							
							$message = "Link updated.";
						}else{
							$message = "Original URL must be HTTP or HTTPS protocol.";
						}
					}
				}
			}
		}
	}else{
		header("Location: ./");
	}
}else{
	header("Location: ./");
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Edit Link</title>
		
		<?php include_once('lib/header.php'); ?>
	</head>
	<body>
		<?php include_once('lib/navbar.php'); ?>
		
		<div class="container">
			<div class="row">
				<div class="col-lg-12" style="padding-top:50px;">
					
					<h1>Edit Link</h1>
					<?php echo($message); ?>
					<form action="edit.php?id=<?php echo($id); ?>" method="post" role="form">
						<div class="row">
							<div class="col-lg-12">
								<label for="name">Original Link</label>
								<input type="text" class="form-control" name="original" value="<?php echo($url); ?>" required>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-lg-12">
								<label for="name">Shortened Link</label>
								<input type="text" class="form-control" name="short" value="<?php echo($short); ?>" required>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-sm-6">
								<label for="name">Privacy</label>
								<select name="privacy" class="form-control" required>
									<option value="public" <?php if($privacy == "public"){ ?> selected="selected" <?php } ?>>Public</option>
									<option value="unlisted" <?php if($privacy == "unlisted"){ ?> selected="selected" <?php } ?>>Unlisted</option>
								</select>
							</div>
							<div class="col-sm-6">
								<label for="name">Owner</label>
								<select name="owner" class="form-control" required>
									<?php $users = users();
									foreach($users as $user): ?>
									<option value="<?php echo($user['id']); ?>" <?php if($user['id'] == $link_owner){ ?> selected="selected" <?php } ?>><?php echo($user['username']); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<br>
						<input type="submit" class="btn btn-primary" value="Update Link"/>
					</form>
					
				</div>
			</div>
		</div>
		
		<?php include_once('lib/footer.php'); ?>
	</body>
</html>
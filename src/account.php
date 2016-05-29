<?php
include_once('protected/config.php');
include_once('lib/global.php');

if($logged == 0){
	header("Location: ./");
}

$session_id = $_SESSION['id'];

$stmt = $mysqli->prepare("SELECT username,firstname,lastname,email FROM `users` WHERE `id` = ?");
echo($mysqli->error);
$stmt->bind_param('i', $session_id);
$stmt->execute();
$stmt->bind_result($a_username,$a_fname,$a_lname,$a_email);
$stmt->fetch();
$stmt->close();


$a_message = "";
if(isset($_POST['username'])){

	$username = $_POST['username'];
	$email = $_POST['email'];
	$fname = $_POST['firstname'];
	$lname = $_POST['lastname'];
	
	//error handling
	if( (!$username) || (!$email) || (!$fname) || (!$lname) ){
		$a_message = "Please complete all the fields below!";
	}else{
		//check for duplicates
		$stmt = $mysqli->prepare("SELECT username FROM users WHERE id <> ? AND (username = ? OR email = ?)");
		echo($mysqli->error);
		$stmt->bind_param('iss', $session_id,$username,$email);
		$stmt->execute();
		$stmt->bind_result($user_query);
			
		if($stmt->fetch()){
			if($user_query == $username){
				$a_message = "Your username is already in use.";
			}else{
				$a_message = "Your email is already in use.";
			}
		}else{
			$stmt->close();
			//insert the members
			
			$stmt = $mysqli->prepare("UPDATE users SET username = ?, email = ?, firstname = ?, lastname = ? WHERE id = ?");
			echo($mysqli->error);
			$stmt->bind_param('ssssi', $username, $email, $fname, $lname, $_SESSION["id"]);
			$stmt->execute();
			$stmt->bind_result($query);
			
			$a_message = "Account details updated!";
		}
		$stmt->close();
	}
}

$b_message = "";
if(isset($_POST['pass'])){

	$pass = $_POST['pass'];
	$npass1 = $_POST['npass1'];
	$npass2 = $_POST['npass2'];
	
	$stmt = $mysqli->prepare("SELECT password FROM `users` WHERE `id` = ? LIMIT 1");
	echo($mysqli->error);
	$stmt->bind_param('i', $_SESSION["id"]);
	$stmt->execute();
	$stmt->bind_result($pwhash);
	$stmt->fetch();
	
	//error handling
	if( (!$pass) || (!$npass1) || (!$npass2) ){
		// Checking for completion
		$b_message = "Please complete all the fields below!";
	}elseif($npass1 != $npass2){
		// Making sure they match
		$b_message = "Your new passwords do not match.";
	}elseif($pwhash !== crypt($pass, $pwhash)){
		// Checking if current password is correct
		$b_message = "Your current password is incorrect.";
	}else{
		$stmt->close();
		// Change the password
		$npass1 = crypt($npass1);
		
		$stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
		echo($mysqli->error);
		$stmt->bind_param('si', $npass1, $_SESSION["id"]);
		$stmt->execute();
		
		$b_message = "Password changed!";
	}
	$stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>My <?php echo($app); ?> Account</title>
		
		<?php include_once('lib/header.php'); ?>
	</head>
	<body>
		<?php include_once('lib/navbar.php'); ?>
		
		<div class="container">
			<div class="row">
				<div class="col-lg-12" style="padding-top:50px;">
					<?php if($_GET['view'] == "edit"){ ?>
					<h1>Edit Account</h1>
					<ol class="breadcrumb">
					  <li><a href="account.php">Account</a></li>
					  <li class="active">Edit</li>
					</ol>
					
					<?php echo($a_message); ?>
					<form action="account.php" method="post" role="form">
						
						<div class="row">
							<div class="col-sm-6">
								<label for="name">Username</label>
								<input type="text" class="form-control" name="username" value="<?php echo($a_username); ?>" required><br>
							</div>
							<div class="col-sm-6">
								<label for="name">Email Address</label>
								<input type="text" class="form-control" name="email" value="<?php echo($a_email); ?>" required><br>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-6">
								<label for="name">First Name</label>
								<input type="text" class="form-control" name="firstname" value="<?php echo($a_fname); ?>" required>
							</div>
							<div class="col-sm-6">
								<label for="name">Last Name</label>
								<input type="text" class="form-control" name="lastname" value="<?php echo($a_lname); ?>" required>
							</div>
						</div><br>
						
						<button class="btn btn-primary" style="margin-bottom:20px" type="submit" name="submit">Submit</button>
					</form>
					
					<?php }elseif($_GET['view'] == "pass"){ ?>
					<h1>Change Password</h1>
					<ol class="breadcrumb">
					  <li><a href="account.php">Account</a></li>
					  <li class="active">Password</li>
					</ol>
					
					<?php echo($b_message); ?>
					<form action="account.php" method="post" role="form">
						<input type="password" class="form-control" name="pass" placeholder="Current Password" required><br>
						<input type="password" class="form-control" name="npass1" placeholder="New Password" required><br>
						<input type="password" class="form-control" name="npass2" placeholder="Confirm New Password" required><br>
						
						<button class="btn btn-primary" type="submit" name="changepass">Change Password</button>
					</form>
					<?php }else{ ?>
					
					<h1>Hey there <?php echo($fname); ?>!</h1>
					<a href="account.php?view=edit" class="btn btn-primary" role="button">Edit Account</a> <a href="account.php?view=pass" class="btn btn-primary" role="button">Change Password</a>
					
					<?php } ?>
				</div>
			</div>
		</div>
		
		<?php include_once('lib/footer.php'); ?>
	</body>
</html>
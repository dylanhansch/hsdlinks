<?php
require_once('protected/config.php');
require_once('global.php');

if(isset($_SESSION['id'])){
	$session_id = $_SESSION['id'];
	$stmt = $mysqli->prepare("SELECT role FROM users WHERE id = ?");
	echo($mysqli->error);
	$stmt->bind_param('i', $session_id);
	$stmt->execute();
	$stmt->bind_result($role);
	$stmt->fetch();
	$stmt->close();
}

if($logged == 0){
	header('Location: login.php');
}elseif($role != "admin"){
	die('Admin privileges are required to edit user accounts');
}

if(isset($_GET['edit'])){
	$edit = $_GET['edit'];
}

function users(){
	global $mysqli;
	
	$stmt = $mysqli->prepare("SELECT id,username,firstname,lastname,email,role FROM users ORDER BY id");
	echo($mysqli->error);
	$stmt->execute();
	$stmt->bind_result($out_id, $out_username, $out_fname, $out_lname, $out_email, $out_role);
	$users = array();

	while($stmt->fetch()){
		$users[] = array('id' => $out_id, 'username' => $out_username, 'fname' => $out_fname, 'lname' => $out_lname, 'email' => $out_email, 'role' => $out_role);
	}
	$stmt->close();

	return $users;
}

if(isset($_GET['edit'])){
	$stmt = $mysqli->prepare("SELECT username FROM users WHERE id = ?");
	$stmt->bind_param('i', $_GET['edit']);
	$stmt->execute();
	if($stmt->fetch()){
		$editfetch = True;
	}
	$stmt->close();
}

if(isset($_GET['create'])){
	$title = "Create User";
	$pageheader = '<h1 class="center">Create User</h1>';
	$createuser_message = "";
	$create = $_GET['create'];
	
	if(isset($_POST['username'])){

		$username = $_POST['username'];
		$email = $_POST['email'];
		$fname = $_POST['fname'];
		$lname = $_POST['lname'];
		$pass1 = $_POST['pass1'];
		$pass2 = $_POST['pass2'];
		$role = $_POST['role'];
		
		if( (!$username) || (!$email) || (!$fname) || (!$lname) || (!$pass1) || (!$pass2) || (!$role) ){
			$createuser_message = "Please complete all the fields below.";
		}else{
			if($pass1 != $pass2){
				$createuser_message = "Your passwords do not match.";
			}else{
				//securing the data
				$pass1 = crypt($pass1);
				
				//check for duplicates
				$stmt = $mysqli->prepare("SELECT username FROM `users` WHERE `username` = ? OR `email` = ? LIMIT 1");
				echo($mysqli->error);
				$stmt->bind_param('ss', $username,$email);
				$stmt->execute();
				$stmt->bind_result($user_query);
				if($stmt->fetch()){
					if($user_query == $username){
						$createuser_message = "Your username is already in use.";
					}else{
						$createuser_message = "Your email is already in use.";
					}
				}else{
					$stmt->close();
					//insert the members
					$ip_address = $_SERVER['REMOTE_ADDR'];
					
					$stmt = $mysqli->prepare("INSERT INTO users (username, email, firstname, lastname, password, role) VALUES (?, ?, ?, ?, ?, ?)");
					echo($mysqli->error);
					$stmt->bind_param('ssssss', $username, $email, $fname, $lname, $pass1, $role);
					$stmt->execute();
					
					$createuser_message = "User registered.";
				}
				$stmt->close();
			}
		}
	}
}elseif(isset($_GET['edit']) && isset($_GET['pass']) && $editfetch == True){
	$title = "Update User Password";
	$pageheader = '<h1 class="center">Update Password</h1>';
	$editpassword_message = "";
	
	if(isset($_POST['npass1'])){
		
		$npass1 = $_POST['npass1'];
		$npass2 = $_POST['npass2'];
		
		if( (!$npass1) || (!$npass2) ){
			// Checking for completion
			$editpassword_message = "Please complete all the fields below.";
		}elseif($npass1 != $npass2){
			// Making sure they match
			$editpassword_message = "Your new passwords do not match.";
		}else{
			$stmt->close();
			// Change the password
			$npass1 = crypt($npass1);
			
			$stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
			$stmt->bind_param('si', $npass1, $edit);
			$stmt->execute();
			
			$editpassword_message = "Password changed!";
		}
		$stmt->close();
	}
}elseif(isset($_GET['edit']) && $editfetch == True){
	$title = "Edit User";
	$pageheader = '<h1 class="center">Edit User <a href="'.$basedir.'users.php?edit='.$edit.'&pass" class="btn btn-info btn-sm">Change Password</a></h1>';
	$edituser_message = "";
	
	
	$stmt = $mysqli->prepare("SELECT username,email,firstname,lastname,role FROM users WHERE id = ?");
	echo($mysqli->error);
	$stmt->bind_param('i', $edit);
	$stmt->execute();
	$stmt->bind_result($e_username,$e_email,$e_firstname,$e_lastname,$e_role);
	$stmt->fetch();
	$stmt->close();
	
	if(isset($_POST['username'])){

		$username = $_POST['username'];
		$email = $_POST['email'];
		$fname = $_POST['fname'];
		$lname = $_POST['lname'];
		$role = $_POST['role'];
		
		//error handling
		if( (!$username) || (!$email) || (!$fname) || (!$lname) || (!$role) ){
			$edituser_message = "Please complete all the fields below.";
		}else{
			//check for duplicates
			$stmt = $mysqli->prepare("SELECT username FROM users WHERE id <> ? AND (username = ? OR email = ?)");
			echo($mysqli->error);
			$stmt->bind_param('iss', $edit,$username,$email);
			$stmt->execute();
			$stmt->bind_result($user_query);
				
			if($stmt->fetch()){
				if($user_query == $username){
					$edituser_message = "That username is already in use.";
				}else{
					$edituser_message = "That email is already in use.";
				}
			}else{
				$stmt->close();
				//insert the members
				
				$stmt = $mysqli->prepare("UPDATE users SET username = ?, email = ?, firstname = ?, lastname = ?, role = ? WHERE id = ?");
				echo($mysqli->error);
				$stmt->bind_param('sssssi', $username, $email, $fname, $lname, $role, $edit);
				$stmt->execute();
				
				$edituser_message = "User's account updated.";
			}
			$stmt->close();
		}
	}
}else{
	$title = "User Management";
	$pageheader = '<h1 class="center">Manage Users <a href="?create" class="btn btn-info btn-sm">Create User</a></h1>';
}

// Delete specified link from database
function del_user($user_id){
	global $mysqli, $session_id;
	
	$stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
	echo($mysqli->error);
	$stmt->bind_param("i", $user_id);
	$stmt->execute();
	$stmt->close();
	
	$stmt = $mysqli->prepare("UPDATE privileges SET user_id = ? WHERE user_id = ? AND role = 'owner'");
	echo($mysqli->error);
	$stmt->bind_param('ii', $session_id, $user_id);
	$stmt->execute();
	$stmt->close();
}

if(isset($_GET['del'])){
	del_user($_GET['del']);
	header("Location: users.php");
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?php echo($title); ?> | HSDLinks</title>
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
					<?php
					echo($pageheader);
					
					if(isset($_GET['create'])){ ?>
					
					<ol class="breadcrumb" style="margin-top:10px;">
						<li><a href="<?php echo($basedir); ?>users.php">Users</a></li>
						<li class="active">Create</li>
					</ol>
					
					<div class="well">
						<?php echo($createuser_message); ?>
						<form class="form-signin" action="users.php?create" method="post">
							<div class="row">
								<div class="col-sm-6">
									<label for="name">Username</label>
									<input type="text" class="form-control" name="username" placeholder="Username" required autofocus>
								</div>
								<div class="col-sm-6">
									<label for="name">Email Address</label>
									<input type="text" class="form-control" name="email" placeholder="Email" required>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-sm-6">
									<label for="name">First Name</label>
									<input type="text" class="form-control" name="fname" placeholder="Firstname" required>
								</div>
								<div class="col-sm-6">
									<label for="name">Last Name</label>
									<input type="text" class="form-control" name="lname" placeholder="Lastname" required>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-sm-6">
									<label for="name">Password</label>
									<input type="password" class="form-control" name="pass1" placeholder="Password" required>
								</div>
								<div class="col-sm-6">
									<label for="name">Last Name</label>
									<input type="password" class="form-control" name="pass2" placeholder="Confirm Password" required>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-sm-3">
									<label for="name">Role</label>
									<select name="role" class="form-control" required>
										<option value="user" selected="selected">User</option>
										<option value="admin">Admin</option>
									</select>
								</div>
							</div>
							<br>
							<input type="submit" class="btn btn-warning center" value="Create User"/>
						</form>
					</div>
					
					<?php }elseif(isset($_GET['edit']) && isset($_GET['pass']) && $editfetch == True){ ?>
					
					<ol class="breadcrumb" style="margin-top:10px;">
						<li><a href="<?php echo($basedir); ?>users.php">Users</a></li>
						<li><a href="<?php echo($basedir); ?>users.php?edit=<?php echo($edit); ?>">Edit</a></li>
						<li class="active">Password</li>
					</ol>
					
					<div class="well">
						<?php echo($editpassword_message); ?>
						<form class="form-signin" action="users.php?edit=<?php echo($edit); ?>&pass" method="post">
							<div class="row">
								<div class="col-sm-6">
									<label for="name">New Password</label>
									<input type="password" class="form-control" name="npass1" placeholder="New Password" required autofocus>
								</div>
								<div class="col-sm-6">
									<label for="name">Confirm New Password</label>
									<input type="password" class="form-control" name="npass2" placeholder="Confirm New Password" required>
								</div>
							</div>
							<br>
							<input type="submit" class="btn btn-warning center" value="Update Password"/>
						</form>
					</div>
					
					<?php }elseif(isset($_GET['edit']) && $editfetch == True){ ?>
					
					<ol class="breadcrumb" style="margin-top:10px;">
						<li><a href="<?php echo($basedir); ?>users.php">Users</a></li>
						<li class="active">Edit</li>
					</ol>
					
					<div class="well">
						<?php echo($edituser_message); ?>
						<form class="form-signin" action="users.php?edit=<?php echo($edit); ?>" method="post">
							<div class="row">
								<div class="col-sm-6">
									<label for="name">Username</label>
									<input type="text" class="form-control" name="username" value="<?php echo($e_username); ?>" required>
								</div>
								<div class="col-sm-6">
									<label for="name">Email Address</label>
									<input type="text" class="form-control" name="email" value="<?php echo($e_email); ?>" required>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-sm-6">
									<label for="name">First Name</label>
									<input type="text" class="form-control" name="fname" value="<?php echo($e_firstname); ?>" required>
								</div>
								<div class="col-sm-6">
									<label for="name">Last Name</label>
									<input type="text" class="form-control" name="lname" value="<?php echo($e_lastname); ?>" required>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-sm-3">
									<label for="name">Role</label>
									<select name="role" class="form-control" required>
										<option value="user" <?php if($e_role == "user"){ ?> selected="selected" <?php } ?>>User</option>
										<option value="staff" <?php if($e_role == "staff"){ ?> selected="selected" <?php } ?>>Staff</option>
										<option value="admin" <?php if($e_role == "admin"){ ?> selected="selected" <?php } ?>>Admin</option>
									</select>
								</div>
							</div>
							<br>
							<input type="submit" class="btn btn-warning center" value="Update User"/>
						</form>
					</div>
					
					<?php }else{ ?>
					
					<ol class="breadcrumb" style="margin-top:10px;">
						<li class="active">Users</li>
					</ol>
					
					<div class="well">
						<table class="table table-striped">
							<tr>
								<th>Username</th>
								<th>Name</th>
								<th>Email</th>
								<th>Role</th>
								<th></th>
							</tr>
							<?php $users = users();
							foreach($users as $user): ?>
							<tr>
								<td><?php echo('<a href="'.$basedir.'users.php?edit='.$user["id"].'">'.$user["username"].'</a>'); ?></td>
								<td><?php echo($user['fname'].' '.$user['lname']); ?></td>
								<td><?php echo($user['email']); ?></td>
								<td><?php echo($user['role']); ?></td>
								<td><a href="?del=<?php echo($user["id"]); ?>"><span class="glyphicon glyphicon-remove"></span></a></td>
							</tr>
							<?php endforeach; ?>
						</table>
					</div>
					
					<?php } ?>
				</div>
			</div>
		</div>
		
		<?php include("footer.php"); ?>
	</body>
</html>
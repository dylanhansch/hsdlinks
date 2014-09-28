<?php
include_once("global.php");
include_once("protected/config.php");

$message = "";
if(isset($_POST['username'])){

	$username = $_POST['username'];
	$email = $_POST['email'];
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$pass1 = $_POST['pass1'];
	$pass2 = $_POST['pass2'];
	
	//error handling
	if( (!$username) || (!$email) || (!$fname) || (!$lname) || (!$pass1) || (!$pass2) ){
		$message = "Please complete all the fields below!";
	}else{
		if($pass1 != $pass2){
			$message = "Your passwords do not match!";
		}else{
			//securing the data
			$pass1 = crypt($pass1);
			
			$role = ("user");
			
			//check for duplicates
			$stmt = $mysqli->prepare("SELECT username FROM `users` WHERE `username` = ? OR `email` = ? LIMIT 1");
			$stmt->bind_param('ss', $username,$email);
			$stmt->execute();
			$stmt->bind_result($user_query);
			
			if($stmt->fetch()){
				if($user_query == $username){
					$message = "Your username is already in use.";
				}else{
					$message = "Your email is already in use.";
				}
			}else{
				$stmt->close();
				//insert the members
				$ip_address = $_SERVER['REMOTE_ADDR'];
				
				$stmt = $mysqli->prepare("INSERT INTO users (username, email, firstname, lastname, password, sign_up_date, ip_address, role) VALUES (? , ?, ?, ?, ?, now(), ?, ?)");
				$stmt->bind_param('sssssss', $username, $email, $fname, $lname, $pass1, $ip_address, $role);
				$stmt->execute();
				$stmt->bind_result($query);
				
				$message = "You have now been registered!";
			}
			$stmt->close();
		}
	}
	
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Register at HSDLinks</title>
		<meta charset="utf-8">
		<meta name="author" content="Dylan Hansch">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<link rel="shortcut icon" content="none">
		
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
		
		<style>
			body {
			  padding-top: 40px;
			  padding-bottom: 40px;
			  background-color: #eee;
			}

			.form-signin {
			  max-width: 330px;
			  padding: 15px;
			  margin: 0 auto;
			}
			.form-signin .form-signin-heading,
			.form-signin .checkbox {
			  margin-bottom: 10px;
			}
			.form-signin .checkbox {
			  font-weight: normal;
			}
			.form-signin .form-control {
			  position: relative;
			  height: auto;
			  -webkit-box-sizing: border-box;
				 -moz-box-sizing: border-box;
					  box-sizing: border-box;
			  padding: 10px;
			  font-size: 16px;
			}
			.form-signin .form-control:focus {
			  z-index: 2;
			}
			.form-signin input[type="email"] {
			  margin-bottom: -1px;
			  border-bottom-right-radius: 0;
			  border-bottom-left-radius: 0;
			}
			.form-signin input[type="password"] {
			  margin-bottom: 10px;
			  border-top-left-radius: 0;
			  border-top-right-radius: 0;
			}
		</style>
	</head>
	<body>
		<?php include("navbar.php"); ?>
		
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					
					<form action="register.php" method="post" class="form-signin" role="form">
						<h2 class="form-signin-heading">Register</h2>
						<p><?php echo($message); ?></p>
						<input type="text" class="form-control" name="username" placeholder="Username" required autofocus>
							<input type="text" class="form-control" name="email" placeholder="Email Address" required>
							<input type="text" class="form-control" name="fname" placeholder="First Name" required>
							<input type="text" class="form-control" name="lname" placeholder="Last Name" required>
							<input type="password" class="form-control" name="pass1" placeholder="Password" required>
							<input type="password" class="form-control" name="pass2" placeholder="Confirm Password" required>
						<button class="btn btn-lg btn-primary btn-block" type="submit" name="Register">Register</button>
					</form>
					
				</div>
			</div>
		</div>
		
		<hr>
		<?php include("footer.php"); ?>
	</body>
</html>
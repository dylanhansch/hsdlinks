<?php
include_once('protected/config.php');
include_once('global.php');

if(isset($_SESSION['id'])){
	$stmt = $mysqli->prepare("SELECT firstname,role FROM `users` WHERE `id` = ?");
	echo($mysqli->error);
	$stmt->bind_param('i', $session_id);
	$stmt->execute();
	$stmt->bind_result($fname,$role);
	$stmt->fetch();
	$stmt->close();
}
?>
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href=""><?php echo($app); ?></a>
				</div>
				<div class="collapse navbar-collapse navbar-ex1-collapse">
					<ul class="nav navbar-nav pull-left">
						<li><a href="about.php">About</a></li>
						<?php if($logged == 1){ ?>
						<li><a href="create.php">Create</a></li>
						<?php } ?>
						<?php if($role == "admin"){ ?>
						<li><a href="users.php">Users</a></li>
						<?php } ?>
					</ul>
					<ul class="nav navbar-nav pull-right">
						<?php if($logged == 0){ ?>
						<li><a href="login.php">Login</a></li>
						<li><a href="register.php">Register</a></li>
						<?php }else{ ?>
						<li class="dropdown">
							<a href="" class="dropdown-toggle" data-toggle="dropdown">Hello, <?php print($fname); ?> <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="account.php">Account</a></li>
								<li class="divider"></li>
								<li><a href="logout.php">Logout</a></li>
							</ul>
						</li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</nav>

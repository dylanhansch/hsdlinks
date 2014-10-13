<?php
include_once("../global.php");
include_once("../protected/config.php");

$session_id = $_SESSION['id'];

$stmt = $mysqli->prepare("SELECT firstname,role FROM `users` WHERE `id` = ?");
$stmt->bind_param('i', $session_id);
$stmt->execute();
$stmt->bind_result($fname,$role);
$stmt->fetch();
$stmt->close();

if($logged == 0){
	header("Location: " . $basedir . "login.php");
}
if($role != admin){
	echo("You do not have permission to view this directory!");
	echo("<br>");
	echo("For administrative access, please talk to Dylan Hansch.");
	exit();
}

function links(){
	global $mysqli;
	
	$stmt = $mysqli->prepare("SELECT links.id, links.short, links.url, links.privacy, users.username FROM links INNER JOIN users ON links.owner = users.id");
	$stmt->execute();
	$stmt->bind_result($out_id,$out_short,$out_url,$out_privacy,$out_owner);
	$links = array();
	
	while($stmt->fetch()){
		$links[] = array('id' => $out_id, 'short' => $out_short, 'url' => $out_url, 'privacy' => $out_privacy, 'owner' => $out_owner);
	}
	$stmt->close();
	
	return $links;
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>HSDLinks Admin</title>
		<meta charset="utf-8">
		<meta name="author" content="Dylan Hansch">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<link rel="shortcut icon" content="none">
		
		<link rel="stylesheet" type="text/css" href="../style.css">
		<link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css">
	</head>
	<body>
		<?php include("../navbar.php"); ?>
		
		<div class="container">
			<div class="row">
				<div class="col-lg-12" style="padding-top:50px;">
					<h1>HSDLinks Admin Dashboard</h1>
					
					<table class="table table-striped">
						<tr>
							<th>Shortened URL</th>
							<th>Full URL</th>
							<th>Privacy</th>
							<th>User</th>
						</tr>
						<?php $links = links();
						foreach($links as $link): ?>
						<tr>
							<td><?php echo('<a href="'.$link["url"].'">'.$link["short"].'</a>'); ?></td>
							<td><?php echo($link["url"]); ?></td>
							<td><?php echo($link["privacy"]); ?></td>
							<td><?php echo($link["owner"]); ?></td>
						</tr>
						<?php endforeach; ?>
					</table>
					
				</div>
			</div>
		</div>
		
		<?php include("../footer.php"); ?>
	</body>
</html>
<?php
require_once("global.php");
require_once("protected/config.php");

$id = $_GET['id'];

$stmt = $mysqli->prepare("SELECT `url` FROM `links` WHERE `short` = ?");
echo($mysqli->error);
$stmt->bind_param('s', $id);
$stmt->execute();
$stmt->bind_result($url);

if($stmt->fetch()){
    header("Location: ". $url);
	exit;
}else{
	header('404 Not Found');
}

$stmt->free_result();
$stmt->close();

if(isset($_GET["del"])){
	del_link($_GET["del"]);
	header("Location: " . $basedir);
}

if(isset($session_id)){
	$stmt = $mysqli->prepare("SELECT role FROM users WHERE id = ?");
	echo($mysqli->error);
	$stmt->bind_param('i', $session_id);
	$stmt->execute();
	$stmt->bind_result($role);
	$stmt->fetch();
	$stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?php echo($app); ?></title>
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
					<h1><?php echo($app); ?></h1>
					
					<table class="table table-striped">
						<tr>
							<th>Shortened URL</th>
							<th>Full URL</th>
							<?php if($logged != 0){
							echo("<th>Privacy</th>");
							echo("<th>Owner</th>");
							echo("<th></th>"); } ?>
						</tr>
						<?php $links = links();
						foreach($links as $link):
						
						if(isset($session_id) && $role == "admin"){
							$perm = "admin";
						}elseif(isset($session_id)){
							$stmt = $mysqli->prepare("SELECT role FROM privileges WHERE user_id = ? AND link_id = ?");
							echo($mysqli->error);
							$stmt->bind_param('ii', $session_id, $link['id']);
							$stmt->execute();
							$stmt->bind_result($perm);
							if(!($stmt->fetch())){
								$perm = "view";
							}
							$stmt->close();
						}
						?>
						<tr>
							<td><?php echo('<a href="'.$link["url"].'">'.$link["short"].'</a>'); ?></td>
							<td><?php echo($link["url"]); ?></td>
							<?php if($logged != 0){
							echo("<td>" . $link["privacy"] . "</td>");
							echo("<td>" . $link["owner"] . "</td>");
								if($perm != "view"){
									echo('<td><a href="edit.php?id='.$link["id"].'"><span class="glyphicon glyphicon-pencil"></a></span></a> <a href="?del='.$link["id"].'" onclick="return confirmation()"><span class="glyphicon glyphicon-remove"></span></a></td>');
								}else{
									echo('<td></td>');
								}
							} ?>
						</tr>
						<?php endforeach; ?>
					</table>
					
				</div>
			</div>
		</div>
		
		<script type="text/javascript">
		function confirmation() {
			var r = confirm("WARNING!\nThis action is perminate and non reversable. Are you sure you want to continue?");
			if (r == true) {
				return true;
			} else {
				return false;
			}
		}
		</script>
		
		<?php include("footer.php"); ?>
	</body>
</html>

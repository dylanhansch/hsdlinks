<?php
include_once('protected/config.php');
include_once('lib/global.php');

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
	header("Location: ./");
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
		
		<?php include_once('lib/header.php'); ?>
	</head>
	<body>
		<?php include_once('lib/navbar.php'); ?>
		
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
		
		<?php include_once('lib/footer.php'); ?>
	</body>
</html>

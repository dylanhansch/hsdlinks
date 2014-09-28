<?php
include_once("global.php");
include_once("protected/config.php");

$id = $_GET['id'];

$stmt = $mysqli->prepare("SELECT `url` FROM `links` WHERE `short` = ?");
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

function links(){
	global $mysqli;
	
	$stmt = $mysqli->prepare("SELECT id,short,url FROM `links` WHERE `privacy` = 'public'");
	$stmt->execute();
	$stmt->bind_result($out_id,$out_short,$out_url);
	$links = array();
	
	while($stmt->fetch()){
		$links[] = array('id' => $out_id, 'short' => $out_short, 'url' => $out_url);
	}
	$stmt->close();
	
	return $links;
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>HSD Links</title>
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
					<h1>HSDLinks <small>Alpha</small></h1>
					
					<table class="table table-striped">
						<tr>
							<th>Shortened URL</th>
							<th>Full URL</th>
						</tr>
						<?php $links = links();
						foreach($links as $link): ?>
						<tr>
							<td><?php echo('<a href="'.$link["url"].'">'.$link["short"].'</a>'); ?></td>
							<td><?php echo($link["url"]); ?></td>
						</tr>
						<?php endforeach; ?>
					</table>
					
				</div>
			</div>
		</div>
		
		<?php include("footer.php"); ?>
	</body>
</html>
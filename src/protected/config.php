<?php
// URL of HSDLinks installation
$baseurl = "http://dev.dylanhansch.net/hsdlinks/";
$app = "HSDLinks";

// Connecting to the database
$host = "localhost";
$user = "demo";
$pass = "";
$database = "hsdlinksdev";

$mysqli = new mysqli($host, $user, $pass, $database);
if ($mysqli->connect_errno) {
    die('Failed to connect to MySQL: (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

<?php
// Web directory to HSDLinks installation, with beginning and trailing slash.
$basedir = "/hsdlinks/";

// Connecting to the database
$host = "localhost";
$user = "demo";
$pass = "";
$database = "hsdlinks";

$mysqli = new mysqli($host, $user, $pass, $database);
if ($mysqli->connect_errno) {
    die('Failed to connect to MySQL: (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

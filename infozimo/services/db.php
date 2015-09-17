<?php
function getDB() {
	$dbhost="127.0.0.1";
	$dbuser="root";
	$dbpass="rajesh";
	$dbname="infozimo_db";
	$dbConnection = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);	
	$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbConnection;
}
?>

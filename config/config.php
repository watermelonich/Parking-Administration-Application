<?php
// Define the database connection settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'parking_demo');
define('DB_USER', 'root');
define('DB_PASS', '');

// Connect to the database

try {
	$connect = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);

	$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	session_start();

	if(isset($_SESSION['timezone']))
	{
		date_default_timezone_set($_SESSION['timezone']);
	}
	else
	{
		date_default_timezone_set("Asia/Calcutta");
	}

	if(isset($_SESSION['currency']))
	{
		define('CURRENCY', $_SESSION['currency']);
	}
	else
	{
		define('CURRENCY', '&#8377;');
	}

	if(isset($_SESSION['date_time_format']))
	{
		define('DT_FORMATE', $_SESSION['date_time_format']);
	}
	else
	{
		define('DT_FORMATE', 'Y-m-d H:i:s');
	}
}
catch (PDOException $e) 
{
	echo $e->getMessage();

	exit();
}

?>

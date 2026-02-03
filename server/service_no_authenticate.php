<?php 
if (session_status() == PHP_SESSION_NONE)	{
	session_start();
}
$config="../config.php";
include($config);
require_once("../common/validation.php");
require_once("../class/system.php");
require_once("authorization.php");
require_once("accounting.php");
$timezone = "Africa/Dar_es_Salaam";
try {
	$conn = mysql_connect($hostname, $user, $pass) or Object::shootException("Could not connect to a database services");
	$profile1 = new Profile($database, Profile::getProfileReference($database, $conn), $conn);
	if (! is_null($profile1->getPHPTimezone())) $timezone = $profile1->getPHPTimezone()->getZoneName();
	date_default_timezone_set($timezone);
	mysql_close($conn);
} catch (Exception $e)	{
	$message = $e->getMessage();
	die(json_encode(array("code"=>"1","message"=>$message)));
}
$date=date("Y:m:d:H:i:s");
$systemDate1 = new DateAndTime("Ndimangwa", $date, "Fadhili");
if (isset($_SESSION['login'][0]['id'])) json_encode(array("code"=>"1","message"=>"You were Already Logged Out"));
//Clear Session 
$_SESSION = array();
session_destroy();
//Now We are okay 
echo json_encode(array("code"=>"0", "message"=>"Logged Out Successful"));
?>

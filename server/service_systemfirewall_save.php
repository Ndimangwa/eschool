<?php 
if (session_status() == PHP_SESSION_NONE)	{
	session_start();
}
if (! isset($_SESSION['login'][0]['id']))	{
	die(json_encode(array("code"=>"1","message"=>"You are not Logged In to the System")));
}
require_once("../class/system.php");
require_once("authorization.php");
require_once("accounting.php");
$config="../config.php";
include($config);
$conn=mysql_connect($hostname, $user, $pass) or die(json_encode(array("code"=>"1","message"=>"Could not connect to a database service")));
$profile1 = null;
try {
	$__profileId = Profile::getProfileReference($database, $conn);
	$profile1 = new Profile($database, $__profileId, $conn);
} catch (Exception $e)	{
	$dmessage=$e->getMessage();
	die(json_encode(array("code"=>"1","message"=>"$dmessage")));
}
$timezone="Africa/Dar_es_Salaam";
if (! is_null($profile1->getPHPTimezone())) $timezone = $profile1->getPHPTimezone()->getZoneName();
date_default_timezone_set($timezone);
$date=date("Y:m:d:H:i:s");
$date1 = new DateAndTime("Ndimangwa", $date, "Fadhili");
if (! isset($_SESSION['login'][0]['id'])) die(json_encode(array("code"=>"1","message"=>"Session is not set")));
if (! (isset($_POST['objecttype']) && isset($_POST['objectid']) && isset($_POST['securecode']) && isset($_POST['contextstring']))) die(json_encode(array("code"=>"1","message"=>"Parameters were not set propely")));
$type=$_POST['objecttype'];
$contextstring=$_POST['contextstring'];
$securecode=$_POST['securecode'];
$object1 = null;
if ($type=="login")	{
	try	{
		$object1 = new Login($database, $_POST['objectid'], $conn);
	} catch (Exception $e)	{
		$dmessage=$e->getMessage();
		die(json_encode(array("code"=>"1","message"=>$dmessage)));
	}
} else if ($type=="group")	{
	try	{
		$object1 = new Group($database, $_POST['objectid'], $conn);
	} catch (Exception $e)	{
		$dmessage=$e->getMessage();
		die(json_encode(array("code"=>"1","message"=>$dmessage)));
	}
} else if ($type=="jobtitle")	{
	try	{
		$object1 = new JobTitle($database, $_POST['objectid'], $conn);
	} catch (Exception $e)	{
		$dmessage=$e->getMessage();
		die(json_encode(array("code"=>"1","message"=>$dmessage)));
	}
} else {
	die(json_encode(array("code"=>"1","message"=>"Type of Object were not recognized")));
}
//Check that this was a genuine submission 
if ($securecode != $object1->getExtraFilter())	die(json_encode(array("code"=>"1","message"=>"The System has detected you are replaying data in your browser window")));
try {
	$object1->setContext($contextstring);
	$object1->setExtraFilter(System::getCodeString(8));
	$object1->commitUpdate();
} catch (Exception $e)	{
	die(json_encode(array("code"=>"1","message"=>"Error in Saving Firewall Rules")));
}
mysql_close($conn);
echo json_encode(array("code"=>"0","message"=>"Firewall Settings Saved"));
?>
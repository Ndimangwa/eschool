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
$conn = mysql_connect($hostname, $user, $pass) or die(json_encode(array("code"=>"1", "message"=>"Could not establish connection with a database service")));
$profile1 = null;
try {
	$__profileId = Profile::getProfileReference($database, $conn);
	$profile1 = new Profile($database, $__profileId, $conn);
	if (! System::isSystemSSLTLSCertificateVerificationSuccessful($profile1)) Object::shootException("Protocol Denied");
} catch (Exception $e)	{
	$message = $e->getMessage();
	mysql_close($conn);
	die(json_encode(array("code"=>"1","message"=>$message)));
}
date_default_timezone_set("Africa/Dar_es_Salaam");
$date=date("Y:m:d:H:i:s");
$systemDate1 = new DateAndTime("Ndimangwa", $date, "Fadhili");
if (! (isset($_POST['param1']) && isset($_POST['param2']))) die(json_encode(array("code"=>"1", "message"=>"Could not log In some Parameters are missing")));
$username = mysql_real_escape_string($_POST['param1']);
$password = sha1($_POST['param2']);
$login1 = null;
try {
	$login1 = Login::logInThisUser($database, $conn, $username, $password);
	if (is_null($login1)) Object::shootException("Could Not Be Logged In");
} catch (Exception $e)	{
	mysql_close($conn);
	$message = $e->getMessage();
	die(json_encode(array("code"=>"1", "message"=>"$message")));
}
mysql_close($conn);
//Now I have the Live login1
//Just to satisfy the Authorize class
$_SESSION['login'] = array();
$_SESSION['login'][0] = array();
$_SESSION['login'][0]['id'] = $login1->getLoginId();
if (! Authorize::isAllowable($config, "managelogin", "normal", "do_not_setlog", "-1", "-1"))	{
	//Not allowed at all 
	$_SESSION = array();
	session_destroy();
	die(json_encode(array("code"=>"1", "message"=>"Perhaps you have reached the firewall, kindly check with your Administrator")));
}
//Now We are okay 
echo json_encode(array("code"=>"0", "message"=>"Logged Successful"));
?>
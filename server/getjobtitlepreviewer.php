<?php 
/*
INPUT param1, param2
OUTPUT: code, message, rows {i{tr{j{td}}}}
*/
if (session_status() == PHP_SESSION_NONE)	{
	session_start();
}
if (! isset($_SESSION['login'][0]['id']))	{
	die(json_encode(array("code"=>"1","message"=>"You are not Logged In to the System")));
}
require_once("../class/system.php");
$config="../config.php";
include($config);
$conn = mysql_connect($hostname, $user, $pass) or die(json_encode(array("code"=>"1","message"=>"Could not connect to a database services")));
$profile1 = null;
$referencePolicy1 = null;
try {
	$__profileId = Profile::getProfileReference($database, $conn);
	$profile1 = new Profile($database, $__profileId, $conn);
	$referencePolicy1 = new MessageAccessList($database, "../data/message/policy.csv", $conn);
} catch(Exception $e)	{
	$message = $e->getMessage();
	mysql_close($conn);
	die(json_encode(array("code"=>"1","message"=>"$message")));
}
if (! (isset($_POST['param1']) && isset($_POST['param2']))) die(json_encode(array("code"=>"1","message"=>"Some parameters were not set properly")));
$prefix = $_POST['param1'];
$messageType = $_POST['param2'];
$resultArray = array();
$resultArray['code'] = "0";
$resultArray['message'] = "Server-Successful";
$resultArray['prefix'] = $prefix;
$resultArray['recordsLimitPerPage'] = $profile1->getMaximumNumberOfDisplayedRowsPerPage();
$resultArray['rows'] = array();
$counter = 0;
//Headers
$resultArray['rows'][$counter] = array();
$resultArray['rows'][$counter]['tr'] = array();
	
$resultArray['rows'][$counter]['tr'][0] = array();
$resultArray['rows'][$counter]['tr'][0]['td'] = "Job Title";
$counter++;
$query = "SELECT jobId FROM jobTitle";
$result = mysql_db_query($database, $query, $conn) or die(json_encode(array("code"=>"1","message"=>"There were problems in loading and executing query")));
while (list($id)=mysql_fetch_row($result))	{
	$policy1 = null;
	$isPolicyAccepted = false;
	try {
		$policy1 = $referencePolicy1->cloneMe();
		//Load the applicable rules for the logged In person He/She is the source
		$policy1 = $policy1->loadMyRules($_SESSION['login'][0]['id'], $messageType, MessageAccessList::$__MESSAGE_ACCESS_LOGIN); 
		//Check Policy Acceptance 
		$isPolicyAccepted = $policy1->isAccepted(MessageAccessList::$__MESSAGE_ACCESS_JOBTITLE, $id);
	} catch (Exception $e)	{
		$message = $e->getMessage();
		mysql_close($conn);
		die(json_encode(array("code"=>"1","message"=>"$message")));
	}
	if ($isPolicyAccepted)	{ 
		$job1 = null;
		try {
			$job1 = new JobTitle($database, $id, $conn);
		} catch (Exception $e)	{
			$message = $e->getMessage();
			die(json_encode(array("code"=>"1","message"=>"Object Creation Failed $message")));
		}
		$resultArray['rows'][$counter] = array();
		$resultArray['rows'][$counter]['id'] = $job1->getJobId();
		$resultArray['rows'][$counter]['tr'] = array();
	
		$resultArray['rows'][$counter]['tr'][0] = array();
		$resultArray['rows'][$counter]['tr'][0]['td'] = $job1->getJobName();

		$counter++;
	}
}
mysql_close($conn);
echo json_encode($resultArray);
?>
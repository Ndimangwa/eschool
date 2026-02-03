<?php 
/*
INPUT: param1 [dataId]

OUTPUT: json code, message 
*/
if (session_status() == PHP_SESSION_NONE)	{
	session_start();
}
if (! isset($_SESSION['login'][0]['id']))	{
	die(json_encode(array("code"=>"1","message"=>"You are not Logged In to the System")));
}
require_once("../class/system.php");
require_once("../server/authorization.php");
require_once("../server/accounting.php");
$config="../config.php";
include($config);
if (! (isset($_POST['param1']) && isset($_POST['param2']))) die(json_encode(array("code"=>"1","message"=>"Some parameters are missing")));
$conn = mysql_connect($hostname, $user, $pass) or die(json_encode(array("code"=>"1","message"=>"Could not connect to a database services")));
$date = $_POST['param2'];
$defaultApproveText = "";
$requestedFullname = "";
try {
	$login1 = new Login($database, $_SESSION['login'][0]['id'], $conn);
	if (is_null($login1->getJobTitle())) die(json_encode(array("code"=>"1","message"=>"You are not registered to any Job Title")));
	$data1 = new ApprovalSequenceData($database, $_POST['param1'], $conn);
	if (is_null($data1->getSchema())) die(json_encode(array("code"=>"1","message"=>"There is not schema registered with this operation")));
	//Check if Am allowed 
	if (! $data1->getSchema()->isJobInApprovingJobList($login1->getJobTitle()->getJobId())) die(json_encode(array("code"=>"1","message"=>"You are not in the Approving List")));
	//Add me on already Approved List 
	$listToUpdate = array(); //Array for Algorithm Compliance
	$listToUpdate[0] = $_SESSION['login'][0]['id'];
	$listToUpdate = Object::attachIdListToObjectList($data1->getAlreadyApprovedList(), $listToUpdate);
	$data1->setAlreadyApprovedList($listToUpdate);
	//get The next person supposed to Approve 
	$nextApprovingJobId = $data1->getSchema()->getTheNextApprovingJob($login1->getJobTitle()->getJobId()); //Who will be the next one to sign after I sign 
	if (! is_null($nextApprovingJobId)) $data1->setNextJobToApprove($nextApprovingJobId);
	$data1->commitUpdate();
	$defaultApproveText = "Approved";
	if (! is_null($data1->getRequestedBy()))	{
		$requestedFullname = $data1->getRequestedBy()->getFullname();
		$requestedFullname = " : ".$requestedFullname;
	}
} catch (Exception $e)	{
	$message = $e->getMessage();
	die(json_encode(array("code"=>"1","message"=>$message)));
}
mysql_close($conn);
//Add Log
$approvalLog = $defaultApproveText.$requestedFullname;
Accounting::addLog($config, $date, $login1->getLoginName(), "manageapprovalsequencedata_edit", $approvalLog);
echo json_encode(array("code"=>"0","message"=>"You have successful approved the action for the user"));
?>
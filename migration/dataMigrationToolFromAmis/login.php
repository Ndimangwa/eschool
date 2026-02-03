<?php 
include("../../config.php");
require_once("../../class/system.php");
$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
$oldDatabase="ischool1";
/*Begin Data Here*/
//Remove ID one since it might have data 
$query = "DELETE FROM Login WHERE loginId='1'";
$oldResults = mysql_db_query($oldDatabase, $query, $conn) or die("\nCould not remove an existing 1st Record\n");
//From the target table 
$query = "DELETE FROM login WHERE loginId <> '1'";
$result = mysql_db_query($database, $query, $conn) or die("\nCould not remove an existing Non 1st Record\n");
//
$query="SELECT loginId, loginName, sha1Password, securityqn1, securityans1, securityqn2, securityans2, isAuthenticated, admittime, root FROM Login";
$oldResults = mysql_db_query($oldDatabase, $query, $conn) or die("\nCould Not fetch Login Details\n");
$loginPrefix = "login";
$counter=0;
while (list($loginId, $loginName, $sha1Password, $securityqn1, $securityans1, $securityqn2, $securityans2, $isAuthenticated, $admittime, $root)=mysql_fetch_row($oldResults))	{
	$counter++;
	$flags = 15;
	if ($isAuthenticated == 0) continue; //No Need of adding unathenticated user
	if ($isAuthenticated == 1) $flags = 31;
	if (is_null($loginName) || $loginName == "") $loginName = $loginPrefix.$counter;
	$context = Object::$defaultContextValue;
	$query = "";
	$securityans1=mysql_real_escape_string($securityans1);
	$securityans2 = mysql_real_escape_string($securityans2);
	if (! is_null($securityqn1) && ! is_null($securityqn2))	{
		$query = "INSERT INTO login (loginId, loginName, password, qnSecurity1, ansSecurity1, qnSecurity2, ansSecurity2, flags, admissionTime, root, statusId, context) VALUES('$loginId', '$loginName', '$sha1Password', '$securityqn1', '$securityans1', '$securityqn2', '$securityans2', '$flags', '$admittime', '$root', '1', '$context')";
	} else {
		$query = "INSERT INTO login (loginId, loginName, password, ansSecurity1, ansSecurity2, flags, admissionTime, root, statusId, context) VALUES('$loginId', '$loginName', '$sha1Password', '$securityans1', '$securityans2', '$flags', '$admittime', '$root', '1', '$context')";
	}
	$result = mysql_db_query($database, $query, $conn) or die("\nCould not insert record \n".mysql_error());
}
/*End Data Here*/
mysql_close($conn);
echo "\nSuccessful\n";
?>
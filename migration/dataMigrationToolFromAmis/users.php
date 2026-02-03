<?php 
include("../../config.php");
require_once("../../class/system.php");
$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
$oldDatabase="ischool1";
/*Begin Date Here*/
$query="SELECT userId, loginId, fullName, userDetails, dob, sexId, maritalId FROM users";
$oldResults = mysql_db_query($oldDatabase, $query, $conn) or die("\nCould not pull old users data \n".mysql_error());
//Clear the table
$query ="DELETE FROM users";
$result = mysql_db_query($database, $query, $conn) or die("Could not wipe data out of users store");
$counter=0;
while (list($userId, $loginId, $fullName, $userDetails, $dob, $sexId, $maritalId)=mysql_fetch_row($oldResults))	{
	$counter++;
	$query="SELECT isAuthenticated FROM Login, users WHERE users.loginId=Login.loginId AND Login.loginId='$loginId'";
	$result = mysql_db_query($oldDatabase, $query, $conn) or die("Could not get Authenticated Information");
	$dob = DateAndTime::convertFromGUIDateFormatToSystemDateAndTimeFormat($dob);
	$email = "e".System::convertIntegerToStringOfAGivenLength($counter, 4)."@kcmuco.ac.tz";
	$phone = "1".System::convertIntegerToStringOfAGivenLength($counter, 9);
	//User Table
	$query="INSERT INTO users (userId, loginId, extraInformation) VALUES('$userId', '$loginId', '$userDetails')";
	$result = mysql_db_query($database, $query, $conn) or die("\nCould not Insert in a User Table\n");
	//Login Table 
	$query = "UPDATE login SET groupId='1', jobId='2', fullname='$fullName', firstname='Firstname', middlename='Middlename', lastname='Lastname', dob='$dob', sexId='$sexId', maritalId='$maritalId', typeId='1', email='$email', phone='$phone', photo='default.png' WHERE loginId='$loginId'";
	$result = mysql_db_query($database, $query, $conn) or die("\nCould not Update Login Table\n");
}
/*End Date Here*/

mysql_close($conn);
echo "\nSuccessful\n";
?>
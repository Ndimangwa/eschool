<?php 
include("../../config.php");
require_once("../../class/system.php");
$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
$oldDatabase="ischool1";

/*Begin Data Here*/
//We need to remove odds some emails and phone numbers are not unique
$query="SELECT studentId, registrationNo, loginId, nameincertificate, firstname, middlename, lastname, dob, sexId, maritalId, citizenshipId, nativeplace, isdisability, disability, denominationId, email, mobile, postaladdress, phyaddress, isoccupation, occupation, regtime, photo, courseId, isemployed, completed, iscontinuing, extras, empname, empemail, emptelephone, empmobile, emppostaladdress, empphyaddress, nxtkinname, nxtkinrelationship, nxtkinemail, nxtkintelephone, nxtkinmobile, nxtkinpostaladdress, nxtkinphyaddress, sponsorname, sponsoremail, sponsortelephone, sponsormobile, sponsorpostaladdress, sponsorphyaddress, sponsorfax, formfourindex, formfourinstitution, isformsix, formsixindex, formsixinstitution, formsixspecialization, isformsixeq, formsixeqindex, formsixeqinstitution, formsixeqspecialization, ismaster, masterindex, masterinstitution, masterspecialization, isdegree, degreeindex, degreeinstitution, degreespecialization 	FROM Student";
$oldResults = mysql_db_query($oldDatabase, $query, $conn) or die("Could not load Student data from older table ".mysql_error());
//Clear the table 
$query="DELETE FROM students";
$result = mysql_db_query($database, $query, $conn) or die("Could not wipe data out of students store");
$counter=0;
while (list($studentId, $registrationNumber, $loginId, $nameincertificate, $firstname, $middlename, $lastname, $dob, $sexId, $maritalId, $citizenshipId, $nativeplace, $isdisability, $disability, $denominationId, $email, $mobile, $postaladdress, $phyaddress, $isoccupation, $occupation, $regtime, $photo, $courseId, $isemployed, $completed, $iscontinuing, $extras, $empname, $empemail, $emptelephone, $empmobile, $emppostaladdress, $empphyaddress, $nxtkinname, $nxtkinrelationship, $nxtkinemail, $nxtkintelephone, $nxtkinmobile, $nxtkinpostaladdress, $nxtkinphyaddress, $sponsorname, $sponsoremail, $sponsortelephone, $sponsormobile, $sponsorpostaladdress, $sponsorphyaddress, $sponsorfax, $formfourindex, $formfourinstitution, $isformsix, $formsixindex, $formsixinstitution, $formsixspecialization, $isformsixeq, $formsixeqindex, $formsixeqinstitution, $formsixeqspecialization, $ismaster, $masterindex, $masterinstitution, $masterspecialization, $isdegree, $degreeindex, $degreeinstitution, $degreespecialization)=mysql_fetch_row($oldResults))	{
	$counter++; 
	$query="SELECT isAuthenticated FROM Login, Student WHERE Student.loginId=Login.loginId AND Login.loginId='$loginId'";
	$result = mysql_db_query($oldDatabase, $query, $conn) or die("Could not get Authenticated Information");
	list($isAuthenticated)=mysql_fetch_row($result);
	if ($isAuthenticated == 0) continue; //We do not need unadmitted students
	if ($isdisability == 0) $disability = null; 
	if ($isoccupation == 0) $occupation = null;
	$flags = 14;
	if ($completed == 1) $flags = $flags + 1;
	if ($iscontinuing == 1) $flags = $flags + 16;
	$xmlFile = "student_".$studentId.".xml";
	//Calculating batch 
	$batchId = 1;
	if (! is_null($registrationNumber))	{
		$tdataArr = explode("/", $registrationNumber);
		$rYear=$tdataArr[3];
		if (is_numeric($rYear))	{
			$lYear = intval($rYear) - 1;
			$aYear = $lYear."/".$rYear;
			try {
				$batchId = AccademicYear::getBatchIdFromName($database, $conn, $aYear);
			} catch (Exception $e)	{
				$batchId = 1;
			}
		} 
		echo "!";
	} else {
		$registrationNumber="RegNumber".$counter;
		echo ".";
	}
	//Correction is needed
	if ($denominationId > 204)	{
		$denominationId = $denominationId - 2;
	} else if ($denominationId > 183)	{
		$denominationId = $denominationId - 1;
	}
	$phyaddress = mysql_real_escape_string($phyaddress);
	$postaladdress = mysql_real_escape_string($postaladdress);
	$occupation = mysql_real_escape_string($occupation);
	$extras = mysql_real_escape_string($extras);
	$query="INSERT INTO students (studentId, registrationNumber, loginId, citizenshipId, nativeplace, disability, denominationId, postalAddress, physicalAddress, occupation, registrationTime, courseId, employed, flags, extraFilter, xmlFile, formFourYear, formFourIndex, admissionBatch, batchList) VALUES ('$studentId', '$registrationNumber', '$loginId', '$citizenshipId', '$nativeplace', '$disability', '$denominationId', '$postaladdress', '$phyaddress', '$occupation', '$regtime', '$courseId', '$isemployed', '$flags', '$extras', '$xmlFile', '2000', '$formfourindex', '$batchId', '$batchId')";
	$result = mysql_db_query($database, $query, $conn) or die("\nCould not Insert a record into student \n".mysql_error());
	//Photo name 
	//File Extension
	$temp = explode(".", $photo);
	$fileextension = end($temp);
	$photoname = "student_".$studentId.".".$fileextension;
	$sourcepath = "../../../oldSchoolBackup/photo/".$photo;
	$targetpath = "../../data/students/photo/".$photoname;
	if (file_exists($sourcepath) && ! copy($sourcepath, $targetpath)) echo "$counter: Could not move photo;";
	//Writing to login 
	$dob = $dob."00:00:00";
	$tnameincertificate = mysql_real_escape_string($nameincertificate);
	$firstname = mysql_real_escape_string($firstname);
	$middlename = mysql_real_escape_string($middlename);
	$lastname = mysql_real_escape_string($lastname);
	$query="UPDATE login SET groupId='2', jobId='1', fullname='$tnameincertificate', firstname='$firstname', middlename='$middlename', lastname='$lastname', dob='$dob', sexId='$sexId', maritalId='$maritalId', typeId='3', email='$email', phone='$mobile', photo='$photoname' WHERE loginId='$loginId'";
	try {
		$result = mysql_db_query($database, $query, $conn) or Object::shootException("\nCould not Update Login record \n".mysql_error());
	} catch (Exception $e)	{
		$email = "duplicate".$counter."@dup.cn";
		$mobile = "2".System::convertIntegerToStringOfAGivenLength($counter, 9);
		$query="UPDATE login SET groupId='2', jobId='1', fullname='$tnameincertificate', firstname='$firstname', middlename='$middlename', lastname='$lastname', dob='$dob', sexId='$sexId', maritalId='$maritalId', typeId='3', email='$email', phone='$mobile', photo='$photoname' WHERE loginId='$loginId'";
		$result = mysql_db_query($database, $query, $conn) or die("\nCould not Update Login record \n".mysql_error());
	}
	//Dealing with xmlFile 
	$fullPathXMLFile = "../../data/students/".$xmlFile;
	
	
	$doc=new DOMDocument(Object::$xmlVersion);
				$doc->formatOutput = true;
				//code here 
$student=$doc->createElement('student');
$studentIdR=$doc->createElement('studentId');
$studentIdR->appendChild($doc->createTextNode($studentId));
$student->appendChild($studentIdR);;
$studentName=$doc->createElement('studentName');
$studentName->appendChild($doc->createTextNode($nameincertificate));
$student->appendChild($studentName);;
$accademicHistory=$doc->createElement('accademicHistory');

$institution=$doc->createElement('institution');
$institutionName=$doc->createElement('institutionName');
$institutionName->appendChild($doc->createTextNode($formfourinstitution));
$institution->appendChild($institutionName);
$specialization=$doc->createElement('specialization');
$specialization->appendChild($doc->createTextNode("Certificate of Secondary Education"));
$institution->appendChild($specialization);
$levelId=$doc->createElement('levelId');
$levelId->appendChild($doc->createTextNode("4"));
$institution->appendChild($levelId);
$indexNumber=$doc->createElement('indexNumber');
$indexNumber->appendChild($doc->createTextNode($formfourindex));
$institution->appendChild($indexNumber);
$award=$doc->createElement('award');
$institution->appendChild($award);
$grade=$doc->createElement('grade');
$institution->appendChild($grade);
$startYear=$doc->createElement('startYear');
$institution->appendChild($startYear);
$endYear=$doc->createElement('endYear');
$institution->appendChild($endYear);
$accademicHistory->appendChild($institution);

if ($isformsix == 1)	{
	$institution=$doc->createElement('institution');
	$institutionName=$doc->createElement('institutionName');
	$institutionName->appendChild($doc->createTextNode($formsixinstitution));
	$institution->appendChild($institutionName);
	$specialization=$doc->createElement('specialization');
	$specialization->appendChild($doc->createTextNode($formsixspecialization));
	$institution->appendChild($specialization);
	$levelId=$doc->createElement('levelId');
	$levelId->appendChild($doc->createTextNode("8"));
	$institution->appendChild($levelId);
	$indexNumber=$doc->createElement('indexNumber');
	$indexNumber->appendChild($doc->createTextNode($formsixindex));
	$institution->appendChild($indexNumber);
	$award=$doc->createElement('award');
	$institution->appendChild($award);
	$grade=$doc->createElement('grade');
	$institution->appendChild($grade);
	$startYear=$doc->createElement('startYear');
	$institution->appendChild($startYear);
	$endYear=$doc->createElement('endYear');
	$institution->appendChild($endYear);
	$accademicHistory->appendChild($institution);
}

if ($isformsixeq == 1)	{
	$institution=$doc->createElement('institution');
	$institutionName=$doc->createElement('institutionName');
	$institutionName->appendChild($doc->createTextNode($formsixeqinstitution));
	$institution->appendChild($institutionName);
	$specialization=$doc->createElement('specialization');
	$specialization->appendChild($doc->createTextNode($formsixeqspecialization));
	$institution->appendChild($specialization);
	$levelId=$doc->createElement('levelId');
	$levelId->appendChild($doc->createTextNode("8"));
	$institution->appendChild($levelId);
	$indexNumber=$doc->createElement('indexNumber');
	$indexNumber->appendChild($doc->createTextNode($formsixeqindex));
	$institution->appendChild($indexNumber);
	$award=$doc->createElement('award');
	$institution->appendChild($award);
	$grade=$doc->createElement('grade');
	$institution->appendChild($grade);
	$startYear=$doc->createElement('startYear');
	$institution->appendChild($startYear);
	$endYear=$doc->createElement('endYear');
	$institution->appendChild($endYear);
	$accademicHistory->appendChild($institution);
}

if ($isdegree == 1)	{
	$institution=$doc->createElement('institution');
	$institutionName=$doc->createElement('institutionName');
	$institutionName->appendChild($doc->createTextNode($degreeinstitution));
	$institution->appendChild($institutionName);
	$specialization=$doc->createElement('specialization');
	$specialization->appendChild($doc->createTextNode($degreespecialization));
	$institution->appendChild($specialization);
	$levelId=$doc->createElement('levelId');
	$levelId->appendChild($doc->createTextNode("15"));
	$institution->appendChild($levelId);
	$indexNumber=$doc->createElement('indexNumber');
	$indexNumber->appendChild($doc->createTextNode($degreeindex));
	$institution->appendChild($indexNumber);
	$award=$doc->createElement('award');
	$institution->appendChild($award);
	$grade=$doc->createElement('grade');
	$institution->appendChild($grade);
	$startYear=$doc->createElement('startYear');
	$institution->appendChild($startYear);
	$endYear=$doc->createElement('endYear');
	$institution->appendChild($endYear);
	$accademicHistory->appendChild($institution);
}

if ($ismaster == 1)	{
	$institution=$doc->createElement('institution');
	$institutionName=$doc->createElement('institutionName');
	$institutionName->appendChild($doc->createTextNode($masterinstitution));
	$institution->appendChild($institutionName);
	$specialization=$doc->createElement('specialization');
	$specialization->appendChild($doc->createTextNode($masterspecialization));
	$institution->appendChild($specialization);
	$levelId=$doc->createElement('levelId');
	$levelId->appendChild($doc->createTextNode("17"));
	$institution->appendChild($levelId);
	$indexNumber=$doc->createElement('indexNumber');
	$indexNumber->appendChild($doc->createTextNode($masterindex));
	$institution->appendChild($indexNumber);
	$award=$doc->createElement('award');
	$institution->appendChild($award);
	$grade=$doc->createElement('grade');
	$institution->appendChild($grade);
	$startYear=$doc->createElement('startYear');
	$institution->appendChild($startYear);
	$endYear=$doc->createElement('endYear');
	$institution->appendChild($endYear);
	$accademicHistory->appendChild($institution);
}

$student->appendChild($accademicHistory);
$employmentHistory=$doc->createElement('employmentHistory');
if ($isemployed == 1)	{
	$employer=$doc->createElement('employer');
	$employerName=$doc->createElement('employerName');
	$employerName->appendChild($doc->createTextNode($empname));
	$employer->appendChild($employerName);
	$postalAddress=$doc->createElement('postalAddress');
	$postalAddress->appendChild($doc->createTextNode($emppostaladdress));
	$employer->appendChild($postalAddress);
	$physicalAddress=$doc->createElement('physicalAddress');
	$physicalAddress->appendChild($doc->createTextNode($empphyaddress));
	$employer->appendChild($physicalAddress);
	$telephone=$doc->createElement('telephone');
	$telephone->appendChild($doc->createTextNode($emptelephone));
	$employer->appendChild($telephone);
	$email=$doc->createElement('email');
	$email->appendChild($doc->createTextNode($empemail));
	$employer->appendChild($email);
	$mobile=$doc->createElement('mobile');
	$employer->appendChild($mobile);
	$fax=$doc->createElement('fax');
	$employer->appendChild($fax);
	$startYear=$doc->createElement('startYear');
	$employer->appendChild($startYear);
	$endYear=$doc->createElement('endYear');
	$employer->appendChild($endYear);
	$extraInformation=$doc->createElement('extraInformation');
	$employer->appendChild($extraInformation);
	$employmentHistory->appendChild($employer);
}
$student->appendChild($employmentHistory);
$financialSponsor=$doc->createElement('financialSponsor');

$sponsor=$doc->createElement('sponsor');
$sponsorName=$doc->createElement('sponsorName');
$sponsorName->appendChild($doc->createTextNode($sponsorname));
$sponsor->appendChild($sponsorName);
$payerId=$doc->createElement('payerId');
$payerId->appendChild($doc->createTextNode("11"));
$sponsor->appendChild($payerId);
$postalAddress=$doc->createElement('postalAddress');
$postalAddress->appendChild($doc->createTextNode($sponsorpostaladdress));
$sponsor->appendChild($postalAddress);
$physicalAddress=$doc->createElement('physicalAddress');
$physicalAddress->appendChild($doc->createTextNode($sponsorphyaddress));
$sponsor->appendChild($physicalAddress);
$fax=$doc->createElement('fax');
$sponsor->appendChild($fax);
$sponsorfax=$doc->createElement('sponsorfax');
$sponsorfax->appendChild($doc->createTextNode($sponsortelephone));
$sponsor->appendChild($sponsorfax);
$mobile=$doc->createElement('mobile');
$mobile->appendChild($doc->createTextNode($sponsormobile));
$sponsor->appendChild($mobile);
$email=$doc->createElement('email');
$email->appendChild($doc->createTextNode($sponsoremail));
$sponsor->appendChild($email);
$financedItem=$doc->createElement('financedItem');
$sponsor->appendChild($financedItem);
$extraInformation=$doc->createElement('extraInformation');
$sponsor->appendChild($extraInformation);
$financialSponsor->appendChild($sponsor);

$student->appendChild($financialSponsor);
$nextOfKins=$doc->createElement('nextOfKins');

$nextOfKin=$doc->createElement('nextOfKin');
$kinName=$doc->createElement('kinName');
$kinName->appendChild($doc->createTextNode($nxtkinname));
$nextOfKin->appendChild($kinName);
$relationship=$doc->createElement('relationship');
$relationship->appendChild($doc->createTextNode($nxtkinrelationship));
$nextOfKin->appendChild($relationship);
$postalAddress=$doc->createElement('postalAddress');
$postalAddress->appendChild($doc->createTextNode($nxtkinpostaladdress));
$nextOfKin->appendChild($postalAddress);
$physicalAddress=$doc->createElement('physicalAddress');
$physicalAddress->appendChild($doc->createTextNode($nxtkinphyaddress));
$nextOfKin->appendChild($physicalAddress);
$fax=$doc->createElement('fax');
$nextOfKin->appendChild($fax);
$telephone=$doc->createElement('telephone');
$telephone->appendChild($doc->createTextNode($nxtkintelephone));
$nextOfKin->appendChild($telephone);
$mobile=$doc->createElement('mobile');
$mobile->appendChild($doc->createTextNode($nxtkinmobile));
$nextOfKin->appendChild($mobile);
$email=$doc->createElement('email');
$email->appendChild($doc->createTextNode($nxtkinemail));
$nextOfKin->appendChild($email);
$extraInformation=$doc->createElement('extraInformation');
$nextOfKin->appendChild($extraInformation);
$nextOfKins->appendChild($nextOfKin);

$student->appendChild($nextOfKins);
$bankAccount=$doc->createElement('banks');
$student->appendChild($bankAccount);
$subjects=$doc->createElement('subjects');
$student->appendChild($subjects);
$results=$doc->createElement('results');
$student->appendChild($results);
$doc->appendChild($student);
				//code end here
				if (! $doc->save($fullPathXMLFile)) die("Could not write an XML File");
	
}
/*End Data Here*/

mysql_close($conn);
echo "\nSuccessful\n";
?>

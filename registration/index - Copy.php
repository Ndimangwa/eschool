<?php 
include("../config.php");
require_once("../common/validation.php");
require_once("../class/system.php");
$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
$profile1 = null;
$__profileId = Profile::getProfileReference($database, $conn);
try {
	$profile1 = new Profile($database, $__profileId , $conn);
	$profile1->loadXMLFolder("../data/profile");
} catch (Exception $e)	{
	die($e->getMessage());
}
mysql_close($conn);
if (! $profile1->isInstallationComplete())	{
	header("Location: ../installation/");
	exit();
}
if ($profile1->isAdmissionClosed())	{
	die("Hello, Registration process has alredy been closed; <a href='../'>Click Here</a> to go to the Home Page");
}
$dataFolder = "data";
$studentFolder = "../data/students";
$themeFolder = "sunny"; //--set this as a default 
if (! is_null($profile1->getTheme())) $themeFolder = $profile1->getTheme()->getThemeFolder();
//We Just need to get A Default Timezone so we can extra a year
$timezone="Africa/Dar_es_Salaam";
if (! is_null($profile1->getPHPTimezone())) $timezone = $profile1->getPHPTimezone()->getZoneName();
date_default_timezone_set($timezone);
$date=date("Y:m:d:H:i:s");
$__systemDayOffset = 0;
if (! is_null($profile1->getFirstDayOfAWeek()))	{
	$__systemDayOffset = $profile1->getFirstDayOfAWeek()->getOffsetValue();
}
$systemDate1 = new DateAndTime("Ndimangwa", $date, "Fadhili");
$pagenumber = -1;
$prevpagenumber = 9999;
$thispage=$_SERVER['PHP_SELF'];
if (isset($_REQUEST['pagenumber']))	{ $pagenumber=intval($_REQUEST['pagenumber']); }
if (isset($_REQUEST['prevpagenumber']))	{ $prevpagenumber=intval($_REQUEST['prevpagenumber']); }
?>
<html>
<head>
<title><?= $profile1->getProfileName() ?></title>
<link rel="stylesheet" type="text/css" media="all" href="../client/jquery-ui-1.11.3/themes/<?= $themeFolder ?>/jquery-ui.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/plugin/printArea/PrintArea.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/css/purecss/pure-min.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/css/site.css"/>
<style type="text/css">

</style>
<script type="text/javascript" src="../client/jquery.js"></script>
<script type="text/javascript" src="../client/jquery-ui-1.11.3/jquery-ui.js"></script>
<script type="text/javascript" src="../client/jquery-easy-ticker-master/jquery.easy-ticker.js"></script>
<script type="text/javascript" src="../client/plugin/printArea/jquery.PrintArea.js"></script>
<script type="text/javascript" src="../client/js/jvalidation.js"></script>
<script type="text/javascript" src="../client/js/page.js"></script>
<script type="text/javascript">
(function($)	{
	$(function()	{
		/*Date Handling*/
		$('.datepicker').datepicker({
			dateFormat: 'dd/mm/yy',
			firstDay: <?= $__systemDayOffset ?>,
			changeYear: true,
			yearRange:'1961:2099'
		});
		//Begin Testing 
	
		//--End Testing
	});
})(jQuery);
</script>
</head>
<body class="ui-sys-body">

<div class="ui-sys-main">
	<div class="ui-sys-front-header">
		<div class="ui-sys-front-logo mobile-collapse">
<?php 
	$logo = "../".$dataFolder."/".$profile1->getDataFolder()."/logo/default.jpg";
	if (! is_null($profile1->getLogo())) $logo = "../".$dataFolder."/".$profile1->getDataFolder()."/logo/".$profile1->getLogo();
?>
			<img alt="LG" src="<?= $logo ?>"/>
		</div>
		<div class="ui-sys-front-header-1 mobile-collapse">
			<div class="ui-sys-inst-name">STUDENT REGISTRATION SYSTEM</div>
		</div>
		<div class="ui-sys-clearboth">&nbsp;</div>
	</div>
<?php 
	if (! is_null($profile1->getSystemName()))	{
?>
	<div class="ui-sys-front-systemname mobile-collapse"><?= $profile1->getSystemName() ?></div>
<?php 
	}
?>
	<div class="ui-sys-front-topbutton mobile-collapse">
		<a class="button-link" title="Back to Home Page" href="../">Home</a>
	</div>

<!--BEGINNING OF PUTTING CODE-->
<div class="ui-sys-bg-grid-green">
<?php
	if (isset($_REQUEST['application']) && isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 1002)	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		 
		//Step 2: Handling Data for this Page 
		//$website = "";
		//Beginning UI 
		$referenceNumber = mysql_real_escape_string($_REQUEST['code']);
		$firstname = mysql_real_escape_string($_REQUEST['firstname']);
		$middlename = mysql_real_escape_string($_REQUEST['middlename']);
		$lastname = mysql_real_escape_string($_REQUEST['lastname']);
		$dob = mysql_real_escape_string($_REQUEST['dob']);
		$application = mysql_real_escape_string($_REQUEST['application']);
		$dob = DateAndTime::convertFromGUIDateFormatToSystemDateAndTimeFormat($dob);
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$loginId = Login::getLoginIdForAccount($database, $conn, $firstname, $middlename, $lastname, $dob, $referenceNumber);
		$studentId = null;
		$student1 = null;
		if (is_null($loginId))	{
			$promise1->setPromise(false);
			$promise1->setReason("The Record You are looking was not found");
		}
		if ($promise1->isPromising())	{
			$studentId = Student::getStudentIdFromLoginId($database, $conn, $loginId);
			if (is_null($studentId))	{
				$promise1->setPromise(false);
				$promise1->setReason("The Record You are looking was not found, the student reference could not be found from the login reference");
			}
		}
		if ($promise1->isPromising())	{
			try {
				$student1 = new Student($database, $studentId, $conn);
			} catch (Exception $e)	{
				$promise1->setReason(false);
				$promise1->setReason($e->getMessage());
			}
		}
		if ($promise1->isPromising())	{
			$logicA = ($application == "new");
			$logicB = ($application == "continuing");
			$logicC = $student1->isContinuingStudent();
			$logicD = ($referenceNumber == $student1->getReferenceNumber());
			//echo "A= $logicA, B = $logicB, C = $logicC, D = $logicD, application = $application";
			if (((! $logicA && $logicB && $logicC && $logicD) || ($logicA && ! $logicB && ! $logicC && $logicD)) && ! $student1->getLogin()->isAdmitted())	{
				$promise1->setPromise(true);
			} else {
				$promise1->setPromise(false);
				$promise1->setReason("There were no record associated with your Input Data");
			}
		}
?>
		<div class="mobile-collapse ui-sys-panel-container">
			<div class="ui-sys-panel ui-sys-data-capture ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Pending Application</div>
				<div class="ui-sys-panel-body">
					<div>
<?php 
	if ($promise1->isPromising())	{
?>
		Record Found; <a href="<?= $thispage ?>?application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>&prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $student1->getApplicationCounter() ?>"><b>CLICK HERE</b></a> to proceed to your records
<?php
	} else {
?>
		There were no record Associated with your Input data <br />
		Reason: <?= $promise1->getReason() ?><br /><br />
		<a href="../">Click Here</a> to proceed to Home Page
<?php
	}
?>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<?php
		mysql_close($conn);
	} else if (isset($_REQUEST['application']) && isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 1001)	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		 
		//Step 2: Handling Data for this Page 
		//$website = "";
		//Beginning UI 
?>
		<div class="mobile-collapse ui-sys-panel-container">
			<div class="ui-sys-panel ui-sys-data-capture ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Pending Application</div>
				<div class="ui-sys-panel-body">
					<div>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<input type="hidden" name="application" value="<?= $_REQUEST['application'] ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="code">Your Application Code </label>
									<input type="text" name="code" id="code" size="32" required pattern="<?= $exprL16Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL16Name ?>" validate_message="Application Code : <?= $msgL16Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="firstname">Your Firstname </label>
									<input type="text" name="firstname" id="firstname" size="32" required pattern="<?= $exprA32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Firstname : <?= $msgA32Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="middlename">Your Middlename </label>
									<input type="text" name="middlename" id="middlename" size="32" required pattern="<?= $exprA32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Middlename : <?= $msgA32Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="lastname">Your Lastname </label>
									<input type="text" name="lastname" id="lastname" size="32" required pattern="<?= $exprA32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Lastname : <?= $msgA32Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="dob">Your Date of Birth </label>
									<input class="datepicker" type="text" name="dob" id="dob" size="32" required pattern="<?= $exprDate ?>" validate="true" validate_control="text" validate_expression="<?= $exprDate ?>" validate_message="Date: <?= $msgDate ?>"/>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								<div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if (isset($_REQUEST['application']) && isset($_REQUEST['id'])&& isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 17)	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$student1 = null;
		$login1 = null;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		try {
			$student1 = new Student($database, $_REQUEST['id'], $conn);
			$login1 = $student1->getLogin();
			$student1->loadXMLFolder("../data/students");
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		$storedSafePage = intval($student1->getApplicationCounter());
		$enableUpdate = false;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
			} else {
				//INSERT WINDOW
				//Do Not Move App Counter 
				$student1->setRegistrationTime($systemDate1->getDateAndTimeString());
				//Note A student will be a member of his/her current accademicBatch 
				$student1->setListOfBatches($student1->getAdmissionBatch()->getAccademicYearId());
				$student1->setCurrentBatch($student1->getAdmissionBatch()->getAccademicYearId());
				//Current Accademic Year goes with the profile 
				$student1->setCurrentAccademicYear($profile1->getCurrentAccademicYear()->getAccademicYearId());
				$checksum = FileFactory::getFileChecksum($student1->getAbsoluteFilePath());
				if (! is_null($checksum))	{
					$student1->setXMLFileChecksum($checksum);
				}
				$login1->setRevisionNumber($profile1->getRevisionNumber());
				$login1->setRevisionTime($systemDate1->getDateAndTimeString());
				$login1->setAdmitted("0");
				$login1->setUsingDefaultPassword("1");
				$student1->setCompleted("1");
				$enableUpdate = true;
			}
			if ($enableUpdate)	{
				try {
					//We need To Add an ApprovalSequenceRecord 
					$promise1 = $student1->addApprovalSequenceDataEntryForAdmission($systemDate1); 
					if (! $promise1->isPromising()) Object::shootException($promise1->getReason());
					$login1->commitUpdate();
					$student1->commitUpdate();
				} catch (Exception $e)	{
					$promise1->setReason($e->getMessage());
					$promise1->setPromise(false);
				}
			}
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
		}
		//Beginning UI 
?>
		<div class="mobile-collapse ui-sys-panel-container">
			<div class="ui-sys-panel ui-sys-data-capture ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Congratulations</div>
				<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
					<div>
						<div class="ui-sys-center-block">
							Congratulations, <i><?= $login1->getFullname() ?></i>; <br/>
							<br/>You have successful made your registration at <i><?= $profile1->getProfileName() ?></i><br/>
							Your Registration was completed on <?= $student1->getRegistrationTime()->getDateAndTimeString() ?><br/><br/>
							<div>
								<b>Kindly Proceed to the Admission Office, to verify your documents, once your documents is verified you can log in to the system using</b> <br/>
								<div>
									Username : <b><?= $login1->getLoginName() ?></b>  OR <b><?= $login1->getEmail() ?></b><br/>
									Password : <b><?= Object::$defaultPassword ?></b><br/><br/>
									<div>
										<br/>Remember to Close this window and to change your password immediately after Login
									</div>
								</div>
							</div>
							<div>
								<br/>Remember, Anytime before Admission Office Approval Your Application, You can go to your Application, and choose Pending Application to modify your data<br/>
								You will still use your Application Reference Number : <b><?= $student1->getReferenceNumber() ?></b>
							</div>
						</div>
					</div>
<?php 
	} else {
?>
					<div class="ui-state-error">
						There were some problems in finalizing the Registration <br/>
						Details : <?= $promise1->getReason() ?>
					</div>
<?php
	}
?>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<?php
		mysql_close($conn);
	} else if (isset($_REQUEST['application']) && isset($_REQUEST['id'])&& isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 16)	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$student1 = null;
		$login1 = null;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		try {
			$student1 = new Student($database, $_REQUEST['id'], $conn);
			$login1 = $student1->getLogin();
			$student1->loadXMLFolder("../data/students");
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		$storedSafePage = intval($student1->getApplicationCounter());
		$enableUpdate = false;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
			} else {
				//INSERT WINDOW
				$student1->setApplicationCounter($pagenumber); 
				$enableUpdate = true;
			}
			if ($enableUpdate)	{
				try {
					$student1->commitUpdate();
				} catch (Exception $e)	{
					$promise1->setReason($e->getMessage());
					$promise1->setPromise(false);
				}
			}
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
		}
		//Beginning UI 
?>
		<div class="mobile-collapse ui-sys-panel-container">
			<div class="ui-sys-panel ui-sys-data-capture ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Agreement<br/>App Ref No: <?= $student1->getReferenceNumber() ?></div>
				<div class="ui-sys-panel-body">
					<div>
<?php 
	if ($promise1->isPromising())	{
?>
						<form  class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="id" value="<?= $student1->getStudentId() ?>"/>
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<input type="hidden" name="application" value="<?= $_REQUEST['application'] ?>"/>
							<div class="ui-sys-agreement-container">
								<div class="ui-sys-agreement-content">
									<div class="ui-sys-agreement-title">Between <?= $profile1->getProfileName() ?> and <?= $login1->getFullname() ?> on <?=  DateAndTime::convertFromDateTimeObjectToGUIDateFormat($systemDate1) ?></div>
<?php 
	include("../server/docs/admissionagreement.php");
?>
								</div>
								<div class="ui-sys-agreement-controls">
									<label><input type="checkbox" id="chkIAgree" value="1"/> I Agree</label>
								</div>
							</div>
							<fieldset>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								<div class="pure-controls">
									<span id="td-control-container">
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
									</span>
								</div>
							</fieldset>
						</form>
<?php 
	} else {
?>
		<div class="ui-state-error">
			There were problems in updating some of the data<br/>
			Details : <?= $promise1->getReason() ?>
		</div><br/>
		<div class="ui-sys-inline-controls-center"><a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;</div>
<?php
	}
?>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#chkIAgree').on('change', function(event)	{
		event.preventDefault();
		var $controlButtonContainer1 = $('#td-control-container');
		if (! $controlButtonContainer1.length) return;
		var $submitButton1 = $('#submitButton1');
		var $check1 = $(this);
		if ($check1.prop('checked') && ! $submitButton1.length)	{
			//I Agree 
			$('<input/>').attr('type', 'button')
						.attr('id', 'submitButton1')
						.attr('value', '  Finish  ')
						.button()
						.on('click', function(event)	{
							event.preventDefault();
							generalFormSubmission(this, 'form1', 'perror');
						})
						.appendTo($controlButtonContainer1);
		} else if ($submitButton1.length) {
			//Just remove the submitButton
			$submitButton1.remove();
		}
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if (isset($_REQUEST['application']) && isset($_REQUEST['id'])&& isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 15)	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$student1 = null;
		$promise1 = null;
		$login1 = null;
		try {
			$student1 = new Student($database, $_REQUEST['id'], $conn);
			$login1 = $student1->getLogin();
			$student1->loadXMLFolder("../data/students");
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		$storedSafePage = intval($student1->getApplicationCounter());
		$enableUpdate = false;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$email = mysql_real_escape_string($_REQUEST['email']);
			$firstSecurityQuestionId = mysql_real_escape_string($_REQUEST['firstSecurityQuestionId']);
			$firstSecurityAnswer = mysql_real_escape_string($_REQUEST['firstSecurityAnswer']);
			$secondSecurityQuestionId = mysql_real_escape_string($_REQUEST['secondSecurityQuestionId']);
			$secondSecurityAnswer = mysql_real_escape_string($_REQUEST['secondSecurityAnswer']);
			$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
				if ($email != $login1->getEmail())	{
					$login1->setEmail($email); $enableUpdate = true;
				}
				if (is_null($login1->getFirstSecurityQuestion()) || ($firstSecurityQuestionId != $login1->getFirstSecurityQuestion()->getQuestionId()))	{
					$login1->setFirstSecurityQuestion($firstSecurityQuestionId); $enableUpdate = true;
				}
				if ($firstSecurityAnswer != $login1->getFirstSecurityAnswer())	{
					$login1->setFirstSecurityAnswer($firstSecurityAnswer); $enableUpdate = true;
				}
				if (is_null($login1->getSecondSecurityQuestion()) || ($secondSecurityQuestionId != $login1->getSecondSecurityQuestion()->getQuestionId()))	{
					$login1->setSecondSecurityQuestion($secondSecurityQuestionId); $enableUpdate = true;
				}
				if ($secondSecurityAnswer != $login1->getSecondSecurityAnswer())	{
					$login1->setSecondSecurityAnswer($secondSecurityAnswer); $enableUpdate = true;
				}
				if ($extraInformation != $student1->getExtraInformation())	{
					$student1->setExtraInformation($extraInformation); $enableUpdate = true;
				}
			} else {
				//INSERT WINDOW
				$login1->setEmail($email);
				$login1->setFirstSecurityQuestion($firstSecurityQuestionId);
				$login1->setFirstSecurityAnswer($firstSecurityAnswer);
				$login1->setSecondSecurityQuestion($secondSecurityQuestionId);
				$login1->setSecondSecurityAnswer($secondSecurityAnswer);
				$student1->setExtraInformation($extraInformation);
				$student1->setApplicationCounter($pagenumber);
				$enableUpdate = true;
			}
			if ($enableUpdate)	{
				try {
					$login1->commitUpdate();
					$student1->commitUpdate();
				} catch (Exception $e)	{
					$promise1 = new Promise();
					$promise1->setPromise(false);
					$promise1->setReason($e->getMessage());
				}
			}
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
		}
		//Beginning UI 
?>
		<div class="mobile-collapse ui-sys-panel-container">
			<div class="ui-sys-panel ui-sys-data-capture ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Information Review<br/>App Ref No: <?= $student1->getReferenceNumber() ?></div>
				<div class="ui-sys-panel-body">
					<div>
<?php 
	if (is_null($promise1) || $promise1->isPromising())	{
?>
		<div class="ui-sys-content-window-scrollable">
			<div class="ui-sys-printable ui-sys-size-a4-potrait">
				<div class="ui-sys-printable-controls">
					<a><img title="Print" alt="P" src="../sysimage/print.png"/></a>
				</div>
<!--Beginning of Display Container-->
<?php 
	$profile1->setExtraFilter("../data/profile/logo/".$profile1->getLogo());
	$student1->setExtraFilter("../data/students/photo/".$student1->getLogin()->getPhoto());
	include("../documentreader/student.php");
?>
<!--End of Display Container-->			
			</div>
		</div>
				
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="id" value="<?= $student1->getStudentId() ?>"/>
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<input type="hidden" name="application" value="<?= $_REQUEST['application'] ?>"/>
							<fieldset>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								<div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
<?php 
	} else {
?>
		<div class="ui-state-error">
			There were problems in the Security Data <br/>
			Reason : <?= $promise1->getReason() ?>
		</div><br/>
		<div class="ui-sys-inline-controls-center"><a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;</div>
<?php
	}
?>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if (isset($_REQUEST['application']) && isset($_REQUEST['id'])&& isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 14)	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$student1 = null;
		$login1 = null;
		$promise1 = null;
		try {
			$student1 = new Student($database, $_REQUEST['id'], $conn);
			$login1 = $student1->getLogin();
			$student1->loadXMLFolder("../data/students");
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		$storedSafePage = intval($student1->getApplicationCounter());
		$enableUpdate = false;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$dataArray = array();
			$arrayIndex = 0;
			foreach ($_REQUEST['kinName'] as $index => $val)	{
				//Both share the same index 
				$dataArray[$arrayIndex] = array();
				$dataArray[$arrayIndex]['kinName'] = $_REQUEST['kinName'][$index];
				$dataArray[$arrayIndex]['relationship'] = $_REQUEST['relationship'][$index];
				$dataArray[$arrayIndex]['postalAddress'] = $_REQUEST['postalAddress'][$index];
				$dataArray[$arrayIndex]['physicalAddress'] = $_REQUEST['physicalAddress'][$index];
				$dataArray[$arrayIndex]['fax'] = $_REQUEST['fax'][$index];
				$dataArray[$arrayIndex]['telephone'] = $_REQUEST['telephone'][$index];
				$dataArray[$arrayIndex]['mobile'] = $_REQUEST['mobile'][$index];
				$dataArray[$arrayIndex]['email'] = $_REQUEST['email'][$index];
				$dataArray[$arrayIndex]['extraInformation'] = $_REQUEST['extraInformation'][$index];
				$arrayIndex++;
			}

			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW 
				//Do Nothing
			} else {
				//INSERT WINDOW
				$student1->setApplicationCounter($pagenumber);
				//We need to run the username Algorithm at this point
				try {
					$username = $login1->generateMyUsername();
					$login1->setLoginName($username);
					$defaultPassword = sha1(Object::$defaultPassword);
					$login1->setPassword($defaultPassword);
				} catch (Exception $e)	{
					die($e->getMessage());
				}
				$enableUpdate = true;
			}
			$promise1 = new Promise();
			$promise1->setPromise(true); //If remain untouched 
			if (sizeof($dataArray) > 0)	{
				//There is items to save 
				$promise1 = $student1->backupXMLFile();
				$promise1 = $student1->updateListOfNextOfKins($dataArray);
			}
			if ($enableUpdate && $promise1->isPromising())	{
				try {
					$login1->commitUpdate();
					$student1->commitUpdate();
				} catch (Exception $e)	{
					die($e->getMessage());
				}
			}
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		$email = "";
		//Password Display NONE 
		$firstSecurityQuestionId = -1;
		$firstSecurityAnswer = "";
		$secondSecurityQuestionId = -1;
		$secondSecurityAnswer = "";
		$extraInformation = "";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
			$email = $login1->getEmail();
			$firstSecurityQuestionId = $login1->getFirstSecurityQuestion()->getQuestionId();
			$firstSecurityAnswer = $login1->getFirstSecurityAnswer();
			$secondSecurityQuestionId = $login1->getSecondSecurityQuestion()->getQuestionId();
			$secondSecurityAnswer = $login1->getSecondSecurityAnswer();
			$extraInformation = $student1->getExtraInformation();
		}
		//Beginning UI 
?>
		<div class="mobile-collapse ui-sys-panel-container">
			<div class="ui-sys-panel ui-sys-data-capture ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Security<br/>App Ref No: <?= $student1->getReferenceNumber() ?></div>
				<div class="ui-sys-panel-body">
					<div>
<?php 
	if (is_null($promise1) || $promise1->isPromising())	{
?>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="id" value="<?= $student1->getStudentId() ?>"/>
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<input type="hidden" name="application" value="<?= $_REQUEST['application'] ?>"/>
							<div class="ui-sys-center-block">
								Your Username is 			:  <b><?= $login1->getLoginName() ?></b><br/>
								Your Default Password is 	:  <b><?= Object::$defaultPassword ?></b>
							</div>
							<fieldset>
								<div class="pure-control-group">
									<label for="email">Email Address</label>
									<input type="text" name="email" id="email" size="32" value="<?= $email ?>" required pattern="<?= $exprEmail ?>" validate="true" validate_control="text" validate_expression="<?= $exprEmail ?>%<?= $exprL32Name ?>" validate_message="Email Format : <?= $msgEmail ?>%Email Length: <?= $msgL32Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="cemail">Confirm Email Address</label>
									<input type="text" id="cemail" size="32" value="<?= $email ?>" required pattern="<?= $exprEmail ?>" validate="true" validate_control="text" validate_expression="<?= $exprEmail ?>%<?= $exprL32Name ?>" validate_message="Email Format : <?= $msgEmail ?>%Email Length: <?= $msgL32Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="firstSecurityQuestionId">First Security Question </label>
									<select id="firstSecurityQuestionId" name="firstSecurityQuestionId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select First Security Question">
										<option value="_@32767@_">--select--</option>
<?php 
	$list1 = SecurityQuestion::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($login1->getFirstSecurityQuestion()) && ($alist1['id'] == $login1->getFirstSecurityQuestion()->getQuestionId())) $selected="selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
									</select>
								</div>
								<div class="pure-control-group">
									<label for="firstSecurityAnswer">First Security Answer </label>
									<input type="text" name="firstSecurityAnswer" id="firstSecurityAnswer" size="32" value="<?= $firstSecurityAnswer ?>" required pattern="<?= $exprL32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL32Name ?>" validate_message="First Security Answer : <?= $msgL32Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="secondSecurityQuestionId">Second Security Question </label>
									<select id="secondSecurityQuestionId" name="secondSecurityQuestionId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Second Security Question">
										<option value="_@32767@_">--select--</option>
<?php 
	$list1 = SecurityQuestion::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($login1->getSecondSecurityQuestion()) && ($alist1['id'] == $login1->getSecondSecurityQuestion()->getQuestionId())) $selected="selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
									</select>
								</div>
								<div class="pure-control-group">
									<label for="secondSecurityAnswer">Second Security Answer </label>
									<input type="text" name="secondSecurityAnswer" id="secondSecurityAnswer" size="32" value="<?= $secondSecurityAnswer ?>" required pattern="<?= $exprL32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL32Name ?>" validate_message="Second Security Answer : <?= $msgL32Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="extraInformation">Any Extra Information </label>
									<input type="text" name="extraInformation" id="extraInformation" size="32" value="<?= $extraInformation ?>" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								<div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
<?php 
	} else {
?>
		<div class="ui-state-error">
			There were problems with Next Of Kins <br/>
			Reason: <?= $promise1->getReason() ?>
		</div><br/>
		<div class="ui-sys-inline-controls-center"><a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;</div>
<?php
	}
?>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		var $target1 = $('#perror');
		if (! $target1.length) return;
		var loginName="!@#$%^&*()";
		var $email1 = $('#email');
		if (! $email1.length) return;
		$target1.empty();
		if ($('#email').val() != $('#cemail').val())	{
			$('<span/>').html("Mismatch of Emails ==> Emails do not Match")
				.appendTo($target1);
			return;
		}
		$.ajax({
			url: "../server/service_username_email_availability.php",
			method: "POST",
			data: { param1: loginName, param2: $email1.val(), param3: <?= $login1->getLoginId() ?> },
			dataType: "json",
			cache: false,
			async: false
		}).done(function(data, textStatus, jqXHR)	{
			if (parseInt(data.code) === 0)	{
				generalFormSubmission(this, 'form1', 'perror');
			} else	{
				//Failed 
				$target1.html("1-- Failed " + data.message); return;
			}
		}).fail(function(jqXHR, textStatus, errorThrown)	{
			$target1.html("2-- Failed " + textStatus); return;
		}).always(function(data, textStatus, jqXHR)	{
			//Default Always
		})
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if (isset($_REQUEST['application']) && isset($_REQUEST['id'])&& isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 13)	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$student1 = null;
		$promise1 = null;
		try {
			$student1 = new Student($database, $_REQUEST['id'], $conn);
			$student1->loadXMLFolder("../data/students");
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		$storedSafePage = intval($student1->getApplicationCounter());
		$enableUpdate = false;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$accountName = mysql_real_escape_string($_REQUEST['accountName']);
			$bankAccount = mysql_real_escape_string($_REQUEST['bankAccount']);
			$bankName = mysql_real_escape_string($_REQUEST['bankName']);
			$branchName = mysql_real_escape_string($_REQUEST['branchName']);
			//Just Do A blindly add 
			$bankAccount1 = array();
			$bankAccount1['accountName'] = $accountName;
			$bankAccount1['bankAccount'] = $bankAccount;
			$bankAccount1['bankName'] = $bankName;
			$bankAccount1['branchName'] = $branchName;
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW -- do nothing
			} else {
				//INSERT WINDOW
				$student1->setApplicationCounter($pagenumber);
				$enableUpdate = true;
			}
			$promise1 = new Promise();
			$promise1->setPromise(true);
			$promise1 = $student1->backupXMLFile();
			$promise1 = $student1->clearBankAccounts();
			$promise1 = $student1->addBankAccount($bankAccount1);
			if ($enableUpdate)	{
				try {
					$student1->commitUpdate();
				} catch (Exception $e)	{
					$promise1->setReason($e->getMessage());
					$promise1->setPromise(false);
				}
			}
		}
		//Step 2: Handling Data for this Page 
		//$website = ""; Bank Account Saving Data 
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
		}
		//Beginning UI
?>
		<div class="mobile-collapse ui-sys-panel-container">
			<div class="ui-sys-panel ui-sys-data-capture ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Next Of Kins<br/>App Ref No: <?= $student1->getReferenceNumber() ?></div>
				<div class="ui-sys-panel-body">
					<div class="ui-sys-warning">
						We strongly recommend the next of kin to be a closer and a responsible person for your life at <?= $profile1->getProfileName() ?>. <br/>
						If your parents are still alive, the next of your kin should be your parents, if otherwise your guardian can be your next of kin
					</div>
					<div>
<?php 
	if (is_null($promise1) || $promise1->isPromising())	{
?>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="id" value="<?= $student1->getStudentId() ?>"/>
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<input type="hidden" name="application" value="<?= $_REQUEST['application'] ?>"/>
							<div class="ui-sys-field-block-container">
<!--Begin of the Container-->
<?php 
//Preparing Page 
$displayList1 = new DisplayList();
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'kinName'); $intent1->putExtra('labelCaption', 'Next of Kin Name');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprA64Name");
$intent1->putExtra('validateMessage', "Next of Kin Name $msgA64Name"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'kinName');
$displayList1->add($intent1);
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'relationship'); $intent1->putExtra('labelCaption', 'Relationship with You');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprA64Name");
$intent1->putExtra('validateMessage', "Relationship with You $msgA64Name"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'relationship');
$displayList1->add($intent1);
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'postalAddress'); $intent1->putExtra('labelCaption', 'Next of Kin Postal Address');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprA64Name");
$intent1->putExtra('validateMessage', "Next of Kin Postal Address $msgA64Name"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'postalAddress');
$displayList1->add($intent1);
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'physicalAddress'); $intent1->putExtra('labelCaption', 'Next of Kin Physical Address');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprA64Name");
$intent1->putExtra('validateMessage', "Next of Kin Physcial Address $msgA64Name"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'physicalAddress');
$displayList1->add($intent1);
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'fax'); $intent1->putExtra('labelCaption', 'Next of Kin Fax');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprPhone");
$intent1->putExtra('validateMessage', "Next of Kin Fax $msgPhone"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'fax'); $intent1->putExtra('notrequired', 'true');
$displayList1->add($intent1);
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'telephone'); $intent1->putExtra('labelCaption', 'Next of Kin Telephone Number');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprPhone");
$intent1->putExtra('validateMessage', "Next of Kin Telephone Number $msgPhone"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'telephone'); $intent1->putExtra('notrequired', 'true');
$displayList1->add($intent1);
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'mobile'); $intent1->putExtra('labelCaption', 'Next of Kin Mobile Number');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprPhone");
$intent1->putExtra('validateMessage', "Next of Kin Mobile Number $msgPhone"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'mobile'); $intent1->putExtra('notrequired', 'true');
$displayList1->add($intent1);
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'email'); $intent1->putExtra('labelCaption', 'Next of Kin Email');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprEmail");
$intent1->putExtra('validateMessage', "Next of Kin Email $msgEmail"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'email'); $intent1->putExtra('notrequired', 'true');
$displayList1->add($intent1);
$intent1 = new Intent('Valentina');
$intent1->putExtra('namePrefix', 'extraInformation'); $intent1->putExtra('labelCaption', 'Next of Kin Extra Information');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprA64Name");
$intent1->putExtra('validateMessage', "Next of Kin Extra Information $msgA64Name"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'extraInformation'); $intent1->putExtra('notrequired', 'true');
$displayList1->add($intent1);
//Fetch Previous Content 
$kinList1 = $student1->getListOfNextOfKins();
$blockToDisplay = UIServices::displayUIFieldBlocks($database, $conn, $kinList1, $displayList1, 0);
echo $blockToDisplay;
?>
<div class="ui-sys-field-block-add"><a class="button-link">Add</a></div>
<!--End of the Container-->							
							</div>
							<fieldset>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								 <div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
<?php 
	} else {
?>
		<div class="ui-state-error">
			There were problems in updating some of the details<br/>
			Details : <?= $promise1->getReason() ?>
		</div><br/>
		<div class="ui-sys-inline-controls-center"><a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;</div>
<?php
	}
?>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if (isset($_REQUEST['application']) && isset($_REQUEST['id'])&& isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 12)	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$student1 = null;
		$promise1 = null;
		try {
			$student1 = new Student($database, $_REQUEST['id'], $conn);
			$student1->loadXMLFolder("../data/students");
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		$storedSafePage = intval($student1->getApplicationCounter());
		$enableUpdate = false;
		//Step 1: Handling Data From the Previous Page
		if ($prevpagenumber < $pagenumber)	{
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$dataArray = array();
			$arrayIndex = 0;
			foreach ($_REQUEST['sponsorName'] as $index => $val)	{
				//Both share the same index 
				$dataArray[$arrayIndex] = array();
				$dataArray[$arrayIndex]['sponsorName'] = $_REQUEST['sponsorName'][$index];
				$dataArray[$arrayIndex]['payerId'] = $_REQUEST['payerId'][$index];
				$dataArray[$arrayIndex]['postalAddress'] = $_REQUEST['postalAddress'][$index];
				$dataArray[$arrayIndex]['physicalAddress'] = $_REQUEST['physicalAddress'][$index];
				$dataArray[$arrayIndex]['fax'] = $_REQUEST['fax'][$index];
				$dataArray[$arrayIndex]['telephone'] = $_REQUEST['telephone'][$index];
				$dataArray[$arrayIndex]['mobile'] = $_REQUEST['mobile'][$index];
				$dataArray[$arrayIndex]['email'] = $_REQUEST['email'][$index];
				$dataArray[$arrayIndex]['financedItem'] = $_REQUEST['financedItem'][$index];
				$dataArray[$arrayIndex]['extraInformation'] = $_REQUEST['extraInformation'][$index];
				$arrayIndex++;
			}

			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW 
				//Do Nothing
			} else {
				//INSERT WINDOW
				$student1->setApplicationCounter($pagenumber); $enableUpdate = true;
			}
			$promise1 = new Promise();
			$promise1->setPromise(true); //If remain untouched 
			if (sizeof($dataArray) > 0)	{
				//There is items to save 
				$promise1 = $student1->backupXMLFile();
				$promise1 = $student1->updateListOfSponsors($dataArray);
			}
			if ($enableUpdate && $promise1->isPromising())	{
				try {
					$student1->commitUpdate();
				} catch (Exception $e)	{
					$promise1->setReason($e->getMessage());
					$promise1->setPromise(false);
				}
			}
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		$accountName = "";
		$bankAccount = "";
		$bankName = "";
		$branchName = "";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
			$bankAccount1 = $student1->getBankAccountList();
			if (! is_null($bankAccount1))	{
				$bankAccount1 = $bankAccount1[0];
				$accountName = $bankAccount1->getElementsByTagName('accountName')->item(0)->nodeValue;
				$bankAccount = $bankAccount1->getElementsByTagName('bankAccount')->item(0)->nodeValue;
				$bankName = $bankAccount1->getElementsByTagName('bankName')->item(0)->nodeValue;
				$branchName = $bankAccount1->getElementsByTagName('branchName')->item(0)->nodeValue;
			}
		}
		//Beginning UI 
		//We must make sure the promise is set and has return valid response
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Bank Account <br/>App Ref No: <?= $student1->getReferenceNumber() ?></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if (is_null($promise1) || $promise1->isPromising())	{
?>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="id" value="<?= $student1->getStudentId() ?>"/>
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<input type="hidden" name="application" value="<?= $_REQUEST['application'] ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="accountName">Account Name </label>
									<input type="text" name="accountName" id="accountName" size="32" value="<?= $accountName ?>" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Account Name : <?= $msgA48Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="bankAccount">Bank Acount </label>
									<input type="text" name="bankAccount" id="bankAccount" size="32" value="<?= $bankAccount ?>" required pattern="<?= $exprL32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL32Name ?>" validate_message="Bank Account : <?= $msgL32Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="bankName">Bank Name </label>
									<input type="text" name="bankName" id="bankName" size="32" value="<?= $bankName ?>" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Bank Name : <?= $msgA48Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="branchName">Branch Name </label>
									<input type="text" name="branchName" id="branchName" size="32" value="<?= $branchName ?>" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Branch Name : <?= $msgA48Name ?>"/>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								<div class="pure-controls">
									<span class="ui-sys-inline-controls-right">
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems with saving sponsor details data<br/>
			Details : <?= $promise1->getReason() ?>
		</div><br/>
		<div class="ui-sys-inline-controls-center"><a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
				<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if (isset($_REQUEST['application']) && isset($_REQUEST['id'])&& isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 11)	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$student1 = null;
		$promise1 = null;
		try {
			$student1 = new Student($database, $_REQUEST['id'], $conn);
			$student1->loadXMLFolder("../data/students");
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		$storedSafePage = intval($student1->getApplicationCounter());
		$enableUpdate = false;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$dataArray = array();
			$arrayIndex = 0;
			$student1->setEmployed("0"); $enableUpdate = true;
			if (isset($_REQUEST['isEmployed']))	{
				$student1->setEmployed("1"); $enableUpdate = true;
				foreach ($_REQUEST['employerName'] as $index => $val)	{
					//Both share the same index 
					$dataArray[$arrayIndex] = array();
					$dataArray[$arrayIndex]['employerName'] = $_REQUEST['employerName'][$index];
					$dataArray[$arrayIndex]['postalAddress'] = $_REQUEST['postalAddress'][$index];
					$dataArray[$arrayIndex]['physicalAddress'] = $_REQUEST['physicalAddress'][$index];
					$dataArray[$arrayIndex]['fax'] = $_REQUEST['fax'][$index];
					$dataArray[$arrayIndex]['telephone'] = $_REQUEST['telephone'][$index];
					$dataArray[$arrayIndex]['mobile'] = $_REQUEST['mobile'][$index];
					$dataArray[$arrayIndex]['email'] = $_REQUEST['email'][$index];
					$dataArray[$arrayIndex]['startYear'] = $_REQUEST['startYear'][$index];
					$dataArray[$arrayIndex]['endYear'] = $_REQUEST['endYear'][$index];
					$dataArray[$arrayIndex]['extraInformation'] = $_REQUEST['extraInformation'][$index];
					$arrayIndex++;
				}
			}
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW 
				//Do Nothing
			} else {
				//INSERT WINDOW
				$student1->setApplicationCounter($pagenumber); $enableUpdate = true;
			}
			$promise1 = new Promise();
			$promise1->setPromise(true); //If remain untouched 
			if (sizeof($dataArray) > 0)	{
				//There is items to save 
				$promise1 = $student1->backupXMLFile();
				$promise1 = $student1->updateListOfEmployers($dataArray);
			}
			if ($enableUpdate && $promise1->isPromising())	{
				try {
					$student1->commitUpdate();
				} catch (Exception $e)	{
					$promise1->setReason($e->getMessage());
					$promise1->setPromise(false);
				}
			}
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
		}
		//Beginning UI 
?>
		<div class="mobile-collapse ui-sys-panel-container">
			<div class="ui-sys-panel ui-sys-data-capture ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Sponsor Details <br/>App Ref No: <?= $student1->getReferenceNumber() ?></div>
				<div class="ui-sys-panel-body">
					<div>
<?php 
	if (is_null($promise1) || $promise1->isPromising())	{
?>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="id" value="<?= $student1->getStudentId() ?>"/>
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<input type="hidden" name="application" value="<?= $_REQUEST['application'] ?>"/>
							<div class="ui-sys-field-block-container">
<!--Begin of the Container-->
<?php 
//Preparing Page 
$displayList1 = new DisplayList();
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'sponsorName'); $intent1->putExtra('labelCaption', 'Sponsor Name');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprA64Name");
$intent1->putExtra('validateMessage', "Sponsor Name $msgA64Name"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'sponsorName');
$displayList1->add($intent1);
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'payerId'); $intent1->putExtra('labelCaption', 'Sponsor Category');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "select"); $intent1->putExtra('validateExpression', "select");
$intent1->putExtra('validateMessage', "Kindly Select a Sponsor Category"); $intent1->putExtra('className', 'FeePayer');
$intent1->putExtra('tagName', 'payerId');
$displayList1->add($intent1);
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'postalAddress'); $intent1->putExtra('labelCaption', 'Sponsor Postal Address');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprA64Name");
$intent1->putExtra('validateMessage', "Sponsor Postal Address $msgA64Name"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'postalAddress');
$displayList1->add($intent1);
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'physicalAddress'); $intent1->putExtra('labelCaption', 'Sponsor Physical Address');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprA64Name");
$intent1->putExtra('validateMessage', "Sponsor Physcial Address $msgA64Name"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'physicalAddress');
$displayList1->add($intent1);
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'fax'); $intent1->putExtra('labelCaption', 'Sponsor Fax');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprPhone");
$intent1->putExtra('validateMessage', "Sponsor Fax $msgPhone"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'fax'); $intent1->putExtra('notrequired', 'true');
$displayList1->add($intent1);
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'telephone'); $intent1->putExtra('labelCaption', 'Sponsor Telephone Number');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprPhone");
$intent1->putExtra('validateMessage', "Sponsor Telephone Number $msgPhone"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'telephone'); $intent1->putExtra('notrequired', 'true');
$displayList1->add($intent1);
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'mobile'); $intent1->putExtra('labelCaption', 'Sponsor Mobile Number');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprPhone");
$intent1->putExtra('validateMessage', "Sponsor Mobile Number $msgPhone"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'mobile'); $intent1->putExtra('notrequired', 'true');
$displayList1->add($intent1);
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'email'); $intent1->putExtra('labelCaption', 'Sponsor Email');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprEmail");
$intent1->putExtra('validateMessage', "Sponsor Email $msgEmail"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'email'); $intent1->putExtra('notrequired', 'true');
$displayList1->add($intent1);
$intent1 = new Intent('Valentina');
$intent1->putExtra('namePrefix', 'financedItem'); $intent1->putExtra('labelCaption', 'What is S/He Sponsoring? ');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprA64Name");
$intent1->putExtra('validateMessage', "What is S/He Sponsoring $msgA64Name"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'financedItem'); $intent1->putExtra('notrequired', 'true');
$displayList1->add($intent1);
$intent1 = new Intent('Valentina');
$intent1->putExtra('namePrefix', 'extraInformation'); $intent1->putExtra('labelCaption', 'Sponsor Extra Information');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprA64Name");
$intent1->putExtra('validateMessage', "Sponsor Extra Information $msgA64Name"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'extraInformation'); $intent1->putExtra('notrequired', 'true');
$displayList1->add($intent1);
//Fetch Previous Content 
$sponsorList1 = $student1->getListOfSponsors();
$blockToDisplay = UIServices::displayUIFieldBlocks($database, $conn, $sponsorList1, $displayList1, 0);
echo $blockToDisplay;
?>
<div class="ui-sys-field-block-add"><a class="button-link">Add</a></div>
<!--End of the Container-->							
							</div>
							<fieldset>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								<div class="pure-controls">
									<span class="ui-sys-inline-controls-right">
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
<?php 
	} else {
?>
		<div class="ui-state-error">
			There were problems related to Writing on the System Files for Employment History <br/>
			Reason: <?= $promise1->getReason() ?>
		</div><br/>
		<div class="ui-sys-inline-controls-center"><a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;</div>
<?php
	}
?>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if (isset($_REQUEST['application']) && isset($_REQUEST['id'])&& isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 10)	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$student1 = null;
		$login1 = null;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		try {
			$student1 = new Student($database, $_REQUEST['id'], $conn);
			$login1 = $student1->getLogin();
			$student1->loadXMLFolder("../data/students");
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		$storedSafePage = intval($student1->getApplicationCounter());
		$enableUpdate = false;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$postalAddress = mysql_real_escape_string($_REQUEST['postalAddress']);
			$physicalAddress = mysql_real_escape_string($_REQUEST['physicalAddress']);
			$phone = mysql_real_escape_string($_REQUEST['phone']);
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
				if ($postalAddress != $student1->getPostalAddress())	{
					$student1->setPostalAddress($postalAddress); $enableUpdate=true;
				}
				if ($physicalAddress != $student1->getPhysicalAddress())	{
					$student1->setPhysicalAddress($physicalAddress); $enableUpdate=true;
				}
				if ($phone != $login1->getPhone())	{
					$login1->setPhone($phone); $enableUpdate=true;
				}
			} else {
				//INSERT WINDOW
				$student1->setPostalAddress($postalAddress);
				$student1->setPhysicalAddress($physicalAddress);
				$login1->setPhone($phone);
				$student1->setApplicationCounter($pagenumber);
				$enableUpdate=true;
			}
			if ($enableUpdate)	{
				try {
					$login1->commitUpdate();
					$student1->commitUpdate();
				} catch (Exception $e)	{ 
					$promise1->setReason($e->getMessage());
					$promise1->setPromise(false);
				}
			}
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		$chkIsEmployed = "";
		if ($student1->isEmployed()) $chkIsEmployed="checked";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
		}
		//Beginning UI 
?>
		<div class="mobile-collapse ui-sys-panel-container">
			<div class="ui-sys-panel ui-sys-data-capture ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Employment History<br/>App Ref No: <?= $student1->getReferenceNumber() ?></div>
				<div class="ui-sys-panel-body">
					<div>
<?php 
	if ($promise1->isPromising())	{
?>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="id" value="<?= $student1->getStudentId() ?>"/>
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<input type="hidden" name="application" value="<?= $_REQUEST['application'] ?>"/>
							<div class="ui-sys-center-block">
								<label><input class="pure-checkbox" <?= $chkIsEmployed ?> name="isEmployed" id="isEmployed" type="checkbox" value="1"/>&nbsp;&nbsp; Are You Employed or Have your Ever Being Employed?</label>
							</div>
							<div class="ui-sys-field-block-container">
<!--Begin of the Container-->
<?php 
//Preparing Page 
$displayList1 = new DisplayList();
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'employerName'); $intent1->putExtra('labelCaption', 'Employer Name');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprA64Name");
$intent1->putExtra('validateMessage', "Employer Name $msgA64Name"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'employerName');
$displayList1->add($intent1);
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'postalAddress'); $intent1->putExtra('labelCaption', 'Employer Postal Address');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprA64Name");
$intent1->putExtra('validateMessage', "Employer Postal Address $msgA64Name"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'postalAddress');
$displayList1->add($intent1);
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'physicalAddress'); $intent1->putExtra('labelCaption', 'Employer Physical Address');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprA64Name");
$intent1->putExtra('validateMessage', "Employer Physcial Address $msgA64Name"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'physicalAddress');
$displayList1->add($intent1);
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'fax'); $intent1->putExtra('labelCaption', 'Employer Fax');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprPhone");
$intent1->putExtra('validateMessage', "Employer Fax $msgPhone"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'fax'); $intent1->putExtra('notrequired', 'true');
$displayList1->add($intent1);
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'telephone'); $intent1->putExtra('labelCaption', 'Employer Telephone Number');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprPhone");
$intent1->putExtra('validateMessage', "Employer Telephone Number $msgPhone"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'telephone'); $intent1->putExtra('notrequired', 'true');
$displayList1->add($intent1);
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'mobile'); $intent1->putExtra('labelCaption', 'Employer Mobile Number');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprPhone");
$intent1->putExtra('validateMessage', "Employer Mobile Number $msgPhone"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'mobile'); $intent1->putExtra('notrequired', 'true');
$displayList1->add($intent1);
$intent1 = new Intent('Ndimangwa');
$intent1->putExtra('namePrefix', 'email'); $intent1->putExtra('labelCaption', 'Employer Email');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprEmail");
$intent1->putExtra('validateMessage', "Employer Email $msgEmail"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'email'); $intent1->putExtra('notrequired', 'true');
$displayList1->add($intent1);
$intent1 = new Intent('Zoomtong');
$intent1->putExtra('namePrefix', 'startYear'); $intent1->putExtra('labelCaption', 'From (Year)');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprDateYearOnly");
$intent1->putExtra('validateMessage', "Year From $msgDateYearOnly"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'startYear');
$displayList1->add($intent1);
$intent1 = new Intent('Zoomtong');
$intent1->putExtra('namePrefix', 'endYear'); $intent1->putExtra('labelCaption', 'To (Year)');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprDateYearOnly");
$intent1->putExtra('validateMessage', "Year To $msgDateYearOnly"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'endYear');
$displayList1->add($intent1);
$intent1 = new Intent('Valentina');
$intent1->putExtra('namePrefix', 'extraInformation'); $intent1->putExtra('labelCaption', 'Employer Extra Information');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprA64Name");
$intent1->putExtra('validateMessage', "Employer Extra Information $msgA64Name"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'extraInformation'); $intent1->putExtra('notrequired', 'true');
$displayList1->add($intent1);
//Fetch Previous Content 
$employerList1 = $student1->getListOfEmployers();
$blockToDisplay = UIServices::displayUIFieldBlocks($database, $conn, $employerList1, $displayList1, 0);
echo $blockToDisplay;
?>
<div class="ui-sys-field-block-add"><a class="button-link">Add</a></div>
<!--End of the Container-->
							</div>
							<fieldset>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								<div class="pure-controls">
									<span class="ui-sys-inline-controls-right">
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
<?php 
	} else {
?>
		<div class="ui-state-error">
			There were problems in updating some of the data; We do suspect the phone/telephone you entered is already assigned to another student/user<br/>
			Details: <?= $promise1->getReason() ?>
		</div><br/>
		<div class="ui-sys-inline-controls-center"><a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;</div>
<?php
	}
?>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	function uiMaintainanceForFieldBlock(isEnable)	{
		$('div.ui-sys-field-block .data-capture-control').prop('disabled', ! isEnable);
		var $addControl1 = $('div.ui-sys-field-block-add a');
		var $removeControl1 = $('div.ui-sys-field-block-remove a');
		if (! ($addControl1.length && $removeControl1.length)) return;
		if (isEnable)	{
			if ($addControl1.hasClass('disable-action-button')) $addControl1.removeClass('disable-action-button');
			if ($removeControl1.hasClass('disable-action-button')) $removeControl1.removeClass('disable-action-button');
		} else {
			if (! $addControl1.hasClass('disable-action-button')) $addControl1.addClass('disable-action-button');
			if (! $removeControl1.hasClass('disable-action-button')) $removeControl1.addClass('disable-action-button');
		}	
	}
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
	//Event State 
	$('#isEmployed').on('change', function(event)	{
		event.preventDefault();
		uiMaintainanceForFieldBlock($(this).prop('checked'));
	});
	//Initial State 
	uiMaintainanceForFieldBlock($('#isEmployed').prop('checked'));
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if (isset($_REQUEST['application']) && isset($_REQUEST['id'])&& isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 9)	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$student1 = null;
		$login1 = null;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		try {
			$student1 = new Student($database, $_REQUEST['id'], $conn);
			$login1 = $student1->getLogin();
			$student1->loadXMLFolder("../data/students");
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		$storedSafePage = intval($student1->getApplicationCounter());
		$enableUpdate = false;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$denominationId = mysql_real_escape_string($_REQUEST['denominationId']);
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
				if (is_null($student1->getDenomination()) || ($denominationId != $student1->getDenomination()->getDenominationId()))	{
					$student1->setDenomination($denominationId); $enableUpdate = true;
				}
			} else {
				//INSERT WINDOW
				$student1->setDenomination($denominationId);
				$student1->setApplicationCounter($pagenumber);
				$enableUpdate =true;
			}
			if ($enableUpdate)	{
				try {
					$student1->commitUpdate();
				} catch (Exception $e)	{ 
					$promise1->setReason($e->getMessage());
					$promise1->setPromise(false);
				}
			} 
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		$phone = "";
		$postalAddress = "";
		$physicalAddress = "";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
			$phone = $login1->getPhone();
			$postalAddress = $student1->getPostalAddress();
			$physicalAddress = $student1->getPhysicalAddress();
		}
		//Beginning UI 
?>
		<div class="mobile-collapse ui-sys-panel-container">
			<div class="ui-sys-panel ui-sys-data-capture ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Your Addresses & Phone<br/>App Ref No: <?= $student1->getReferenceNumber() ?></div>
				<div class="ui-sys-panel-body">
					<div>
<?php 
	if ($promise1->isPromising())	{
?>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="id" value="<?= $student1->getStudentId() ?>"/>
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<input type="hidden" name="application" value="<?= $_REQUEST['application'] ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="postalAddress">Postal Address </label>
									<input type="text" name="postalAddress" id="postalAddress" size="32" value="<?= $postalAddress ?>" required pattern="<?= $exprA64Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA64Name ?>" validate_message="Postal Address : <?= $msgA64Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="physicalAddress">Physical Address </label>
									<input type="text" name="physicalAddress" id="physicalAddress" size="32" value="<?= $physicalAddress ?>" required pattern="<?= $exprA64Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA64Name ?>" validate_message="Physical Address : <?= $msgA64Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="phone">Phone/Telephone </label>
									<input type="text" name="phone" id="phone" size="32" value="<?= $phone ?>" required pattern="<?= $exprPhone ?>" validate="true" validate_control="text" validate_expression="<?= $exprPhone ?>" validate_message="Phone/Telephone : <?= $msgPhone ?>"/>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								<div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
<?php 
	} else {
?>
		<div class="ui-state-error">
			There were problems in updating some of the data<br/>
			Details : <?= $promise1->getReason() ?>
		</div><br/>
		<div class="ui-sys-inline-controls-center"><a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;</div>
<?php
	}
?>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if (isset($_REQUEST['application']) && isset($_REQUEST['id'])&& isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 8)	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$student1 = null;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		try {
			$student1 = new Student($database, $_REQUEST['id'], $conn);
			$student1->loadXMLFolder("../data/students");
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		$storedSafePage = intval($student1->getApplicationCounter());
		$enableUpdate = false;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$countryId = mysql_real_escape_string($_REQUEST['countryId']);
			$nativeplace = mysql_real_escape_string($_REQUEST['nativeplace']);
			$occupation = mysql_real_escape_string($_REQUEST['occupation']);
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
				if (is_null($student1->getCitizenship()) || ($countryId != $student1->getCitizenship()->getCountryId()))	{
					$student1->setCitizenship($countryId); $enableUpdate = true;
				}
				if ($nativeplace != $student1->getNativeplace())	{
					$student1->setNativeplace($nativeplace); $enableUpdate = true;
				}
				if ($occupation != $student1->getOccupation())	{
					$student1->setOccupation($occupation); $enableUpdate = true;
				}
			} else {
				//INSERT WINDOW
				$student1->setCitizenship($countryId);
				$student1->setNativeplace($nativeplace);
				$student1->setOccupation($occupation);
				$student1->setApplicationCounter($pagenumber);
				$enableUpdate = true;
			}
			if ($enableUpdate)	{
				try {
					$student1->commitUpdate();
				} catch (Exception $e)	{ 
					$promise1->setReason($e->getMessage());
					$promise1->setPromise(false);
				}
			}
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		$religionId = "";
		$denominationId = "";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
			$religionId = $student1->getDenomination()->getReligion()->getReligionId();
			$denominationId = $student1->getDenomination()->getDenominationId();
		}
		//Beginning UI 
?>
		<div class="mobile-collapse ui-sys-panel-container">
			<div class="ui-sys-panel ui-sys-data-capture ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Religion<br/>App Ref No: <?= $student1->getReferenceNumber() ?></div>
				<div class="ui-sys-panel-body">
					<div>
<?php 
	if ($promise1->isPromising())	{
?>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="id" value="<?= $student1->getStudentId() ?>"/>
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<input type="hidden" name="application" value="<?= $_REQUEST['application'] ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="religionId">Religion </label>
									<select id="religionId" name="religionId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly select your Religion">
										<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Religion::loadAllData($database, $conn);
	$holdOthers = false;
	$othersId = "";
	$othersName = "";
	$othersSelected = "";
	foreach ($list1 as $alist1)	{
		$selected = "";
		if ($alist1['id'] == $religionId) $selected="selected";
		if ($alist1['val'] == "Others")	{
			if ($alist1['id'] == $religionId) $othersSelected="selected";
			$othersId = $alist1['id'];
			$othersName = $alist1['val'];
			$holdOthers = true;
			continue;
		}
?>
		<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
	if ($holdOthers)	{
?>
		<option <?= $othersSelected ?> value="<?= $othersId ?>"><?= $othersName ?></option>
<?php
	}
?>
									</select>
								</div>
								<div class="pure-control-group">
									<label for="denominationId">Denomination </label>
									<select id="denominationId" name="denominationId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly select your Denomination">
										<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Denomination::loadAllData($database, $conn);
	$holdOthers = false;
	$othersSelected = "";
	$othersId = "";
	$othersName = "";
	foreach ($list1 as $alist1)	{
		$denomination1 = null;
		try {
			$denomination1 = new Denomination($database, $alist1['id'], $conn);
		} catch (Exception $e)	{
			$__e_message = $e->getMessage();
			die("<option value='_@32767@_'>$__e_message</option>");
		}
		//Display Only belonging the religion above 
		if ($denomination1->getReligion()->getReligionId() == $religionId)	{
			//Everything Should be displayed at this area 
			$selected = "";
			if ($denominationId == $denomination1->getDenominationId())	 $selected="selected";
			if ($denomination1->getDenominationName() == "Others")	{
				if ($denominationId == $denomination1->getDenominationId())	 $othersSelected="selected";
				$othersId = $denomination1->getDenominationId();
				$othersName = $denomination1->getDenominationName();
				$holdOthers = true;
				continue;
			}
?>
			<option <?= $selected ?> value="<?= $denomination1->getDenominationId() ?>"><?= $denomination1->getDenominationName() ?></option>
<?php
		} //end--if 
	}
	if ($holdOthers)	{
?>
		<option <?= $othersSelected ?> value="<?= $othersId ?>"><?= $othersName ?></option>
<?php
	}
?>
									</select>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								 <div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
<?php 
	} else {
?>
		<div class="ui-state-error">
			There were problems in updating some of the details <br/>
			Details : <?= $promise1->getReason() ?>
		</div><br/>
		<div class="ui-sys-inline-controls-center"><a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;</div>
<?php
	}
?>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
	$('#religionId').on('change', function(event)	{
		event.preventDefault();
		var $select1 = $(this).closest('select');
		var religionId = $select1.val();
		var $target1 = $('#perror');
		if (! $target1.length) return;
		$target1.empty();
		$.ajax({
			url: "../server/service_load_denomination.php",
			method: "POST",
			data: { param1: religionId },
			dataType: "json",
			cache: false,
			async: false
		}).done(function(data, textStatus, jqXHR)	{
			if (parseInt(data.code) === 0)	{
				var $select1 = $('#denominationId');
				if (! $select1.length) return;
				$select1.empty();
				$('<option/>').attr('value', '_@32767@_').html('--select--').appendTo($select1);
				var othersEnable = false;
				var othersId = "";
				var othersName = "";
				for (var i in data.options)	{
					var option1 = data.options[i].option;
					if (option1.val == "Others")	{
						othersId = option1.id;
						othersName = option1.val;
						othersEnable = true;
						continue;
					}
					$('<option/>').attr('value', option1.id).html(option1.val).appendTo($select1);
				}
				if (othersEnable)	{
					$('<option/>').attr('value', othersId).html(othersName).appendTo($select1);
				}
			} else	{
				//Failed 
				$target1.html("1-- Failed " + data.message); return;
			}
		}).fail(function(jqXHR, textStatus, errorThrown)	{
			$target1.html("2-- Failed " + textStatus); return;
		}).always(function(data, textStatus, jqXHR)	{
			//Default Always
		});
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if (isset($_REQUEST['application']) && isset($_REQUEST['id'])&& isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 7)	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$student1 = null;
		$login1 = null;
		$promise1 = null;
		try {
			$student1 = new Student($database, $_REQUEST['id'], $conn);
			$student1->loadXMLFolder("../data/students");
			$login1 = $student1->getLogin();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		$storedSafePage = intval($student1->getApplicationCounter());
		$enableUpdate = false;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$logoArray1 = $_FILES['logo'];
			$enablePhotoUpdate = false;
			if ($pagenumber - 1 < $storedSafePage)	{
				if (Object::isThereAnyFileReceivedFromTheClient($logoArray1))	{
					$enablePhotoUpdate = true;
				}
			} else {
				if (Object::isThereAnyFileReceivedFromTheClient($logoArray1))	{
					$enablePhotoUpdate = true;
				}
				$enableUpdate = true;
				$student1->setApplicationCounter($pagenumber);
			}
			$logoFolder = "../data/students/photo";
			$logofilename = "student_".$login1->getLoginId();
			$fileextension = Object::getUploadedFileExtension($logoArray1);
			$uploadedFileName = $logofilename.".".$fileextension;
			if ($enablePhotoUpdate)	{
				//We will just overwrite 
				$validTypes = array("image/jpeg", "image/png", "image/jpg");
				$validExtensions = array("jpeg", "png", "jpg");
				$maximumUploadedSize = Object::$__MAXIMUM_UPLOADED_IMAGE_SIZE;
				$promise1 = Object::saveUploadedFile($logoArray1, $logoFolder, $logofilename, $validTypes, $validExtensions, $maximumUploadedSize);
			}
			if (! is_null($promise1) && $promise1->isPromising())	{
				$login1->setPhoto($uploadedFileName);
				$enableUpdate = true;
			}
			if ($enableUpdate)	{
				try {
					$login1->commitUpdate();
					$student1->commitUpdate();
				} catch (Exception $e)	{ die($e->getMessage()); }
			}
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		$countryId = "";
		$nativeplace = "";
		$occupation = "";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
			$countryId = $student1->getCitizenship()->getCountryId();
			$nativeplace = $student1->getNativeplace();
			if (! is_null($student1->getOccupation()))	{
				$occupation = $student1->getOccupation();
			}
		}
		//Beginning UI 
?>
		<div class="mobile-collapse ui-sys-panel-container">
			<div class="ui-sys-panel ui-sys-data-capture ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Locality<br/>App Ref No: <?= $student1->getReferenceNumber() ?></div>
				<div class="ui-sys-panel-body">
					<div>
<?php 
	if (is_null($promise1) || $promise1->isPromising())	{
?>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="id" value="<?= $student1->getStudentId() ?>"/>
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<input type="hidden" name="application" value="<?= $_REQUEST['application'] ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="countryId">Citizenship </label>
									<select id="countryId" name="countryId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly select your Citizenship">
										<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Country::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if ($alist1['id'] == $countryId) $selected="selected";
?>
		<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
									</select>
								</div>
								<div class="pure-control-group">
									<label title="A Place of Your Origin" for="nativeplace">Native Place </label>
									<input type="text" name="nativeplace" id="nativeplace" size="32" value="<?= $nativeplace ?>" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Native Place : <?= $msgA48Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="occupation">Your Occupation </label>
									<input type="text" name="occupation" id="occupation" size="32" value="<?= $occupation ?>" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Occupation : <?= $msgA32Name ?>"/>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								<div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
<?php 
	} else {
?>
		<div class="ui-state-error">
			There were problems in uploading your photo <br/>
			Reason: <?= $promise1->getReason() ?>
		</div><br/>
		<div class="ui-sys-inline-controls-center"><a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;</div>
<?php
	}
?>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if (isset($_REQUEST['application']) && isset($_REQUEST['id'])&& isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 6)	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$student1 = null;
		$login1 = null;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		try {
			$student1 = new Student($database, $_REQUEST['id'], $conn);
			$student1->loadXMLFolder("../data/students");
			$login1 = $student1->getLogin();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		$storedSafePage = intval($student1->getApplicationCounter());
		$enableUpdate = false;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$courseId = mysql_real_escape_string($_REQUEST['courseId']);
			$formFourYear = mysql_real_escape_string($_REQUEST['formFourYear']);
			$formFourIndex = mysql_real_escape_string($_REQUEST['formFourIndex']);
			$currentYear = 1;
			if (isset($_REQUEST['currentYear'])) $currentYear = mysql_real_escape_string($_REQUEST['currentYear']);
			$currentSemester = 1;
			if (isset($_REQUEST['currentSemester'])) $currentSemester = mysql_real_escape_string($_REQUEST['currentSemester']);
			$registrationNumber = null;
			if (isset($_REQUEST['registrationNumber'])) $registrationNumber = mysql_real_escape_string($_REQUEST['registrationNumber']);
			$batchId = $profile1->getCurrentAccademicYear()->getAccademicYearId();
			if (isset($_REQUEST['batchId'])) $batchId = mysql_real_escape_string($_REQUEST['batchId']);
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
				if (is_null($student1->getCourse()) || ($student1->getCourse()->getCourseId() != $courseId))	{
					$student1->setCourse($courseId); $enableUpdate = true;
				}
				if ($formFourYear != $student1->getFormFourYear())	{
					$student1->setFormFourYear($formFourYear); $enableUpdate = true;
				}
				if ($formFourIndex != $student1->getFormFourIndex())	{
					$student1->setFormFourIndex($formFourIndex); $enableUpdate = true;
				}
				if ($currentYear != $student1->getCurrentYear())	{
					$student1->setCurrentYear($currentYear); $enableUpdate = true;
				}
				if ($currentSemester != $student1->getCurrentSemester())	{
					$student1->setCurrentSemester($currentSemester); $enableUpdate = true;
				}
				if (isset($_REQUEST['registrationNumber']) && ! is_null($registrationNumber) && ($registrationNumber != $student1->getRegistrationNumber()))	{
					$student1->setRegistrationNumber($registrationNumber); $enableUpdate = true;
				}
				if (is_null($student1->getAdmissionBatch()) || ($student1->getAdmissionBatch()->getAccademicYearId() != $batchId))	{
					$student1->setAdmissionBatch($batchId); $enableUpdate = true;
				}
			} else {
				//INSERT WINDOW
				$student1->setCourse($courseId);
				$student1->setFormFourYear($formFourYear);
				$student1->setFormFourIndex($formFourIndex);
				$student1->setCurrentYear($currentYear);
				$student1->setCurrentSemester($currentSemester);
				$student1->setAdmissionBatch($batchId);
				if (isset($_REQUEST['registrationNumber']) && ! is_null($registrationNumber))	{
					$student1->setRegistrationNumber($registrationNumber);
				}
				$student1->setApplicationCounter($pagenumber);
				$enableUpdate = true;
			}
			if ($enableUpdate)	{
				try {
					$student1->commitUpdate();
				} catch (Exception $e)	{
					$promise1->setReason($e->getMessage());
					$promise1->setPromise(false);
				}
			}
		}
		//Step 2: Handling Data for this Page 
		$photo = "../data/students/photo/default.png";
		$trackchange = 0;
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
			$photo = "../data/students/photo/".$login1->getPhoto();
			$trackchange = 1;
		}
		//Beginning UI 
?>
		<div class="mobile-collapse ui-sys-panel-container">
			<div class="ui-sys-panel ui-sys-data-capture ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Photo Upload<br/>App Ref No: <?= $student1->getReferenceNumber() ?></div>
				<div class="ui-sys-panel-body">
					<!--Beginning a photo container-->	
<?php 
	if ($promise1->isPromising())	{
?>
<div class="photocontainer">
	<div class="photodisplay">
		<img id="__id_image_photo_container" alt="PIC" src="<?= $photo ?>"/>
	</div>
	<div class="photodata">
		<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
			<input type="hidden" name="id" value="<?= $student1->getStudentId() ?>"/>
			<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
			<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
			<input type="hidden" name="application" value="<?= $_REQUEST['application'] ?>"/>
			<fieldset>
				<div class="pure-control-group">
					<label for="__id_image_photo_control">Select Photo </label>
					<input  id="__id_image_photo_control" type="file" class="ui-sys-file-upload" name="logo" data-trackchange="<?= $trackchange ?>" accept="image/*"/>
				</div>
				<div class="pure-controls">
					<span id="perror" class="ui-sys-inline-controls-center ui-sys-error-message"></span>
				</div>
				<div class="pure-controls">
					<span> 
					<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
					<input type="submit" value="Upload"/></span>
				</div>
			</fieldset>
		</form>
	</div>
	<div class="ui-sys-clear-both">&nbsp;</div>
</div>
<?php 
	} else {
?>
		<div class="ui-state-error">
			There were problems in updating some of the details<br/>
			Details : <?= $promise1->getReason() ?>
		</div>
		<br/>
		<div class="ui-sys-inline-controls-center"><a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;</div>
<?php
	}
?>
<!--Ending a photo container-->
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
					function showImage(_fileSource, _imageDestination)	{
						var fileSource1 = document.getElementById(_fileSource);
						if (! fileSource1) return;
						var imageDestination1 = document.getElementById(_imageDestination);
						if (! imageDestination1) return;
						var fileReader1 = new FileReader();
						$(fileReader1).on('load', function(event)	{
							imageDestination1.src = this.result;
						});
						$(fileSource1).on('change', function(event)	{
							fileReader1.readAsDataURL(fileSource1.files[0]);
						});
					}
					showImage('__id_image_photo_control', '__id_image_photo_container');
					(function($)	{
						$('#__id_image_photo_control').on('change', function(event)	{
							$('#__id_image_photo_control').data('trackchange', '1');
							showImage('__id_image_photo_control', '__id_image_photo_container');
						});
						$('#form1').on('submit', function(event)	{
							event.preventDefault();
							//Make use of track changes to validate further
							var fileControl1 = document.getElementById('__id_image_photo_control');
							if (! fileControl1) return;
							if (parseInt($(fileControl1).data('trackchange')) == 1)	{
								generalFormSubmission(this, 'form1', 'perror');
							} else {
								$('#perror').html("No Update were done");
							} //end-if-else
						});
					})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if (isset($_REQUEST['application']) && isset($_REQUEST['id'])&& isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 5)	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$student1 = null;
		try {
			$student1 = new Student($database, $_REQUEST['id'], $conn);
			$student1->loadXMLFolder("../data/students");
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		$storedSafePage = intval($student1->getApplicationCounter());
		$enableUpdate = false;
		$promise1 = null;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$dataArray = array();
			$arrayIndex = 0;
			foreach ($_REQUEST['institutionName'] as $index => $val)	{
				//Both share the same Index
				$dataArray[$arrayIndex] = array();
				$dataArray[$arrayIndex]['institutionName'] = $_REQUEST['institutionName'][$index];
				$dataArray[$arrayIndex]['specialization'] = $_REQUEST['specialization'][$index];
				$dataArray[$arrayIndex]['levelId'] = $_REQUEST['levelId'][$index];
				$dataArray[$arrayIndex]['indexNumber'] = $_REQUEST['indexNumber'][$index];
				$dataArray[$arrayIndex]['award'] = $_REQUEST['award'][$index];
				$dataArray[$arrayIndex]['grade'] = $_REQUEST['grade'][$index];
				$dataArray[$arrayIndex]['startYear'] = $_REQUEST['startYear'][$index];
				$dataArray[$arrayIndex]['endYear'] = $_REQUEST['endYear'][$index];
				$arrayIndex++;
			}
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
				//Nothing Here
			} else {
				//INSERT WINDOW
				$student1->setApplicationCounter($pagenumber); $enableUpdate = true;
			}
			$promise1 = $student1->backupXMLFile();
			$promise1 = $student1->updateListOfAccademicInstitutions($dataArray);
			if ($enableUpdate && $promise1->isPromising())	{
				try {
					$student1->commitUpdate();
				} catch (Exception $e)	{
					die($e->getMessage());
				}
			}
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		$courseId = -1;
		$currentYear = "";
		$currentSemester = "";
		$registrationNumber = "";
		$batchId = -1;
		$formFourYear = "";
		$formFourIndex = "";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
			$courseId = $student1->getCourse()->getCourseId();
			$formFourYear = $student1->getFormFourYear();
			$formFourIndex = $student1->getFormFourIndex();
			if ($student1->isContinuingStudent())	{
				$currentYear = $student1->getCurrentYear();
				$currentSemester = $student1->getCurrentSemester();
				$registrationNumber = $student1->getRegistrationNumber();
				$batchId = $student1->getAdmissionBatch()->getAccademicYearId();
			}
		}
		//Beginning UI 
?>
		<div class="mobile-collapse ui-sys-panel-container">
			<div class="ui-sys-panel ui-sys-data-capture ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Course Information<br/>App Ref No: <?= $student1->getReferenceNumber() ?></div>
				<div class="ui-sys-panel-body">
					<div>
<?php 
	if (is_null($promise1) || $promise1->isPromising())	{
?>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="id" value="<?= $student1->getStudentId() ?>"/>
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<input type="hidden" name="application" value="<?= $_REQUEST['application'] ?>"/>
							<input type="hidden" id="dataStore" value="DataStore" data-current-year="<?= $currentYear ?>" data-current-semester="<?= $currentSemester ?>" data-semester-limit="<?= $profile1->getNumberOfSemestersPerYear() ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="courseId">Course you are Joining</label>
									<select id="courseId" name="courseId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select the Course you are Joining to">
											<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Course::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$course1 = new Course($database, $alist1['id'], $conn);
		$selected = "";
		if (! is_null($student1->getCourse()) && ($student1->getCourse()->getCourseId() == $alist1['id'])) $selected="selected";
?>
			<option data-duration="<?= $course1->getDuration() ?>" <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
										</select>
								</div>
								<div class="pure-control-group">
									<label for="formFourYear" title="Year you completed form four/Secondary School">Form Four Completion Year </label>
									<input type="text" name="formFourYear" id="formFourYear" size="8" value="<?= $formFourYear ?>" required pattern="<?= $exprDateYearOnly ?>" validate="true" validate_control="text" validate_expression="<?= $exprDateYearOnly ?>" validate_message="Form Four Completion Year : <?= $msgDateYearOnly ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="formFourIndex" title="Form Four Index Number">Form Four Index Number </label>
									<input type="text" name="formFourIndex" id="formFourIndex" size="32" value="<?= $formFourIndex ?>" required pattern="<?= $exprL32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL32Name ?>" validate_message="Form Four Index Number : <?= $msgL32Name ?>"/>
								</div>
<?php 
	if ($student1->isContinuingStudent())	{
?>
									<div class="pure-control-group">
										<label for="batchId" title="Accademic Year you joined <?= $profile1->getProfileName() ?>">Accademic Year you Joined</label>
										<select id="batchId" name="batchId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select the Accademic you joined">
											<option value="_@32767@_">--select--</option>
<?php 
	$list1 = AccademicYear::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($student1->getAdmissionBatch()) && ($student1->getAdmissionBatch()->getAccademicYearId() == $alist1['id'])) $selected="selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
										</select>
									</div>
									<div class="pure-control-group">
										<label for="currentYear" title="NOTE: Each NTA Level Start with Year 1">Current Year of Study </label>
										<select id="currentYear" name="currentYear" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Year of Study">
											<option value="_@32767@_">--select--</option>
										</select>
									</div>
									<div class="pure-control-group">
										<label for="currentSemester" title="1,2,3, which year are you 1 for first, 2 for second etc">Current Semester of Study </label>
										<select id="currentSemester" name="currentSemester" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Semester of Study">
											<option value="_@32767@_">--select--</option>
<?php 
	$numberOfSemesters = $profile1->getNumberOfSemestersPerYear();
	for ($i = 1; $i <= $numberOfSemesters; $i++)	{
		$selected = "";
		if (intval($currentSemester) == $i) $selected = "selected";
?>
		<option <?= $selected ?> value="<?= $i ?>"><?= $i ?></option>
<?php
	}
?>
										</select>
									</div>
									<div class="pure-control-group">
										<label for="registrationNumber" title="Registration Number">Registration Number </label>
										<input type="text" name="registrationNumber" id="registrationNumber" size="32" value="<?= $registrationNumber ?>" required pattern="<?= $exprRegistrationNumber ?>" validate="true" validate_control="text" validate_expression="<?= $exprRegistrationNumber ?>" validate_message="<?= $msgRegistrationNumber ?>"/>
									</div>
<?php
	}//end-if 
?>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								 <div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
<?php 
	} else {
?>
		<div class="ui-state-error">
			There were Problems with saving Institution List <br/>
			Reason: <?= $promise1->getReason() ?>
		</div><br/>
		<div class="ui-sys-inline-controls-center"><a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;</div>
<?php
	}
?>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	function uiMaitainance($course1, $currentYear1, $currentSemester1, $dataStore1)	{
		if (! $course1.length) return;
		if (! $currentYear1.length) return;
		if (! $currentSemester1.length) return;
		if (! $dataStore1.length) return;
		var option1 = $course1.find('option:selected');
		var $option1 = $(option1);
		if (! $option1.length) return;
		var duration = $option1.attr('data-duration');
		if (! duration) return;
		duration = parseInt(duration);
		var currentYear = $dataStore1.attr('data-current-year');
		if (! currentYear) { currentYear = 0; }
		currentYear = parseInt(currentYear);
		var currentSemester = $dataStore1.attr('data-current-semester');
		if (! currentSemester) { currentSemester = 0; }
		currentSemester = parseInt(currentSemester);
		var numberOfSemesters = $dataStore1.attr('data-semester-limit');
		if (! numberOfSemesters) return;
		numberOfSemesters = parseInt(numberOfSemesters);
		//Now you need to wipe currentYear1 control 
		$currentYear1.empty();
		$('<option/>').attr('value', '_@32767@_')
			.html('--select--')
			.appendTo($currentYear1);
		for (var i = 1; i <= duration; i++)	{
			var $option1 = $('<option/>').attr('value', i)
				.html(i);
			if (i == currentYear)	$option1.prop("selected", true);
			$option1.appendTo($currentYear1);
		}
		//Now you need to wipe currentSemester1 control 
		$currentSemester1.empty();
		$('<option/>').attr('value', '_@32767@_')
			.html('--select--')
			.appendTo($currentSemester1);
		for (var i = 1; i <= numberOfSemesters; i++)	{
			var $option1 = $('<option/>').attr('value', i)
				.html(i);
			if (i == currentSemester) $option1.prop("selected", true);
			$option1.appendTo($currentSemester1);
		}
	}
	uiMaitainance($('#courseId'), $('#currentYear'), $('#currentSemester'), $('#dataStore'));
	$('#courseId').on('change', function(event)	{
		uiMaitainance($(this), $('#currentYear'), $('#currentSemester'), $('#dataStore'));		
	});
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if (isset($_REQUEST['application']) && isset($_REQUEST['id'])&& isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 4)	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$student1 = null;
		$login1 = null;
		$promise1 = null;
		try {
			$student1 = new Student($database, $_REQUEST['id'], $conn);
			$login1 = $student1->getLogin();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		$storedSafePage = intval($student1->getApplicationCounter());
		$enableUpdate = false;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$firstname = mysql_real_escape_string($_REQUEST['firstname']);
			$middlename = mysql_real_escape_string($_REQUEST['middlename']);
			$lastname = mysql_real_escape_string($_REQUEST['lastname']);
			$fullname = mysql_real_escape_string($_REQUEST['fullname']);
			$dob = mysql_real_escape_string($_REQUEST['dob']);
			$dob = DateAndTime::convertFromGUIDateFormatToSystemDateAndTimeFormat($dob);
			$sexId = mysql_real_escape_string($_REQUEST['sexId']);
			$maritalId = mysql_real_escape_string($_REQUEST['maritalId']);
			$disability = mysql_real_escape_string($_REQUEST['disability']);
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
				if ($firstname != $login1->getFirstname())	{
					$login1->setFirstname($firstname); $enableUpdate = true;
				}
				if ($middlename != $login1->getMiddlename()) {
					$login1->setMiddlename($middlename); $enableUpdate = true;
				}
				if ($lastname != $login1->getLastname())	{
					$login1->setLastname($lastname); $enableUpdate = true;
				}
				if ($fullname != $login1->getFullname()) {
					$login1->setFullname($fullname); $enableUpdate = true;
				}
				if (is_null($login1->getDOB()) || ($dob != $login1->getDOB()->getDateAndTimeString()))	{
					$login1->setDOB($dob); $enableUpdate = true;
				}
				if (is_null($login1->getSex()) || ($sexId != $login1->getSex()->getSexId()))	{
					$login1->setSex($sexId); $enableUpdate = true;
				}
				if (is_null($login1->getMarital()) || ($maritalId != $login1->getMarital()->getMaritalId()))	{
					$login1->setMarital($maritalId); $enableUpdate = true;
				}
				if (! ((trim($disability) == "") || ($disability == $student1->getDisability())))	{
					$student1->setDisability($disability); $enableUpdate = true;
				}
			} else {
				//INSERT WINDOW
				$login1->setFirstname($firstname);
				$login1->setMiddlename($middlename);
				$login1->setLastname($lastname);
				$login1->setFullname($fullname);
				$login1->setDOB($dob);
				$login1->setSex($sexId);
				$login1->setMarital($maritalId);
				if (! (trim($disability) == ""))	$student1->setDisability($disability);
				$student1->setApplicationCounter($pagenumber);
				$student1->setXMLFile("student_".$student1->getStudentId().".xml");
				$student1->loadXMLFolder("../data/students");
				$enableUpdate = true;
				//We need to Initialize the filesystem at this point 
				$promise1 = new Promise();
				$promise1->setPromise(true);
				$studentFilename = $studentFolder."/".$student1->getXMLFile();
				$doc=new DOMDocument(Object::$xmlVersion);
				$doc->formatOutput = true;
				//code here 
$student=$doc->createElement('student');
$studentId=$doc->createElement('studentId');
$studentId->appendChild($doc->createTextNode($student1->getStudentId()));
$student->appendChild($studentId);;
$studentName=$doc->createElement('studentName');
$studentName->appendChild($doc->createTextNode($student1->getLogin()->getFullname()));
$student->appendChild($studentName);;
$accademicHistory=$doc->createElement('accademicHistory');
$student->appendChild($accademicHistory);
$employmentHistory=$doc->createElement('employmentHistory');
$student->appendChild($employmentHistory);
$financialSponsor=$doc->createElement('financialSponsor');
$student->appendChild($financialSponsor);
$nextOfKins=$doc->createElement('nextOfKins');
$student->appendChild($nextOfKins);
$bankAccount=$doc->createElement('banks');
$student->appendChild($bankAccount);
$subjects=$doc->createElement('subjects');
$student->appendChild($subjects);
$results=$doc->createElement('results');
$student->appendChild($results);
$doc->appendChild($student);
				//code end here
				if (! $doc->save($studentFilename)) {
					$enableUpdate =  false;
					$promise1->setPromise(false);
					$promise1->setReason("The Server firewall might not allow you to create a file");
				}
				//End of filesystem Initiliazation
			}
			if ($enableUpdate)	{
				try {
					$login1->commitUpdate();
					$student1->commitUpdate();
				} catch (Exception $e)	{ die($e->getMessage()); }
			}
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
		}
		//Beginning UI 
?>
		<div class="mobile-collapse ui-sys-panel-container">
			<div class="ui-sys-panel ui-sys-data-capture ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Academic History<br />App Ref No: <?= $student1->getReferenceNumber() ?></div>
				<div class="ui-sys-panel-body">
					<div>
<?php 
	if (is_null($promise1) || ($promise1->isPromising()))	{
?>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="id" value="<?= $student1->getStudentId() ?>"/>
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<input type="hidden" name="application" value="<?= $_REQUEST['application'] ?>"/>
							<div class="ui-sys-field-block-container">
<!--Begin of the Container-->
<?php 
$student1->loadXMLFolder("../data/students");
//Preparing Page 
$displayList1 = new DisplayList();
$intent1 = new Intent('Zoomtong');
$intent1->putExtra('namePrefix', 'institutionName'); $intent1->putExtra('labelCaption', 'Name of Institution');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprA64Name");
$intent1->putExtra('validateMessage', "Institution Name $msgA64Name"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'institutionName');
$displayList1->add($intent1);
$intent1 = new Intent('Zoomtong');
$intent1->putExtra('namePrefix', 'specialization'); $intent1->putExtra('labelCaption', 'Specialization');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprA64Name");
$intent1->putExtra('validateMessage', "Specialization $msgA64Name"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'specialization');
$displayList1->add($intent1);
$intent1 = new Intent('Zoomtong');
$intent1->putExtra('namePrefix', 'levelId'); $intent1->putExtra('labelCaption', 'Level Of Education');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "select"); $intent1->putExtra('validateExpression', "select");
$intent1->putExtra('validateMessage', "Select Education Level"); $intent1->putExtra('className', 'EducationLevel');
$intent1->putExtra('tagName', 'levelId');
$displayList1->add($intent1);
$intent1 = new Intent('Zoomtong');
$intent1->putExtra('namePrefix', 'indexNumber'); $intent1->putExtra('labelCaption', 'Index Number/Certificate Number');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprL32Name");
$intent1->putExtra('validateMessage', "Index Number $msgL32Name"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'indexNumber');
$displayList1->add($intent1);
$intent1 = new Intent('Zoomtong');
$intent1->putExtra('namePrefix', 'award'); $intent1->putExtra('labelCaption', 'Award');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprA64Name");
$intent1->putExtra('validateMessage', "Award $msgA64Name"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'award');
$displayList1->add($intent1);
$intent1 = new Intent('Zoomtong');
$intent1->putExtra('namePrefix', 'grade'); $intent1->putExtra('labelCaption', 'Grades Earned');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprL64Name");
$intent1->putExtra('validateMessage', "Grades Earned $msgL64Name"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'grade');
$displayList1->add($intent1);
$intent1 = new Intent('Zoomtong');
$intent1->putExtra('namePrefix', 'startYear'); $intent1->putExtra('labelCaption', 'From (Year)');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprDateYearOnly");
$intent1->putExtra('validateMessage', "Year From $msgDateYearOnly"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'startYear');
$displayList1->add($intent1);
$intent1 = new Intent('Zoomtong');
$intent1->putExtra('namePrefix', 'endYear'); $intent1->putExtra('labelCaption', 'To (Year)');
$intent1->putExtra('validate', 'true'); $intent1->putExtra('validateControl', "text"); $intent1->putExtra('validateExpression', "$exprDateYearOnly");
$intent1->putExtra('validateMessage', "Year To $msgDateYearOnly"); $intent1->putExtra('className', 'None');
$intent1->putExtra('tagName', 'endYear');
$displayList1->add($intent1);
//Fetch Previous Content 
$instutionList1 = $student1->getListOfAccademicInstitutions();
$blockToDisplay = UIServices::displayUIFieldBlocks($database, $conn, $instutionList1, $displayList1, 0);
echo $blockToDisplay;
?>
<div class="ui-sys-field-block-add"><a title="Add Another Institution you passed through" class="button-link">Add</a></div>
<!--End of the Container-->
							</div>
							<fieldset>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								<div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
<?php 
	} else {
?>
		<div class="ui-state-error">
			Perhaps you are lacking permission to write data on the server filesystem <br />
			Reason : <?= $promise1->getReason() ?>
		</div><br/>
		<div class="ui-sys-inline-controls-center"><a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>&application=<?= $_REQUEST['application'] ?>&id=<?= $student1->getStudentId() ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;</div>
<?php	
	} //end-if-else-de-promise
?>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if (isset($_REQUEST['application']) && isset($_REQUEST['id'])&& isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 3)	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$student1 = null;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		try {
			$student1 = new Student($database, $_REQUEST['id'], $conn);
			//$profile1->loadXMLFolder("../data/profile");
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		$storedSafePage = intval($student1->getApplicationCounter());
		$enableUpdate = false;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
				//This is the first page it does not apply
			} else {
				//INSERT WINDOW
				$student1->setCompleted("0");
				//Set if continuing or not kd788
				$student1->setContinuingStudent("0");
				if ($_REQUEST['application'] == "continuing")	$student1->setContinuingStudent("1");
				$student1->setApplicationCounter($pagenumber); 
				$enableUpdate = true;
			}
			if ($enableUpdate)	{
				try {
					$student1->commitUpdate();
				} catch (Exception $e)	{ 
					$promise1->setReason($e->getMessage());
					$promise1->setPromise(false);
				}
			}
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		$firstname = "";
		$middlename = "";
		$lastname = "";
		$fullname = "";
		$dob = "";
		$sexId = -1;
		$maritalId = -1;
		$disability = "";
		$login1 = null;
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
			$login1 = $student1->getLogin();
			$firstname = $login1->getFirstname();
			$middlename = $login1->getMiddlename();
			$lastname = $login1->getLastname();
			$fullname = $login1->getFullname();
			$dob = DateAndTime::convertFromDateTimeObjectToGUIDateFormat($login1->getDOB());
			$sexId = $login1->getSex()->getSexId();
			$maritalId = $login1->getMarital()->getMaritalId();
			$disability = $student1->getDisability();
		}
		//Beginning UI 
?>
		<div class="mobile-collapse ui-sys-panel-container">
			<div class="ui-sys-panel ui-sys-data-capture ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Student Bio Data<br />App Ref No: <b><?= $student1->getReferenceNumber() ?></b></div>
				<div class="ui-sys-panel-body">
					<div>
<?php 
	if ($promise1->isPromising())	{
?>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="id" value="<?= $student1->getStudentId() ?>"/>
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<input type="hidden" name="application" value="<?= $_REQUEST['application'] ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="firstname">Firstname </label>
									<input type="text" name="firstname" id="firstname" value="<?= $firstname ?>" size="32" required pattern="<?= $exprA32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Firstname : <?= $msgA32Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="middlename">Middlename </label>
									<input type="text" name="middlename" id="middlename" value="<?= $middlename ?>" size="32" required pattern="<?= $exprA32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Middlename : <?= $msgA32Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="lastname">Lastname </label>
									<input type="text" name="lastname" id="lastname" value="<?= $lastname ?>" size="32" required pattern="<?= $exprA32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Lastname : <?= $msgA32Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="fullname" title="Your Name as it appears on your Academic Certificates">Academic Name </label>
									<input type="text" name="fullname" id="fullname" size="32" value="<?= $fullname ?>" required pattern="<?= $exprA64Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA64Name ?>" validate_message="Accademic Name : <?= $msgA64Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="dob">Date of Birth </label>
									<input class="datepicker" type="text" name="dob" id="dob" value="<?= $dob ?>" size="32" required pattern="<?= $exprDate ?>" validate="true" validate_control="text" validate_expression="<?= $exprDate ?>" validate_message="Date: <?= $msgDate ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="sexId">Sex </label>
									<select id="sexId" name="sexId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Sex">
										<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Sex::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($login1) && ! is_null($login1->getSex()) && ($alist1['id'] == $login1->getSex()->getSexId())) $selected="selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
									</select>
								</div>
								<div class="pure-control-group">
									<label for="maritalId">Marital Status</label>
									<select id="maritalId" name="maritalId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Marital">
										<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Marital::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($login1) && ! is_null($login1->getMarital()) && ($alist1['id'] == $login1->getMarital()->getMaritalId())) $selected="selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
									</select>
								</div>
								<div class="pure-control-group">
									<label for="disability">Disability </label>
									<input type="text" name="disability" id="disability" value="<?= $disability ?>" size="32" notrequired="true" pattern="<?= $exprL32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL32Name ?>" validate_message="Disability : <?= $msgL32Name ?>"/>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								<div class="pure-controls">
									<span class="ui-sys-inline-controls-right">
										<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
<?php 
	} else {
?>
		<div class="ui-state-error">
			There were problems in updating some of your details<br/>
			Details: <?= $promise1->getReason() ?>
		</div>
<?php
	} 
?>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if (isset($_REQUEST['application']) && isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 2)	{
		$storedSafePage = intval($profile1->getApplicationCounter());
		$enableUpdate = false;
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$profile1 = null;
		try {
			$profile1 = new Profile($database, $__profileId, $conn);
			$profile1->loadXMLFolder("../data/profile");
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		//Step 1: Handling Data From the Previous Page 
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$studentId = -1;
		$code = System::getCodeString(16);
		$code = $code.$systemDate1->getDateAndTimeString();
		$code = md5($code);
		try {
			$studentId = Student::initializeStudentRecord($database, $conn, $code);
		} catch (Exception $e)	{ 
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
		}
		$studentCode = "";
		$student1 = null;
		$login1 = null;
		if ($promise1->isPromising())	{
			$studentCode = System::getCodeString(4);
			try {
				$student1 = new Student($database, $studentId, $conn);
				$student1->setApplicationCounter($pagenumber);
				$student1->setReferenceNumber($studentCode);
				$login1 = $student1->getLogin();
				$login1->setExtraFilter($studentCode);
				$login1->commitUpdate();
				$student1->commitUpdate();
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
			}
		}
		//Beginning UI 
?>
		<div class="mobile-collapse ui-sys-panel-container">
			<div class="ui-sys-panel ui-sys-data-capture ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Application Reference Number</div>
				<div class="ui-sys-panel-body">
					<div>
<?php 
	if ($promise1->isPromising())	{
?>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="id" value="<?= $student1->getStudentId() ?>"/>
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<input type="hidden" name="application" value="<?= $_REQUEST['application'] ?>"/>
							<fieldset>
								<div class="pure-control-group">
										Kindly note down your Application Code <br />
										The code below will be used on the future to track your application <br/>
										As long as you have the code below you can pause the Application any time as you wish <br/>
										and proceed anytime you wish
								</div>
								<div class="pure-control-group">
										Your Application Code is : <b><?= $student1->getReferenceNumber() ?></b>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								<div class="pure-controls">
									<span>
										<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
<?php 
	} else {
?>
		<div class="ui-state-error">
			There were problems in Initializing your record in the system <br/>
			Error Details : <i><?= $promise1->getReason() ?></i>
		</div>
<?php	
	}
?>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if (isset($_REQUEST['application']) && isset($prevpagenumber) && isset($pagenumber) && ($pagenumber == 1))	{
?>
		<div class="mobile-collapse">
			<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Student Registration</div>
				<div class="ui-sys-panel-body ui-sys-center-text">
					<div>
						<a title="This is the First time you are doing this Online Registration" class="button-link" href="<?= $thispage ?>?pagenumber=<?= $pagenumber + 1 ?>&prevpagenumber=<?= $pagenumber ?>&application=<?= $_REQUEST['application'] ?>">New Application</a>&nbsp;&nbsp;&nbsp;&nbsp;<a title="This is an Incomplete Registration which was stopped somewhere in between" href="<?= $thispage ?>?pagenumber=1001&prevpagenumber=9887&application=<?= $_REQUEST['application'] ?>" class="button-link">Pending Application</a>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both">&nbsp;</div>
<?php	
	} else {
?>
		<div class="mobile-collapse">
			<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Student Registration</div>
				<div class="ui-sys-panel-body ui-sys-center-text">
					<div>
						<a title="For a New student who is joining at <?= $profile1->getProfileName() ?>" class="button-link" href="<?= $thispage ?>?pagenumber=1&prevpagenumber=0&application=new">New Student Registration</a>&nbsp;&nbsp;&nbsp;&nbsp;<a title="For Existing Student at <?= $profile1->getProfileName() ?>" href="<?= $thispage ?>?pagenumber=1&prevpagenumber=0&application=continuing" class="button-link">Continuing Student Registration</a>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both">&nbsp;</div>
<?php
	}
?>
</div>
<!--ENDING OF PUTTING CODE-->
	<div class="ui-sys-footer mobile-collapse">
<?php   
	//You must have a DateAndTime Object carrying date, we are interested with only year 
	//So any default_timezone is okay with us at this point 
	include("../template/footer.php");
?>
	</div>
</div>
</body>
</html>
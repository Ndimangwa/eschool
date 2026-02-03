<?php 
if (session_status() == PHP_SESSION_NONE)	{
	session_start();
}
if (! isset($_SESSION['login'][0]['id']))	{
	header("Location: ../");
	exit();
}
$config="../config.php";
include($config);
require_once("../common/validation.php");
require_once("../class/system.php");
require_once("../server/authorization.php");
require_once("../server/accounting.php");
$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
$profile1 = null;
$login1 = null;
$registeredUser1 = null;
$__profileId = Profile::getProfileReference($database, $conn);
try {
	$profile1 = new Profile($database, $__profileId , $conn);
	$login1 = new Login($database, $_SESSION['login'][0]['id'], $conn);
	$profile1->loadXMLFolder("../data/profile");
	$registeredUser1 = Login::getUserWithThisLogin($database, $conn, $login1);
	if (is_null($registeredUser1)) Object::shootException("Could not pull this user type");
} catch (Exception $e)	{
	//Clear Session 
	$_SESSION = array();
	session_destroy();
	$logoutLink = "<a class=\"ui-sys-logout-control bypass-data-error-control\" data-error-control=\"__ui_common_errors\" data-next-page=\"../\" data-server-directory=\"../server\" title=\"Logout\" href=\"../\">Proceed to Logout</a>";
	echo "<br/>User Pulling failed ".$e->getMessage();
	die("<br/>$logoutLink");
}
mysql_close($conn);
//Will redirect Automatically If Necessary
System::systemSSLTLSCertificateVerification($profile1);
$__login_extra_filter = $login1->getExtraFilter();
if (! $profile1->isInstallationComplete())	{
	header("Location: ../installation/");
	exit();
}
$thispage = $_SERVER['PHP_SELF'];
$page = null;
if (isset($_REQUEST['page'])) $page = $_REQUEST['page'];
//Awaiting for my Approval
$__request_awaiting_my_approval_searchtext = "________!____@atr_____j_____i____";
if (($page == "request_awaiting_my_approval") && isset($_REQUEST['searchtext'])) $__request_awaiting_my_approval_searchtext = $_REQUEST['searchtext'];
//You may use extraFilter to store path of the photo , user the setExtraFilter of login
$__user_data_folder = "../data/unknown/";
$__user_photo_prefix = "default";
if ($login1->getUserType()->getTypeCode() == UserType::$__COURSEINSTRUCTOR)	{
	$login1->setExtraFilter("../data/instructors/photo/".$login1->getPhoto());
	$__user_data_folder = "../data/instructors/";
	$__user_photo_prefix = "instructor";
} else if ($login1->getUserType()->getTypeCode() == UserType::$__STUDENT)	{
	$login1->setExtraFilter("../data/students/photo/".$login1->getPhoto());
	$__user_data_folder = "../data/students/";
	$__user_photo_prefix = "student";
} else if ($login1->getUserType()->getTypeCode() == UserType::$__USER)	{
	$login1->setExtraFilter("../data/users/photo/".$login1->getPhoto());
	$__user_data_folder = "../data/users/";
	$__user_photo_prefix = "user";
} else {
	die("The System Could not identify this Type of the User");
}
$dataFolder = "data";
$studentFolder = "../data/students";
$themeFolder = "sunny"; //--set this as a default 
if (! is_null($profile1->getTheme())) $themeFolder = $profile1->getTheme()->getThemeFolder();
if (! is_null($login1->getTheme())) $themeFolder = $login1->getTheme()->getThemeFolder();
//We Just need to get A Default Timezone so we can extra a year
$timezone="Africa/Dar_es_Salaam";
if (! is_null($profile1->getPHPTimezone())) $timezone = $profile1->getPHPTimezone()->getZoneName();
date_default_timezone_set($timezone);
$date=date("Y:m:d:H:i:s");
$__systemDayOffset = 0;
if (! is_null($profile1->getFirstDayOfAWeek()))	{
	$__systemDayOffset = $profile1->getFirstDayOfAWeek()->getOffsetValue();
}
if (! is_null($login1->getFirstDayOfAWeek()))	{
	$__systemDayOffset = $login1->getFirstDayOfAWeek()->getOffsetValue();
}
$systemDate1 = new DateAndTime("Ndimangwa", $date, "Fadhili");
$systemTime1 = $systemDate1;
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
<link rel="stylesheet" type="text/css" media="all" href="../client/plugin/twbsPagination/twbsPagination.min.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/plugin/printArea/PrintArea.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/plugin/navgoco/jquery.navgoco.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/css/purecss/pure-min.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/plugin/chromose/chromoselector-2.1.8/chromoselector.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/css/site.css"/>
<style type="text/css">

</style>
<script type="text/javascript" src="../client/jquery.js"></script>
<script type="text/javascript" src="../client/jquery-ui-1.11.3/jquery-ui.js"></script>
<script type="text/javascript" src="../client/jquery-easy-ticker-master/jquery.easy-ticker.js"></script>
<script type="text/javascript" src="../client/plugin/twbsPagination/jquery.twbsPagination.min.js"></script>
<script type="text/javascript" src="../client/plugin/printArea/jquery.PrintArea.js"></script>
<script type="text/javascript" src="../client/plugin/navgoco/jquery.navgoco.js"></script>
<!-- <script type="text/javascript" src="../client/plugin/chromoselector-2.1.8/chromoselector.min.js"></script> -->
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
			yearRange:'1932:2099'
		});
		//Begin Testing 
		$('ul.ui-sys-profile-menu').css({
			zIndex: 100
		}).menu();
		//--End Testing
	});
})(jQuery);
</script>
</head>
<body class="ui-sys-body">
<div id="__id_general_dialog_holder">
<!--Holding Popup dialogs, they shoud use absolute positioning -->
</div>
<div class="ui-sys-main">
<?php 
	if (! is_null($profile1->getSystemName()))	{
?>
	<div class="ui-sys-front-systemname-1 mobile-collapse"><?= $profile1->getSystemName() ?></div>
<?php 
	}
?>
	<div class="ui-sys-front-topbutton-1 mobile-collapse">
		<span class="ui-sys-server-date"><?= date("l, jS F Y") ?>
<?php 
	if (! is_null($login1->getLastLoginTime()))	{
?>
		<br/>Last Login Time: <?= $login1->getLastLoginTime()->getDateAndTimeString() ?>
<?php
	}
?>
		</span>
		<span class="ui-sys-title-text-block">
			<span class="ui-sys-logged-in-text">You are Logged In As : <?= $login1->getLoginName() ?></span><br/>
			<span class="ui-sys-accademic-year">(Current Accademic Year : <?= $profile1->getCurrentAccademicYear()->getAccademicYear() ?>; Current Semester : <?= $profile1->getCurrentSemester() ?>)</span>
		</span>
		<a class="ui-sys-logout-control ui-sys-float-right button-link" data-error-control="__ui_common_errors" data-next-page="../" data-server-directory="../server" title="Logout, <?= $login1->getFullname() ?>" href="#">Logout</a>
	</div>

<!--BEGINNING OF PUTTING CODE-->
<div class="ui-sys-bg-grid-green">
	<div class="ui-sys-profile-left-panel">
		<div class="module-summary">
<!--Module Summary Should BEgin after this line-->
<div class="module-title profile-photo">
	<img title="Profile for <?= $login1->getFullname() ?>" alt="PC" src="<?= $login1->getExtraFilter() ?>"/>
</div>
<div class="module-title">
	<ul class="ui-sys-profile-menu">
		<li><span class="ui-icon ui-icon-home"></span><a href="./">Home</a></li>
		<li><span class="ui-icon ui-icon-gear"></span><a href="../system/">My System</a></li>
		<li><span class="ui-icon ui-icon-note"></span><a href="#">Messaging</a>
			<ul>
				<li><a href="<?= $thispage ?>?page=sendmail">Send Email</a></li>
				<li><a href="<?= $thispage ?>?page=sendsms">Send SMS</a></li>
				<li><a>Local Messages</a>
					<ul>
						<li><a href="#">Inbox</a></li>
						<li><a href="#">Sent Items</a></li>
					</ul>
				</li>
			</ul>
		</li>
		<li><span class="ui-icon ui-icon-suitcase"></span><a href="#">ID Card</a></li>
		<li><span class="ui-icon ui-icon-calendar"></span><a href="<?= $thispage ?>?page=timetable">Timetable</a></li>
		<li><span class="ui-icon ui-icon-person"></span><a href="#">My Profile</a>
			<ul>
				<li><a href="<?= $thispage ?>?page=view_my_profile">View My Profile</a></li>
				<li><a href="<?= $thispage ?>?page=edit_my_profile">Edit My Profile</a></li>
			</ul>
		</li>
		<li><span class="ui-icon ui-icon-person"></span><a href="<?= $thispage ?>?page=personalize">Personalize</a></li>
	</ul>
</div>
<!--Module Summary Should not Exceed this point-->		
		</div>
	</div>
	<div class="ui-sys-profile-right-panel">
		<div class="module-container">
<!--Begin All Module Should be loaded at this point-->
<?php 
//Restoring Original Value of extra Filter 
$login1->setExtraFilter($__login_extra_filter);
	if ($login1->isUsingDefaultPassword())	{
		/*DEFAULT PASSWORD STARTS HERE*/
		if ($page == "undo_default_password" && isset($_REQUEST['id']))	{
			$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
			$password = sha1($_REQUEST['passwd']);
			$promise1 = new Promise();
			$promise1->setPromise(true);
			$enableUpdate = false;
			$ulogin1 = null;
			try {
				$ulogin1 = new Login($database, $_REQUEST['id'], $conn);
				$ulogin1->setUsingDefaultPassword("0");
				$ulogin1->setPassword($password);
				$ulogin1->commitUpdate();
				$enableUpdate = true;
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
			}
?>
			<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Password Updated Report</div>
				<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Dear, <i><?= $ulogin1->getFullname() ?></i>, your password has been updated successful, kindly keep your password secretly<br/>
			<a href="<?= $thispage ?>">Click Here</a> to proceed to your profile
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in updating the password <br/>
			Details: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>				
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
<?php
			mysql_close($conn);
		} else {
?>
			<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Default Password Changer Tool</div>
				<div class="ui-sys-panel-body">
					<div>
						You are using a default Password.<br/>
						Kindly setup a new password and make sure you do not forget it <br/>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
							<input type="hidden" name="page" value="undo_default_password"/>
							<input type="hidden" name="id" value="<?= $login1->getLoginId() ?>"/>
							<div class="pure-control-group">
								<label for="passwd">Password</label>
								<input type="password" size="48" id="passwd" name="passwd" required pattern="<?= $exprPassword ?>" validate="true" validate_control="text" validate_expression="<?= $exprPassword ?>" validate_message="Password: <?= $msgPassword ?>"/>
							</div>
							<div class="pure-control-group">
								<label for="cpasswd">Confirm Password</label>
								<input type="password" size="48" id="cpasswd"/>
							</div>
							<div id="perror" class="pure-controls ui-sys-error-message"></div>
						</form>
					</div>
				</div>
				<div class="ui-sys-panel-footer"><input type="button" id="cmdSubmit" value="Submit"/></div>
			</div>
<script type="text/javascript">
(function($)	{
	$('#cmdSubmit').on('click', function(event)	{
		event.preventDefault();
		var $target1 = $('#perror');
		if (! $target1.length) return;
		$target1.empty();
		var $passwd1 = $('#passwd');
		if (! $passwd1.length) return;
		var $cpasswd1 = $('#cpasswd');
		if (! $cpasswd1.length) return;
		var form1 = document.getElementById('form1');
		if (! form1)	{
			$('<span/>').html("Form Reference Could not be found")
				.appendTo($target1);
			return false;
		}
		//form1.action = location.href.replace(/^http:/, 'https');
		if ($passwd1.val() == $cpasswd1.val())	{
			generalFormSubmission(this, 'form1', 'perror');
		} else {
			$('<span/>').html("Passwords are not matching")
				.appendTo($target1);
			return;
		}
	});
})(jQuery);
</script>
<?php
		}
		/*DEFAULT PASSWORD ENDS HERE*/
	} else {
/*LIFE BEGIN Potential Business for this page will start here*/
	/*BEGIN General Attendance Begins Here*/if ($page == "manageattendance" && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && isset($_REQUEST['upload']) && isset($_REQUEST['id']))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$timetable1 = null;
		$filename = "";
		try {
			$timetable1 = new Timetable($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Upload Attendance <br/>(<?= $timetable1->getCaptionName() ?>)</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
				try {
					if (trim($_REQUEST['rndx']) != $timetable1->getExtraFilter()) Object::shootException("The System has detected you are replaying your Window Browser");
					$promise1 = Object::checkUploadedFile($_FILES['attendancefile'], array("text/csv"), array("csv"), 2097153);
					//getResults contains the absolute path of the temporary attendance
					$attendanceTemplateFile1 = new AttendanceTemplateFile($database, $promise1->getResults(), $conn);
					if (is_null($attendanceTemplateFile1->getHeader('timetableId')) || ($attendanceTemplateFile1->getHeader('timetableId') != $timetable1->getTimetableId())) Object::shootException("You are Uploading Attendance to the wrong timetable");
					//Get Real Attendance File 
					$attendanceFile1 = AttendanceFile::getAttendanceFileFromAttendanceTemplate($database, $conn, "../data/attendance/", $attendanceTemplateFile1, $systemTime1);
					if (is_null($attendanceFile1)) Object::shootException("The Logical Attendance File Could not be created");
					//Entering saving mode
					$attendanceFile1->save();
					$timetable1->setExtraFilter(System::getCodeString(8));
					$timetable1->commitUpdate();
				} catch (Exception $e)	{
					$promise1->setReason($e->getMessage());
					$promise1->setPromise(false);
				}
				if ($promise1->isPromising())	{
?>
					<div class="ui-state-highlight">
						Congratulations; The System has recorded the Attendance Successful
					</div>
<?php
				} else {
?>
					<div class="ui-state-error">
						There were problems in recording the Attendance <br/>
						Details : <?= $promise1->getReason() ?>
					</div>
<?php
				}
?>			
			</div>
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>?page=manageattendance&id=<?= $timetable1->getTimetableId() ?>">Go Back to General Attendance Page</a></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "manageattendance" && isset($_REQUEST['upload']) && isset($_REQUEST['id']))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$timetable1 = null;
		try {
			$timetable1 = new Timetable($database, $_REQUEST['id'], $conn);
			$timetable1->setExtraFilter(System::getCodeString(8));
			$timetable1->commitUpdate();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Upload Attendance <br/>(<?= $timetable1->getCaptionName() ?>)</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form id="form1" method="POST" action="<?= $thispage ?>" enctype="multipart/form-data">
					<input type="hidden" name="page" value="manageattendance"/>
					<input type="hidden" name="id" value="<?= $timetable1->getTimetableId() ?>"/>
					<input type="hidden" name="upload" value="<?= $_REQUEST['upload'] ?>"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="rndx" value="<?= $timetable1->getExtraFilter() ?>"/>
					<div class="ui-sys-data-capture ui-sys-inline-controls-center">
						<input class="ui-sys-file-upload" id="resultsfile" data-capture="0" type="file" name="attendancefile" accept="*.csv"/> &nbsp;&nbsp;<input type="button" id="__add_record" value="Upload Attendance"/> 
					</div>
					<div class="ui-sys-error-message" id="perror"></div>
				</form>
			</div>
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>?page=manageattendance&id=<?= $timetable1->getTimetableId() ?>">Go Back to General Attendance Page</a></div>
		</div>
<script type="text/javascript">
(function($)	{
	$('#resultsfile').on('change', function(event)	{ 
		event.preventDefault();
		var $file1 = $(this);
		$file1.prop('data-capture', '1');
	});
	$('#__add_record').on('click', function(event)	{
		event.preventDefault();
		var $file1 = $('#resultsfile');
		if (! $file1.length) return;
		if (parseInt($file1.prop('data-capture')) == 1)	{
			generalFormSubmission(this, 'form1', 'perror');
		} else {
			var $target1 = $('#perror');
			if (! $target1.length) return;
			$target1.empty();
			$('<span/>').html('You have not selected the file to upload')
				.appendTo($target1);
		}
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if ($page == "manageattendance" && isset($_REQUEST['report']) && isset($_REQUEST['template']) && isset($_REQUEST['id']))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$timetable1 = null;
		try {
			$timetable1 = new Timetable($database, $_REQUEST['id'], $conn); 
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		$filename = $_REQUEST['report'];
		try {
			if (! file_exists($filename)) Object::shootException("The Resource File Had Already being removed from the Server");
			if (! unlink($filename)) Object::shootException("Could not Release the Temporary File");
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Attendance [Template] for <?= $timetable1->getCaptionName() ?></div>
			<div class="ui-sys-panel-body ui-sys-data-capture ui-sys-inline-controls-center">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			The System has successful released the  Server Resources
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			The System could not release temporary Files <br/>
			Details : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>	
			</div>
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>?page=manageattendance&id=<?= $timetable1->getTimetableId() ?>">Go Back to Attendance Manager</a></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "manageattendance" && isset($_REQUEST['template']) && isset($_REQUEST['id']))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$timetable1 = null;
		try {
			$timetable1 = new Timetable($database, $_REQUEST['id'], $conn); 
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Attendance [Template] for <?= $timetable1->getCaptionName() ?></div>
			<div class="ui-sys-panel-body ui-sys-data-capture ui-sys-inline-controls-center">
<?php 
	$filename = "../temp/".System::getCodeString(12).".csv";
	try {
		$promise1 = AttendanceTemplateFile::getAttendanceTemplate($database, $conn, $timetable1->getTimetableId(), $login1->getLoginId(), $filename, $systemTime1);
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
	if ($promise1->isPromising())	{
?>
		<div>
			Your file is Ready for downloading<br/>
			<b style="font-size: 1.1em;"><a href="<?= $filename ?>">Click Here</a></b> to download your file<br/><br/>
			Once the download is complete, kindly click the proceed button 
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in getting the Attendance Template File <br/>
			Details : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer">
<?php 
	if ($promise1->isPromising())	{
?>
		<a class="button-link" href="<?= $thispage ?>?page=manageattendance&report=<?= $filename ?>&template=io&id=<?= $timetable1->getTimetableId() ?>">Proceed</a>
<?php
	}
?>			
			</div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "manageattendance" && isset($_REQUEST['id']))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$timetable1 = null;
		try {
			$timetable1 = new Timetable($database, $_REQUEST['id'], $conn); 
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Attendance for <?= $timetable1->getCaptionName() ?></div>
			<div class="ui-sys-panel-body ui-sys-data-capture ui-sys-inline-controls-center">
				<a class="button-link" href="<?= $thispage ?>?page=manageattendance&template=true&id=<?= $timetable1->getTimetableId() ?>">Download Template</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a class="button-link" href="<?= $thispage ?>?page=manageattendance&upload=true&id=<?= $timetable1->getTimetableId() ?>">Upload Attendance</a>
			</div>
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>?page=timetable">Go Back to Timetable</a></div>
		</div>
<?php
		mysql_close($conn);
	}/*END General Attendance Ends Here*/ else /*BEGIN Advance Semester or Year, Self Init Page*/if ($login1->isStudent() && isset($_REQUEST['report']) && ! $registeredUser1->isDiscontinued() && $registeredUser1->shouldIAdvanceYearOrSemester($profile1) && $login1->isANewSubmission() && ApprovalSequenceData::isApprovalComplete($config, $login1->getLoginId(), ApprovalSequenceSchema::$__ADVANCE_YEAR_OR_SEMESTER, $exprA108Name, $msgA108Name, "../server/createnewapprovalsequence.php", null))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		//Reconstruct data Structure 
		$ds1 = array();
		$ds1['actionCode'] = $_REQUEST['actionCode'];
		$ds1['nextAccademicYear'] = $_REQUEST['nextAccademicYear'];
		$ds1['nextYearOfStudy'] = $_REQUEST['nextYearOfStudy'];
		$ds1['nextSemesterOfStudy'] = $_REQUEST['nextSemesterOfStudy'];
		$customMessage = "";
		$student1 = null;
		$ulogin1 = null;
		try {
			$student1 = new Student($database, $registeredUser1->getStudentId(), $conn);
			$ulogin1 = $student1->getLogin();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		switch ($ds1['actionCode'])	{
			case Student::$__CAN_GRADUATE:
				//Generate Transcript 
				//Graduation Business should be done at this point 
				$promise1 = Student::graduateStudent($database, $conn, $student1->getStudentId(), $profile1, $systemTime1, "../data/results/", "../data/transcripts/");
				$customMessage = "You have made it, you have graduated";
				break;
			case Student::$__HAS_FAILED:
				$customMessage = "You have moved to a new Accademic Year, We strongly remind you to work hard and clear pending subjects";
				break;
			case Student::$__HAS_DISQUALIFIED:
				$customMessage = "You have been discontinued from ".$profile1->getProfileName().", we strongly encourage you to work hard when another opportunity is available for you";
				break;
			case Student::$__CAN_ADVANCE_SEMESTER:
				$customMessage = "You have moved to a new Semester, keep working hard";
				break;
			case Student::$__CAN_ADVANCE_YEAR:
				$customMessage = "You have moved to a new Accademic Year, wishing you all the best";
				break;
			case Student::$__NOTHING_TO_DO:
				$customMessage = "The System did not do anything. Try to communicate with your subject teachers whose subjects/modules still appears in your continuous results section";
				break;
			default: $customMessage = "Unknown Option";
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Advance Year or Semester</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	try {
		if (trim($_REQUEST['rndx']) != $ulogin1->getExtraFilter()) Object::shootException("The System has Detected you are replaying data in your Browser window");
		$promise1 = $student1->reallocateMeAccordingToDataStructure($ds1);
		if (! $promise1->isPromising()) Object::shootException($promise1->getReason());
		$ulogin1->setExtraFilter(System::getCodeString(8));
		$student1->commitUpdate();
		$ulogin1->commitUpdate();
		//Now we need to remove the approvalSequenceData 
		$schemaId = ApprovalSequenceSchema::getApprovedSchemaFromCodeAndLogin($database, $conn, $login1->getLoginId(), ApprovalSequenceSchema::$__ADVANCE_YEAR_OR_SEMESTER);
		if (is_null($schemaId)) Object::shootException("The Approval Sequence Schema Can not be empty");
		$dataId = ApprovalSequenceData::getDataIdForRequesterInASchema($database, $conn, $login1->getLoginId(), $schemaId, null);
		if (is_null($dataId)) Object::shootException("There were not Approval Sequence Data Found");
		$data1 = new ApprovalSequenceData($database, $dataId, $conn);
		$data1->commitDelete();
	} catch (Exception $e)	{
		$promise1->setPromise($e->getMessage());
		$promise1->setPromise(false);
	}
	if ($promise1->isPromising())	{
?>
		<div class="ui-sys-highlight">
			Congratulations; The System has placed you in a correct year/semester <br/>
			Details : <?= $customMessage ?>
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were some problems in Advancing me in Semester/Year <br/>
			Details: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($login1->isStudent() && ! $registeredUser1->isDiscontinued() && $registeredUser1->shouldIAdvanceYearOrSemester($profile1) && ApprovalSequenceData::isApprovalComplete($config, $login1->getLoginId(), ApprovalSequenceSchema::$__ADVANCE_YEAR_OR_SEMESTER, $exprA108Name, $msgA108Name, "../server/createnewapprovalsequence.php", null))	{
			//Load that coming Semester and Year Subject 
			$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
			$promise1 = new Promise();
			$promise1->setPromise(true);
?>
			<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Advance Year or Semester</div>
				<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	//Get Data Structure to use 
	$student1 = null;
	$ds1 = null;
	$buttonText = "";
	$ulogin1 = null;
	try {
		$student1 = new Student($database, $registeredUser1->getStudentId(), $conn);
		$ulogin1 = $student1->getLogin();
		$ulogin1->setExtraFilter(System::getCodeString(8));
		$ulogin1->commitUpdate(); 
		$ds1 = $student1->advanceYearOrSemesterDataStructure($profile1,"../data/results/");
		if (is_null($ds1)) Object::shootException("The System returned an Empty Data Structure");
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
	//v788. Here is where to proceed 
	if ($promise1->isPromising())	{
		switch ($ds1['actionCode'])	{
			case Student::$__CAN_GRADUATE:
				//Run Graduation Algorithm
				$buttonText = "Confirm You are Graduating";
				break;
			case Student::$__HAS_FAILED:
				//Proceed with warning to this Student
				echo "<div class=\"ui-sys-warning\">You have failed some of the subjects, however the system will allow you to proceed but you will not graduate until you clear those subjects<br/>If you are a finalist you will still remain to be a finalist, if you are a non finalist you will carry this/these subject(s)/module(s)</div>";
				//Show the next Courses he she will be taking
				try {
					$listToDisplay = $student1->showMySubjectListForSemester($ds1['nextYearOfStudy'], $ds1['nextSemesterOfStudy']);
					echo $listToDisplay;
				} catch (Exception $e)	{
					echo "<div class=\"ui-state-error\">The System could not pull the upcoming subject list</div>";
				}
				$buttonText = "Confirm to Proceed with Fails";
				break;
			case Student::$__HAS_DISQUALIFIED:
				//Run Disquitinuing Algorithm
				$profileName = $profile1->getProfileName();
				echo "<div class=\"ui-state-warning\">You are about to DISCONTINUING from $profileName</div>";
				$buttonText = "Confirm you are Discontinuing";
				break;
			case Student::$__CAN_ADVANCE_SEMESTER:
				// Show the next Subjects he/she will be taking
				try {
					$listToDisplay = $student1->showMySubjectListForSemester($ds1['nextYearOfStudy'], $ds1['nextSemesterOfStudy']);
					echo $listToDisplay;
				} catch (Exception $e)	{
					echo "<div class=\"ui-state-error\">The System could not pull the upcoming subject list</div>";
				}
				$buttonText = "Confirm to Move to Semester ".$ds1['nextSemesterOfStudy'];
				break;
			case Student::$__CAN_ADVANCE_YEAR:
				//Show the next Subjects he/she will be taking
				try {
					$listToDisplay = $student1->showMySubjectListForSemester($ds1['nextYearOfStudy'], $ds1['nextSemesterOfStudy']);
					echo $listToDisplay;
				} catch (Exception $e)	{
					echo "<div class=\"ui-state-error\">The System could not pull the upcoming subject list</div>";
				}
				$buttonText = "Confirm to move to Year ".$ds1['nextYearOfStudy'].", Semester ".$ds1['nextSemesterOfStudy'];
				break;
			case Student::$__NOTHING_TO_DO:
				echo "<div class=\"ui-sys-warning\">The system can not take you to the coming year or allow you to graduate<br/>This happens because some of your continuous results have not yet compiled</div>";
				$buttonText = "Continue";
				break;
			default: echo "The System Received an Invalid Choice";
		}
	} else {
?>
		<div class="ui-state-error">
			There were problems in advancing Year or Semester <br/>
			Details: <?= $promise1->getReason() ?>
		</div>
<?php
	}
	if ($promise1->isPromising())	{
?>
		<div class="ui-sys-inline-controls-right">
			<form method="POST" action="<?= $thispage ?>">
				<input type="hidden" name="report" value="true"/>
				<input type="hidden" name="rndx" value="<?= $ulogin1->getExtraFilter() ?>"/>
				<input type="hidden" name="actionCode" value="<?= $ds1['actionCode'] ?>"/>
				<input type="hidden" name="nextAccademicYear" value="<?= $ds1['nextAccademicYear'] ?>"/>
				<input type="hidden" name="nextYearOfStudy" value="<?= $ds1['nextYearOfStudy'] ?>"/>
				<input type="hidden" name="nextSemesterOfStudy" value="<?= $ds1['nextSemesterOfStudy'] ?>"/>
				<input type="submit" value="<?= $buttonText ?>"/>
			</form>
		</div>
<?php
	} //end--fi-promising
?>				
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
<?php
			mysql_close($conn);
	}/*END Advance Semester or Year*/else /*BEGIN Edit My Profile*/if ($page == "edit_my_profile" && isset($_REQUEST['action']) && isset($_REQUEST['report']) && ($_REQUEST['action'] == "change_bank") && $login1->isANewSubmission() && ApprovalSequenceData::isApprovalComplete($config, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_BANK, $exprA108Name, $msgA108Name, "../server/createnewapprovalsequence.php", null)) {
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connec to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		//Perform not procedures like updates 
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
		//
		$ulogin1 = null;
		$student1 = $registeredUser1;
		try {
			$ulogin1 = new Login($database, $login1->getLoginId(), $conn);
			$student1->loadXMLFolder("../data/students");
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		try {
			//Begin to Perform All Changes this user requested
			//The below are file operations ; no need of using a live student object
			$promise1 = $student1->backupXMLFile();
			$promise1 = $student1->clearBankAccounts();
			$promise1 = $student1->addBankAccount($bankAccount1);
			$ulogin1->setExtraFilter(System::getCodeString(8));
			$ulogin1->commitUpdate();
			//End of Performing Changes this user requested 
			//Now we can remove the data sequence since everything is complete not 
			ApprovalSequenceData::clearApprovalSequenceData($database, $conn, ApprovalSequenceSchema::$__USER_EDIT_BANK, null, $login1, null, 0);
			$enableUpdate = true;
		} catch (Exception $e)	{
			$enableUpdate = false;
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Changing Bank Information</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
		$enableUpdate = true;
?>
		<div class="ui-state-highlight">
			You have successful updated bank information
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were some problems in Changing Bank Information <br/>
			Details : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageapprovalsequencedata_edit", "Self Changing Bank Information");
	} else if ($page == "edit_my_profile" && isset($_REQUEST['action']) && ($_REQUEST['action'] == "change_bank") && ApprovalSequenceData::isApprovalComplete($config, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_BANK, $exprA108Name, $msgA108Name, "../server/createnewapprovalsequence.php", null))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$ulogin1 = null;
		$student1 = $registeredUser1;
		try {
			$ulogin1 = new Login($database, $login1->getLoginId(), $conn);
			$ulogin1->setExtraFilter(System::getCodeString(8));
			$ulogin1->commitUpdate();
			$student1->loadXMLFolder("../data/students");
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Changing Bank Information</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<div class="ui-sys-warning">
					<b>BE CAREFUL:</b> Changing of data is one time event. If you make a mistake you will have to begin the entire cycle of approval. The Approving Authorities has approved you to change this data(s) Once and Once Only
				</div>
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="edit_my_profile"/>
					<input type="hidden" name="action" value="change_bank"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="rndx" value="<?= $ulogin1->getExtraFilter() ?>"/>
<?php 
	$accountName = "";
	$bankAccount = "";
	$bankName = "";
	$branchName = "";
	$bankAccount1 = $student1->getBankAccountList();
	if (! is_null($bankAccount1))	{
		$bankAccount1 = $bankAccount1[0];
		if (! is_null($bankAccount1->getElementsByTagName('accountName'))) $accountName = $bankAccount1->getElementsByTagName('accountName')->item(0)->nodeValue;
		if (! is_null($bankAccount1->getElementsByTagName('bankAccount'))) $bankAccount = $bankAccount1->getElementsByTagName('bankAccount')->item(0)->nodeValue;
		if (! is_null($bankAccount1->getElementsByTagName('bankName'))) $bankName = $bankAccount1->getElementsByTagName('bankName')->item(0)->nodeValue;
		if (! is_null($bankAccount1->getElementsByTagName('branchName'))) $branchName = $bankAccount1->getElementsByTagName('branchName')->item(0)->nodeValue;
	}
?>
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
						<input id="__add_record" type="button" value="Change Bank Information"/>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<script type="text/javascript">
(function($)	{
	$('#__add_record').on('click', function(event)	{
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if ($page == "edit_my_profile" && $login1->isStudent() && isset($_REQUEST['action']) && isset($_REQUEST['report']) && ($_REQUEST['action'] == "change_course") && $login1->isANewSubmission() && ApprovalSequenceData::isApprovalComplete($config, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_COURSE, $exprA108Name, $msgA108Name, "../server/createnewapprovalsequence.php", null)) {
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connec to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		//Perform not procedures like updates 
		$courseId = mysql_real_escape_string($_REQUEST['courseId']);
		//
		$ulogin1 = null;
		$student1 = null;
		try {
			$ulogin1 = new Login($database, $login1->getLoginId(), $conn);
			$student1 = Login::getUserWithThisLogin($database, $conn, $ulogin1);
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		if (is_null($student1->getCourse()) || ($courseId != $student1->getCourse()->getCourseId()))	{
			$student1->setCourse($courseId); $enableUpdate = true;
		}
		try {
			if (! $enableUpdate) Object::shootException("No Changes has been made, you did not Update Anything");
			$schemaId = ApprovalSequenceSchema::getApprovedSchemaFromCodeAndLogin($database, $conn, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_COURSE);
			if (is_null($schemaId)) Object::shootException("The Approval Sequence Schema Can not be empty");
			$dataId = ApprovalSequenceData::getDataIdForRequesterInASchema($database, $conn, $login1->getLoginId(), $schemaId, null);
			if (is_null($dataId)) Object::shootException("There were not Approval Sequence Data Found");
			$data1 = new ApprovalSequenceData($database, $dataId, $conn);
			//Begin to Perform All Changes this user requested
			$ulogin1->setExtraFilter(System::getCodeString(8));
			//We need to allocate this student in a proper group now 
			$promise1 = Student::reallocateStudent($database, $conn, $student1);
			if (! $promise1->isPromising()) Object::shootException($promise1->getReason());
			$ulogin1->setGroup($promise1->getResults());
			$student1->setAlreadyGraduated("0");
			$student1->graduateIfGraduated();
			$student1->setFinalYear("0");
			$student1->allocateToFinalYearIfFinalYear();
			$student1->commitUpdate();
			$ulogin1->commitUpdate();
			//End of Performing Changes this user requested 
			//Now we can remove the data sequence since everything is complete not 
			$data1->commitDelete();
		} catch (Exception $e)	{
			$enableUpdate = false;
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Changing Course of Study</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
		$enableUpdate = true;
?>
		<div class="ui-state-highlight">
			You have successful updated your Course of Study
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were some problems in Changing Course of Study <br/>
			Details : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageapprovalsequencedata_edit", "Self Changing Course of Study");
	} else if ($page == "edit_my_profile" && $login1->isStudent() && isset($_REQUEST['action']) && ($_REQUEST['action'] == "change_course") && ApprovalSequenceData::isApprovalComplete($config, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_COURSE, $exprA108Name, $msgA108Name, "../server/createnewapprovalsequence.php", null))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$fullnameText = "Your Full Name";
		if ($login1->isStudent()) $fullnameText = "Name(s) in Certificate";
		$ulogin1 = null;
		try {
			$ulogin1 = new Login($database, $login1->getLoginId(), $conn);
			$ulogin1->setExtraFilter(System::getCodeString(8));
			$ulogin1->commitUpdate();
		} catch (Exception $e)	{
			die($e->getMessage()); 
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Changing Course of Study</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<div class="ui-sys-warning">
					<b>BE CAREFUL:</b> Changing of data is one time event. If you make a mistake you will have to begin the entire cycle of approval. The Approving Authorities has approved you to change this data(s) Once and Once Only
				</div>
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="edit_my_profile"/>
					<input type="hidden" name="action" value="change_course"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="rndx" value="<?= $ulogin1->getExtraFilter() ?>"/>
					<div class="pure-control-group block-course-control">
						<label for="courseId">Course Name</label>
						<select id="courseId" name="courseId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Course">
							<option value="_@32767@_">--select--</option>
<?php 
	$list = Course::loadAllData($database, $conn);
	foreach ($list as $alist)	{
		$selected="";
		if (! is_null($registeredUser1->getCourse()) && ($alist['id'] == $registeredUser1->getCourse()->getCourseId())) $selected = "selected";
?>
		<option <?= $selected ?> value="<?= $alist['id'] ?>"><?= $alist['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Change My Course of Study"/>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<script type="text/javascript">
(function($)	{
	$('#__add_record').on('click', function(event)	{
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if ($page == "edit_my_profile" && $login1->isStudent() && isset($_REQUEST['action']) && isset($_REQUEST['report']) && ($_REQUEST['action'] == "change_reg_number") && $login1->isANewSubmission() && ApprovalSequenceData::isApprovalComplete($config, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_REG_NUMBER, $exprA108Name, $msgA108Name, "../server/createnewapprovalsequence.php", null)) {
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connec to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		//Perform not procedures like updates 
		$registrationNumber = mysql_real_escape_string($_REQUEST['registrationNumber']);
		//
		$ulogin1 = null;
		$student1 = null;
		try {
			$ulogin1 = new Login($database, $login1->getLoginId(), $conn);
			$student1 = Login::getUserWithThisLogin($database, $conn, $ulogin1);
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		if ($registrationNumber != $student1->getRegistrationNumber())	{
			$student1->setRegistrationNumber($registrationNumber); $enableUpdate = true;
		}
		try {
			if (! $enableUpdate) Object::shootException("No Changes has been made, you did not Update Anything");
			$schemaId = ApprovalSequenceSchema::getApprovedSchemaFromCodeAndLogin($database, $conn, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_REG_NUMBER);
			if (is_null($schemaId)) Object::shootException("The Approval Sequence Schema Can not be empty");
			$dataId = ApprovalSequenceData::getDataIdForRequesterInASchema($database, $conn, $login1->getLoginId(), $schemaId, null);
			if (is_null($dataId)) Object::shootException("There were not Approval Sequence Data Found");
			$data1 = new ApprovalSequenceData($database, $dataId, $conn);
			//Begin to Perform All Changes this user requested
			$ulogin1->setExtraFilter(System::getCodeString(8));
			$student1->commitUpdate();
			$ulogin1->commitUpdate();
			//End of Performing Changes this user requested 
			//Now we can remove the data sequence since everything is complete not 
			$data1->commitDelete();
		} catch (Exception $e)	{
			$enableUpdate = false;
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Changing Registration Number</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
		$enableUpdate = true;
?>
		<div class="ui-state-highlight">
			You have successful updated Registration Number
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were some problems in Changing Registration Number <br/>
			Details : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageapprovalsequencedata_edit", "Self Changing Registration Number");
	} else if ($page == "edit_my_profile" && $login1->isStudent() && isset($_REQUEST['action']) && ($_REQUEST['action'] == "change_reg_number") && ApprovalSequenceData::isApprovalComplete($config, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_REG_NUMBER, $exprA108Name, $msgA108Name, "../server/createnewapprovalsequence.php", null))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$fullnameText = "Your Full Name";
		if ($login1->isStudent()) $fullnameText = "Name(s) in Certificate";
		$ulogin1 = null;
		try {
			$ulogin1 = new Login($database, $login1->getLoginId(), $conn);
			$ulogin1->setExtraFilter(System::getCodeString(8));
			$ulogin1->commitUpdate();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Changing Registration Number</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<div class="ui-sys-warning">
					<b>BE CAREFUL:</b> Changing of data is one time event. If you make a mistake you will have to begin the entire cycle of approval. The Approving Authorities has approved you to change this data(s) Once and Once Only
				</div>
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="edit_my_profile"/>
					<input type="hidden" name="action" value="change_reg_number"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="rndx" value="<?= $ulogin1->getExtraFilter() ?>"/>
					<div class="pure-control-group">
						<label for="registrationNumber">Registration Number </label>
						<input value="<?= $registeredUser1->getRegistrationNumber() ?>" type="text" name="registrationNumber" id="registrationNumber" size="32" required pattern="<?= $exprRegistrationNumber ?>" validate="true" validate_control="text" validate_expression="<?= $exprRegistrationNumber ?>" validate_message="<?= $msgRegistrationNumber ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Change Registration Number"/>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<script type="text/javascript">
(function($)	{
	$('#__add_record').on('click', function(event)	{
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if ($page == "edit_my_profile" && isset($_REQUEST['action']) && isset($_REQUEST['report']) && ($_REQUEST['action'] == "change_password") && $login1->isANewSubmission() && ApprovalSequenceData::isApprovalComplete($config, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_PASSWORD, $exprA108Name, $msgA108Name, "../server/createnewapprovalsequence.php", null)) {
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		//Perform not procedures like updates 
		$password = sha1($_POST['password']);
		$oldpassword = sha1($_POST['oldpassword']);
		//
		$ulogin1 = null;
		try {
			$ulogin1 = new Login($database, $login1->getLoginId(), $conn);
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		try {
			if ($oldpassword != $ulogin1->getPassword()) Object::shootException("The Old Password you supplied is incorrect, you can contact the Administrator to reset your password");
			if ($oldpassword == $password) Object::shootException("You have not changed password, you have just supplied the same password");
			$ulogin1->setPassword($password); $enableUpdate = true;
			if (! $enableUpdate) Object::shootException("No Changes has been made, you did not Update Anything");
			$schemaId = ApprovalSequenceSchema::getApprovedSchemaFromCodeAndLogin($database, $conn, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_PASSWORD);
			if (is_null($schemaId)) Object::shootException("The Approval Sequence Schema Can not be empty");
			$dataId = ApprovalSequenceData::getDataIdForRequesterInASchema($database, $conn, $login1->getLoginId(), $schemaId, null);
			if (is_null($dataId)) Object::shootException("There were not Approval Sequence Data Found");
			$data1 = new ApprovalSequenceData($database, $dataId, $conn);
			//Begin to Perform All Changes this user requested
			$ulogin1->setExtraFilter(System::getCodeString(8));
			$ulogin1->commitUpdate();
			//End of Performing Changes this user requested 
			//Now we can remove the data sequence since everything is complete not 
			$data1->commitDelete();
		} catch (Exception $e)	{
			$enableUpdate = false;
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Changing Password</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
		$enableUpdate = true;
?>
		<div class="ui-state-highlight">
			You have successful updated password
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were some problems in Changing Password <br/>
			Details : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageapprovalsequencedata_edit", "Self Changing Password");
	} else if ($page == "edit_my_profile" && isset($_REQUEST['action']) && ($_REQUEST['action'] == "change_password") && ApprovalSequenceData::isApprovalComplete($config, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_PASSWORD, $exprA108Name, $msgA108Name, "../server/createnewapprovalsequence.php", null))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$fullnameText = "Your Full Name";
		if ($login1->isStudent()) $fullnameText = "Name(s) in Certificate";
		$ulogin1 = null;
		try {
			$ulogin1 = new Login($database, $login1->getLoginId(), $conn);
			$ulogin1->setExtraFilter(System::getCodeString(8));
			$ulogin1->commitUpdate();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Changing Password</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<div class="ui-sys-warning">
					<b>BE CAREFUL:</b> Changing of data is one time event. If you make a mistake you will have to begin the entire cycle of approval. The Approving Authorities has approved you to change this data(s) Once and Once Only
				</div>
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="edit_my_profile"/>
					<input type="hidden" name="action" value="change_password"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="rndx" value="<?= $ulogin1->getExtraFilter() ?>"/>
					 <div class="pure-control-group">
						<label for="oldpassword">Old Password</label>
						<input type="password" name="oldpassword" id="oldpassword" size="32" required pattern="<?= $exprPassword ?>" validate="true" validate_control="text" validate_expression="<?= $exprPassword ?>" validate_message="Old Password: <?= $msgPassword ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="password">Password</label>
						<input type="password" name="password" id="password" size="32" required pattern="<?= $exprPassword ?>" validate="true" validate_control="text" validate_expression="<?= $exprPassword ?>" validate_message="Password: <?= $msgPassword ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="cpassword">Confirm Password</label>
						<input type="password" id="cpassword" size="32" required/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Change Password"/>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<script type="text/javascript">
(function($)	{
	$('#__add_record').on('click', function(event)	{
		var $target1 = $('#perror');
		if (! $target1.length) return;
		var $password1 = $('#password');
		if (! $password1.length) return;
		var $cpassword1 = $('#cpassword');
		if (! $cpassword1.length) return;
		if ($password1.val() == $cpassword1.val())	{
			generalFormSubmission(this, 'form1', 'perror');
		} else {
			$target1.empty();
			$target1.html('Password Mismatch, Make sure Password and Confirm Password do match');
		}
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if ($page == "edit_my_profile" && isset($_REQUEST['action']) && isset($_REQUEST['report']) && ($_REQUEST['action'] == "change_contacts") && $login1->isANewSubmission() && ApprovalSequenceData::isApprovalComplete($config, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_CONTACTS, $exprA108Name, $msgA108Name, "../server/createnewapprovalsequence.php", null)) {
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		//Perform not procedures like updates 
		$email = mysql_real_escape_string($_REQUEST['email']);
		$phone = mysql_real_escape_string($_REQUEST['phone']);
		//
		$ulogin1 = null;
		try {
			$ulogin1 = new Login($database, $login1->getLoginId(), $conn);
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		if ($email != $ulogin1->getEmail())	{
			$ulogin1->setEmail($email); $enableUpdate = true;
		}
		if ($phone != $ulogin1->getPhone())	{
			$ulogin1->setPhone($phone); $enableUpdate = true;
		}
		try {
			if (! $enableUpdate) Object::shootException("No Changes has been made, you did not Update Anything");
			$schemaId = ApprovalSequenceSchema::getApprovedSchemaFromCodeAndLogin($database, $conn, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_CONTACTS);
			if (is_null($schemaId)) Object::shootException("The Approval Sequence Schema Can not be empty");
			$dataId = ApprovalSequenceData::getDataIdForRequesterInASchema($database, $conn, $login1->getLoginId(), $schemaId, null);
			if (is_null($dataId)) Object::shootException("There were not Approval Sequence Data Found");
			$data1 = new ApprovalSequenceData($database, $dataId, $conn);
			//Begin to Perform All Changes this user requested
			$ulogin1->setExtraFilter(System::getCodeString(8));
			$ulogin1->commitUpdate();
			//End of Performing Changes this user requested 
			//Now we can remove the data sequence since everything is complete not 
			$data1->commitDelete();
		} catch (Exception $e)	{
			$enableUpdate = false;
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Changing Contacts</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
		$enableUpdate = true;
?>
		<div class="ui-state-highlight">
			You have successful updated contacts
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were some problems in Changing Contacts <br/>
			Details : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageapprovalsequencedata_edit", "Self Changing Contacts");
	} else if ($page == "edit_my_profile" && isset($_REQUEST['action']) && ($_REQUEST['action'] == "change_contacts") && ApprovalSequenceData::isApprovalComplete($config, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_CONTACTS, $exprA108Name, $msgA108Name, "../server/createnewapprovalsequence.php", null))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$fullnameText = "Your Full Name";
		if ($login1->isStudent()) $fullnameText = "Name(s) in Certificate";
		$ulogin1 = null;
		try {
			$ulogin1 = new Login($database, $login1->getLoginId(), $conn);
			$ulogin1->setExtraFilter(System::getCodeString(8));
			$ulogin1->commitUpdate();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Changing Contacts</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<div class="ui-sys-warning">
					<b>BE CAREFUL:</b> Changing of data is one time event. If you make a mistake you will have to begin the entire cycle of approval. The Approving Authorities has approved you to change this data(s) Once and Once Only
				</div>
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="edit_my_profile"/>
					<input type="hidden" name="action" value="change_contacts"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="rndx" value="<?= $ulogin1->getExtraFilter() ?>"/>
					 <div class="pure-control-group">
						<label for="email">Email Address</label>
						<input value="<?= $login1->getEmail() ?>" type="text" name="email" id="email" size="32" required pattern="<?= $exprEmail ?>" validate="true" validate_control="text" validate_expression="<?= $exprEmail ?>%<?= $exprL32Name ?>" validate_message="Email Format : <?= $msgEmail ?>%Email Length: <?= $msgL32Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="cemail">Confirm Email Address</label>
						<input value="<?= $login1->getEmail() ?>" type="text" id="cemail" size="32" required pattern="<?= $exprEmail ?>" validate="true" validate_control="text" validate_expression="<?= $exprEmail ?>%<?= $exprL32Name ?>" validate_message="Email Format : <?= $msgEmail ?>%Email Length: <?= $msgL32Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="phone">Phone/Telephone </label>
						<input value="<?= $login1->getPhone() ?>" type="text" name="phone" id="phone" size="32" required pattern="<?= $exprPhone ?>" validate="true" validate_control="text" validate_expression="<?= $exprPhone ?>" validate_message="Phone/Telephone : <?= $msgPhone ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Change Contacts"/>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<script type="text/javascript">
(function($)	{
	$('#__add_record').on('click', function(event)	{
		event.preventDefault();
		var $target1 = $('#perror');
		if (! $target1.length) return;
		var loginName="!@#$%^&*()";
		var $email1 = $('#email');
		if (! $email1.length) return;
		var $phone1 = $('#phone');
		if (! $phone1.length) return;
		$target1.empty();
		if ($('#email').val() != $('#cemail').val())	{
			$('<span/>').html("Mismatch of Emails ==> Emails do not Match")
				.appendTo($target1);
			return;
		}
		$.ajax({
			url: "../server/service_username_email_phone_availability.php",
			method: "POST",
			data: { param1: loginName, param2: $email1.val(), param3: $phone1.val(), param4: <?= $login1->getLoginId() ?> },
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
	} else if ($page == "edit_my_profile" && isset($_REQUEST['action']) && isset($_REQUEST['report']) && ($_REQUEST['action'] == "change_photo") && $login1->isANewSubmission() && ApprovalSequenceData::isApprovalComplete($config, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_PHOTO, $exprA108Name, $msgA108Name, "../server/createnewapprovalsequence.php", null)) {
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connec to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		//
		$ulogin1 = null;
		try {
			$ulogin1 = new Login($database, $login1->getLoginId(), $conn);
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		$logoArray1 = $_FILES['logo'];
		try {
			if (! Object::isThereAnyFileReceivedFromTheClient($logoArray1)) Object::shootException("There is no file received from the Client");
			$logoFolder = $__user_data_folder."photo";
			$logofilename = $__user_photo_prefix."_".$ulogin1->getLoginId();
			$fileextension = Object::getUploadedFileExtension($logoArray1);
			$uploadedFileName = $logofilename.".".$fileextension;
			$validTypes = array("image/jpeg", "image/png", "image/jpg");
			$validExtensions = array("jpeg", "png", "jpg");
			$maximumUploadedSize = Object::$__MAXIMUM_UPLOADED_IMAGE_SIZE;
			$promise1 = Object::saveUploadedFile($logoArray1, $logoFolder, $logofilename, $validTypes, $validExtensions, $maximumUploadedSize);
			if (! $promise1->isPromising()) Object::shootException($promise1->getReason());
			$schemaId = ApprovalSequenceSchema::getApprovedSchemaFromCodeAndLogin($database, $conn, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_PHOTO);
			if (is_null($schemaId)) Object::shootException("The Approval Sequence Schema Can not be empty");
			$dataId = ApprovalSequenceData::getDataIdForRequesterInASchema($database, $conn, $login1->getLoginId(), $schemaId, null);
			if (is_null($dataId)) Object::shootException("There were not Approval Sequence Data Found");
			$data1 = new ApprovalSequenceData($database, $dataId, $conn);
			//Begin to Perform All Changes this user requested
			$ulogin1->setPhoto($uploadedFileName);
			$ulogin1->setExtraFilter(System::getCodeString(8));
			$ulogin1->commitUpdate();
			//End of Performing Changes this user requested 
			//Now we can remove the data sequence since everything is complete not 
			$data1->commitDelete();
		} catch (Exception $e)	{
			$enableUpdate = false;
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Changing  Picture</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
		$enableUpdate = true;
?>
		<div class="ui-state-highlight">
			You have successful updated profile picture
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were some problems in Changing Profile Picture <br/>
			Details : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageapprovalsequencedata_edit", "Self Changing Profile Picture");
	} else if ($page == "edit_my_profile" && isset($_REQUEST['action']) && ($_REQUEST['action'] == "change_photo") && ApprovalSequenceData::isApprovalComplete($config, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_PHOTO, $exprA108Name, $msgA108Name, "../server/createnewapprovalsequence.php", null))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$ulogin1 = null;
		try {
			$ulogin1 = new Login($database, $login1->getLoginId(), $conn);
			$ulogin1->setExtraFilter(System::getCodeString(8));
			$ulogin1->commitUpdate();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Changing Profile Picture</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<div class="ui-sys-warning">
					<b>BE CAREFUL:</b> Changing of data is one time event. If you make a mistake you will have to begin the entire cycle of approval. The Approving Authorities has approved you to change this data(s) Once and Once Only
				</div>
<!--Beginning a photo container-->	
<?php 
		$photo = $__user_data_folder."photo/default.png";
		$trackchange = 0;
		//$website = $profile1->getWebsite()
		if (! is_null($login1->getPhoto())) $photo = $__user_data_folder."photo/".$login1->getPhoto();
?>
<div class="photocontainer">
	<div class="photodisplay">
		<img id="__id_image_photo_container" alt="PIC" src="<?= $photo ?>"/>
	</div>
	<div class="photodata">
		<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
			<input type="hidden" name="page" value="edit_my_profile"/>
			<input type="hidden" name="action" value="change_photo"/>
			<input type="hidden" name="report" value="io"/>
			<input type="hidden" name="rndx" value="<?= $ulogin1->getExtraFilter() ?>"/>
			<fieldset>
				<div class="pure-control-group">
					<label for="__id_image_photo_control">Select Photo </label>
					<input  id="__id_image_photo_control" type="file" class="ui-sys-file-upload" name="logo" data-trackchange="<?= $trackchange ?>" accept="image/*"/>
				</div>
				<div class="pure-controls">
					<span id="perror" class="ui-sys-inline-controls-center ui-sys-error-message"></span>
				</div>
				<div class="pure-controls">
					<span><input type="submit" value="Change Profile Photo"/></span>
				</div>
			</fieldset>
		</form>
	</div>
	<div class="ui-sys-clear-both">&nbsp;</div>
</div>
<!--Ending a photo container-->
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
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
	}  else if ($page == "edit_my_profile" && isset($_REQUEST['action']) && isset($_REQUEST['report']) && ($_REQUEST['action'] == "change_marital") && $login1->isANewSubmission() && ApprovalSequenceData::isApprovalComplete($config, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_MARITAL, $exprA108Name, $msgA108Name, "../server/createnewapprovalsequence.php", null)) {
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connec to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		//Perform not procedures like updates 
		$maritalId = mysql_real_escape_string($_REQUEST['maritalId']);
		//
		$ulogin1 = null;
		try {
			$ulogin1 = new Login($database, $login1->getLoginId(), $conn);
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		if (is_null($ulogin1->getMarital()) || ($maritalId != $ulogin1->getMarital()->getMaritalId()))	{
			$ulogin1->setMarital($maritalId); $enableUpdate = true;
		}
		try {
			if (! $enableUpdate) Object::shootException("No Changes has been made, you did not Update Anything");
			$schemaId = ApprovalSequenceSchema::getApprovedSchemaFromCodeAndLogin($database, $conn, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_MARITAL);
			if (is_null($schemaId)) Object::shootException("The Approval Sequence Schema Can not be empty");
			$dataId = ApprovalSequenceData::getDataIdForRequesterInASchema($database, $conn, $login1->getLoginId(), $schemaId, null);
			if (is_null($dataId)) Object::shootException("There were not Approval Sequence Data Found");
			$data1 = new ApprovalSequenceData($database, $dataId, $conn);
			//Begin to Perform All Changes this user requested
			$ulogin1->setExtraFilter(System::getCodeString(8));
			$ulogin1->commitUpdate();
			//End of Performing Changes this user requested 
			//Now we can remove the data sequence since everything is complete not 
			$data1->commitDelete();
		} catch (Exception $e)	{
			$enableUpdate = false;
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Changing Marital Status</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
		$enableUpdate = true;
?>
		<div class="ui-state-highlight">
			You have successful updated marital status
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were some problems in Changing Marital Status <br/>
			Details : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageapprovalsequencedata_edit", "Self Changing Marital Status");
	} else if ($page == "edit_my_profile" && isset($_REQUEST['action']) && ($_REQUEST['action'] == "change_marital") && ApprovalSequenceData::isApprovalComplete($config, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_MARITAL, $exprA108Name, $msgA108Name, "../server/createnewapprovalsequence.php", null))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$ulogin1 = null;
		try {
			$ulogin1 = new Login($database, $login1->getLoginId(), $conn);
			$ulogin1->setExtraFilter(System::getCodeString(8));
			$ulogin1->commitUpdate();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Changing Marital Status</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<div class="ui-sys-warning">
					<b>BE CAREFUL:</b> Changing of data is one time event. If you make a mistake you will have to begin the entire cycle of approval. The Approving Authorities has approved you to change this data(s) Once and Once Only
				</div>
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="edit_my_profile"/>
					<input type="hidden" name="action" value="change_marital"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="rndx" value="<?= $ulogin1->getExtraFilter() ?>"/>
					<div class="pure-control-group">
						<label for="maritalId">Marital Status</label>
						<select id="maritalId" name="maritalId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Marital">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Marital::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($login1->getMarital()) && ($alist1['id'] == $login1->getMarital()->getMaritalId())) $selected="selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Change Marital Status"/>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<script type="text/javascript">
(function($)	{
	$('#__add_record').on('click', function(event)	{
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if ($page == "edit_my_profile" && isset($_REQUEST['action']) && isset($_REQUEST['report']) && ($_REQUEST['action'] == "change_names") && $login1->isANewSubmission() && ApprovalSequenceData::isApprovalComplete($config, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_NAMES, $exprA108Name, $msgA108Name, "../server/createnewapprovalsequence.php", null)) {
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connec to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		//Perform not procedures like updates 
		$firstname = mysql_real_escape_string($_REQUEST['firstname']);
		$middlename = mysql_real_escape_string($_REQUEST['middlename']);
		$lastname = mysql_real_escape_string($_REQUEST['lastname']);
		$fullname = mysql_real_escape_string($_REQUEST['fullname']);
		//
		$ulogin1 = null;
		try {
			$ulogin1 = new Login($database, $login1->getLoginId(), $conn);
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		if ($firstname != $ulogin1->getFirstname())	{
			$ulogin1->setFirstname($firstname); $enableUpdate = true;
		}
		if ($middlename != $ulogin1->getMiddlename())	{
			$ulogin1->setMiddlename($middlename); $enableUpdate = true;
		}
		if ($lastname != $ulogin1->getLastname())	{
			$ulogin1->setLastname($lastname); $enableUpdate = true;
		}
		if ($fullname != $ulogin1->getFullname())	{
			$ulogin1->setFullname($fullname); $enableUpdate = true;
		}
		try {
			if (! $enableUpdate) Object::shootException("No Changes has been made, you did not Update Anything");
			$schemaId = ApprovalSequenceSchema::getApprovedSchemaFromCodeAndLogin($database, $conn, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_NAMES);
			if (is_null($schemaId)) Object::shootException("The Approval Sequence Schema Can not be empty");
			$dataId = ApprovalSequenceData::getDataIdForRequesterInASchema($database, $conn, $login1->getLoginId(), $schemaId, null);
			if (is_null($dataId)) Object::shootException("There were not Approval Sequence Data Found");
			$data1 = new ApprovalSequenceData($database, $dataId, $conn);
			//Begin to Perform All Changes this user requested
			$ulogin1->setExtraFilter(System::getCodeString(8));
			$ulogin1->commitUpdate();
			//End of Performing Changes this user requested 
			//Now we can remove the data sequence since everything is complete not 
			$data1->commitDelete();
		} catch (Exception $e)	{
			$enableUpdate = false;
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Changing Names</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
		$enableUpdate = true;
?>
		<div class="ui-state-highlight">
			You have successful updated name(s)
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were some problems in Changing Names <br/>
			Details : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageapprovalsequencedata_edit", "Self Changing Names");
	} else if ($page == "edit_my_profile" && isset($_REQUEST['action']) && ($_REQUEST['action'] == "change_names") && ApprovalSequenceData::isApprovalComplete($config, $login1->getLoginId(), ApprovalSequenceSchema::$__USER_EDIT_NAMES, $exprA108Name, $msgA108Name, "../server/createnewapprovalsequence.php", null))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$fullnameText = "Your Full Name";
		if ($login1->isStudent()) $fullnameText = "Name(s) in Certificate";
		$ulogin1 = null;
		try {
			$ulogin1 = new Login($database, $login1->getLoginId(), $conn);
			$ulogin1->setExtraFilter(System::getCodeString(8));
			$ulogin1->commitUpdate();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Changing Names</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<div class="ui-sys-warning">
					<b>BE CAREFUL:</b> Changing of data is one time event. If you make a mistake you will have to begin the entire cycle of approval. The Approving Authorities has approved you to change this data(s) Once and Once Only
				</div>
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="edit_my_profile"/>
					<input type="hidden" name="action" value="change_names"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="rndx" value="<?= $ulogin1->getExtraFilter() ?>"/>
					<div class="pure-control-group">
						<label for="firstname">Your Firstname </label>
						<input value="<?= $login1->getFirstname() ?>" type="text" name="firstname" id="firstname" size="32" required pattern="<?= $exprA32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Firstname : <?= $msgA32Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="middlename">Your Middlename </label>
						<input value="<?= $login1->getMiddlename() ?>" type="text" name="middlename" id="middlename" size="32" required pattern="<?= $exprA32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Middlename : <?= $msgA32Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="lastname">Your Lastname </label>
						<input value="<?= $login1->getLastname() ?>" type="text" name="lastname" id="lastname" size="32" required pattern="<?= $exprA32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Lastname : <?= $msgA32Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="fullname"><?= $fullnameText ?></label>
						<input type="text" name="fullname" id="fullname" size="32" value="<?= $login1->getFullname() ?>" required pattern="<?= $exprA64Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA64Name ?>" validate_message="<?= $fullnameText ?> : <?= $msgA64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Change Names"/>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<script type="text/javascript">
(function($)	{
	$('#__add_record').on('click', function(event)	{
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if ($page == "view_my_profile")	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header"></div>
			<div class="ui-sys-panel-body">
<?php 
	if ($login1->isStudent())	{
		$student1 = $registeredUser1;
?>
		<!--Entering Student Mode-->
	<div style="width: 96%; overflow-x: scroll;">
		<div class="ui-sys-content-window-scrollable">
			<div class="ui-sys-printable ui-sys-size-a4-potrait">
				<div class="ui-sys-printable-controls">
					<a><img title="Print" alt="P" src="../sysimage/print.png"/></a>
				</div>
<!--Beginning of Display Container-->
<?php 
	$profile1->setExtraFilter("../data/profile/logo/".$profile1->getLogo());
	$student1->setExtraFilter("../data/students/photo/".$student1->getLogin()->getPhoto());
	$student1->loadXMLFolder("../data/students");
	include("../documentreader/student.php");
?>
<!--End of Display Container-->			
			</div>
		</div>	
	</div>
		<!--Leaving Student Mode-->
<?php
	} else {
		echo "Coming Soon for System Users and Course Instructors";
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "edit_my_profile")	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">My Profile Editing Tool</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<!--Begin ICON Loading-->
<?php $captionText="Change My Names";	$action = "names";   ?>	<span class="ui-sys-icon"><a title="<?= $captionText ?>" href="<?= $thispage ?>?page=edit_my_profile&action=change_<?= $action ?>"><img alt="IMG" src="../sysimage/profile_<?= $action ?>.png"/></a><span><?= Object::summarizeString($captionText, 14) ?></span></span>
<?php $captionText="Change my Marital Status";	$action = "marital";   ?>	<span class="ui-sys-icon"><a title="<?= $captionText ?>" href="<?= $thispage ?>?page=edit_my_profile&action=change_<?= $action ?>"><img alt="IMG" src="../sysimage/profile_<?= $action ?>.png"/></a><span><?= Object::summarizeString($captionText, 14) ?></span></span>
<?php if ($login1->isStudent())  { $captionText="Edit My Disability Status";	$action = "disability";   ?>	<span class="ui-sys-icon"><a title="<?= $captionText ?>" href="<?= $thispage ?>?page=edit_my_profile&action=change_<?= $action ?>"><img alt="IMG" src="../sysimage/profile_<?= $action ?>.png"/></a><span><?= Object::summarizeString($captionText, 14) ?></span></span> <?php } ?>
<?php if ($login1->isStudent())  { $captionText="Change My Course of Study";	$action = "course";   ?>	<span class="ui-sys-icon"><a title="<?= $captionText ?>" href="<?= $thispage ?>?page=edit_my_profile&action=change_<?= $action ?>"><img alt="IMG" src="../sysimage/profile_<?= $action ?>.png"/></a><span><?= Object::summarizeString($captionText, 14) ?></span></span> <?php } ?>
<?php if ($login1->isStudent())  { $captionText="Change My Registration Number";	$action = "reg_number";   ?>	<span class="ui-sys-icon"><a title="<?= $captionText ?>" href="<?= $thispage ?>?page=edit_my_profile&action=change_<?= $action ?>"><img alt="IMG" src="../sysimage/profile_<?= $action ?>.png"/></a><span><?= Object::summarizeString($captionText, 14) ?></span></span> <?php } ?>
<?php $captionText="Change My Photo";	$action = "photo";   ?>	<span class="ui-sys-icon"><a title="<?= $captionText ?>" href="<?= $thispage ?>?page=edit_my_profile&action=change_<?= $action ?>"><img alt="IMG" src="../sysimage/profile_<?= $action ?>.png"/></a><span><?= Object::summarizeString($captionText, 14) ?></span></span>
<?php if ($login1->isStudent())  { $captionText="Change My Citizenship";	$action = "citizenship";   ?>	<span class="ui-sys-icon"><a title="<?= $captionText ?>" href="<?= $thispage ?>?page=edit_my_profile&action=change_<?= $action ?>"><img alt="IMG" src="../sysimage/profile_<?= $action ?>.png"/></a><span><?= Object::summarizeString($captionText, 14) ?></span></span> <?php } ?>
<?php if ($login1->isStudent())  { $captionText="Change My Occupation";	$action = "occupation";   ?>	<span class="ui-sys-icon"><a title="<?= $captionText ?>" href="<?= $thispage ?>?page=edit_my_profile&action=change_<?= $action ?>"><img alt="IMG" src="../sysimage/profile_<?= $action ?>.png"/></a><span><?= Object::summarizeString($captionText, 14) ?></span></span> <?php } ?>
<?php if ($login1->isStudent())  { $captionText="Change My Religion or Denomination";	$action = "religion";   ?>	<span class="ui-sys-icon"><a title="<?= $captionText ?>" href="<?= $thispage ?>?page=edit_my_profile&action=change_<?= $action ?>"><img alt="IMG" src="../sysimage/profile_<?= $action ?>.png"/></a><span><?= Object::summarizeString($captionText, 14) ?></span></span> <?php } ?>
<?php if ($login1->isStudent())  { $captionText="Change My Address";	$action = "address";   ?>	<span class="ui-sys-icon"><a title="<?= $captionText ?>" href="<?= $thispage ?>?page=edit_my_profile&action=change_<?= $action ?>"><img alt="IMG" src="../sysimage/profile_<?= $action ?>.png"/></a><span><?= Object::summarizeString($captionText, 14) ?></span></span> <?php } ?>
<?php if ($login1->isStudent())  { $captionText="Update My Employments";	$action = "employment";   ?>	<span class="ui-sys-icon"><a title="<?= $captionText ?>" href="<?= $thispage ?>?page=edit_my_profile&action=change_<?= $action ?>"><img alt="IMG" src="../sysimage/profile_<?= $action ?>.png"/></a><span><?= Object::summarizeString($captionText, 14) ?></span></span> <?php } ?>
<?php if ($login1->isStudent())  { $captionText="Update My Financial Sponsor";	$action = "sponsor";   ?>	<span class="ui-sys-icon"><a title="<?= $captionText ?>" href="<?= $thispage ?>?page=edit_my_profile&action=change_<?= $action ?>"><img alt="IMG" src="../sysimage/profile_<?= $action ?>.png"/></a><span><?= Object::summarizeString($captionText, 14) ?></span></span> <?php } ?>
<?php if ($login1->isStudent())  { $captionText="Change My Banking Details";	$action = "bank";   ?>	<span class="ui-sys-icon"><a title="<?= $captionText ?>" href="<?= $thispage ?>?page=edit_my_profile&action=change_<?= $action ?>"><img alt="IMG" src="../sysimage/profile_<?= $action ?>.png"/></a><span><?= Object::summarizeString($captionText, 14) ?></span></span> <?php } ?>
<?php if ($login1->isStudent())  { $captionText="Update My Next of Kin(s) Information";	$action = "nextofkin";   ?>	<span class="ui-sys-icon"><a title="<?= $captionText ?>" href="<?= $thispage ?>?page=edit_my_profile&action=change_<?= $action ?>"><img alt="IMG" src="../sysimage/profile_<?= $action ?>.png"/></a><span><?= Object::summarizeString($captionText, 14) ?></span></span> <?php } ?>
<?php $captionText="Change My Contacts (Email or Phone)";	$action = "contacts";   ?>	<span class="ui-sys-icon"><a title="<?= $captionText ?>" href="<?= $thispage ?>?page=edit_my_profile&action=change_<?= $action ?>"><img alt="IMG" src="../sysimage/profile_<?= $action ?>.png"/></a><span><?= Object::summarizeString($captionText, 14) ?></span></span>
<?php $captionText="Change My Passwords";	$action = "password";   ?>	<span class="ui-sys-icon"><a title="<?= $captionText ?>" href="<?= $thispage ?>?page=edit_my_profile&action=change_<?= $action ?>"><img alt="IMG" src="../sysimage/profile_<?= $action ?>.png"/></a><span><?= Object::summarizeString($captionText, 14) ?></span></span>

				<!--End ICON Loading-->
				<div class="ui-sys-clearboth">&nbsp;&nbsp;</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	}/*END Edit My Profile*/else /*BEGIN Attendance Record*/if ($page == "recordmyattendance" && isset($_REQUEST['tid']))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$timetable1 = null;
		try {
			$timetable1 = new Timetable($database, $_REQUEST['tid'], $conn);
			$promise1 = Attendance::recordAttendance($database, $conn, $profile1, $login1->getLoginId(), $_REQUEST['tid'], "../data/attendance/", $systemTime1);
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Attendance Recorder</div>
		<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Dear <?= $login1->getFullname() ?>; <br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Your Attendance for <i><?= $timetable1->getCaptionName() ?></i> has been recorded succesful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in recording your attendance <br/>
			Details : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>		
		</div>
		<div class="ui-sys-panel-footer"></div>
	</div>
<?php
		mysql_close($conn);
	}/* END Attendance Record*/ else /*BEGIN SEND SMS*/if ($page == "sendsms" && isset($_REQUEST['report']) && isset($_REQUEST['subject']) && isset($_REQUEST['message']))	{
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
	$promise1 = new Promise();
	$promise1->setPromise(true);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Sms Sender</div>
		<div class="ui-sys-panel-body">
<?php 
	try {
		if (! isset($_REQUEST['groupArr']) && ! isset($_REQUEST['jobArr']) && ! isset($_REQUEST['username'])) Object::shootException("The target recepient were not found");
		$groupArr = null;
		$jobArr = null;
		$usernameList = null;
		if (isset($_REQUEST['groupArr'])) $groupArr = $_REQUEST['groupArr'];
		if (isset($_REQUEST['jobArr'])) $jobArr = $_REQUEST['jobArr'];
		if (isset($_REQUEST['username'])) $usernameList = $_REQUEST['username'];
		$collection1 = System::getPhonesForMembers($database, $conn, $profile1, $groupArr, $jobArr, $usernameList);
		if ($collection1->getLength() == 0) Object::shootException("The System Returned Empty Phone numbers, perhaps the uses you supplied does not have a valid phone numbers");
		$from = $_REQUEST['subject'];
		$text = $_REQUEST['message'];
		$messageId = System::getCodeString(8);
		//Fetching username and password 
		$smsAccount1 = $profile1->getSMSAccountList();
		if (is_null($smsAccount1)) Object::shootException("SMS Account is not found");
		$smsAccount1 = $smsAccount1[0];
		$username = $smsAccount1->getElementsByTagName('accountUsername')->item(0)->nodeValue;
		$password = $smsAccount1->getElementsByTagName('accountPassword')->item(0)->nodeValue;
		$notifyUrl = "";
		$notifyContentType = "";
		$callbackData = "";
		$postUrl = "https://api.infobip.com/sms/1/text/advanced";
		$phoneCollections = $collection1->getCollection();
		if (is_null($phoneCollections)) Object::shootException("Phone Collections were not found");	
		//response 
		$ch = curl_init();
		$response = Communication::sendSMSUsingInfoBipAPI($ch, $username, $password, $postUrl, $messageId, $phoneCollections, $from, $text, $notifyUrl, $notifyContentType, $callbackData);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$responseBody = json_decode($response);
		curl_close($ch);
		if (is_null($responseBody)) Object::shootException("There were no response data from the Communication System");
		if ($httpCode >= 200 && $httpCode < 300)	{
			$table1 = Communication::getSMSUIFromInfoBipAPIResponse($responseBody, $profile1);
			if (is_null($table1)) Object::shootException("The system returned an empty UI Window");
			$promise1->setResults($table1);
		} else {
			$errorMessage = "The system could not send your sms, we kindly request you to refresh the browser, it might be due to internet fluctuation";
			if (! (is_null($responseBody->requestError) || is_null($responseBody->requestError->serviceException)))	{
				$errorMessage = "The system could not send your sms, we kindly request you to refresh the browser, it might be due to internet fluctuation <br/>".$responseBody->requestError->serviceException->text;
			}
			Object::shootException($errorMessage);
		}
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
	/*Proceed to results*/
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			The message has been sent successful <br/>
			<div style="font-size: 0.8em;">
				This means the system has sent the message successful, the system can not guarantee that the message has been delivered to all parties
			</div>
		</div><br/>
		<div class="ui-widget ui-widget-content ui-sys-panel-container ui-sys-panel">
			<div class="ui-sys-panel-header ui-widget-header">Sent Message Summary</div>
			<div class="ui-sys-panel-body ui-sys-horizontal-scrollable">
<?php 
				$window1 = $promise1->getResults();
				echo $window1;
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in sending the message <br/>
			Details: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>
		</div>
		<div class="ui-sys-panel-footer"></div>
	</div>
<?php
	mysql_close($conn);
} else if ($page == "sendsms" && isset($_REQUEST['message']))	{
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
	$promise1 = new Promise();
	$promise1->setPromise(true);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Sms Sender</div>
		<div class="ui-sys-panel-body">
			<form class="pure-form" id="form1" method="POST" action="<?= $thispage ?>">
				<input type="hidden" name="page" value="sendsms"/>
				<input type="hidden" name="subject" value="<?= $_REQUEST['subject'] ?>"/>
				<input type="hidden" name="message" value="<?= $_REQUEST['message'] ?>"/>
				<input type="hidden" name="report" value="io"/>
<!--Begin Group Selecting-->
	<div class="ui-sys-panel-data-container ui-sys-message ui-sys-sendsms">
		<div class="ui-sys-message-header">
			<label><input class="checkbox1" type="checkbox" name="chk_esms_group" value="1"/>&nbsp;&nbsp;Send Sms to Group</label>
		</div>
		<div class="ui-sys-message-body">
			<table class="pure-table pure-table-bordered ui-sys-data-table">
				<thead>
					<th></th>
					<th></th>
					<th>Group Name</th>
					<th>Parent Group</th>
					<th>Course Name</th>
					<th>Year</th>
					<th>Semester</th>
				</thead>
				<tbody></tbody>
			</table>
		</div>
		<div class="ui-sys-message-footer ui-sys-inline-controls-right">
			<input class="__a_load_panel_data" data-message-type="<?= MessageType::$__MESSAGE_TYPE_SMS ?>" data-name-prefix="groupArr" data-dialog-container="__id_general_dialog_holder" data-server-forward-path="../server/getgrouppreviewer.php" data-error-control="perror" type="button" value="Add Group" disabled/>
		</div>
	</div>
<!--End Group Selecting-->	
<!--Begin JobTitle Selecting-->
	<div class="ui-sys-panel-data-container ui-sys-message ui-sys-sendsms">
		<div class="ui-sys-message-header">
			<label><input class="checkbox1" type="checkbox" name="chk_esms_job" value="1"/>&nbsp;&nbsp;Send Sms to Job Titles</label>
		</div>
		<div class="ui-sys-message-body">
			<table class="pure-table pure-table-bordered ui-sys-data-table">
				<thead>
					<th></th>
					<th></th>
					<th>Job Title</th>
				</thead>
				<tbody></tbody>
			</table>
		</div>
		<div class="ui-sys-message-footer ui-sys-inline-controls-right">
			<input class="__a_load_panel_data" data-message-type="<?= MessageType::$__MESSAGE_TYPE_SMS ?>" data-name-prefix="jobArr" data-dialog-container="__id_general_dialog_holder" data-server-forward-path="../server/getjobtitlepreviewer.php" data-error-control="perror" type="button" value="Add Job Title" disabled/>
		</div>
	</div>
<!--End JobTitle Selecting-->	
<!--Begin Login Filling (usernames)-->
	<div class="ui-sys-panel-data-container ui-sys-message ui-sys-sendsms">
		<div class="ui-sys-message-header">
			<label><input class="checkbox1" type="checkbox" name="chk_esms_login" value="1"/>&nbsp;&nbsp;Send Sms to Users</label>
			<div class="ui-sys-warning">
				You have to supply the USERNAMES of users you want to send message to them<br/>
				Multiple usernames can be separated by commas <br/>
				Example: john1,joel@kpi.com,vlnd98
			</div>
		</div>
		<div class="ui-sys-message-body">
			<label>Usernames <input disabled type="text" name="username" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Username Lists <?= $msgL64Name ?>"/></label>
		</div>
		<div class="ui-sys-message-footer ui-sys-inline-controls-right">
			<input class="ui-sys-hidden __a_load_panel_data" data-name-prefix="username" data-dialog-container="__id_general_dialog_holder" data-server-forward-path="" data-error-control="perror" type="button" value="Add Username" disabled/>
		</div>
	</div>
<!--End Login Filling (usernames)-->
			</form>
			<div id="perror" class="ui-sys-error-message"></div>
		</div>
		<div class="ui-sys-panel-footer">
			<input type="button" value="Send Sms" id="__add_record"/>
		</div>
	</div>
<script type="text/javascript">
(function($)	{
	$('#__add_record').on('click', function(event)	{
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
	mysql_close($conn);
} else if ($page == "sendsms")	{
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
	$promise1 = new Promise();
	$promise1->setPromise(true);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Sms Sender</div>
		<div class="ui-sys-panel-body ui-sys-data-capture">
			<form class="pure-form" id="form1" method="POST" action="<?= $thispage ?>">
				<input type="hidden" name="page" value="sendsms"/>
				<table class="pure-table">
					<thead>
						<tr>
							<th colspan="2">Sms Composer</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><label for="subject">Sender</label></td>
							<td><input type="text" size="48" id="subject" name="subject" required pattern="<?= $exprL11Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL11Name ?>" validate_message="Sender <?= $msgL11Name ?>"/></td>
						</tr>
						<tr>
							<td><label for="message">Message</label></td>
							<td><textarea name="message" id="message" rows="4" cols="80" required pattern="<?= $exprL480Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL480Name ?>" validate_message="Message : <?= $msgL480Name ?>"></textarea></td>
						</tr>
						<tr>
							<td colspan="2" id="perror" class="ui-sys-error-message"></td>
						</tr>
						<tr>
							<td></td>
							<td><input id="__add_record" type="button" value="Proceed"/></td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
		<div class="ui-sys-panel-footer"></div>
	</div>
<script type="text/javascript">
(function($)	{
	$('#__add_record').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
	mysql_close($conn);
} /*END SEND SMS*/else/*BEGIN SEND MAIL*/if ($page == "sendmail" && isset($_REQUEST['report']) && isset($_REQUEST['subject']) && isset($_REQUEST['message']))	{
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
	$promise1 = new Promise();
	$promise1->setPromise(true);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Email Sender</div>
		<div class="ui-sys-panel-body">
<?php 
	try {
		if (! isset($_REQUEST['groupArr']) && ! isset($_REQUEST['jobArr']) && ! isset($_REQUEST['username'])) Object::shootException("The target recepient were not found");
		$groupArr = null;
		$jobArr = null;
		$usernameList = null;
		if (isset($_REQUEST['groupArr'])) $groupArr = $_REQUEST['groupArr'];
		if (isset($_REQUEST['jobArr'])) $jobArr = $_REQUEST['jobArr'];
		if (isset($_REQUEST['username'])) $usernameList = $_REQUEST['username'];
		$collection1 = System::getEmailsForMembers($database, $conn, $groupArr, $jobArr, $usernameList);
		$subject = $_REQUEST['subject'];
		$message = $_REQUEST['message'];
		//Testing now 
		$list1 = $collection1->getCollection();
		var_dump($list1);
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
	/*Proceed to results*/
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			The message has been sent successful <br/>
			<div style="font-size: 0.8em;">
				This means the system has sent the message successful, the system can not guarantee that the message has been delivered
			</div>
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in sending the message <br/>
			Details: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>
		</div>
		<div class="ui-sys-panel-footer"></div>
	</div>
<?php
	mysql_close($conn);
} else if ($page == "sendmail" && isset($_REQUEST['message']))	{
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
	$promise1 = new Promise();
	$promise1->setPromise(true);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Email Sender</div>
		<div class="ui-sys-panel-body">
			<form class="pure-form" id="form1" method="POST" action="<?= $thispage ?>">
				<input type="hidden" name="page" value="sendmail"/>
				<input type="hidden" name="subject" value="<?= $_REQUEST['subject'] ?>"/>
				<input type="hidden" name="message" value="<?= $_REQUEST['message'] ?>"/>
				<input type="hidden" name="report" value="io"/>
<!--Begin Group Selecting-->
	<div class="ui-sys-panel-data-container ui-sys-message ui-sys-sendmail">
		<div class="ui-sys-message-header">
			<label><input class="checkbox1" type="checkbox" name="chk_email_group" value="1"/>&nbsp;&nbsp;Send Email to Group</label>
		</div>
		<div class="ui-sys-message-body">
			<table class="pure-table pure-table-bordered ui-sys-data-table">
				<thead>
					<th></th>
					<th></th>
					<th>Group Name</th>
					<th>Parent Group</th>
					<th>Course Name</th>
					<th>Year</th>
					<th>Semester</th>
				</thead>
				<tbody></tbody>
			</table>
		</div>
		<div class="ui-sys-message-footer ui-sys-inline-controls-right">
			<input class="__a_load_panel_data" data-message-type="<?= MessageType::$__MESSAGE_TYPE_EMAIL ?>" data-name-prefix="groupArr" data-dialog-container="__id_general_dialog_holder" data-server-forward-path="../server/getgrouppreviewer.php" data-error-control="perror" type="button" value="Add Group" disabled/>
		</div>
	</div>
<!--End Group Selecting-->	
<!--Begin JobTitle Selecting-->
	<div class="ui-sys-panel-data-container ui-sys-message ui-sys-sendmail">
		<div class="ui-sys-message-header">
			<label><input class="checkbox1" type="checkbox" name="chk_email_job" value="1"/>&nbsp;&nbsp;Send Email to Job Titles</label>
		</div>
		<div class="ui-sys-message-body">
			<table class="pure-table pure-table-bordered ui-sys-data-table">
				<thead>
					<th></th>
					<th></th>
					<th>Job Title</th>
				</thead>
				<tbody></tbody>
			</table>
		</div>
		<div class="ui-sys-message-footer ui-sys-inline-controls-right">
			<input class="__a_load_panel_data" data-message-type="<?= MessageType::$__MESSAGE_TYPE_EMAIL ?>" data-name-prefix="jobArr" data-dialog-container="__id_general_dialog_holder" data-server-forward-path="../server/getjobtitlepreviewer.php" data-error-control="perror" type="button" value="Add Job Title" disabled/>
		</div>
	</div>
<!--End JobTitle Selecting-->	
<!--Begin Login Filling (usernames)-->
	<div class="ui-sys-panel-data-container ui-sys-message ui-sys-sendmail">
		<div class="ui-sys-message-header">
			<label><input class="checkbox1" type="checkbox" name="chk_email_login" value="1"/>&nbsp;&nbsp;Send Email to Users</label>
			<div class="ui-sys-warning">
				You have to supply the USERNAMES of users you want to send message to them<br/>
				Multiple usernames can be separated by commas <br/>
				Example: john1,joel@kpi.com,vlnd98
			</div>
		</div>
		<div class="ui-sys-message-body">
			<label>Usernames <input disabled type="text" name="username" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Username Lists <?= $msgL64Name ?>"/></label>
		</div>
		<div class="ui-sys-message-footer ui-sys-inline-controls-right">
			<input class="ui-sys-hidden __a_load_panel_data" data-name-prefix="username" data-dialog-container="__id_general_dialog_holder" data-server-forward-path="" data-error-control="perror" type="button" value="Add Username" disabled/>
		</div>
	</div>
<!--End Login Filling (usernames)-->
			</form>
			<div id="perror" class="ui-sys-error-message"></div>
		</div>
		<div class="ui-sys-panel-footer">
			<input type="button" value="Send Email" id="__add_record"/>
		</div>
	</div>
<script type="text/javascript">
(function($)	{
	$('#__add_record').on('click', function(event)	{
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
	mysql_close($conn);
} else if ($page == "sendmail")	{
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
	$promise1 = new Promise();
	$promise1->setPromise(true);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Email Sender</div>
		<div class="ui-sys-panel-body ui-sys-data-capture">
			<form class="pure-form" id="form1" method="POST" action="<?= $thispage ?>">
				<input type="hidden" name="page" value="sendmail"/>
				<table class="pure-table">
					<thead>
						<tr>
							<th colspan="2">Email Composer</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><label for="subject">Subject</label></td>
							<td><input type="text" size="48" id="subject" name="subject" required pattern="<?= $exprL48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL48Name ?>" validate_message="Subject <?= $msgL48Name ?>"/></td>
						</tr>
						<tr>
							<td><label for="message">Message</label></td>
							<td><textarea name="message" id="message" rows="4" cols="80" required pattern="<?= $exprL512Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL512Name ?>" validate_message="Message : <?= $msgL512Name ?>"></textarea></td>
						</tr>
						<tr>
							<td colspan="2" id="perror" class="ui-sys-error-message"></td>
						</tr>
						<tr>
							<td></td>
							<td><input id="__add_record" type="button" value="Proceed"/></td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
		<div class="ui-sys-panel-footer"></div>
	</div>
<script type="text/javascript">
(function($)	{
	$('#__add_record').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
	mysql_close($conn);
} /*END SEND MAIL*/else if ($page == "timetable")	{
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
	$promise1 = new Promise();
	$promise1->setPromise(true);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">My Timetable(s)</div>
		<div class="ui-sys-panel-body">
<?php 
	$listIAmHosting1 = null;
	$listIAmHosted1 = null;
	try {
		$listIAmHosting1 = Timetable::getTimetableListIAmHosting($database, $conn, $login1->getLoginId(), $systemTime1);
		$listIAmHosted1 = Timetable::getTimetableListIAmHosted($database, $conn, $login1->getLoginId(), $systemTime1);
		if (is_null($listIAmHosting1) && is_null($listIAmHosted1)) Object::shootException("Sorry, There is no any timetable related to you");
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
	if ($promise1->isPromising())	{
?>
		<div class="ui-sys-tabs">
			<ul>
<?php 
	if (! is_null($listIAmHosting1))	{
?>
		<li><a href="#__zk_d001">My Teaching Timetable</a></li>
<?php
	}
	if (! is_null($listIAmHosted1))	{
?>
		<li><a href="#__zk_d002">My Timetable</a></li>
<?php
	}
	if (! is_null($listIAmHosting1))	{
?>
		<div id="__zk_d001">
<?php 
			try {
				$systemTime1->setExtraFilter("1");
				$timetableToDraw = Timetable::drawMyTimetable($database, $conn, $listIAmHosting1, $systemTime1, $__systemDayOffset);
				echo $timetableToDraw;
			} catch (Exception $e)	{
?>
				<div class="ui-state-error">
					There were problems in dispaying the timetable <br/>
					Details: <?= $e->getMessage() ?>
				</div>
<?php
			}
?>		
		</div>
<?php	
	}
	if (! is_null($listIAmHosted1))	{
?>
		<div id="__zk_d002">
<?php 
			try {
				$systemTime1->setExtraFilter("0");
				$timetableToDraw = Timetable::drawMyTimetable($database, $conn, $listIAmHosted1, $systemTime1, $__systemDayOffset);
				echo $timetableToDraw;
			} catch (Exception $e)	{
?>
				<div class="ui-state-error">
					There were problems in dispaying the timetable <br/>
					Details: <?= $e->getMessage() ?>
				</div>
<?php
			}
?>		
		</div>
<?php	
	}
?>			
			</ul>
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			Sorry; It seems there is no any timetable related to you<br/>
			Details: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>
		</div>
		<div class="ui-sys-panel-footer"></div>
	</div>
<script type="text/javascript">
(function($)	{
	$('div.ui-sys-timetable-container').on('click', 'a.ui-sys-record-attendance', function(event)	{
		event.preventDefault();
		var $button1 = $(this).closest('a.ui-sys-record-attendance');
		if (! $button1.length) return;
		var data1 = "";
		try {
			data1 = $.parseJSON($button1.attr('data-timetable-popup'));
		} catch (err)	{
			return "Data parsing error " + err.message;
		}
		window.location.assign("<?= $thispage ?>?page=manageattendance&id=" + data1.timetableId);
	});
})(jQuery);
</script>
<?php
	mysql_close($conn);
} else if ($page == "theme" && isset($_REQUEST['report']))	{
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
	$enableUpdate = false;
	$promise1 = new Promise();
	$promise1->setPromise(true);
	$login1 = null;
	try {
		$login1 = new Login($database, $_SESSION['login'][0]['id'], $conn);
		$login1->setTheme($_REQUEST['themeId'], $conn);
		$login1->commitUpdate();
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Your System Theme<br/>Report</div>
		<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			You have successful updated your system them to <u><i><?= $login1->getTheme()->getThemeName() ?></i></u>
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in updating your system theme <br/>
			Error Details: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>		
		</div>
		<div class="ui-sys-panel-footer"></div>
	</div>
<?php
	mysql_close($conn);
} else if ($page == "theme")	{
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
	$enableUpdate = false;
	$promise1 = new Promise();
	$promise1->setPromise(true);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Your System Theme<br/>Setup</div>
		<div class="ui-sys-panel-body ui-sys-data-capture">
			<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
				<input type="hidden" name="page" value="theme"/>
				<input type="hidden" name="report" value="true"/>
				<div class="pure-control-group">
					<label for="themeId">Theme </label>
					<select id="themeId" name="themeId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Theme">
						<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Theme::loadAllData($database, $conn);
	$themeId = "";
	if (! is_null($login1->getTheme()))	{
		$themeId = $login1->getTheme()->getThemeId();
	}
	foreach ($list1 as $alist1)	{
		$selected = "";
		if ($alist1['id'] == $themeId) $selected = "selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
					</select>
				</div>
				<div class="pure-controls">
					<span id="perror" class="ui-sys-error-message"></span>
				</div>
				<div class="pure-controls">
					<input id="__add_record" type="button" value="Update your System Theme"/>
				</div>
			</form>
		</div>
		<div class="ui-sys-panel-footer"></div>
	</div>
<script type="text/javascript">
(function($)	{
	$('#__add_record').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
	mysql_close($conn);
} else if ($page == "firstdayofaweek" && isset($_REQUEST['report']))	{
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
	$enableUpdate = false;
	$promise1 = new Promise();
	$promise1->setPromise(true);
	$login1 = null;
	try {
		$login1 = new Login($database, $_SESSION['login'][0]['id'], $conn);
		$login1->setFirstDayOfAWeek($_REQUEST['dayId']);
		$login1->commitUpdate();
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">First Day of A Week<br/>Report</div>
		<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			You have successful updated the first day of a week to <u><i><?= $login1->getFirstDayOfAWeek()->getDayName() ?></i></u>
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in updating the first day of a week<br/>
			Error Details: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>
		</div>
		<div class="ui-sys-panel-footer"></div>
	</div>
<?php
	mysql_close($conn);
} else if ($page == "firstdayofaweek")	{
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">First Day of A Week <br/>Setup</div>
		<div class="ui-sys-panel-body ui-sys-data-capture">
			<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
				<input type="hidden" name="page" value="firstdayofaweek"/>
				<input type="hidden" name="report" value="true"/>
				<div class="pure-control-group">
					<label for="dayId">First Day of A Week </label>
					<select id="dayId" name="dayId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select First Day of A Week">
						<option value="_@32767@_">--select--</option>
<?php 
	$list1 = DaysOfAWeek::loadAllData($database, $conn);
	$dayId = "";
	if (! is_null($login1->getFirstDayOfAWeek()))	{
		$dayId = $login1->getFirstDayOfAWeek()->getDayId();
	}
	foreach ($list1 as $alist1)	{
		$selected = "";
		if ($alist1['id'] == $dayId) $selected = "selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
					</select>
				</div>
				<div class="pure-controls">
					<span id="perror" class="ui-sys-error-message"></span>
				</div>
				<div class="pure-controls">
					<input id="__add_record" type="button" value="Update First Day of A Week"/>
				</div>
			</form>
		</div>
		<div class="ui-sys-panel-footer"></div>
	</div>
<script type="text/javascript">
(function($)	{
	$('#__add_record').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
	mysql_close($conn);
} else if ($page == "personalize")	{
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Personalize your System</div>
		<div class="ui-sys-panel-body ui-sys-data-capture">
			<table class="pure-table pure-table-horizontal">
				<thead>
					<tr>
						<th></th>
						<th>Details of Operation</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>1</td>
						<td>
							Change <b>First Day Of A Week</b>; This will change the firstday of a week to all your calendar
						</td>
						<td><a class="button-link" href="<?= $thispage ?>?page=firstdayofaweek" title="Change First Day of A Week">Change</a></td>
					</tr>
					<tr>
						<td>2</td>
						<td>
							Change your <b>theme</b>; This will change the Appearance of your pages
						</td>
						<td><a class="button-link" href="<?= $thispage ?>?page=theme" title="Change your System Theme">Change</a></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="ui-sys-panel-footer"></div>
	</div>
<?php
} else {
	//Default Landing Page 
?>
	<!--Basic Module-->
<?php 
	try {
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$ulogin1 = new Login($database, $login1->getLoginId(), $conn);
		$ulogin1->putData('profileObject', $profile1);
		//Do Correction Here For the Group 
		if ($ulogin1->isStudent() &&  ! $registeredUser1->isInTheCorrectGroup())	{
			//Now Allocate Properly
			$promise1 = Student::reallocateStudent($database, $conn, new Student($database, $registeredUser1->getStudentId(), $conn));
			if (! $promise1->isPromising()) Object::shootException($promise1->getReason());
			//Now save permanently 
			$ulogin1->setGroup($promise1->getResults());
			$ulogin1->commitUpdate();
		}
		$module1 = $ulogin1->loadMyProfileModule();
		mysql_close($conn);
		echo $module1."<br/>";
	} catch (Exception $e)	{
		
	}
?>
	<!--Default Module-->
	<div class="ui-widget module">
		<div class="ui-widget-content">
			<div class="ui-widget-header">General Instructions</div>
			Dear <?= $login1->getFullname() ?>, you are welcome to our <i><?= $profile1->getSystemName() ?></i> for <i><?= $profile1->getProfileName() ?></i><br/>
			You are now on the profile page of the system, in this page your can navigate and change basic settings for your profile <br/><br/>
			To get fully functionality of the system authorized to you, you may click <b>My System</b> on the left hand panel or you may <b><a href="../system/">Click Here</a></b>
		</div>
	</div>
<?php
	//BEGIN: Custom Module
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not Connect to a database services");
	//My List Awaiting to be Approved Module 
	$window1 = ApprovalSequenceData::getMyApprovalSequenceWaitingToBeApprovedWindow($database, $conn, $login1->getLoginId());
	if (! is_null($window1)) echo "<br/>".$window1;
	//My List Awaiting my Approval 
	$window1 = ApprovalSequenceData::getApprovalSequenceAwaitingMyApprovalWindow($database, $conn, $systemTime1, $login1->getLoginId(), "../server/approvetheapprovalsequence.php", "../server/rejecttheapprovalsequence.php", $thispage, "../sysimage/buttonsearch.png", $__request_awaiting_my_approval_searchtext);
	if (! is_null($window1)) echo "<br/>".$window1;
	mysql_close($conn);
	//END: Custom Module 
}
/*LIFE END Potential Business for this page is ending here*/
	} //end-of usingi-else-defaultPassword
?>
<!--End All Modules Should not be loaded beyond this point-->
		</div>
	</div>
	<div class="ui-sys-clearboth">&nbsp;</div>
	<div id="__ui_common_errors" class="ui-sys-error-message"></div>
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
<?php 
	//Update Login Time 
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
	try {
		$login1 = new Login($database, $login1->getLoginId(), $conn);
		$login1->setLastLoginTime($systemDate1->getDateAndTimeString());
		$login1->commitUpdate();
	} catch (Exception $e)	{ die($e->getMessage()); }
	mysql_close($conn);
?>
</html>

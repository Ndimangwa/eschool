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
	die($e->getMessage());
}
mysql_close($conn);
Object::$iconPath="../sysimage/";
if (! $profile1->isInstallationComplete())	{
	header("Location: ../installation/");
	exit();
}
if ($login1->isUsingDefaultPassword())	{
	header("Location: ../profile/");
	exit();
}
$thispage = $_SERVER['PHP_SELF'];
$page = null;
if (isset($_REQUEST['page'])) $page = $_REQUEST['page'];
//You may use extraFilter to store path of the photo , user the setExtraFilter of login
if ($login1->getUserType()->getTypeCode() == UserType::$__COURSEINSTRUCTOR)	{
	$login1->setExtraFilter("../data/instructors/photo/".$login1->getPhoto());
} else if ($login1->getUserType()->getTypeCode() == UserType::$__STUDENT)	{
	$login1->setExtraFilter("../data/students/photo/".$login1->getPhoto());
} else if ($login1->getUserType()->getTypeCode() == UserType::$__USER)	{
	$login1->setExtraFilter("../data/users/photo/".$login1->getPhoto());
} else {
	die("The System Could not identify this Type of the User");
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
<link rel="stylesheet" type="text/css" media="all" href="../client/plugin/wickedpicker/wp/wickedpicker.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/plugin/printArea/PrintArea.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/plugin/fileMenu/fileMenu.min.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/css/purecss/pure-min.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/css/site.css"/>
<style type="text/css">

</style>
<script type="text/javascript" src="../client/jquery.js"></script>
<script type="text/javascript" src="../client/jquery-ui-1.11.3/jquery-ui.js"></script>
<script type="text/javascript" src="../client/jquery-easy-ticker-master/jquery.easy-ticker.js"></script>
<script type="text/javascript" src="../client/plugin/twbsPagination/jquery.twbsPagination.min.js"></script>
<script type="text/javascript" src="../client/plugin/wickedpicker/wp/wickedpicker.js"></script>
<script type="text/javascript" src="../client/plugin/printArea/jquery.PrintArea.js"></script>
<script type="text/javascript" src="../client/plugin/fileMenu/fileMenu.js"></script>
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
		$('.timepicker').wickedpicker({
			twentyFour: true,
			upArrow: 'wickedpicker__controls__control-up',
			downArrow: 'wickedpicker__controls__control-down',
			title: 'Pick Time'
		});
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
	<div class="ui-sys-menubar">
		<ul class="ui-sys-navigation-menu">
<?php 
		if (Authorize::isAllowable($config, "menu_system", "normal", "donotsetlog", "-1", "-1"))	{
?>
			<li>System
				<ul>
					<li><i class="fa fa-fw fa-file"></i> <a href="./">Home<kbd>Ctrl+H</kbd></a></li>
					<li><i class="fa fa-fw fa-file"></i> <a href="../profile/">Profile</a><kbd>Alt+P</kbd></li>
					<li><i class="fa fa-fw fa-file"></i> Settings
						<ul>
							<li><i class="fa fa-fw fa-file"></i> <a href="#">Last Resort for Don't Care</a></li>
							<li><i class="fa fa-fw fa-file"></i> <a href="#">System Policies</a></li>
<?php 
						if ($login1->isRoot() && Authorize::isAllowable($config, "manageprofile", "normal", "donotsetlog", "-1", "-1"))	{
?>
							<li><i class="fa fa-fw fa-file"></i> <a href="<?= $thispage ?>?page=manageprofile">System Settings</a></li>
<?php 
						}
?>
						</ul>
					</li>
<?php 
				if (Authorize::isAllowable($config, "managesystemlogs", "normal", "donotsetlog", "-1", "-1"))	{
?>
					<li><i class="fa fa-fw fa-file"></i> <a href="<?= $thispage ?>?page=managesystemlogs">System Logs</a></li>
<?php 
				}
?>
					<li><i class="fa fa-fw fa-file"></i> <a class="ui-sys-logout-control" data-error-control="__ui_common_errors" data-next-page="../" data-server-directory="../server" title="Logout, <?= $login1->getFullname() ?>" href="#">Logout</a><kbd>Esc</kbd></li>
				</ul>
			</li>
<?php 
		}
		if (Authorize::isAllowable($config, "menu_users", "normal", "donotsetlog", "-1", "-1"))	{
?>
			<li>Users
				<ul>
<?php 
				if (Authorize::isAllowable($config, "managegroup", "normal", "donotsetlog", "-1", "-1"))	{
?>
					<li><i class="fa fa-fw fa-file"></i> <a href="<?= $thispage ?>?page=managegroup">Groups<kbd>Alt+G</kbd></a></li>
<?php
				}
				if (Authorize::isAllowable($config, "managejobtitle", "normal", "donotsetlog", "-1", "-1"))	{
?>
					<li><i class="fa fa-fw fa-file"></i> <a href="<?= $thispage ?>?page=managejobtitle">Job Titles<kbd>Alt+J</kbd></a></li>
<?php 
				}
				if (Authorize::isAllowable($config, "managecourseinstructor", "normal", "donotsetlog", "-1", "-1"))	{
?>
					<li><i class="fa fa-fw fa-file"></i> <a href="<?= $thispage ?>?page=managecourseinstructor">Course Instructors<kbd>Alt+I</kbd></a></li>
<?php 
				}
?>
					<li><i class="fa fa-fw fa-file"></i> Students<kbd>Alt+U</kbd></li>
<?php 
				if (Authorize::isAllowable($config, "manageuser", "normal", "donotsetlog", "-1", "-1"))	{
?>
					<li><i class="fa fa-fw fa-file"></i> <a href="<?= $thispage ?>?page=manageuser">System Users</a></li>
<?php 
				}
?>
				</ul>
			</li>
<?php 
		}
		if (Authorize::isAllowable($config, "menu_courses", "normal", "donotsetlog", "-1", "-1"))	{
?>
			<li>Courses
				<ul>
<?php 
				if (Authorize::isAllowable($config, "managedepartment", "normal", "donotsetlog", "-1", "-1"))	{
?>
					<li><i class="fa fa-fw fa-file"></i> <a href="<?= $thispage ?>?page=managedepartment">Departments</a></li>
<?php 
				}
				if (Authorize::isAllowable($config, "managecourse", "normal", "donotsetlog", "-1", "-1"))	{
?>
					<li><i class="fa fa-fw fa-file"></i> <a href="<?= $thispage ?>?page=managecourse">Courses</a></li>
<?php 
				}
				if (Authorize::isAllowable($config, "managesubject", "normal", "donotsetlog", "-1", "-1"))	{
?>
					<li><i class="fa fa-fw fa-file"></i> <a href="<?= $thispage ?>?page=managesubject">Subjects/Modules</a></li>
<?php 
				}
				if (Authorize::isAllowable($config, "managecourseandsubjecttransaction", "normal", "donotsetlog", "-1", "-1"))	{
?>
					<li><i class="fa fa-fw fa-file"></i> <a href="<?= $thispage ?>?page=managecourseandsubjecttransaction">Course & Subject Mapping</a></li>
<?php 
				}
?>
				</ul>
			</li>
<?php 
		}
		if (Authorize::isAllowable($config, "menu_registration", "normal", "donotsetlog", "-1", "-1"))	{
?>
			<li>Registration
				<ul>
					<li><i class="fa fa-fw fa-file"></i> Open/Close Registration</li>
					<li><i class="fa fa-fw fa-file"></i> Student Admission</li>
				</ul>
			</li>
<?php 
		}
		if (Authorize::isAllowable($config, "menu_accademics", "normal", "donotsetlog", "-1", "-1"))	{
?>
			<li>Accademics
				<ul>
<?php 
				if (Authorize::isAllowable($config, "manageeducationlevel", "normal", "donotsetlog", "-1", "-1"))	{
?>
					<li><i class="fa fa-fw fa-file"></i> <a href="<?= $thispage ?>?page=manageeducationlevel">Education Level</a></li>
<?php 
				}
?>
					<li><i class="fa fa-fw fa-file"></i> Grading Policy</li>
				</ul>
			</li>
<?php 
		}
		if (Authorize::isAllowable($config, "menu_examination", "normal", "donotsetlog", "-1", "-1"))	{
?>
			<li>Examination
				<ul>
					<li><i class="fa fa-fw fa-file"></i> Examination Groups</li>
					<li><i class="fa fa-fw fa-file"></i> Examination</li>
					<li><i class="fa fa-fw fa-file"></i> Examination Numbers 
						<ul>
							<li><i class="fa fa-fw fa-file"></i> Number Filters(ByPass)</li>
							<li><i class="fa fa-fw fa-file"></i> Number Scope</li>
							<li><i class="fa fa-fw fa-file"></i> Examination Number</li>
						</ul>
					</li>
					<li><i class="fa fa-fw fa-file"></i> Results
						<ul>
							<li><i class="fa fa-fw fa-file"></i> Publish Results</li>
							<li><i class="fa fa-fw fa-file"></i> View My Results</li>
							<li><i class="fa fa-fw fa-file"></i> Results Changing Policy</li>
						</ul>
					</li>
				</ul>
			</li>
<?php 
		}
		if (Authorize::isAllowable($config, "menu_resources", "normal", "donotsetlog", "-1", "-1"))	{
?>
			<li>Resources
				<ul>
<?php 
				if (Authorize::isAllowable($config, "managevenue", "normal", "donotsetlog", "-1", "-1"))	{
?>
					<li><i class="fa fa-fw fa-file"></i> <a href="<?= $thispage ?>?page=managevenue">Venue</a></li>
<?php 
				}
				if (Authorize::isAllowable($config, "manageholiday", "normal", "donotsetlog", "-1", "-1"))	{
?>
					<li><i class="fa fa-fw fa-file"></i> <a href="<?= $thispage ?>?page=manageholiday">Holidays</a></li>
<?php 
				}
				if (Authorize::isAllowable($config, "manageschedule", "normal", "donotsetlog", "-1", "-1"))	{
?>
					<li><i class="fa fa-fw fa-file"></i> <a href="<?= $thispage ?>?page=manageschedule">Schedule</a></li>
<?php 
				}
				if (Authorize::isAllowable($config, "managetimetable", "normal", "donotsetlog", "-1", "-1"))	{
?>
					<li><i class="fa fa-fw fa-file"></i> <a href="<?= $thispage ?>?page=managetimetable">Timetable</a></li>
<?php 
				}
?>
					<li><i class="fa fa-fw fa-file"></i> Human Resource
						<ul>
							<li><i class="fa fa-fw fa-file"></i> Leave Approval Group</li>
							<li><i class="fa fa-fw fa-file"></i> Leave Approval Schema</li>
							<li><i class="fa fa-fw fa-file"></i> Apply for Leave</li>
							<li><i class="fa fa-fw fa-file"></i> Grant Leave</li>
						</ul>
					</li>
				</ul>
			</li>
<?php 
		}
		if (Authorize::isAllowable($config, "menu_accounts", "normal", "donotsetlog", "-1", "-1"))	{
?>
			<li>Accounts 
				<ul>
					<li><i class="fa fa-fw fa-file"></i> Fee Structure</li>
					<li><i class="fa fa-fw fa-file"></i> Invoices</li>
					<li><i class="fa fa-fw fa-file"></i> Fee Payer Type</li>
					<li><i class="fa fa-fw fa-file"></i> Payment History</li>
				</ul>
			</li>
<?php 
		}
		if (Authorize::isAllowable($config, "menu_messaging", "normal", "donotsetlog", "-1", "-1"))	{
?>
			<li>Messaging
				<ul>
					<li><i class="fa fa-fw fa-file"></i> Messaging Policy</li>
				</ul>
			</li>
<?php 
		}
		if (Authorize::isAllowable($config, "menu_attendance", "normal", "donotsetlog", "-1", "-1"))	{
?>
			<li>Attendance
				<ul>
					<li><i class="fa fa-fw fa-file"></i> View Attendance</li>
				</ul>
			</li>
<?php 
		}
		if (Authorize::isAllowable($config, "menu_tools", "normal", "donotsetlog", "-1", "-1"))	{
?>
			<li>Tools
				<ul>
<?php 
				if (Authorize::isAllowable($config, "managedaysofaweek", "normal", "donotsetlog", "-1", "-1"))	{
?>
					<li><i class="fa fa-fw fa-file"></i> <a href="<?= $thispage ?>?page=managedaysofaweek">Days of A Week</a></li>
<?php
				}
				if (Authorize::isAllowable($config, "managemonthofayear", "normal", "donotsetlog", "-1", "-1"))	{
?>
					<li><i class="fa fa-fw fa-file"></i> <a href="<?= $thispage ?>?page=managemonthofayear">Month Of A Year</a></li>
<?php				
				}
				if (Authorize::isAllowable($config, "managetheme", "normal", "donotsetlog", "-1", "-1"))	{
					
				}
				if (Authorize::isAllowable($config, "managecountry", "normal", "donotsetlog", "-1", "-1"))	{
					
				}
				if (Authorize::isAllowable($config, "managesecurityquestion", "normal", "donotsetlog", "-1", "-1"))	{
					
				}
				if (Authorize::isAllowable($config, "manageusertype", "normal", "donotsetlog", "-1", "-1"))	{
					
				}
				if (Authorize::isAllowable($config, "manageuserstatus", "normal", "donotsetlog", "-1", "-1"))	{
					
				}
				if (Authorize::isAllowable($config, "managesex", "normal", "donotsetlog", "-1", "-1"))	{
					
				}
				if (Authorize::isAllowable($config, "managemarital", "normal", "donotsetlog", "-1", "-1"))	{
					
				}
?>
				</ul>
			</li>
<?php
		}
		if (Authorize::isAllowable($config, "menu_help", "normal", "donotsetlog", "-1", "-1"))	{
?>
			<li>Help
				<ul>
					<li><i class="fa fa-fw fa-file"></i> About Us</li>
					<li><i class="fa fa-fw fa-file"></i> User Guide Manual</li>
					<li><i class="fa fa-fw fa-file"></i> Contact Us</li>
				</ul>
			</li>
<?php 
		} //end-of Authorize Menus sequence
?>
		</ul>
	</div>
<script type="text/javascript">
(function($)	{
	$('ul.ui-sys-navigation-menu').fileMenu({ slideSpeed: 200 });
})(jQuery);	
</script>
<!--BEGINNING OF PUTTING CODE-->
<div class="ui-sys-bg-grid-green">
<?php
	/*Timetable BEGIN*/if ($page == "managetimetable_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['detach_groups']) && Authorize::isAllowable($config, "managetimetable_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$timetable1 = null;
		$headerText = "UNKNOWN";
		try {
			$timetable1 = new Timetable($database, $_REQUEST['id'], $conn);
			$headerText = "De-Assign Event Targets Map from ".$timetable1->getActivityName();
			if (! isset($_REQUEST['did'])) Object::shootException("There were nothing to Dettach");
			$listToUpdate = Object::detachIdListFromObjectList($timetable1->getGroupList(), $_REQUEST['did']);
			$timetable1->setGroupList($listToUpdate);
			$timetable1->commitUpdate();
			$enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header"></div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Group List Attachment were removed successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in De-Attaching Group List <br/>
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
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managetimetable_edit", "Detach Groups for ".$timetable1->getActivityName());
	} else if ($page == "managetimetable_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['attach_groups']) && Authorize::isAllowable($config, "managetimetable_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$timetable1 = null;
		$headerText = "UNKNOWN";
		try {
			$timetable1 = new Timetable($database, $_REQUEST['id'], $conn);
			$headerText = "Assign Group Map to ".$timetable1->getActivityName();
			if (! isset($_REQUEST['did'])) Object::shootException("There were nothing to Attach");
			$listToUpdate = Object::attachIdListToObjectList($timetable1->getGroupList(), $_REQUEST['did']);
			$timetable1->setGroupList($listToUpdate);
			$timetable1->commitUpdate();
			$enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header"></div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Group List Attachment were done successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Attaching Group List <br/>
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
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managetimetable_edit", "Attach Groups for ".$timetable1->getActivityName());
	} else if ($page == "managetimetable_edit" && isset($_REQUEST['id']) && isset($_REQUEST['attach_groups']) && Authorize::isAllowable($config, "managetimetable_edit", "normal", "setlog", "-1", "-1")) {
		$conn  = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$timetable1 = null;
		$headerText = "UNKNOWN";
		try {
			$timetable1 = new Timetable($database, $_REQUEST['id'], $conn);
			$headerText = "Assign Group Map to ".$timetable1->getActivityName();
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header"><?= $headerText ?></div>
			<div class="ui-sys-panel-body ui-sys-search-results">
<?php 
	if ($promise1->isPromising())	{
/*Begin of BLOCK ONE */
?>
		<!--Begin of Cross Reference List Insertion-->
<?php 
	if (! is_null($timetable1->getGroupList()) && sizeof($timetable1->getGroupList()) > 0)	{
		$objectList1 = $timetable1->getGroupList();
?>
			<form method="POST" action="<?= $thispage ?>">
				<input type="hidden" name="page" value="managetimetable_edit"/>
				<input type="hidden" name="id" value="<?= $timetable1->getTimetableId() ?>"/>
				<input type="hidden" name="report" value="true"/>
				<input type="hidden" name="detach_groups" value="true"/>
				<table class="pure-table pure-table-aligned ui-sys-search-results ui-sys-search-results-1">
					<thead>
					<tr>
						<th colspan="7">Targeted Groups</th>
					</tr>
					<tr>
						<th></th>
						<th>S/N</th>
						<th>Group Name</th>
						<th>Parent Group</th>
						<th>Course</th>
						<th>Year</th>
						<th>Semester</th>
					</tr>
				</thead>
<?php 
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	foreach ($objectList1 as $obj1)	{
		if (is_null($obj1)) continue;
		if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
			$numberOfTBodies++;
			$tbodyClosed = false;
		} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
			$numberOfTBodies++;
			$perTbodyCounter = 0;
		}
		//In All Cases we need to display tr
		$pureTableOddClass = "";
		if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
		<tr class="<?= $pureTableOddClass ?>">
			<td><input type="checkbox" name="did[<?= $sqlReturnedRowCount ?>]" value="<?= $obj1->getGroupId() ?>"/></td>
			<td><?= $sqlReturnedRowCount + 1 ?></td>
			<td><?= $obj1->getGroupName() ?></td>
			<td>
<?php 
	if (! is_null($obj1->getParentGroup()))	{
		echo $obj1->getParentGroup()->getGroupName();
	}
?>			
			</td>
			<td>
<?php 
	if (! is_null($obj1->getCourse()))	{
		echo $obj1->getCourse()->getCourseName();
	}
?>			
			</td>
			<td><?= $obj1->getYear() ?></td>
			<td><?= $obj1->getSemester() ?></td>
		</tr>
<?php
		$perTbodyCounter++;
		$sqlReturnedRowCount++;
	}
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
?>
				</table>
				<div class="ui-sys-right">
					<input type="submit" value="Detach Groups"/>
				</div>
			</form>
			<ul class="ui-sys-pagination-1"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination-1').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-search-results-1 tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>

<?php
	}
?>
			<!--End of Cross Reference List Insertion-->
<?php
/*End of BLOCK ONE*/
/*Begin of BLOCK TWO -- Extra*/
?>
	<div style="padding: 1px; border: 1px gold dotted;">
		<!--Content of Block Two Extra BEgins-->
		<!--Block ONE controls for searching-->
				<div class="ui-sys-search-container pure-form pure-transaction-control">
					<input type="text" title="At Least Three Characters should be supplied" required placeholder="ABC" size="32"/>
					<a title="Click to Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=managetimetable_edit&id=<?= $timetable1->getTimetableId() ?>&attach_groups=true" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
				</div>
<!--Block ONE ends-->	
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
		//saving Authorization Rules 
?>
<!--Block Two, Search Results-->
<div class="ui-sys-search-results">
	<label id="statustextlabel"></label>
	<form method="POST" action="<?= $thispage ?>">
		<input type="hidden" name="page" value="managetimetable_edit"/>
		<input type="hidden" name="id" value="<?= $timetable1->getTimetableId() ?>"/>
		<input type="hidden" name="report" value="true"/>
		<input type="hidden" name="attach_groups" value="true"/>
		<table class="pure-table ui-sys-search-results-2">
			<thead>
				<tr>
					<th></th>
					<th>S/N</th>
					<th>Group Name</th>
					<th>Parent Group</th>
					<th>Course</th>
					<th>Year</th>
					<th>Semester</th>
				</tr>
			</thead>
<?php 
	$query = Group::getQueryText();
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$result = mysql_db_query($database, $query, $conn) or die("Could not fetch transaction data from the storage");
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	while (list($id) = mysql_fetch_row($result))	{
		if ($sqlReturnedRowCount > $maximumReturnedRows)	{
			//Finalize All House Keeping Here
			break;
		}
		$transaction1 = null;
		try {
			$transaction1 = new Group($database, $id, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated per objects 
		if ($transaction1->searchMatrix($matrix1)->evaluateResult())	{
			if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
				$numberOfTBodies++;
				$tbodyClosed = false;
			} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
				$numberOfTBodies++;
				$perTbodyCounter = 0;
			}
			//In All Cases we need to display tr
			$pureTableOddClass = "";
			if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
			<tr class="<?= $pureTableOddClass ?>">
				<td><input type="checkbox" name="did[<?= $sqlReturnedRowCount ?>]" value="<?= $transaction1->getGroupId() ?>"/></td>
				<td><?= $sqlReturnedRowCount + 1 ?></td>
			<td><?= $transaction1->getGroupName() ?></td>
			<td>
<?php 
	if (! is_null($transaction1->getParentGroup()))	{
		echo $transaction1->getParentGroup()->getGroupName();
	}
?>			
			</td>
			<td>
<?php 
	if (! is_null($transaction1->getCourse()))	{
		echo $transaction1->getCourse()->getCourseName();
	}
?>			
			</td>
			<td><?= $transaction1->getYear() ?></td>
			<td><?= $transaction1->getSemester() ?></td>
			</tr>
<?php
			$perTbodyCounter++;
			$sqlReturnedRowCount++;
		}//end-evaluate-results
	}//end-while
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
?>
		</table>
		<div class="ui-sys-right">
			<input type="submit" value="Attach Group"/>
		</div>
	</form>
	<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
	<ul class="ui-sys-pagination-2"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination-2').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-search-results-2 tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
</div>
<!--Block Two Ends-->
<?php
	} //end-if-searchtext
?>		
		<!--Content of Block Two Extra Ends-->
	</div>
<?php
/*End of BLOCK TWO -- Extra*/
	} else {
?>
		<div class="ui-state-error">
			There were problems in the initial setup of Assignment <br/>
			Reason : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managetimetable_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['detach_login']) && Authorize::isAllowable($config, "managetimetable_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$timetable1 = null;
		$headerText = "UNKNOWN";
		try {
			$timetable1 = new Timetable($database, $_REQUEST['id'], $conn);
			$headerText = "De-Assign Event Creators Map from ".$timetable1->getActivityName();
			if (! isset($_REQUEST['did'])) Object::shootException("There were nothing to Attach");
			$listToUpdate = Object::detachIdListFromObjectList($timetable1->getInstructorList(), $_REQUEST['did']);
			$timetable1->setInstructorList($listToUpdate);
			$timetable1->commitUpdate();
			$enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header"></div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Instructor List Attachment were removed successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in De-Attaching Instructor List <br/>
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
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managetimetable_edit", "Detach Instructors for ".$timetable1->getActivityName());
	} else if ($page == "managetimetable_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['attach_login']) && Authorize::isAllowable($config, "managetimetable_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$timetable1 = null;
		$headerText = "UNKNOWN";
		try {
			$timetable1 = new Timetable($database, $_REQUEST['id'], $conn);
			$headerText = "Assign Instructor Map to ".$timetable1->getActivityName();
			if (! isset($_REQUEST['did'])) Object::shootException("There were nothing to Attach");
			$listToUpdate = Object::attachIdListToObjectList($timetable1->getInstructorList(), $_REQUEST['did']);
			$timetable1->setInstructorList($listToUpdate);
			$timetable1->commitUpdate();
			$enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header"></div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Instructor List Attachment were done successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Attaching Instructor List <br/>
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
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managetimetable_edit", "Attach Instructors for ".$timetable1->getActivityName());
	} else if ($page == "managetimetable_edit" && isset($_REQUEST['id']) && isset($_REQUEST['attach_login']) && Authorize::isAllowable($config, "managetimetable_edit", "normal", "setlog", "-1", "-1")) {
		$conn  = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$timetable1 = null;
		$headerText = "UNKNOWN";
		try {
			$timetable1 = new Timetable($database, $_REQUEST['id'], $conn);
			$headerText = "Assign Instructor Map to ".$timetable1->getActivityName();
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header"><?= $headerText ?></div>
			<div class="ui-sys-panel-body ui-sys-search-results">
<?php 
	if ($promise1->isPromising())	{
/*Begin of BLOCK ONE */
?>
		<!--Begin of Cross Reference List Insertion-->
<?php 
	if (! is_null($timetable1->getInstructorList()) && sizeof($timetable1->getInstructorList()) > 0)	{
		$objectList1 = $timetable1->getInstructorList();
?>
			<form method="POST" action="<?= $thispage ?>">
				<input type="hidden" name="page" value="managetimetable_edit"/>
				<input type="hidden" name="id" value="<?= $timetable1->getTimetableId() ?>"/>
				<input type="hidden" name="report" value="true"/>
				<input type="hidden" name="detach_login" value="true"/>
				<table class="pure-table pure-table-aligned ui-sys-search-results ui-sys-search-results-1">
					<thead>
						<tr>
						<th colspan="6">Timetabled By</th>
						</tr>
						<tr>
							<th></th>
							<th></th>
							<th>Name</th>
							<th>Email</th>
							<th>Phone</th>
							<th>Status</th>
						</tr>
					</thead>
<?php 
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	foreach ($objectList1 as $obj1)	{
		if (is_null($obj1)) continue;
		if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
			$numberOfTBodies++;
			$tbodyClosed = false;
		} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
			$numberOfTBodies++;
			$perTbodyCounter = 0;
		}
		//In All Cases we need to display tr
		$pureTableOddClass = "";
		if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
		<tr class="<?= $pureTableOddClass ?>">
			<td><input type="checkbox" name="did[<?= $sqlReturnedRowCount ?>]" value="<?= $obj1->getLoginId() ?>"/></td>
			<td><?= $sqlReturnedRowCount + 1 ?></td>
			<td><?= $obj1->getFullname() ?></td>
			<td><?= $obj1->getEmail() ?></td>
			<td><?= $obj1->getPhone() ?></td>
			<td>
<?php 
	if (! is_null($obj1->getUserStatus()))	{
		echo $obj1->getUserStatus()->getStatusName();
	}
?>			
			</td>
		</tr>
<?php
		$perTbodyCounter++;
		$sqlReturnedRowCount++;
	}
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
?>
				</table>
				<div class="ui-sys-right">
					<input type="submit" value="Detach Instructors"/>
				</div>
			</form>
			<ul class="ui-sys-pagination-1"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination-1').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-search-results-1 tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>

<?php
	}
?>
			<!--End of Cross Reference List Insertion-->
<?php
/*End of BLOCK ONE*/
/*Begin of BLOCK TWO -- Extra*/
?>
	<div style="padding: 1px; border: 1px gold dotted;">
		<!--Content of Block Two Extra BEgins-->
		<!--Block ONE controls for searching-->
				<div class="ui-sys-search-container pure-form pure-transaction-control">
					<input type="text" title="At Least Three Characters should be supplied" required placeholder="ABC" size="32"/>
					<a title="Click to Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=managetimetable_edit&id=<?= $timetable1->getTimetableId() ?>&attach_login=true" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
				</div>
<!--Block ONE ends-->	
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
		//saving Authorization Rules 
?>
<!--Block Two, Search Results-->
<div class="ui-sys-search-results">
	<label id="statustextlabel"></label>
	<form method="POST" action="<?= $thispage ?>">
		<input type="hidden" name="page" value="managetimetable_edit"/>
		<input type="hidden" name="id" value="<?= $timetable1->getTimetableId() ?>"/>
		<input type="hidden" name="report" value="true"/>
		<input type="hidden" name="attach_login" value="true"/>
		<table class="pure-table ui-sys-table-search-results-2">
			<thead>
				<tr>
					<th></th>
					<th>S/N</th>
					<th>Name</th>
					<th>Email</th>
					<th>Phone</th>
					<th>Status</th>
				</tr>
			</thead>
<?php 
	$query = Login::getQueryText();
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$result = mysql_db_query($database, $query, $conn) or die("Could not fetch transaction data from the storage");
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	while (list($id) = mysql_fetch_row($result))	{
		if ($sqlReturnedRowCount > $maximumReturnedRows)	{
			//Finalize All House Keeping Here
			break;
		}
		$transaction1 = null;
		try {
			$transaction1 = new Login($database, $id, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated per objects 
		if ($transaction1->searchMatrix($matrix1)->evaluateResult())	{
			if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
				$numberOfTBodies++;
				$tbodyClosed = false;
			} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
				$numberOfTBodies++;
				$perTbodyCounter = 0;
			}
			//In All Cases we need to display tr
			$pureTableOddClass = "";
			if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
			<tr class="<?= $pureTableOddClass ?>">
				<td><input type="checkbox" name="did[<?= $sqlReturnedRowCount ?>]" value="<?= $transaction1->getLoginId() ?>"/></td>
				<td><?= $sqlReturnedRowCount + 1 ?></td>
				<td><?= $transaction1->getFullname() ?></td>
				<td><?= $transaction1->getEmail() ?></td>
				<td><?= $transaction1->getPhone() ?></td>
				<td>
<?php 
	if (! is_null($transaction1->getUserStatus()))	{
		echo $transaction1->getUserStatus()->getStatusName();
	}
?>				
				</td>
			</tr>
<?php
			$perTbodyCounter++;
			$sqlReturnedRowCount++;
		}//end-evaluate-results
	}//end-while
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
?>
		</table>
		<div class="ui-sys-right">
			<input type="submit" value="Attach Instructor"/>
		</div>
	</form>
	<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
	<ul class="ui-sys-pagination-2"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination-2').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results-2 tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
</div>
<!--Block Two Ends-->
<?php
	} //end-if-searchtext
?>		
		<!--Content of Block Two Extra Ends-->
	</div>
<?php
/*End of BLOCK TWO -- Extra*/
	} else {
?>
		<div class="ui-state-error">
			There were problems in the initial setup of Assignment <br/>
			Reason : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managetimetable_csv" && isset($_REQUEST['report']) && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managetimetable_csv", "normal", "setlog", "-1", "-1"))	{
		$filename=$_REQUEST['report'];
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Timetable CSV File<br/> (Download Report)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (unlink($filename))	{
?>
		<div class="ui-state-highlight">
			The temporary file has been removed succesful from the Server
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problem on removing the temporary file from the Server
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	} else if ($page == "managetimetable_csv" && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managetimetable_csv", "normal", "setlog", "-1", "-1"))	{
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$promise1 = new Promise();
		$promise1->setPromise(true);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Timetable CSV File <br/> (Download CSV File)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	$filename="";
	$tfname=$filename.$date;
	$tfname=$tfname.rand(100,999);
	$filename=sha1($tfname);
	$filename=$filename.".csv";
	$filename="../temp/".$filename;
	$searchText = Object::getSearchTextFromUserFieldData($_REQUEST['controlIndex'], $_REQUEST['fieldname'], $_REQUEST['op'], $_REQUEST['namespaceTag'], $_REQUEST['fieldvalue']);
	$lineCounter = 0;
	$file1 = fopen($filename, "w") or die("Could not Open the File for writing");
	$csvProcessor1 = null;
	try {
		$csvProcessor1 = new CSVProcessor($searchText, $file1);
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$primise1->setPromise(false);
	}
	$result = null;
	if ($promise1->isPromising())	{
		$query = Timetable::getQueryText();
		try {
			$result = mysql_db_query($database, $query, $conn) or Object::shootException("Timetable[CSV]: Could not Execute Query");
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$primise1->setPromise(false);
		}
	}
	if ($promise1->isPromising())	{
		while (list($id) = mysql_fetch_row($result))	{
			$timetable1 = null;
			try	{
				$timetable1 = new Timetable($database, $id, $conn);
			} catch (Exception $e)	{ die($e->getMessage()); }
			$csvProcessor1->reload(); //Clear the Previous Object Properties 
			if ($timetable1->processCSV($csvProcessor1)->evaluateResult())	{
				$lineCounter++;
			}
		}//end-while 
	}//end-if-promising
	fclose($file1);
	if ($promise1->isPromising())	{
?>
		<div>
			Your file is Ready for downloading<br/>
			Number data rows it contains is <?= $lineCounter ?><br/><br/>
			<b style="font-size: 1.1em;"><a href="<?= $filename ?>">Click Here</a></b> to download your file<br/><br/>
			Once the download is complete, kindly click the proceed button 
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were problems in generating your file <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>					
			</div>
			<div class="ui-sys-panel-footer">
				<div id="perror" class="ui-sys-error-message"></div>
<?php 
	if ($promise1->isPromising())	{
		if ($lineCounter > 0)	{
?>
			<a id="__id_csv_previewer" data-error-control="perror" data-server-forward-path="../server/getcsvpreviewer.php" data-file-to-read="<?= $filename ?>" data-dialog-container="__id_general_dialog_holder" class="button-link">Preview</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php
		}
?>
		<a class="button-link" href="<?= $thispage ?>?page=managetimetable_csv&report=<?= $filename ?>&downloadable=true">Continue</a>
<?php
	}
?>			
			</div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managetimetable_csv" && isset($_REQUEST['sortable']) && Authorize::isAllowable($config, "managetimetable_csv", "normal", "setlog", "-1", "-1"))	{
		$enableUpdate = false;
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Timetable CSV File <br/> (Column Sorting)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (isset($_REQUEST['fieldname']) && isset($_REQUEST['fieldtype']) && isset($_REQUEST['namespaceTag']) && isset($_REQUEST['fieldvalue']))	{
		$enableUpdate = true;
?>
				<form class="pure-form" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managetimetable_csv"/>
					<input type="hidden" name="downloadable" value="true"/>
<?php 
	$sortableContainer = Object::getUICSVDivSortable(Object::getLineToSortFromUserFieldData($_REQUEST['fieldname'], $_REQUEST['fieldtype'], $_REQUEST['namespaceTag'],$_REQUEST['fieldvalue']));
	echo $sortableContainer;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
<?php 
	} else {
?>
		Perhaps you did not select any column to be included in your CSV File <br/>
		Kindly go back to Column Selection and select atleast ONE Column
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer">
				<a class="button-link" href="<?= $thispage ?>?page=managetimetable_csv">Back to Column Selection</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php 
	if ($enableUpdate)	{
?>				
				<input type="button" id="__add_record" value="Proceed"/>
<?php 
	}
?>
			</div>
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
	} else if ($page == "managetimetable_csv" && Authorize::isAllowable($config, "managetimetable_csv", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Timetable CSV File <br/>(Column Selection)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
						<input type="hidden" name="page" value="managetimetable_csv"/>
						<input type="hidden" name="sortable" value="true"/>
<?php
	$listOfAvailableColumnsInTables = Object::getUICSVTables(Timetable::getAvailableViewableColumns());
	echo $listOfAvailableColumnsInTables;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer">
				<input type="button" id="__add_record" value="Proceed"/>
			</div>
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
	} else if ($page == "managetimetable_delete" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managetimetable_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$timetable1 = null;
		try {
			 $timetable1 = new Timetable($database, $_REQUEST['id'], $conn);
			 $timetable1->commitDelete();
			 $enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason("You might be dealing with a Timetable which is no longer existing in the system or a None Empty Record");
			$promise1->setPromise(false);
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Timetable Removal Report</div>
			<div class="ui-sys-panel-pody ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			The Timetable <?= $timetable1->getActivityName() ?> has been successful removed from the system
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Removing the Timetable <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managetimetable_delete", "Deleted ".$timetable1->getActivityName());
	} else if ($page == "managetimetable_delete" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managetimetable_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to database services");
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
			<div class="ui-sys-panel-header ui-widget-header">Delete Confirmation</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<div class="ui-sys-warning">
					You are about to remove a Timetable <b><?= $timetable1->getActivityName() ?></b> from the System <br/>
					<b>NOTE: This Operation Can not be reversed</b><br/>
					Are You Sure You want to remove the Timetable <?= $timetable1->getActivityName() ?> from the System?
				</div>
				<div class="ui-sys-center-text">
					<a class="button-link-warning" href="<?= $thispage ?>?page=managetimetable_delete&report=io&id=<?= $timetable1->getTimetableId() ?>&rndx=<?= $timetable1->getExtraFilter() ?>">Yes (Remove)</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a class="button-link" href="<?= $thispage ?>?page=managetimetable">No (Cancel)</a>
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managetimetable_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managetimetable_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$timetable1 = null;
		try {
			$timetable1 = new Timetable($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$promise1 = new Promise();
		$promise1->setReason("Nothing Has been Updated");
		$enableUpdate = false;
		$timetableName = mysql_real_escape_string($_REQUEST['timetableName']);
		$venueId = mysql_real_escape_string($_REQUEST['venueId']);
		$textColor = mysql_real_escape_string($_REQUEST['textColor']);
		$backgroundColor = mysql_real_escape_string($_REQUEST['backgroundColor']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
		$startTime = DateAndTime::convertFromGUIDateAndTimeFormatToSystemDateAndTimeFormat($_REQUEST['startDate'], $_REQUEST['startTime']);
		$endTime = DateAndTime::convertFromGUIDateAndTimeFormatToSystemDateAndTimeFormat($_REQUEST['endDate'], $_REQUEST['endTime']);
		if ($timetableName != $timetable1->getActivityName())	{
			$timetable1->setActivityName($timetableName); $enableUpdate = true;
		}
		if (is_null($timetable1->getVenue()) || ($timetable1->getVenue()->getVenueId() != $venueId))	{
			$timetable1->setVenue($venueId); $enableUpdate = true;
		}
		if (is_null($timetable1->getStartTime()) || ($timetable1->getStartTime()->getDateAndTimeString() != $startTime))	{
			$timetable1->setStartTime($startTime); $enableUpdate = true;
		}
		if (is_null($timetable1->getEndTime()) || ($timetable1->getEndTime()->getDateAndTimeString() != $endTime))	{
			$timetable1->setEndTime($endTime); $enableUpdate = true;
		}
		if ($textColor != $timetable1->getTextColor())	{
			$timetable1->setTextColor($textColor); $enableUpdate = true;
		}
		if ($backgroundColor != $timetable1->getBackgroundColor())	{
			$timetable1->setBackgroundColor($backgroundColor); $enableUpdate = true;
		}
		if ($extraInformation != $timetable1->getExtraInformation())	{
			$timetable1->setExtraInformation($extraInformation); $enableUpdate = true;
		}
		//This is the Last 
		if (trim($_REQUEST['rndx']) != $timetable1->getExtraFilter())	{
			$enableUpdate = false;
			$promise1->setReason("The System has Detected you are replaying data in your Browser window");
			$promise1->setPromise(false);
		}
		//Now proceed with general find a new value of extra Filter 
		$timetable1->setExtraFilter(System::getCodeString(8));
		if ($enableUpdate)	{
			try {
				$startTime1 = new DateAndTime("Zoomtong", $startTime, "Company");
				$endTime1 = new DateAndTime("Zoomtong", $endTime, "Company");
				if (! (($systemTime1->compareDateAndTime($startTime1) < 0) && ($startTime1->compareDateAndTime($endTime1) < 0))) Object::shootException("Timetable[Time Range]: Time range is not possible ");
				$timetable1->commitUpdate();
				$promise1->setPromise(true);
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
				$enableUpdate = false;
			}
		} 
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Timetable Editing Report</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Timetable <?= $timetable1->getActivityName() ?> has been updated successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were no updates for the timetable <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managetimetable_edit", "Edited ".$timetable1->getActivityName());
	} else if ($page == "managetimetable_edit" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managetimetable_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$timetable1 = null;
		$extraFilter = System::getCodeString(8);
		try {
			$timetable1 = new Timetable($database, $_REQUEST['id'], $conn);
			$timetable1->setExtraFilter($extraFilter);
			$timetable1->commitUpdate();
		} catch (Exception $e)	{ die($e->getMessage()); }
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Edit Timetable <?= $timetable1->getActivityName() ?></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managetimetable_edit"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="id" value="<?= $timetable1->getTimetableId() ?>"/>
					<input type="hidden" name="rndx" value="<?= $timetable1->getExtraFilter() ?>"/>
					<div class="pure-control-group">
						<label for="timetableName">Timetable Name </label>
						<input value="<?= $timetable1->getActivityName() ?>" type="text" name="timetableName" id="timetableName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Timetable Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="courseId">Venue </label>
						<select id="venueId" name="venueId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Venue">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Venue::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($timetable1->getVenue()) && ($timetable1->getVenue()->getVenueId() == $alist1['id'])) $selected="selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
<?php 
	$startDate = "";
	$startTime = "";
	if (! is_null($timetable1->getStartTime()))	{
		$list = DateAndTime::convertFromSystemDateAndTimeFormatToGUIDateAndTimeFormat($timetable1->getStartTime()->getDateAndTimeString());
		$startDate = $list['date'];
		$startTime = $list['time'];
	}
?>
					<div class="pure-control-group">
						<label for="startDate">Start Time </label>
						<input value="<?= $startDate ?>" class="datepicker"type="text" name="startDate" id="startDate" size="12" required pattern="<?= $exprDate ?>" validate="true" validate_control="text" validate_expression="<?= $exprDate ?>" validate_message="Start Date : <?= $msgDate ?>"/>
						<input value="<?= $startTime ?>" class="timepicker" type="text" name="startTime" size="12" required pattern="<?= $exprTime ?>" validate="true" validate_control="text" validate_expression="<?= $exprTime ?>" validate_message="Start Time: <?= $msgTime ?>"/>
					</div>
<?php 
	$endDate = "";
	$endTime = "";
	if (! is_null($timetable1->getEndTime()))	{
		$list = DateAndTime::convertFromSystemDateAndTimeFormatToGUIDateAndTimeFormat($timetable1->getEndTime()->getDateAndTimeString());
		$endDate = $list['date'];
		$endTime = $list['time'];
	}
?>
					<div class="pure-control-group">
						<label for="startDate">End Time </label>
						<input value="<?= $endDate ?>" class="datepicker"type="text" name="endDate" id="endDate" size="12" required pattern="<?= $exprDate ?>" validate="true" validate_control="text" validate_expression="<?= $exprDate ?>" validate_message="End Date : <?= $msgDate ?>"/>
						<input value="<?= $endTime ?>" class="timepicker" type="text" name="endTime" size="12" required required pattern="<?= $exprTime ?>" validate="true" validate_control="text" validate_expression="<?= $exprTime ?>" validate_message="End Time: <?= $msgTime ?>" />
					</div>
					<div class="pure-control-group">
						<label title="Color which will be used to show this timetable on the grid" for="textColor">Text Color </label>
						<input value="<?= $timetable1->getTextColor() ?>" type="text" name="textColor" id="textColor" size="24" required pattern="<?= $exprHexColor ?>" validate="true" validate_control="text" validate_expression="<?= $exprHexColor ?>" validate_message="Text Color : <?= $msgHexColor ?>"/>
					</div>
					<div class="pure-control-group">
						<label title="Background Color which will be used to show this timetable on the grid" for="backgroundColor">Background Color </label>
						<input value="<?= $timetable1->getBackgroundColor() ?>" type="text" name="backgroundColor" id="backgroundColor" size="24" required pattern="<?= $exprHexColor ?>" validate="true" validate_control="text" validate_expression="<?= $exprHexColor ?>" validate_message="Background Color : <?= $msgHexColor ?>"/>
					</div>
					
					
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input value="<?= $timetable1->getExtraInformation() ?>" type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Edit Record"/>
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
	} else if ($page == "managetimetable_detail" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managetimetable_detail", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$timetable1 = null;
		try {
			$timetable1 = new Timetable($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{die($e->getMessage());}
		mysql_close($conn);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Details for <?= $timetable1->getActivityName() ?></div>
		<div class="ui-sys-panel-content ui-sys-data-capture">
			<table class="pure-table">
				<thead>
					<tr>
						<th>Field Name</th>
						<th>Field Value</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Timetable Name</td>
						<td><?= $timetable1->getActivityName() ?></td>
					</tr>
<?php 
	if (! is_null($timetable1->getVenue()))	{
?>
					<tr>
						<td>Venue</td>
						<td><?= $timetable1->getVenue()->getVenueName() ?></td>
					</tr>
<?php
	}
	if (! is_null($timetable1->getStartTime()))	{
?>
		<tr>
			<td>Start Time</td>
			<td><?= $timetable1->getStartTime()->getDateAndTimeString() ?></td>
		</tr>
<?php
	}
	if (! is_null($timetable1->getEndTime()))	{
?>
		<tr>
			<td>End Time</td>
			<td><?= $timetable1->getEndTime()->getDateAndTimeString() ?></td>
		</tr>
<?php
	}
?>
					<tr>
						<td>Text Color</td>
						<td>
<?php 
	$textColor = $timetable1->getTextColor();
?>				
					<span style="background-color: <?= $textColor ?>;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;<?= $textColor ?>
		
						</td>
					</tr>
					<tr>
						<td>Background Color</td>
						<td>
<?php 
	$textColor = $timetable1->getBackgroundColor();
?>				
					<span style="background-color: <?= $textColor ?>;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;<?= $textColor ?>
		
						</td>
					</tr>
<?php 
	if (! is_null($timetable1->getExtraInformation()))	{
?>
					<tr>
						<td>Extra Information</td>
						<td><?= $timetable1->getExtraInformation() ?></td>
					</tr>
<?php
	}
?>
				</tbody>
			</table><br/>
			<!--Begin of Cross Reference List Insertion-->
<?php 
	if (! is_null($timetable1->getInstructorList()) && sizeof($timetable1->getInstructorList()) > 0)	{
		$objectList1 = $timetable1->getInstructorList();
?>
			<table class="pure-table pure-table-aligned ui-sys-search-results ui-sys-search-results-1">
				<thead>
					<tr>
						<th colspan="5">Timetabled By</th>
					</tr>
					<tr>
						<th></th>
						<th>Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Status</th>
					</tr>
				</thead>
<?php 
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	foreach ($objectList1 as $obj1)	{
		if (is_null($obj1)) continue;
		if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
			$numberOfTBodies++;
			$tbodyClosed = false;
		} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
			$numberOfTBodies++;
			$perTbodyCounter = 0;
		}
		//In All Cases we need to display tr
		$pureTableOddClass = "";
		if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
		<tr class="<?= $pureTableOddClass ?>">
			<td><?= $sqlReturnedRowCount + 1 ?></td>
			<td><?= $obj1->getFullname() ?></td>
			<td><?= $obj1->getEmail() ?></td>
			<td><?= $obj1->getPhone() ?></td>
			<td>
<?php 
	if (! is_null($obj1->getUserStatus()))	{
		echo $obj1->getUserStatus()->getStatusName();
	}
?>			
			</td>
		</tr>
<?php
		$perTbodyCounter++;
		$sqlReturnedRowCount++;
	}
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
?>
			</table>
			<ul class="ui-sys-pagination-1"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination-1').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results-1 tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>

<?php
	}
?>
			<!--End of Cross Reference List Insertion-->
			<br/>
			<!--Begin of Cross Reference List Insertion-->
<?php 
	if (! is_null($timetable1->getGroupList()) && sizeof($timetable1->getGroupList()) > 0)	{
		$objectList1 = $timetable1->getGroupList();
?>
			<table class="pure-table pure-table-aligned ui-sys-search-results ui-sys-search-results-2">
				<thead>
					<tr>
						<th colspan="6">Targeted Groups</th>
					</tr>
					<tr>
						<th></th>
						<th>Group Name</th>
						<th>Parent Group</th>
						<th>Course</th>
						<th>Year</th>
						<th>Semester</th>
					</tr>
				</thead>
<?php 
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	foreach ($objectList1 as $obj1)	{
		if (is_null($obj1)) continue;
		if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
			$numberOfTBodies++;
			$tbodyClosed = false;
		} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
			$numberOfTBodies++;
			$perTbodyCounter = 0;
		}
		//In All Cases we need to display tr
		$pureTableOddClass = "";
		if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
		<tr class="<?= $pureTableOddClass ?>">
			<td><?= $sqlReturnedRowCount + 1 ?></td>
			<td><?= $obj1->getGroupName() ?></td>
			<td>
<?php 
	if (! is_null($obj1->getParentGroup()))	{
		echo $obj1->getParentGroup()->getGroupName();
	}
?>			
			</td>
			<td>
<?php 
	if (! is_null($obj1->getCourse()))	{
		echo $obj1->getCourse()->getCourseName();
	}
?>			
			</td>
			<td><?= $obj1->getYear() ?></td>
			<td><?= $obj1->getSemester() ?></td>
		</tr>
<?php
		$perTbodyCounter++;
		$sqlReturnedRowCount++;
	}
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
?>
			</table>
			<ul class="ui-sys-pagination-2"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination-2').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results-2 tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>

<?php
	}
?>
			<!--End of Cross Reference List Insertion-->
		</div>
		<div class="ui-sys-panel-footer">
			<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "managetimetable_edit", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Edit <?= $timetable1->getActivityName() ?>" href="<?= $thispage ?>?page=managetimetable_edit&id=<?= $timetable1->getTimetableId() ?>"><img alt="DAT" src="../sysimage/buttonedit.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "managetimetable_delete", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Delete <?= $timetable1->getActivityName() ?>" href="<?= $thispage ?>?page=managetimetable_delete&id=<?= $timetable1->getTimetableId() ?>"><img alt="DAT" src="../sysimage/buttondelete.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "managetimetable_edit", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Attach Activity Creators" href="<?= $thispage ?>?page=managetimetable_edit&id=<?= $timetable1->getTimetableId() ?>&attach_login=true"><img alt="DAT" src="../sysimage/buttonattach.png"/></a>
		<a title="Attach Targeted Group" href="<?= $thispage ?>?page=managetimetable_edit&id=<?= $timetable1->getTimetableId() ?>&attach_groups=true"><img alt="DAT" src="../sysimage/buttonattach.png"/></a>
<?php
	}
?>
				<span class="ui-sys-clear-both">&nbsp;</span><br />
			</div>
		</div>
	</div>
<?php
	} else if ($page == "managetimetable_add" && isset($_REQUEST['report']) && Authorize::isAllowable($config, "managetimetable_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$timetableName = mysql_real_escape_string($_REQUEST['timetableName']);
		$venueId = mysql_real_escape_string($_REQUEST['venueId']);
		$textColor = mysql_real_escape_string($_REQUEST['textColor']);
		$backgroundColor = mysql_real_escape_string($_REQUEST['backgroundColor']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
		$startTime = DateAndTime::convertFromGUIDateAndTimeFormatToSystemDateAndTimeFormat($_REQUEST['startDate'], $_REQUEST['startTime']);
		$endTime = DateAndTime::convertFromGUIDateAndTimeFormatToSystemDateAndTimeFormat($_REQUEST['endDate'], $_REQUEST['endTime']);
		if ($textColor == "")	$textColor = "000000";
		if ($backgroundColor == "") $backgroundColor = "ffffff";
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel header ui-widget-header">Summary Report for <i><?= $timetableName ?></i></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	$extraFilter = System::getCodeString(8);
	$query="INSERT INTO timetable (timetableName, venueId, startTime, endTime, textColor, backgroundColor, extraFilter, extraInformation) VALUES('$timetableName', '$venueId', '$startTime', '$endTime', '$textColor', '$backgroundColor', '$extraFilter', '$extraInformation')";
	try {
		$startTime1 = new DateAndTime("Zoomtong", $startTime, "Company");
		$endTime1 = new DateAndTime("Zoomtong", $endTime, "Company");
		if (! (($systemTime1->compareDateAndTime($startTime1) < 0) && ($startTime1->compareDateAndTime($endTime1) < 0))) Object::shootException("Timetable[Time Range]: Time range is not possible ");
		mysql_db_query($database, $query, $conn) or Object::shootException("Timetable[Adding]: Could not Add a Record into the System");
		$promise1->setPromise(true);
		$enableUpdate = true;
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">The Timetable <?= $timetableName ?>, has successful Added to the system</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Adding the timetable<br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managetimetable_add", "Added $timetableName");
	} else if ($page == "managetimetable_add" && Authorize::isAllowable($config, "managetimetable_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Add a New Timetable</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managetimetable_add"/>
					<input type="hidden" name="report" value="io"/>
					<div class="pure-control-group">
						<label for="timetableName">Timetable Name </label>
						<input type="text" name="timetableName" id="timetableName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Timetable Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="courseId">Venue </label>
						<select id="venueId" name="venueId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Venue">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Venue::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
?>
			<option value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="startDate">Start Time </label>
						<input class="datepicker"type="text" name="startDate" id="startDate" size="12" required pattern="<?= $exprDate ?>" validate="true" validate_control="text" validate_expression="<?= $exprDate ?>" validate_message="Start Date : <?= $msgDate ?>"/>
						<input class="timepicker" type="text" name="startTime" size="12" required pattern="<?= $exprTime ?>" validate="true" validate_control="text" validate_expression="<?= $exprTime ?>" validate_message="Start Time: <?= $msgTime ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="startDate">End Time </label>
						<input class="datepicker"type="text" name="endDate" id="endDate" size="12" required pattern="<?= $exprDate ?>" validate="true" validate_control="text" validate_expression="<?= $exprDate ?>" validate_message="End Date : <?= $msgDate ?>"/>
						<input class="timepicker" type="text" name="endTime" size="12" required required pattern="<?= $exprTime ?>" validate="true" validate_control="text" validate_expression="<?= $exprTime ?>" validate_message="End Time: <?= $msgTime ?>" />
					</div>
					<div class="pure-control-group">
						<label title="Color which will be used to show this timetable on the grid" for="textColor">Text Color </label>
						<input type="text" name="textColor" id="textColor" size="24" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprHexColor ?>" validate_message="Text Color : <?= $msgHexColor ?>"/>
					</div>
					<div class="pure-control-group">
						<label title="Background Color which will be used to show this timetable on the grid" for="backgroundColor">Background Color </label>
						<input type="text" name="backgroundColor" id="backgroundColor" size="24" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprHexColor ?>" validate_message="Background Color : <?= $msgHexColor ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Add Record"/>
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
	} else if ($page == "managetimetable" && Authorize::isAllowable($config, "managetimetable", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Timetables Management</div>
			<div class="ui-sys-panel-body">
<!--Block ONE controls for searching-->
				<div class="ui-sys-search-container pure-form pure-timetable-control">
					<input type="text" title="At Least Three Characters should be supplied" required placeholder="ABC" size="32"/>
					<a title="Click to Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=managetimetable" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
				</div>
<!--Block ONE ends-->	
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
		//saving Authorization Rules 
		$blnAuthorizationDetail = Authorize::isAllowable($config, "managetimetable_detail", "normal", "donotsetlog", "-1", "-1");
?>
<!--Block Two, Search Results-->
<div class="ui-sys-search-results">
	<label id="statustextlabel"></label>
	<table class="pure-table ui-sys-table-search-results">
		<thead>
			<tr>
				<th>S/N</th>
				<th>Activity <br/>Name</th>
				<th>Venue</th>
				<th>Start Time</th>
				<th>End Time</th>
				<th>Text Color</th>
				<th>Background<br/>Color</th>
				<th></th>
			</tr>
		</thead>
<?php 
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
	$query = Timetable::getQueryText();
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$result = mysql_db_query($database, $query, $conn) or die("Could not fetch timetable data from the storage");
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	while (list($id) = mysql_fetch_row($result))	{
		if ($sqlReturnedRowCount > $maximumReturnedRows)	{
			//Finalize All House Keeping Here
			break;
		}
		$timetable1 = null;
		try {
			$timetable1 = new Timetable($database, $id, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated per objects 
		if ($timetable1->searchMatrix($matrix1)->evaluateResult())	{
			if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
				$numberOfTBodies++;
				$tbodyClosed = false;
			} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
				$numberOfTBodies++;
				$perTbodyCounter = 0;
			}
			//In All Cases we need to display tr
			$pureTableOddClass = "";
			if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
			<tr class="<?= $pureTableOddClass ?>">
				<td><?= $sqlReturnedRowCount + 1 ?></td>
				<td><?= $timetable1->getActivityName() ?></td>
				<td>
<?php 
	if (! is_null($timetable1->getVenue()))	{
?>
		<?= $timetable1->getVenue()->getVenueName() ?>
<?php
	}
?>				
				</td>
				<td>
<?php 
	if (! is_null($timetable1->getStartTime()))	{
?>
		<?= $timetable1->getStartTime()->getDateAndTimeString() ?>
<?php
	}
?>
				</td>
				<td>
<?php 
	if (! is_null($timetable1->getEndTime()))	{
?>
		<?= $timetable1->getEndTime()->getDateAndTimeString() ?>
<?php
	}
?>				
				</td>
				<td>
<?php 
	$textColor = $timetable1->getTextColor();
?>				
					<span style="background-color: <?= $textColor ?>;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;<?= $textColor ?>
				</td>
				<td>
<?php 
	$textColor = $timetable1->getBackgroundColor();
?>				
					<span style="background-color: <?= $textColor ?>;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;<?= $textColor ?>
				</td>
				<td>
<?php 
	if ($blnAuthorizationDetail)	{
?>
		<a href="<?= $thispage ?>?page=managetimetable_detail&id=<?= $timetable1->getTimetableId() ?>" class="ui-sys-search-results-controls"><img alt="DAT" src="../sysimage/buttondetail.png"/></a>
<?php
	}
?>				
				</td>
			</tr>
<?php
			$perTbodyCounter++;
			$sqlReturnedRowCount++;
		}//end-evaluate-results
	}//end-while
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
	mysql_close($conn);
?>
	</table>
	<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
	<ul class="ui-sys-pagination"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
</div>
<!--Block Two Ends-->
<?php
	} //end-if-searchtext
?>		
<!--Beginning of Block Three-->
				<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "managetimetable_csv", "normal", "donotsetlog", "-1", "-1")) {
?>
		<a title="Download CSV/EXCEL Formatted Data" href="<?= $thispage ?>?page=managetimetable_csv"><img alt="DAT" src="../sysimage/buttoncsv.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "managetimetable_add", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Add a New System Timetable" href="<?= $thispage ?>?page=managetimetable_add"><img alt="DAT" src="../sysimage/buttonadd.png"/></a>
<?php	
	}
?>				
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	}/*Timetable End*/ else/*Schedule BEGIN*/if ($page == "manageschedule_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['detach_groups']) && Authorize::isAllowable($config, "manageschedule_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$schedule1 = null;
		$headerText = "UNKNOWN";
		try {
			$schedule1 = new Schedule($database, $_REQUEST['id'], $conn);
			$headerText = "De-Assign Event Targets Map from ".$schedule1->getActivityName();
			if (! isset($_REQUEST['did'])) Object::shootException("There were nothing to Dettach");
			$listToUpdate = Object::detachIdListFromObjectList($schedule1->getGroupList(), $_REQUEST['did']);
			$schedule1->setGroupList($listToUpdate);
			$schedule1->commitUpdate();
			$enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header"></div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Group List Attachment were removed successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in De-Attaching Group List <br/>
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
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageschedule_edit", "Detach Groups for ".$schedule1->getActivityName());
	} else if ($page == "manageschedule_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['attach_groups']) && Authorize::isAllowable($config, "manageschedule_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$schedule1 = null;
		$headerText = "UNKNOWN";
		try {
			$schedule1 = new Schedule($database, $_REQUEST['id'], $conn);
			$headerText = "Assign Group Map to ".$schedule1->getActivityName();
			if (! isset($_REQUEST['did'])) Object::shootException("There were nothing to Attach");
			$listToUpdate = Object::attachIdListToObjectList($schedule1->getGroupList(), $_REQUEST['did']);
			$schedule1->setGroupList($listToUpdate);
			$schedule1->commitUpdate();
			$enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header"></div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Group List Attachment were done successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Attaching Group List <br/>
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
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageschedule_edit", "Attach Groups for ".$schedule1->getActivityName());
	} else if ($page == "manageschedule_edit" && isset($_REQUEST['id']) && isset($_REQUEST['attach_groups']) && Authorize::isAllowable($config, "manageschedule_edit", "normal", "setlog", "-1", "-1")) {
		$conn  = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$schedule1 = null;
		$headerText = "UNKNOWN";
		try {
			$schedule1 = new Schedule($database, $_REQUEST['id'], $conn);
			$headerText = "Assign Group Map to ".$schedule1->getActivityName();
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header"><?= $headerText ?></div>
			<div class="ui-sys-panel-body ui-sys-search-results">
<?php 
	if ($promise1->isPromising())	{
/*Begin of BLOCK ONE */
?>
		<!--Begin of Cross Reference List Insertion-->
<?php 
	if (! is_null($schedule1->getGroupList()) && sizeof($schedule1->getGroupList()) > 0)	{
		$objectList1 = $schedule1->getGroupList();
?>
			<form method="POST" action="<?= $thispage ?>">
				<input type="hidden" name="page" value="manageschedule_edit"/>
				<input type="hidden" name="id" value="<?= $schedule1->getScheduleId() ?>"/>
				<input type="hidden" name="report" value="true"/>
				<input type="hidden" name="detach_groups" value="true"/>
				<table class="pure-table pure-table-aligned ui-sys-search-results ui-sys-search-results-1">
					<thead>
					<tr>
						<th colspan="7">Targeted Groups</th>
					</tr>
					<tr>
						<th></th>
						<th>S/N</th>
						<th>Group Name</th>
						<th>Parent Group</th>
						<th>Course</th>
						<th>Year</th>
						<th>Semester</th>
					</tr>
				</thead>
<?php 
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	foreach ($objectList1 as $obj1)	{
		if (is_null($obj1)) continue;
		if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
			$numberOfTBodies++;
			$tbodyClosed = false;
		} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
			$numberOfTBodies++;
			$perTbodyCounter = 0;
		}
		//In All Cases we need to display tr
		$pureTableOddClass = "";
		if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
		<tr class="<?= $pureTableOddClass ?>">
			<td><input type="checkbox" name="did[<?= $sqlReturnedRowCount ?>]" value="<?= $obj1->getGroupId() ?>"/></td>
			<td><?= $sqlReturnedRowCount + 1 ?></td>
			<td><?= $obj1->getGroupName() ?></td>
			<td>
<?php 
	if (! is_null($obj1->getParentGroup()))	{
		echo $obj1->getParentGroup()->getGroupName();
	}
?>			
			</td>
			<td>
<?php 
	if (! is_null($obj1->getCourse()))	{
		echo $obj1->getCourse()->getCourseName();
	}
?>			
			</td>
			<td><?= $obj1->getYear() ?></td>
			<td><?= $obj1->getSemester() ?></td>
		</tr>
<?php
		$perTbodyCounter++;
		$sqlReturnedRowCount++;
	}
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
?>
				</table>
				<div class="ui-sys-right">
					<input type="submit" value="Detach Groups"/>
				</div>
			</form>
			<ul class="ui-sys-pagination-1"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination-1').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results-1 tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>

<?php
	}
?>
			<!--End of Cross Reference List Insertion-->
<?php
/*End of BLOCK ONE*/
/*Begin of BLOCK TWO -- Extra*/
?>
	<div style="padding: 1px; border: 1px gold dotted;">
		<!--Content of Block Two Extra BEgins-->
		<!--Block ONE controls for searching-->
				<div class="ui-sys-search-container pure-form pure-transaction-control">
					<input type="text" title="At Least Three Characters should be supplied" required placeholder="ABC" size="32"/>
					<a title="Click to Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=manageschedule_edit&id=<?= $schedule1->getScheduleId() ?>&attach_groups=true" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
				</div>
<!--Block ONE ends-->	
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
		//saving Authorization Rules 
?>
<!--Block Two, Search Results-->
<div class="ui-sys-search-results">
	<label id="statustextlabel"></label>
	<form method="POST" action="<?= $thispage ?>">
		<input type="hidden" name="page" value="manageschedule_edit"/>
		<input type="hidden" name="id" value="<?= $schedule1->getScheduleId() ?>"/>
		<input type="hidden" name="report" value="true"/>
		<input type="hidden" name="attach_groups" value="true"/>
		<table class="pure-table ui-sys-table-search-results-2">
			<thead>
				<tr>
					<th></th>
					<th>S/N</th>
					<th>Group Name</th>
					<th>Parent Group</th>
					<th>Course</th>
					<th>Year</th>
					<th>Semester</th>
				</tr>
			</thead>
<?php 
	$query = Group::getQueryText();
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$result = mysql_db_query($database, $query, $conn) or die("Could not fetch transaction data from the storage");
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	while (list($id) = mysql_fetch_row($result))	{
		if ($sqlReturnedRowCount > $maximumReturnedRows)	{
			//Finalize All House Keeping Here
			break;
		}
		$transaction1 = null;
		try {
			$transaction1 = new Group($database, $id, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated per objects 
		if ($transaction1->searchMatrix($matrix1)->evaluateResult())	{
			if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
				$numberOfTBodies++;
				$tbodyClosed = false;
			} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
				$numberOfTBodies++;
				$perTbodyCounter = 0;
			}
			//In All Cases we need to display tr
			$pureTableOddClass = "";
			if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
			<tr class="<?= $pureTableOddClass ?>">
				<td><input type="checkbox" name="did[<?= $sqlReturnedRowCount ?>]" value="<?= $transaction1->getGroupId() ?>"/></td>
				<td><?= $sqlReturnedRowCount + 1 ?></td>
			<td><?= $transaction1->getGroupName() ?></td>
			<td>
<?php 
	if (! is_null($transaction1->getParentGroup()))	{
		echo $transaction1->getParentGroup()->getGroupName();
	}
?>			
			</td>
			<td>
<?php 
	if (! is_null($transaction1->getCourse()))	{
		echo $transaction1->getCourse()->getCourseName();
	}
?>			
			</td>
			<td><?= $transaction1->getYear() ?></td>
			<td><?= $transaction1->getSemester() ?></td>
			</tr>
<?php
			$perTbodyCounter++;
			$sqlReturnedRowCount++;
		}//end-evaluate-results
	}//end-while
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
?>
		</table>
		<div class="ui-sys-right">
			<input type="submit" value="Attach Group"/>
		</div>
	</form>
	<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
	<ul class="ui-sys-pagination-2"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination-2').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results-2 tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
</div>
<!--Block Two Ends-->
<?php
	} //end-if-searchtext
?>		
		<!--Content of Block Two Extra Ends-->
	</div>
<?php
/*End of BLOCK TWO -- Extra*/
	} else {
?>
		<div class="ui-state-error">
			There were problems in the initial setup of Assignment <br/>
			Reason : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "manageschedule_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['detach_login']) && Authorize::isAllowable($config, "manageschedule_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$schedule1 = null;
		$headerText = "UNKNOWN";
		try {
			$schedule1 = new Schedule($database, $_REQUEST['id'], $conn);
			$headerText = "De-Assign Event Creators Map from ".$schedule1->getActivityName();
			if (! isset($_REQUEST['did'])) Object::shootException("There were nothing to Attach");
			$listToUpdate = Object::detachIdListFromObjectList($schedule1->getInstructorList(), $_REQUEST['did']);
			$schedule1->setInstructorList($listToUpdate);
			$schedule1->commitUpdate();
			$enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header"></div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Instructor List Attachment were removed successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in De-Attaching Instructor List <br/>
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
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageschedule_edit", "Detach Instructors for ".$schedule1->getActivityName());
	} else if ($page == "manageschedule_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['attach_login']) && Authorize::isAllowable($config, "manageschedule_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$schedule1 = null;
		$headerText = "UNKNOWN";
		try {
			$schedule1 = new Schedule($database, $_REQUEST['id'], $conn);
			$headerText = "Assign Instructor Map to ".$schedule1->getActivityName();
			if (! isset($_REQUEST['did'])) Object::shootException("There were nothing to Attach");
			$listToUpdate = Object::attachIdListToObjectList($schedule1->getInstructorList(), $_REQUEST['did']);
			$schedule1->setInstructorList($listToUpdate);
			$schedule1->commitUpdate();
			$enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header"></div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Instructor List Attachment were done successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Attaching Instructor List <br/>
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
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageschedule_edit", "Attach Instructors for ".$schedule1->getActivityName());
	} else if ($page == "manageschedule_edit" && isset($_REQUEST['id']) && isset($_REQUEST['attach_login']) && Authorize::isAllowable($config, "manageschedule_edit", "normal", "setlog", "-1", "-1")) {
		$conn  = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$schedule1 = null;
		$headerText = "UNKNOWN";
		try {
			$schedule1 = new Schedule($database, $_REQUEST['id'], $conn);
			$headerText = "Assign Instructor Map to ".$schedule1->getActivityName();
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header"><?= $headerText ?></div>
			<div class="ui-sys-panel-body ui-sys-search-results">
<?php 
	if ($promise1->isPromising())	{
/*Begin of BLOCK ONE */
?>
		<!--Begin of Cross Reference List Insertion-->
<?php 
	if (! is_null($schedule1->getInstructorList()) && sizeof($schedule1->getInstructorList()) > 0)	{
		$objectList1 = $schedule1->getInstructorList();
?>
			<form method="POST" action="<?= $thispage ?>">
				<input type="hidden" name="page" value="manageschedule_edit"/>
				<input type="hidden" name="id" value="<?= $schedule1->getScheduleId() ?>"/>
				<input type="hidden" name="report" value="true"/>
				<input type="hidden" name="detach_login" value="true"/>
				<table class="pure-table pure-table-aligned ui-sys-search-results ui-sys-search-results-1">
					<thead>
						<tr>
						<th colspan="6">Scheduled By</th>
						</tr>
						<tr>
							<th></th>
							<th></th>
							<th>Name</th>
							<th>Email</th>
							<th>Phone</th>
							<th>Status</th>
						</tr>
					</thead>
<?php 
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	foreach ($objectList1 as $obj1)	{
		if (is_null($obj1)) continue;
		if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
			$numberOfTBodies++;
			$tbodyClosed = false;
		} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
			$numberOfTBodies++;
			$perTbodyCounter = 0;
		}
		//In All Cases we need to display tr
		$pureTableOddClass = "";
		if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
		<tr class="<?= $pureTableOddClass ?>">
			<td><input type="checkbox" name="did[<?= $sqlReturnedRowCount ?>]" value="<?= $obj1->getLoginId() ?>"/></td>
			<td><?= $sqlReturnedRowCount + 1 ?></td>
			<td><?= $obj1->getFullname() ?></td>
			<td><?= $obj1->getEmail() ?></td>
			<td><?= $obj1->getPhone() ?></td>
			<td>
<?php 
	if (! is_null($obj1->getUserStatus()))	{
		echo $obj1->getUserStatus()->getStatusName();
	}
?>			
			</td>
		</tr>
<?php
		$perTbodyCounter++;
		$sqlReturnedRowCount++;
	}
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
?>
				</table>
				<div class="ui-sys-right">
					<input type="submit" value="Detach Instructors"/>
				</div>
			</form>
			<ul class="ui-sys-pagination-1"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination-1').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results-1 tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>

<?php
	}
?>
			<!--End of Cross Reference List Insertion-->
<?php
/*End of BLOCK ONE*/
/*Begin of BLOCK TWO -- Extra*/
?>
	<div style="padding: 1px; border: 1px gold dotted;">
		<!--Content of Block Two Extra BEgins-->
		<!--Block ONE controls for searching-->
				<div class="ui-sys-search-container pure-form pure-transaction-control">
					<input type="text" title="At Least Three Characters should be supplied" required placeholder="ABC" size="32"/>
					<a title="Click to Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=manageschedule_edit&id=<?= $schedule1->getScheduleId() ?>&attach_login=true" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
				</div>
<!--Block ONE ends-->	
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
		//saving Authorization Rules 
?>
<!--Block Two, Search Results-->
<div class="ui-sys-search-results">
	<label id="statustextlabel"></label>
	<form method="POST" action="<?= $thispage ?>">
		<input type="hidden" name="page" value="manageschedule_edit"/>
		<input type="hidden" name="id" value="<?= $schedule1->getScheduleId() ?>"/>
		<input type="hidden" name="report" value="true"/>
		<input type="hidden" name="attach_login" value="true"/>
		<table class="pure-table ui-sys-table-search-results-2">
			<thead>
				<tr>
					<th></th>
					<th>S/N</th>
					<th>Name</th>
					<th>Email</th>
					<th>Phone</th>
					<th>Status</th>
				</tr>
			</thead>
<?php 
	$query = Login::getQueryText();
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$result = mysql_db_query($database, $query, $conn) or die("Could not fetch transaction data from the storage");
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	while (list($id) = mysql_fetch_row($result))	{
		if ($sqlReturnedRowCount > $maximumReturnedRows)	{
			//Finalize All House Keeping Here
			break;
		}
		$transaction1 = null;
		try {
			$transaction1 = new Login($database, $id, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated per objects 
		if ($transaction1->searchMatrix($matrix1)->evaluateResult())	{
			if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
				$numberOfTBodies++;
				$tbodyClosed = false;
			} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
				$numberOfTBodies++;
				$perTbodyCounter = 0;
			}
			//In All Cases we need to display tr
			$pureTableOddClass = "";
			if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
			<tr class="<?= $pureTableOddClass ?>">
				<td><input type="checkbox" name="did[<?= $sqlReturnedRowCount ?>]" value="<?= $transaction1->getLoginId() ?>"/></td>
				<td><?= $sqlReturnedRowCount + 1 ?></td>
				<td><?= $transaction1->getFullname() ?></td>
				<td><?= $transaction1->getEmail() ?></td>
				<td><?= $transaction1->getPhone() ?></td>
				<td>
<?php 
	if (! is_null($transaction1->getUserStatus()))	{
		echo $transaction1->getUserStatus()->getStatusName();
	}
?>				
				</td>
			</tr>
<?php
			$perTbodyCounter++;
			$sqlReturnedRowCount++;
		}//end-evaluate-results
	}//end-while
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
?>
		</table>
		<div class="ui-sys-right">
			<input type="submit" value="Attach Instructor"/>
		</div>
	</form>
	<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
	<ul class="ui-sys-pagination-2"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination-2').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results-2 tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
</div>
<!--Block Two Ends-->
<?php
	} //end-if-searchtext
?>		
		<!--Content of Block Two Extra Ends-->
	</div>
<?php
/*End of BLOCK TWO -- Extra*/
	} else {
?>
		<div class="ui-state-error">
			There were problems in the initial setup of Assignment <br/>
			Reason : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "manageschedule_csv" && isset($_REQUEST['report']) && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "manageschedule_csv", "normal", "setlog", "-1", "-1"))	{
		$filename=$_REQUEST['report'];
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Schedule CSV File<br/> (Download Report)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (unlink($filename))	{
?>
		<div class="ui-state-highlight">
			The temporary file has been removed succesful from the Server
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problem on removing the temporary file from the Server
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	} else if ($page == "manageschedule_csv" && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "manageschedule_csv", "normal", "setlog", "-1", "-1"))	{
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$promise1 = new Promise();
		$promise1->setPromise(true);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Schedule CSV File <br/> (Download CSV File)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	$filename="";
	$tfname=$filename.$date;
	$tfname=$tfname.rand(100,999);
	$filename=sha1($tfname);
	$filename=$filename.".csv";
	$filename="../temp/".$filename;
	$searchText = Object::getSearchTextFromUserFieldData($_REQUEST['controlIndex'], $_REQUEST['fieldname'], $_REQUEST['op'], $_REQUEST['namespaceTag'], $_REQUEST['fieldvalue']);
	$lineCounter = 0;
	$file1 = fopen($filename, "w") or die("Could not Open the File for writing");
	$csvProcessor1 = null;
	try {
		$csvProcessor1 = new CSVProcessor($searchText, $file1);
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$primise1->setPromise(false);
	}
	$result = null;
	if ($promise1->isPromising())	{
		$query = Schedule::getQueryText();
		try {
			$result = mysql_db_query($database, $query, $conn) or Object::shootException("Schedule[CSV]: Could not Execute Query");
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$primise1->setPromise(false);
		}
	}
	if ($promise1->isPromising())	{
		while (list($id) = mysql_fetch_row($result))	{
			$schedule1 = null;
			try	{
				$schedule1 = new Schedule($database, $id, $conn);
			} catch (Exception $e)	{ die($e->getMessage()); }
			$csvProcessor1->reload(); //Clear the Previous Object Properties 
			if ($schedule1->processCSV($csvProcessor1)->evaluateResult())	{
				$lineCounter++;
			}
		}//end-while 
	}//end-if-promising
	fclose($file1);
	if ($promise1->isPromising())	{
?>
		<div>
			Your file is Ready for downloading<br/>
			Number data rows it contains is <?= $lineCounter ?><br/><br/>
			<b style="font-size: 1.1em;"><a href="<?= $filename ?>">Click Here</a></b> to download your file<br/><br/>
			Once the download is complete, kindly click the proceed button 
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were problems in generating your file <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>					
			</div>
			<div class="ui-sys-panel-footer">
				<div id="perror" class="ui-sys-error-message"></div>
<?php 
	if ($promise1->isPromising())	{
		if ($lineCounter > 0)	{
?>
			<a id="__id_csv_previewer" data-error-control="perror" data-server-forward-path="../server/getcsvpreviewer.php" data-file-to-read="<?= $filename ?>" data-dialog-container="__id_general_dialog_holder" class="button-link">Preview</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php
		}
?>
		<a class="button-link" href="<?= $thispage ?>?page=manageschedule_csv&report=<?= $filename ?>&downloadable=true">Continue</a>
<?php
	}
?>			
			</div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "manageschedule_csv" && isset($_REQUEST['sortable']) && Authorize::isAllowable($config, "manageschedule_csv", "normal", "setlog", "-1", "-1"))	{
		$enableUpdate = false;
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Schedule CSV File <br/> (Column Sorting)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (isset($_REQUEST['fieldname']) && isset($_REQUEST['fieldtype']) && isset($_REQUEST['namespaceTag']) && isset($_REQUEST['fieldvalue']))	{
		$enableUpdate = true;
?>
				<form class="pure-form" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="manageschedule_csv"/>
					<input type="hidden" name="downloadable" value="true"/>
<?php 
	$sortableContainer = Object::getUICSVDivSortable(Object::getLineToSortFromUserFieldData($_REQUEST['fieldname'], $_REQUEST['fieldtype'], $_REQUEST['namespaceTag'],$_REQUEST['fieldvalue']));
	echo $sortableContainer;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
<?php 
	} else {
?>
		Perhaps you did not select any column to be included in your CSV File <br/>
		Kindly go back to Column Selection and select atleast ONE Column
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer">
				<a class="button-link" href="<?= $thispage ?>?page=manageschedule_csv">Back to Column Selection</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php 
	if ($enableUpdate)	{
?>				
				<input type="button" id="__add_record" value="Proceed"/>
<?php 
	}
?>
			</div>
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
	} else if ($page == "manageschedule_csv" && Authorize::isAllowable($config, "manageschedule_csv", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Schedule CSV File <br/>(Column Selection)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
						<input type="hidden" name="page" value="manageschedule_csv"/>
						<input type="hidden" name="sortable" value="true"/>
<?php
	$listOfAvailableColumnsInTables = Object::getUICSVTables(Schedule::getAvailableViewableColumns());
	echo $listOfAvailableColumnsInTables;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer">
				<input type="button" id="__add_record" value="Proceed"/>
			</div>
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
	} else if ($page == "manageschedule_delete" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "manageschedule_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$schedule1 = null;
		try {
			 $schedule1 = new Schedule($database, $_REQUEST['id'], $conn);
			 $schedule1->commitDelete();
			 $enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason("You might be dealing with a Schedule which is no longer existing in the system or a None Empty Record");
			$promise1->setPromise(false);
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Schedule Removal Report</div>
			<div class="ui-sys-panel-pody ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			The Schedule <?= $schedule1->getActivityName() ?> has been successful removed from the system
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Removing the Schedule <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageschedule_delete", "Deleted ".$schedule1->getActivityName());
	} else if ($page == "manageschedule_delete" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "manageschedule_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to database services");
		$schedule1 = null;
		try {
			$schedule1 = new Schedule($database, $_REQUEST['id'], $conn);
			$schedule1->setExtraFilter(System::getCodeString(8));
			$schedule1->commitUpdate();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Delete Confirmation</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<div class="ui-sys-warning">
					You are about to remove a Schedule <b><?= $schedule1->getActivityName() ?></b> from the System <br/>
					<b>NOTE: This Operation Can not be reversed</b><br/>
					Are You Sure You want to remove the Schedule <?= $schedule1->getActivityName() ?> from the System?
				</div>
				<div class="ui-sys-center-text">
					<a class="button-link-warning" href="<?= $thispage ?>?page=manageschedule_delete&report=io&id=<?= $schedule1->getScheduleId() ?>&rndx=<?= $schedule1->getExtraFilter() ?>">Yes (Remove)</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a class="button-link" href="<?= $thispage ?>?page=manageschedule">No (Cancel)</a>
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "manageschedule_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "manageschedule_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$schedule1 = null;
		try {
			$schedule1 = new Schedule($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$promise1 = new Promise();
		$promise1->setReason("Nothing Has been Updated");
		$enableUpdate = false;
		$scheduleName = mysql_real_escape_string($_REQUEST['scheduleName']);
		$venueId = mysql_real_escape_string($_REQUEST['venueId']);
		$textColor = mysql_real_escape_string($_REQUEST['textColor']);
		$backgroundColor = mysql_real_escape_string($_REQUEST['backgroundColor']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
		$startTime = DateAndTime::convertFromGUIDateAndTimeFormatToSystemDateAndTimeFormat($_REQUEST['startDate'], $_REQUEST['startTime']);
		$endTime = DateAndTime::convertFromGUIDateAndTimeFormatToSystemDateAndTimeFormat($_REQUEST['endDate'], $_REQUEST['endTime']);
		if ($scheduleName != $schedule1->getActivityName())	{
			$schedule1->setActivityName($scheduleName); $enableUpdate = true;
		}
		if (is_null($schedule1->getVenue()) || ($schedule1->getVenue()->getVenueId() != $venueId))	{
			$schedule1->setVenue($venueId); $enableUpdate = true;
		}
		if (is_null($schedule1->getStartTime()) || ($schedule1->getStartTime()->getDateAndTimeString() != $startTime))	{
			$schedule1->setStartTime($startTime); $enableUpdate = true;
		}
		if (is_null($schedule1->getEndTime()) || ($schedule1->getEndTime()->getDateAndTimeString() != $endTime))	{
			$schedule1->setEndTime($endTime); $enableUpdate = true;
		}
		if ($textColor != $schedule1->getTextColor())	{
			$schedule1->setTextColor($textColor); $enableUpdate = true;
		}
		if ($backgroundColor != $schedule1->getBackgroundColor())	{
			$schedule1->setBackgroundColor($backgroundColor); $enableUpdate = true;
		}
		if ($extraInformation != $schedule1->getExtraInformation())	{
			$schedule1->setExtraInformation($extraInformation); $enableUpdate = true;
		}
		//This is the Last 
		if (trim($_REQUEST['rndx']) != $schedule1->getExtraFilter())	{
			$enableUpdate = false;
			$promise1->setReason("The System has Detected you are replaying data in your Browser window");
			$promise1->setPromise(false);
		}
		//Now proceed with general find a new value of extra Filter 
		$schedule1->setExtraFilter(System::getCodeString(8));
		if ($enableUpdate)	{
			try {
				$startTime1 = new DateAndTime("Zoomtong", $startTime, "Company");
				$endTime1 = new DateAndTime("Zoomtong", $endTime, "Company");
				if (! (($systemTime1->compareDateAndTime($startTime1) < 0) && ($startTime1->compareDateAndTime($endTime1) < 0))) Object::shootException("Schedule[Time Range]: Time range is not possible ");
				$schedule1->commitUpdate();
				$promise1->setPromise(true);
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
				$enableUpdate = false;
			}
		} 
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Schedule Editing Report</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Schedule <?= $schedule1->getActivityName() ?> has been updated successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were no updates for the schedule <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageschedule_edit", "Edited ".$schedule1->getActivityName());
	} else if ($page == "manageschedule_edit" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "manageschedule_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$schedule1 = null;
		$extraFilter = System::getCodeString(8);
		try {
			$schedule1 = new Schedule($database, $_REQUEST['id'], $conn);
			$schedule1->setExtraFilter($extraFilter);
			$schedule1->commitUpdate();
		} catch (Exception $e)	{ die($e->getMessage()); }
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Edit Schedule <?= $schedule1->getActivityName() ?></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="manageschedule_edit"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="id" value="<?= $schedule1->getScheduleId() ?>"/>
					<input type="hidden" name="rndx" value="<?= $schedule1->getExtraFilter() ?>"/>
					<div class="pure-control-group">
						<label for="scheduleName">Schedule Name </label>
						<input value="<?= $schedule1->getActivityName() ?>" type="text" name="scheduleName" id="scheduleName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Schedule Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="courseId">Venue </label>
						<select id="venueId" name="venueId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Venue">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Venue::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($schedule1->getVenue()) && ($schedule1->getVenue()->getVenueId() == $alist1['id'])) $selected="selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
<?php 
	$startDate = "";
	$startTime = "";
	if (! is_null($schedule1->getStartTime()))	{
		$list = DateAndTime::convertFromSystemDateAndTimeFormatToGUIDateAndTimeFormat($schedule1->getStartTime()->getDateAndTimeString());
		$startDate = $list['date'];
		$startTime = $list['time'];
	}
?>
					<div class="pure-control-group">
						<label for="startDate">Start Time </label>
						<input value="<?= $startDate ?>" class="datepicker"type="text" name="startDate" id="startDate" size="12" required pattern="<?= $exprDate ?>" validate="true" validate_control="text" validate_expression="<?= $exprDate ?>" validate_message="Start Date : <?= $msgDate ?>"/>
						<input value="<?= $startTime ?>" class="timepicker" type="text" name="startTime" size="12" required pattern="<?= $exprTime ?>" validate="true" validate_control="text" validate_expression="<?= $exprTime ?>" validate_message="Start Time: <?= $msgTime ?>"/>
					</div>
<?php 
	$endDate = "";
	$endTime = "";
	if (! is_null($schedule1->getEndTime()))	{
		$list = DateAndTime::convertFromSystemDateAndTimeFormatToGUIDateAndTimeFormat($schedule1->getEndTime()->getDateAndTimeString());
		$endDate = $list['date'];
		$endTime = $list['time'];
	}
?>
					<div class="pure-control-group">
						<label for="startDate">End Time </label>
						<input value="<?= $endDate ?>" class="datepicker"type="text" name="endDate" id="endDate" size="12" required pattern="<?= $exprDate ?>" validate="true" validate_control="text" validate_expression="<?= $exprDate ?>" validate_message="End Date : <?= $msgDate ?>"/>
						<input value="<?= $endTime ?>" class="timepicker" type="text" name="endTime" size="12" required required pattern="<?= $exprTime ?>" validate="true" validate_control="text" validate_expression="<?= $exprTime ?>" validate_message="End Time: <?= $msgTime ?>" />
					</div>
					<div class="pure-control-group">
						<label title="Color which will be used to show this schedule on the grid" for="textColor">Text Color </label>
						<input value="<?= $schedule1->getTextColor() ?>" type="text" name="textColor" id="textColor" size="24" required pattern="<?= $exprHexColor ?>" validate="true" validate_control="text" validate_expression="<?= $exprHexColor ?>" validate_message="Text Color : <?= $msgHexColor ?>"/>
					</div>
					<div class="pure-control-group">
						<label title="Background Color which will be used to show this schedule on the grid" for="backgroundColor">Background Color </label>
						<input value="<?= $schedule1->getBackgroundColor() ?>" type="text" name="backgroundColor" id="backgroundColor" size="24" required pattern="<?= $exprHexColor ?>" validate="true" validate_control="text" validate_expression="<?= $exprHexColor ?>" validate_message="Background Color : <?= $msgHexColor ?>"/>
					</div>
					
					
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input value="<?= $schedule1->getExtraInformation() ?>" type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Edit Record"/>
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
	} else if ($page == "manageschedule_detail" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "manageschedule_detail", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$schedule1 = null;
		try {
			$schedule1 = new Schedule($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{die($e->getMessage());}
		mysql_close($conn);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Details for <?= $schedule1->getActivityName() ?></div>
		<div class="ui-sys-panel-content ui-sys-data-capture">
			<table class="pure-table">
				<thead>
					<tr>
						<th>Field Name</th>
						<th>Field Value</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Schedule Name</td>
						<td><?= $schedule1->getActivityName() ?></td>
					</tr>
<?php 
	if (! is_null($schedule1->getVenue()))	{
?>
					<tr>
						<td>Venue</td>
						<td><?= $schedule1->getVenue()->getVenueName() ?></td>
					</tr>
<?php
	}
	if (! is_null($schedule1->getStartTime()))	{
?>
		<tr>
			<td>Start Time</td>
			<td><?= $schedule1->getStartTime()->getDateAndTimeString() ?></td>
		</tr>
<?php
	}
	if (! is_null($schedule1->getEndTime()))	{
?>
		<tr>
			<td>End Time</td>
			<td><?= $schedule1->getEndTime()->getDateAndTimeString() ?></td>
		</tr>
<?php
	}
?>
					<tr>
						<td>Text Color</td>
						<td>
<?php 
	$textColor = $schedule1->getTextColor();
?>				
					<span style="background-color: <?= $textColor ?>;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;<?= $textColor ?>
		
						</td>
					</tr>
					<tr>
						<td>Background Color</td>
						<td>
<?php 
	$textColor = $schedule1->getBackgroundColor();
?>				
					<span style="background-color: <?= $textColor ?>;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;<?= $textColor ?>
		
						</td>
					</tr>
<?php 
	if (! is_null($schedule1->getExtraInformation()))	{
?>
					<tr>
						<td>Extra Information</td>
						<td><?= $schedule1->getExtraInformation() ?></td>
					</tr>
<?php
	}
?>
				</tbody>
			</table><br/>
			<!--Begin of Cross Reference List Insertion-->
<?php 
	if (! is_null($schedule1->getInstructorList()) && sizeof($schedule1->getInstructorList()) > 0)	{
		$objectList1 = $schedule1->getInstructorList();
?>
			<table class="pure-table pure-table-aligned ui-sys-search-results ui-sys-search-results-1">
				<thead>
					<tr>
						<th colspan="5">Scheduled By</th>
					</tr>
					<tr>
						<th></th>
						<th>Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Status</th>
					</tr>
				</thead>
<?php 
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	foreach ($objectList1 as $obj1)	{
		if (is_null($obj1)) continue;
		if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
			$numberOfTBodies++;
			$tbodyClosed = false;
		} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
			$numberOfTBodies++;
			$perTbodyCounter = 0;
		}
		//In All Cases we need to display tr
		$pureTableOddClass = "";
		if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
		<tr class="<?= $pureTableOddClass ?>">
			<td><?= $sqlReturnedRowCount + 1 ?></td>
			<td><?= $obj1->getFullname() ?></td>
			<td><?= $obj1->getEmail() ?></td>
			<td><?= $obj1->getPhone() ?></td>
			<td>
<?php 
	if (! is_null($obj1->getUserStatus()))	{
		echo $obj1->getUserStatus()->getStatusName();
	}
?>			
			</td>
		</tr>
<?php
		$perTbodyCounter++;
		$sqlReturnedRowCount++;
	}
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
?>
			</table>
			<ul class="ui-sys-pagination-1"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination-1').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results-1 tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>

<?php
	}
?>
			<!--End of Cross Reference List Insertion-->
			<br/>
			<!--Begin of Cross Reference List Insertion-->
<?php 
	if (! is_null($schedule1->getGroupList()) && sizeof($schedule1->getGroupList()) > 0)	{
		$objectList1 = $schedule1->getGroupList();
?>
			<table class="pure-table pure-table-aligned ui-sys-search-results ui-sys-search-results-2">
				<thead>
					<tr>
						<th colspan="6">Targeted Groups</th>
					</tr>
					<tr>
						<th></th>
						<th>Group Name</th>
						<th>Parent Group</th>
						<th>Course</th>
						<th>Year</th>
						<th>Semester</th>
					</tr>
				</thead>
<?php 
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	foreach ($objectList1 as $obj1)	{
		if (is_null($obj1)) continue;
		if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
			$numberOfTBodies++;
			$tbodyClosed = false;
		} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
			$numberOfTBodies++;
			$perTbodyCounter = 0;
		}
		//In All Cases we need to display tr
		$pureTableOddClass = "";
		if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
		<tr class="<?= $pureTableOddClass ?>">
			<td><?= $sqlReturnedRowCount + 1 ?></td>
			<td><?= $obj1->getGroupName() ?></td>
			<td>
<?php 
	if (! is_null($obj1->getParentGroup()))	{
		echo $obj1->getParentGroup()->getGroupName();
	}
?>			
			</td>
			<td>
<?php 
	if (! is_null($obj1->getCourse()))	{
		echo $obj1->getCourse()->getCourseName();
	}
?>			
			</td>
			<td><?= $obj1->getYear() ?></td>
			<td><?= $obj1->getSemester() ?></td>
		</tr>
<?php
		$perTbodyCounter++;
		$sqlReturnedRowCount++;
	}
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
?>
			</table>
			<ul class="ui-sys-pagination-2"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination-2').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results-2 tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>

<?php
	}
?>
			<!--End of Cross Reference List Insertion-->
		</div>
		<div class="ui-sys-panel-footer">
			<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "manageschedule_edit", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Edit <?= $schedule1->getActivityName() ?>" href="<?= $thispage ?>?page=manageschedule_edit&id=<?= $schedule1->getScheduleId() ?>"><img alt="DAT" src="../sysimage/buttonedit.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "manageschedule_delete", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Delete <?= $schedule1->getActivityName() ?>" href="<?= $thispage ?>?page=manageschedule_delete&id=<?= $schedule1->getScheduleId() ?>"><img alt="DAT" src="../sysimage/buttondelete.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "manageschedule_edit", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Attach Activity Creators" href="<?= $thispage ?>?page=manageschedule_edit&id=<?= $schedule1->getScheduleId() ?>&attach_login=true"><img alt="DAT" src="../sysimage/buttonattach.png"/></a>
		<a title="Attach Targeted Group" href="<?= $thispage ?>?page=manageschedule_edit&id=<?= $schedule1->getScheduleId() ?>&attach_groups=true"><img alt="DAT" src="../sysimage/buttonattach.png"/></a>
<?php
	}
?>
				<span class="ui-sys-clear-both">&nbsp;</span><br />
			</div>
		</div>
	</div>
<?php
	} else if ($page == "manageschedule_add" && isset($_REQUEST['report']) && Authorize::isAllowable($config, "manageschedule_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$scheduleName = mysql_real_escape_string($_REQUEST['scheduleName']);
		$venueId = mysql_real_escape_string($_REQUEST['venueId']);
		$textColor = mysql_real_escape_string($_REQUEST['textColor']);
		$backgroundColor = mysql_real_escape_string($_REQUEST['backgroundColor']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
		$startTime = DateAndTime::convertFromGUIDateAndTimeFormatToSystemDateAndTimeFormat($_REQUEST['startDate'], $_REQUEST['startTime']);
		$endTime = DateAndTime::convertFromGUIDateAndTimeFormatToSystemDateAndTimeFormat($_REQUEST['endDate'], $_REQUEST['endTime']);
		if ($textColor == "")	$textColor = "000000";
		if ($backgroundColor == "") $backgroundColor = "ffffff";
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel header ui-widget-header">Summary Report for <i><?= $scheduleName ?></i></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	$extraFilter = System::getCodeString(8);
	$query="INSERT INTO schedule (scheduleName, venueId, startTime, endTime, textColor, backgroundColor, extraFilter, extraInformation) VALUES('$scheduleName', '$venueId', '$startTime', '$endTime', '$textColor', '$backgroundColor', '$extraFilter', '$extraInformation')";
	try {
		$startTime1 = new DateAndTime("Zoomtong", $startTime, "Company");
		$endTime1 = new DateAndTime("Zoomtong", $endTime, "Company");
		if (! (($systemTime1->compareDateAndTime($startTime1) < 0) && ($startTime1->compareDateAndTime($endTime1) < 0))) Object::shootException("Schedule[Time Range]: Time range is not possible ");
		mysql_db_query($database, $query, $conn) or Object::shootException("Schedule[Adding]: Could not Add a Record into the System");
		$promise1->setPromise(true);
		$enableUpdate = true;
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">The Schedule <?= $scheduleName ?>, has successful Added to the system</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Adding the schedule<br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageschedule_add", "Added $scheduleName");
	} else if ($page == "manageschedule_add" && Authorize::isAllowable($config, "manageschedule_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Add a New Schedule</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="manageschedule_add"/>
					<input type="hidden" name="report" value="io"/>
					<div class="pure-control-group">
						<label for="scheduleName">Schedule Name </label>
						<input type="text" name="scheduleName" id="scheduleName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Schedule Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="courseId">Venue </label>
						<select id="venueId" name="venueId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Venue">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Venue::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
?>
			<option value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="startDate">Start Time </label>
						<input class="datepicker"type="text" name="startDate" id="startDate" size="12" required pattern="<?= $exprDate ?>" validate="true" validate_control="text" validate_expression="<?= $exprDate ?>" validate_message="Start Date : <?= $msgDate ?>"/>
						<input class="timepicker" type="text" name="startTime" size="12" required pattern="<?= $exprTime ?>" validate="true" validate_control="text" validate_expression="<?= $exprTime ?>" validate_message="Start Time: <?= $msgTime ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="startDate">End Time </label>
						<input class="datepicker"type="text" name="endDate" id="endDate" size="12" required pattern="<?= $exprDate ?>" validate="true" validate_control="text" validate_expression="<?= $exprDate ?>" validate_message="End Date : <?= $msgDate ?>"/>
						<input class="timepicker" type="text" name="endTime" size="12" required required pattern="<?= $exprTime ?>" validate="true" validate_control="text" validate_expression="<?= $exprTime ?>" validate_message="End Time: <?= $msgTime ?>" />
					</div>
					<div class="pure-control-group">
						<label title="Color which will be used to show this schedule on the grid" for="textColor">Text Color </label>
						<input type="text" name="textColor" id="textColor" size="24" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprHexColor ?>" validate_message="Text Color : <?= $msgHexColor ?>"/>
					</div>
					<div class="pure-control-group">
						<label title="Background Color which will be used to show this schedule on the grid" for="backgroundColor">Background Color </label>
						<input type="text" name="backgroundColor" id="backgroundColor" size="24" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprHexColor ?>" validate_message="Background Color : <?= $msgHexColor ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Add Record"/>
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
	} else if ($page == "manageschedule" && Authorize::isAllowable($config, "manageschedule", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Schedules Management</div>
			<div class="ui-sys-panel-body">
<!--Block ONE controls for searching-->
				<div class="ui-sys-search-container pure-form pure-schedule-control">
					<input type="text" title="At Least Three Characters should be supplied" required placeholder="ABC" size="32"/>
					<a title="Click to Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=manageschedule" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
				</div>
<!--Block ONE ends-->	
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
		//saving Authorization Rules 
		$blnAuthorizationDetail = Authorize::isAllowable($config, "manageschedule_detail", "normal", "donotsetlog", "-1", "-1");
?>
<!--Block Two, Search Results-->
<div class="ui-sys-search-results">
	<label id="statustextlabel"></label>
	<table class="pure-table ui-sys-table-search-results">
		<thead>
			<tr>
				<th>S/N</th>
				<th>Activity <br/>Name</th>
				<th>Venue</th>
				<th>Start Time</th>
				<th>End Time</th>
				<th>Text Color</th>
				<th>Background<br/>Color</th>
				<th></th>
			</tr>
		</thead>
<?php 
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
	$query = Schedule::getQueryText();
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$result = mysql_db_query($database, $query, $conn) or die("Could not fetch schedule data from the storage");
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	while (list($id) = mysql_fetch_row($result))	{
		if ($sqlReturnedRowCount > $maximumReturnedRows)	{
			//Finalize All House Keeping Here
			break;
		}
		$schedule1 = null;
		try {
			$schedule1 = new Schedule($database, $id, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated per objects 
		if ($schedule1->searchMatrix($matrix1)->evaluateResult())	{
			if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
				$numberOfTBodies++;
				$tbodyClosed = false;
			} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
				$numberOfTBodies++;
				$perTbodyCounter = 0;
			}
			//In All Cases we need to display tr
			$pureTableOddClass = "";
			if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
			<tr class="<?= $pureTableOddClass ?>">
				<td><?= $sqlReturnedRowCount + 1 ?></td>
				<td><?= $schedule1->getActivityName() ?></td>
				<td>
<?php 
	if (! is_null($schedule1->getVenue()))	{
?>
		<?= $schedule1->getVenue()->getVenueName() ?>
<?php
	}
?>				
				</td>
				<td>
<?php 
	if (! is_null($schedule1->getStartTime()))	{
?>
		<?= $schedule1->getStartTime()->getDateAndTimeString() ?>
<?php
	}
?>
				</td>
				<td>
<?php 
	if (! is_null($schedule1->getEndTime()))	{
?>
		<?= $schedule1->getEndTime()->getDateAndTimeString() ?>
<?php
	}
?>				
				</td>
				<td>
<?php 
	$textColor = $schedule1->getTextColor();
?>				
					<span style="background-color: <?= $textColor ?>;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;<?= $textColor ?>
				</td>
				<td>
<?php 
	$textColor = $schedule1->getBackgroundColor();
?>				
					<span style="background-color: <?= $textColor ?>;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;<?= $textColor ?>
				</td>
				<td>
<?php 
	if ($blnAuthorizationDetail)	{
?>
		<a href="<?= $thispage ?>?page=manageschedule_detail&id=<?= $schedule1->getScheduleId() ?>" class="ui-sys-search-results-controls"><img alt="DAT" src="../sysimage/buttondetail.png"/></a>
<?php
	}
?>				
				</td>
			</tr>
<?php
			$perTbodyCounter++;
			$sqlReturnedRowCount++;
		}//end-evaluate-results
	}//end-while
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
	mysql_close($conn);
?>
	</table>
	<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
	<ul class="ui-sys-pagination"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
</div>
<!--Block Two Ends-->
<?php
	} //end-if-searchtext
?>		
<!--Beginning of Block Three-->
				<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "manageschedule_csv", "normal", "donotsetlog", "-1", "-1")) {
?>
		<a title="Download CSV/EXCEL Formatted Data" href="<?= $thispage ?>?page=manageschedule_csv"><img alt="DAT" src="../sysimage/buttoncsv.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "manageschedule_add", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Add a New System Schedule" href="<?= $thispage ?>?page=manageschedule_add"><img alt="DAT" src="../sysimage/buttonadd.png"/></a>
<?php	
	}
?>				
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	}/*Schedule End*/ else/*Holiday BEGIN*/if ($page == "manageholiday_csv" && isset($_REQUEST['report']) && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "manageholiday_csv", "normal", "setlog", "-1", "-1"))	{
		$filename=$_REQUEST['report'];
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Holiday CSV File<br/> (Download Report)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (unlink($filename))	{
?>
		<div class="ui-state-highlight">
			The temporary file has been removed succesful from the Server
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problem on removing the temporary file from the Server
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	} else if ($page == "manageholiday_csv" && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "manageholiday_csv", "normal", "setlog", "-1", "-1"))	{
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$promise1 = new Promise();
		$promise1->setPromise(true);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Holiday CSV File <br/> (Download CSV File)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	$filename="";
	$tfname=$filename.$date;
	$tfname=$tfname.rand(100,999);
	$filename=sha1($tfname);
	$filename=$filename.".csv";
	$filename="../temp/".$filename;
	$searchText = Object::getSearchTextFromUserFieldData($_REQUEST['controlIndex'], $_REQUEST['fieldname'], $_REQUEST['op'], $_REQUEST['namespaceTag'], $_REQUEST['fieldvalue']);
	$lineCounter = 0;
	$file1 = fopen($filename, "w") or die("Could not Open the File for writing");
	$csvProcessor1 = null;
	try {
		$csvProcessor1 = new CSVProcessor($searchText, $file1);
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$primise1->setPromise(false);
	}
	$result = null;
	if ($promise1->isPromising())	{
		$query = Holiday::getQueryText();
		try {
			$result = mysql_db_query($database, $query, $conn) or Object::shootException("Holiday[CSV]: Could not Execute Query");
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$primise1->setPromise(false);
		}
	}
	if ($promise1->isPromising())	{
		while (list($id) = mysql_fetch_row($result))	{
			$holiday1 = null;
			try	{
				$holiday1 = new Holiday($database, $id, $conn);
			} catch (Exception $e)	{ die($e->getMessage()); }
			$csvProcessor1->reload(); //Clear the Previous Object Properties 
			if ($holiday1->processCSV($csvProcessor1)->evaluateResult())	{
				$lineCounter++;
			}
		}//end-while 
	}//end-if-promising
	fclose($file1);
	if ($promise1->isPromising())	{
?>
		<div>
			Your file is Ready for downloading<br/>
			Number data rows it contains is <?= $lineCounter ?><br/><br/>
			<b style="font-size: 1.1em;"><a href="<?= $filename ?>">Click Here</a></b> to download your file<br/><br/>
			Once the download is complete, kindly click the proceed button 
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were problems in generating your file <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>					
			</div>
			<div class="ui-sys-panel-footer">
				<div id="perror" class="ui-sys-error-message"></div>
<?php 
	if ($promise1->isPromising())	{
		if ($lineCounter > 0)	{
?>
			<a id="__id_csv_previewer" data-error-control="perror" data-server-forward-path="../server/getcsvpreviewer.php" data-file-to-read="<?= $filename ?>" data-dialog-container="__id_general_dialog_holder" class="button-link">Preview</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php
		}
?>
		<a class="button-link" href="<?= $thispage ?>?page=manageholiday_csv&report=<?= $filename ?>&downloadable=true">Continue</a>
<?php
	}
?>			
			</div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "manageholiday_csv" && isset($_REQUEST['sortable']) && Authorize::isAllowable($config, "manageholiday_csv", "normal", "setlog", "-1", "-1"))	{
		$enableUpdate = false;
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Holiday CSV File <br/> (Column Sorting)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (isset($_REQUEST['fieldname']) && isset($_REQUEST['fieldtype']) && isset($_REQUEST['namespaceTag']) && isset($_REQUEST['fieldvalue']))	{
		$enableUpdate = true;
?>
				<form class="pure-form" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="manageholiday_csv"/>
					<input type="hidden" name="downloadable" value="true"/>
<?php 
	$sortableContainer = Object::getUICSVDivSortable(Object::getLineToSortFromUserFieldData($_REQUEST['fieldname'], $_REQUEST['fieldtype'], $_REQUEST['namespaceTag'],$_REQUEST['fieldvalue']));
	echo $sortableContainer;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
<?php 
	} else {
?>
		Perhaps you did not select any column to be included in your CSV File <br/>
		Kindly go back to Column Selection and select atleast ONE Column
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer">
				<a class="button-link" href="<?= $thispage ?>?page=manageholiday_csv">Back to Column Selection</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php 
	if ($enableUpdate)	{
?>				
				<input type="button" id="__add_record" value="Proceed"/>
<?php 
	}
?>
			</div>
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
	} else if ($page == "manageholiday_csv" && Authorize::isAllowable($config, "manageholiday_csv", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Holiday CSV File <br/>(Column Selection)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
						<input type="hidden" name="page" value="manageholiday_csv"/>
						<input type="hidden" name="sortable" value="true"/>
<?php
	$listOfAvailableColumnsInTables = Object::getUICSVTables(Holiday::getAvailableViewableColumns());
	echo $listOfAvailableColumnsInTables;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer">
				<input type="button" id="__add_record" value="Proceed"/>
			</div>
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
	} else if ($page == "manageholiday_delete" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "manageholiday_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$holiday1 = null;
		try {
			 $holiday1 = new Holiday($database, $_REQUEST['id'], $conn);
			 $holiday1->commitDelete();
			 $enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason("You might be dealing with a Holiday which is no longer existing in the system or a None Empty Record");
			$promise1->setPromise(false);
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Holiday Removal Report</div>
			<div class="ui-sys-panel-pody ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			The Holiday <?= $holiday1->getHolidayName() ?> has been successful removed from the system
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Removing the Holiday <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageholiday_delete", "Deleted ".$holiday1->getHolidayName());
	} else if ($page == "manageholiday_delete" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "manageholiday_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to database services");
		$holiday1 = null;
		try {
			$holiday1 = new Holiday($database, $_REQUEST['id'], $conn);
			$holiday1->setExtraFilter(System::getCodeString(8));
			$holiday1->commitUpdate();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Delete Confirmation</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<div class="ui-sys-warning">
					You are about to remove a Holiday <b><?= $holiday1->getHolidayName() ?></b> from the System <br/>
					<b>NOTE: This Operation Can not be reversed</b><br/>
					Are You Sure You want to remove the Holiday <?= $holiday1->getHolidayName() ?> from the System?
				</div>
				<div class="ui-sys-center-text">
					<a class="button-link-warning" href="<?= $thispage ?>?page=manageholiday_delete&report=io&id=<?= $holiday1->getHolidayId() ?>&rndx=<?= $holiday1->getExtraFilter() ?>">Yes (Remove)</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a class="button-link" href="<?= $thispage ?>?page=manageholiday">No (Cancel)</a>
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "manageholiday_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "manageholiday_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$holiday1 = null;
		try {
			$holiday1 = new Holiday($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$promise1 = new Promise();
		$promise1->setReason("Nothing Has been Updated");
		$enableUpdate = false;
		$holidayName = mysql_real_escape_string($_REQUEST['holidayName']);
		$holidayTime = mysql_real_escape_string($_REQUEST['holidayTime']);
		$textColor = mysql_real_escape_string($_REQUEST['textColor']);
		$backgroundColor = mysql_real_escape_string($_REQUEST['backgroundColor']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
		$holidayTime = $holidayTime."/*";
		$holidayTime = DateAndTime::convertFromGUIDateFormatToSystemDateAndTimeFormat($holidayTime); 
		if ($holidayName != $holiday1->getHolidayName())	{
			$holiday1->setHolidayName($holidayName); $enableUpdate = true;
		}
		if (is_null($holiday1->getHolidayTime()) || ($holiday1->getHolidayTime()->getDateAndTimeString() != $holidayTime))	{
			$holiday1->setHolidayTime($holidayTime); $enableUpdate = true;
		}
		if ($textColor != $holiday1->getTextColor())	{
			$holiday1->setTextColor($textColor); $enableUpdate = true;
		}
		if ($backgroundColor != $holiday1->getBackgroundColor())	{
			$holiday1->setBackgroundColor($backgroundColor); $enableUpdate = true;
		}
		if ($extraInformation != $holiday1->getExtraInformation())	{
			$holiday1->setExtraInformation($extraInformation); $enableUpdate = true;
		}
		//This is the Last 
		if (trim($_REQUEST['rndx']) != $holiday1->getExtraFilter())	{
			$enableUpdate = false;
			$promise1->setReason("The System has Detected you are replaying data in your Browser window");
			$promise1->setPromise(false);
		}
		//Now proceed with general find a new value of extra Filter 
		$holiday1->setExtraFilter(System::getCodeString(8));
		if ($enableUpdate)	{
			try {
				$holiday1->commitUpdate();
				$promise1->setPromise(true);
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
				$enableUpdate = false;
			}
		} 
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Holiday Editing Report</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Holiday <?= $holiday1->getHolidayName() ?> has been updated successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were no updates for the holiday <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageholiday_edit", "Edited ".$holiday1->getHolidayName());
	} else if ($page == "manageholiday_edit" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "manageholiday_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$holiday1 = null;
		$extraFilter = System::getCodeString(8);
		try {
			$holiday1 = new Holiday($database, $_REQUEST['id'], $conn);
			$holiday1->setExtraFilter($extraFilter);
			$holiday1->commitUpdate();
		} catch (Exception $e)	{ die($e->getMessage()); }
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Edit Holiday <?= $holiday1->getHolidayName() ?></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="manageholiday_edit"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="id" value="<?= $holiday1->getHolidayId() ?>"/>
					<input type="hidden" name="rndx" value="<?= $holiday1->getExtraFilter() ?>"/>
					<div class="pure-control-group">
						<label for="holidayName">Holiday Name </label>
						<input value="<?= $holiday1->getHolidayName() ?>" type="text" name="holidayName" id="holidayName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Holiday Name : <?= $msgA48Name ?>"/>
					</div>
<?php 
	$holidayTime = $holiday1->getHolidayTime();
	if (! is_null($holidayTime))	{
		$holidayTime = System::numberWidthCorrection($holidayTime->getDay(), 2)."/".System::numberWidthCorrection($holidayTime->getMonth(), 2);
	} else {
		$holidayTime = "";
	}
?>
					<div class="pure-control-group">
						<label for="holidayTime">Date </label>
						<input value="<?= $holidayTime ?>" type="text" name="holidayTime" id="holidayTime" size="24" required pattern="<?= $exprDateNoYear ?>" validate="true" validate_control="text" validate_expression="<?= $exprDateNoYear ?>" validate_message="Date : <?= $msgDateNoYear ?>"/>
					</div>
					<div class="pure-control-group">
						<label title="Color which will be used to show this holiday on the grid" for="textColor">Text Color </label>
						<input value="<?= $holiday1->getTextColor() ?>" type="text" name="textColor" id="textColor" size="24" required pattern="<?= $exprHexColor ?>" validate="true" validate_control="text" validate_expression="<?= $exprHexColor ?>" validate_message="Text Color : <?= $msgHexColor ?>"/>
					</div>
					<div class="pure-control-group">
						<label title="Background Color which will be used to show this holiday on the grid" for="backgroundColor">Background Color </label>
						<input value="<?= $holiday1->getBackgroundColor() ?>" type="text" name="backgroundColor" id="backgroundColor" size="24" required pattern="<?= $exprHexColor ?>" validate="true" validate_control="text" validate_expression="<?= $exprHexColor ?>" validate_message="Background Color : <?= $msgHexColor ?>"/>
					</div>
					
					
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input value="<?= $holiday1->getExtraInformation() ?>" type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Edit Record"/>
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
	} else if ($page == "manageholiday_detail" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "manageholiday_detail", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$holiday1 = null;
		try {
			$holiday1 = new Holiday($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{die($e->getMessage());}
		mysql_close($conn);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Details for <?= $holiday1->getHolidayName() ?></div>
		<div class="ui-sys-panel-content ui-sys-data-capture">
			<table class="pure-table">
				<thead>
					<tr>
						<th>Field Name</th>
						<th>Field Value</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Holiday Name</td>
						<td><?= $holiday1->getHolidayName() ?></td>
					</tr>
					<tr>
						<td>Date </td>
						<td>
<?php 
	if (! is_null($holiday1->getHolidayTime()))	{
		$holidayTime1 = $holiday1->getHolidayTime();
		$dtString = System::dayOfAMonthValueAdjustment($holidayTime1->getDay());
		$month1 = null;
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		try {
			$monthId = MonthOfAYear::getMonthOfAYearReferenceFromMonthNumber($database, $holidayTime1->getMonth(), $conn);
			$month1 = new MonthOfAYear($database, $monthId, $conn);
			$dtString .= " of ".$month1->getMonthName();
		} catch (Exception $e)	{ die($e->getMessage()); }
		mysql_close($conn);
					echo $dtString;
	}
?>	
						</td>
					</tr>
					<tr>
						<td>Text Color</td>
						<td>
<?php 
	$textColor = $holiday1->getTextColor();
?>				
					<span style="background-color: <?= $textColor ?>;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;<?= $textColor ?>
		
						</td>
					</tr>
					<tr>
						<td>Background Color</td>
						<td>
<?php 
	$textColor = $holiday1->getBackgroundColor();
?>				
					<span style="background-color: <?= $textColor ?>;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;<?= $textColor ?>
		
						</td>
					</tr>
<?php 
	if (! is_null($holiday1->getExtraInformation()))	{
?>
					<tr>
						<td>Extra Information</td>
						<td><?= $holiday1->getExtraInformation() ?></td>
					</tr>
<?php
	}
?>
				</tbody>
			</table>
		</div>
		<div class="ui-sys-panel-footer">
			<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "manageholiday_edit", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Edit <?= $holiday1->getHolidayName() ?>" href="<?= $thispage ?>?page=manageholiday_edit&id=<?= $holiday1->getHolidayId() ?>"><img alt="DAT" src="../sysimage/buttonedit.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "manageholiday_delete", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Delete <?= $holiday1->getHolidayName() ?>" href="<?= $thispage ?>?page=manageholiday_delete&id=<?= $holiday1->getHolidayId() ?>"><img alt="DAT" src="../sysimage/buttondelete.png"/></a>
<?php
	}
?>
				<span class="ui-sys-clear-both">&nbsp;</span><br />
			</div>
		</div>
	</div>
<?php
	} else if ($page == "manageholiday_add" && isset($_REQUEST['report']) && Authorize::isAllowable($config, "manageholiday_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$holidayName = mysql_real_escape_string($_REQUEST['holidayName']);
		$holidayTime = mysql_real_escape_string($_REQUEST['holidayTime']);
		$textColor = mysql_real_escape_string($_REQUEST['textColor']);
		$backgroundColor = mysql_real_escape_string($_REQUEST['backgroundColor']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
		$holidayTime = $holidayTime."/*";
		$holidayTime = DateAndTime::convertFromGUIDateFormatToSystemDateAndTimeFormat($holidayTime); 
		if ($textColor == "")	$textColor = "000000";
		if ($backgroundColor == "") $backgroundColor = "ffffff";
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel header ui-widget-header">Summary Report for <i><?= $holidayName ?></i></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	$extraFilter = System::getCodeString(8);
	$query="INSERT INTO holiday (holidayName, holidayTime, textColor, backgroundColor, extraFilter, extraInformation) VALUES('$holidayName', '$holidayTime', '$textColor', '$backgroundColor', '$extraFilter', '$extraInformation')";
	try {
		mysql_db_query($database, $query, $conn) or Object::shootException("Holiday[Adding]: Could not Add a Record into the System");
		$promise1->setPromise(true);
		$enableUpdate = true;
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">The Holiday <?= $holidayName ?>, has successful Added to the system</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Adding the holiday<br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageholiday_add", "Added $holidayName");
	} else if ($page == "manageholiday_add" && Authorize::isAllowable($config, "manageholiday_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Add a New Holiday</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="manageholiday_add"/>
					<input type="hidden" name="report" value="io"/>
					<div class="pure-control-group">
						<label for="holidayName">Holiday Name </label>
						<input type="text" name="holidayName" id="holidayName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Holiday Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="holidayTime">Date </label>
						<input type="text" name="holidayTime" id="holidayTime" size="24" required pattern="<?= $exprDateNoYear ?>" validate="true" validate_control="text" validate_expression="<?= $exprDateNoYear ?>" validate_message="Date : <?= $msgDateNoYear ?>"/>
					</div>
					<div class="pure-control-group">
						<label title="Color which will be used to show this holiday on the grid" for="textColor">Text Color </label>
						<input type="text" name="textColor" id="textColor" size="24" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprHexColor ?>" validate_message="Text Color : <?= $msgHexColor ?>"/>
					</div>
					<div class="pure-control-group">
						<label title="Background Color which will be used to show this holiday on the grid" for="backgroundColor">Background Color </label>
						<input type="text" name="backgroundColor" id="backgroundColor" size="24" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprHexColor ?>" validate_message="Background Color : <?= $msgHexColor ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Add Record"/>
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
	} else if ($page == "manageholiday" && Authorize::isAllowable($config, "manageholiday", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Holidays Management</div>
			<div class="ui-sys-panel-body">
<!--Block ONE controls for searching-->
				<div class="ui-sys-search-container pure-form pure-holiday-control">
					<input type="text" title="At Least Three Characters should be supplied" required placeholder="ABC" size="32"/>
					<a title="Click to Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=manageholiday" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
				</div>
<!--Block ONE ends-->	
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
		//saving Authorization Rules 
		$blnAuthorizationDetail = Authorize::isAllowable($config, "manageholiday_detail", "normal", "donotsetlog", "-1", "-1");
?>
<!--Block Two, Search Results-->
<div class="ui-sys-search-results">
	<label id="statustextlabel"></label>
	<table class="pure-table ui-sys-table-search-results">
		<thead>
			<tr>
				<th>S/N</th>
				<th>Name of the Holiday</th>
				<th>Date</th>
				<th>Text Color</th>
				<th>Background<br/>Color</th>
				<th></th>
			</tr>
		</thead>
<?php 
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
	$query = Holiday::getQueryText();
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$result = mysql_db_query($database, $query, $conn) or die("Could not fetch holiday data from the storage");
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	while (list($id) = mysql_fetch_row($result))	{
		if ($sqlReturnedRowCount > $maximumReturnedRows)	{
			//Finalize All House Keeping Here
			break;
		}
		$holiday1 = null;
		try {
			$holiday1 = new Holiday($database, $id, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated per objects 
		if ($holiday1->searchMatrix($matrix1)->evaluateResult())	{
			if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
				$numberOfTBodies++;
				$tbodyClosed = false;
			} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
				$numberOfTBodies++;
				$perTbodyCounter = 0;
			}
			//In All Cases we need to display tr
			$pureTableOddClass = "";
			if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
			<tr class="<?= $pureTableOddClass ?>">
				<td><?= $sqlReturnedRowCount + 1 ?></td>
				<td><?= $holiday1->getHolidayName() ?></td>
				<td>
<?php 
	if (! is_null($holiday1->getHolidayTime()))	{
		$holidayTime1 = $holiday1->getHolidayTime();
		$dtString = System::dayOfAMonthValueAdjustment($holidayTime1->getDay());
		$month1 = null;
		try {
			$monthId = MonthOfAYear::getMonthOfAYearReferenceFromMonthNumber($database, $holidayTime1->getMonth(), $conn);
			$month1 = new MonthOfAYear($database, $monthId, $conn);
			$dtString .= " of ".$month1->getMonthName();
		} catch (Exception $e)	{ die($e->getMessage()); }
					echo $dtString;
	}
?>				
				</td>
				<td>
<?php 
	$textColor = $holiday1->getTextColor();
?>				
					<span style="background-color: <?= $textColor ?>;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;<?= $textColor ?>
				</td>
				<td>
<?php 
	$textColor = $holiday1->getBackgroundColor();
?>				
					<span style="background-color: <?= $textColor ?>;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;<?= $textColor ?>
				</td>
				<td>
<?php 
	if ($blnAuthorizationDetail)	{
?>
		<a href="<?= $thispage ?>?page=manageholiday_detail&id=<?= $holiday1->getHolidayId() ?>" class="ui-sys-search-results-controls"><img alt="DAT" src="../sysimage/buttondetail.png"/></a>
<?php
	}
?>				
				</td>
			</tr>
<?php
			$perTbodyCounter++;
			$sqlReturnedRowCount++;
		}//end-evaluate-results
	}//end-while
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
	mysql_close($conn);
?>
	</table>
	<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
	<ul class="ui-sys-pagination"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
</div>
<!--Block Two Ends-->
<?php
	} //end-if-searchtext
?>		
<!--Beginning of Block Three-->
				<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "manageholiday_csv", "normal", "donotsetlog", "-1", "-1")) {
?>
		<a title="Download CSV/EXCEL Formatted Data" href="<?= $thispage ?>?page=manageholiday_csv"><img alt="DAT" src="../sysimage/buttoncsv.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "manageholiday_add", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Add a New System Holiday" href="<?= $thispage ?>?page=manageholiday_add"><img alt="DAT" src="../sysimage/buttonadd.png"/></a>
<?php	
	}
?>				
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	}/*Holiday End*/ else/*Venue BEGIN*/if ($page == "managevenue_csv" && isset($_REQUEST['report']) && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managevenue_csv", "normal", "setlog", "-1", "-1"))	{
		$filename=$_REQUEST['report'];
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Venue CSV File<br/> (Download Report)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (unlink($filename))	{
?>
		<div class="ui-state-highlight">
			The temporary file has been removed succesful from the Server
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problem on removing the temporary file from the Server
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	} else if ($page == "managevenue_csv" && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managevenue_csv", "normal", "setlog", "-1", "-1"))	{
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$promise1 = new Promise();
		$promise1->setPromise(true);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Venue CSV File <br/> (Downlad CSV File)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	$filename="";
	$tfname=$filename.$date;
	$tfname=$tfname.rand(100,999);
	$filename=sha1($tfname);
	$filename=$filename.".csv";
	$filename="../temp/".$filename;
	$searchText = Object::getSearchTextFromUserFieldData($_REQUEST['controlIndex'], $_REQUEST['fieldname'], $_REQUEST['op'], $_REQUEST['namespaceTag'], $_REQUEST['fieldvalue']);
	$lineCounter = 0;
	$file1 = fopen($filename, "w") or die("Could not Open the File for writing");
	$csvProcessor1 = null;
	try {
		$csvProcessor1 = new CSVProcessor($searchText, $file1);
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$primise1->setPromise(false);
	}
	$result = null;
	if ($promise1->isPromising())	{
		$query = Venue::getQueryText();
		try {
			$result = mysql_db_query($database, $query, $conn) or Object::shootException("Venue[CSV]: Could not Execute Query");
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$primise1->setPromise(false);
		}
	}
	if ($promise1->isPromising())	{
		while (list($id) = mysql_fetch_row($result))	{
			$venue1 = null;
			try	{
				$venue1 = new Venue($database, $id, $conn);
			} catch (Exception $e)	{ die($e->getMessage()); }
			$csvProcessor1->reload(); //Clear the Previous Object Properties 
			if ($venue1->processCSV($csvProcessor1)->evaluateResult())	{
				$lineCounter++;
			}
		}//end-while 
	}//end-if-promising
	fclose($file1);
	if ($promise1->isPromising())	{
?>
		<div>
			Your file is Ready for downloading<br/>
			Number data rows it contains is <?= $lineCounter ?><br/><br/>
			<b style="font-size: 1.1em;"><a href="<?= $filename ?>">Click Here</a></b> to download your file<br/><br/>
			Once the download is complete, kindly click the proceed button 
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were problems in generating your file <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>					
			</div>
			<div class="ui-sys-panel-footer">
				<div id="perror" class="ui-sys-error-message"></div>
<?php 
	if ($promise1->isPromising())	{
		if ($lineCounter > 0)	{
?>
			<a id="__id_csv_previewer" data-error-control="perror" data-server-forward-path="../server/getcsvpreviewer.php" data-file-to-read="<?= $filename ?>" data-dialog-container="__id_general_dialog_holder" class="button-link">Preview</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php
		}
?>
		<a class="button-link" href="<?= $thispage ?>?page=managevenue_csv&report=<?= $filename ?>&downloadable=true">Continue</a>
<?php
	}
?>			
			</div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managevenue_csv" && isset($_REQUEST['sortable']) && Authorize::isAllowable($config, "managevenue_csv", "normal", "setlog", "-1", "-1"))	{
		$enableUpdate = false;
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Venue CSV File <br/> (Column Sorting)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (isset($_REQUEST['fieldname']) && isset($_REQUEST['fieldtype']) && isset($_REQUEST['namespaceTag']) && isset($_REQUEST['fieldvalue']))	{
		$enableUpdate = true;
?>
				<form class="pure-form" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managevenue_csv"/>
					<input type="hidden" name="downloadable" value="true"/>
<?php 
	$sortableContainer = Object::getUICSVDivSortable(Object::getLineToSortFromUserFieldData($_REQUEST['fieldname'], $_REQUEST['fieldtype'], $_REQUEST['namespaceTag'],$_REQUEST['fieldvalue']));
	echo $sortableContainer;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
<?php 
	} else {
?>
		Perhaps you did not select any column to be included in your CSV File <br/>
		Kindly go back to Column Selection and select atleast ONE Column
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer">
				<a class="button-link" href="<?= $thispage ?>?page=managevenue_csv">Back to Column Selection</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php 
	if ($enableUpdate)	{
?>				
				<input type="button" id="__add_record" value="Proceed"/>
<?php 
	}
?>
			</div>
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
	} else if ($page == "managevenue_csv" && Authorize::isAllowable($config, "managevenue_csv", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Venue CSV File <br/>(Column Selection)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
						<input type="hidden" name="page" value="managevenue_csv"/>
						<input type="hidden" name="sortable" value="true"/>
<?php
	$listOfAvailableColumnsInTables = Object::getUICSVTables(Venue::getAvailableViewableColumns());
	echo $listOfAvailableColumnsInTables;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer">
				<input type="button" id="__add_record" value="Proceed"/>
			</div>
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
	} else if ($page == "managevenue_delete" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managevenue_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$venue1 = null;
		try {
			 $venue1 = new Venue($database, $_REQUEST['id'], $conn);
			 $venue1->commitDelete();
			 $enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason("You might be dealing with a Venue which is no longer existing in the system or a None Empty Record");
			$promise1->setPromise(false);
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Venue Removal Report</div>
			<div class="ui-sys-panel-pody ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			The Venue <?= $venue1->getVenueName() ?> has been successful removed from the system
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Removing the Venue <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managevenue_delete", "Deleted ".$venue1->getVenueName());
	} else if ($page == "managevenue_delete" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managevenue_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to database services");
		$venue1 = null;
		try {
			$venue1 = new Venue($database, $_REQUEST['id'], $conn);
			$venue1->setExtraFilter(System::getCodeString(8));
			$venue1->commitUpdate();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Delete Confirmation</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<div class="ui-sys-warning">
					You are about to remove a Venue <b><?= $venue1->getVenueName() ?></b> from the System <br/>
					<b>NOTE: This Operation Can not be reversed</b><br/>
					Are You Sure You want to remove the Venue <?= $venue1->getVenueName() ?> from the System?
				</div>
				<div class="ui-sys-center-text">
					<a class="button-link-warning" href="<?= $thispage ?>?page=managevenue_delete&report=io&id=<?= $venue1->getVenueId() ?>&rndx=<?= $venue1->getExtraFilter() ?>">Yes (Remove)</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a class="button-link" href="<?= $thispage ?>?page=managevenue">No (Cancel)</a>
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managevenue_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managevenue_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$venue1 = null;
		try {
			$venue1 = new Venue($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$promise1 = new Promise();
		$promise1->setReason("Nothing Has been Updated");
		$enableUpdate = false;
		$venueName = mysql_real_escape_string($_REQUEST['venueName']);
		$venueCode = mysql_real_escape_string($_REQUEST['venueCode']);
		$capacity = mysql_real_escape_string($_REQUEST['capacity']);
		$numberOfConcurrentAllocations = mysql_real_escape_string($_REQUEST['numberOfConcurrentAllocations']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
		if ($venueName != $venue1->getVenueName())	{
			$venue1->setVenueName($venueName); $enableUpdate = true;
		}
		if ($venueCode != $venue1->getVenueCode())	{
			$venue1->setVenueCode($venueCode); $enableUpdate = true;
		}
		if ($capacity != $venue1->getCapacity())	{
			$venue1->setCapacity($capacity); $enableUpdate = true;
		}
		if ($numberOfConcurrentAllocations != $venue1->getNumberOfConcurrentAllocations())	{
			$venue1->setNumberOfConcurrentAllocations($numberOfConcurrentAllocations); $enableUpdate = true;
		}
		if ($extraInformation != $venue1->getExtraInformation())	{
			$venue1->setExtraInformation($extraInformation); $enableUpdate = true;
		}
		//This is the Last 
		if (trim($_REQUEST['rndx']) != $venue1->getExtraFilter())	{
			$enableUpdate = false;
			$promise1->setReason("The System has Detected you are replaying data in your Browser window");
			$promise1->setPromise(false);
		}
		//Now proceed with general find a new value of extra Filter 
		$venue1->setExtraFilter(System::getCodeString(8));
		if ($enableUpdate)	{
			try {
				$venue1->commitUpdate();
				$promise1->setPromise(true);
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
				$enableUpdate = false;
			}
		} 
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Venue Editing Report</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Venue <?= $venue1->getVenueName() ?> has been updated successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were no updates for the venue <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managevenue_edit", "Edited ".$venue1->getVenueName());
	} else if ($page == "managevenue_edit" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managevenue_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$venue1 = null;
		$extraFilter = System::getCodeString(8);
		try {
			$venue1 = new Venue($database, $_REQUEST['id'], $conn);
			$venue1->setExtraFilter($extraFilter);
			$venue1->commitUpdate();
		} catch (Exception $e)	{ die($e->getMessage()); }
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Edit Venue <?= $venue1->getVenueName() ?></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managevenue_edit"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="id" value="<?= $venue1->getVenueId() ?>"/>
					<input type="hidden" name="rndx" value="<?= $venue1->getExtraFilter() ?>"/>
					<div class="pure-control-group">
						<label for="venueName">Venue Name </label>
						<input value="<?= $venue1->getVenueName() ?>" type="text" name="venueName" id="venueName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Venue Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="venueCode">Venue Code </label>
						<input value="<?= $venue1->getVenueCode() ?>" type="text" name="venueCode" id="venueCode" size="24" required pattern="<?= $exprL8Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL8Name ?>" validate_message="Venue Code : <?= $msgL8Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="capacity">Capacity </label>
						<input value="<?= $venue1->getCapacity() ?>" class="text-can-not-be-zero" type="text" name="capacity" id="capacity" size="24" required pattern="<?= $exprD8Number ?>" validate="true" validate_control="text" validate_expression="<?= $exprD8Number ?>" validate_message="Capacity : <?= $msgD8Number ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="numberOfConcurrentAllocations" title="Maximum Number of Concurrent Allocations, recommended value is 1">Concurrency Limit </label>
						<input value="<?= $venue1->getNumberOfConcurrentAllocations() ?>" class="text-can-not-be-zero" type="text" name="numberOfConcurrentAllocations" id="numberOfConcurrentAllocations" size="24" required placeholder="1" pattern="<?= $expr2Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr2Number ?>" validate_message="Max Concurrent Allocations : <?= $msg2Number ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input value="<?= $venue1->getExtraInformation() ?>" type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Edit Record"/>
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
		var enableSubmit = true;
		$('input.text-can-not-be-zero').each(function(i, v)	{
			var $text1 = $(v);
			enableSubmit = enableSubmit && (parseInt($text1.val()) != 0);
		});
		if (enableSubmit)	{
			generalFormSubmission(this, 'form1', 'perror');
		} else {
			$target1.empty();
			$('<span/>').html('Records Count Can not be Zero')
				.appendTo($target1);
		}
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if ($page == "managevenue_detail" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managevenue_detail", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$venue1 = null;
		try {
			$venue1 = new Venue($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{die($e->getMessage());}
		mysql_close($conn);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Details for <?= $venue1->getVenueName() ?></div>
		<div class="ui-sys-panel-content ui-sys-data-capture">
			<table class="pure-table">
				<thead>
					<tr>
						<th>Field Name</th>
						<th>Field Value</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Venue Name</td>
						<td><?= $venue1->getVenueName() ?></td>
					</tr>
					<tr>
						<td>Venue Code</td>
						<td><?= $venue1->getVenueCode() ?></td>
					</tr>
					<tr>
						<td>Capacity</td>
						<td><?= $venue1->getCapacity() ?></td>
					</tr>
					<tr>
						<td>Current Allocations</td>
						<td><?= $venue1->getNumberOfConcurrentAllocations() ?></td>
					</tr>
<?php 
	if (! is_null($venue1->getExtraInformation()))	{
?>
					<tr>
						<td>Extra Information</td>
						<td><?= $venue1->getExtraInformation() ?></td>
					</tr>
<?php
	}
?>
				</tbody>
			</table>
		</div>
		<div class="ui-sys-panel-footer">
			<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "managevenue_edit", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Edit <?= $venue1->getVenueName() ?>" href="<?= $thispage ?>?page=managevenue_edit&id=<?= $venue1->getVenueId() ?>"><img alt="DAT" src="../sysimage/buttonedit.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "managevenue_delete", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Delete <?= $venue1->getVenueName() ?>" href="<?= $thispage ?>?page=managevenue_delete&id=<?= $venue1->getVenueId() ?>"><img alt="DAT" src="../sysimage/buttondelete.png"/></a>
<?php
	}
?>
				<span class="ui-sys-clear-both">&nbsp;</span><br />
			</div>
		</div>
	</div>
<?php
	} else if ($page == "managevenue_add" && isset($_REQUEST['report']) && Authorize::isAllowable($config, "managevenue_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$venueName = mysql_real_escape_string($_REQUEST['venueName']);
		$venueCode = mysql_real_escape_string($_REQUEST['venueCode']);
		$capacity = mysql_real_escape_string($_REQUEST['capacity']);
		$numberOfConcurrentAllocations = mysql_real_escape_string($_REQUEST['numberOfConcurrentAllocations']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel header ui-widget-header">Summary Report for <i><?= $venueName ?></i></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	$extraFilter = System::getCodeString(8);
	$query="INSERT INTO venue (venueName, venueCode, capacity, numberOfConcurrentAllocations, extraFilter, extraInformation) VALUES('$venueName', '$venueCode', '$capacity', '$numberOfConcurrentAllocations', '$extraFilter', '$extraInformation')";
	try {
		mysql_db_query($database, $query, $conn) or Object::shootException("Venue[Adding]: Could not Add a Record into the System ".mysql_error());
		$promise1->setPromise(true);
		$enableUpdate = true;
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">The Venue <?= $venueName ?>, has successful Added to the system</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Adding the venue<br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managevenue_add", "Added $venueName");
	} else if ($page == "managevenue_add" && Authorize::isAllowable($config, "managevenue_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Add a New Venue</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managevenue_add"/>
					<input type="hidden" name="report" value="io"/>
					<div class="pure-control-group">
						<label for="venueName">Venue Name </label>
						<input type="text" name="venueName" id="venueName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Venue Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="venueCode">Venue Code </label>
						<input type="text" name="venueCode" id="venueCode" size="24" required pattern="<?= $exprL8Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL8Name ?>" validate_message="Venue Code : <?= $msgL8Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="capacity">Capacity </label>
						<input class="text-can-not-be-zero" type="text" name="capacity" id="capacity" size="24" required pattern="<?= $exprD8Number ?>" validate="true" validate_control="text" validate_expression="<?= $exprD8Number ?>" validate_message="Capacity : <?= $msgD8Number ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="numberOfConcurrentAllocations" title="Maximum Number of Concurrent Allocations, recommended value is 1">Concurrency Limit </label>
						<input class="text-can-not-be-zero" type="text" name="numberOfConcurrentAllocations" id="numberOfConcurrentAllocations" size="24" required placeholder="1" pattern="<?= $expr2Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr2Number ?>" validate_message="Max Concurrent Allocations : <?= $msg2Number ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Add Record"/>
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
		var enableSubmit = true;
		$('input.text-can-not-be-zero').each(function(i, v)	{
			var $text1 = $(v);
			enableSubmit = enableSubmit && (parseInt($text1.val()) != 0);
		});
		if (enableSubmit)	{
			generalFormSubmission(this, 'form1', 'perror');
		} else {
			$target1.empty();
			$('<span/>').html('Records Count Can not be Zero')
				.appendTo($target1);
		}
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if ($page == "managevenue" && Authorize::isAllowable($config, "managevenue", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Venues Management</div>
			<div class="ui-sys-panel-body">
<!--Block ONE controls for searching-->
				<div class="ui-sys-search-container pure-form pure-venue-control">
					<input type="text" title="At Least Three Characters should be supplied" required placeholder="ABC" size="32"/>
					<a title="Click to Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=managevenue" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
				</div>
<!--Block ONE ends-->	
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
		//saving Authorization Rules 
		$blnAuthorizationDetail = Authorize::isAllowable($config, "managevenue_detail", "normal", "donotsetlog", "-1", "-1");
?>
<!--Block Two, Search Results-->
<div class="ui-sys-search-results">
	<label id="statustextlabel"></label>
	<table class="pure-table ui-sys-table-search-results">
		<thead>
			<tr>
				<th>S/N</th>
				<th>Venue Name</th>
				<th>Venue Code</th>
				<th>Capacity</th>
				<th>Concurrency<br/>Limit</th>
				<th></th>
			</tr>
		</thead>
<?php 
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
	$query = Venue::getQueryText();
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$result = mysql_db_query($database, $query, $conn) or die("Could not fetch venue data from the storage");
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	while (list($id) = mysql_fetch_row($result))	{
		if ($sqlReturnedRowCount > $maximumReturnedRows)	{
			//Finalize All House Keeping Here
			break;
		}
		$venue1 = null;
		try {
			$venue1 = new Venue($database, $id, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated per objects 
		if ($venue1->searchMatrix($matrix1)->evaluateResult())	{
			if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
				$numberOfTBodies++;
				$tbodyClosed = false;
			} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
				$numberOfTBodies++;
				$perTbodyCounter = 0;
			}
			//In All Cases we need to display tr
			$pureTableOddClass = "";
			if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
			<tr class="<?= $pureTableOddClass ?>">
				<td><?= $sqlReturnedRowCount + 1 ?></td>
				<td><?= $venue1->getVenueName() ?></td>
				<td><?= $venue1->getVenueCode() ?></td>
				<td><?= $venue1->getCapacity() ?></td>
				<td><?= $venue1->getNumberOfConcurrentAllocations() ?></td>
				<td>
<?php 
	if ($blnAuthorizationDetail)	{
?>
		<a href="<?= $thispage ?>?page=managevenue_detail&id=<?= $venue1->getVenueId() ?>" class="ui-sys-search-results-controls"><img alt="DAT" src="../sysimage/buttondetail.png"/></a>
<?php
	}
?>				
				</td>
			</tr>
<?php
			$perTbodyCounter++;
			$sqlReturnedRowCount++;
		}//end-evaluate-results
	}//end-while
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
	mysql_close($conn);
?>
	</table>
	<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
	<ul class="ui-sys-pagination"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
</div>
<!--Block Two Ends-->
<?php
	} //end-if-searchtext
?>		
<!--Beginning of Block Three-->
				<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "managevenue_csv", "normal", "donotsetlog", "-1", "-1")) {
?>
		<a title="Download CSV/EXCEL Formatted Data" href="<?= $thispage ?>?page=managevenue_csv"><img alt="DAT" src="../sysimage/buttoncsv.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "managevenue_add", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Add a New System Venue" href="<?= $thispage ?>?page=managevenue_add"><img alt="DAT" src="../sysimage/buttonadd.png"/></a>
<?php	
	}
?>				
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	}/*Venue End*/ else/*MonthOfAYear BEGIN*/if ($page == "managemonthofayear_csv" && isset($_REQUEST['report']) && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managemonthofayear_csv", "normal", "setlog", "-1", "-1"))	{
		$filename=$_REQUEST['report'];
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">MonthOfAYear CSV File<br/> (Download Report)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (unlink($filename))	{
?>
		<div class="ui-state-highlight">
			The temporary file has been removed succesful from the Server
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problem on removing the temporary file from the Server
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	} else if ($page == "managemonthofayear_csv" && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managemonthofayear_csv", "normal", "setlog", "-1", "-1"))	{
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$promise1 = new Promise();
		$promise1->setPromise(true);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">MonthOfAYear CSV File <br/> (Downlad CSV File)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	$filename="";
	$tfname=$filename.$date;
	$tfname=$tfname.rand(100,999);
	$filename=sha1($tfname);
	$filename=$filename.".csv";
	$filename="../temp/".$filename;
	$searchText = Object::getSearchTextFromUserFieldData($_REQUEST['controlIndex'], $_REQUEST['fieldname'], $_REQUEST['op'], $_REQUEST['namespaceTag'], $_REQUEST['fieldvalue']);
	$lineCounter = 0;
	$file1 = fopen($filename, "w") or die("Could not Open the File for writing");
	$csvProcessor1 = null;
	try {
		$csvProcessor1 = new CSVProcessor($searchText, $file1);
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$primise1->setPromise(false);
	}
	$result = null;
	if ($promise1->isPromising())	{
		$query = MonthOfAYear::getQueryText();
		try {
			$result = mysql_db_query($database, $query, $conn) or Object::shootException("MonthOfAYear[CSV]: Could not Execute Query");
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$primise1->setPromise(false);
		}
	}
	if ($promise1->isPromising())	{
		while (list($id) = mysql_fetch_row($result))	{
			$moy1 = null;
			try	{
				$moy1 = new MonthOfAYear($database, $id, $conn);
			} catch (Exception $e)	{ die($e->getMessage()); }
			$csvProcessor1->reload(); //Clear the Previous Object Properties 
			if ($moy1->processCSV($csvProcessor1)->evaluateResult())	{
				$lineCounter++;
			}
		}//end-while 
	}//end-if-promising
	fclose($file1);
	if ($promise1->isPromising())	{
?>
		<div>
			Your file is Ready for downloading<br/>
			Number data rows it contains is <?= $lineCounter ?><br/><br/>
			<b style="font-size: 1.1em;"><a href="<?= $filename ?>">Click Here</a></b> to download your file<br/><br/>
			Once the download is complete, kindly click the proceed button 
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were problems in generating your file <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>					
			</div>
			<div class="ui-sys-panel-footer">
				<div id="perror" class="ui-sys-error-message"></div>
<?php 
	if ($promise1->isPromising())	{
		if ($lineCounter > 0)	{
?>
			<a id="__id_csv_previewer" data-error-control="perror" data-server-forward-path="../server/getcsvpreviewer.php" data-file-to-read="<?= $filename ?>" data-dialog-container="__id_general_dialog_holder" class="button-link">Preview</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php
		}
?>
		<a class="button-link" href="<?= $thispage ?>?page=managemonthofayear_csv&report=<?= $filename ?>&downloadable=true">Continue</a>
<?php
	}
?>			
			</div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managemonthofayear_csv" && isset($_REQUEST['sortable']) && Authorize::isAllowable($config, "managemonthofayear_csv", "normal", "setlog", "-1", "-1"))	{
		$enableUpdate = false;
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">MonthOfAYear CSV File <br/> (Column Sorting)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (isset($_REQUEST['fieldname']) && isset($_REQUEST['fieldtype']) && isset($_REQUEST['namespaceTag']) && isset($_REQUEST['fieldvalue']))	{
		$enableUpdate = true;
?>
				<form class="pure-form" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managemonthofayear_csv"/>
					<input type="hidden" name="downloadable" value="true"/>
<?php 
	$sortableContainer = Object::getUICSVDivSortable(Object::getLineToSortFromUserFieldData($_REQUEST['fieldname'], $_REQUEST['fieldtype'], $_REQUEST['namespaceTag'],$_REQUEST['fieldvalue']));
	echo $sortableContainer;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
<?php 
	} else {
?>
		Perhaps you did not select any column to be included in your CSV File <br/>
		Kindly go back to Column Selection and select atleast ONE Column
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer">
				<a class="button-link" href="<?= $thispage ?>?page=managemonthofayear_csv">Back to Column Selection</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php 
	if ($enableUpdate)	{
?>				
				<input type="button" id="__add_record" value="Proceed"/>
<?php 
	}
?>
			</div>
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
	} else if ($page == "managemonthofayear_csv" && Authorize::isAllowable($config, "managemonthofayear_csv", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">MonthOfAYear CSV File <br/>(Column Selection)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
						<input type="hidden" name="page" value="managemonthofayear_csv"/>
						<input type="hidden" name="sortable" value="true"/>
<?php
	$listOfAvailableColumnsInTables = Object::getUICSVTables(MonthOfAYear::getAvailableViewableColumns());
	echo $listOfAvailableColumnsInTables;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer">
				<input type="button" id="__add_record" value="Proceed"/>
			</div>
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
	} else if ($page == "managemonthofayear_delete" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managemonthofayear_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$moy1 = null;
		try {
			 $moy1 = new MonthOfAYear($database, $_REQUEST['id'], $conn);
			 $moy1->commitDelete();
			 $enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason("You might be dealing with a MonthOfAYear which is no longer existing in the system or a None Empty Record");
			$promise1->setPromise(false);
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">MonthOfAYear Removal Report</div>
			<div class="ui-sys-panel-pody ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			The MonthOfAYear <?= $moy1->getMonthOfAYearName() ?> has been successful removed from the system
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Removing the MonthOfAYear <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managemonthofayear_delete", "Deleted ".$moy1->getMonthOfAYearName());
	} else if ($page == "managemonthofayear" && Authorize::isAllowable($config, "managemonthofayear", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Months of A Year</div>
			<div class="ui-sys-panel-body">
<!--Block ONE controls for searching-->
				<div class="ui-sys-search-container pure-form pure-monthOfAYear-control">
					<input type="text" title="At Least Three Characters should be supplied" required placeholder="ABC" size="32"/>
					<a title="Click to Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=managemonthofayear" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
				</div>
<!--Block ONE ends-->	
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
?>
<!--Block Two, Search Results-->
<div class="ui-sys-search-results">
	<label id="statustextlabel"></label>
	<table class="pure-table ui-sys-table-search-results">
		<thead>
			<tr>
				<th>S/N</th>
				<th>Name of A Month</th>
				<th>Month <br/>Abbreviation</th>
				<th>Month Number</th>
				<th>Extra <br/>Information</th>
			</tr>
		</thead>
<?php 
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
	$query = MonthOfAYear::getQueryText();
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$result = mysql_db_query($database, $query, $conn) or die("Could not fetch monthOfAYear data from the storage");
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	while (list($id) = mysql_fetch_row($result))	{
		if ($sqlReturnedRowCount > $maximumReturnedRows)	{
			//Finalize All House Keeping Here
			break;
		}
		$moy1 = null;
		try {
			$moy1 = new MonthOfAYear($database, $id, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated per objects 
		if ($moy1->searchMatrix($matrix1)->evaluateResult())	{
			if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
				$numberOfTBodies++;
				$tbodyClosed = false;
			} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
				$numberOfTBodies++;
				$perTbodyCounter = 0;
			}
			//In All Cases we need to display tr
			$pureTableOddClass = "";
			if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
			<tr class="<?= $pureTableOddClass ?>">
				<td><?= $sqlReturnedRowCount + 1 ?></td>
				<td><?= $moy1->getMonthName() ?></td>
				<td><?= $moy1->getMonthAbbreviation() ?></td>
				<td><?= $moy1->getMonthNumber() ?></td>
				<td><?= $moy1->getExtraInformation() ?></td>
			</tr>
<?php
			$perTbodyCounter++;
			$sqlReturnedRowCount++;
		}//end-evaluate-results
	}//end-while
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
	mysql_close($conn);
?>
	</table>
	<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
	<ul class="ui-sys-pagination"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
</div>
<!--Block Two Ends-->
<?php
	} //end-if-searchtext
?>		
<!--Beginning of Block Three-->
				<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "managemonthofayear_csv", "normal", "donotsetlog", "-1", "-1")) {
?>
		<a title="Download CSV/EXCEL Formatted Data" href="<?= $thispage ?>?page=managemonthofayear_csv"><img alt="DAT" src="../sysimage/buttoncsv.png"/></a>
<?php
	}
?>				
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	}/*MonthOfAYear End*/ else/*DaysOfAWeek BEGIN*/if ($page == "managedaysofaweek_csv" && isset($_REQUEST['report']) && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managedaysofaweek_csv", "normal", "setlog", "-1", "-1"))	{
		$filename=$_REQUEST['report'];
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">DaysOfAWeek CSV File<br/> (Download Report)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (unlink($filename))	{
?>
		<div class="ui-state-highlight">
			The temporary file has been removed succesful from the Server
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problem on removing the temporary file from the Server
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	} else if ($page == "managedaysofaweek_csv" && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managedaysofaweek_csv", "normal", "setlog", "-1", "-1"))	{
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$promise1 = new Promise();
		$promise1->setPromise(true);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">DaysOfAWeek CSV File <br/> (Downlad CSV File)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	$filename="";
	$tfname=$filename.$date;
	$tfname=$tfname.rand(100,999);
	$filename=sha1($tfname);
	$filename=$filename.".csv";
	$filename="../temp/".$filename;
	$searchText = Object::getSearchTextFromUserFieldData($_REQUEST['controlIndex'], $_REQUEST['fieldname'], $_REQUEST['op'], $_REQUEST['namespaceTag'], $_REQUEST['fieldvalue']);
	$lineCounter = 0;
	$file1 = fopen($filename, "w") or die("Could not Open the File for writing");
	$csvProcessor1 = null;
	try {
		$csvProcessor1 = new CSVProcessor($searchText, $file1);
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$primise1->setPromise(false);
	}
	$result = null;
	if ($promise1->isPromising())	{
		$query = DaysOfAWeek::getQueryText();
		try {
			$result = mysql_db_query($database, $query, $conn) or Object::shootException("DaysOfAWeek[CSV]: Could not Execute Query");
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$primise1->setPromise(false);
		}
	}
	if ($promise1->isPromising())	{
		while (list($id) = mysql_fetch_row($result))	{
			$dow1 = null;
			try	{
				$dow1 = new DaysOfAWeek($database, $id, $conn);
			} catch (Exception $e)	{ die($e->getMessage()); }
			$csvProcessor1->reload(); //Clear the Previous Object Properties 
			if ($dow1->processCSV($csvProcessor1)->evaluateResult())	{
				$lineCounter++;
			}
		}//end-while 
	}//end-if-promising
	fclose($file1);
	if ($promise1->isPromising())	{
?>
		<div>
			Your file is Ready for downloading<br/>
			Number data rows it contains is <?= $lineCounter ?><br/><br/>
			<b style="font-size: 1.1em;"><a href="<?= $filename ?>">Click Here</a></b> to download your file<br/><br/>
			Once the download is complete, kindly click the proceed button 
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were problems in generating your file <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>					
			</div>
			<div class="ui-sys-panel-footer">
				<div id="perror" class="ui-sys-error-message"></div>
<?php 
	if ($promise1->isPromising())	{
		if ($lineCounter > 0)	{
?>
			<a id="__id_csv_previewer" data-error-control="perror" data-server-forward-path="../server/getcsvpreviewer.php" data-file-to-read="<?= $filename ?>" data-dialog-container="__id_general_dialog_holder" class="button-link">Preview</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php
		}
?>
		<a class="button-link" href="<?= $thispage ?>?page=managedaysofaweek_csv&report=<?= $filename ?>&downloadable=true">Continue</a>
<?php
	}
?>			
			</div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managedaysofaweek_csv" && isset($_REQUEST['sortable']) && Authorize::isAllowable($config, "managedaysofaweek_csv", "normal", "setlog", "-1", "-1"))	{
		$enableUpdate = false;
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">DaysOfAWeek CSV File <br/> (Column Sorting)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (isset($_REQUEST['fieldname']) && isset($_REQUEST['fieldtype']) && isset($_REQUEST['namespaceTag']) && isset($_REQUEST['fieldvalue']))	{
		$enableUpdate = true;
?>
				<form class="pure-form" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managedaysofaweek_csv"/>
					<input type="hidden" name="downloadable" value="true"/>
<?php 
	$sortableContainer = Object::getUICSVDivSortable(Object::getLineToSortFromUserFieldData($_REQUEST['fieldname'], $_REQUEST['fieldtype'], $_REQUEST['namespaceTag'],$_REQUEST['fieldvalue']));
	echo $sortableContainer;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
<?php 
	} else {
?>
		Perhaps you did not select any column to be included in your CSV File <br/>
		Kindly go back to Column Selection and select atleast ONE Column
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer">
				<a class="button-link" href="<?= $thispage ?>?page=managedaysofaweek_csv">Back to Column Selection</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php 
	if ($enableUpdate)	{
?>				
				<input type="button" id="__add_record" value="Proceed"/>
<?php 
	}
?>
			</div>
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
	} else if ($page == "managedaysofaweek_csv" && Authorize::isAllowable($config, "managedaysofaweek_csv", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">DaysOfAWeek CSV File <br/>(Column Selection)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
						<input type="hidden" name="page" value="managedaysofaweek_csv"/>
						<input type="hidden" name="sortable" value="true"/>
<?php
	$listOfAvailableColumnsInTables = Object::getUICSVTables(DaysOfAWeek::getAvailableViewableColumns());
	echo $listOfAvailableColumnsInTables;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer">
				<input type="button" id="__add_record" value="Proceed"/>
			</div>
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
	} else if ($page == "managedaysofaweek_delete" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managedaysofaweek_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$dow1 = null;
		try {
			 $dow1 = new DaysOfAWeek($database, $_REQUEST['id'], $conn);
			 $dow1->commitDelete();
			 $enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason("You might be dealing with a DaysOfAWeek which is no longer existing in the system or a None Empty Record");
			$promise1->setPromise(false);
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">DaysOfAWeek Removal Report</div>
			<div class="ui-sys-panel-pody ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			The DaysOfAWeek <?= $dow1->getDaysOfAWeekName() ?> has been successful removed from the system
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Removing the DaysOfAWeek <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managedaysofaweek_delete", "Deleted ".$dow1->getDaysOfAWeekName());
	} else if ($page == "managedaysofaweek" && Authorize::isAllowable($config, "managedaysofaweek", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Days of A Week</div>
			<div class="ui-sys-panel-body">
<!--Block ONE controls for searching-->
				<div class="ui-sys-search-container pure-form pure-daysOfAWeek-control">
					<input type="text" title="At Least Three Characters should be supplied" required placeholder="ABC" size="32"/>
					<a title="Click to Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=managedaysofaweek" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
				</div>
<!--Block ONE ends-->	
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
?>
<!--Block Two, Search Results-->
<div class="ui-sys-search-results">
	<label id="statustextlabel"></label>
	<table class="pure-table ui-sys-table-search-results">
		<thead>
			<tr>
				<th>S/N</th>
				<th>Name of A Day</th>
				<th>Day <br/>Abbreviation</th>
				<th>Offset</th>
				<th>Extra <br/>Information</th>
			</tr>
		</thead>
<?php 
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
	$query = DaysOfAWeek::getQueryText();
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$result = mysql_db_query($database, $query, $conn) or die("Could not fetch daysOfAWeek data from the storage");
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	while (list($id) = mysql_fetch_row($result))	{
		if ($sqlReturnedRowCount > $maximumReturnedRows)	{
			//Finalize All House Keeping Here
			break;
		}
		$dow1 = null;
		try {
			$dow1 = new DaysOfAWeek($database, $id, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated per objects 
		if ($dow1->searchMatrix($matrix1)->evaluateResult())	{
			if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
				$numberOfTBodies++;
				$tbodyClosed = false;
			} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
				$numberOfTBodies++;
				$perTbodyCounter = 0;
			}
			//In All Cases we need to display tr
			$pureTableOddClass = "";
			if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
			<tr class="<?= $pureTableOddClass ?>">
				<td><?= $sqlReturnedRowCount + 1 ?></td>
				<td><?= $dow1->getDayName() ?></td>
				<td><?= $dow1->getDayAbbreviation() ?></td>
				<td><?= $dow1->getOffsetValue() ?></td>
				<td><?= $dow1->getExtraInformation() ?></td>
			</tr>
<?php
			$perTbodyCounter++;
			$sqlReturnedRowCount++;
		}//end-evaluate-results
	}//end-while
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
	mysql_close($conn);
?>
	</table>
	<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
	<ul class="ui-sys-pagination"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
</div>
<!--Block Two Ends-->
<?php
	} //end-if-searchtext
?>		
<!--Beginning of Block Three-->
				<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "managedaysofaweek_csv", "normal", "donotsetlog", "-1", "-1")) {
?>
		<a title="Download CSV/EXCEL Formatted Data" href="<?= $thispage ?>?page=managedaysofaweek_csv"><img alt="DAT" src="../sysimage/buttoncsv.png"/></a>
<?php
	}
?>				
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	}/*DaysOfAWeek End*/ else if ($page == "managesystemlogs" && Authorize::isAllowable($config, "managesystemlogs", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">System Logs</div>
			<div class="ui-sys-panel-body">
				<!--Block ONE BEGIN Search box-->
					<div class="pure-form ui-sys-search-container">
						<input title="Atleast Three Characters should be supplied" type="text" required placeholder="ABC" size="32" /> 
						<a title="Click To Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=managesystemlogs" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
					</div>
					<!--Block ONE ENDS-->
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
		/*Block Two Begin Search Results*/
?>
		<div class="ui-sys-search-results">
			<label id="statustextlabel"></label>
			<table class="pure-table pure-table-horizontal ui-sys-table-search-results">
				<thead>
					<tr>
						<th></th>
						<th>Time</th>
						<th>Actor</th>
						<th>Action</th>
						<th>Acted To</th>
					</tr>
<?php 
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	$list=SystemLogs::searchFromSystemLogs($database, $conn, $searchtext, 512); //Does not Allowed to exceed 512, due to memory leakage 
	foreach ($list as $alist)	{
		if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
			$numberOfTBodies++;
			$tbodyClosed = false;
		} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
			$numberOfTBodies++;
			$perTbodyCounter = 0;
		}
?>
		<tr>
			<td><?= $sqlReturnedRowCount + 1 ?></td>
			<td><?= $alist['when'] ?></td>
			<td><?= $alist['who'] ?></td>
			<td><?= $alist['what'] ?></td>
			<td><?= $alist['towhom'] ?></td>
		</tr>
<?php
		$perTbodyCounter++;
		$sqlReturnedRowCount++;
	}//end-foreach
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
	mysql_close($conn);
?>
				</thead>
			</table>
			<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
			<ul class="ui-sys-pagination"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
		</div>
<?php		
		/*Block Two Ending Search Results*/
	} //end-if-searchtext
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	} else if ($page == "managesystemfirewall_graph" && isset($_REQUEST['searchtext']) && isset($_REQUEST['type']) && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managesystemfirewall_graph", "normal", "setlog", "-1", "-1")) {
		$type = $_REQUEST['type'];
		$searchtext = $_REQUEST['searchtext'];
		$object1 = null;
		$objectText = "UNKNOWN";
		$targetObjectName = "UNKNOWN";
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		if ($type == "login")	{
			try {
				$object1 = new Login($database, $_REQUEST['id'], $conn);
				$targetObjectName = $object1->getClassName();
				$targetText = $object1->getFullname();
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
			}
		} else if ($type == "group")	{
			try {
				$object1 = new Group($database, $_REQUEST['id'], $conn);
				$targetObjectName = $object1->getClassName();
				$targetText = $object1->getGroupName();
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
			}
		} else {
			$promise1->setReason("The System has received an Invalid Instruction, the Object Type could not be Identified");
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Authorization Graph for <?= $targetText ?><br/>(<?= $targetObjectName ?>)</div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising() && ($object1->getClassName() == "Login") && $object1->isRoot())	{
?>
		<div class="ui-state-highlight">
			This is a SUPER USER, This User can perform ALL Operations in the System<br/>
			This User is ABOVE All the Firewall Rules
		</div>
<?php
	} else if ($promise1->isPromising())	{
		//General Non Root 
		$ds1 = Authorize::getAuthorizationGraphDataStructure($database, $conn, $object1, $type, $searchtext);
		if (! is_null($ds1))	{
			$numberOfColumns = sizeof($ds1['header']);
?>
			<table class="pure-table ui-sys-graph-drawable ui-sys-table-search-results">
				<thead>
					<tr>
						<th></th>
<?php 
	foreach ($ds1['header'] as $aheader)	{
?>
		<th><?= $aheader ?></th>
<?php
	}
?>
					</tr>
					<tr>
						<th></th>
<?php 
	foreach ($ds1['caption'] as $acaption)	{
?>
		<th><?= $acaption ?></th>
<?php
	}
?>
					</tr>
				</thead>
<?php 
	$row = "row";
	$viewableText = "";
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$tbodyClosed=true;
	for ($i=0; $i < sizeof($ds1) -2; $i++)	{
		$sqlReturnedRowCount = $i;
		if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
			$numberOfTBodies++;
			$tbodyClosed = false;
		} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
			$numberOfTBodies++;
			$perTbodyCounter = 0;
		}
		$rowIndex = $row.$i;
		$contextId = $ds1[$rowIndex][0];
		$context1 = null;
		try {
			$context1 = new ContextPosition($database, $contextId, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$colorToDraw = "black";
		$viewableText = "";
		if ($ds1[$rowIndex][$numberOfColumns-1] == 1)	{
			$colorToDraw = "blue";
			$viewableText = "Allowed by";
		} else if ($ds1[$rowIndex][$numberOfColumns-1] == 0)	{
			$colorToDraw = "red";
			$viewableText = "Denied by";
		}
		$rowClassName = "";
		if (($perTbodyCounter % 2) == 1)	$rowClassName="pure-table-odd";
?>
		<tr class="<?= $rowClassName ?>">
			<td><?= $i+1 ?></td>
			<td><?= $context1->getContextCaption() ?></td>
<?php 
	$lengthToMark = 0;
	$markValue="X";
	for ($lengthToMark=1; $lengthToMark < $numberOfColumns; $lengthToMark++)	{
		if (isset($ds1[$rowIndex][$lengthToMark]))	{
			$markValue=$ds1[$rowIndex][$lengthToMark];
			if ($markValue != "X")	{
				break;
			}
		} else {
			break;
		}
	}//end-for-lengthToMark
	$tempText = $ds1['header'][$lengthToMark]."[".$ds1['caption'][$lengthToMark]."]";
	$viewableText = $viewableText." ".$tempText;
	$remainingLength = $numberOfColumns-($lengthToMark+1);
?>
			<td title="<?= $context1->getContextCaption() ?>" colspan="<?= $lengthToMark ?>" style="background-color: <?= $colorToDraw ?>;">&nbsp;</td>
			<td colspan="<?= $remainingLength ?>" style="font-style: italic; font-size: 0.8em;">(<?= $viewableText ?>)</td>
		</tr>
<?php
		$perTbodyCounter++;
	}
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
?>				
			</table>
<ul class="ui-sys-pagination"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
<?php
		} else {
?>
			<div class="ui-state-error">
				There were no related Authorization Graph 
			</div>
<?php
		}
	} else {
		//Error Here 
?>
		<div class="ui-state-error">
			Could not load Graph for Authorization <br/>
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
	} else if ($page == "managesystemfirewall_graph" && isset($_REQUEST['type']) && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managesystemfirewall_graph", "normal", "setlog", "-1", "-1"))	{
		$type = $_REQUEST['type'];
		$object1 = null;
		$targetText = "UNKNOWN";
		$targetObjectName = "UNKNOWN";
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		if ($type == "login")	{
			try {
				$object1 = new Login($database, $_REQUEST['id'], $conn);
				$targetObjectName = $object1->getClassName();
				$targetText = $object1->getFullname();
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
			}
		} else if ($type == "group")	{
			try {
				$object1 = new Group($database, $_REQUEST['id'], $conn);
				$targetObjectName = $object1->getClassName();
				$targetText = $object1->getGroupName();
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
			}
		} else {
			$promise1->setReason("Could not load the Object, The System received an Invalid Instruction");
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Authorization Graph for <?= $targetText ?><br/>(<?= $targetObjectName ?>)</div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
		<!--Begin Search UIs now comes here-->
			<div class="ui-sys-search-container pure-form">
				<input type="text" title="Atleast Three Characters should be supplied" required placeholder="ABC" size="32"/>
				<a title="Click To Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=managesystemfirewall_graph&type=<?= $_REQUEST['type'] ?>&id=<?= $_REQUEST['id'] ?>" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
			</div>
		<!--End Search UIs comes to end-->
<?php
	} else {
?>
				<div class="ui-state-error">
					The System Could not load graph <br/>
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
	} else if ($page == "managesystemfirewall" && isset($_REQUEST['report']) && isset($_REQUEST['type']) && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managesystemfirewall", "normal", "setlog", "-1", "-1"))	{
		$type=$_REQUEST['type'];
		$object1 = null;
		$objectName = "";
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$enableUpdate = false;
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		if ($type == "login")	{
			$object1 = new Login($database, $_REQUEST['id'], $conn);
			$objectName = $object1->getFullname();
		} else if ($type == "group")	{
			$object1 = new Group($database, $_REQUEST['id'], $conn);
			$objectName = $object1->getGroupName();
		} else if ($type == "jobtitle")	{
			$object1 = new JobTitle($database, $_REQUEST['id'], $conn);
			$objectName = $object1->getJobName();
		} else {
			$promise1->setReason("Perhaps the system has received a wrong Instruction OR invalid Object Type");
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">System Firewall Report for <?= $objectName ?></div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
		$enableUpdate = true;
?>
		<div class="ui-state-highlight">
			Firewall for <?= $objectName ?> has been updated successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in updating the firewall or generating a report<br/>
			Details of a problem: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managesystemfirewall", "Changed for $objectName ($type)");
	} else if ($page == "managesystemfirewall" && isset($_REQUEST['type']) && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managesystemfirewall", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$type = trim($_REQUEST['type']);
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$object1 = null;
		$objectName = "";
		$contextString1 = "";
		if ($type == "login")	{
			try {
				$object1 = new Login($database, $_REQUEST['id'], $conn);
				$objectName = $object1->getFullname();
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
			}
		} else if ($type == "group")	{
			try {
				$object1 = new Group($database, $_REQUEST['id'], $conn);
				$objectName = $object1->getGroupName();
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
			}
		} else if ($type == "jobtitle")	{
			 try {
				$object1 = new JobTitle($database, $_REQUEST['id'], $conn);
				$objectName = $object1->getJobName();
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
			}
		} else {
			$promise1->setReason("The type Object could not be Identified");
			$promise1->setPromise(false);
		}
		if ($promise1->isPromising())	{
			$contextString1 = $object1->getContext();
			$object1->setExtraFilter(System::getCodeString(8));
			try {
				$object1->commitUpdate();
			} catch (Exception $e)	{
				$promise1->setReason("Can not set filter, ".$e->getMessage());
				$promise1->setPromise(false);
			}
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">System Firewall for <?= $objectName ?></div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
		<!--Begin Search Box-->
		<div class="pure-form pure-group-control ui-sys-search-container">
			<input title="Atleast Three Characters should be supplied" type="text" required placeholder="ABC" size="32" />
			<a title="Click To Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=managesystemfirewall&type=<?= $_REQUEST['type'] ?>&id=<?= $_REQUEST['id'] ?>" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
		</div>
		<!--End Search Box-->
		<!--Begin Box with Allow/Deny Do Not Care All-->
		<div class="ui-sys-firewall-all">
			<span>
				<input type="checkbox" value="1"/><input type="button" value="Allow All" title="Set All Actions to Allow" data-secure-code="<?= $object1->getExtraFilter() ?>" data-context-string="<?= $contextString1 ?>" data-server-path="../server/service_systemfirewall_save.php" data-context-character="1" data-object-type="<?= $_REQUEST['type'] ?>" data-object-id="<?= $_REQUEST['id'] ?>" data-context-target="perror" data-next-page="<?= $thispage ?>?page=managesystemfirewall&type=<?= $_REQUEST['type'] ?>&id=<?= $_REQUEST['id'] ?>&report=kdiie&rndx=<?= $object1->getExtraFilter() ?>" disabled/>
			</span>
			<span>
				<input type="checkbox" value="0"/><input type="button" value="Deny All" title="Set All Actions to Deny" data-secure-code="<?= $object1->getExtraFilter() ?>" data-context-string="<?= $contextString1 ?>" data-server-path="../server/service_systemfirewall_save.php" data-context-character="0" data-object-type="<?= $_REQUEST['type'] ?>" data-object-id="<?= $_REQUEST['id'] ?>" data-context-target="perror" data-next-page="<?= $thispage ?>?page=managesystemfirewall&type=<?= $_REQUEST['type'] ?>&id=<?= $_REQUEST['id'] ?>&report=kdiie&rndx=<?= $object1->getExtraFilter() ?>" disabled/>
			</span>
			<span>
				<input type="checkbox" value="X"/><input type="button" value="Do Not Care All" title="Set All Actions to Do Not Care" data-secure-code="<?= $object1->getExtraFilter() ?>" data-context-string="<?= $contextString1 ?>" data-server-path="../server/service_systemfirewall_save.php" data-context-character="X" data-object-type="<?= $_REQUEST['type'] ?>" data-object-id="<?= $_REQUEST['id'] ?>" data-context-target="perror" data-next-page="<?= $thispage ?>?page=managesystemfirewall&type=<?= $_REQUEST['type'] ?>&id=<?= $_REQUEST['id'] ?>&report=kdiie&rndx=<?= $object1->getExtraFilter() ?>" disabled/>
			</span>
		</div>

		<!--End Box with Allow/Deny Do Not Care All-->
		<!--Begin Another Block with Search Results-->
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
		$query = ContextPosition::getQueryText();
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$result = mysql_db_query($database, $query, $conn) or die("Could not connect execute Context Query");
?>
		<!--Block Two Begins: with search results-->
		<div class="ui-sys-search-results">
			<label id="statustextlabel"></label>
			<table class="pure-table ui-sys-table-search-results ui-sys-context-table">
		<thead>
			<tr>
				<th></th>
				<th>Caption</th>
				<th>Level</th>
				<th>Allow</th>
				<th>Deny</th>
				<th>Do Not Care</th>
			</tr>
		</thead>
<?php 
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	while (list($id) = mysql_fetch_row($result))	{
		if ($sqlReturnedRowCount > $maximumReturnedRows)	{
			//Finalize All House Keeping Here
			break;
		}
		$context1 = null;
		try {
			$context1 = new ContextPosition($database, $id, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$pos = $context1->getCharacterPosition();
		$char1 = Authorize::getContextCharacter($contextString1, $pos);
		//Marker for display
		$action="";
		$level = 0;
		$chkAllow="";
		$chkDeny="";
		$chkDoNotCare="";
		if ($char1=="X")	{
			$action="donotcare";
			$chkDoNotCare="Checked";
		} else {
			$val=-1;
			try {
				$val = ContextDefinition::getValueFromCharacter($database, $char1, $conn);
			} catch (Exception $e)	{ die($e->getMessage()); }
			if ($val == 0)	{
				$action="deny";
				$chkDeny="checked";
			} else {
				$action="allow";
				$level=$val;
				$chkAllow="checked";
			}//end-if-else 
		}//end-if-else 
		$selectEnable="disabled";
		if ($action == "allow") $selectEnable="";
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated per objects 
		if ($context1->searchMatrix($matrix1)->evaluateResult())	{
			if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
				$numberOfTBodies++;
				$tbodyClosed = false;
			} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
				$numberOfTBodies++;
				$perTbodyCounter = 0;
			}
			$disabled = "";
			//In All Cases we need to display tr
			$pureTableOddClass = "";
			if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
			<tr class="<?= $pureTableOddClass ?> ui-sys-context-data-row" data-context-id="<?= $context1->getContextId() ?>" data-context-position="<?= $pos ?>" data-context-character="<?= $char1 ?>">
				<td><?= $sqlReturnedRowCount + 1 ?></td>
				<td><?= $context1->getContextCaption() ?></td>
				<td><label><select class="ui-sys-context-allow-select" <?= $selectEnable ?>>
<?php 
	for ($i=1; $i<=30; $i++)	{
		$selected = "";
		if (($action=="allow") && ($i==$level)) $selected="selected";
?>
		<option <?= $selected ?> value="<?= ContextDefinition::getCharacterFromValue($database, $i, $conn) ?>"><?= $i ?></option>
<?php
	} //end-for
?>				
				</select></label></td>
				<td><label><input <?= $chkAllow ?> value="1" class="ui-sys-control-radio control-radio-allow" type="radio" name="radauth<?= $id ?>"/>Allow</label></td>
				<td><label><input <?= $chkDeny ?> value="0" class="ui-sys-control-radio control-radio-deny" type="radio" name="radauth<?= $id ?>"/>Deny</label></td>
				<td><label><input <?= $chkDoNotCare ?> value="X" class="ui-sys-control-radio control-radio-donotcare" type="radio" name="radauth<?= $id ?>"/>Do Not Care</label></td>
			</tr>
<?php
			$perTbodyCounter++;
			$sqlReturnedRowCount++;
		}//end-evaluate-results
	}//end-while
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
?>
	</table>
	<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
	<ul class="ui-sys-pagination"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
			<div class="ui-sys-right">
				<input id="firewallRulesSavingCommand" type="button" title="Save the Customized Firewall Roles for <?= $objectName ?>" data-secure-code="<?= $object1->getExtraFilter() ?>" data-context-string="<?= $contextString1 ?>" data-server-path="../server/service_systemfirewall_save.php" data-object-type="<?= $_REQUEST['type'] ?>" data-object-id="<?= $_REQUEST['id'] ?>" data-context-target="perror" data-next-page="<?= $thispage ?>?page=managesystemfirewall&type=<?= $_REQUEST['type'] ?>&id=<?= $_REQUEST['id'] ?>&report=kdiie&rndx=<?= $object1->getExtraFilter() ?>" value="Save Firewall Rules"/>
			</div>
		</div>
		<!--Block Two Ends: with search result-->
<?php
		mysql_close($conn);
	} //end-if-search-text
?>		
		<!--End Another Block with Search Results-->
		<div id="perror" class="ui-sys-error-message"></div>
<?php
	} else {
?>
		<div class="ui-state-error">
			Could not load the reference Object, perhaps wrong Instruction<br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer">
			</div>
		</div>
<?php
	} else if ($page == "manageprofile_edit" && $login1->isRoot() && isset($_REQUEST['report']) && isset($_REQUEST['record']) && Authorize::isAllowable($config, "manageprofile_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$maximumNumberOfReturnedSearchRecords = mysql_real_escape_string($_REQUEST['maximumNumberOfReturnedSearchRecords']);
		$maximumNumberOfDisplayedRowsPerPage = mysql_real_escape_string($_REQUEST['maximumNumberOfDisplayedRowsPerPage']);
		$profile1 = null;
		try {
			$profile1 = new profile($database, $__profileId, $conn);
			if ($maximumNumberOfReturnedSearchRecords != $profile1->getMaximumNumberOfReturnedSearchRecords())	{
				$profile1->setMaximumNumberOfReturnedSearchRecords($maximumNumberOfReturnedSearchRecords); $enableUpdate = true;
			}
			if ($maximumNumberOfDisplayedRowsPerPage != $profile1->getMaximumNumberOfDisplayedRowsPerPage())	{
				$profile1->setMaximumNumberOfDisplayedRowsPerPage($maximumNumberOfDisplayedRowsPerPage); $enableUpdate = true;
			}
			if ($enableUpdate) {
				$profile1->commitUpdate();
			}
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
			$enableUpdate = false;
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">System Records Size (Report)</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div ui-state-highlight>
			You have successful updated the records limit
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems on saving the record limits<br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageprofile_edit", "Records Count Updates");
	} else if ($page == "manageprofile_edit" && $login1->isRoot() && isset($_REQUEST['record']) && Authorize::isAllowable($config, "manageprofile_edit", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">System Records Size</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="manageprofile_edit"/>
					<input type="hidden" name="record" value="<?= $_REQUEST['record'] ?>"/>
					<input type="hidden" name="report" value="true"/>
					<div class="pure-control-group">
						<label for="maximumNumberOfReturnedSearchRecords">Maximum Number of Returned Search Records </label>
						<input value="<?= $profile1->getMaximumNumberOfReturnedSearchRecords() ?>" class="text-can-not-be-zero" type="text" name="maximumNumberOfReturnedSearchRecords" id="maximumNumberOfReturnedSearchRecords" size="48" required pattern="<?= $exprRecordCount ?>" validate="true" validate_control="text" validate_expression="<?= $exprRecordCount ?>" validate_message="Maximu Number of Returned Search Records : <?= $msgRecordCount ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="maximumNumberOfDisplayedRowsPerPage">Maximum Number of Displayed Rows Per Page </label>
						<input value="<?= $profile1->getMaximumNumberOfDisplayedRowsPerPage() ?>" class="text-can-not-be-zero" type="text" name="maximumNumberOfDisplayedRowsPerPage" id="maximumNumberOfDisplayedRowsPerPage" size="48" required pattern="<?= $exprRecordCount ?>" validate="true" validate_control="text" validate_expression="<?= $exprRecordCount ?>" validate_message="Maximu Number of Displayed Rows Per Page : <?= $msgRecordCount ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Edit Record"/>
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
		var enableSubmit = true;
		$('input.text-can-not-be-zero').each(function(i, v)	{
			var $text1 = $(v);
			enableSubmit = enableSubmit && (parseInt($text1.val()) != 0);
		});
		if (enableSubmit)	{
			generalFormSubmission(this, 'form1', 'perror');
		} else {
			$target1.empty();
			$('<span/>').html('Records Count Can not be Zero')
				.appendTo($target1);
		}
	});
})(jQuery);
</script>
<?php
	} else if ($page == "manageprofile_edit" && $login1->isRoot() && isset($_REQUEST['warmreload']) && Authorize::isAllowable($config, "manageprofile_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$profile1 = null;
		try	{
			$profile1 = new Profile($database, $__profileId, $conn);
			//Hapa hapa tunamaliza kila kitu 
			$profile1->setInstallationComplete("0");
			$profile1->setApplicationCounter(intval($profile1->getApplicationCounter()) - 1);
			$profile1->commitUpdate();
			$enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Changing Installation Data<br/>Warm Reload</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
				<div>
					To complete this action, <a href="../">Click Here</a>
				</div>
<?php	
	} else {
?>
				<div class="ui-state-error">
					There were problems in attempting to change Installation Data <br/>
					Reason: <?= $promise1->getReason() ?>
				</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageprofile_edit", "Warm Reload of the System");
	} else if ($page == "manageprofile" && $login1->isRoot() && Authorize::isAllowable($config, "manageprofile", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">System Settings</div>
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
								Change Installation Data, this is all the installation data you supplied during Installation <br/>
								NOTE: This will not affect the rest of existing system data
							</td>
							<td>
								<a href="<?= $thispage ?>?page=manageprofile_edit&warmreload=true" class="button-link" title="Changing Installation Data">Change</a>
							</td>
						</tr>
						<tr>
							<td>2</td>
							<td>
								This will change the size of maximum number of returned search records, it is also used to change <br/>
								maximum number of rows the system is allowed to display per page
							</td>
							<td>
								<a href="<?= $thispage ?>?page=manageprofile_edit&record=io" class="button-link" title="Changing Records Sizes">Change</a>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	} else /*CourseAndSubjectTransaction BEGIN*/if ($page == "managecourseandsubjecttransaction_csv" && isset($_REQUEST['report']) && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managecourseandsubjecttransaction_csv", "normal", "setlog", "-1", "-1"))	{
		$filename=$_REQUEST['report'];
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">CourseAndSubjectTransaction CSV File<br/> (Download Report)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (unlink($filename))	{
?>
		<div class="ui-state-highlight">
			The temporary file has been removed succesful from the Server
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problem on removing the temporary file from the Server
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	} else if ($page == "managecourseandsubjecttransaction_csv" && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managecourseandsubjecttransaction_csv", "normal", "setlog", "-1", "-1"))	{
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$promise1 = new Promise();
		$promise1->setPromise(true);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">CourseAndSubjectTransaction CSV File <br/> (Downlad CSV File)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	$filename="";
	$tfname=$filename.$date;
	$tfname=$tfname.rand(100,999);
	$filename=sha1($tfname);
	$filename=$filename.".csv";
	$filename="../temp/".$filename;
	$searchText = Object::getSearchTextFromUserFieldData($_REQUEST['controlIndex'], $_REQUEST['fieldname'], $_REQUEST['op'], $_REQUEST['namespaceTag'], $_REQUEST['fieldvalue']);
	$lineCounter = 0;
	$file1 = fopen($filename, "w") or die("Could not Open the File for writing");
	$csvProcessor1 = null;
	try {
		$csvProcessor1 = new CSVProcessor($searchText, $file1);
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$primise1->setPromise(false);
	}
	$result = null;
	if ($promise1->isPromising())	{
		$query = CourseAndSubjectTransaction::getQueryText();
		try {
			$result = mysql_db_query($database, $query, $conn) or Object::shootException("CourseAndSubjectTransaction[CSV]: Could not Execute Query");
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$primise1->setPromise(false);
		}
	}
	if ($promise1->isPromising())	{
		while (list($id) = mysql_fetch_row($result))	{
			$transaction1 = null;
			try	{
				$transaction1 = new CourseAndSubjectTransaction($database, $id, $conn);
			} catch (Exception $e)	{ die($e->getMessage()); }
			$csvProcessor1->reload(); //Clear the Previous Object Properties 
			if ($transaction1->processCSV($csvProcessor1)->evaluateResult())	{
				$lineCounter++;
			}
		}//end-while 
	}//end-if-promising
	fclose($file1);
	if ($promise1->isPromising())	{
?>
		<div>
			Your file is Ready for downloading<br/>
			Number data rows it contains is <?= $lineCounter ?><br/><br/>
			<b style="font-size: 1.1em;"><a href="<?= $filename ?>">Click Here</a></b> to download your file<br/><br/>
			Once the download is complete, kindly click the proceed button 
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were problems in generating your file <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>					
			</div>
			<div class="ui-sys-panel-footer">
				<div id="perror" class="ui-sys-error-message"></div>
<?php 
	if ($promise1->isPromising())	{
		if ($lineCounter > 0)	{
?>
			<a id="__id_csv_previewer" data-error-control="perror" data-server-forward-path="../server/getcsvpreviewer.php" data-file-to-read="<?= $filename ?>" data-dialog-container="__id_general_dialog_holder" class="button-link">Preview</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php
		}
?>
		<a class="button-link" href="<?= $thispage ?>?page=managecourseandsubjecttransaction_csv&report=<?= $filename ?>&downloadable=true">Continue</a>
<?php
	}
?>			
			</div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managecourseandsubjecttransaction_csv" && isset($_REQUEST['sortable']) && Authorize::isAllowable($config, "managecourseandsubjecttransaction_csv", "normal", "setlog", "-1", "-1"))	{
		$enableUpdate = false;
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">CourseAndSubjectTransaction CSV File <br/> (Column Sorting)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (isset($_REQUEST['fieldname']) && isset($_REQUEST['fieldtype']) && isset($_REQUEST['namespaceTag']) && isset($_REQUEST['fieldvalue']))	{
		$enableUpdate = true;
?>
				<form class="pure-form" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managecourseandsubjecttransaction_csv"/>
					<input type="hidden" name="downloadable" value="true"/>
<?php 
	$sortableContainer = Object::getUICSVDivSortable(Object::getLineToSortFromUserFieldData($_REQUEST['fieldname'], $_REQUEST['fieldtype'], $_REQUEST['namespaceTag'],$_REQUEST['fieldvalue']));
	echo $sortableContainer;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
<?php 
	} else {
?>
		Perhaps you did not select any column to be included in your CSV File <br/>
		Kindly go back to Column Selection and select atleast ONE Column
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer">
				<a class="button-link" href="<?= $thispage ?>?page=managecourseandsubjecttransaction_csv">Back to Column Selection</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php 
	if ($enableUpdate)	{
?>				
				<input type="button" id="__add_record" value="Proceed"/>
<?php 
	}
?>
			</div>
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
	} else if ($page == "managecourseandsubjecttransaction_csv" && Authorize::isAllowable($config, "managecourseandsubjecttransaction_csv", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">CourseAndSubjectTransaction CSV File <br/>(Column Selection)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
						<input type="hidden" name="page" value="managecourseandsubjecttransaction_csv"/>
						<input type="hidden" name="sortable" value="true"/>
<?php
	$listOfAvailableColumnsInTables = Object::getUICSVTables(CourseAndSubjectTransaction::getAvailableViewableColumns());
	echo $listOfAvailableColumnsInTables;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer">
				<input type="button" id="__add_record" value="Proceed"/>
			</div>
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
	} else if ($page == "managecourseandsubjecttransaction_delete" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managecourseandsubjecttransaction_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$transaction1 = null;
		try {
			 $transaction1 = new CourseAndSubjectTransaction($database, $_REQUEST['id'], $conn);
			 $transaction1->commitDelete();
			 $enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason("You might be dealing with a CourseAndSubject Mapping which is no longer existing in the system or a None Empty Record");
			$promise1->setPromise(false);
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Course & Subject Mapping Removal Report</div>
			<div class="ui-sys-panel-pody ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			The Course and Subject Mapping has been successful removed from the system
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Removing the CourseAndSubjectTransaction <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managecourseandsubjecttransaction_delete", "Deleted [Course & Subject] Mapping");
	} else if ($page == "managecourseandsubjecttransaction_delete" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managecourseandsubjecttransaction_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to database services");
		$transaction1 = null;
		try {
			$transaction1 = new CourseAndSubjectTransaction($database, $_REQUEST['id'], $conn);
			$transaction1->setExtraFilter(System::getCodeString(8));
			$transaction1->commitUpdate();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Delete Confirmation</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<div class="ui-sys-warning">
					You are about to remove a Course & Subject Mapping from the System <br/>
					<b>NOTE: This Operation Can not be reversed</b><br/>
					Are You Sure You want to remove the Course And Subject Mapping from the System?
				</div>
				<div class="ui-sys-center-text">
					<a class="button-link-warning" href="<?= $thispage ?>?page=managecourseandsubjecttransaction_delete&report=io&id=<?= $transaction1->getTransactionId() ?>&rndx=<?= $transaction1->getExtraFilter() ?>">Yes (Remove)</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a class="button-link" href="<?= $thispage ?>?page=managecourseandsubjecttransaction">No (Cancel)</a>
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managecourseandsubjecttransaction_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managecourseandsubjecttransaction_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$transaction1 = null;
		try {
			$transaction1 = new CourseAndSubjectTransaction($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$promise1 = new Promise();
		$promise1->setReason("Nothing Has been Updated");
		$enableUpdate = false;
		$courseId = mysql_real_escape_string($_REQUEST['courseId']);
		$subjectId = mysql_real_escape_string($_REQUEST['subjectId']);
		$year = mysql_real_escape_string($_REQUEST['year']);
		$semester = mysql_real_escape_string($_REQUEST['semester']);
		$compulsory = 0;
		if (isset($_REQUEST['compulsory'])) $compulsory = 1;
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
		if (is_null($transaction1->getCourse()) || ($courseId != $transaction1->getCourse()->getCourseId()))	{
			$transaction1->setCourse($courseId); $enableUpdate = true;
		}
		if (is_null($transaction1->getSubject()) || ($subjectId != $transaction1->getSubject()->getSubjectId()))	{
			$transaction1->setSubject($subjectId); $enableUpdate = true;
		}
		if ($year != $transaction1->getYear())	{
			$transaction1->setYear($year); $enableUpdate = true;
		}
		if ($semester != $transaction1->getSemester())	{
			$transaction1->setSemester($semester); $enableUpdate = true;
		}
		if (($compulsory == 1) xor $transaction1->isCompulsory())	{
			$transaction1->setCompulsory($compulsory); $enableUpdate = true;
		}
		if ($extraInformation != $transaction1->getExtraInformation())	{
			$transaction1->setExtraInformation($extraInformation); $enableUpdate = true;
		}
		//This is the Last 
		if (trim($_REQUEST['rndx']) != $transaction1->getExtraFilter())	{
			$enableUpdate = false;
			$promise1->setReason("The System has Detected you are replaying data in your Browser window");
			$promise1->setPromise(false);
		}
		//Now proceed with general find a new value of extra Filter 
		$transaction1->setExtraFilter(System::getCodeString(8));
		if ($enableUpdate)	{
			try {
				$transaction1->commitUpdate();
				$promise1->setPromise(true);
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
				$enableUpdate = false;
			}
		} 
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Course & Subject Mapping Editing Report</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Course And Subject Mapping has been updated successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were no updates for the transaction <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managecourseandsubjecttransaction_edit", "Edited [Course & Subject] Mapping");
	} else if ($page == "managecourseandsubjecttransaction_edit" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managecourseandsubjecttransaction_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$transaction1 = null;
		$extraFilter = System::getCodeString(8);
		try {
			$transaction1 = new CourseAndSubjectTransaction($database, $_REQUEST['id'], $conn);
			$transaction1->setExtraFilter($extraFilter);
			$transaction1->commitUpdate();
		} catch (Exception $e)	{ die($e->getMessage()); }
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Edit Course & Subject Mapping</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managecourseandsubjecttransaction_edit"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="id" value="<?= $transaction1->getTransactionId() ?>"/>
					<input type="hidden" name="rndx" value="<?= $transaction1->getExtraFilter() ?>"/>
					<div class="pure-control-group">
						<label for="extraInformation">Map Name </label>
						<input title="Any Valid Name which will Identify your Map" value="<?= $transaction1->getExtraInformation() ?>" type="text" name="extraInformation" id="extraInformation" size="48" required pattern="<?= $epxrL64Name  ?>" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Map Name : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="courseId">Course </label>
						<select id="courseId" name="courseId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Course">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Course::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($transaction1->getCourse()) && ($alist1['id'] == $transaction1->getCourse()->getCourseId())) $selected="selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="subjectId">Subject </label>
						<select id="subjectId" name="subjectId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Subject/Module">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Subject::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($transaction1->getSubject()) && ($alist1['id'] == $transaction1->getSubject()->getSubjectId())) $selected="selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="year">Year </label>
						<input value="<?= $transaction1->getYear() ?>" class="text-can-not-be-zero" type="text" name="year" id="year" size="8" required pattern="<?= $expr2Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr2Number ?>" validate_message="Year : <?= $msg2Number ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="semester">Semester </label>
						<input value="<?= $transaction1->getSemester() ?>" class="text-can-not-be-zero" type="text" name="semester" id="semester" size="8" required pattern="<?= $expr2Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr2Number ?>" validate_message="Semester : <?= $msg2Number ?>"/>
					</div>
					<div class="pure-controls">
<?php 
	$chkCompulsory = "";
	if ($transaction1->isCompulsory()) $chkCompulsory="checked";
?>
						<label><input <?= $chkCompulsory ?> type="checkbox" name="compulsory" value="1"/>&nbsp;&nbsp;Is A Compulsory?</label>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Edit Record"/>
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
		var enableSubmit = true;
		$('input.text-can-not-be-zero').each(function(i, v)	{
			var $text1 = $(v);
			enableSubmit = enableSubmit && (parseInt($text1.val()) != 0);
		});
		if (enableSubmit)	{
			generalFormSubmission(this, 'form1', 'perror');
		} else {
			$target1.empty();
			$('<span/>').html('Neither Year Nor Semester Can be zero')
				.appendTo($target1);
		}
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if ($page == "managecourseandsubjecttransaction_detail" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managecourseandsubjecttransaction_detail", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$transaction1 = null;
		try {
			$transaction1 = new CourseAndSubjectTransaction($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{die($e->getMessage());}
		mysql_close($conn);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Details for Course & Subject Map</div>
		<div class="ui-sys-panel-content ui-sys-data-capture">
			<table class="pure-table">
				<thead>
					<tr>
						<th>Field Name</th>
						<th>Field Value</th>
					</tr>
				</thead>
				<tbody>
<?php 
	
	if (! is_null($transaction1->getExtraInformation()))	{
?>
					<tr>
						<td>Map Name</td>
						<td><?= $transaction1->getExtraInformation() ?></td>
					</tr>
<?php
	} 
	if (! is_null($transaction1->getCourse()))	{
?>
					<tr>
						<td>Course</td>
						<td><?= $transaction1->getCourse()->getCourseName() ?></td>
					</tr>
<?php 
	}
	if (! is_null($transaction1->getSubject()))	{
?>
					<tr>
						<td>Subject</td>
						<td><?= $transaction1->getSubject()->getSubjectName() ?></td>
					</tr>
<?php 
	}
?>
					<tr>
						<td>Year</td>
						<td><?= $transaction1->getYear() ?></td>
					</tr>
					<tr>
						<td>Semester</td>
						<td><?= $transaction1->getSemester() ?></td>
					</tr>
					<tr>
						<td colspan="2">
<?php 
	$compulsoryText = "Optional Module";
	if ($transaction1->isCompulsory()) $compulsoryText = "Compulsory Module";
?>						
							<?= $compulsoryText ?>
						</td>
					</tr>
			</tbody>
			</table>
		</div>
		<div class="ui-sys-panel-footer">
			<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "managecourseandsubjecttransaction_edit", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Edit Mapping" href="<?= $thispage ?>?page=managecourseandsubjecttransaction_edit&id=<?= $transaction1->getTransactionId() ?>"><img alt="DAT" src="../sysimage/buttonedit.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "managecourseandsubjecttransaction_delete", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Delete Mapping" href="<?= $thispage ?>?page=managecourseandsubjecttransaction_delete&id=<?= $transaction1->getTransactionId() ?>"><img alt="DAT" src="../sysimage/buttondelete.png"/></a>
<?php
	}
?>
				<span class="ui-sys-clear-both">&nbsp;</span><br />
			</div>
		</div>
	</div>
<?php
	} else if ($page == "managecourseandsubjecttransaction_add" && isset($_REQUEST['report']) && Authorize::isAllowable($config, "managecourseandsubjecttransaction_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$courseId = mysql_real_escape_string($_REQUEST['courseId']);
		$subjectId = mysql_real_escape_string($_REQUEST['subjectId']);
		$year = mysql_real_escape_string($_REQUEST['year']);
		$semester = mysql_real_escape_string($_REQUEST['semester']);
		$compulsory = 0;
		if (isset($_REQUEST['compulsory'])) $compulsory = 1;
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel header ui-widget-header">Summary Report for Course & Subject Mapping</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	$extraFilter = System::getCodeString(8);
	$query="INSERT INTO courseAndSubjectTransaction (courseId, subjectId, year, semester, compulsory, extraFilter, extraInformation) VALUES('$courseId', '$subjectId', '$year', '$semester', '$compulsory', '$extraFilter', '$extraInformation')";
	try {
		mysql_db_query($database, $query, $conn) or Object::shootException("CourseAndSubjectTransaction[Adding]: Could not Add a Record into the System");
		$promise1->setPromise(true);
		$enableUpdate = true;
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">The Map for Course and Subject has successful Added to the system</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Adding the Mapping<br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managecourseandsubjecttransaction_add", "Added Map [Course & Subject]");
	} else if ($page == "managecourseandsubjecttransaction_add" && Authorize::isAllowable($config, "managecourseandsubjecttransaction_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Add a New Course And Subject Mappings</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managecourseandsubjecttransaction_add"/>
					<input type="hidden" name="report" value="io"/>
					<div class="pure-control-group">
						<label for="extraInformation">Map Name </label>
						<input title="Any Valid Name which will identify your map" type="text" name="extraInformation" id="extraInformation" size="48" required pattern="<?= $exprL64Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Map Name : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="courseId">Course </label>
						<select id="courseId" name="courseId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Course">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Course::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
?>
			<option value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="subjectId">Subject </label>
						<select id="subjectId" name="subjectId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Subject/Module">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Subject::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
?>
			<option value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="year">Year </label>
						<input class="text-can-not-be-zero" type="text" name="year" id="year" size="8" required pattern="<?= $expr2Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr2Number ?>" validate_message="Year : <?= $msg2Number ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="semester">Semester </label>
						<input class="text-can-not-be-zero" type="text" name="semester" id="semester" size="8" required pattern="<?= $expr2Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr2Number ?>" validate_message="Semester : <?= $msg2Number ?>"/>
					</div>
					<div class="pure-controls">
						<label><input type="checkbox" name="compulsory" value="1"/>&nbsp;&nbsp;Is A Compulsory?</label>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Add Record"/>
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
		var enableSubmit = true;
		$('input.text-can-not-be-zero').each(function(i, v)	{
			var $text1 = $(v);
			enableSubmit = enableSubmit && (parseInt($text1.val()) != 0);
		});
		if (enableSubmit)	{
			generalFormSubmission(this, 'form1', 'perror');
		} else {
			$target1.empty();
			$('<span/>').html('Neither Year Nor Semester Can be zero')
				.appendTo($target1);
		}
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if ($page == "managecourseandsubjecttransaction" && Authorize::isAllowable($config, "managecourseandsubjecttransaction", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">CourseAndSubjectTransactions Management</div>
			<div class="ui-sys-panel-body">
<!--Block ONE controls for searching-->
				<div class="ui-sys-search-container pure-form pure-transaction-control">
					<input type="text" title="At Least Three Characters should be supplied" required placeholder="ABC" size="32"/>
					<a title="Click to Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=managecourseandsubjecttransaction" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
				</div>
<!--Block ONE ends-->	
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
		//saving Authorization Rules 
		$blnAuthorizationDetail = Authorize::isAllowable($config, "managecourseandsubjecttransaction_detail", "normal", "donotsetlog", "-1", "-1");
?>
<!--Block Two, Search Results-->
<div class="ui-sys-search-results">
	<label id="statustextlabel"></label>
	<table class="pure-table ui-sys-table-search-results">
		<thead>
			<tr>
				<th>S/N</th>
				<th>Map Name</th>
				<th>Course</th>
				<th>Subject/Module</th>
				<th>Year</th>
				<th>Semester</th>
				<th>Status</th>
				<th></th>
			</tr>
		</thead>
<?php 
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
	$query = CourseAndSubjectTransaction::getQueryText();
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$result = mysql_db_query($database, $query, $conn) or die("Could not fetch transaction data from the storage");
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	while (list($id) = mysql_fetch_row($result))	{
		if ($sqlReturnedRowCount > $maximumReturnedRows)	{
			//Finalize All House Keeping Here
			break;
		}
		$transaction1 = null;
		try {
			$transaction1 = new CourseAndSubjectTransaction($database, $id, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated per objects 
		if ($transaction1->searchMatrix($matrix1)->evaluateResult())	{
			if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
				$numberOfTBodies++;
				$tbodyClosed = false;
			} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
				$numberOfTBodies++;
				$perTbodyCounter = 0;
			}
			//In All Cases we need to display tr
			$pureTableOddClass = "";
			if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
			<tr class="<?= $pureTableOddClass ?>">
				<td><?= $sqlReturnedRowCount + 1 ?></td>
				<td><?= $transaction1->getExtraInformation() ?></td>
				<td>
<?php 
	if (! is_null($transaction1->getCourse()))	{
?>
		<?= $transaction1->getCourse()->getCourseName() ?>
<?php
	}
?>				
				</td>
				<td>
<?php 
	if (! is_null($transaction1->getSubject()))	{
?>
		<?= $transaction1->getSubject()->getSubjectName() ?>
<?php
	}
?>				
				</td>
				<td><?= $transaction1->getYear() ?></td>
				<td><?= $transaction1->getSemester() ?></td>
				<td>
<?php 
	$compulsoryText = "Optional";
	if ($transaction1->isCompulsory())	{
		$compulsoryText = "Compulsory";
	}
?>					<?= $compulsoryText ?>
				</td>
				<td>
<?php 
	if ($blnAuthorizationDetail)	{
?>
		<a href="<?= $thispage ?>?page=managecourseandsubjecttransaction_detail&id=<?= $transaction1->getTransactionId() ?>" class="ui-sys-search-results-controls"><img alt="DAT" src="../sysimage/buttondetail.png"/></a>
<?php
	}
?>				
				</td>
			</tr>
<?php
			$perTbodyCounter++;
			$sqlReturnedRowCount++;
		}//end-evaluate-results
	}//end-while
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
	mysql_close($conn);
?>
	</table>
	<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
	<ul class="ui-sys-pagination"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
</div>
<!--Block Two Ends-->
<?php
	} //end-if-searchtext
?>		
<!--Beginning of Block Three-->
				<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "managecourseandsubjecttransaction_csv", "normal", "donotsetlog", "-1", "-1")) {
?>
		<a title="Download CSV/EXCEL Formatted Data" href="<?= $thispage ?>?page=managecourseandsubjecttransaction_csv"><img alt="DAT" src="../sysimage/buttoncsv.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "managecourseandsubjecttransaction_add", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Add a New System CourseAndSubjectTransaction" href="<?= $thispage ?>?page=managecourseandsubjecttransaction_add"><img alt="DAT" src="../sysimage/buttonadd.png"/></a>
<?php	
	}
?>				
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	}/*CourseAndSubjectTransaction End*/ else/*Subject BEGIN*/if ($page == "managesubject_csv" && isset($_REQUEST['report']) && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managesubject_csv", "normal", "setlog", "-1", "-1"))	{
		$filename=$_REQUEST['report'];
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Subject CSV File<br/> (Download Report)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (unlink($filename))	{
?>
		<div class="ui-state-highlight">
			The temporary file has been removed succesful from the Server
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problem on removing the temporary file from the Server
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	} else if ($page == "managesubject_csv" && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managesubject_csv", "normal", "setlog", "-1", "-1"))	{
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$promise1 = new Promise();
		$promise1->setPromise(true);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Subject CSV File <br/> (Downlad CSV File)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	$filename="";
	$tfname=$filename.$date;
	$tfname=$tfname.rand(100,999);
	$filename=sha1($tfname);
	$filename=$filename.".csv";
	$filename="../temp/".$filename;
	$searchText = Object::getSearchTextFromUserFieldData($_REQUEST['controlIndex'], $_REQUEST['fieldname'], $_REQUEST['op'], $_REQUEST['namespaceTag'], $_REQUEST['fieldvalue']);
	$lineCounter = 0;
	$file1 = fopen($filename, "w") or die("Could not Open the File for writing");
	$csvProcessor1 = null;
	try {
		$csvProcessor1 = new CSVProcessor($searchText, $file1);
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$primise1->setPromise(false);
	}
	$result = null;
	if ($promise1->isPromising())	{
		$query = Subject::getQueryText();
		try {
			$result = mysql_db_query($database, $query, $conn) or Object::shootException("Subject[CSV]: Could not Execute Query");
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$primise1->setPromise(false);
		}
	}
	if ($promise1->isPromising())	{
		while (list($id) = mysql_fetch_row($result))	{
			$subject1 = null;
			try	{
				$subject1 = new Subject($database, $id, $conn);
			} catch (Exception $e)	{ die($e->getMessage()); }
			$csvProcessor1->reload(); //Clear the Previous Object Properties 
			if ($subject1->processCSV($csvProcessor1)->evaluateResult())	{
				$lineCounter++;
			}
		}//end-while 
	}//end-if-promising
	fclose($file1);
	if ($promise1->isPromising())	{
?>
		<div>
			Your file is Ready for downloading<br/>
			Number data rows it contains is <?= $lineCounter ?><br/><br/>
			<b style="font-size: 1.1em;"><a href="<?= $filename ?>">Click Here</a></b> to download your file<br/><br/>
			Once the download is complete, kindly click the proceed button 
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were problems in generating your file <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>					
			</div>
			<div class="ui-sys-panel-footer">
				<div id="perror" class="ui-sys-error-message"></div>
<?php 
	if ($promise1->isPromising())	{
		if ($lineCounter > 0)	{
?>
			<a id="__id_csv_previewer" data-error-control="perror" data-server-forward-path="../server/getcsvpreviewer.php" data-file-to-read="<?= $filename ?>" data-dialog-container="__id_general_dialog_holder" class="button-link">Preview</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php
		}
?>
		<a class="button-link" href="<?= $thispage ?>?page=managesubject_csv&report=<?= $filename ?>&downloadable=true">Continue</a>
<?php
	}
?>			
			</div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managesubject_csv" && isset($_REQUEST['sortable']) && Authorize::isAllowable($config, "managesubject_csv", "normal", "setlog", "-1", "-1"))	{
		$enableUpdate = false;
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Subject CSV File <br/> (Column Sorting)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (isset($_REQUEST['fieldname']) && isset($_REQUEST['fieldtype']) && isset($_REQUEST['namespaceTag']) && isset($_REQUEST['fieldvalue']))	{
		$enableUpdate = true;
?>
				<form class="pure-form" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managesubject_csv"/>
					<input type="hidden" name="downloadable" value="true"/>
<?php 
	$sortableContainer = Object::getUICSVDivSortable(Object::getLineToSortFromUserFieldData($_REQUEST['fieldname'], $_REQUEST['fieldtype'], $_REQUEST['namespaceTag'],$_REQUEST['fieldvalue']));
	echo $sortableContainer;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
<?php 
	} else {
?>
		Perhaps you did not select any column to be included in your CSV File <br/>
		Kindly go back to Column Selection and select atleast ONE Column
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer">
				<a class="button-link" href="<?= $thispage ?>?page=managesubject_csv">Back to Column Selection</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php 
	if ($enableUpdate)	{
?>				
				<input type="button" id="__add_record" value="Proceed"/>
<?php 
	}
?>
			</div>
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
	} else if ($page == "managesubject_csv" && Authorize::isAllowable($config, "managesubject_csv", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Subject CSV File <br/>(Column Selection)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
						<input type="hidden" name="page" value="managesubject_csv"/>
						<input type="hidden" name="sortable" value="true"/>
<?php
	$listOfAvailableColumnsInTables = Object::getUICSVTables(Subject::getAvailableViewableColumns());
	echo $listOfAvailableColumnsInTables;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer">
				<input type="button" id="__add_record" value="Proceed"/>
			</div>
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
	} else if ($page == "managesubject_delete" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managesubject_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$subject1 = null;
		try {
			 $subject1 = new Subject($database, $_REQUEST['id'], $conn);
			 $subject1->commitDelete();
			 $enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason("You might be dealing with a Subject which is no longer existing in the system or a None Empty Record");
			$promise1->setPromise(false);
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Subject Removal Report</div>
			<div class="ui-sys-panel-pody ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			The Subject <?= $subject1->getSubjectName() ?> has been successful removed from the system
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Removing the Subject <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managesubject_delete", "Deleted ".$subject1->getSubjectName());
	} else if ($page == "managesubject_delete" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managesubject_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to database services");
		$subject1 = null;
		try {
			$subject1 = new Subject($database, $_REQUEST['id'], $conn);
			$subject1->setExtraFilter(System::getCodeString(8));
			$subject1->commitUpdate();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Delete Confirmation</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<div class="ui-sys-warning">
					You are about to remove a Subject <b><?= $subject1->getSubjectName() ?></b> from the System <br/>
					<b>NOTE: This Operation Can not be reversed</b><br/>
					Are You Sure You want to remove the Subject <?= $subject1->getSubjectName() ?> from the System?
				</div>
				<div class="ui-sys-center-text">
					<a class="button-link-warning" href="<?= $thispage ?>?page=managesubject_delete&report=io&id=<?= $subject1->getSubjectId() ?>&rndx=<?= $subject1->getExtraFilter() ?>">Yes (Remove)</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a class="button-link" href="<?= $thispage ?>?page=managesubject">No (Cancel)</a>
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managesubject_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managesubject_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$subject1 = null;
		try {
			$subject1 = new Subject($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$promise1 = new Promise();
		$promise1->setReason("Nothing Has been Updated");
		$enableUpdate = false;
		$subjectName = mysql_real_escape_string($_REQUEST['subjectName']);
		$subjectCode = mysql_real_escape_string($_REQUEST['subjectCode']);
		$lectureHours = mysql_real_escape_string($_REQUEST['lectureHours']);
		$lectureUnits = mysql_real_escape_string($_REQUEST['lectureUnits']);
		$practicalHours = mysql_real_escape_string($_REQUEST['practicalHours']);
		$practicalUnits = mysql_real_escape_string($_REQUEST['practicalUnits']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
		if ($subjectName != $subject1->getSubjectName())	{
			$subject1->setSubjectName($subjectName); $enableUpdate = true;
		}
		if ($subjectCode != $subject1->getSubjectCode())	{
			$subject1->setSubjectCode($subjectCode); $enableUpdate = true;
		}
		if ($lectureHours != $subject1->getLectureHours())	{
			$subject1->setLectureHours($lectureHours); $enableUpdate = true;
		}
		if ($lectureUnits != $subject1->getLectureUnits())	{
			$subject1->setLectureUnits($lectureUnits); $enableUpdate = true;
		}
		if ($practicalHours != $subject1->getPracticalHours())	{
			$subject1->setPracticalHours($practicalHours); $enableUpdate = true;
		}
		if ($practicalUnits != $subject1->getPracticalUnits())	{
			$subject1->setPracticalUnits($practicalUnits); $enableUpdate = true;
		}
		if ($extraInformation != $subject1->getExtraInformation())	{
			$subject1->setExtraInformation($extraInformation); $enableUpdate = true;
		}
		//This is the Last 
		if (trim($_REQUEST['rndx']) != $subject1->getExtraFilter())	{
			$enableUpdate = false;
			$promise1->setReason("The System has Detected you are replaying data in your Browser window");
			$promise1->setPromise(false);
		}
		//Now proceed with general find a new value of extra Filter 
		$subject1->setExtraFilter(System::getCodeString(8));
		if ($enableUpdate)	{
			try {
				$subject1->commitUpdate();
				$promise1->setPromise(true);
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
				$enableUpdate = false;
			}
		} 
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Subject Editing Report</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Subject <?= $subject1->getSubjectName() ?> has been updated successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were no updates for the subject <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managesubject_edit", "Edited ".$subject1->getSubjectName());
	} else if ($page == "managesubject_edit" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managesubject_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$subject1 = null;
		$extraFilter = System::getCodeString(8);
		try {
			$subject1 = new Subject($database, $_REQUEST['id'], $conn);
			$subject1->setExtraFilter($extraFilter);
			$subject1->commitUpdate();
		} catch (Exception $e)	{ die($e->getMessage()); }
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Edit Subject <?= $subject1->getSubjectName() ?></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managesubject_edit"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="id" value="<?= $subject1->getSubjectId() ?>"/>
					<input type="hidden" name="rndx" value="<?= $subject1->getExtraFilter() ?>"/>
					<div class="pure-control-group">
						<label for="subjectName">Subject Name </label>
						<input value="<?= $subject1->getSubjectName() ?>" type="text" name="subjectName" id="subjectName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Subject Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="subjectCode">Subject Code </label>
						<input value="<?= $subject1->getSubjectCode() ?>" type="text" name="subjectCode" id="subjectCode" size="24" required pattern="<?= $exprL8Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL8Name ?>" validate_message="Subject Code : <?= $msgL8Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="lectureHours">Lecture Hours </label>
						<input value="<?= $subject1->getLectureHours() ?>" class="text-can-not-be-zero" type="text" name="lectureHours" id="lectureHours" size="16" required pattern="<?= $expr4Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr4Number ?>" validate_message="Lecture Hours : <?= $msg4Number ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="lectureUnits">Lecture Units </label>
						<input value="<?= $subject1->getLectureUnits() ?>" class="text-can-not-be-zero" type="text" name="lectureUnits" id="lectureUnits" size="16" required pattern="<?= $expr4Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr4Number ?>" validate_message="Lecture Units : <?= $msg4Number ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="practicalHours">Practical Hours </label>
						<input value="<?= $subject1->getPracticalHours() ?>" class="text-can-not-be-zero" type="text" name="practicalHours" id="practicalHours" size="16" required pattern="<?= $expr4Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr4Number ?>" validate_message="Practical Hours : <?= $msg4Number ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="practicalUnits">Practical Units </label>
						<input value="<?= $subject1->getPracticalUnits() ?>" class="text-can-not-be-zero" type="text" name="practicalUnits" id="practicalUnits" size="16" required pattern="<?= $expr4Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr4Number ?>" validate_message="Practical Units : <?= $msg4Number ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input value="<?= $subject1->getExtraInformation() ?>" type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Edit Record"/>
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
		var enableSubmit = true;
		$('input.text-can-not-be-zero').each(function(i, v)	{
			var $text1 = $(v);
			enableSubmit = enableSubmit && (parseInt($text1.val()) != 0);
		});
		if (enableSubmit)	{
			generalFormSubmission(this, 'form1', 'perror');
		} else {
			$target1.empty();
			$('<span/>').html('Lecture Units or Lecture Hours can not be zero')
				.appendTo($target1);
			$('<br/>').appendTo($target1);
			$('<span/>').html('and Practical Units or Practical Hours can not be zero')
				.appendTo($target1);
		}
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if ($page == "managesubject_detail" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managesubject_detail", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$subject1 = null;
		try {
			$subject1 = new Subject($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{die($e->getMessage());}
		mysql_close($conn);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Details for <?= $subject1->getSubjectName() ?></div>
		<div class="ui-sys-panel-content ui-sys-data-capture">
			<table class="pure-table">
				<thead>
					<tr>
						<th>Field Name</th>
						<th>Field Value</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Subject Name</td>
						<td><?= $subject1->getSubjectName() ?></td>
					</tr>
					<tr>
						<td>Subject Code</td>
						<td><?= $subject1->getSubjectCode() ?></td>
					</tr>
					<tr>
						<td>Lecture Hours</td>
						<td><?= $subject1->getLectureHours() ?></td>
					</tr>
					<tr>
						<td>Lecture Units</td>
						<td><?= $subject1->getLectureUnits() ?></td>
					</tr>
					<tr>
						<td>Practical Hours</td>
						<td><?= $subject1->getPracticalHours() ?></td>
					</tr>
					<tr>
						<td>Practical Units</td>
						<td><?= $subject1->getPracticalUnits() ?></td>
					</tr>
<?php 
	if (! is_null($subject1->getExtraInformation()))	{
?>
					<tr>
						<td>Extra Information</td>
						<td><?= $subject1->getExtraInformation() ?></td>
					</tr>
<?php
	}
?>
				</tbody>
			</table>
		</div>
		<div class="ui-sys-panel-footer">
			<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "managesubject_edit", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Edit <?= $subject1->getSubjectName() ?>" href="<?= $thispage ?>?page=managesubject_edit&id=<?= $subject1->getSubjectId() ?>"><img alt="DAT" src="../sysimage/buttonedit.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "managesubject_delete", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Delete <?= $subject1->getSubjectName() ?>" href="<?= $thispage ?>?page=managesubject_delete&id=<?= $subject1->getSubjectId() ?>"><img alt="DAT" src="../sysimage/buttondelete.png"/></a>
<?php
	}
?>
				<span class="ui-sys-clear-both">&nbsp;</span><br />
			</div>
		</div>
	</div>
<?php
	} else if ($page == "managesubject_add" && isset($_REQUEST['report']) && Authorize::isAllowable($config, "managesubject_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$subjectName = mysql_real_escape_string($_REQUEST['subjectName']);
		$subjectCode = mysql_real_escape_string($_REQUEST['subjectCode']);
		$lectureHours = mysql_real_escape_string($_REQUEST['lectureHours']);
		$lectureUnits = mysql_real_escape_string($_REQUEST['lectureUnits']);
		$practicalHours = mysql_real_escape_string($_REQUEST['practicalHours']);
		$practicalUnits = mysql_real_escape_string($_REQUEST['practicalUnits']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel header ui-widget-header">Summary Report for <i><?= $subjectName ?></i></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	$extraFilter = System::getCodeString(8);
	$query="INSERT INTO subject (subjectName, subjectCode, lectureHours, lectureUnits, practicalHours, practicalUnits, extraFilter, extraInformation) VALUES('$subjectName', '$subjectCode', '$lectureHours', '$lectureUnits', '$practicalHours', '$practicalUnits', '$extraFilter', '$extraInformation')";
	try {
		mysql_db_query($database, $query, $conn) or Object::shootException("Subject[Adding]: Could not Add a Record into the System");
		$promise1->setPromise(true);
		$enableUpdate = true;
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">The Subject <?= $subjectName ?>, has successful Added to the system</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Adding the subject<br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managesubject_add", "Added $subjectName");
	} else if ($page == "managesubject_add" && Authorize::isAllowable($config, "managesubject_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Add a New Subject</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managesubject_add"/>
					<input type="hidden" name="report" value="io"/>
					<div class="pure-control-group">
						<label for="subjectName">Subject Name </label>
						<input type="text" name="subjectName" id="subjectName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Subject Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="subjectCode">Subject Code </label>
						<input type="text" name="subjectCode" id="subjectCode" size="24" required pattern="<?= $exprL8Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL8Name ?>" validate_message="Subject Code : <?= $msgL8Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="lectureHours">Lecture Hours </label>
						<input class="text-can-not-be-zero" type="text" name="lectureHours" id="lectureHours" size="16" required pattern="<?= $expr4Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr4Number ?>" validate_message="Lecture Hours : <?= $msg4Number ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="lectureUnits">Lecture Units </label>
						<input class="text-can-not-be-zero" type="text" name="lectureUnits" id="lectureUnits" size="16" required pattern="<?= $expr4Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr4Number ?>" validate_message="Lecture Units : <?= $msg4Number ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="practicalHours">Practical Hours </label>
						<input class="text-can-not-be-zero" type="text" name="practicalHours" id="practicalHours" size="16" required pattern="<?= $expr4Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr4Number ?>" validate_message="Practical Hours : <?= $msg4Number ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="practicalUnits">Practical Units </label>
						<input class="text-can-not-be-zero" type="text" name="practicalUnits" id="practicalUnits" size="16" required pattern="<?= $expr4Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr4Number ?>" validate_message="Practical Units : <?= $msg4Number ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Add Record"/>
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
		var enableSubmit = true;
		$('input.text-can-not-be-zero').each(function(i, v)	{
			var $text1 = $(v);
			enableSubmit = enableSubmit && (parseInt($text1.val()) != 0);
		});
		if (enableSubmit)	{
			generalFormSubmission(this, 'form1', 'perror');
		} else {
			$target1.empty();
			$('<span/>').html('Lecture Units or Lecture Hours can not be zero')
				.appendTo($target1);
			$('<br/>').appendTo($target1);
			$('<span/>').html('and Practical Units or Practical Hours can not be zero')
				.appendTo($target1);
		}
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if ($page == "managesubject" && Authorize::isAllowable($config, "managesubject", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Subjects Management</div>
			<div class="ui-sys-panel-body">
<!--Block ONE controls for searching-->
				<div class="ui-sys-search-container pure-form pure-subject-control">
					<input type="text" title="At Least Three Characters should be supplied" required placeholder="ABC" size="32"/>
					<a title="Click to Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=managesubject" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
				</div>
<!--Block ONE ends-->	
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
		//saving Authorization Rules 
		$blnAuthorizationDetail = Authorize::isAllowable($config, "managesubject_detail", "normal", "donotsetlog", "-1", "-1");
?>
<!--Block Two, Search Results-->
<div class="ui-sys-search-results">
	<label id="statustextlabel"></label>
	<table class="pure-table ui-sys-table-search-results">
		<thead>
			<tr>
				<th>S/N</th>
				<th>Subject Name</th>
				<th>Subject <br/>Code</th>
				<th title="Lecture Hours">L.H</th>
				<th title="Lecture Units">L.U</th>
				<th title="Practical Hours">P.H</th>
				<th title="Practical Units">P.U</th>
				<th></th>
			</tr>
		</thead>
<?php 
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
	$query = Subject::getQueryText();
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$result = mysql_db_query($database, $query, $conn) or die("Could not fetch subject data from the storage");
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	while (list($id) = mysql_fetch_row($result))	{
		if ($sqlReturnedRowCount > $maximumReturnedRows)	{
			//Finalize All House Keeping Here
			break;
		}
		$subject1 = null;
		try {
			$subject1 = new Subject($database, $id, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated per objects 
		if ($subject1->searchMatrix($matrix1)->evaluateResult())	{
			if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
				$numberOfTBodies++;
				$tbodyClosed = false;
			} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
				$numberOfTBodies++;
				$perTbodyCounter = 0;
			}
			//In All Cases we need to display tr
			$pureTableOddClass = "";
			if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
			<tr class="<?= $pureTableOddClass ?>">
				<td><?= $sqlReturnedRowCount + 1 ?></td>
				<td><?= $subject1->getSubjectName() ?></td>
				<td><?= $subject1->getSubjectCode() ?></td>
				<td><?= $subject1->getLectureHours() ?></td>
				<td><?= $subject1->getLectureUnits() ?></td>
				<td><?= $subject1->getPracticalHours() ?></td>
				<td><?= $subject1->getPracticalUnits() ?></td>
				<td>
<?php 
	if ($blnAuthorizationDetail)	{
?>
		<a href="<?= $thispage ?>?page=managesubject_detail&id=<?= $subject1->getSubjectId() ?>" class="ui-sys-search-results-controls"><img alt="DAT" src="../sysimage/buttondetail.png"/></a>
<?php
	}
?>				
				</td>
			</tr>
<?php
			$perTbodyCounter++;
			$sqlReturnedRowCount++;
		}//end-evaluate-results
	}//end-while
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
	mysql_close($conn);
?>
	</table>
	<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
	<ul class="ui-sys-pagination"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
</div>
<!--Block Two Ends-->
<?php
	} //end-if-searchtext
?>		
<!--Beginning of Block Three-->
				<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "managesubject_csv", "normal", "donotsetlog", "-1", "-1")) {
?>
		<a title="Download CSV/EXCEL Formatted Data" href="<?= $thispage ?>?page=managesubject_csv"><img alt="DAT" src="../sysimage/buttoncsv.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "managesubject_add", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Add a New System Subject" href="<?= $thispage ?>?page=managesubject_add"><img alt="DAT" src="../sysimage/buttonadd.png"/></a>
<?php	
	}
?>				
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	}/*Subject End*/ else/*Course BEGIN*/if ($page == "managecourse_csv" && isset($_REQUEST['report']) && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managecourse_csv", "normal", "setlog", "-1", "-1"))	{
		$filename=$_REQUEST['report'];
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Course CSV File<br/> (Download Report)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (unlink($filename))	{
?>
		<div class="ui-state-highlight">
			The temporary file has been removed succesful from the Server
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problem on removing the temporary file from the Server
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	} else if ($page == "managecourse_csv" && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managecourse_csv", "normal", "setlog", "-1", "-1"))	{
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$promise1 = new Promise();
		$promise1->setPromise(true);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Course CSV File <br/> (Downlad CSV File)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	$filename="";
	$tfname=$filename.$date;
	$tfname=$tfname.rand(100,999);
	$filename=sha1($tfname);
	$filename=$filename.".csv";
	$filename="../temp/".$filename;
	$searchText = Object::getSearchTextFromUserFieldData($_REQUEST['controlIndex'], $_REQUEST['fieldname'], $_REQUEST['op'], $_REQUEST['namespaceTag'], $_REQUEST['fieldvalue']);
	$lineCounter = 0;
	$file1 = fopen($filename, "w") or die("Could not Open the File for writing");
	$csvProcessor1 = null;
	try {
		$csvProcessor1 = new CSVProcessor($searchText, $file1);
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$primise1->setPromise(false);
	}
	$result = null;
	if ($promise1->isPromising())	{
		$query = Course::getQueryText();
		try {
			$result = mysql_db_query($database, $query, $conn) or Object::shootException("Course[CSV]: Could not Execute Query");
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$primise1->setPromise(false);
		}
	}
	if ($promise1->isPromising())	{
		while (list($id) = mysql_fetch_row($result))	{
			$course1 = null;
			try	{
				$course1 = new Course($database, $id, $conn);
			} catch (Exception $e)	{ die($e->getMessage()); }
			$csvProcessor1->reload(); //Clear the Previous Object Properties 
			if ($course1->processCSV($csvProcessor1)->evaluateResult())	{
				$lineCounter++;
			}
		}//end-while 
	}//end-if-promising
	fclose($file1);
	if ($promise1->isPromising())	{
?>
		<div>
			Your file is Ready for downloading<br/>
			Number data rows it contains is <?= $lineCounter ?><br/><br/>
			<b style="font-size: 1.1em;"><a href="<?= $filename ?>">Click Here</a></b> to download your file<br/><br/>
			Once the download is complete, kindly click the proceed button 
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were problems in generating your file <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>					
			</div>
			<div class="ui-sys-panel-footer">
				<div id="perror" class="ui-sys-error-message"></div>
<?php 
	if ($promise1->isPromising())	{
		if ($lineCounter > 0)	{
?>
			<a id="__id_csv_previewer" data-error-control="perror" data-server-forward-path="../server/getcsvpreviewer.php" data-file-to-read="<?= $filename ?>" data-dialog-container="__id_general_dialog_holder" class="button-link">Preview</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php
		}
?>
		<a class="button-link" href="<?= $thispage ?>?page=managecourse_csv&report=<?= $filename ?>&downloadable=true">Continue</a>
<?php
	}
?>			
			</div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managecourse_csv" && isset($_REQUEST['sortable']) && Authorize::isAllowable($config, "managecourse_csv", "normal", "setlog", "-1", "-1"))	{
		$enableUpdate = false;
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Course CSV File <br/> (Column Sorting)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (isset($_REQUEST['fieldname']) && isset($_REQUEST['fieldtype']) && isset($_REQUEST['namespaceTag']) && isset($_REQUEST['fieldvalue']))	{
		$enableUpdate = true;
?>
				<form class="pure-form" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managecourse_csv"/>
					<input type="hidden" name="downloadable" value="true"/>
<?php 
	$sortableContainer = Object::getUICSVDivSortable(Object::getLineToSortFromUserFieldData($_REQUEST['fieldname'], $_REQUEST['fieldtype'], $_REQUEST['namespaceTag'],$_REQUEST['fieldvalue']));
	echo $sortableContainer;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
<?php 
	} else {
?>
		Perhaps you did not select any column to be included in your CSV File <br/>
		Kindly go back to Column Selection and select atleast ONE Column
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer">
				<a class="button-link" href="<?= $thispage ?>?page=managecourse_csv">Back to Column Selection</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php 
	if ($enableUpdate)	{
?>				
				<input type="button" id="__add_record" value="Proceed"/>
<?php 
	}
?>
			</div>
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
	} else if ($page == "managecourse_csv" && Authorize::isAllowable($config, "managecourse_csv", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Course CSV File <br/>(Column Selection)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
						<input type="hidden" name="page" value="managecourse_csv"/>
						<input type="hidden" name="sortable" value="true"/>
<?php
	$listOfAvailableColumnsInTables = Object::getUICSVTables(Course::getAvailableViewableColumns());
	echo $listOfAvailableColumnsInTables;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer">
				<input type="button" id="__add_record" value="Proceed"/>
			</div>
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
	} else if ($page == "managecourse_delete" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managecourse_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$course1 = null;
		try {
			 $course1 = new Course($database, $_REQUEST['id'], $conn);
			 $course1->commitDelete();
			 $enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason("You might be dealing with a Course which is no longer existing in the system or a None Empty Record");
			$promise1->setPromise(false);
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Course Removal Report</div>
			<div class="ui-sys-panel-pody ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			The Course <?= $course1->getCourseName() ?> has been successful removed from the system
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Removing the Course <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managecourse_delete", "Deleted ".$course1->getCourseName());
	} else if ($page == "managecourse_delete" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managecourse_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to database services");
		$course1 = null;
		try {
			$course1 = new Course($database, $_REQUEST['id'], $conn);
			$course1->setExtraFilter(System::getCodeString(8));
			$course1->commitUpdate();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Delete Confirmation</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<div class="ui-sys-warning">
					You are about to remove a Course <b><?= $course1->getCourseName() ?></b> from the System <br/>
					<b>NOTE: This Operation Can not be reversed</b><br/>
					Are You Sure You want to remove the Course <?= $course1->getCourseName() ?> from the System?
				</div>
				<div class="ui-sys-center-text">
					<a class="button-link-warning" href="<?= $thispage ?>?page=managecourse_delete&report=io&id=<?= $course1->getCourseId() ?>&rndx=<?= $course1->getExtraFilter() ?>">Yes (Remove)</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a class="button-link" href="<?= $thispage ?>?page=managecourse">No (Cancel)</a>
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managecourse_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managecourse_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$course1 = null;
		try {
			$course1 = new Course($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$promise1 = new Promise();
		$promise1->setReason("Nothing Has been Updated");
		$enableUpdate = false;
		$courseName = mysql_real_escape_string($_REQUEST['courseName']);
		$courseCode = mysql_real_escape_string($_REQUEST['courseCode']);
		$duration = mysql_real_escape_string($_REQUEST['duration']);
		$departmentId = mysql_real_escape_string($_REQUEST['departmentId']);
		$nextRegistrationNumber = mysql_real_escape_string($_REQUEST['nextRegistrationNumber']);
		$registrationNumberWidth = mysql_real_escape_string($_REQUEST['registrationNumberWidth']);
		$nextExaminationNumber = mysql_real_escape_string($_REQUEST['nextExaminationNumber']);
		$examinationNumberWidth = mysql_real_escape_string($_REQUEST['examinationNumberWidth']);
		$levelId = mysql_real_escape_string($_REQUEST['levelId']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
		if ($courseName != $course1->getCourseName())	{
			$course1->setCourseName($courseName); $enableUpdate = true;
		}
		if ($courseCode != $course1->getCourseCode())	{
			$course1->setCourseCode($courseCode); $enableUpdate = true;
		}
		if ($duration != $course1->getDuration())	{
			$course1->setDuration($duration); $enableUpdate = true;
		}
		if (is_null($course1->getDepartment()) || ($departmentId != $course1->getDepartment()->getDepartmentId()))	{
			$course1->setDepartment($departmentId); $enableUpdate = true;
		}
		if ($nextRegistrationNumber != $course1->getNextRegistrationNumber())	{
			$course1->setNextRegistrationNumber($nextRegistrationNumber); $enableUpdate = true;
		}
		if ($registrationNumberWidth != $course1->getRegistrationNumberWidth())	{
			$course1->setRegistrationNumberWidth($registrationNumberWidth); $enableUpdate = true;
		}
		if ($nextExaminationNumber != $course1->getNextExaminationNumber())	{
			$course1->setNextExaminationNumber($nextExaminationNumber); $enableUpdate = true;
		}
		if ($examinationNumberWidth != $course1->getExaminationNumberWidth())	{
			$course1->setExaminationNumberWidth($examinationNumberWidth); $enableUpdate = true;
		}
		if (is_null($course1->getEducationLevel()) || ($levelId != $course1->getEducationLevel()->getLevelId()))	{
			$course1->setEducationLevel($levelId); $enableUpdate = true;
		}
		if ($extraInformation != $course1->getExtraInformation())	{
			$course1->setExtraInformation($extraInformation); $enableUpdate = true;
		}
		//This is the Last 
		if (trim($_REQUEST['rndx']) != $course1->getExtraFilter())	{
			$enableUpdate = false;
			$promise1->setReason("The System has Detected you are replaying data in your Browser window");
			$promise1->setPromise(false);
		}
		//Now proceed with general find a new value of extra Filter 
		$course1->setExtraFilter(System::getCodeString(8));
		if ($enableUpdate)	{
			try {
				$course1->commitUpdate();
				$promise1->setPromise(true);
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
				$enableUpdate = false;
			}
		} 
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Course Editing Report</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Course <?= $course1->getCourseName() ?> has been updated successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were no updates for the course <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managecourse_edit", "Edited ".$course1->getCourseName());
	} else if ($page == "managecourse_edit" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managecourse_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$course1 = null;
		$extraFilter = System::getCodeString(8);
		try {
			$course1 = new Course($database, $_REQUEST['id'], $conn);
			$course1->setExtraFilter($extraFilter);
			$course1->commitUpdate();
		} catch (Exception $e)	{ die($e->getMessage()); }
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Edit Course <?= $course1->getCourseName() ?></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managecourse_edit"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="id" value="<?= $course1->getCourseId() ?>"/>
					<input type="hidden" name="rndx" value="<?= $course1->getExtraFilter() ?>"/>
					<div class="pure-control-group">
						<label for="courseName">Course Name </label>
						<input value="<?= $course1->getCourseName() ?>" type="text" name="courseName" id="courseName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Course Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="courseCode">Course Code </label>
						<input value="<?= $course1->getCourseCode() ?>" type="text" name="courseCode" id="courseCode" size="24" required pattern="<?= $exprL8Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL8Name ?>" validate_message="Course Code : <?= $msgL8Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="duration">Course Duration(years) </label>
						<input value="<?= $course1->getDuration() ?>" type="text" name="duration" id="duration" size="4" required pattern="<?= $expr2Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr2Number ?>" validate_message="Course Duration(Years) : <?= $msg2Number ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="departmentId">Department </label>
						<select id="departmentId" name="departmentId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Department">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Department::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($course1->getDepartment()) && ($alist1['id'] == $course1->getDepartment()->getDepartmentId())) $selected="selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="nextRegistrationNumber">Next Registration Number </label>
						<input class="text-can-not-be-zero" value="<?= $course1->getNextRegistrationNumber() ?>" type="text" name="nextRegistrationNumber" id="nextRegistrationNumber" size="16" required pattern="<?= $exprD8Number ?>" validate="true" validate_control="text" validate_expression="<?= $exprD8Number ?>" validate_message="Next Registration Number : <?= $msgD8Number ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="registrationNumberWidth">Registration Number Width </label>
						<input class="text-can-not-be-zero" value="<?= $course1->getRegistrationNumberWidth() ?>" type="text" name="registrationNumberWidth" id="registrationNumberWidth" size="16" required pattern="<?= $exprD8Number ?>" validate="true" validate_control="text" validate_expression="<?= $exprD8Number ?>" validate_message="Registration Number Width : <?= $msgD8Number ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="nextExaminationNumber">Next Examination Number </label>
						<input class="text-can-not-be-zero" value="<?= $course1->getNextExaminationNumber() ?>" type="text" name="nextExaminationNumber" id="nextExaminationNumber" size="16" required pattern="<?= $exprD8Number ?>" validate="true" validate_control="text" validate_expression="<?= $exprD8Number ?>" validate_message="Next Examination Number : <?= $msgD8Number ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="examinationNumberWidth">Examination Number Width </label>
						<input class="text-can-not-be-zero" value="<?= $course1->getExaminationNumberWidth() ?>" type="text" name="examinationNumberWidth" id="examinationNumberWidth" size="16" required pattern="<?= $exprD8Number ?>" validate="true" validate_control="text" validate_expression="<?= $exprD8Number ?>" validate_message="Examination Number Width : <?= $msgD8Number ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="levelId">Education Level </label>
						<select id="levelId" name="levelId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Level this course belong to">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = EducationLevel::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($course1->getEducationLevel()) && ($alist1['id'] == $course1->getEducationLevel()->getLevelId())) $selected="selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input value="<?= $course1->getExtraInformation() ?>" type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Edit Record"/>
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
		var enableSubmit = true;
		//Process here 
		$('input.text-can-not-be-zero').each(function(i, val)	{
			var $text1 = $(val);
			enableSubmit = enableSubmit && (parseInt($text1.val()) != 0);
		});
		if (enableSubmit)	{
			generalFormSubmission(this, 'form1', 'perror');
		} else {
			$target1.empty();
			$('<span/>').html("None of Registration or Examination Number can start at zero")
						.appendTo($target1);
			$('<br/>').appendTo($target1);
			$('<span/>').html("Neither their width can be zero")
						.appendTo($target1);
		}
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if ($page == "managecourse_detail" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managecourse_detail", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$course1 = null;
		try {
			$course1 = new Course($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{die($e->getMessage());}
		mysql_close($conn);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Details for <?= $course1->getCourseName() ?></div>
		<div class="ui-sys-panel-content ui-sys-data-capture">
			<table class="pure-table">
				<thead>
					<tr>
						<th>Field Name</th>
						<th>Field Value</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Course Name</td>
						<td><?= $course1->getCourseName() ?></td>
					</tr>
					<tr>
						<td>Course Code</td>
						<td><?= $course1->getCourseCode() ?></td>
					</tr>
					<tr>
						<td>Course Duration (Year)</td>
						<td><?= $course1->getDuration() ?></td>
					</tr>
<?php 
	if (! is_null($course1->getDepartment()))	{
?>
					<tr>
						<td>Department</td>
						<td><?= $course1->getDepartment()->getDepartmentName() ?></td>
					</tr>
<?php
	}
?>
					<tr>
						<td>Next Registration Number</td>
						<td><?= $course1->getNextRegistrationNumber() ?></td>
					</tr>
					<tr>
						<td>Registration Numnber Width</td>
						<td><?= $course1->getRegistrationNumberWidth() ?></td>
					</tr>
					<tr>
						<td>Next Examination Number</td>
						<td><?= $course1->getNextExaminationNumber() ?></td>
					</tr>
					<tr>
						<td>Examination Number Width</td>
						<td><?= $course1->getExaminationNumberWidth() ?></td>
					</tr>
<?php
	if (! is_null($course1->getEducationLevel()))	{
?>
					<tr>
						<td>Education Level</td>
						<td><?= $course1->getEducationLevel()->getLevelName() ?></td>
					</tr>
<?php
	}
	if (! is_null($course1->getExtraInformation()))	{
?>
					<tr>
						<td>Extra Information</td>
						<td><?= $course1->getExtraInformation() ?></td>
					</tr>
<?php
	}
?>
				</tbody>
			</table>
		</div>
		<div class="ui-sys-panel-footer">
			<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "managecourse_edit", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Edit <?= $course1->getCourseName() ?>" href="<?= $thispage ?>?page=managecourse_edit&id=<?= $course1->getCourseId() ?>"><img alt="DAT" src="../sysimage/buttonedit.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "managecourse_delete", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Delete <?= $course1->getCourseName() ?>" href="<?= $thispage ?>?page=managecourse_delete&id=<?= $course1->getCourseId() ?>"><img alt="DAT" src="../sysimage/buttondelete.png"/></a>
<?php
	}
?>
				<span class="ui-sys-clear-both">&nbsp;</span><br />
			</div>
		</div>
	</div>
<?php
	} else if ($page == "managecourse_add" && isset($_REQUEST['report']) && Authorize::isAllowable($config, "managecourse_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$courseName = mysql_real_escape_string($_REQUEST['courseName']);
		$courseCode = mysql_real_escape_string($_REQUEST['courseCode']);
		$duration = mysql_real_escape_string($_REQUEST['duration']);
		$departmentId = mysql_real_escape_string($_REQUEST['departmentId']);
		$levelId = mysql_real_escape_string($_REQUEST['levelId']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel header ui-widget-header">Summary Report for <i><?= $courseName ?></i></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	$extraFilter = System::getCodeString(8);
	$query="INSERT INTO course (courseName, courseCode, duration, departmentId, levelId, extraFilter, extraInformation) VALUES('$courseName', '$courseCode', '$duration', '$departmentId', '$levelId', '$extraFilter', '$extraInformation')";
	try {
		mysql_db_query($database, $query, $conn) or Object::shootException("Course[Adding]: Could not Add a Record into the System");
		$promise1->setPromise(true);
		$enableUpdate = true;
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">The Course <?= $courseName ?>, has successful Added to the system</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Adding the course<br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managecourse_add", "Added $courseName");
	} else if ($page == "managecourse_add" && Authorize::isAllowable($config, "managecourse_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Add a New Course</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managecourse_add"/>
					<input type="hidden" name="report" value="io"/>
					<div class="pure-control-group">
						<label for="courseName">Course Name </label>
						<input type="text" name="courseName" id="courseName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Course Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="courseCode">Course Code </label>
						<input type="text" name="courseCode" id="courseCode" size="24" required pattern="<?= $exprL8Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL8Name ?>" validate_message="Course Code : <?= $msgL8Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="duration">Course Duration(years) </label>
						<input type="text" name="duration" id="duration" size="4" required pattern="<?= $expr2Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr2Number ?>" validate_message="Course Duration(Years) : <?= $msg2Number ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="departmentId">Department </label>
						<select id="departmentId" name="departmentId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Department">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Department::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
?>
			<option value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="levelId">Education Level </label>
						<select id="levelId" name="levelId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Level this course belong to">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = EducationLevel::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
?>
			<option value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Add Record"/>
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
	} else if ($page == "managecourse" && Authorize::isAllowable($config, "managecourse", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Courses Management</div>
			<div class="ui-sys-panel-body">
<!--Block ONE controls for searching-->
				<div class="ui-sys-search-container pure-form pure-course-control">
					<input type="text" title="At Least Three Characters should be supplied" required placeholder="ABC" size="32"/>
					<a title="Click to Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=managecourse" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
				</div>
<!--Block ONE ends-->	
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
		//saving Authorization Rules 
		$blnAuthorizationDetail = Authorize::isAllowable($config, "managecourse_detail", "normal", "donotsetlog", "-1", "-1");
?>
<!--Block Two, Search Results-->
<div class="ui-sys-search-results">
	<label id="statustextlabel"></label>
	<table class="pure-table ui-sys-table-search-results">
		<thead>
			<tr>
				<th>S/N</th>
				<th>Course Name</th>
				<th>Course Code</th>
				<th>Course <br/>Duration</th>
				<th>Department</th>
				<th></th>
			</tr>
		</thead>
<?php 
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
	$query = Course::getQueryText();
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$result = mysql_db_query($database, $query, $conn) or die("Could not fetch course data from the storage");
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	while (list($id) = mysql_fetch_row($result))	{
		if ($sqlReturnedRowCount > $maximumReturnedRows)	{
			//Finalize All House Keeping Here
			break;
		}
		$course1 = null;
		try {
			$course1 = new Course($database, $id, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated per objects 
		if ($course1->searchMatrix($matrix1)->evaluateResult())	{
			if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
				$numberOfTBodies++;
				$tbodyClosed = false;
			} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
				$numberOfTBodies++;
				$perTbodyCounter = 0;
			}
			//In All Cases we need to display tr
			$pureTableOddClass = "";
			if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
			<tr class="<?= $pureTableOddClass ?>">
				<td><?= $sqlReturnedRowCount + 1 ?></td>
				<td><?= $course1->getCourseName() ?></td>
				<td><?= $course1->getCourseCode() ?></td>
				<td><?= $course1->getDuration() ?></td>
				<td>
<?php 
	if (! is_null($course1->getDepartment()))	{
?>
					<?= $course1->getDepartment()->getDepartmentName() ?>
<?php
	}
?>				
				</td>
				<td>
<?php 
	if ($blnAuthorizationDetail)	{
?>
		<a href="<?= $thispage ?>?page=managecourse_detail&id=<?= $course1->getCourseId() ?>" class="ui-sys-search-results-controls"><img alt="DAT" src="../sysimage/buttondetail.png"/></a>
<?php
	}
?>				
				</td>
			</tr>
<?php
			$perTbodyCounter++;
			$sqlReturnedRowCount++;
		}//end-evaluate-results
	}//end-while
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
	mysql_close($conn);
?>
	</table>
	<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
	<ul class="ui-sys-pagination"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
</div>
<!--Block Two Ends-->
<?php
	} //end-if-searchtext
?>		
<!--Beginning of Block Three-->
				<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "managecourse_csv", "normal", "donotsetlog", "-1", "-1")) {
?>
		<a title="Download CSV/EXCEL Formatted Data" href="<?= $thispage ?>?page=managecourse_csv"><img alt="DAT" src="../sysimage/buttoncsv.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "managecourse_add", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Add a New System Course" href="<?= $thispage ?>?page=managecourse_add"><img alt="DAT" src="../sysimage/buttonadd.png"/></a>
<?php	
	}
?>				
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	}/*Course End*/ else/*EducationLevel BEGIN*/if ($page == "manageeducationlevel_csv" && isset($_REQUEST['report']) && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "manageeducationlevel_csv", "normal", "setlog", "-1", "-1"))	{
		$filename=$_REQUEST['report'];
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">EducationLevel CSV File<br/> (Download Report)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (unlink($filename))	{
?>
		<div class="ui-state-highlight">
			The temporary file has been removed succesful from the Server
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problem on removing the temporary file from the Server
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	} else if ($page == "manageeducationlevel_csv" && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "manageeducationlevel_csv", "normal", "setlog", "-1", "-1"))	{
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$promise1 = new Promise();
		$promise1->setPromise(true);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">EducationLevel CSV File <br/> (Downlad CSV File)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	$filename="";
	$tfname=$filename.$date;
	$tfname=$tfname.rand(100,999);
	$filename=sha1($tfname);
	$filename=$filename.".csv";
	$filename="../temp/".$filename;
	$searchText = Object::getSearchTextFromUserFieldData($_REQUEST['controlIndex'], $_REQUEST['fieldname'], $_REQUEST['op'], $_REQUEST['namespaceTag'], $_REQUEST['fieldvalue']);
	$lineCounter = 0;
	$file1 = fopen($filename, "w") or die("Could not Open the File for writing");
	$csvProcessor1 = null;
	try {
		$csvProcessor1 = new CSVProcessor($searchText, $file1);
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$primise1->setPromise(false);
	}
	$result = null;
	if ($promise1->isPromising())	{
		$query = EducationLevel::getQueryText();
		try {
			$result = mysql_db_query($database, $query, $conn) or Object::shootException("EducationLevel[CSV]: Could not Execute Query");
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$primise1->setPromise(false);
		}
	}
	if ($promise1->isPromising())	{
		while (list($id) = mysql_fetch_row($result))	{
			$level1 = null;
			try	{
				$level1 = new EducationLevel($database, $id, $conn);
			} catch (Exception $e)	{ die($e->getMessage()); }
			$csvProcessor1->reload(); //Clear the Previous Object Properties 
			if ($level1->processCSV($csvProcessor1)->evaluateResult())	{
				$lineCounter++;
			}
		}//end-while 
	}//end-if-promising
	fclose($file1);
	if ($promise1->isPromising())	{
?>
		<div>
			Your file is Ready for downloading<br/>
			Number data rows it contains is <?= $lineCounter ?><br/><br/>
			<b style="font-size: 1.1em;"><a href="<?= $filename ?>">Click Here</a></b> to download your file<br/><br/>
			Once the download is complete, kindly click the proceed button 
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were problems in generating your file <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>					
			</div>
			<div class="ui-sys-panel-footer">
				<div id="perror" class="ui-sys-error-message"></div>
<?php 
	if ($promise1->isPromising())	{
		if ($lineCounter > 0)	{
?>
			<a id="__id_csv_previewer" data-error-control="perror" data-server-forward-path="../server/getcsvpreviewer.php" data-file-to-read="<?= $filename ?>" data-dialog-container="__id_general_dialog_holder" class="button-link">Preview</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php
		}
?>
		<a class="button-link" href="<?= $thispage ?>?page=manageeducationlevel_csv&report=<?= $filename ?>&downloadable=true">Continue</a>
<?php
	}
?>			
			</div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "manageeducationlevel_csv" && isset($_REQUEST['sortable']) && Authorize::isAllowable($config, "manageeducationlevel_csv", "normal", "setlog", "-1", "-1"))	{
		$enableUpdate = false;
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">EducationLevel CSV File <br/> (Column Sorting)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (isset($_REQUEST['fieldname']) && isset($_REQUEST['fieldtype']) && isset($_REQUEST['namespaceTag']) && isset($_REQUEST['fieldvalue']))	{
		$enableUpdate = true;
?>
				<form class="pure-form" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="manageeducationlevel_csv"/>
					<input type="hidden" name="downloadable" value="true"/>
<?php 
	$sortableContainer = Object::getUICSVDivSortable(Object::getLineToSortFromUserFieldData($_REQUEST['fieldname'], $_REQUEST['fieldtype'], $_REQUEST['namespaceTag'],$_REQUEST['fieldvalue']));
	echo $sortableContainer;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
<?php 
	} else {
?>
		Perhaps you did not select any column to be included in your CSV File <br/>
		Kindly go back to Column Selection and select atleast ONE Column
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer">
				<a class="button-link" href="<?= $thispage ?>?page=manageeducationlevel_csv">Back to Column Selection</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php 
	if ($enableUpdate)	{
?>				
				<input type="button" id="__add_record" value="Proceed"/>
<?php 
	}
?>
			</div>
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
	} else if ($page == "manageeducationlevel_csv" && Authorize::isAllowable($config, "manageeducationlevel_csv", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">EducationLevel CSV File <br/>(Column Selection)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
						<input type="hidden" name="page" value="manageeducationlevel_csv"/>
						<input type="hidden" name="sortable" value="true"/>
<?php
	$listOfAvailableColumnsInTables = Object::getUICSVTables(EducationLevel::getAvailableViewableColumns());
	echo $listOfAvailableColumnsInTables;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer">
				<input type="button" id="__add_record" value="Proceed"/>
			</div>
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
	} else if ($page == "manageeducationlevel_delete" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "manageeducationlevel_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$level1 = null;
		try {
			 $level1 = new EducationLevel($database, $_REQUEST['id'], $conn);
			 $level1->commitDelete();
			 $enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason("You might be dealing with a EducationLevel which is no longer existing in the system or a None Empty Record");
			$promise1->setPromise(false);
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">EducationLevel Removal Report</div>
			<div class="ui-sys-panel-pody ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			The EducationLevel <?= $level1->getLevelName() ?> has been successful removed from the system
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Removing the EducationLevel <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageeducationlevel_delete", "Deleted ".$level1->getLevelName());
	} else if ($page == "manageeducationlevel_delete" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "manageeducationlevel_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to database services");
		$level1 = null;
		try {
			$level1 = new EducationLevel($database, $_REQUEST['id'], $conn);
			$level1->setExtraFilter(System::getCodeString(8));
			$level1->commitUpdate();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Delete Confirmation</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<div class="ui-sys-warning">
					You are about to remove a EducationLevel <b><?= $level1->getLevelName() ?></b> from the System <br/>
					<b>NOTE: This Operation Can not be reversed</b><br/>
					Are You Sure You want to remove the EducationLevel <?= $level1->getLevelName() ?> from the System?
				</div>
				<div class="ui-sys-center-text">
					<a class="button-link-warning" href="<?= $thispage ?>?page=manageeducationlevel_delete&report=io&id=<?= $level1->getLevelId() ?>&rndx=<?= $level1->getExtraFilter() ?>">Yes (Remove)</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a class="button-link" href="<?= $thispage ?>?page=manageeducationlevel">No (Cancel)</a>
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "manageeducationlevel_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "manageeducationlevel_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$level1 = null;
		try {
			$level1 = new EducationLevel($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$promise1 = new Promise();
		$promise1->setReason("Nothing Has been Updated");
		$enableUpdate = false;
		$levelName = mysql_real_escape_string($_REQUEST['levelName']);
		$levelCode = mysql_real_escape_string($_REQUEST['levelCode']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
		if ($levelName != $level1->getLevelName())	{
			$level1->setLevelName($levelName); $enableUpdate = true;
		}
		if ($levelCode != $level1->getLevelCode())	{
			$level1->setLevelCode($levelCode); $enableUpdate = true;
		}
		if ($extraInformation != $level1->getExtraInformation())	{
			$level1->setExtraInformation($extraInformation); $enableUpdate = true;
		}
		//This is the Last 
		if (trim($_REQUEST['rndx']) != $level1->getExtraFilter())	{
			$enableUpdate = false;
			$promise1->setReason("The System has Detected you are replaying data in your Browser window");
			$promise1->setPromise(false);
		}
		//Now proceed with general find a new value of extra Filter 
		$level1->setExtraFilter(System::getCodeString(8));
		if ($enableUpdate)	{
			try {
				$level1->commitUpdate();
				$promise1->setPromise(true);
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
				$enableUpdate = false;
			}
		} 
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">EducationLevel Editing Report</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			EducationLevel <?= $level1->getLevelName() ?> has been updated successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were no updates for the level <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageeducationlevel_edit", "Edited ".$level1->getLevelName());
	} else if ($page == "manageeducationlevel_edit" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "manageeducationlevel_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$level1 = null;
		$extraFilter = System::getCodeString(8);
		try {
			$level1 = new EducationLevel($database, $_REQUEST['id'], $conn);
			$level1->setExtraFilter($extraFilter);
			$level1->commitUpdate();
		} catch (Exception $e)	{ die($e->getMessage()); }
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Edit EducationLevel <?= $level1->getLevelName() ?></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="manageeducationlevel_edit"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="id" value="<?= $level1->getLevelId() ?>"/>
					<input type="hidden" name="rndx" value="<?= $level1->getExtraFilter() ?>"/>
					<div class="pure-control-group">
						<label for="levelName">EducationLevel Name </label>
						<input value="<?= $level1->getLevelName() ?>" type="text" name="levelName" id="levelName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="EducationLevel Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="levelCode">EducationLevel Code </label>
						<input value="<?= $level1->getLevelCode() ?>" type="text" name="levelCode" id="levelCode" size="24" required pattern="<?= $exprL8Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL8Name ?>" validate_message="EducationLevel Code : <?= $msgL8Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input value="<?= $level1->getExtraInformation() ?>" type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Edit Record"/>
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
	} else if ($page == "manageeducationlevel_detail" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "manageeducationlevel_detail", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$level1 = null;
		try {
			$level1 = new EducationLevel($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{die($e->getMessage());}
		mysql_close($conn);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Details for <?= $level1->getLevelName() ?></div>
		<div class="ui-sys-panel-content ui-sys-data-capture">
			<table class="pure-table">
				<thead>
					<tr>
						<th>Field Name</th>
						<th>Field Value</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>EducationLevel Name</td>
						<td><?= $level1->getLevelName() ?></td>
					</tr>
					<tr>
						<td>EducationLevel Code</td>
						<td><?= $level1->getLevelCode() ?></td>
					</tr>
<?php 
	if (! is_null($level1->getExtraInformation()))	{
?>
					<tr>
						<td>Extra Information</td>
						<td><?= $level1->getExtraInformation() ?></td>
					</tr>
<?php
	}
?>
				</tbody>
			</table>
		</div>
		<div class="ui-sys-panel-footer">
			<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "manageeducationlevel_edit", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Edit <?= $level1->getLevelName() ?>" href="<?= $thispage ?>?page=manageeducationlevel_edit&id=<?= $level1->getLevelId() ?>"><img alt="DAT" src="../sysimage/buttonedit.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "manageeducationlevel_delete", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Delete <?= $level1->getLevelName() ?>" href="<?= $thispage ?>?page=manageeducationlevel_delete&id=<?= $level1->getLevelId() ?>"><img alt="DAT" src="../sysimage/buttondelete.png"/></a>
<?php
	}
?>
				<span class="ui-sys-clear-both">&nbsp;</span><br />
			</div>
		</div>
	</div>
<?php
	} else if ($page == "manageeducationlevel_add" && isset($_REQUEST['report']) && Authorize::isAllowable($config, "manageeducationlevel_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$levelName = mysql_real_escape_string($_REQUEST['levelName']);
		$levelCode = mysql_real_escape_string($_REQUEST['levelCode']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel header ui-widget-header">Summary Report for <i><?= $levelName ?></i></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	$extraFilter = System::getCodeString(8);
	$query="INSERT INTO educationLevel (levelName, levelCode, extraFilter, extraInformation) VALUES('$levelName', '$levelCode', '$extraFilter', '$extraInformation')";
	try {
		mysql_db_query($database, $query, $conn) or Object::shootException("EducationLevel[Adding]: Could not Add a Record into the System");
		$promise1->setPromise(true);
		$enableUpdate = true;
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">The EducationLevel <?= $levelName ?>, has successful Added to the system</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Adding the level<br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageeducationlevel_add", "Added $levelName");
	} else if ($page == "manageeducationlevel_add" && Authorize::isAllowable($config, "manageeducationlevel_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Add a New EducationLevel</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="manageeducationlevel_add"/>
					<input type="hidden" name="report" value="io"/>
					<div class="pure-control-group">
						<label for="levelName">EducationLevel Name </label>
						<input type="text" name="levelName" id="levelName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="EducationLevel Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="levelCode">EducationLevel Code </label>
						<input type="text" name="levelCode" id="levelCode" size="24" required pattern="<?= $exprL8Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL8Name ?>" validate_message="EducationLevel Code : <?= $msgL8Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Add Record"/>
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
	} else if ($page == "manageeducationlevel" && Authorize::isAllowable($config, "manageeducationlevel", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">EducationLevels Management</div>
			<div class="ui-sys-panel-body">
<!--Block ONE controls for searching-->
				<div class="ui-sys-search-container pure-form pure-level-control">
					<input type="text" title="At Least Three Characters should be supplied" required placeholder="ABC" size="32"/>
					<a title="Click to Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=manageeducationlevel" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
				</div>
<!--Block ONE ends-->	
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
		//saving Authorization Rules 
		$blnAuthorizationDetail = Authorize::isAllowable($config, "manageeducationlevel_detail", "normal", "donotsetlog", "-1", "-1");
?>
<!--Block Two, Search Results-->
<div class="ui-sys-search-results">
	<label id="statustextlabel"></label>
	<table class="pure-table ui-sys-table-search-results">
		<thead>
			<tr>
				<th>S/N</th>
				<th>EducationLevel Name</th>
				<th>EducationLevel Code</th>
				<th></th>
			</tr>
		</thead>
<?php 
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
	$query = EducationLevel::getQueryText();
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$result = mysql_db_query($database, $query, $conn) or die("Could not fetch level data from the storage");
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	while (list($id) = mysql_fetch_row($result))	{
		if ($sqlReturnedRowCount > $maximumReturnedRows)	{
			//Finalize All House Keeping Here
			break;
		}
		$level1 = null;
		try {
			$level1 = new EducationLevel($database, $id, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated per objects 
		if ($level1->searchMatrix($matrix1)->evaluateResult())	{
			if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
				$numberOfTBodies++;
				$tbodyClosed = false;
			} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
				$numberOfTBodies++;
				$perTbodyCounter = 0;
			}
			//In All Cases we need to display tr
			$pureTableOddClass = "";
			if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
			<tr class="<?= $pureTableOddClass ?>">
				<td><?= $sqlReturnedRowCount + 1 ?></td>
				<td><?= $level1->getLevelName() ?></td>
				<td><?= $level1->getLevelCode() ?></td>
				<td>
<?php 
	if ($blnAuthorizationDetail)	{
?>
		<a href="<?= $thispage ?>?page=manageeducationlevel_detail&id=<?= $level1->getLevelId() ?>" class="ui-sys-search-results-controls"><img alt="DAT" src="../sysimage/buttondetail.png"/></a>
<?php
	}
?>				
				</td>
			</tr>
<?php
			$perTbodyCounter++;
			$sqlReturnedRowCount++;
		}//end-evaluate-results
	}//end-while
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
	mysql_close($conn);
?>
	</table>
	<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
	<ul class="ui-sys-pagination"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
</div>
<!--Block Two Ends-->
<?php
	} //end-if-searchtext
?>		
<!--Beginning of Block Three-->
				<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "manageeducationlevel_csv", "normal", "donotsetlog", "-1", "-1")) {
?>
		<a title="Download CSV/EXCEL Formatted Data" href="<?= $thispage ?>?page=manageeducationlevel_csv"><img alt="DAT" src="../sysimage/buttoncsv.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "manageeducationlevel_add", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Add a New System EducationLevel" href="<?= $thispage ?>?page=manageeducationlevel_add"><img alt="DAT" src="../sysimage/buttonadd.png"/></a>
<?php	
	}
?>				
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	}/*EducationLevel End*/ else/*Department BEGIN*/if ($page == "managedepartment_csv" && isset($_REQUEST['report']) && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managedepartment_csv", "normal", "setlog", "-1", "-1"))	{
		$filename=$_REQUEST['report'];
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Department CSV File<br/> (Download Report)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (unlink($filename))	{
?>
		<div class="ui-state-highlight">
			The temporary file has been removed succesful from the Server
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problem on removing the temporary file from the Server
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	} else if ($page == "managedepartment_csv" && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managedepartment_csv", "normal", "setlog", "-1", "-1"))	{
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$promise1 = new Promise();
		$promise1->setPromise(true);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Department CSV File <br/> (Downlad CSV File)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	$filename="";
	$tfname=$filename.$date;
	$tfname=$tfname.rand(100,999);
	$filename=sha1($tfname);
	$filename=$filename.".csv";
	$filename="../temp/".$filename;
	$searchText = Object::getSearchTextFromUserFieldData($_REQUEST['controlIndex'], $_REQUEST['fieldname'], $_REQUEST['op'], $_REQUEST['namespaceTag'], $_REQUEST['fieldvalue']);
	$lineCounter = 0;
	$file1 = fopen($filename, "w") or die("Could not Open the File for writing");
	$csvProcessor1 = null;
	try {
		$csvProcessor1 = new CSVProcessor($searchText, $file1);
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$primise1->setPromise(false);
	}
	$result = null;
	if ($promise1->isPromising())	{
		$query = Department::getQueryText();
		try {
			$result = mysql_db_query($database, $query, $conn) or Object::shootException("Department[CSV]: Could not Execute Query");
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$primise1->setPromise(false);
		}
	}
	if ($promise1->isPromising())	{
		while (list($id) = mysql_fetch_row($result))	{
			$department1 = null;
			try	{
				$department1 = new Department($database, $id, $conn);
			} catch (Exception $e)	{ die($e->getMessage()); }
			$csvProcessor1->reload(); //Clear the Previous Object Properties 
			if ($department1->processCSV($csvProcessor1)->evaluateResult())	{
				$lineCounter++;
			}
		}//end-while 
	}//end-if-promising
	fclose($file1);
	if ($promise1->isPromising())	{
?>
		<div>
			Your file is Ready for downloading<br/>
			Number data rows it contains is <?= $lineCounter ?><br/><br/>
			<b style="font-size: 1.1em;"><a href="<?= $filename ?>">Click Here</a></b> to download your file<br/><br/>
			Once the download is complete, kindly click the proceed button 
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were problems in generating your file <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>					
			</div>
			<div class="ui-sys-panel-footer">
				<div id="perror" class="ui-sys-error-message"></div>
<?php 
	if ($promise1->isPromising())	{
		if ($lineCounter > 0)	{
?>
			<a id="__id_csv_previewer" data-error-control="perror" data-server-forward-path="../server/getcsvpreviewer.php" data-file-to-read="<?= $filename ?>" data-dialog-container="__id_general_dialog_holder" class="button-link">Preview</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php
		}
?>
		<a class="button-link" href="<?= $thispage ?>?page=managedepartment_csv&report=<?= $filename ?>&downloadable=true">Continue</a>
<?php
	}
?>			
			</div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managedepartment_csv" && isset($_REQUEST['sortable']) && Authorize::isAllowable($config, "managedepartment_csv", "normal", "setlog", "-1", "-1"))	{
		$enableUpdate = false;
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Department CSV File <br/> (Column Sorting)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (isset($_REQUEST['fieldname']) && isset($_REQUEST['fieldtype']) && isset($_REQUEST['namespaceTag']) && isset($_REQUEST['fieldvalue']))	{
		$enableUpdate = true;
?>
				<form class="pure-form" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managedepartment_csv"/>
					<input type="hidden" name="downloadable" value="true"/>
<?php 
	$sortableContainer = Object::getUICSVDivSortable(Object::getLineToSortFromUserFieldData($_REQUEST['fieldname'], $_REQUEST['fieldtype'], $_REQUEST['namespaceTag'],$_REQUEST['fieldvalue']));
	echo $sortableContainer;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
<?php 
	} else {
?>
		Perhaps you did not select any column to be included in your CSV File <br/>
		Kindly go back to Column Selection and select atleast ONE Column
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer">
				<a class="button-link" href="<?= $thispage ?>?page=managedepartment_csv">Back to Column Selection</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php 
	if ($enableUpdate)	{
?>				
				<input type="button" id="__add_record" value="Proceed"/>
<?php 
	}
?>
			</div>
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
	} else if ($page == "managedepartment_csv" && Authorize::isAllowable($config, "managedepartment_csv", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Department CSV File <br/>(Column Selection)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
						<input type="hidden" name="page" value="managedepartment_csv"/>
						<input type="hidden" name="sortable" value="true"/>
<?php
	$listOfAvailableColumnsInTables = Object::getUICSVTables(Department::getAvailableViewableColumns());
	echo $listOfAvailableColumnsInTables;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer">
				<input type="button" id="__add_record" value="Proceed"/>
			</div>
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
	} else if ($page == "managedepartment_delete" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managedepartment_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$department1 = null;
		try {
			 $department1 = new Department($database, $_REQUEST['id'], $conn);
			 $department1->commitDelete();
			 $enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason("You might be dealing with a Department which is no longer existing in the system or a None Empty Record");
			$promise1->setPromise(false);
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Department Removal Report</div>
			<div class="ui-sys-panel-pody ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			The Department <?= $department1->getDepartmentName() ?> has been successful removed from the system
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Removing the Department <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managedepartment_delete", "Deleted ".$department1->getDepartmentName());
	} else if ($page == "managedepartment_delete" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managedepartment_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to database services");
		$department1 = null;
		try {
			$department1 = new Department($database, $_REQUEST['id'], $conn);
			$department1->setExtraFilter(System::getCodeString(8));
			$department1->commitUpdate();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Delete Confirmation</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<div class="ui-sys-warning">
					You are about to remove a Department <b><?= $department1->getDepartmentName() ?></b> from the System <br/>
					<b>NOTE: This Operation Can not be reversed</b><br/>
					Are You Sure You want to remove the Department <?= $department1->getDepartmentName() ?> from the System?
				</div>
				<div class="ui-sys-center-text">
					<a class="button-link-warning" href="<?= $thispage ?>?page=managedepartment_delete&report=io&id=<?= $department1->getDepartmentId() ?>&rndx=<?= $department1->getExtraFilter() ?>">Yes (Remove)</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a class="button-link" href="<?= $thispage ?>?page=managedepartment">No (Cancel)</a>
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managedepartment_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managedepartment_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$department1 = null;
		try {
			$department1 = new Department($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$promise1 = new Promise();
		$promise1->setReason("Nothing Has been Updated");
		$enableUpdate = false;
		$departmentName = mysql_real_escape_string($_REQUEST['departmentName']);
		$departmentCode = mysql_real_escape_string($_REQUEST['departmentCode']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
		if ($departmentName != $department1->getDepartmentName())	{
			$department1->setDepartmentName($departmentName); $enableUpdate = true;
		}
		if ($departmentCode != $department1->getDepartmentCode())	{
			$department1->setDepartmentCode($departmentCode); $enableUpdate = true;
		}
		if ($extraInformation != $department1->getExtraInformation())	{
			$department1->setExtraInformation($extraInformation); $enableUpdate = true;
		}
		//This is the Last 
		if (trim($_REQUEST['rndx']) != $department1->getExtraFilter())	{
			$enableUpdate = false;
			$promise1->setReason("The System has Detected you are replaying data in your Browser window");
			$promise1->setPromise(false);
		}
		//Now proceed with general find a new value of extra Filter 
		$department1->setExtraFilter(System::getCodeString(8));
		if ($enableUpdate)	{
			try {
				$department1->commitUpdate();
				$promise1->setPromise(true);
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
				$enableUpdate = false;
			}
		} 
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Department Editing Report</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Department <?= $department1->getDepartmentName() ?> has been updated successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were no updates for the department <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managedepartment_edit", "Edited ".$department1->getDepartmentName());
	} else if ($page == "managedepartment_edit" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managedepartment_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$department1 = null;
		$extraFilter = System::getCodeString(8);
		try {
			$department1 = new Department($database, $_REQUEST['id'], $conn);
			$department1->setExtraFilter($extraFilter);
			$department1->commitUpdate();
		} catch (Exception $e)	{ die($e->getMessage()); }
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Edit Department <?= $department1->getDepartmentName() ?></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managedepartment_edit"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="id" value="<?= $department1->getDepartmentId() ?>"/>
					<input type="hidden" name="rndx" value="<?= $department1->getExtraFilter() ?>"/>
					<div class="pure-control-group">
						<label for="departmentName">Department Name </label>
						<input value="<?= $department1->getDepartmentName() ?>" type="text" name="departmentName" id="departmentName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Department Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="departmentCode">Department Code </label>
						<input value="<?= $department1->getDepartmentCode() ?>" type="text" name="departmentCode" id="departmentCode" size="24" required pattern="<?= $exprL8Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL8Name ?>" validate_message="Department Code : <?= $msgL8Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input value="<?= $department1->getExtraInformation() ?>" type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Edit Record"/>
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
	} else if ($page == "managedepartment_detail" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managedepartment_detail", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$department1 = null;
		try {
			$department1 = new Department($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{die($e->getMessage());}
		mysql_close($conn);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Details for <?= $department1->getDepartmentName() ?></div>
		<div class="ui-sys-panel-content ui-sys-data-capture">
			<table class="pure-table">
				<thead>
					<tr>
						<th>Field Name</th>
						<th>Field Value</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Department Name</td>
						<td><?= $department1->getDepartmentName() ?></td>
					</tr>
					<tr>
						<td>Department Code</td>
						<td><?= $department1->getDepartmentCode() ?></td>
					</tr>
<?php 
	if (! is_null($department1->getExtraInformation()))	{
?>
					<tr>
						<td>Extra Information</td>
						<td><?= $department1->getExtraInformation() ?></td>
					</tr>
<?php
	}
?>
				</tbody>
			</table>
		</div>
		<div class="ui-sys-panel-footer">
			<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "managedepartment_edit", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Edit <?= $department1->getDepartmentName() ?>" href="<?= $thispage ?>?page=managedepartment_edit&id=<?= $department1->getDepartmentId() ?>"><img alt="DAT" src="../sysimage/buttonedit.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "managedepartment_delete", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Delete <?= $department1->getDepartmentName() ?>" href="<?= $thispage ?>?page=managedepartment_delete&id=<?= $department1->getDepartmentId() ?>"><img alt="DAT" src="../sysimage/buttondelete.png"/></a>
<?php
	}
?>
				<span class="ui-sys-clear-both">&nbsp;</span><br />
			</div>
		</div>
	</div>
<?php
	} else if ($page == "managedepartment_add" && isset($_REQUEST['report']) && Authorize::isAllowable($config, "managedepartment_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$departmentName = mysql_real_escape_string($_REQUEST['departmentName']);
		$departmentCode = mysql_real_escape_string($_REQUEST['departmentCode']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel header ui-widget-header">Summary Report for <i><?= $departmentName ?></i></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	$extraFilter = System::getCodeString(8);
	$query="INSERT INTO departments (departmentName, departmentCode, extraFilter, extraInformation) VALUES('$departmentName', '$departmentCode', '$extraFilter', '$extraInformation')";
	try {
		mysql_db_query($database, $query, $conn) or Object::shootException("Department[Adding]: Could not Add a Record into the System");
		$promise1->setPromise(true);
		$enableUpdate = true;
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">The Department <?= $departmentName ?>, has successful Added to the system</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Adding the department<br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managedepartment_add", "Added $departmentName");
	} else if ($page == "managedepartment_add" && Authorize::isAllowable($config, "managedepartment_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Add a New Department</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managedepartment_add"/>
					<input type="hidden" name="report" value="io"/>
					<div class="pure-control-group">
						<label for="departmentName">Department Name </label>
						<input type="text" name="departmentName" id="departmentName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Department Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="departmentCode">Department Code </label>
						<input type="text" name="departmentCode" id="departmentCode" size="24" required pattern="<?= $exprL8Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL8Name ?>" validate_message="Department Code : <?= $msgL8Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Add Record"/>
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
	} else if ($page == "managedepartment" && Authorize::isAllowable($config, "managedepartment", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Departments Management</div>
			<div class="ui-sys-panel-body">
<!--Block ONE controls for searching-->
				<div class="ui-sys-search-container pure-form pure-group-control">
					<input type="text" title="At Least Three Characters should be supplied" required placeholder="ABC" size="32"/>
					<a title="Click to Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=managedepartment" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
				</div>
<!--Block ONE ends-->	
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
		//saving Authorization Rules 
		$blnAuthorizationDetail = Authorize::isAllowable($config, "managedepartment_detail", "normal", "donotsetlog", "-1", "-1");
?>
<!--Block Two, Search Results-->
<div class="ui-sys-search-results">
	<label id="statustextlabel"></label>
	<table class="pure-table ui-sys-table-search-results">
		<thead>
			<tr>
				<th>S/N</th>
				<th>Department Name</th>
				<th>Department Code</th>
				<th></th>
			</tr>
		</thead>
<?php 
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
	$query = Department::getQueryText();
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$result = mysql_db_query($database, $query, $conn) or die("Could not fetch department data from the storage");
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	while (list($id) = mysql_fetch_row($result))	{
		if ($sqlReturnedRowCount > $maximumReturnedRows)	{
			//Finalize All House Keeping Here
			break;
		}
		$department1 = null;
		try {
			$department1 = new Department($database, $id, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated per objects 
		if ($department1->searchMatrix($matrix1)->evaluateResult())	{
			if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
				$numberOfTBodies++;
				$tbodyClosed = false;
			} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
				$numberOfTBodies++;
				$perTbodyCounter = 0;
			}
			//In All Cases we need to display tr
			$pureTableOddClass = "";
			if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
			<tr class="<?= $pureTableOddClass ?>">
				<td><?= $sqlReturnedRowCount + 1 ?></td>
				<td><?= $department1->getDepartmentName() ?></td>
				<td><?= $department1->getDepartmentCode() ?></td>
				<td>
<?php 
	if ($blnAuthorizationDetail)	{
?>
		<a href="<?= $thispage ?>?page=managedepartment_detail&id=<?= $department1->getDepartmentId() ?>" class="ui-sys-search-results-controls"><img alt="DAT" src="../sysimage/buttondetail.png"/></a>
<?php
	}
?>				
				</td>
			</tr>
<?php
			$perTbodyCounter++;
			$sqlReturnedRowCount++;
		}//end-evaluate-results
	}//end-while
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
	mysql_close($conn);
?>
	</table>
	<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
	<ul class="ui-sys-pagination"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
</div>
<!--Block Two Ends-->
<?php
	} //end-if-searchtext
?>		
<!--Beginning of Block Three-->
				<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "managedepartment_csv", "normal", "donotsetlog", "-1", "-1")) {
?>
		<a title="Download CSV/EXCEL Formatted Data" href="<?= $thispage ?>?page=managedepartment_csv"><img alt="DAT" src="../sysimage/buttoncsv.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "managedepartment_add", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Add a New System Department" href="<?= $thispage ?>?page=managedepartment_add"><img alt="DAT" src="../sysimage/buttonadd.png"/></a>
<?php	
	}
?>				
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	}/*Department End*/ else/*CourseInstructor BEGIN*/if ($page == "managecourseinstructor_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['detach_courseAndSubjectTransaction']) && Authorize::isAllowable($config, "managecourseinstructor_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$instructor1 = null;
		$ulogin1 = null;
		$headerText = "UNKNOWN";
		try {
			$instructor1 = new CourseInstructor($database, $_REQUEST['id'], $conn);
			$ulogin1 = $instructor1->getLogin();
			$headerText = "De-Assign Course & Subject Map from ".$ulogin1->getFullname();
			if (! isset($_REQUEST['did'])) Object::shootException("There were nothing to Attach");
			$listToUpdate = Object::detachIdListFromObjectList($instructor1->getCourseAndSubjectTransactionList(), $_REQUEST['did']);
			$instructor1->setCourseAndSubjectTransactionList($listToUpdate);
			$instructor1->commitUpdate();
			$enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header"></div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Course and Subject List Attachment were removed successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in De-Attaching Course and Subject List <br/>
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
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managecourseinstructor_edit", "Detach Course & Subjects for ".$ulogin1->getFullname());
	} else if ($page == "managecourseinstructor_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['attach_courseAndSubjectTransaction']) && Authorize::isAllowable($config, "managecourseinstructor_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$instructor1 = null;
		$ulogin1 = null;
		$headerText = "UNKNOWN";
		try {
			$instructor1 = new CourseInstructor($database, $_REQUEST['id'], $conn);
			$ulogin1 = $instructor1->getLogin();
			$headerText = "Assign Course & Subject Map to ".$ulogin1->getFullname();
			if (! isset($_REQUEST['did'])) Object::shootException("There were nothing to Attach");
			$listToUpdate = Object::attachIdListToObjectList($instructor1->getCourseAndSubjectTransactionList(), $_REQUEST['did']);
			$instructor1->setCourseAndSubjectTransactionList($listToUpdate);
			$instructor1->commitUpdate();
			$enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header"></div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Course and Subject List Attachment were done successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Attaching Course and Subject List <br/>
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
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managecourseinstructor_edit", "Attach Course & Subjects for ".$ulogin1->getFullname());
	} else if ($page == "managecourseinstructor_edit" && isset($_REQUEST['id']) && isset($_REQUEST['attach_courseAndSubjectTransaction']) && Authorize::isAllowable($config, "managecourseinstructor_edit", "normal", "setlog", "-1", "-1")) {
		$conn  = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$instructor1 = null;
		$ulogin1 = null;
		$headerText = "UNKNOWN";
		try {
			$instructor1 = new CourseInstructor($database, $_REQUEST['id'], $conn);
			$ulogin1 = $instructor1->getLogin();
			$headerText = "Assign Course & Subject Map to ".$ulogin1->getFullname();
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header"><?= $headerText ?></div>
			<div class="ui-sys-panel-body ui-sys-search-results">
<?php 
	if ($promise1->isPromising())	{
/*Begin of BLOCK ONE */
		if (! is_null($instructor1->getCourseAndSubjectTransactionList()) && sizeof($instructor1->getCourseAndSubjectTransactionList()) > 0)	{
			$objectList1 = $instructor1->getCourseAndSubjectTransactionList();
?>
			<form method="POST" action="<?= $thispage ?>">
				<input type="hidden" name="page" value="managecourseinstructor_edit"/>
				<input type="hidden" name="id" value="<?= $instructor1->getInstructorId() ?>"/>
				<input type="hidden" name="report" value="true"/>
				<input type="hidden" name="detach_courseAndSubjectTransaction" value="true"/>
				<table class="pure-table pure-table-aligned ui-sys-search-results-1">
					<thead>
						<tr>
							<th colspan="8">Course & Subjects Attached To</th>
						</tr>
						<tr>
							<th></th>
							<th></th>
							<th>Map Name</th>
							<th>Course</th>
							<th>Subject/Module</th>
							<th>Year</th>
							<th>Semester</th>
							<th>Status</th>
						</tr>
					</thead>
<?php 
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	foreach ($objectList1 as $obj1)	{
		if (is_null($obj1)) continue;
		if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
			$numberOfTBodies++;
			$tbodyClosed = false;
		} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
			$numberOfTBodies++;
			$perTbodyCounter = 0;
		}
		//In All Cases we need to display tr
		$pureTableOddClass = "";
		if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
		<tr class="<?= $pureTableOddClass ?>">
			<td><input type="checkbox" name="did[<?= $sqlReturnedRowCount ?>]" value="<?= $obj1->getTransactionId() ?>"/></td>
			<td><?= $sqlReturnedRowCount + 1 ?></td>
			<td><?= $obj1->getExtraInformation() ?></td>
			<td>
<?php 
	if (! is_null($obj1->getCourse()))	{
?>
		<?= $obj1->getCourse()->getCourseName() ?>
<?php
	}
?>			
			</td>
			<td>
<?php 
	if (! is_null($obj1->getSubject()))	{
?>
		<?= $obj1->getSubject()->getSubjectName() ?>
<?php
	}
?>			
			</td>
			<td><?= $obj1->getYear() ?></td>
			<td><?= $obj1->getSemester() ?></td>
			<td>
<?php 
	$compulsoryText="Optional";
	if ($obj1->isCompulsory())	$compulsoryText="Compulsory";
	echo $compulsoryText;
?>			
			</td>
		</tr>
<?php
		$perTbodyCounter++;
		$sqlReturnedRowCount++;
	}
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
?>
			</table>
			<div class="ui-sys-right">
				<input type="submit" value="Detach Course & Subject Mapping"/>
			</div>
		</form>
		<ul class="ui-sys-pagination-1"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination-1').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results-1 tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>

<?php
		}
/*End of BLOCK ONE*/
/*Begin of BLOCK TWO -- Extra*/
?>
	<div style="padding: 1px; border: 1px gold dotted;">
		<!--Content of Block Two Extra BEgins-->
		<!--Block ONE controls for searching-->
				<div class="ui-sys-search-container pure-form pure-transaction-control">
					<input type="text" title="At Least Three Characters should be supplied" required placeholder="ABC" size="32"/>
					<a title="Click to Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=managecourseinstructor_edit&id=<?= $instructor1->getInstructorId() ?>&attach_courseAndSubjectTransaction=true" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
				</div>
<!--Block ONE ends-->	
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
		//saving Authorization Rules 
?>
<!--Block Two, Search Results-->
<div class="ui-sys-search-results">
	<label id="statustextlabel"></label>
	<form method="POST" action="<?= $thispage ?>">
		<input type="hidden" name="page" value="managecourseinstructor_edit"/>
		<input type="hidden" name="id" value="<?= $instructor1->getInstructorId() ?>"/>
		<input type="hidden" name="report" value="true"/>
		<input type="hidden" name="attach_courseAndSubjectTransaction" value="true"/>
		<table class="pure-table ui-sys-table-search-results-2">
			<thead>
				<tr>
					<th></th>
					<th>S/N</th>
					<th>Map Name</th>
					<th>Course</th>
					<th>Subject/Module</th>
					<th>Year</th>
					<th>Semester</th>
					<th>Status</th>
				</tr>
			</thead>
<?php 
	$query = CourseAndSubjectTransaction::getQueryText();
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$result = mysql_db_query($database, $query, $conn) or die("Could not fetch transaction data from the storage");
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	while (list($id) = mysql_fetch_row($result))	{
		if ($sqlReturnedRowCount > $maximumReturnedRows)	{
			//Finalize All House Keeping Here
			break;
		}
		$transaction1 = null;
		try {
			$transaction1 = new CourseAndSubjectTransaction($database, $id, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated per objects 
		if ($transaction1->searchMatrix($matrix1)->evaluateResult())	{
			if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
				$numberOfTBodies++;
				$tbodyClosed = false;
			} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
				$numberOfTBodies++;
				$perTbodyCounter = 0;
			}
			//In All Cases we need to display tr
			$pureTableOddClass = "";
			if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
			<tr class="<?= $pureTableOddClass ?>">
				<td><input type="checkbox" name="did[<?= $sqlReturnedRowCount ?>]" value="<?= $transaction1->getTransactionId() ?>"/></td>
				<td><?= $sqlReturnedRowCount + 1 ?></td>
				<td><?= $transaction1->getExtraInformation() ?></td>
				<td>
<?php 
	if (! is_null($transaction1->getCourse()))	{
?>
		<?= $transaction1->getCourse()->getCourseName() ?>
<?php
	}
?>				
				</td>
				<td>
<?php 
	if (! is_null($transaction1->getSubject()))	{
?>
		<?= $transaction1->getSubject()->getSubjectName() ?>
<?php
	}
?>				
				</td>
				<td><?= $transaction1->getYear() ?></td>
				<td><?= $transaction1->getSemester() ?></td>
				<td>
<?php 
	$compulsoryText = "Optional";
	if ($transaction1->isCompulsory())	{
		$compulsoryText = "Compulsory";
	}
?>					<?= $compulsoryText ?>
				</td>
			</tr>
<?php
			$perTbodyCounter++;
			$sqlReturnedRowCount++;
		}//end-evaluate-results
	}//end-while
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
?>
		</table>
		<div class="ui-sys-right">
			<input type="submit" value="Attach Course & Subject Mapping"/>
		</div>
	</form>
	<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
	<ul class="ui-sys-pagination-2"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination-2').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results-2 tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
</div>
<!--Block Two Ends-->
<?php
	} //end-if-searchtext
?>		
		<!--Content of Block Two Extra Ends-->
	</div>
<?php
/*End of BLOCK TWO -- Extra*/
	} else {
?>
		<div class="ui-state-error">
			There were problems in the initial setup of Assignment <br/>
			Reason : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managecourseinstructor_csv" && isset($_REQUEST['report']) && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managecourseinstructor_csv", "normal", "setlog", "-1", "-1"))	{
		$filename=$_REQUEST['report'];
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">CourseInstructor CSV File<br/> (Download Report)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (unlink($filename))	{
?>
		<div class="ui-state-highlight">
			The temporary file has been removed succesful from the Server
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problem on removing the temporary file from the Server
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	} else if ($page == "managecourseinstructor_csv" && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managecourseinstructor_csv", "normal", "setlog", "-1", "-1"))	{
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$promise1 = new Promise();
		$promise1->setPromise(true);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">CourseInstructor CSV File <br/> (Downlad CSV File)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	$filename="";
	$tfname=$filename.$date;
	$tfname=$tfname.rand(100,999);
	$filename=sha1($tfname);
	$filename=$filename.".csv";
	$filename="../temp/".$filename;
	$searchText = Object::getSearchTextFromUserFieldData($_REQUEST['controlIndex'], $_REQUEST['fieldname'], $_REQUEST['op'], $_REQUEST['namespaceTag'], $_REQUEST['fieldvalue']);
	$lineCounter = 0;
	$file1 = fopen($filename, "w") or die("Could not Open the File for writing");
	$csvProcessor1 = null;
	try {
		$csvProcessor1 = new CSVProcessor($searchText, $file1);
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$primise1->setPromise(false);
	}
	$result = null;
	if ($promise1->isPromising())	{
		$query = CourseInstructor::getQueryText();
		try {
			$result = mysql_db_query($database, $query, $conn) or Object::shootException("CourseInstructor[CSV]: Could not Execute Query");
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$primise1->setPromise(false);
		}
	}
	if ($promise1->isPromising())	{
		while (list($id) = mysql_fetch_row($result))	{
			$instructor1 = null;
			try	{
				$instructor1 = new CourseInstructor($database, $id, $conn);
			} catch (Exception $e)	{ die($e->getMessage()); }
			$csvProcessor1->reload(); //Clear the Previous Object Properties 
			if ($instructor1->processCSV($csvProcessor1)->evaluateResult())	{
				$lineCounter++;
			}
		}//end-while 
	}//end-if-promising
	fclose($file1);
	if ($promise1->isPromising())	{
?>
		<div>
			Your file is Ready for downloading<br/>
			Number data rows it contains is <?= $lineCounter ?><br/><br/>
			<b style="font-size: 1.1em;"><a href="<?= $filename ?>">Click Here</a></b> to download your file<br/><br/>
			Once the download is complete, kindly click the proceed button 
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were problems in generating your file <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>					
			</div>
			<div class="ui-sys-panel-footer">
				<div id="perror" class="ui-sys-error-message"></div>
<?php 
	if ($promise1->isPromising())	{
		if ($lineCounter > 0)	{
?>
			<a id="__id_csv_previewer" data-error-control="perror" data-server-forward-path="../server/getcsvpreviewer.php" data-file-to-read="<?= $filename ?>" data-dialog-container="__id_general_dialog_holder" class="button-link">Preview</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php
		}
?>
		<a class="button-link" href="<?= $thispage ?>?page=managecourseinstructor_csv&report=<?= $filename ?>&downloadable=true">Continue</a>
<?php
	}
?>			
			</div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managecourseinstructor_csv" && isset($_REQUEST['sortable']) && Authorize::isAllowable($config, "managecourseinstructor_csv", "normal", "setlog", "-1", "-1"))	{
		$enableUpdate = false;
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">CourseInstructor CSV File <br/> (Column Sorting)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (isset($_REQUEST['fieldname']) && isset($_REQUEST['fieldtype']) && isset($_REQUEST['namespaceTag']) && isset($_REQUEST['fieldvalue']))	{
		$enableUpdate = true;
?>
				<form class="pure-form" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managecourseinstructor_csv"/>
					<input type="hidden" name="downloadable" value="true"/>
<?php 
	$sortableContainer = Object::getUICSVDivSortable(Object::getLineToSortFromUserFieldData($_REQUEST['fieldname'], $_REQUEST['fieldtype'], $_REQUEST['namespaceTag'],$_REQUEST['fieldvalue']));
	echo $sortableContainer;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
<?php 
	} else {
?>
		Perhaps you did not select any column to be included in your CSV File <br/>
		Kindly go back to Column Selection and select atleast ONE Column
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer">
				<a class="button-link" href="<?= $thispage ?>?page=managecourseinstructor_csv">Back to Column Selection</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php 
	if ($enableUpdate)	{
?>				
				<input type="button" id="__add_record" value="Proceed"/>
<?php 
	}
?>
			</div>
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
	} else if ($page == "managecourseinstructor_csv" && Authorize::isAllowable($config, "managecourseinstructor_csv", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">CourseInstructor CSV File <br/>(Column Selection)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
						<input type="hidden" name="page" value="managecourseinstructor_csv"/>
						<input type="hidden" name="sortable" value="true"/>
<?php
	$listOfAvailableColumnsInTables = Object::getUICSVTables(CourseInstructor::getAvailableViewableColumns());
	echo $listOfAvailableColumnsInTables;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer">
				<input type="button" id="__add_record" value="Proceed"/>
			</div>
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
	} else if ($page == "managecourseinstructor_delete" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managecourseinstructor_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$instructor1 = null;
		$ulogin1 = null;
		try {
			 $instructor1 = new CourseInstructor($database, $_REQUEST['id'], $conn);
			 $ulogin1 = $instructor1->getLogin();
			 $instructor1->commitDelete();
			 $ulogin1->commitDelete();
			 $enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason("You might be dealing with a CourseInstructor which is no longer existing in the system or a None Empty Record");
			$promise1->setPromise(false);
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">CourseInstructor Removal Report</div>
			<div class="ui-sys-panel-pody ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			The CourseInstructor <?= $ulogin1->getFullname() ?> has been successful removed from the system
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Removing the CourseInstructor <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managecourseinstructor_delete", "Deleted ".$ulogin1->getLoginName());
	} else if ($page == "managecourseinstructor_delete" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managecourseinstructor_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to database services");
		$instructor1 = null;
		$ulogin1 = null;
		try {
			$instructor1 = new CourseInstructor($database, $_REQUEST['id'], $conn);
			$ulogin1 = $instructor1->getLogin();
			$instructor1->setExtraFilter(System::getCodeString(8));
			$instructor1->commitUpdate();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Delete Confirmation</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<div class="ui-sys-warning">
					You are about to remove a CourseInstructor <b><?= $ulogin1->getFullname() ?></b> from the System <br/>
					<b>NOTE: This Operation Can not be reversed</b><br/>
					Are You Sure You want to remove the CourseInstructor <?= $ulogin1->getFullname() ?> from the System?
				</div>
				<div class="ui-sys-center-text">
					<a class="button-link-warning" href="<?= $thispage ?>?page=managecourseinstructor_delete&report=io&id=<?= $instructor1->getInstructorId() ?>&rndx=<?= $instructor1->getExtraFilter() ?>">Yes (Remove)</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a class="button-link" href="<?= $thispage ?>?page=managecourseinstructor">No (Cancel)</a>
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managecourseinstructor_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managecourseinstructor_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$instructor1 = null;
		$ulogin1 = null;
		try {
			$instructor1 = new CourseInstructor($database, $_REQUEST['id'], $conn);
			$ulogin1 = $instructor1->getLogin();
		} catch (Exception $e)	{ die($e->getMessage()); }
		$promise1 = new Promise();
		$promise1->setReason("Nothing Has been Updated");
		$enableUpdate = false;
		$statusId = mysql_real_escape_string($_REQUEST['statusId']);
		$departmentId = mysql_real_escape_string($_REQUEST['departmentId']);
		$jobId = mysql_real_escape_string($_REQUEST['jobId']);
		$groupId = mysql_real_escape_string($_REQUEST['groupId']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
		$isroot = 0;
		if (isset($_REQUEST['isroot'])) $isroot = 1;
		if (is_null($ulogin1->getUserStatus()) || ($statusId != $ulogin1->getUserStatus()->getStatusId()))	{
			$ulogin1->setCourseInstructorStatus($statusId); $enableUpdate = true;
		}
		if (is_null($ulogin1->getJobTitle()) || ($jobId != $ulogin1->getJobTitle()->getJobId()))	{
			$ulogin1->setJobTitle($jobId); $enableUpdate = true;
		}
		if (is_null($ulogin1->getGroup()) || ($groupId != $ulogin1->getGroup()->getGroupId()))	{
			$ulogin1->setGroup($groupId); $enableUpdate = true;
		}
		if ($extraInformation != $ulogin1->getExtraInformation())	{
			$ulogin1->setExtraInformation($extraInformation); $enableUpdate = true;
		}
		if (($isroot == 1) xor $ulogin1->isRoot())	{
			$ulogin1->setRoot($isroot); $enableUpdate = true;
		}
		if (is_null($instructor1->getDepartment()) || ($instructor1->getDepartment()->getDepartmentId() != $departmentId))	{
			$instructor1->setDepartment($departmentId); $enableUpdate = true;
		}
		//This is the Last 
		if (trim($_REQUEST['rndx']) != $instructor1->getExtraFilter())	{
			$enableUpdate = false;
			$promise1->setReason("The System has Detected you are replaying data in your Browser window");
			$promise1->setPromise(false);
		}
		//Now proceed with general find a new value of extra Filter 
		$instructor1->setExtraFilter(System::getCodeString(8));
		if ($enableUpdate)	{
			try {
				$instructor1->commitUpdate();
				$ulogin1->commitUpdate();
				$promise1->setPromise(true);
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
				$enableUpdate = false;
			}
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">CourseInstructor Editing Report</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			CourseInstructor <?= $ulogin1->getFullname() ?> has been updated successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were no updates for the instructor <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managecourseinstructor_edit", "Edited ".$ulogin1->getLoginName());
	} else if ($page == "managecourseinstructor_edit" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managecourseinstructor_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$instructor1 = null;
		$ulogin1 = null;
		$extraFilter = System::getCodeString(8);
		try {
			$instructor1 = new CourseInstructor($database, $_REQUEST['id'], $conn);
			$ulogin1 = $instructor1->getLogin();
			$instructor1->setExtraFilter($extraFilter);
			$instructor1->commitUpdate();
		} catch (Exception $e)	{ die($e->getMessage()); }
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Edit CourseInstructor <?= $ulogin1->getFullname() ?></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managecourseinstructor_edit"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="id" value="<?= $instructor1->getInstructorId() ?>"/>
					<input type="hidden" name="rndx" value="<?= $instructor1->getExtraFilter() ?>"/>
					<div class="pure-control-group">
						<label for="statusId">User Status </label>
						<select id="statusId" name="statusId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select User Status">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = UserStatus::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($ulogin1->getUserStatus()) && ($ulogin1->getUserStatus()->getStatusId() == $alist1['id'])) $selected="selected"; 
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="departmentId">Department </label>
						<select id="departmentId" name="departmentId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Department">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Department::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($instructor1->getDepartment()) && ($instructor1->getDepartment()->getDepartmentId() == $alist1['id'])) $selected="selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="jobId">Job Title </label>
						<select id="jobId" name="jobId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Job Title">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = JobTitle::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($ulogin1->getJobTitle()) && ($ulogin1->getJobTitle()->getJobId() == $alist1['id'])) $selected="selected"; 
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="groupId">Group </label>
						<select id="groupId" name="groupId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Group">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Group::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($ulogin1->getGroup()) && ($ulogin1->getGroup()->getGroupId() == $alist1['id'])) $selected="selected"; 
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
<?php 
	if ($login1->isRoot() && ($login1->getLoginId() != $ulogin1->getLoginId()))	{
		//Only those logged in as root instructors are allowed to change 
		//this status
		//And as usual you can not change this for yourself even if you are the root instructor
		$chkEnable = "";
		if ($ulogin1->isRoot()) $chkEnable = "checked";
?>
					<div class="pure-controls">
						<label><input type="checkbox" name="isroot" value="1" <?= $chkEnable ?>/>Enable/Disable System CourseInstructor Status</label>
					</div>
<?php
	}
?>
					
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input value="<?= $ulogin1->getExtraInformation() ?>" type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Edit Record"/>
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
	} else if ($page == "managecourseinstructor_detail" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managecourseinstructor_detail", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$instructor1 = null;
		$ulogin1 = null;
		try {
			$instructor1 = new CourseInstructor($database, $_REQUEST['id'], $conn);
			$ulogin1 = $instructor1->getLogin();
			$photoname = "default.png";
			if (! is_null($ulogin1->getSex()))	{
				if (strtolower($ulogin1->getSex()->getSexName()) == "male") $photoname="default_male.png";
				else if (strtolower($ulogin1->getSex()->getSexName()) == "female") $photoname="default_female.png";
			}
			if (! is_null($ulogin1->getPhoto())) $photoname = $ulogin1->getPhoto();
			$ulogin1->setExtraFilter("../data/instructors/photo/".$photoname);
		} catch (Exception $e)	{die($e->getMessage());}
		mysql_close($conn);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Details for <?= $ulogin1->getFullname() ?></div>
		<div class="ui-sys-panel-content ui-sys-data-capture">
			<table class="pure-table">
				<thead>
					<tr>
						<th>Field Name</th>
						<th>Field Value</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2">
							<div class="ui-sys-instructor-photo-detail"><img alt="PDET" src="<?= $ulogin1->getExtraFilter() ?>"/></div>
						</td>
					</tr>
					<tr>
						<td>Firstname</td>
						<td><?= $ulogin1->getFirstname() ?></td>
					</tr>
					<tr>
						<td>Middlename</td>
						<td><?= $ulogin1->getMiddlename() ?></td>
					</tr>
					<tr>
						<td>Lastname</td>
						<td><?= $ulogin1->getLastname() ?></td>
					</tr>
<?php 
	if (! is_null($instructor1->getDepartment()))	{
?>
					<tr>
						<td>Department</td>
						<td><?= $instructor1->getDepartment()->getDepartmentName() ?></td>
					</tr>
<?php
	}
	if (! is_null($ulogin1->getUserStatus()))	{
?>
					<tr>
						<td>Status</td>
						<td><?= $ulogin1->getUserStatus()->getStatusName() ?></td>
					</tr>
<?php		
	}
	if (! is_null($ulogin1->getDOB()))	{
?>
					<tr>
						<td>Date of Birth</td>
						<td><?= DateAndTime::convertFromDateTimeObjectToGUIDateFormat($ulogin1->getDOB()) ?></td>
					</tr>
<?php
	}
	if (! is_null($ulogin1->getSex()))	{
?>
					<tr>
						<td>Sex</td>
						<td><?= $ulogin1->getSex()->getSexName() ?></td>
					</tr>
<?php
	}
	if (! is_null($ulogin1->getMarital()))	{
?>
					<tr>
						<td>Marital</td>
						<td><?= $ulogin1->getMarital()->getMaritalName() ?></td>
					</tr>
<?php
	}
	if (! is_null($ulogin1->getUserType()))	{
?>
					<tr>
						<td>Type of User</td>
						<td><?= $ulogin1->getUserType()->getTypeName() ?></td>
					</tr>
<?php		
	}
	$rootCourseInstructorText = "Normal User";
	if ($ulogin1->isRoot()) $rootCourseInstructorText = "System User";
?>
					<tr>
						<td colspan="2">This is a <b><i><?= $rootCourseInstructorText ?></i></b></td>
					</tr>
					<tr>
						<td>Email</td>
						<td><?= $ulogin1->getEmail() ?></td>
					</tr>
					<tr>
						<td>Username </td>
						<td><?= $ulogin1->getLoginName() ?></td>
					</tr>
					<tr>
						<td>Phone</td>
						<td><?= $ulogin1->getPhone() ?></td>
					</tr>
<?php 
	if (! is_null($ulogin1->getJobTitle()))	{
?>
					<tr>
						<td>Job Title</td>
						<td><?= $ulogin1->getJobTitle()->getJobName() ?></td>
					</tr>
<?php
	}
	if (! is_null($ulogin1->getGroup()))	{
?>
					<tr>
						<td>Group</td>
						<td><?= $ulogin1->getGroup()->getGroupName() ?></td>
					</tr>
<?php
	}
	if (! is_null($ulogin1->getTheme()))	{
?>
					<tr>
						<td>CourseInstructor Theme</td>
						<td><?= $ulogin1->getTheme()->getThemeName() ?></td>
					</tr>
<?php		
	}
	if (! is_null($ulogin1->getFirstDayOfAWeek()))	{
?>
					<tr>
						<td>First Day of a Week</td>
						<td><?= $ulogin1->getFirstDayOfAWeek()->getDayName() ?></td>
					</tr>
<?php
	}
	if (! is_null($ulogin1->getAdmissionTime()))	{
?>
					<tr>
						<td>Time of Admission </td>
						<td><?= $ulogin1->getAdmissionTime()->getDateAndTimeString() ?></td>
					</tr>
<?php
	}
	if (! is_null($ulogin1->getLastLoginTime()))	{
?>
					<tr>
						<td>Time of Last Login </td>
						<td><?= $ulogin1->getLastLoginTime()->getDateAndTimeString() ?></td>
					</tr>
<?php
	}
?>
					<tr>
						<td>Extra Information</td>
						<td><?= $ulogin1->getExtraInformation() ?><br/><?= $instructor1->getExtraInformation() ?></td>
					</tr>
				</tbody>
			</table><br/>
<?php 
	if (! is_null($instructor1->getCourseAndSubjectTransactionList()) && sizeof($instructor1->getCourseAndSubjectTransactionList()) > 0)	{
		$objectList1 = $instructor1->getCourseAndSubjectTransactionList();
?>
			<table class="pure-table pure-table-aligned ui-sys-search-results">
				<thead>
					<tr>
						<th colspan="7">Course & Subjects Attached To</th>
					</tr>
					<tr>
						<th></th>
						<th>Map Name</th>
						<th>Course</th>
						<th>Subject/Module</th>
						<th>Year</th>
						<th>Semester</th>
						<th>Status</th>
					</tr>
				</thead>
<?php 
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	foreach ($objectList1 as $obj1)	{
		if (is_null($obj1)) continue;
		if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
			$numberOfTBodies++;
			$tbodyClosed = false;
		} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
			$numberOfTBodies++;
			$perTbodyCounter = 0;
		}
		//In All Cases we need to display tr
		$pureTableOddClass = "";
		if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
		<tr class="<?= $pureTableOddClass ?>">
			<td><?= $sqlReturnedRowCount + 1 ?></td>
			<td><?= $obj1->getExtraInformation() ?></td>
			<td>
<?php 
	if (! is_null($obj1->getCourse()))	{
?>
		<?= $obj1->getCourse()->getCourseName() ?>
<?php
	}
?>			
			</td>
			<td>
<?php 
	if (! is_null($obj1->getSubject()))	{
?>
		<?= $obj1->getSubject()->getSubjectName() ?>
<?php
	}
?>			
			</td>
			<td><?= $obj1->getYear() ?></td>
			<td><?= $obj1->getSemester() ?></td>
			<td>
<?php 
	$compulsoryText="Optional";
	if ($obj1->isCompulsory())	$compulsoryText="Compulsory";
	echo $compulsoryText;
?>			
			</td>
		</tr>
<?php
		$perTbodyCounter++;
		$sqlReturnedRowCount++;
	}
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
?>
			</table>
			<ul class="ui-sys-pagination"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>

<?php
	}
?>
		</div>
		<div class="ui-sys-panel-footer">
			<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Login::shouldAllowUpdateControls($login1, $ulogin1) && Authorize::isAllowable($config, "managecourseinstructor_edit", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Edit <?= $ulogin1->getFullname() ?>" href="<?= $thispage ?>?page=managecourseinstructor_edit&id=<?= $instructor1->getInstructorId() ?>"><img alt="DAT" src="../sysimage/buttonedit.png"/></a>
<?php
	}
	if (Login::shouldAllowUpdateControls($login1, $ulogin1) && Authorize::isAllowable($config, "managecourseinstructor_delete", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Delete <?= $ulogin1->getFullname() ?>" href="<?= $thispage ?>?page=managecourseinstructor_delete&id=<?= $instructor1->getInstructorId() ?>"><img alt="DAT" src="../sysimage/buttondelete.png"/></a>
<?php
	}
	if (Login::shouldAllowUpdateControls($login1, $ulogin1) && Authorize::isAllowable($config, "managecourseinstructor_edit", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Attach Course and Subject Mappings" href="<?= $thispage ?>?page=managecourseinstructor_edit&id=<?= $instructor1->getInstructorId() ?>&attach_courseAndSubjectTransaction=true"><img alt="DAT" src="../sysimage/buttonattach.png"/></a>
<?php
	}
	if (Login::shouldAllowUpdateControls($login1, $ulogin1) && Authorize::isAllowable($config, "managesystemfirewall", "normal", "donsetlog", "-1", "-1"))	{
?>
		<a title="Set System Firewall for <?= $ulogin1->getFullname() ?>" href="<?= $thispage ?>?page=managesystemfirewall&type=login&id=<?= $ulogin1->getLoginId() ?>"><img alt="DAT" src="../sysimage/buttonfirewall.png"/></a>
<?php	
	}
	if (Login::shouldAllowUpdateControls($login1, $ulogin1) && Authorize::isAllowable($config, "managesystemfirewall_graph", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="View Authorization Graph for <?= $ulogin1->getFullname() ?>" href="<?= $thispage ?>?page=managesystemfirewall_graph&type=login&id=<?= $ulogin1->getLoginId() ?>"><img alt="DAT" src="../sysimage/buttongraph.png"/></a>
<?php	
	}
?>
				<span class="ui-sys-clear-both">&nbsp;</span><br />
			</div>
		</div>
	</div>
<?php
	} else if ($page == "managecourseinstructor_add" && isset($_REQUEST['report']) && Authorize::isAllowable($config, "managecourseinstructor_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$firstname = mysql_real_escape_string($_REQUEST['firstname']);
		$middlename = mysql_real_escape_string($_REQUEST['middlename']);
		$lastname = mysql_real_escape_string($_REQUEST['lastname']);
		$fullname = mysql_real_escape_string($_REQUEST['fullname']);
		$departmentId = mysql_real_escape_string($_REQUEST['departmentId']);
		$dob = DateAndTime::convertFromGUIDateFormatToSystemDateAndTimeFormat(mysql_real_escape_string($_REQUEST['dob']));
		$sexId = mysql_real_escape_string($_REQUEST['sexId']);
		$maritalId = mysql_real_escape_string($_REQUEST['maritalId']);
		$jobId = mysql_real_escape_string($_REQUEST['jobId']);
		$groupId = mysql_real_escape_string($_REQUEST['groupId']);
		$email = mysql_real_escape_string($_REQUEST['email']);
		$phone = mysql_real_escape_string($_REQUEST['phone']);
		$loginName = mysql_real_escape_string($_REQUEST['loginName']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel header ui-widget-header">Summary Report for <i><?= $fullname ?></i></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	$extraFilter = System::getCodeString(8);
	$ulogin1 = null;
	try {
		$loginId = Login::initializeLoginRecordForCourseInstructor($database, $conn, $loginName);
		$query="INSERT INTO courseInstructors (loginId, departmentId, extraFilter, extraInformation) VALUES('$loginId', '$departmentId', '$extraFilter', '$extraInformation')";
		mysql_db_query($database, $query, $conn) or Object::shootException("CourseInstructor[Adding]: Could not Add a Record into the System");
		$ulogin1 = new Login($database, $loginId, $conn);
		/*Proceed on Updating*/
		$ulogin1->setFirstname($firstname);
		$ulogin1->setMiddlename($middlename);
		$ulogin1->setLastname($lastname);
		$ulogin1->setFullname($fullname);
		$ulogin1->setDOB($dob);
		$ulogin1->setSex($sexId);
		$ulogin1->setMarital($maritalId);
		$ulogin1->setJobTitle($jobId);
		$ulogin1->setGroup($groupId);
		$ulogin1->setEmail($email);
		$ulogin1->setPhone($phone);
		//instructorname/loginName is alredy set during initialization 
		$ulogin1->setAdmitted("1");
		$ulogin1->setUsingDefaultPassword("1");
		$ulogin1->setPassword(sha1(Object::$defaultPassword));
		$ulogin1->setAdmissionTime($systemDate1->getDateAndTimeString());
		$ulogin1->commitUpdate();
		/*Ending Update*/
		$promise1->setPromise(true);
		$enableUpdate = true;
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
		$enableUpdate = false;
	}
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">The Course Instructor <?= $ulogin1->getFullname() ?>, has successful Added to the system<br/>
			<br/>
			This Course Instructor is using a default password <b><?= Object::$defaultPassword ?></b>
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Adding the Course Instructor<br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managecourseinstructor_add", "Added $loginName");
	} else if ($page == "managecourseinstructor_add" && Authorize::isAllowable($config, "managecourseinstructor_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Add a New CourseInstructor</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managecourseinstructor_add"/>
					<input type="hidden" name="report" value="io"/>
					<div class="pure-control-group">
						<label for="firstname">Firstname </label>
						<input type="text" name="firstname" id="firstname" size="48" required pattern="<?= $exprA32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Firstname : <?= $msgA32Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="middlename">Middlename </label>
						<input type="text" name="middlename" id="middlename" size="48" required pattern="<?= $exprA32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Middlename : <?= $msgA32Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="lastname">Lastname </label>
						<input type="text" name="lastname" id="lastname" size="48" required pattern="<?= $exprA32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Lastname : <?= $msgA32Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="fullname">Full Name </label>
						<input type="text" name="fullname" id="fullname" size="48" required pattern="<?= $exprA64Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA64Name ?>" validate_message="Accademic Name : <?= $msgA64Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="departmentId">Department </label>
						<select id="departmentId" name="departmentId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Department">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Department::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
?>
			<option value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="dob">Date of Birth </label>
						<input class="datepicker" type="text" name="dob" id="dob" size="48" required pattern="<?= $exprDate ?>" validate="true" validate_control="text" validate_expression="<?= $exprDate ?>" validate_message="Date: <?= $msgDate ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="sexId">Sex </label>
						<select id="sexId" name="sexId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Sex">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Sex::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
?>
			<option value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
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
?>
			<option value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="phone">Phone/Telephone </label>
						<input type="text" name="phone" id="phone" size="32" required pattern="<?= $exprPhone ?>" validate="true" validate_control="text" validate_expression="<?= $exprPhone ?>" validate_message="Phone/Telephone : <?= $msgPhone ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="email">Email Address</label>
						<input type="text" name="email" id="email" size="48" required pattern="<?= $exprEmail ?>" validate="true" validate_control="text" validate_expression="<?= $exprEmail ?>%<?= $exprL32Name ?>" validate_message="Email Format : <?= $msgEmail ?>%Email Length: <?= $msgL32Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="loginName">User Name </label>
						<input type="text" name="loginName" id="loginName" size="48" required pattern="<?= $exprA32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="CourseInstructorname : <?= $msgA32Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="jobId">Job Title </label>
						<select id="jobId" name="jobId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Job Title">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = JobTitle::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
?>
			<option value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="groupId">Group </label>
						<select id="groupId" name="groupId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Group">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Group::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
?>
			<option value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Add Record"/>
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
		var $loginName1 = $('#loginName');
		if (! $loginName1.length) return;
		var $email1 = $('#email');
		if (! $email1.length) return;
		$target1.empty();
		$.ajax({
			url: "../server/service_username_email_availability.php",
			method: "POST",
			data: { param1: $loginName1.val(), param2: $email1.val() },
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
	} else if ($page == "managecourseinstructor" && Authorize::isAllowable($config, "managecourseinstructor", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">CourseInstructors Management</div>
			<div class="ui-sys-panel-body">
<!--Block ONE controls for searching-->
				<div class="ui-sys-search-container pure-form pure-instructor-control">
					<input type="text" title="At Least Three Characters should be supplied" required placeholder="ABC" size="32"/>
					<a title="Click to Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=managecourseinstructor" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
				</div>
<!--Block ONE ends-->	
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
		//saving Authorization Rules 
		$blnAuthorizationDetail = Authorize::isAllowable($config, "managecourseinstructor_detail", "normal", "donotsetlog", "-1", "-1");
?>
<!--Block Two, Search Results-->
<div class="ui-sys-search-results">
	<label id="statustextlabel"></label>
	<table class="pure-table ui-sys-table-search-results">
		<thead>
			<tr>
				<th>S/N</th>
				<th>Name</th>
				<th>Department</th>
				<th>Status</th>
				<th>Sex</th>
				<th>Email</th>
				<th>Phone</th>
				<th></th>
			</tr>
		</thead>
<?php 
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
	$query = CourseInstructor::getQueryText();
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$result = mysql_db_query($database, $query, $conn) or die("Could not fetch instructor data from the storage");
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	while (list($id) = mysql_fetch_row($result))	{
		if ($sqlReturnedRowCount > $maximumReturnedRows)	{
			//Finalize All House Keeping Here
			break;
		}
		$instructor1 = null;
		$ulogin1 = null;
		try {
			$instructor1 = new CourseInstructor($database, $id, $conn);
			$ulogin1 = $instructor1->getLogin();
		} catch (Exception $e)	{ die($e->getMessage()); }
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated per objects 
		if ($instructor1->searchMatrix($matrix1)->evaluateResult())	{
			if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
				$numberOfTBodies++;
				$tbodyClosed = false;
			} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
				$numberOfTBodies++;
				$perTbodyCounter = 0;
			}
			//In All Cases we need to display tr
			$pureTableOddClass = "";
			if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
			<tr class="<?= $pureTableOddClass ?>">
				<td><?= $sqlReturnedRowCount + 1 ?></td>
				<td><?= $ulogin1->getFullname() ?></td>
				<td>
<?php 
	if (! is_null($instructor1->getDepartment()))	{
?>
					<?= $instructor1->getDepartment()->getDepartmentName() ?>
<?php
	}
?>				
				</td>
				<td>
<?php 
	if (! is_null($ulogin1->getUserStatus()))	{
?>
		<?= $ulogin1->getUserStatus()->getStatusName() ?>
<?php
	}
?>				
				</td>
				<td>
<?php 
	if (! is_null($ulogin1->getSex()))	{
?>
		<?= $ulogin1->getSex()->getSexName() ?>
<?php
	}
?>				
				</td>
				<td><?= $ulogin1->getEmail() ?></td>
				<td><?= $ulogin1->getPhone() ?></td>
				<td>
<?php 
	if ($blnAuthorizationDetail)	{
?>
		<a href="<?= $thispage ?>?page=managecourseinstructor_detail&id=<?= $instructor1->getInstructorId() ?>" class="ui-sys-search-results-controls"><img alt="DAT" src="../sysimage/buttondetail.png"/></a>
<?php
	}
?>				
				</td>
			</tr>
<?php
			$perTbodyCounter++;
			$sqlReturnedRowCount++;
		}//end-evaluate-results
	}//end-while
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
	mysql_close($conn);
?>
	</table>
	<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
	<ul class="ui-sys-pagination"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
</div>
<!--Block Two Ends-->
<?php
	} //end-if-searchtext
?>		
<!--Beginning of Block Three-->
				<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "managecourseinstructor_csv", "normal", "donotsetlog", "-1", "-1")) {
?>
		<a title="Download CSV/EXCEL Formatted Data" href="<?= $thispage ?>?page=managecourseinstructor_csv"><img alt="DAT" src="../sysimage/buttoncsv.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "managecourseinstructor_add", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Add a New System CourseInstructor" href="<?= $thispage ?>?page=managecourseinstructor_add"><img alt="DAT" src="../sysimage/buttonadd.png"/></a>
<?php	
	}
?>				
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	}/*CourseInstructor End*/ else/*User BEGIN*/if ($page == "manageuser_csv" && isset($_REQUEST['report']) && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "manageuser_csv", "normal", "setlog", "-1", "-1"))	{
		$filename=$_REQUEST['report'];
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">User CSV File<br/> (Download Report)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (unlink($filename))	{
?>
		<div class="ui-state-highlight">
			The temporary file has been removed succesful from the Server
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problem on removing the temporary file from the Server
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	} else if ($page == "manageuser_csv" && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "manageuser_csv", "normal", "setlog", "-1", "-1"))	{
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$promise1 = new Promise();
		$promise1->setPromise(true);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">User CSV File <br/> (Downlad CSV File)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	$filename="";
	$tfname=$filename.$date;
	$tfname=$tfname.rand(100,999);
	$filename=sha1($tfname);
	$filename=$filename.".csv";
	$filename="../temp/".$filename;
	$searchText = Object::getSearchTextFromUserFieldData($_REQUEST['controlIndex'], $_REQUEST['fieldname'], $_REQUEST['op'], $_REQUEST['namespaceTag'], $_REQUEST['fieldvalue']);
	$lineCounter = 0;
	$file1 = fopen($filename, "w") or die("Could not Open the File for writing");
	$csvProcessor1 = null;
	try {
		$csvProcessor1 = new CSVProcessor($searchText, $file1);
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$primise1->setPromise(false);
	}
	$result = null;
	if ($promise1->isPromising())	{
		$query = User::getQueryText();
		try {
			$result = mysql_db_query($database, $query, $conn) or Object::shootException("User[CSV]: Could not Execute Query");
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$primise1->setPromise(false);
		}
	}
	if ($promise1->isPromising())	{
		while (list($id) = mysql_fetch_row($result))	{
			$user1 = null;
			try	{
				$user1 = new User($database, $id, $conn);
			} catch (Exception $e)	{ die($e->getMessage()); }
			$csvProcessor1->reload(); //Clear the Previous Object Properties 
			if ($user1->processCSV($csvProcessor1)->evaluateResult())	{
				$lineCounter++;
			}
		}//end-while 
	}//end-if-promising
	fclose($file1);
	if ($promise1->isPromising())	{
?>
		<div>
			Your file is Ready for downloading<br/>
			Number data rows it contains is <?= $lineCounter ?><br/><br/>
			<b style="font-size: 1.1em;"><a href="<?= $filename ?>">Click Here</a></b> to download your file<br/><br/>
			Once the download is complete, kindly click the proceed button 
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were problems in generating your file <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>					
			</div>
			<div class="ui-sys-panel-footer">
				<div id="perror" class="ui-sys-error-message"></div>
<?php 
	if ($promise1->isPromising())	{
		if ($lineCounter > 0)	{
?>
			<a id="__id_csv_previewer" data-error-control="perror" data-server-forward-path="../server/getcsvpreviewer.php" data-file-to-read="<?= $filename ?>" data-dialog-container="__id_general_dialog_holder" class="button-link">Preview</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php
		}
?>
		<a class="button-link" href="<?= $thispage ?>?page=manageuser_csv&report=<?= $filename ?>&downloadable=true">Continue</a>
<?php
	}
?>			
			</div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "manageuser_csv" && isset($_REQUEST['sortable']) && Authorize::isAllowable($config, "manageuser_csv", "normal", "setlog", "-1", "-1"))	{
		$enableUpdate = false;
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">User CSV File <br/> (Column Sorting)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (isset($_REQUEST['fieldname']) && isset($_REQUEST['fieldtype']) && isset($_REQUEST['namespaceTag']) && isset($_REQUEST['fieldvalue']))	{
		$enableUpdate = true;
?>
				<form class="pure-form" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="manageuser_csv"/>
					<input type="hidden" name="downloadable" value="true"/>
<?php 
	$sortableContainer = Object::getUICSVDivSortable(Object::getLineToSortFromUserFieldData($_REQUEST['fieldname'], $_REQUEST['fieldtype'], $_REQUEST['namespaceTag'],$_REQUEST['fieldvalue']));
	echo $sortableContainer;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
<?php 
	} else {
?>
		Perhaps you did not select any column to be included in your CSV File <br/>
		Kindly go back to Column Selection and select atleast ONE Column
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer">
				<a class="button-link" href="<?= $thispage ?>?page=manageuser_csv">Back to Column Selection</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php 
	if ($enableUpdate)	{
?>				
				<input type="button" id="__add_record" value="Proceed"/>
<?php 
	}
?>
			</div>
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
	} else if ($page == "manageuser_csv" && Authorize::isAllowable($config, "manageuser_csv", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">User CSV File <br/>(Column Selection)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
						<input type="hidden" name="page" value="manageuser_csv"/>
						<input type="hidden" name="sortable" value="true"/>
<?php
	$listOfAvailableColumnsInTables = Object::getUICSVTables(User::getAvailableViewableColumns());
	echo $listOfAvailableColumnsInTables;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer">
				<input type="button" id="__add_record" value="Proceed"/>
			</div>
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
	} else if ($page == "manageuser_delete" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "manageuser_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$user1 = null;
		$ulogin1 = null;
		try {
			 $user1 = new User($database, $_REQUEST['id'], $conn);
			 $ulogin1 = $user1->getLogin();
			 $user1->commitDelete();
			 $ulogin1->commitDelete();
			 $enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason("You might be dealing with a User which is no longer existing in the system or a None Empty Record");
			$promise1->setPromise(false);
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">User Removal Report</div>
			<div class="ui-sys-panel-pody ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			The User <?= $ulogin1->getFullname() ?> has been successful removed from the system
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Removing the User <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageuser_delete", "Deleted ".$ulogin1->getLoginName());
	} else if ($page == "manageuser_delete" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "manageuser_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to database services");
		$user1 = null;
		$ulogin1 = null;
		try {
			$user1 = new User($database, $_REQUEST['id'], $conn);
			$ulogin1 = $user1->getLogin();
			$user1->setExtraFilter(System::getCodeString(8));
			$user1->commitUpdate();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Delete Confirmation</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<div class="ui-sys-warning">
					You are about to remove a User <b><?= $ulogin1->getFullname() ?></b> from the System <br/>
					<b>NOTE: This Operation Can not be reversed</b><br/>
					Are You Sure You want to remove the User <?= $ulogin1->getFullname() ?> from the System?
				</div>
				<div class="ui-sys-center-text">
					<a class="button-link-warning" href="<?= $thispage ?>?page=manageuser_delete&report=io&id=<?= $user1->getUserId() ?>&rndx=<?= $user1->getExtraFilter() ?>">Yes (Remove)</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a class="button-link" href="<?= $thispage ?>?page=manageuser">No (Cancel)</a>
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "manageuser_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "manageuser_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$user1 = null;
		$ulogin1 = null;
		try {
			$user1 = new User($database, $_REQUEST['id'], $conn);
			$ulogin1 = $user1->getLogin();
		} catch (Exception $e)	{ die($e->getMessage()); }
		$promise1 = new Promise();
		$promise1->setReason("Nothing Has been Updated");
		$enableUpdate = false;
		$statusId = mysql_real_escape_string($_REQUEST['statusId']);
		$jobId = mysql_real_escape_string($_REQUEST['jobId']);
		$groupId = mysql_real_escape_string($_REQUEST['groupId']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
		$isroot = 0;
		if (isset($_REQUEST['isroot'])) $isroot = 1;
		if (is_null($ulogin1->getUserStatus()) || ($statusId != $ulogin1->getUserStatus()->getStatusId()))	{
			$ulogin1->setUserStatus($statusId); $enableUpdate = true;
		}
		if (is_null($ulogin1->getJobTitle()) || ($jobId != $ulogin1->getJobTitle()->getJobId()))	{
			$ulogin1->setJobTitle($jobId); $enableUpdate = true;
		}
		if (is_null($ulogin1->getGroup()) || ($groupId != $ulogin1->getGroup()->getGroupId()))	{
			$ulogin1->setGroup($groupId); $enableUpdate = true;
		}
		if ($extraInformation != $ulogin1->getExtraInformation())	{
			$ulogin1->setExtraInformation($extraInformation); $enableUpdate = true;
		}
		if (($isroot == 1) xor $ulogin1->isRoot())	{
			$ulogin1->setRoot($isroot); $enableUpdate = true;
		}
		//This is the Last 
		if (trim($_REQUEST['rndx']) != $user1->getExtraFilter())	{
			$enableUpdate = false;
			$promise1->setReason("The System has Detected you are replaying data in your Browser window");
			$promise1->setPromise(false);
		}
		//Now proceed with general find a new value of extra Filter 
		$user1->setExtraFilter(System::getCodeString(8));
		if ($enableUpdate)	{
			try {
				$user1->commitUpdate();
				$ulogin1->commitUpdate();
				$promise1->setPromise(true);
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
				$enableUpdate = false;
			}
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">User Editing Report</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			User <?= $ulogin1->getFullname() ?> has been updated successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were no updates for the user <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageuser_edit", "Edited ".$ulogin1->getLoginName());
	} else if ($page == "manageuser_edit" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "manageuser_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$user1 = null;
		$ulogin1 = null;
		$extraFilter = System::getCodeString(8);
		try {
			$user1 = new User($database, $_REQUEST['id'], $conn);
			$ulogin1 = $user1->getLogin();
			$user1->setExtraFilter($extraFilter);
			$user1->commitUpdate();
		} catch (Exception $e)	{ die($e->getMessage()); }
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Edit User <?= $ulogin1->getFullname() ?></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="manageuser_edit"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="id" value="<?= $user1->getUserId() ?>"/>
					<input type="hidden" name="rndx" value="<?= $user1->getExtraFilter() ?>"/>
					<div class="pure-control-group">
						<label for="statusId">User Status </label>
						<select id="statusId" name="statusId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select User Status">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = UserStatus::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($ulogin1->getUserStatus()) && ($ulogin1->getUserStatus()->getStatusId() == $alist1['id'])) $selected="selected"; 
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="jobId">Job Title </label>
						<select id="jobId" name="jobId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Job Title">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = JobTitle::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($ulogin1->getJobTitle()) && ($ulogin1->getJobTitle()->getJobId() == $alist1['id'])) $selected="selected"; 
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="groupId">Group </label>
						<select id="groupId" name="groupId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Group">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Group::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($ulogin1->getGroup()) && ($ulogin1->getGroup()->getGroupId() == $alist1['id'])) $selected="selected"; 
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
<?php 
	if ($login1->isRoot() && ($login1->getLoginId() != $ulogin1->getLoginId()))	{
		//Only those logged in as root users are allowed to change 
		//this status
		//And as usual you can not change this for yourself even if you are the root user
		$chkEnable = "";
		if ($ulogin1->isRoot()) $chkEnable = "checked";
?>
					<div class="pure-controls">
						<label><input type="checkbox" name="isroot" value="1" <?= $chkEnable ?>/>Enable/Disable System User Status</label>
					</div>
<?php
	}
?>
					
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input value="<?= $ulogin1->getExtraInformation() ?>" type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Edit Record"/>
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
	} else if ($page == "manageuser_detail" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "manageuser_detail", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$user1 = null;
		$ulogin1 = null;
		try {
			$user1 = new User($database, $_REQUEST['id'], $conn);
			$ulogin1 = $user1->getLogin();
			$photoname = "default.png";
			if (! is_null($ulogin1->getSex()))	{
				if (strtolower($ulogin1->getSex()->getSexName()) == "male") $photoname="default_male.png";
				else if (strtolower($ulogin1->getSex()->getSexName()) == "female") $photoname="default_female.png";
			}
			if (! is_null($ulogin1->getPhoto())) $photoname = $ulogin1->getPhoto();
			$ulogin1->setExtraFilter("../data/users/photo/".$photoname);
		} catch (Exception $e)	{die($e->getMessage());}
		mysql_close($conn);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Details for <?= $ulogin1->getFullname() ?></div>
		<div class="ui-sys-panel-content ui-sys-data-capture">
			<table class="pure-table">
				<thead>
					<tr>
						<th>Field Name</th>
						<th>Field Value</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2">
							<div class="ui-sys-user-photo-detail"><img alt="PDET" src="<?= $ulogin1->getExtraFilter() ?>"/></div>
						</td>
					</tr>
					<tr>
						<td>Firstname</td>
						<td><?= $ulogin1->getFirstname() ?></td>
					</tr>
					<tr>
						<td>Middlename</td>
						<td><?= $ulogin1->getMiddlename() ?></td>
					</tr>
					<tr>
						<td>Lastname</td>
						<td><?= $ulogin1->getLastname() ?></td>
					</tr>
<?php 
	if (! is_null($ulogin1->getUserStatus()))	{
?>
					<tr>
						<td>Status</td>
						<td><?= $ulogin1->getUserStatus()->getStatusName() ?></td>
					</tr>
<?php		
	}
	if (! is_null($ulogin1->getDOB()))	{
?>
					<tr>
						<td>Date of Birth</td>
						<td><?= DateAndTime::convertFromDateTimeObjectToGUIDateFormat($ulogin1->getDOB()) ?></td>
					</tr>
<?php
	}
	if (! is_null($ulogin1->getSex()))	{
?>
					<tr>
						<td>Sex</td>
						<td><?= $ulogin1->getSex()->getSexName() ?></td>
					</tr>
<?php
	}
	if (! is_null($ulogin1->getMarital()))	{
?>
					<tr>
						<td>Marital</td>
						<td><?= $ulogin1->getMarital()->getMaritalName() ?></td>
					</tr>
<?php
	}
	if (! is_null($ulogin1->getUserType()))	{
?>
					<tr>
						<td>Type of User</td>
						<td><?= $ulogin1->getUserType()->getTypeName() ?></td>
					</tr>
<?php		
	}
	$rootUserText = "Normal User";
	if ($ulogin1->isRoot()) $rootUserText = "System User";
?>
					<tr>
						<td colspan="2">This is a <b><i><?= $rootUserText ?></i></b></td>
					</tr>
					<tr>
						<td>Email</td>
						<td><?= $ulogin1->getEmail() ?></td>
					</tr>
					<tr>
						<td>Username </td>
						<td><?= $ulogin1->getLoginName() ?></td>
					</tr>
					<tr>
						<td>Phone</td>
						<td><?= $ulogin1->getPhone() ?></td>
					</tr>
<?php 
	if (! is_null($ulogin1->getJobTitle()))	{
?>
					<tr>
						<td>Job Title</td>
						<td><?= $ulogin1->getJobTitle()->getJobName() ?></td>
					</tr>
<?php
	}
	if (! is_null($ulogin1->getGroup()))	{
?>
					<tr>
						<td>Group</td>
						<td><?= $ulogin1->getGroup()->getGroupName() ?></td>
					</tr>
<?php
	}
	if (! is_null($ulogin1->getTheme()))	{
?>
					<tr>
						<td>User Theme</td>
						<td><?= $ulogin1->getTheme()->getThemeName() ?></td>
					</tr>
<?php		
	}
	if (! is_null($ulogin1->getFirstDayOfAWeek()))	{
?>
					<tr>
						<td>First Day of a Week</td>
						<td><?= $ulogin1->getFirstDayOfAWeek()->getDayName() ?></td>
					</tr>
<?php
	}
	if (! is_null($ulogin1->getAdmissionTime()))	{
?>
					<tr>
						<td>Time of Admission </td>
						<td><?= $ulogin1->getAdmissionTime()->getDateAndTimeString() ?></td>
					</tr>
<?php
	}
	if (! is_null($ulogin1->getLastLoginTime()))	{
?>
					<tr>
						<td>Time of Last Login </td>
						<td><?= $ulogin1->getLastLoginTime()->getDateAndTimeString() ?></td>
					</tr>
<?php
	}
?>
					<tr>
						<td>Extra Information</td>
						<td><?= $ulogin1->getExtraInformation() ?><br/><?= $user1->getExtraInformation() ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="ui-sys-panel-footer">
			<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Login::shouldAllowUpdateControls($login1, $ulogin1) && Authorize::isAllowable($config, "manageuser_edit", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Edit <?= $ulogin1->getFullname() ?>" href="<?= $thispage ?>?page=manageuser_edit&id=<?= $user1->getUserId() ?>"><img alt="DAT" src="../sysimage/buttonedit.png"/></a>
<?php
	}
	if (Login::shouldAllowUpdateControls($login1, $ulogin1) && Authorize::isAllowable($config, "manageuser_delete", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Delete <?= $ulogin1->getFullname() ?>" href="<?= $thispage ?>?page=manageuser_delete&id=<?= $user1->getUserId() ?>"><img alt="DAT" src="../sysimage/buttondelete.png"/></a>
<?php
	}
	if (Login::shouldAllowUpdateControls($login1, $ulogin1) && Authorize::isAllowable($config, "managesystemfirewall", "normal", "donsetlog", "-1", "-1"))	{
?>
		<a title="Set System Firewall for <?= $ulogin1->getFullname() ?>" href="<?= $thispage ?>?page=managesystemfirewall&type=login&id=<?= $ulogin1->getLoginId() ?>"><img alt="DAT" src="../sysimage/buttonfirewall.png"/></a>
<?php	
	}
	if (Login::shouldAllowUpdateControls($login1, $ulogin1) && Authorize::isAllowable($config, "managesystemfirewall_graph", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="View Authorization Graph for <?= $ulogin1->getFullname() ?>" href="<?= $thispage ?>?page=managesystemfirewall_graph&type=login&id=<?= $ulogin1->getLoginId() ?>"><img alt="DAT" src="../sysimage/buttongraph.png"/></a>
<?php	
	}
?>
				<span class="ui-sys-clear-both">&nbsp;</span><br />
			</div>
		</div>
	</div>
<?php
	} else if ($page == "manageuser_add" && isset($_REQUEST['report']) && Authorize::isAllowable($config, "manageuser_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$firstname = mysql_real_escape_string($_REQUEST['firstname']);
		$middlename = mysql_real_escape_string($_REQUEST['middlename']);
		$lastname = mysql_real_escape_string($_REQUEST['lastname']);
		$fullname = mysql_real_escape_string($_REQUEST['fullname']);
		$dob = DateAndTime::convertFromGUIDateFormatToSystemDateAndTimeFormat(mysql_real_escape_string($_REQUEST['dob']));
		$sexId = mysql_real_escape_string($_REQUEST['sexId']);
		$maritalId = mysql_real_escape_string($_REQUEST['maritalId']);
		$jobId = mysql_real_escape_string($_REQUEST['jobId']);
		$groupId = mysql_real_escape_string($_REQUEST['groupId']);
		$email = mysql_real_escape_string($_REQUEST['email']);
		$phone = mysql_real_escape_string($_REQUEST['phone']);
		$loginName = mysql_real_escape_string($_REQUEST['loginName']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel header ui-widget-header">Summary Report for <i><?= $fullname ?></i></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	$extraFilter = System::getCodeString(8);
	$ulogin1 = null;
	try {
		$loginId = Login::initializeLoginRecordForUser($database, $conn, $loginName);
		$query="INSERT INTO users (loginId, extraFilter, extraInformation) VALUES('$loginId', '$extraFilter', '$extraInformation')";
		mysql_db_query($database, $query, $conn) or Object::shootException("User[Adding]: Could not Add a Record into the System");
		$ulogin1 = new Login($database, $loginId, $conn);
		/*Proceed on Updating*/
		$ulogin1->setFirstname($firstname);
		$ulogin1->setMiddlename($middlename);
		$ulogin1->setLastname($lastname);
		$ulogin1->setFullname($fullname);
		$ulogin1->setDOB($dob);
		$ulogin1->setSex($sexId);
		$ulogin1->setMarital($maritalId);
		$ulogin1->setJobTitle($jobId);
		$ulogin1->setGroup($groupId);
		$ulogin1->setEmail($email);
		$ulogin1->setPhone($phone);
		//username/loginName is alredy set during initialization 
		$ulogin1->setAdmitted("1");
		$ulogin1->setUsingDefaultPassword("1");
		$ulogin1->setPassword(sha1(Object::$defaultPassword));
		$ulogin1->setAdmissionTime($systemDate1->getDateAndTimeString());
		$ulogin1->commitUpdate();
		/*Ending Update*/
		$promise1->setPromise(true);
		$enableUpdate = true;
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
		$enableUpdate = false;
	}
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">The User <?= $ulogin1->getFullname() ?>, has successful Added to the system<br/>
			<br/>
			This user is using a default password <b><?= Object::$defaultPassword ?></b>
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Adding the user<br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageuser_add", "Added $loginName");
	} else if ($page == "manageuser_add" && Authorize::isAllowable($config, "manageuser_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Add a New User</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="manageuser_add"/>
					<input type="hidden" name="report" value="io"/>
					<div class="pure-control-group">
						<label for="firstname">Firstname </label>
						<input type="text" name="firstname" id="firstname" size="48" required pattern="<?= $exprA32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Firstname : <?= $msgA32Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="middlename">Middlename </label>
						<input type="text" name="middlename" id="middlename" size="48" required pattern="<?= $exprA32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Middlename : <?= $msgA32Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="lastname">Lastname </label>
						<input type="text" name="lastname" id="lastname" size="48" required pattern="<?= $exprA32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Lastname : <?= $msgA32Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="fullname">Full Name </label>
						<input type="text" name="fullname" id="fullname" size="48" required pattern="<?= $exprA64Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA64Name ?>" validate_message="Accademic Name : <?= $msgA64Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="dob">Date of Birth </label>
						<input class="datepicker" type="text" name="dob" id="dob" size="48" required pattern="<?= $exprDate ?>" validate="true" validate_control="text" validate_expression="<?= $exprDate ?>" validate_message="Date: <?= $msgDate ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="sexId">Sex </label>
						<select id="sexId" name="sexId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Sex">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Sex::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
?>
			<option value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
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
?>
			<option value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="phone">Phone/Telephone </label>
						<input type="text" name="phone" id="phone" size="32" required pattern="<?= $exprPhone ?>" validate="true" validate_control="text" validate_expression="<?= $exprPhone ?>" validate_message="Phone/Telephone : <?= $msgPhone ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="email">Email Address</label>
						<input type="text" name="email" id="email" size="48" required pattern="<?= $exprEmail ?>" validate="true" validate_control="text" validate_expression="<?= $exprEmail ?>%<?= $exprL32Name ?>" validate_message="Email Format : <?= $msgEmail ?>%Email Length: <?= $msgL32Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="loginName">Login Username </label>
						<input type="text" name="loginName" id="loginName" size="48" required pattern="<?= $exprA32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Username : <?= $msgA32Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="jobId">Job Title </label>
						<select id="jobId" name="jobId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Job Title">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = JobTitle::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
?>
			<option value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="groupId">Group </label>
						<select id="groupId" name="groupId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Group">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Group::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
?>
			<option value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Add Record"/>
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
		var $loginName1 = $('#loginName');
		if (! $loginName1.length) return;
		var $email1 = $('#email');
		if (! $email1.length) return;
		$target1.empty();
		$.ajax({
			url: "../server/service_username_email_availability.php",
			method: "POST",
			data: { param1: $loginName1.val(), param2: $email1.val() },
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
	} else if ($page == "manageuser" && Authorize::isAllowable($config, "manageuser", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Users Management</div>
			<div class="ui-sys-panel-body">
<!--Block ONE controls for searching-->
				<div class="ui-sys-search-container pure-form pure-user-control">
					<input type="text" title="At Least Three Characters should be supplied" required placeholder="ABC" size="32"/>
					<a title="Click to Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=manageuser" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
				</div>
<!--Block ONE ends-->	
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
		//saving Authorization Rules 
		$blnAuthorizationDetail = Authorize::isAllowable($config, "manageuser_detail", "normal", "donotsetlog", "-1", "-1");
?>
<!--Block Two, Search Results-->
<div class="ui-sys-search-results">
	<label id="statustextlabel"></label>
	<table class="pure-table ui-sys-table-search-results">
		<thead>
			<tr>
				<th>S/N</th>
				<th>Name</th>
				<th>Status</th>
				<th>Sex</th>
				<th>Email</th>
				<th>Phone</th>
				<th></th>
			</tr>
		</thead>
<?php 
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
	$query = User::getQueryText();
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$result = mysql_db_query($database, $query, $conn) or die("Could not fetch user data from the storage");
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	while (list($id) = mysql_fetch_row($result))	{
		if ($sqlReturnedRowCount > $maximumReturnedRows)	{
			//Finalize All House Keeping Here
			break;
		}
		$user1 = null;
		$ulogin1 = null;
		try {
			$user1 = new User($database, $id, $conn);
			$ulogin1 = $user1->getLogin();
		} catch (Exception $e)	{ die($e->getMessage()); }
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated per objects 
		if ($user1->searchMatrix($matrix1)->evaluateResult())	{
			if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
				$numberOfTBodies++;
				$tbodyClosed = false;
			} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
				$numberOfTBodies++;
				$perTbodyCounter = 0;
			}
			//In All Cases we need to display tr
			$pureTableOddClass = "";
			if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
			<tr class="<?= $pureTableOddClass ?>">
				<td><?= $sqlReturnedRowCount + 1 ?></td>
				<td><?= $ulogin1->getFullname() ?></td>
				<td>
<?php 
	if (! is_null($ulogin1->getUserStatus()))	{
?>
		<?= $ulogin1->getUserStatus()->getStatusName() ?>
<?php
	}
?>				
				</td>
				<td>
<?php 
	if (! is_null($ulogin1->getSex()))	{
?>
		<?= $ulogin1->getSex()->getSexName() ?>
<?php
	}
?>				
				</td>
				<td><?= $ulogin1->getEmail() ?></td>
				<td><?= $ulogin1->getPhone() ?></td>
				<td>
<?php 
	if ($blnAuthorizationDetail)	{
?>
		<a href="<?= $thispage ?>?page=manageuser_detail&id=<?= $user1->getUserId() ?>" class="ui-sys-search-results-controls"><img alt="DAT" src="../sysimage/buttondetail.png"/></a>
<?php
	}
?>				
				</td>
			</tr>
<?php
			$perTbodyCounter++;
			$sqlReturnedRowCount++;
		}//end-evaluate-results
	}//end-while
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
	mysql_close($conn);
?>
	</table>
	<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
	<ul class="ui-sys-pagination"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
</div>
<!--Block Two Ends-->
<?php
	} //end-if-searchtext
?>		
<!--Beginning of Block Three-->
				<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "manageuser_csv", "normal", "donotsetlog", "-1", "-1")) {
?>
		<a title="Download CSV/EXCEL Formatted Data" href="<?= $thispage ?>?page=manageuser_csv"><img alt="DAT" src="../sysimage/buttoncsv.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "manageuser_add", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Add a New System User" href="<?= $thispage ?>?page=manageuser_add"><img alt="DAT" src="../sysimage/buttonadd.png"/></a>
<?php	
	}
?>				
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	}/*User End*/ else/*JobTitle BEGIN*/if ($page == "managejobtitle_csv" && isset($_REQUEST['report']) && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managejobtitle_csv", "normal", "setlog", "-1", "-1"))	{
		$filename=$_REQUEST['report'];
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">JobTitle CSV File<br/> (Download Report)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (unlink($filename))	{
?>
		<div class="ui-state-highlight">
			The temporary file has been removed succesful from the Server
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problem on removing the temporary file from the Server
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	} else if ($page == "managejobtitle_csv" && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managejobtitle_csv", "normal", "setlog", "-1", "-1"))	{
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$promise1 = new Promise();
		$promise1->setPromise(true);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">JobTitle CSV File <br/> (Downlad CSV File)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	$filename="";
	$tfname=$filename.$date;
	$tfname=$tfname.rand(100,999);
	$filename=sha1($tfname);
	$filename=$filename.".csv";
	$filename="../temp/".$filename;
	$searchText = Object::getSearchTextFromUserFieldData($_REQUEST['controlIndex'], $_REQUEST['fieldname'], $_REQUEST['op'], $_REQUEST['namespaceTag'], $_REQUEST['fieldvalue']);
	$lineCounter = 0;
	$file1 = fopen($filename, "w") or die("Could not Open the File for writing");
	$csvProcessor1 = null;
	try {
		$csvProcessor1 = new CSVProcessor($searchText, $file1);
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$primise1->setPromise(false);
	}
	$result = null;
	if ($promise1->isPromising())	{
		$query = JobTitle::getQueryText();
		try {
			$result = mysql_db_query($database, $query, $conn) or Object::shootException("JobTitle[CSV]: Could not Execute Query");
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$primise1->setPromise(false);
		}
	}
	if ($promise1->isPromising())	{
		while (list($id) = mysql_fetch_row($result))	{
			$job1 = null;
			try	{
				$job1 = new JobTitle($database, $id, $conn);
			} catch (Exception $e)	{ die($e->getMessage()); }
			$csvProcessor1->reload(); //Clear the Previous Object Properties 
			if ($job1->processCSV($csvProcessor1)->evaluateResult())	{
				$lineCounter++;
			}
		}//end-while 
	}//end-if-promising
	fclose($file1);
	if ($promise1->isPromising())	{
?>
		<div>
			Your file is Ready for downloading<br/>
			Number data rows it contains is <?= $lineCounter ?><br/><br/>
			<b style="font-size: 1.1em;"><a href="<?= $filename ?>">Click Here</a></b> to download your file<br/><br/>
			Once the download is complete, kindly click the proceed button 
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were problems in generating your file <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>					
			</div>
			<div class="ui-sys-panel-footer">
				<div id="perror" class="ui-sys-error-message"></div>
<?php 
	if ($promise1->isPromising())	{
		if ($lineCounter > 0)	{
?>
			<a id="__id_csv_previewer" data-error-control="perror" data-server-forward-path="../server/getcsvpreviewer.php" data-file-to-read="<?= $filename ?>" data-dialog-container="__id_general_dialog_holder" class="button-link">Preview</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php
		}
?>
		<a class="button-link" href="<?= $thispage ?>?page=managejobtitle_csv&report=<?= $filename ?>&downloadable=true">Continue</a>
<?php
	}
?>			
			</div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managejobtitle_csv" && isset($_REQUEST['sortable']) && Authorize::isAllowable($config, "managejobtitle_csv", "normal", "setlog", "-1", "-1"))	{
		$enableUpdate = false;
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">JobTitle CSV File <br/> (Column Sorting)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (isset($_REQUEST['fieldname']) && isset($_REQUEST['fieldtype']) && isset($_REQUEST['namespaceTag']) && isset($_REQUEST['fieldvalue']))	{
		$enableUpdate = true;
?>
				<form class="pure-form" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managejobtitle_csv"/>
					<input type="hidden" name="downloadable" value="true"/>
<?php 
	$sortableContainer = Object::getUICSVDivSortable(Object::getLineToSortFromUserFieldData($_REQUEST['fieldname'], $_REQUEST['fieldtype'], $_REQUEST['namespaceTag'],$_REQUEST['fieldvalue']));
	echo $sortableContainer;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
<?php 
	} else {
?>
		Perhaps you did not select any column to be included in your CSV File <br/>
		Kindly go back to Column Selection and select atleast ONE Column
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer">
				<a class="button-link" href="<?= $thispage ?>?page=managejobtitle_csv">Back to Column Selection</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php 
	if ($enableUpdate)	{
?>				
				<input type="button" id="__add_record" value="Proceed"/>
<?php 
	}
?>
			</div>
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
	} else if ($page == "managejobtitle_csv" && Authorize::isAllowable($config, "managejobtitle_csv", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">JobTitle CSV File <br/>(Column Selection)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
						<input type="hidden" name="page" value="managejobtitle_csv"/>
						<input type="hidden" name="sortable" value="true"/>
<?php
	$listOfAvailableColumnsInTables = Object::getUICSVTables(JobTitle::getAvailableViewableColumns());
	echo $listOfAvailableColumnsInTables;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer">
				<input type="button" id="__add_record" value="Proceed"/>
			</div>
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
	} else if ($page == "managejobtitle_delete" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managejobtitle_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$job1 = null;
		try {
			 $job1 = new JobTitle($database, $_REQUEST['id'], $conn);
			 $job1->commitDelete();
			 $enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason("You might be dealing with a JobTitle which is no longer existing in the system or a None Empty Record");
			$promise1->setPromise(false);
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">JobTitle Removal Report</div>
			<div class="ui-sys-panel-pody ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			The JobTitle <?= $job1->getJobName() ?> has been successful removed from the system
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Removing the JobTitle <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managejobtitle_delete", "Deleted ".$job1->getJobName());
	} else if ($page == "managejobtitle_delete" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managejobtitle_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to database services");
		$job1 = null;
		try {
			$job1 = new JobTitle($database, $_REQUEST['id'], $conn);
			$job1->setExtraFilter(System::getCodeString(8));
			$job1->commitUpdate();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Delete Confirmation</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<div class="ui-sys-warning">
					You are about to remove a JobTitle <b><?= $job1->getJobName() ?></b> from the System <br/>
					<b>NOTE: This Operation Can not be reversed</b><br/>
					Are You Sure You want to remove the JobTitle <?= $job1->getJobName() ?> from the System?
				</div>
				<div class="ui-sys-center-text">
					<a class="button-link-warning" href="<?= $thispage ?>?page=managejobtitle_delete&report=io&id=<?= $job1->getJobId() ?>&rndx=<?= $job1->getExtraFilter() ?>">Yes (Remove)</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a class="button-link" href="<?= $thispage ?>?page=managejobtitle">No (Cancel)</a>
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managejobtitle_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managejobtitle_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$job1 = null;
		try {
			$job1 = new JobTitle($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$promise1 = new Promise();
		$promise1->setReason("Nothing Has been Updated");
		$enableUpdate = false;
		$jobName = mysql_real_escape_string($_REQUEST['jobName']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
		if ($jobName != $job1->getJobName())	{
			$job1->setJobName($jobName); $enableUpdate = true;
		}
		if ($extraInformation != $job1->getExtraInformation())	{
			$job1->setExtraInformation($extraInformation); $enableUpdate = true;
		}
		//This is the Last 
		if (trim($_REQUEST['rndx']) != $job1->getExtraFilter())	{
			$enableUpdate = false;
			$promise1->setReason("The System has Detected you are replaying data in your Browser window");
			$promise1->setPromise(false);
		}
		//Now proceed with general find a new value of extra Filter 
		$job1->setExtraFilter(System::getCodeString(8));
		if ($enableUpdate)	{
			try {
				$job1->commitUpdate();
				$promise1->setPromise(true);
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
				$enableUpdate = false;
			}
		} 
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Job Editing Report</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			JobTitle <?= $job1->getJobName() ?> has been updated successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were no updates for the job <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managejobtitle_edit", "Edited ".$job1->getJobName());
	} else if ($page == "managejobtitle_edit" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managejobtitle_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$job1 = null;
		$extraFilter = System::getCodeString(8);
		try {
			$job1 = new JobTitle($database, $_REQUEST['id'], $conn);
			$job1->setExtraFilter($extraFilter);
			$job1->commitUpdate();
		} catch (Exception $e)	{ die($e->getMessage()); }
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Edit JobTitle <?= $job1->getJobName() ?></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managejobtitle_edit"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="id" value="<?= $job1->getJobId() ?>"/>
					<input type="hidden" name="rndx" value="<?= $job1->getExtraFilter() ?>"/>
					<div class="pure-control-group">
						<label for="jobName">JobTitle Name </label>
						<input value="<?= $job1->getJobName() ?>" type="text" name="jobName" id="jobName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="JobTitle Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input value="<?= $job1->getExtraInformation() ?>" type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Edit Record"/>
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
	} else if ($page == "managejobtitle_detail" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managejobtitle_detail", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$job1 = null;
		try {
			$job1 = new JobTitle($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{die($e->getMessage());}
		mysql_close($conn);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Details for <?= $job1->getJobName() ?></div>
		<div class="ui-sys-panel-content ui-sys-data-capture">
			<table class="pure-table">
				<thead>
					<tr>
						<th>Field Name</th>
						<th>Field Value</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>JobTitle Name</td>
						<td><?= $job1->getJobName() ?></td>
					</tr>
<?php 
	if (! is_null($job1->getExtraInformation()))	{
?>
					<tr>
						<td>Extra Information</td>
						<td><?= $job1->getExtraInformation() ?></td>
					</tr>
<?php
	}
?>
				</tbody>
			</table>
		</div>
		<div class="ui-sys-panel-footer">
			<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (! $job1->isRootJob() && Authorize::isAllowable($config, "managejobtitle_edit", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Edit <?= $job1->getJobName() ?>" href="<?= $thispage ?>?page=managejobtitle_edit&id=<?= $job1->getJobId() ?>"><img alt="DAT" src="../sysimage/buttonedit.png"/></a>
<?php
	}
	if (! $job1->isRootJob() && Authorize::isAllowable($config, "managejobtitle_delete", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Delete <?= $job1->getJobName() ?>" href="<?= $thispage ?>?page=managejobtitle_delete&id=<?= $job1->getJobId() ?>"><img alt="DAT" src="../sysimage/buttondelete.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "managesystemfirewall", "normal", "donsetlog", "-1", "-1"))	{
?>
		<a title="Set System Firewall for <?= $job1->getJobName() ?>" href="<?= $thispage ?>?page=managesystemfirewall&type=jobtitle&id=<?= $job1->getJobId() ?>"><img alt="DAT" src="../sysimage/buttonfirewall.png"/></a>
<?php	
	}
?>
				<span class="ui-sys-clear-both">&nbsp;</span><br />
			</div>
		</div>
	</div>
<?php
	} else if ($page == "managejobtitle_add" && isset($_REQUEST['report']) && Authorize::isAllowable($config, "managejobtitle_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$jobName = mysql_real_escape_string($_REQUEST['jobName']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
		$context = Object::$defaultContextValue;
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel header ui-widget-header">Summary Report for <i><?= $jobName ?></i></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	$extraFilter = System::getCodeString(8);
	$query="INSERT INTO jobTitle (jobName, context, extraFilter, extraInformation) VALUES('$jobName', '$context', '$extraFilter', '$extraInformation')";
	try {
		mysql_db_query($database, $query, $conn) or Object::shootException("JobTitle[Adding]: Could not Add a Record into the System");
		$promise1->setPromise(true);
		$enableUpdate = true;
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">The JobTitle <?= $jobName ?>, has successful Added to the system</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Adding the job<br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managejobtitle_add", "Added $jobName");
	} else if ($page == "managejobtitle_add" && Authorize::isAllowable($config, "managejobtitle_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Add a New JobTitle</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managejobtitle_add"/>
					<input type="hidden" name="report" value="io"/>
					<div class="pure-control-group">
						<label for="jobName">Job Title Name </label>
						<input type="text" name="jobName" id="jobName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="JobTitle Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Add Record"/>
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
	} else if ($page == "managejobtitle" && Authorize::isAllowable($config, "managejobtitle", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">JobTitles Management</div>
			<div class="ui-sys-panel-body">
<!--Block ONE controls for searching-->
				<div class="ui-sys-search-container pure-form pure-job-control">
					<input type="text" title="At Least Three Characters should be supplied" required placeholder="ABC" size="32"/>
					<a title="Click to Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=managejobtitle" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
				</div>
<!--Block ONE ends-->	
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
		//saving Authorization Rules 
		$blnAuthorizationDetail = Authorize::isAllowable($config, "managejobtitle_detail", "normal", "donotsetlog", "-1", "-1");
?>
<!--Block Two, Search Results-->
<div class="ui-sys-search-results">
	<label id="statustextlabel"></label>
	<table class="pure-table ui-sys-table-search-results">
		<thead>
			<tr>
				<th>S/N</th>
				<th>Job Name</th>
				<th></th>
			</tr>
		</thead>
<?php 
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
	$query = JobTitle::getQueryText();
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$result = mysql_db_query($database, $query, $conn) or die("Could not fetch job data from the storage");
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	while (list($id) = mysql_fetch_row($result))	{
		if ($sqlReturnedRowCount > $maximumReturnedRows)	{
			//Finalize All House Keeping Here
			break;
		}
		$job1 = null;
		try {
			$job1 = new JobTitle($database, $id, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated per objects 
		if ($job1->searchMatrix($matrix1)->evaluateResult())	{
			if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
				$numberOfTBodies++;
				$tbodyClosed = false;
			} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
				$numberOfTBodies++;
				$perTbodyCounter = 0;
			}
			//In All Cases we need to display tr
			$pureTableOddClass = "";
			if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
			<tr class="<?= $pureTableOddClass ?>">
				<td><?= $sqlReturnedRowCount + 1 ?></td>
				<td><?= $job1->getJobName() ?></td>
				<td>
<?php 
	if ($blnAuthorizationDetail)	{
?>
		<a href="<?= $thispage ?>?page=managejobtitle_detail&id=<?= $job1->getJobId() ?>" class="ui-sys-search-results-controls"><img alt="DAT" src="../sysimage/buttondetail.png"/></a>
<?php
	}
?>				
				</td>
			</tr>
<?php
			$perTbodyCounter++;
			$sqlReturnedRowCount++;
		}//end-evaluate-results
	}//end-while
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
	mysql_close($conn);
?>
	</table>
	<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
	<ul class="ui-sys-pagination"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
</div>
<!--Block Two Ends-->
<?php
	} //end-if-searchtext
?>		
<!--Beginning of Block Three-->
				<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "managejobtitle_csv", "normal", "donotsetlog", "-1", "-1")) {
?>
		<a title="Download CSV/EXCEL Formatted Data" href="<?= $thispage ?>?page=managejobtitle_csv"><img alt="DAT" src="../sysimage/buttoncsv.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "managejobtitle_add", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Add a New System JobTitle" href="<?= $thispage ?>?page=managejobtitle_add"><img alt="DAT" src="../sysimage/buttonadd.png"/></a>
<?php	
	}
?>				
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	}/*JobTitle End*/ else /*Group BEGIN*/if ($page == "managegroup_csv" && isset($_REQUEST['report']) && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managegroup_csv", "normal", "setlog", "-1", "-1"))	{
		$filename=$_REQUEST['report'];
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Group CSV File<br/> (Download Report)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (unlink($filename))	{
?>
		<div class="ui-state-highlight">
			The temporary file has been removed succesful from the Server
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problem on removing the temporary file from the Server
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	} else if ($page == "managegroup_csv" && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managegroup_csv", "normal", "setlog", "-1", "-1"))	{
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$promise1 = new Promise();
		$promise1->setPromise(true);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Group CSV File <br/> (Downlad CSV File)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	$filename="";
	$tfname=$filename.$date;
	$tfname=$tfname.rand(100,999);
	$filename=sha1($tfname);
	$filename=$filename.".csv";
	$filename="../temp/".$filename;
	$searchText = Object::getSearchTextFromUserFieldData($_REQUEST['controlIndex'], $_REQUEST['fieldname'], $_REQUEST['op'], $_REQUEST['namespaceTag'], $_REQUEST['fieldvalue']);
	$lineCounter = 0;
	$file1 = fopen($filename, "w") or die("Could not Open the File for writing");
	$csvProcessor1 = null;
	try {
		$csvProcessor1 = new CSVProcessor($searchText, $file1);
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$primise1->setPromise(false);
	}
	$result = null;
	if ($promise1->isPromising())	{
		$query = Group::getQueryText();
		try {
			$result = mysql_db_query($database, $query, $conn) or Object::shootException("Group[CSV]: Could not Execute Query");
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$primise1->setPromise(false);
		}
	}
	if ($promise1->isPromising())	{
		while (list($id) = mysql_fetch_row($result))	{
			$group1 = null;
			try	{
				$group1 = new Group($database, $id, $conn);
			} catch (Exception $e)	{ die($e->getMessage()); }
			$csvProcessor1->reload(); //Clear the Previous Object Properties 
			if ($group1->processCSV($csvProcessor1)->evaluateResult())	{
				$lineCounter++;
			}
		}//end-while 
	}//end-if-promising
	fclose($file1);
	if ($promise1->isPromising())	{
?>
		<div>
			Your file is Ready for downloading<br/>
			Number data rows it contains is <?= $lineCounter ?><br/><br/>
			<b style="font-size: 1.1em;"><a href="<?= $filename ?>">Click Here</a></b> to download your file<br/><br/>
			Once the download is complete, kindly click the proceed button 
		</div>
<?php	
	} else {
?>
		<div class="ui-state-error">
			There were problems in generating your file <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>					
			</div>
			<div class="ui-sys-panel-footer">
				<div id="perror" class="ui-sys-error-message"></div>
<?php 
	if ($promise1->isPromising())	{
		if ($lineCounter > 0)	{
?>
			<a id="__id_csv_previewer" data-error-control="perror" data-server-forward-path="../server/getcsvpreviewer.php" data-file-to-read="<?= $filename ?>" data-dialog-container="__id_general_dialog_holder" class="button-link">Preview</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php
		}
?>
		<a class="button-link" href="<?= $thispage ?>?page=managegroup_csv&report=<?= $filename ?>&downloadable=true">Continue</a>
<?php
	}
?>			
			</div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managegroup_csv" && isset($_REQUEST['sortable']) && Authorize::isAllowable($config, "managegroup_csv", "normal", "setlog", "-1", "-1"))	{
		$enableUpdate = false;
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Group CSV File <br/> (Column Sorting)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
<?php 
	if (isset($_REQUEST['fieldname']) && isset($_REQUEST['fieldtype']) && isset($_REQUEST['namespaceTag']) && isset($_REQUEST['fieldvalue']))	{
		$enableUpdate = true;
?>
				<form class="pure-form" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managegroup_csv"/>
					<input type="hidden" name="downloadable" value="true"/>
<?php 
	$sortableContainer = Object::getUICSVDivSortable(Object::getLineToSortFromUserFieldData($_REQUEST['fieldname'], $_REQUEST['fieldtype'], $_REQUEST['namespaceTag'],$_REQUEST['fieldvalue']));
	echo $sortableContainer;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
<?php 
	} else {
?>
		Perhaps you did not select any column to be included in your CSV File <br/>
		Kindly go back to Column Selection and select atleast ONE Column
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer">
				<a class="button-link" href="<?= $thispage ?>?page=managegroup_csv">Back to Column Selection</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php 
	if ($enableUpdate)	{
?>				
				<input type="button" id="__add_record" value="Proceed"/>
<?php 
	}
?>
			</div>
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
	} else if ($page == "managegroup_csv" && Authorize::isAllowable($config, "managegroup_csv", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Group CSV File <br/>(Column Selection)</div>
			<div class="ui-sys-panel-body ui-sys-csv-data-center">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
						<input type="hidden" name="page" value="managegroup_csv"/>
						<input type="hidden" name="sortable" value="true"/>
<?php
	$listOfAvailableColumnsInTables = Object::getUICSVTables(Group::getAvailableViewableColumns());
	echo $listOfAvailableColumnsInTables;
?>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer">
				<input type="button" id="__add_record" value="Proceed"/>
			</div>
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
	} else if ($page == "managegroup_delete" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managegroup_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$group1 = null;
		try {
			 $group1 = new Group($database, $_REQUEST['id'], $conn);
			 $group1->commitDelete();
			 $enableUpdate = true;
		} catch (Exception $e)	{
			$promise1->setReason("You might be dealing with a Group which is no longer existing in the system or a None Empty Record");
			$promise1->setPromise(false);
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Group Removal Report</div>
			<div class="ui-sys-panel-pody ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			The Group <?= $group1->getGroupName() ?> has been successful removed from the system
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Removing the Group <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managegroup_delete", "Deleted ".$group1->getGroupName());
	} else if ($page == "managegroup_delete" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managegroup_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to database services");
		$group1 = null;
		try {
			$group1 = new Group($database, $_REQUEST['id'], $conn);
			$group1->setExtraFilter(System::getCodeString(8));
			$group1->commitUpdate();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Delete Confirmation</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<div class="ui-sys-warning">
					You are about to remove a Group <b><?= $group1->getGroupName() ?></b> from the System <br/>
					<b>NOTE: This Operation Can not be reversed</b><br/>
					Are You Sure You want to remove the Group <?= $group1->getGroupName() ?> from the System?
				</div>
				<div class="ui-sys-center-text">
					<a class="button-link-warning" href="<?= $thispage ?>?page=managegroup_delete&report=io&id=<?= $group1->getGroupId() ?>&rndx=<?= $group1->getExtraFilter() ?>">Yes (Remove)</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a class="button-link" href="<?= $thispage ?>?page=managegroup">No (Cancel)</a>
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "managegroup_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managegroup_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$group1 = null;
		try {
			$group1 = new Group($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$promise1 = new Promise();
		$promise1->setReason("Nothing Has been Updated");
		$enableUpdate = false;
		$isLoopDetected = false;
		$groupName = mysql_real_escape_string($_REQUEST['groupName']);
		$pId = mysql_real_escape_string($_REQUEST['pId']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
		$courseId = null;
		$year = null;
		$semester = null;
		if (isset($_REQUEST['courseId'])) $courseId = mysql_real_escape_string($_REQUEST['courseId']);
		if (isset($_REQUEST['year'])) $year = mysql_real_escape_string($_REQUEST['year']);
		if (isset($_REQUEST['semester'])) $semester = mysql_real_escape_string($_REQUEST['semester']);
		if ($groupName != $group1->getGroupName())	{
			$group1->setGroupName($groupName); $enableUpdate = true;
		}
		if ($pId != $group1->getParentGroup()->getGroupId())	{
			try {
				$group1->setParentGroup($pId); $enableUpdate = true;
			} catch (Exception $e)	{
				$isLoopDetected = true;
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
			}
		}
		if ($extraInformation != $group1->getExtraInformation())	{
			$group1->setExtraInformation($extraInformation); $enableUpdate = true;
		}
		if (! is_null($courseId) && (is_null($group1->getCourse()) || ($group1->getCourse()->getCourseId() != $courseId)))	{
			$group1->setCourse($courseId); $enableUpdate = true; 
		}
		if (! is_null($year) && ($group1->getYear() != $year))	{
			$group1->setYear($year); $enableUpdate = true;
		}
		if (! is_null($semester) && ($group1->getSemester() != $semester))	{
			$group1->setSemester($semester); $enableUpdate = true;
		}
		//This is the Last 
		if (trim($_REQUEST['rndx']) != $group1->getExtraFilter())	{
			$enableUpdate = false;
			$promise1->setReason("The System has Detected you are replaying data in your Browser window");
			$promise1->setPromise(false);
		}
		//Now proceed with general find a new value of extra Filter 
		$group1->setExtraFilter(System::getCodeString(8));
		if ($enableUpdate && ! $isLoopDetected)	{
			try {
				$group1->commitUpdate();
				$promise1->setPromise(true);
			} catch (Exception $e)	{
				$promise1->setReason($e->getMessage());
				$promise1->setPromise(false);
				$enableUpdate = false;
			}
		} else {
			$enableUpdate = false; //Use this for Group Only incase loopIs Detected shut do not keep logs
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Group Editing Report</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Group <?= $group1->getGroupName() ?> has been updated successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were no updates for the group <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managegroup_edit", "Edited ".$group1->getGroupName());
	} else if ($page == "managegroup_edit" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managegroup_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$group1 = null;
		$extraFilter = System::getCodeString(8);
		try {
			$group1 = new Group($database, $_REQUEST['id'], $conn);
			$group1->setExtraFilter($extraFilter);
			$group1->commitUpdate();
		} catch (Exception $e)	{ die($e->getMessage()); }
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Edit Group <?= $group1->getGroupName() ?></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managegroup_edit"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="id" value="<?= $group1->getGroupId() ?>"/>
					<input type="hidden" name="rndx" value="<?= $group1->getExtraFilter() ?>"/>
					<div class="pure-control-group">
						<label for="groupName">Group Name </label>
						<input value="<?= $group1->getGroupName() ?>" type="text" name="groupName" id="groupName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Group Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="pId">Parent Group</label>
						<select id="pId" name="pId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Parent Group">
							<option value="_@32767@_">--select--</option>
<?php 
	$list = Group::loadAllData($database, $conn);
	foreach ($list as $alist)	{
		$selected="";
		if ($alist['id'] == $group1->getParentGroup()->getGroupId()) $selected = "selected";
?>
		<option <?= $selected ?> value="<?= $alist['id'] ?>"><?= $alist['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input value="<?= $group1->getExtraInformation() ?>" type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
<?php 
	$checked = "";
	if (! is_null($group1->getCourse())) $checked="checked";
?>
					<div class="pure-controls">
						<label><input <?= $checked ?> type="checkbox" value="Abel" id="chk-course-control"/> Do you want to Add Course Information?</label>
					</div>
					<div class="pure-control-group block-course-control">
						<label for="courseId">Course Name</label>
						<select id="courseId" name="courseId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Course">
							<option value="_@32767@_">--select--</option>
<?php 
	$list = Course::loadAllData($database, $conn);
	foreach ($list as $alist)	{
		$selected="";
		if (! is_null($group1->getCourse()) && ($alist['id'] == $group1->getCourse()->getCourseId())) $selected = "selected";
?>
		<option <?= $selected ?> value="<?= $alist['id'] ?>"><?= $alist['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group block-course-control">
						<label for="Year">Year </label>
						<input value="<?= $group1->getYear() ?>" type="text" name="year" id="year" size="8" required pattern="<?= $expr2Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr2Number ?>" validate_message="Year : <?= $msg2Number ?>"/>
					</div>
					<div class="pure-control-group block-course-control">
						<label for="semester">Semester </label>
						<input value="<?= $group1->getSemester() ?>" type="text" name="semester" id="semester" size="8" required pattern="<?= $expr2Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr2Number ?>" validate_message="Semester : <?= $msg2Number ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Edit Record"/>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<script type="text/javascript">
(function($)	{
	var $checkCourseControl1 = $('#chk-course-control');
	function courseControlUiMaintainance(_enable)	{
		$('#form1 div.block-course-control').each(function(i, val)	{
			var $block1 = $(val);
			$block1.find('input').prop('disabled', ! _enable);
			$block1.find('select').prop('disabled', ! _enable);
		});
	}
	$checkCourseControl1.on('change', function(event)	{
		event.preventDefault();
		courseControlUiMaintainance($(this).prop('checked'));
	});
	//Default Loader
	courseControlUiMaintainance($checkCourseControl1.prop('checked'));
	$('#__add_record').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if ($page == "managegroup_detail" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managegroup_detail", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$group1 = null;
		try {
			$group1 = new Group($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{die($e->getMessage());}
		mysql_close($conn);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Details for <?= $group1->getGroupName() ?></div>
		<div class="ui-sys-panel-content ui-sys-data-capture">
			<table class="pure-table">
				<thead>
					<tr>
						<th>Field Name</th>
						<th>Field Value</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Group Name</td>
						<td><?= $group1->getGroupName() ?></td>
					</tr>
<?php 
	if (! is_null($group1->getParentGroup()))	{
?>
					<tr>
						<td>Parent Group</td>
						<td><?= $group1->getParentGroup()->getGroupName() ?></td>
					</tr>
<?php	
	}
	if (! is_null($group1->getCourse()))	{
?>
					<tr>
						<td>Course Name</td>
						<td><?= $group1->getCourse()->getCourseName() ?></td>
					</tr>
<?php
	}
?>
					<tr>
						<td>Year</td>
						<td><?= $group1->getYear() ?></td>
					</tr>
					<tr>
						<td>Semester</td>
						<td><?= $group1->getSemester() ?></td>
					</tr>
<?php 
	$typeOfGroupText = "Normal Group";
	if ($group1->isRootGroup())	{
		$typeOfGroupText = "System Group";
?>
					<tr>
						<td>Narure of the Group</td>
						<td><?= $typeOfGroupText ?></td>
					</tr>
<?php
	}
	if (! is_null($group1->getExtraInformation()))	{
?>
					<tr>
						<td>Extra Information</td>
						<td><?= $group1->getExtraInformation() ?></td>
					</tr>
<?php
	}
?>
				</tbody>
			</table>
		</div>
		<div class="ui-sys-panel-footer">
			<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (! $group1->isRootGroup() && Authorize::isAllowable($config, "managegroup_edit", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Edit <?= $group1->getGroupName() ?>" href="<?= $thispage ?>?page=managegroup_edit&id=<?= $group1->getGroupId() ?>"><img alt="DAT" src="../sysimage/buttonedit.png"/></a>
<?php
	}
	if (! $group1->isRootGroup() && Authorize::isAllowable($config, "managegroup_delete", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Delete <?= $group1->getGroupName() ?>" href="<?= $thispage ?>?page=managegroup_delete&id=<?= $group1->getGroupId() ?>"><img alt="DAT" src="../sysimage/buttondelete.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "managesystemfirewall", "normal", "donsetlog", "-1", "-1"))	{
?>
		<a title="Set System Firewall for <?= $group1->getGroupName() ?>" href="<?= $thispage ?>?page=managesystemfirewall&type=group&id=<?= $group1->getGroupId() ?>"><img alt="DAT" src="../sysimage/buttonfirewall.png"/></a>
<?php	
	}
	if (Authorize::isAllowable($config, "managesystemfirewall_graph", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="View Authorization Graph for <?= $group1->getGroupName() ?>" href="<?= $thispage ?>?page=managesystemfirewall_graph&type=group&id=<?= $group1->getGroupId() ?>"><img alt="DAT" src="../sysimage/buttongraph.png"/></a>
<?php	
	}
?>
				<span class="ui-sys-clear-both">&nbsp;</span><br />
			</div>
		</div>
	</div>
<?php
	} else if ($page == "managegroup_add" && isset($_REQUEST['report']) && Authorize::isAllowable($config, "managegroup_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$groupName = mysql_real_escape_string($_REQUEST['groupName']);
		$pId = mysql_real_escape_string($_REQUEST['pId']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
		$context = Object::$defaultContextValue;
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel header ui-widget-header">Summary Report for <i><?= $groupName ?></i></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	$extraFilter = System::getCodeString(8);
	$query="INSERT INTO groups (groupName, pId, context, extraFilter, extraInformation) VALUES('$groupName', '$pId', '$context', '$extraFilter', '$extraInformation')";
	try {
		mysql_db_query($database, $query, $conn) or Object::shootException("Group[Adding]: Could not Add a Record into the System");
		$promise1->setPromise(true);
		$enableUpdate = true;
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">The Group <?= $groupName ?>, has successful Added to the system</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Adding the group<br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managegroup_add", "Added $groupName");
	} else if ($page == "managegroup_add" && Authorize::isAllowable($config, "managegroup_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Add a New Group</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managegroup_add"/>
					<input type="hidden" name="report" value="io"/>
					<div class="pure-control-group">
						<label for="groupName">Group Name </label>
						<input type="text" name="groupName" id="groupName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Group Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="pId">Parent Group</label>
						<select id="pId" name="pId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Parent Group">
							<option value="_@32767@_">--select--</option>
<?php 
	$list = Group::loadAllData($database, $conn);
	foreach ($list as $alist)	{
?>
		<option value="<?= $alist['id'] ?>"><?= $alist['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Add Record"/>
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
	} else if ($page == "managegroup" && Authorize::isAllowable($config, "managegroup", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Groups Management</div>
			<div class="ui-sys-panel-body">
<!--Block ONE controls for searching-->
				<div class="ui-sys-search-container pure-form pure-group-control">
					<input type="text" title="At Least Three Characters should be supplied" required placeholder="ABC" size="32"/>
					<a title="Click to Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=managegroup" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
				</div>
<!--Block ONE ends-->	
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext = $_REQUEST['searchtext'];
		//saving Authorization Rules 
		$blnAuthorizationDetail = Authorize::isAllowable($config, "managegroup_detail", "normal", "donotsetlog", "-1", "-1");
?>
<!--Block Two, Search Results-->
<div class="ui-sys-search-results">
	<label id="statustextlabel"></label>
	<table class="pure-table ui-sys-table-search-results">
		<thead>
			<tr>
				<th>S/N</th>
				<th>Group Name</th>
				<th>Parent Group</th>
				<th>Nature</th>
				<th>Year</th>
				<th>Semester</th>
				<th></th>
			</tr>
		</thead>
<?php 
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
	$query = Group::getQueryText();
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$result = mysql_db_query($database, $query, $conn) or die("Could not fetch group data from the storage");
	$maximumReturnedRows = intval($profile1->getMaximumNumberOfReturnedSearchRecords());
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	while (list($id) = mysql_fetch_row($result))	{
		if ($sqlReturnedRowCount > $maximumReturnedRows)	{
			//Finalize All House Keeping Here
			break;
		}
		$group1 = null;
		try {
			$group1 = new Group($database, $id, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated per objects 
		if ($group1->searchMatrix($matrix1)->evaluateResult())	{
			$parentGroupName = "";
			if (! is_null($group1->getParentGroup())) $parentGroupName = $group1->getParentGroup()->getGroupName();
			$natureOfGroup = "Normal";
			if ($group1->isRootGroup()) $natureOfGroup = "System";
			if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
				$numberOfTBodies++;
				$tbodyClosed = false;
			} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
				echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
				$numberOfTBodies++;
				$perTbodyCounter = 0;
			}
			//In All Cases we need to display tr
			$pureTableOddClass = "";
			if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
			<tr class="<?= $pureTableOddClass ?>">
				<td><?= $sqlReturnedRowCount + 1 ?></td>
				<td><?= $group1->getGroupName() ?></td>
				<td><?= $parentGroupName ?></td>
				<td><?= $natureOfGroup ?></td>
				<td><?= $group1->getYear() ?></td>
				<td><?= $group1->getSemester() ?></td>
				<td>
<?php 
	if ($blnAuthorizationDetail)	{
?>
		<a href="<?= $thispage ?>?page=managegroup_detail&id=<?= $group1->getGroupId() ?>" class="ui-sys-search-results-controls"><img alt="DAT" src="../sysimage/buttondetail.png"/></a>
<?php
	}
?>				
				</td>
			</tr>
<?php
			$perTbodyCounter++;
			$sqlReturnedRowCount++;
		}//end-evaluate-results
	}//end-while
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
	mysql_close($conn);
?>
	</table>
	<input type="hidden" id="saveresultsstorage" value="<?= $sqlReturnedRowCount ?>"/>
	<ul class="ui-sys-pagination"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>
</div>
<!--Block Two Ends-->
<?php
	} //end-if-searchtext
?>		
<!--Beginning of Block Three-->
				<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "managegroup_csv", "normal", "donotsetlog", "-1", "-1")) {
?>
		<a title="Download CSV/EXCEL Formatted Data" href="<?= $thispage ?>?page=managegroup_csv"><img alt="DAT" src="../sysimage/buttoncsv.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "managegroup_add", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Add a New System Group" href="<?= $thispage ?>?page=managegroup_add"><img alt="DAT" src="../sysimage/buttonadd.png"/></a>
<?php	
	}
?>				
				</div>
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<?php
	}/*Group End*/ else {
		if ($login1->getUserType()->getTypeCode() == UserType::$__STUDENT)	{
			//For Student you need to load Subjects and Results 
		} else {
?>
			<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">Welcome, <?= $login1->getFullname() ?></div>
				<div class="ui-sys-panel-body">
				
<?php 
	if (Authorize::isSessionSet())	{
		$op=Authorize::getSessionValue();;
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$cid=ContextPosition::getContextIdFromName($database, $op, $conn);
		$context1=new ContextPosition($database, $cid, $conn);
		mysql_close($conn);
?>
	<div class="ui-sys-authorize-syslog ui-state-error">
		<b><i>OPERATION DENIED!!</i></b> <br />
		You do not have enough rights to perform <b><?= $context1->getContextCaption() ?></b> operation
	</div>
<?php 
		Authorize::clearSession();
	} //end-if
?>				
				
					This is the main operation windows of the system <br/>
					This window is menu controlled. Kindly note whatever changes you are making to the system is always recorded. Incase you are lost go to System Menu and click Home or click Profile<br/>
					Everything you are doing is governed by <i><?= $profile1->getProfileName() ?></i> regulations
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
<?php
		}
	} //end--if-else-page-ladder
?>
</div>
<!--ENDING OF PUTTING CODE-->
	<div id="__ui_common_errors" class="ui-sys-error-message"></div>
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
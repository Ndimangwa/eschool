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
if (! Authorize::isAllowable($config, "console_manage_results", "normal", "donotsetlog", "-1", "-1")) die("Could not Authorize you to Access the Results Management Console");
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
//Will redirect Automatically If Necessary
System::systemSSLTLSCertificateVerification($profile1);
Object::$iconPath="../sysimage/";
if (! $profile1->isInstallationComplete())	{
	header("Location: ../installation/");
	exit();
}
if ($login1->isUsingDefaultPassword())	{
	header("Location: ../profile/");
	exit();
}
if ($login1->isStudent() && ! $registeredUser1->isDiscontinued() && $registeredUser1->shouldIAdvanceYearOrSemester($profile1))	{
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
<link rel="stylesheet" type="text/css" media="all" href="../client/plugin/wickedpicker/wp/wickedpicker.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/plugin/printArea/PrintArea.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/css/fonts/font-awesome.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/plugin/fileMenu/fileMenu.min.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/css/purecss/pure-min.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/css/purecss/grids-responsive-min.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/plugin/chromose/chromoselector-2.1.8/chromoselector.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/plugin/jstree/themes/default/style.min.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/plugin/treetable/jquery.treetable.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/plugin/treetable/jquery.treetable.theme.default.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/plugin/treetable/screen.css"/>
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
<script type="text/javascript" src="../client/plugin/chromoselector-2.1.8/chromoselector.min.js"></script>
<script type="text/javascript" src="../client/plugin/jstree/jstree.min.js"></script>
<script type="text/javascript" src="../client/plugin/treetable/jquery.treetable.js"></script>
<script type="text/javascript" src="../client/js/jvalidation.js"></script>
<script type="text/javascript" src="../client/js/page.js"></script>
<script type="text/javascript">
(function($)	{
	$(function()	{
		/*jstree*/
		$('div.ui-sys-tree').jstree({
			 "core" : {
				 "check_callback" : true
			 },
			"plugins" : [ "wholerow", "checkbox" ]
		});
		/*tree table */
		var $treeTable1 = $('table.ui-sys-treetable');
		$treeTable1.treetable({ 
			expandable: true,
			initialState: "collapsed",
			onNodeExpand: function()	{
				$treeTable1.find('td').css({ border: 'none' });
			},
			onNodeCollapse: function()	{
				$treeTable1.find('td').css({ border: 'none' });
			}
		}).find('td').css({ border: 'none' });
		/*Date Handling*/
		$('.datepicker').datepicker({
			dateFormat: 'dd/mm/yy',
			firstDay: <?= $__systemDayOffset ?>,
			changeYear: true,
			yearRange:'1932:2099'
		});
		//Begin Testing 
		$('.timepicker').wickedpicker({
			twentyFour: true,
			upArrow: 'wickedpicker__controls__control-up',
			downArrow: 'wickedpicker__controls__control-down',
			title: 'Pick Time'
		});
		//Color Picker
		$('input.ui-sys-color-picker').each(function(i, val)	{
			var $colorPicker1 = $(val);
			var $targetForColorPicker1 = $colorPicker1.closest('div.color-picker-container');
			if ($targetForColorPicker1.length)	{
				var targetId = $targetForColorPicker1.attr('id');
				if (targetId)	{
					$colorPicker1.chromoselector({
						target: "#" + targetId,
						autoshow: false,
						create: function()	{
							$(this).chromoselector("show", 0);
						},
						width: 128
					});
				}
				$targetForColorPicker1.css({
					border: '1px solid',
					float: 'left',
					padding: '1em',
					backgroundColor: '#e4ccc1',
					clear: 'both'
				});
			}
			$colorPicker1.css({
				width: '100%'
			});
		});
		//General Clear 
		$('div.pure-control-group').css({
			clear: 'both'
		});
	});
})(jQuery);
</script>
</head>
<body class="ui-sys-body">
<div id="__id_general_dialog_holder">
<!--Holding Popup dialogs, they shoud use absolute positioning -->
</div>
<div class="ui-sys-main">
	<div class="ui-sys-front-topbutton-1 mobile-collapse">
		MANAGEMENT CONSOLE FOR RESULTS
	</div>

<!--BEGINNING OF PUTTING CODE-->
<div class="ui-sys-bg-grid-green">
<!--Begin Left Panel-->
<div style="padding: 0; border: 0; margin: 0; width: 20%; float: left; position: relative; overflow-x: hidden;">
	<div class="module-results-container ui-sys-accordion">
<?php 
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
?>
<!--Begin of Module Title-->
<h3>Briefcase Manager</h3>
<div class="module-title ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content module-dimension">
	<div class="ui-sys-panel-header ui-widget-header">Briefcase Manager</div>
	<div class="ui-sys-panel-body" style="font-size: 0.9em; position: relative;">
		<form class="form-module pure-form pure-form-aligned form1" method="POST" action="<?= $thispage ?>">
			<input type="hidden" name="page" value="managebriefcase"/>
			<div>
				<label>Course <select name="courseId" class="courseId">
					<option value="0">All</option>
<?php 
	$list = Course::loadAllData($database, $conn);
	foreach ($list as $alist)	{
		$course1 = new Course($database, $alist['id'], $conn);
		$id = $course1->getCourseId();
		$val = $course1->getCourseName();
		$duration = $course1->getDuration();
		echo "<option data-course-duration=\"$duration\" value=\"$id\">$val</option>";
	}
?>
				</select></label>
			</div>
			<div>
				<label>Academic Year <select name="batchId" class="batchId">
					<option value="0">All</option>
<?php 
	$list = AccademicYear::loadAllData($database, $conn);
	foreach ($list as $alist)	{
		$id = $alist['id'];
		$val = $alist['val'];
		echo "<option value=\"$id\">$val</option>";
	}
?>
				</select></label>
			</div>
			<div>
				<label>Examination <select name="examinationId" class="examinationId">
					<option value="0">All</option>
<?php 
	$list = Examination::loadAllData($database, $conn);
	foreach ($list as $alist)	{
		$id = $alist['id'];
		$val = $alist['val'];
		echo "<option value=\"$id\">$val</option>";
	}
?>
				</select></label>
			</div>
			<div>
				<label>Year <select name="year" class="year">
					<option value="0">All</option>
				</select></label>
			</div>
			<div class="ui-sys-error-message"></div>
			<div class="ui-sys-inline-controls-right">
				<input class="__add_record" type="submit" value="Query Briefcase"/>
			</div>
		</form>
	</div>
	<div class="ui-sys-panel-footer"></div>
</div>
<!--End of Module Title-->
<!--Begin of Module Title-->
<h3>Results Manager</h3>
<div class="module-title ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content module-dimension">
	<div class="ui-sys-panel-header ui-widget-header">Results Manager</div>
	<div class="ui-sys-panel-body" style="font-size: 0.9em; position: relative;">
		<form class="form-module pure-form pure-form-aligned form1" method="POST" action="<?= $thispage ?>">
			<input type="hidden" name="page" value="manageresults"/>
			<div>
				<label>Course <select name="courseId" class="courseId">
					<option value="0">All</option>
<?php 
	$list = Course::loadAllData($database, $conn);
	foreach ($list as $alist)	{
		$course1 = new Course($database, $alist['id'], $conn);
		$id = $course1->getCourseId();
		$val = $course1->getCourseName();
		$duration = $course1->getDuration();
		echo "<option data-course-duration=\"$duration\" value=\"$id\">$val</option>";
	}
?>
				</select></label>
			</div>
			<div>
				<label>Academic Year <select name="batchId" class="batchId">
					<option value="0">All</option>
<?php 
	$list = AccademicYear::loadAllData($database, $conn);
	foreach ($list as $alist)	{
		$id = $alist['id'];
		$val = $alist['val'];
		echo "<option value=\"$id\">$val</option>";
	}
?>
				</select></label>
			</div>
			<div>
				<label>Year <select name="year" class="year">
					<option value="0">All</option>
				</select></label>
			</div>
			<div>
				<label>Semester <select name="semester" class="semester">
					<option value="0">All</option>
<?php 
	for ($i=1; $i <= intval($profile1->getNumberOfSemestersPerYear()); $i++)	{
		echo "<option value=\"$i\">$i</option>";
	}
?>
				</select></label>
			</div>
			<div class="ui-sys-error-message"></div>
			<div class="ui-sys-inline-controls-right">
				<input class="__add_record" type="submit" value="Query Results"/>
			</div>
		</form>
	</div>
	<div class="ui-sys-panel-footer"></div>
</div>
<!--End of Module Title-->
<!--Begin of Module Title-->
<h3>Transcript Manager</h3>
<div class="module-title ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content module-dimension">
	<div class="ui-sys-panel-header ui-widget-header">Transcript Manager</div>
	<div class="ui-sys-panel-body" style="font-size: 0.9em; position: relative;">
		<form class="form-module pure-form pure-form-aligned form1" method="POST" action="<?= $thispage ?>">
			<div>
				<label>Course <select name="courseId" class="courseId">
					<option value="0">All</option>
<?php 
	$list = Course::loadAllData($database, $conn);
	foreach ($list as $alist)	{
		$course1 = new Course($database, $alist['id'], $conn);
		$id = $course1->getCourseId();
		$val = $course1->getCourseName();
		$duration = $course1->getDuration();
		echo "<option data-course-duration=\"$duration\" value=\"$id\">$val</option>";
	}
?>
				</select></label>
			</div>
			<div>
				<label>Batch <select name="batchId" class="batchId">
					<option value="0">All</option>
<?php 
	$list = AccademicYear::loadAllData($database, $conn);
	foreach ($list as $alist)	{
		$id = $alist['id'];
		$val = $alist['val'];
		echo "<option value=\"$id\">$val</option>";
	}
?>
				</select></label>
			</div>
			<div class="ui-sys-error-message"></div>
			<div class="ui-sys-inline-controls-right">
				<input class="__add_record" type="submit" value="Query Transcript"/>
			</div>
		</form>
	</div>
	<div class="ui-sys-panel-footer"></div>
</div>
<!--End of Module Title-->
<?php 
	mysql_close($conn);
?>
	</div> <!--End of Module Container and accordion-->
<script type="text/javascript">
(function($)	{
	$(function()	{
		$('select.courseId').on('change', function(event)	{
			//Source Select
			var $course1 = $(this).closest('select');
			if (! $course1.length) return;
			//Destination Select 
			$parent1 = $course1.closest('form'); //Use the enclosing form 
			if (! $parent1.length) return; 
			var $year1 = $parent1.find('select.year');
			if (! $year1.length) return; 
			//Initialize the destination
			$year1.empty();
			$('<option/>').attr('value', '0').html('All').appendTo($year1);
			//Get Selected Course Duration
			var $option1 = $course1.find('option:selected');
			if (! $option1.length) return;
			var duration = $option1.attr('data-course-duration');
			if (! duration) return;
			duration = parseInt(duration);
			for (var i = 1; i <= duration; i++)	{
				$('<option/>').attr('value', i).html(i).appendTo($year1);
			}
		});
	});
})(jQuery);
</script>
</div>
<!--End Left Panel, Begin Right Panel-->
<div style="padding: 0; border: 0; margin: 0; width: 80%; float: left; position: relative;">
<div class="main-results-content">
<!--BEGIN MAIN CONTENT WINDOW-->
<?php 
	/*BEGIN Results Synchronization*/if ($page == "manageresults_synchronize" && isset($_REQUEST['report']) && Authorize::isAllowable($config, "manageresults_synchronize", "normal", "setlog", "-1", "-1")) {
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$enableUpdate = false;
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Results Synchronization Tool</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	try {
		$accademicYear1 = new AccademicYear($database, $_REQUEST['batchId'], $conn);
		$promise1 = Results::synchronize($database, $conn, $_REQUEST['batchId']);
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
		$enableUpdate = false;
	}
	if ($promise1->isPromising())	{
		$enableUpdate = true;
?>
		<div class="ui-state-highlight">
			All results for academic year <?= $accademicYear1->getAccademicYear() ?> has been synchronized successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in synchronizing results<br/>
			Details: <?= $promise1->getReason() ?>
		</div>
<?php	
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"><a class="ui-sys-back" href="<?= $thispage ?>">Back to Results Management Console</a></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageresults_synchronize", "Synchronize Results and Examination");
	} else if ($page == "manageresults_synchronize" && Authorize::isAllowable($config, "manageresults_synchronize", "normal", "setlog", "-1", "-1")) {
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Results Synchronization Tool</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="manageresults_synchronize"/>
					<input type="hidden" name="report" value="io"/>
					<div class="pure-control-group">
						<label title="Synchronize Results for Accademic Year" for="batchId">Current Accademic Year </label>
						<select id="batchId" name="batchId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Batch whose results need to be synchronized">
							<option value="_@32767@_">--select--</option>
<?php 
	$controlAccademicCounter = $profile1->getCurrentAccademicYear()->getAccademicYearNumber();
	$list1 = AccademicYear::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		try {
			$accademicYear1 = new AccademicYear($database, $alist1['id'], $conn);
			if ($accademicYear1->getAccademicYearNumber() != $controlAccademicCounter) continue;
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
			<option value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Synchronize Results and Examination"/>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer"><a class="ui-sys-back" href="<?= $thispage ?>">Back to Results Management Console</a></div>
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
	} /*END Results Synchronization*/else/*BEGIN Group Closing Results*/if ($page == "manageresultsgroup_edit" && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && isset($_REQUEST['service_compile']) && isset($_REQUEST['id']) && Authorize::isAllowable($config, "manageresultsgroup_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$resultsFolder = "../data/results/";
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$enableUpdate = false;
		$groupName = "";
		$group1 = null;
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">FINALIZING GROUP RESULTS</div>
			<div class="ui-sys-panel-body">
<?php 
				try {
					$group1 = new ResultsGroup($database, $_REQUEST['id'], $conn);
					$groupName = $group1->getExaminationGroup()->getGroupName();
					if (trim($_REQUEST['rndx']) != $group1->getExtraFilter())	Object::shootException("The System has Detected you are replaying data in your Browser window");
					$promise1 = ResultsGroup::groupResultsCompilation($database, $conn, $profile1, $systemTime1, $group1->getGroupId(), "../data/results/", 51);
					$group1->setExtraFilter(System::getCodeString(8));
					$group1->setResultsLocked("1"); //We need to make sure everything is locked
					$group1->commitUpdate();
				} catch (Exception $e)	{
					$promise1->setReason($e->getMessage());
					$promise1->setPromise(false);
				}
				if ($promise1->isPromising())	{
					$enableUpdate = true;
?>
					<div class="ui-state-highlight">
						You have successful Compiled the group results for <b><?= $group1->getExaminationGroup()->getGroupName() ?></b>
					</div>
<?php
				} else {
?>
					<div class="ui-state-error">
						There were problems in compiling the Group Results<br/>
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
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageresultsgroup_edit", "Finalized $groupName");
	} else if ($page == "manageresultsgroup_edit" && isset($_REQUEST['service_compile']) && isset($_REQUEST['id']) && Authorize::isAllowable($config, "manageresultsgroup_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$resultsFolder = "../data/results/";
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$group1 = null;
		try {
			$group1 = new ResultsGroup($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">FINALIZING GROUP RESULTS</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
		$individualResultsAreFine = true;
?>
<!--Begin Summary Of Individual Results-->
				<div>
					<table class="pure-table" class="pure-table pure-table-horizontal font-family-1 font-size-0-8-em" style="width: 100%;">
						<thead>
							<tr>
								<th colspan="7">Summary of Individual Results in a Group "<?= $group1->getExaminationGroup()->getGroupName() ?>"</th>
							</tr>
							<tr>
								<th></th>
								<th>Examination Name</th>
								<th>Type of Examination</th>
								<th>Semester</th>
								<th>Status</th>
								<th>Storage</th>
								<th>Comment</th>
							</tr>
						</thead>
						<tbody>
<?php 
	$list1 = null;
	try {
		$list1 = ResultsGroup::getResultsBelongingToAGroup($database, $conn, $group1->getGroupId(), false);
	} catch (Exception $e)	{
		
	}
	if (! is_null($list1))	{
		for ($i=0; $i < sizeof($list1); $i++)	{
			$results1 = null;
			try {
				$results1 = new Results($database, $list1[$i], $conn);
			} catch (Exception $e)	{
				continue;
			}
			$primaryText = "N/A";
			$examinationName = "";
			$semesterText = "";
			$statusText = "OPEN";
			$storageColor = "red";
			$commentText = "Data File Not Found";
			$rawFileIsOkay = false;
			$gradedFileIsOkay = false;
			$fileIsFound = false;
			if (! is_null($results1->getExamination()))	{
				$examinationName = $results1->getExamination()->getExaminationName();
				if (! is_null($results1->getRawMarksCSVFile()) && file_exists($resultsFolder.$results1->getRawMarksCSVFile()))	{
					$fileIsFound = true;
					$commentText = "Data File is Present, However the Integrity of Data is Compromised";
					$storageColor = "gold";
					if (md5_file($resultsFolder.$results1->getRawMarksCSVFile()) == $results1->getRawMarksCSVFileChecksum())	{
						$commentText = "Data File is Present, and the Data has passed the Integrity Check";
						$storageColor = "blue";
						$rawFileIsOkay = true;
					}
				}
				//We need to check graded file too 
				if ($rawFileIsOkay && ! is_null($results1->getGradedMarksCSVFile()) && file_exists($resultsFolder.$results1->getGradedMarksCSVFile()))	{
					$commentText = "[Graded] Data File is Present, However the Integrity of Data is Compromised";
					$storageColor = "gold";
					if (md5_file($resultsFolder.$results1->getGradedMarksCSVFile()) == $results1->getGradedMarksCSVFileChecksum())	{
						$commentText = "[Graded] Data File is Present, and the Data has passed the Integrity Check";
						$storageColor = "blue";
						$gradedFileIsOkay = true;
					}
				}
				
				if ($results1->getExamination()->isPrimary())	{
					$primaryText = "Primary";
					$semesterText = $results1->getExamination()->getSemester();
					$individualResultsAreFine = $individualResultsAreFine && $rawFileIsOkay && $gradedFileIsOkay;
				} else if ($results1->getExamination()->isSupplimentary())	{
					$primaryText = "Supplimentary";
					$semesterText = "N/A";
					if ($fileIsFound) $individualResultsAreFine = $individualResultsAreFine && $rawFileIsOkay && $gradedFileIsOkay;
				}
			}
			if ($results1->isResultsLocked())	{
				$statusText = "LOCKED";
			}
			
?>
			<tr>
				<td><?= $i + 1 ?></td>
				<td><?= $examinationName ?></td>
				<td><?= $primaryText ?></td>
				<td><?= $semesterText ?></td>
				<td><?= $statusText ?></td>
				<td><span style="background-color: <?= $storageColor ?>;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
				<td><?= $commentText ?></td>
			</tr>
<?php
		} //end-foreach
	}
?>						
						</tbody>
					</table>
				</div>
<!--End Summary of Individual Results-->	
<?php 
		if ($individualResultsAreFine)	{
			try {
				$group1->setExtraFilter(System::getCodeString(8));
				$group1->commitUpdate();
			} catch (Exception $e)	{
				die($e->getMessage());
			}
?>
			<br/><br/>
			<div class="ui-widget ui-widget-content">
				<div class="ui-widget-header">Finalize Results for Group <?= $group1->getExaminationGroup()->getGroupName() ?></div>
				<div>
					<div class="ui-sys-warning">
						<b>IMPORTANT : </b> You are doing this operation to the Group and its corresponding Results it contains. Make sure all the results for this group are uploaded properly. This operation will lock the entire group together with its corresponding results. This operation will have NO EFFECT on results data or grades for individual results, however it will only affect the combined Group results and Grades
					</div>
					<div>
						<form id="form1" method="POST" action="<?= $thispage ?>">
							<input type="hidden" name="page" value="manageresultsgroup_edit"/>
							<input type="hidden" name="report" value="io"/>
							<input type="hidden" name="rndx" value="<?= $group1->getExtraFilter() ?>"/>
							<input type="hidden" name="id" value="<?= $group1->getGroupId() ?>"/>
							<input type="hidden" name="service_compile" value="true"/>
							<span id="perror">&nbsp;</span>
							<div class="ui-sys-inline-controls-right">
								<input type="button" id="__add_record" value="Finalize Group Results"/>
							</div>
						</form>
					</div>
<script type="text/javascript">
(function($)	{
	$('#__add_record').on('click', function(event)	{
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
				</div>
				<div></div>
			</div>
<?php
		} else {
?>
			<div class="ui-state-error">
				Kindly review the tabular above, one or more of the results were not found, try reading on the comment area 
			</div>
<?php
		}
	} else {
?>
		<div class="ui-state-error">
			There were some problems in processing your request<br/>
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
	}/*End Group Closing Results*/else /*BEGIN Results Group Add*/if ($page == "manageresultsgroup_add" && isset($_REQUEST['report']) && Authorize::isAllowable($config, "manageresultsgroup_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$groupId = mysql_real_escape_string($_REQUEST['groupId']);
		$batchId = mysql_real_escape_string($_REQUEST['batchId']);
		$courseId = mysql_real_escape_string($_REQUEST['courseId']);
		$year = mysql_real_escape_string($_REQUEST['year']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel header ui-widget-header">Summary Report for Results Group</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	$extraFilter = System::getCodeString(8);
	$query="INSERT INTO resultsGroup (examinationGroupId, courseId, _year, batchId, extraFilter, extraInformation) VALUES('$groupId', '$courseId', '$year', '$batchId', '$extraFilter', '$extraInformation')";
	try {
		if (ResultsGroup::isResultsGroupBatchCourseAndYearAlreadyAddedInExaminationGroup($database, $conn, $groupId, $batchId, $courseId, $year)) Object::shootException("There exists a course for the submitted batch for the supplied year already attached to the Examination Group");
		mysql_db_query($database, $query, $conn) or Object::shootException("ResultsGroup[Adding]: Could not Add a Record into the System");
		$promise1->setPromise(true);
		$enableUpdate = true;
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">The Results Group has been successful added to the System</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Adding the Results Group<br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>
			</div>
			<div class="ui-sys-panel-footer">
<?php 
	$captionText = "Try Again";
	if ($enableUpdate) $captionText = "Add Another Results Group";
?>			
				<a class="ui-sys-back" href="<?= $thispage ?>?page=manageresultsgroup_add"><?= $captionText ?></a>&nbsp;&nbsp;
				<a class="ui-sys-back" href="<?= $thispage ?>">Back to Main Console Window</a>
			</div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageresultsgroup_add", "Added Results Group");
	} else if ($page == "manageresultsgroup_add" && Authorize::isAllowable($config, "manageresultsgroup_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Add a New Results Group</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="manageresultsgroup_add"/>
					<input type="hidden" name="report" value="io"/>
					<div class="pure-control-group">
						<label for="groupId">Examination Group </label>
						<select id="groupId" name="groupId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Examination Group">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = ExaminationGroup::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
?>
			<option value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="batchId" title="This is the Accademic Year this group joined at <?= $profile1->getProfileName() ?>">Batch of </label>
						<select id="batchId" name="batchId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Batch (Accademic Year this group joined the college)">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = AccademicYear::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
?>
			<option value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="courseId">Course </label>
						<select id="courseId" name="courseId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Course">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Course::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$course1 = null;
		try {
			$course1 = new Course($database, $alist1['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
?>
			<option value="<?= $course1->getCourseId() ?>" data-course-duration="<?= $course1->getDuration() ?>"><?= $course1->getCourseName() ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="year" title="The year this results will belong to">Year </label>
						<select id="year" name="year" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly select a Year this results belong to">
							<option value="_@32767@_">--select--</option>
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
			<div class="ui-sys-panel-footer">
				<a class="ui-sys-back" href="<?= $thispage ?>">Back to Main Console Window</a>
			</div>
		</div>
<script type="text/javascript">
(function($)	{
	$('#courseId').on('change', function(event)	{
		var $select1 = $(this).closest('select');
		if (! $select1.length) return;
		var $option1 = $select1.find('option:selected');
		if (! $option1.length) return;
		var duration = $option1.attr('data-course-duration');
		if (! duration) return;
		duration = parseInt(duration);
		//Getting year select 
		$select1 = $('#year');
		if (! $select1.length) return;
		$select1.empty();
		$('<option/>').attr('value', '_@32767@_').html('--select--')
			.appendTo($select1);
		for (var i = 1; i <= duration; i++)	{
			$('<option/>').attr('value', i).html(i)
				.appendTo($select1);
		}
	});
	$('#__add_record').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} /*END Results Group Add*/else /*BEGIN Results Manager*/if ($page == "manageresults" && Authorize::isAllowable($config, "manageresults", "normal", "setlog", "-1", "-1"))	{
		$promise1 = new Promise();
		$promise1->setPromise(true);
		//Define All Policies Here 
		$blnSynchronize = Authorize::isAllowable($config, "manageresults_synchronize", "normal", "donotsetlog", "-1", "-1");
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$list = null;
		try {
			$criteriaArray1 = array();
			if (isset($_REQUEST['courseId']) && intval($_REQUEST['courseId']) != 0) $criteriaArray1['resultsGroup.courseId'] = mysql_real_escape_string($_REQUEST['courseId']);
			if (isset($_REQUEST['batchId']) && intval($_REQUEST['batchId']) != 0) $criteriaArray1['resultsGroup.batchId'] = mysql_real_escape_string($_REQUEST['batchId']);
			if (isset($_REQUEST['year']) && intval($_REQUEST['year']) != 0) $criteriaArray1['resultsGroup._year'] = mysql_real_escape_string($_REQUEST['year']);
			if (isset($_REQUEST['semester']) && intval($_REQUEST['semester']) != 0) $criteriaArray1['examinationGroup.semester'] = mysql_real_escape_string($_REQUEST['semester']);
			$list = ResultsGroup::getResultsGroupListFromCriteria($database, $conn, $login1->getLoginId(), $criteriaArray1);
			if (is_null($list)) Object::shootException("There were no results found for you");
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Results Manager</div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
		$imagePath = "background-image: url('../sysimage/sprite1.png');";
		$hashText = Object::$hashText;
?>
		<!--BEGIN of Treetable-->
		<div>
			<table class="results-treetable ui-sys-treetable">
				<caption>
					<a href="#" onclick="jQuery('table.briefcase-treetable').treetable('expandAll'); return false;">Expand all</a>
					<a href="#" onclick="jQuery('table.briefcase-treetable').treetable('collapseAll'); return false;">Collapse all</a>			
				</caption>
				<thead>
					<tr>
						<th>Name</th>
						<th>Status</th>
						<th>Course</th>
						<th>Academic Year</th>
						<th>Year</th>
						<th>Semester</th>
						<th>Weight</th>
					</tr>
				</thead>
				<tbody>
<?php 
	foreach ($list as $groupId)	{
		$group1 = null;
		try {
			$group1 = new ResultsGroup($database, $groupId, $conn);
		} catch (Exception $e)	{
			continue;
		}
		$statusIcons = "";
		$courseName = "";
		if (! is_null($group1->getCourse())) $courseName = $group1->getCourse()->getCourseName();
		$academicYear = "";
		if (! is_null($group1->getBatch())) $academicYear = $group1->getBatch()->getAccademicYear();
		$groupWeight = "100%"; //By Default Group is carrying full weight group 
		$lockedText = "Not Locked";
		$publishedText = "Not Published";
		$lockedPosition = "background-position: -609px -231px;";
		$publishedPosition = "background-position: -177px -231px;";
		if ($group1->isResultsLocked())	{
			$lockedText = "Locked";
			$lockedPosition = "background-position: -479px -231px;";
		}
		if ($group1->isEligibleForPublication())	{
			//Only Locked One Can be Actually Published 
			$publishedText = "Published";
			$publishedPosition = "background-position: -155px -231px;";
		}
		$statusIcons = "<span title=\"$lockedText\" class=\"ui-icon-customized\" style=\"$imagePath $lockedPosition\">&nbsp;</span>&nbsp;&nbsp;<span title=\"$publishedText\" class=\"ui-icon-customized\" style=\"$imagePath $publishedPosition\"></span>";
		$actionIcons = "";
		$id = $group1->getGroupId();
		$class = "ResultsGroup";
		$caption = "";
		if (! is_null($group1->getExaminationGroup())) $caption = $group1->getExaminationGroup()->getGroupName();
		if ($group1->canLockResultsNow())	{
			$nextpage = $thispage."?page=manageresultsgroup_edit&service_compile=true&id=$id";
			$actionIcons .= "<a href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-compile\"></a>";
		}
		if ($group1->canUnlockResultsNow())	{
			$pos = ResultsGroup::$__IS_RESULTS_LOCKED;
			$action = 0;
			$key = $pos.$action.$class.$id.$hashText;
			$key = md5($key);			
			$nextpage = $thispage."?page=manageflagservices&pos=$pos&action=$action&class=$class&id=$id&caption=$caption&key=$key";
			$actionIcons .= "<a href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-unlock\"></a>";
		}
		if ($group1->canPublishNow())	{
			$pos = ResultsGroup::$__IS_PUBLISH_RESULTS;
			$action = 1;
			$key = $pos.$action.$class.$id.$hashText;
			$key = md5($key);			
			$nextpage = $thispage."?page=manageflagservices&pos=$pos&action=$action&class=$class&id=$id&caption=$caption&key=$key";
			$actionIcons .= "<a href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-publish\"></a>";
		}
		if ($group1->canUnpublishNow())	{
			$pos = ResultsGroup::$__IS_PUBLISH_RESULTS;
			$action = 0;
			$key = $pos.$action.$class.$id.$hashText;
			$key = md5($key);			
			$nextpage = $thispage."?page=manageflagservices&pos=$pos&action=$action&class=$class&id=$id&caption=$caption&key=$key";
			$actionIcons .= "<a href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-unpublish\"></a>";
		}
		if ($group1->canAddNewItemNow())	{
			$nextpage = $thispage."?page=manageresults_add&groupId=".$group1->getGroupId();
			$actionIcons .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a title=\"Add a New Results in a Group\" href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-add-new\"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		if ($group1->canEditNow())	{
			$nextpage = $thispage."?page=manageresultsgroup_edit&id=".$group1->getGroupId();
			$actionIcons .= "<a href=\"$nextpage\" title=\"Edit Results Group\" class=\"ui-sys-icon-80-by-16 icon-edit\"></a>";
		} 
		if ($group1->canDeleteNow())	{
			$key = $class.$id.$hashText;
			$key = md5($key);
			$nextpage = $thispage."?page=managedeleteservices&class=$class&id=$id&caption=$caption&key=$key";
			$actionIcons .= "<a href=\"$nextpage\" title=\"Edit Results Group\" class=\"ui-sys-icon-80-by-16 icon-delete\"></a>";
		}
		if ($group1->canViewResultsDetails())	{
			$folder = "results";
			$mode = Results::$__RESULTS_MODE;
			$key = $class.$id.$folder.$mode.$hashText;
			$key = md5($key);
			$classstring = strtolower($class);
			$nextpage = $thispage."?page=manageresultsviewservices&class=$class&id=$id&folder=$folder&mode=$mode&caption=$caption&key=$key";
			$actionIcons .= "<a href=\"$nextpage\" title=\"Details of the Results\" class=\"ui-sys-icon-80-by-16 icon-view\"></a>";
		}
		$semester = "All";
		if (! is_null($group1->getExaminationGroup()) && intval($group1->getExaminationGroup()->getSemester()) != 0) $semester = $group1->getExaminationGroup()->getSemester();
?>
		<tr data-tt-id="_<?= $group1->getGroupId() ?>">
			<td><span class="folder"><span><?= $caption ?></span></span></td>
			<td><?= $statusIcons ?></td>
			<td><?= $courseName ?></td>
			<td><?= $academicYear ?></td>
			<td><?= $group1->getYear() ?></td>
			<td><?= $semester ?></td>
			<td><b><?= $groupWeight ?></b></td>
		</tr>
		<!--First Child Should Carry Actions-->
		<tr data-tt-id="_a_<?= $group1->getGroupId() ?>" data-tt-parent-id="_<?= $group1->getGroupId() ?>">
			<td colspan="7">Group Actions [<?= $caption ?>] : <?= $actionIcons ?></td>
		</tr>
<?php
		//Now process individual results 
		$resultsList = ResultsGroup::getResultsBelongingToAGroup($database, $conn, $group1->getGroupId(), false);
		if (! is_null($resultsList))	{
			foreach ($resultsList as $resultsId)	{
				$results1 = new Results($database, $resultsId, $conn);
				$weight = 0;
				try {
					$weight = Examination::percentageWeightInAGroupInASemester($database, $conn, $results1->getExamination());
					$weight .= "%";
				} catch (Exception $e)	{
					$weight = "#";
				}
				$lockedText = "Not Locked";
				$publishedText = "Not Published";
				$lockedPosition = "background-position: -609px -231px;";
				$publishedPosition = "background-position: -177px -231px;";
				if ($results1->isResultsLocked())	{
					$lockedText = "Locked";
					$lockedPosition = "background-position: -479px -231px;";
				}
				if ($results1->isEligibleForPublication())	{
					//Only Locked One Can be Actually Published 
					$publishedText = "Published";
					$publishedPosition = "background-position: -155px -231px;";
				}
				$statusIcons = "<span title=\"$lockedText\" class=\"ui-icon-customized\" style=\"$imagePath $lockedPosition\">&nbsp;</span>&nbsp;&nbsp;<span title=\"$publishedText\" class=\"ui-icon-customized\" style=\"$imagePath $publishedPosition\"></span>";
				$actionIcons = "";
				$id = $results1->getResultsId();
				$class = "Results";
				$caption = "";
				if (! is_null($results1->getExamination())) $caption = $results1->getExamination()->getExaminationName();
				if ($results1->canLockResultsNow())	{
					$pos = Results::$__IS_RESULTS_LOCKED;
					$action = 1;
					$key = $pos.$action.$class.$id.$hashText;
					$key = md5($key);			
					$nextpage = $thispage."?page=manageflagservices&pos=$pos&action=$action&class=$class&id=$id&caption=$caption&key=$key";
					$actionIcons .= "<a href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-lock\"></a>";
				}
				if ($results1->canUnlockResultsNow())	{
					$pos = Results::$__IS_RESULTS_LOCKED;
					$action = 0;
					$key = $pos.$action.$class.$id.$hashText;
					$key = md5($key);			
					$nextpage = $thispage."?page=manageflagservices&pos=$pos&action=$action&class=$class&id=$id&caption=$caption&key=$key";
					$actionIcons .= "<a href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-unlock\"></a>";
				}
				if ($results1->canPublishNow())	{
					$pos = Results::$__IS_PUBLISH_RESULTS;
					$action = 1;
					$key = $pos.$action.$class.$id.$hashText;
					$key = md5($key);			
					$nextpage = $thispage."?page=manageflagservices&pos=$pos&action=$action&class=$class&id=$id&caption=$caption&key=$key";
					$actionIcons .= "<a href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-publish\"></a>";
				}
				if ($results1->canUnpublishNow())	{
					$pos = Results::$__IS_PUBLISH_RESULTS;
					$action = 0;
					$key = $pos.$action.$class.$id.$hashText;
					$key = md5($key);			
					$nextpage = $thispage."?page=manageflagservices&pos=$pos&action=$action&class=$class&id=$id&caption=$caption&key=$key";
					$actionIcons .= "<a href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-unpublish\"></a>";
				}
				if ($results1->canNullifyNow())	{
					$folder = "results";
					$key = $class.$id.$folder.$hashText;
					$key = md5($key);
					$nextpage = $thispage."?page=managenullificationservices&class=$class&id=$id&caption=$caption&folder=$folder&key=$key";
					$actionIcons .= "<a title=\"Undo results upload\" href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-nullify\"></a>";
				} 
				if ($results1->canEditNow())	{
					$nextpage = $thispage."?page=manageresults_edit&id=$id";
					$actionIcons .= "<a title=\"Edit Results\" href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-edit\"></a>";
				}
				if ($results1->canDeleteNow())	{
					$key = $class.$id.$hashText;
					$key = md5($key);
					$nextpage = $thispage."?page=managedeleteservices&class=$class&id=$id&caption=$caption&key=$key";
					$actionIcons .= "<a href=\"$nextpage\" title=\"Delete Results\" class=\"ui-sys-icon-80-by-16 icon-delete\"></a>";
				}
				if ($results1->canUploadNow())	{
					$folder = "results";
					$mode = Results::$__RESULTS_MODE;
					$key = $class.$id.$folder.$mode.$hashText;
					$key = md5($key);
					$nextpage = $thispage."?page=manageresultsuploadservices&class=$class&id=$id&folder=$folder&mode=$mode&caption=$caption&key=$key";
					$actionIcons .= "<a href=\"$nextpage\" title=\"Upload to Briefcase\" class=\"ui-sys-icon-80-by-16 icon-upload\"></a>";
				}
				if ($results1->canViewResultsDetails())	{
					$folder = "results";
					$mode = Results::$__RESULTS_MODE;
					$key = $class.$id.$folder.$mode.$hashText;
					$key = md5($key);
					$classstring = strtolower($class);
					$nextpage = $thispage."?page=manageresultsviewservices&class=$class&id=$id&folder=$folder&mode=$mode&caption=$caption&key=$key";
					$actionIcons .= "<a href=\"$nextpage\" title=\"Results Details for Briefcase\" class=\"ui-sys-icon-80-by-16 icon-view\"></a>";
				}
				$semester = "All";
				if (! is_null($results1->getExamination())) $semester = $results1->getExamination()->getSemester();
?>
					<tr data-tt-id="_<?= $results1->getResultsId() ?>_<?= $group1->getGroupId() ?>" data-tt-parent-id="_<?= $group1->getGroupId() ?>">
						<td><span class="file"><?= $caption ?></span></td>
						<td><?= $statusIcons ?></td>
						<td><?= $courseName ?></td>
						<td><?= $academicYear ?></td>
						<td><?= $group1->getYear() ?></td>
						<td><?= $semester ?></td>
						<td><?= $weight ?></td>
					</tr>
					<tr data-tt-id="_b_<?= $results1->getResultsId() ?>_<?= $group1->getGroupId() ?>" data-tt-parent-id="_<?= $results1->getResultsId() ?>_<?= $group1->getGroupId() ?>">
						<td colspan="7">Results Actions [<?= $caption ?>] : <?= $actionIcons ?></td>
					</tr>
<?php
			}
		}
	}
?>				
				</tbody>
				<tfoot></tfoot>
			</table>
		</div>
		<!--END of Treetable-->
<?php
	} else {
?>
		<div class="ui-state-highlight">
			It seems you do not have any results, make sure the Results Synchronization Process has been performed for this Academic Year
		</div>
<?php
	}
?>
				<!--Add A New Results Button Should be here-->
				<div class="ui-sys-inline-controls-right">
<?php 
	if ($blnSynchronize)	{
?>
		<a class="menu-icon" title="Synchronize Results inline with the corresponding Examination Definition" href="<?= $thispage ?>?page=manageresults_synchronize"><img alt="DAT" src="../sysimage/synchronize.png"/></a>
<?php 
	}
?>
				</div>
			</div>
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>">Back to Main Console Window</a></div>
		</div>
<?php
		mysql_close($conn);
	}/*END Results Manager*/ else /*BEGIN Results View Services*/if ($page == "manageresultsviewservices" && isset($_REQUEST['service_view_results']) && isset($_REQUEST['paperId']) && isset($_REQUEST['class']) && isset($_REQUEST['id']) && isset($_REQUEST['folder']) && isset($_REQUEST['mode']) && isset($_REQUEST['caption']) && isset($_REQUEST['key']) && Authorize::isAllowable($config, "manage".strtolower($_REQUEST['class'])."_detail", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$captionText = $_REQUEST['caption'];
		$object1 = null;
		$paper1 = null;
		$window1 = "";
		try {
			$key = md5($_REQUEST['class'].$_REQUEST['id'].$_REQUEST['folder'].$_REQUEST['mode'].Object::$hashText);
			if ($key != $_REQUEST['key']) Object::shootException("Perhaps Data is Compromised");
			$object1 = ClassRegistry::getObjectReference($database, $conn, $_REQUEST['class'], $_REQUEST['id']);
			$paper1 = new Paper($database, $_REQUEST['paperId'], $conn);
			//Setting Parametes 
			$object1->putData('lineHeight1', $profile1->getProfileName());
			$object1->putData('lineHeight2', Object::$generalProfileHeader);
			$object1->putData('captionText', $captionText);
			$object1->putData('folder', "../data/".$_REQUEST['folder']."/");
			$object1->putData('logo', "../data/profile/logo/".$profile1->getLogo());
			$window1 = Results::showResultsUI($database, $conn, $object1, $paper1, $systemTime1, $_REQUEST['mode']);
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">View Results <?= $captionText ?></div>
			<div class="ui-sys-panel-body" style="background-color: black; overflow-x: scroll; padding: 5px;">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-sys-float-right"><a href="#" class="menu-icon menu-icon-print" data-content-container="__view_results_services"><img title="Click Here to Print" alt="PRT" src="../sysimage/print.png"/></a><a href="<?= $thispage ?>?page=manageexamination_signing_sheet&pdfdownload=true&courseId=<?= $_REQUEST['courseId'] ?>&year=<?= $_REQUEST['year'] ?>&semester=<?= $_REQUEST['semester'] ?>&paperId=<?= $_REQUEST['paperId'] ?>&controlFlags=<?= $controlFlags ?>" class="menu-icon" data-content-container="__examination_signing_sheet"><img title="Click to Dowload Pdf File" alt="PDF" src="../sysimage/pdf.png"/></a></div>
		<div class="ui-sys-clear-both">&nbsp;</div>
		<div style="background-color: black; overflow-x: scroll; padding: 5px;">
<?php 
	echo $window1;
?>		
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were some problems in displaying the results <br/>
			Details : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>				
			</div>
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>">Back to Home Page</a></div>
		</div>
<?php
		mysql_close($conn);
	} else if ($page == "manageresultsviewservices" && isset($_REQUEST['class']) && isset($_REQUEST['id']) && isset($_REQUEST['folder']) && isset($_REQUEST['mode']) && isset($_REQUEST['caption']) && isset($_REQUEST['key']) && Authorize::isAllowable($config, "manage".strtolower($_REQUEST['class'])."_detail", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$captionText = $_REQUEST['caption'];
		try {
			$key = md5($_REQUEST['class'].$_REQUEST['id'].$_REQUEST['folder'].$_REQUEST['mode'].Object::$hashText);
			if ($key != $_REQUEST['key']) Object::shootException("Perhaps Data is Compromised");	
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Paper Selection for <?= $captionText ?></div>
			<div class="ui-sys-panel-body ui-sys-data-capture ui-sys-inline-controls-center">
<?php 
	if ($promise1->isPromising())	{
		//Now you are free to proceed 
?>
			<div>
		<form id="form1" class="pure-form ui-sys-data-capture" method="POST" action="<?= $thispage ?>">
			<input type="hidden" name="page" value="manageresultsviewservices"/>
			<input type="hidden" name="service_view_results" value="true"/>
			<input type="hidden" name="class" value="<?= $_REQUEST['class'] ?>"/>
			<input type="hidden" name="id" value="<?= $_REQUEST['id'] ?>"/>
			<input type="hidden" name="folder" value="<?= $_REQUEST['folder'] ?>"/>
			<input type="hidden" name="mode" value="<?= $_REQUEST['mode'] ?>"/>
			<input type="hidden" name="key" value="<?= $_REQUEST['key'] ?>"/>
			<input type="hidden" name="caption" value="<?= $_REQUEST['caption'] ?>"/>
			<div class="ui-sys-warning">
				<b>Please Note</b> : The type of paper you are selecting will affect both presentatio view and printing view.
			</div>
			<label>Select Printing Paper <select name="paperId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Paper">
				<option value="_@32767@_">--select--</option>
<?php 
	$list = Paper::loadAllData($database, $conn);
	foreach ($list as $alist)	{
?>
		<option value="<?= $alist['id'] ?>"><?= $alist['val'] ?></option>
<?php
	}
?>
			</select></label>&nbsp;&nbsp;&nbsp;&nbsp; <input type="button" value="Continue" id="__add_record"/>
		</form>
			</div>
			<div class="ui-sys-error-message" id="perror"></div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were some problems in displaying the results <br/>
			Details : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>
				
			</div>
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>">Back to Home Page</a></div>
		</div>
<script type="text/javascript">
	$('#__add_record').on('click', function(event)	{
		generalFormSubmission(this, 'form1', 'perror');
	});
</script>
<?php
		mysql_close($conn);
	}/*END Results View Services*/ else/*BEGIN Attach List of Owners*/if ($page == "managebriefcasegroup_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['detach_login']) && Authorize::isAllowable($config, "managebriefcasegroup_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$briefcaseGroup1 = null;
		$headerText = "UNKNOWN";
		try {
			$briefcaseGroup1 = new BriefcaseGroup($database, $_REQUEST['id'], $conn);
			$headerText = "De-Assign Event Creators Map from ".$briefcaseGroup1->getGroupName();
			if (! isset($_REQUEST['did'])) Object::shootException("There were nothing to Attach");
			$listToUpdate = Object::detachIdListFromObjectList($briefcaseGroup1->getListOfOwners(), $_REQUEST['did']);
			//Note The Primary Owner can not be Detached 
			$collection1 = new Collection();
			$collection1->addCommaSeparatedList($listToUpdate);
			$primaryOwner1 = $briefcaseGroup1->getLogin();
			if (is_null($primaryOwner1)) Object::shootException("Primary Owner were not found");
			if (! $collection1->isItemInACollection($primaryOwner1->getLoginId())) Object::shootException("The Primary Owner Can not be removed");
			$briefcaseGroup1->setListOfOwners($listToUpdate);
			$briefcaseGroup1->commitUpdate();
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
			Owner List Attachment were removed successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in De-Attaching Owner List <br/>
			Details: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>">Back to Home Page</a></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managebriefcasegroup_edit", "Detach Owners for ".$briefcaseGroup1->getGroupName());
	} else if ($page == "managebriefcasegroup_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['attach_login']) && Authorize::isAllowable($config, "managebriefcasegroup_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$briefcaseGroup1 = null;
		$headerText = "UNKNOWN";
		try {
			$briefcaseGroup1 = new BriefcaseGroup($database, $_REQUEST['id'], $conn);
			$headerText = "Assign Owner Map to ".$briefcaseGroup1->getGroupName();
			if (! isset($_REQUEST['did'])) Object::shootException("There were nothing to Attach");
			$listToUpdate = Object::attachIdListToObjectList($briefcaseGroup1->getListOfOwners(), $_REQUEST['did']);
			$briefcaseGroup1->setListOfOwners($listToUpdate);
			$briefcaseGroup1->commitUpdate();
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
			Owner List Attachment were done successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Attaching Owner List <br/>
			Details: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>">Back to Home Page</a></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managebriefcasegroup_edit", "Attach Owners for ".$briefcaseGroup1->getGroupName());
	} else if ($page == "managebriefcasegroup_edit" && isset($_REQUEST['id']) && isset($_REQUEST['attach_login']) && Authorize::isAllowable($config, "managebriefcasegroup_edit", "normal", "setlog", "-1", "-1")) {
		$conn  = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$briefcaseGroup1 = null;
		$headerText = "UNKNOWN";
		try {
			$briefcaseGroup1 = new BriefcaseGroup($database, $_REQUEST['id'], $conn);
			$headerText = "Assign Owner Map to ".$briefcaseGroup1->getGroupName();
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
	if (! is_null($briefcaseGroup1->getListOfOwners()) && sizeof($briefcaseGroup1->getListOfOwners()) > 0)	{
		$objectList1 = $briefcaseGroup1->getListOfOwners();
?>
			<form method="POST" action="<?= $thispage ?>">
				<input type="hidden" name="page" value="managebriefcasegroup_edit"/>
				<input type="hidden" name="id" value="<?= $briefcaseGroup1->getGroupId() ?>"/>
				<input type="hidden" name="report" value="true"/>
				<input type="hidden" name="detach_login" value="true"/>
				<table class="pure-table pure-table-aligned ui-sys-table-search-results ui-sys-table-search-results-1">
					<thead>
						<tr>
						<th colspan="6">BriefcaseGroupd By</th>
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
					<input type="submit" value="Detach Owners"/>
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
				<div class="ui-sys-query-container">
					<form id="form1" class="pure-form" method="POST" action="<?= $thispage ?>">
						<input type="hidden" name="page" value="managebriefcasegroup_edit"/>
						<input type="hidden" name="id" value="<?= $briefcaseGroup1->getGroupId() ?>"/>
						<input type="hidden" name="searchtext" value="dnd"/>
						<input type="hidden" name="attach_login" value="true"/>
						<fieldset class="ui-sys-data-capture">
							<legend>Student Search</legend>
							<div class="pure-g">
								<div class="pure-u-1 pure-u-md-1-3">
									<label for="firstname">First Name</label>
									<input type="text" id="firstname" name="firstname" class="pure-u-23-24" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="First Name : <?= $msgA32Name ?>"/>
								</div>
								<div class="pure-u-1 pure-u-md-1-3">
									<label for="middlename">Middle Name</label>
									<input type="text" id="middlename" name="middlename" class="pure-u-23-24" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Middle Name : <?= $msgA32Name ?>"/>
								</div>
								<div class="pure-u-1 pure-u-md-1-3">
									<label for="lastname">Last Name</label>
									<input type="text" id="lastname" name="lastname" class="pure-u-23-24" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Last Name : <?= $msgA32Name ?>"/>
								</div>
							</div>
							<div class="ui-sys-error-message" id="perror"></div>
						</fieldset>
					</form>
					<div class="ui-sys-inline-controls-right" style="font-size: 1.1em;">
						<input id="__add_record" type="button" value="Fetch Results"/>
					</div> 
				</div>
<script type="text/javascript">
(function($)	{
	$(function()	{
		$('#__add_record').on('click', function(event)	{
			generalFormSubmission(this, 'form1', 'perror');
		});
	});
})(jQuery);
</script>
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
		<input type="hidden" name="page" value="managebriefcasegroup_edit"/>
		<input type="hidden" name="id" value="<?= $briefcaseGroup1->getGroupId() ?>"/>
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
	//Collecting the query
	$collection1 = new Collection();
	if (trim($_REQUEST['firstname']) != "") { $firstname = mysql_real_escape_string($_REQUEST['firstname']); $collection1->addItem("login.firstname='$firstname'"); }
	if (trim($_REQUEST['middlename']) != "") { $middlename = mysql_real_escape_string($_REQUEST['middlename']); $collection1->addItem("login.middlename='$middlename'"); }
	if (trim($_REQUEST['lastname']) != "") { $lastname = mysql_real_escape_string($_REQUEST['lastname']); $collection1->addItem("login.lastname='$lastname'"); }
	$collectionList = $collection1->getCollection();
	if (sizeof($collectionList) > 0)	{
		$itemCount = 0;
		foreach ($collectionList as $item)	{
			if ($itemCount == 0) $query .= " WHERE ".$item;
			else $query .= " AND ".$item;
			$itemCount++;
		}
	}
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
		if (true)	{ //For Complience only
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
			<input type="submit" value="Attach Owners"/>
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
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>">Back to Home Page</a></div>
		</div>
<?php
		mysql_close($conn);
	} /*END Attach List of Owners*/ else/*BEGIN Briefcase Compilation Tool*/if ($page == "managebriefcasegroup_edit" && isset($_REQUEST['service_compile']) && isset($_REQUEST['report']) && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managebriefcasegroup_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$group1 = null;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$enableUpdate = false;
		try {
			$group1 = new BriefcaseGroup($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Results Compilation Tool <br/><?= $group1->getGroupName() ?></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	$list = null;
	try {
		$targetMaximumScore = mysql_real_escape_string($_REQUEST['maximumScore']);
		$list = BriefcaseGroup::getBriefcaseListBelongingToAGroup($database, $conn, $group1->getGroupId());
		if (is_null($list)) Object::shootException("There is no briefcase belonging to this group");
		$bestCount = 0;
		if (intval($_REQUEST['radAll']) == 1)	{
			$bestCount = sizeof($list);
		} else if (intval($_REQUEST['radAll']) == 0)	{
			if (! isset($_REQUEST['bestCount'])) Object::shootException("The number of briefcase results to take were not found");
			$bestCount = intval($_REQUEST['bestCount']);
			//Best Count can not exceed the total number of briefcase 
			if ($bestCount > sizeof($list)) $bestCount = sizeof($list);
			//Initialize the list is null, the ones which must be included 
			$list = null;
			if (isset($_REQUEST['briefcase']))	{
				$list = array();
				foreach ($_REQUEST['briefcase'] as $briefcaseId)	{
					$list[sizeof($list)] = $briefcaseId;
				}
				//bestCount can not be less than the briefcase which must be included 
				if ($bestCount < sizeof($list)) $bestCount = sizeof($list);
			} //--briefcase --if-end
		} else {
			Object::shootException("Mixed Instruction, Could not decide if all or some of briefcase should be taken");
		}
		$promise1 = BriefcaseGroup::compileBriefcaseGroup($database, $conn, $targetMaximumScore, $systemTime1, $group1->getGroupId(), "../data/briefcase/", $list, $bestCount, 0);
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
		$enableUpdate = false;
	}
	if ($promise1->isPromising())	{
		$enableUpdate = true;
?>
		<div class="ui-state-highlight">
			You have successful compiled your briefcase results
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in compiling your briefcase <br/>
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
		$groupName = $group1->getGroupName();
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managebriefcasegroup_edit", "Compiled [$groupName] Results");
	} else if ($page == "managebriefcasegroup_edit" && isset($_REQUEST['service_compile']) && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managebriefcasegroup_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$group1 = null;
		$promise1 = new Promise();
		$promise1->setPromise(true);
		try {
			$group1 = new BriefcaseGroup($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Results Compilation Tool <br/><?= $group1->getGroupName() ?></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	$list = null;
	try {
		$list = BriefcaseGroup::getBriefcaseListBelongingToAGroup($database, $conn, $group1->getGroupId());
		if (is_null($list)) Object::shootException("There is no briefcase belonging to this group");
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
	if (! is_null($list) && $promise1->isPromising())	{
		$isStillValidBriefcase = true;
		$briefcaseFolder = "../data/briefcase/";
?>
			<form id="form1" method="POST" action="<?= $thispage ?>">
				<input type="hidden" name="page" value="managebriefcasegroup_edit"/>
				<input type="hidden" name="service_compile" value="<?= $_REQUEST['service_compile'] ?>"/>
				<input type="hidden" name="report" value="io"/>
				<input type="hidden" name="id" value="<?= $group1->getGroupId() ?>"/>
		<table class="pure-table pure-table-horizontal font-family-1 font-size-0-8-em" style="width: 100%;">
			<thead>
				<tr>
					<th style="ui-sys-inline-controls-center" colspan="7">Compilation Tool for <?= $group1->getGroupName() ?></th>
				</tr>
				<tr>
					<th></th>
					<th>S/N</th>
					<th>Briefcase Name</th>
					<th>Weight (Ratio)</th>
					<th>Status</th>
					<th>Storage</th>
					<th>Comment</th>
				</tr>
			</thead>
			<tbody>
<?php 
	$count = 0;
	foreach ($list as $briefcaseId)	{
		$briefcase1 = null;
		try	{
			$briefcase1 = new Briefcase($database, $briefcaseId, $conn);
		} catch (Exception $e)	{
			$isStillValidBriefcase = false;
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
			break;
		}
		$lockedText = "OPEN";
		if ($briefcase1->isResultsLocked()) $lockedText = "LOCKED";
		$bgcolor = "red";
		$commentText = "Data Files Are Missing";
		$rawFilename = $briefcaseFolder.$briefcase1->getRawMarksCSVFile();
		$gradedFilename = $briefcaseFolder.$briefcase1->getGradedMarksCSVFile();
		$blnFound = false;
		if (! is_null($briefcase1->getRawMarksCSVFile()) && ! is_null($briefcase1->getGradedMarksCSVFile()))	{
			$passedIntegrity = false;
			if (file_exists($rawFilename) && (md5_file($rawFilename) == $briefcase1->getRawMarksCSVFileChecksum()))	{
				$commentText = "Integrity Check has PASSED";
				$bgcolor = "blue";
				$passedIntegrity = true;
			} else {
				$commentText = "[Data] Integrity is Compromised";
				$bgcolor = "gold";
				$passedIntegrity = false;
			}
			if ($passedIntegrity && file_exists($gradedFilename) && (md5_file($gradedFilename) == $briefcase1->getGradedMarksCSVFileChecksum()))	{
				$commentText = "Integrity Check has PASSED";
				$bgcolor = "blue";
			} else if ($passedIntegrity) {
				$commentText = "[Graded] Integrity is Compromised";
				$bgcolor = "gold";
				$passedIntegrity = false;
			}
			$blnFound = $passedIntegrity;
		}
		$isStillValidBriefcase = $isStillValidBriefcase && $blnFound;
?>
		<tr>
			<td><input class="control_select" checked type="checkbox" name="briefcase[<?= $count ?>]" value="<?= $briefcase1->getBriefcaseId() ?>"/></td>
			<td><?= $count + 1 ?></td>
			<td><?= $briefcase1->getBriefcaseName() ?></td>
			<td><?= $briefcase1->getWeight() ?></td>
			<td><?= $lockedText ?></td>
			<td><span style="background-color: <?= $bgcolor ?>;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
			<td><?= $commentText ?></td>
		</tr>
<?php
		$count++;
	} //end-foreach
?>			
			</tbody>
			<tfoot>
				<tr><td colspan="7">&nbsp;</td></tr>
				<tr>
					<td></td>
					<td class="ui-sys-inline-controls-center" colspan="5"><label>Maximum Score <input name="maximumScore" type="text" size="3" class="text-can-not-be-zero" required pattern="<?= $expr4Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr4Number ?>" validate_message="Maximum Score : <?= $msg4Number ?>"/></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label><input checked class="radAll" type="radio" name="radAll" value="1"/> Include All</label>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<span style="background-color: #e1e1e1;"><label><input class="radAll" type="radio" name="radAll" value="0"/>Best &nbsp;&nbsp;</label><input disabled class="text-can-not-be-zero bestCount" type="text" size="2" name="bestCount" placeholder="1" required pattern="<?= $expr2Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr2Number ?>" validate_message="Include best <?= $msg2Number ?>"/></span></td>
					<td></td>
				</tr>
				<tr>
					<td colspan="7" class="ui-sys-error-message" id="perror"></td>
				</tr>
			</tfoot>
		</table>
			</form>
<?php
		if ($isStillValidBriefcase)	{
?>
			<div class="ui-sys-inline-controls-right">
				<input type="button" id="__add_record" value="Compile"/>
			</div>
<?php	
		} else {
?>
			<div class="ui-state-error">
				There were problem on initial compilation process, kindly review and check on the comment area of each briefcase
			</div>
<?php
		}
	} else {
?>
		<div class="ui-state-error">
			Perhaps the Group is Empty <br/>
			Details : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"></div>
		</div>
<script type="text/javascript">
(function($)	{
	$(function()	{
		$('input.radAll').on('change', function(event)	{
			if (parseInt($(this).val()) == 1)	{
				$('input.control_select').prop('checked', true);
				$('input.bestCount').prop('disabled', true);
			} else {
				$('input.control_select').prop('checked', false);
				$('input.bestCount').prop('disabled', false);
			}
		});
		$('#__add_record').on('click', function(event)	{
			var $target1 = $('#perror');
			if (! $target1.length) return;
			var enableSubmit = true;
			$('input.text-can-not-be-zero').each(function(i, v)	{
				var $text1 = $(v);
				if (! $text1.prop('disabled')) enableSubmit = enableSubmit && (parseInt($text1.val()) != 0);
			});
			if (enableSubmit)	{
				generalFormSubmission(this, 'form1', 'perror');
			} else {
				$target1.empty();
				$('<span/>').html('Neither Best Count nor Maximum Score can be Zero')
					.appendTo($target1);
			}
		});
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} /*END Briefcase Compilation Tool*/ else/*BEGIN Results Upload Services*/if ($page == "manageresultsuploadservices" && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && isset($_REQUEST['class']) && isset($_REQUEST['id']) && isset($_REQUEST['folder']) && isset($_REQUEST['mode']) && isset($_REQUEST['caption']) && isset($_REQUEST['key']) && Authorize::isAllowable($config, "manage".strtolower($_REQUEST['class'])."_edit", "normal", "setlog", "-1", "-1") && Authorize::isAllowable($config, "manageresults_upload", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$object1 = null; /* Just confirm with canUploadNow */
		$captionText = $_REQUEST['caption'];
		$contextName = "manageresults_upload";
		$className = $_REQUEST['class'];
		$enableUpdate = false;
		try {
			$key = md5($_REQUEST['class'].$_REQUEST['id'].$_REQUEST['folder'].$_REQUEST['mode'].Object::$hashText);
			if ($key != $_REQUEST['key']) Object::shootException("Perhaps Data is Compromised");
			$object1 = ClassRegistry::getObjectReference($database, $conn, $_REQUEST['class'], $_REQUEST['id']);
			if (is_null($object1)) Object::shootException("Class could not be found in the Registry");
			if (trim($_REQUEST['rndx']) != $object1->getExtraFilter()) Object::shootException("The System has detected you are replaying with your browser");
			if (! $object1->canUploadNow()) Object::shootException("The System can not Allow results Upload now for [$captionText]");
			$promise1 = Object::checkUploadedFile($_FILES['resultsfile'], array("text/csv"), array("csv"), 2097153);
			if (! $promise1->isPromising()) Object::shootException($promise1->getReason()); //Promise->results contain filename
			$object1->setExtraFilter(System::getCodeString(8));
			$folder = "../data/".$_REQUEST['folder']."/";
			$promise1 = Results::uploadResults($database, $conn, $object1, $promise1->getResults(), $folder, $systemTime1, isset($_REQUEST['overwrite']), $_REQUEST['mode']);
			$enableUpdate = true;
		} catch (Exception $e)	{
			$enableUpdate = false;
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">RESULTS UPLOAD TOOL FOR <?= $captionText ?></div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			You have successful uploaded results for <?= $captionText ?>
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were a problem in uploading results for <?= $captionText ?> <br/>
			Details : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>?page=manage<?= $_REQUEST['class'] ?>">Back to <?= $captionText ?></a></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "manageresults_upload", "Uploaded [$className], Results");
	} else if ($page == "manageresultsuploadservices" && isset($_REQUEST['class']) && isset($_REQUEST['id']) && isset($_REQUEST['folder']) && isset($_REQUEST['mode']) && isset($_REQUEST['caption']) && isset($_REQUEST['key']) && Authorize::isAllowable($config, "manage".strtolower($_REQUEST['class'])."_edit", "normal", "setlog", "-1", "-1") && Authorize::isAllowable($config, "manageresults_upload", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$object1 = null; /* Just confirm with canUploadNow */
		$captionText = $_REQUEST['caption'];
		$group1 = null; 
		$defaultPage = "manage".strtolower($_REQUEST['class']);
		try {
			$key = md5($_REQUEST['class'].$_REQUEST['id'].$_REQUEST['folder'].$_REQUEST['mode'].Object::$hashText);
			if ($key != $_REQUEST['key']) Object::shootException("Perhaps Data is Compromised");
			$object1 = ClassRegistry::getObjectReference($database, $conn, $_REQUEST['class'], $_REQUEST['id']);
			if (is_null($object1)) Object::shootException("Class could not be found in the Registry");
			$group1 = $object1->getGroup();
			if (! $object1->canUploadNow()) Object::shootException("The System can not Allow results Upload now for [$captionText]");
			$object1->setExtraFilter(System::getCodeString(8));
			$object1->commitUpdate();	
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">RESULTS UPLOAD TOOL FOR <?= $captionText ?></div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-sys-data-capture">
			<form id="form1" method="POST" action="<?= $thispage ?>" enctype="multipart/form-data">
				<input type="hidden" name="page" value="manageresultsuploadservices"/>
				<input type="hidden" name="class" value="<?= $_REQUEST['class'] ?>"/>
				<input type="hidden" name="id" value="<?= $_REQUEST['id'] ?>"/>
				<input type="hidden" name="mode" value="<?= $_REQUEST['mode'] ?>"/>
				<input type="hidden" name="caption" value="<?= $_REQUEST['caption'] ?>"/>
				<input type="hidden" name="folder" value="<?= $_REQUEST['folder'] ?>"/>
				<input type="hidden" name="key" value="<?= $_REQUEST['key'] ?>"/>
				<input type="hidden" name="report" value="1"/>
				<input type="hidden" name="rndx" value="<?= $object1->getExtraFilter() ?>"/>
				<div class="ui-sys-data-capture ui-sys-inline-controls-center">
					<input class="ui-sys-file-upload" id="resultsfile" data-capture="0" type="file" name="resultsfile" accept="*.csv"/> &nbsp;&nbsp;<input type="button" id="__add_record" value="Upload Results"/> 
				</div>
				<div class="pure-controls ui-sys-inline-controls-center">
					<label><input type="checkbox" name="overwrite" value="1"/>&nbsp;&nbsp; Overwrite existing results (If Any)?</label>
				</div>
				<div class="ui-sys-error-message" id="perror"></div>
			</form>
		</div>
<?php
	} else { 
?>
		<div class="ui-state-error">
			There were problems in initializing the Results Upload tool <br/>
			Details: <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>?page=<?= $defaultPage ?>">Back to <?= $captionText ?></a></div>
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
	}/*END Results Upload Services*/else /*BEGIN Nullification*/if ($page == "managenullificationservices" && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && isset($_REQUEST['class']) && isset($_REQUEST['id']) && isset($_REQUEST['caption']) && isset($_REQUEST['folder']) && isset($_REQUEST['key']) && Authorize::isAllowable($config, "manage".strtolower($_REQUEST['class'])."_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$object1 = null;
		$captionText = $_REQUEST['caption'];
		$enableUpdate = false;
		try {
			$key = md5($_REQUEST['class'].$_REQUEST['id'].$_REQUEST['folder'].Object::$hashText);
			if ($key != $_REQUEST['key']) Object::shootException("Perhaps, data is compromised");
			$object1 = ClassRegistry::getObjectReference($database, $conn, $_REQUEST['class'], $_REQUEST['id']);
			if (is_null($object1)) Object::shootException("Class could not be found in the Registry");
			if (trim($_REQUEST['rndx']) != $object1->getExtraInformation()) Object::shootException("The system has detected a replay on your browser window");
			$folder = "../".$_REQUEST['folder']."/";
			$object1->setExtraInformation($folder);
			$object1->nullifyMe();
			$enableUpdate = true;
		} catch (Exception $e)	{
			$enableUpdate = false;
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">RESULTS NULLIFICATION TOOL <br/>(<?= $captionText ?>)</div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Nullification process [<?= $captionText ?>] has being completed successful 
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problem in Nullifying results for <?= $captionText ?> <br/>
			Details : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>">Back to Main Page</a></div>
		</div>
<?php
		mysql_close($conn);
		$contextName = "manage".strtolower($_REQUEST['class'])."_edit";
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), $contextName, "Edited [Nullify] ".$captionText);
	} else if ($page == "managenullificationservices" && isset($_REQUEST['class']) && isset($_REQUEST['id']) && isset($_REQUEST['caption']) && isset($_REQUEST['folder']) && isset($_REQUEST['key']) && Authorize::isAllowable($config, "manage".strtolower($_REQUEST['class'])."_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$object1 = null;
		$captionText = $_REQUEST['caption'];
		try {
			$key = md5($_REQUEST['class'].$_REQUEST['id'].$_REQUEST['folder'].Object::$hashText);
			if ($key != $_REQUEST['key']) Object::shootException("Perhaps, data is compromised");
			$object1 = ClassRegistry::getObjectReference($database, $conn, $_REQUEST['class'], $_REQUEST['id']);
			if (is_null($object1)) Object::shootException("Class could not be found in the Registry");
			$object1->setExtraFilter(System::getCodeString(8));
			$object1->commitUpdate();
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">RESULTS NULLIFICATION TOOL <br/>(<?= $captionText ?>)</div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-sys-data-capture">
			<form method="POST" action="<?= $thispage ?>">
				<input type="hidden" name="page" value="managenullificationservices"/>
				<input type="hidden" name="class" value="<?= $_REQUEST['class'] ?>"/>
				<input type="hidden" name="id" value="<?= $_REQUEST['id'] ?>"/>
				<input type="hidden" name="caption" value="<?= $_REQUEST['caption'] ?>"/>
				<input type="hidden" name="folder" value="<?= $_REQUEST['folder'] ?>"/>
				<input type="hidden" name="key" value="<?= $_REQUEST['key'] ?>"/>
				<input type="hidden" name="report" value="1"/>
				<input type="hidden" name="rndx" value="<?= $object1->getExtraFilter() ?>"/>
				<div class="ui-sys-inline-controls-center">
					<div class="ui-sys-warning">
						You are about to nullify results completely <?= $captionText ?> from the system. This action might not be reversed. Are you sure you want to proceed? <br/><br/>
					</div>
					<div>
						<input type="submit" value="Yes (Proceed)"/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="button-link" href="<?= $thispage ?>">No (Cancel)</a>
					</div>
				</div>
			</form>
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problem in nullifying results for <?= $captionText ?> <br/>
			Details : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>">Back to Main Page</a></div>
		</div>
<?php
		mysql_close($conn);
	}/*Services END Nullification Services*/else/*BEGIN Briefcase Edit*/if ($page == "managebriefcase_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managebriefcase_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$briefcase1 = null;
		try {
			$briefcase1 = new Briefcase($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$promise1 = new Promise();
		$promise1->setReason("Nothing Has been Updated");
		$enableUpdate = false;
		$briefcaseName = mysql_real_escape_string($_REQUEST['briefcaseName']);
		$groupId = mysql_real_escape_string($_REQUEST['groupId']);
		$weight = mysql_real_escape_string($_REQUEST['weight']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
		if ($briefcaseName != $briefcase1->getBriefcaseName())	{
			$briefcase1->setBriefcaseName($briefcaseName); $enableUpdate = true;
		}
		if (is_null($briefcase1->getGroup()) || ($groupId != $briefcase1->getGroup()->getGroupId()))	{
			$briefcase1->setGroup($groupId); $enableUpdate = true;
		}
		if ($weight != $briefcase1->getWeight())	{
			$briefcase1->setWeight($weight); $enableUpdate = true;
		}
		if ($extraInformation != $briefcase1->getExtraInformation())	{
			$briefcase1->setExtraInformation($extraInformation); $enableUpdate = true;
		}
		//This is the Last 
		if (trim($_REQUEST['rndx']) != $briefcase1->getExtraFilter())	{
			$enableUpdate = false;
			$promise1->setReason("The System has Detected you are replaying data in your Browser window");
			$promise1->setPromise(false);
		}
		//Now proceed with general find a new value of extra Filter 
		$briefcase1->setExtraFilter(System::getCodeString(8));
		if ($enableUpdate)	{
			try {
				if (is_null($briefcase1->getGroup()) || ! $briefcase1->getGroup()->isAuthorizedOwner($login1->getLoginId())) Object::shootException("You are not the Authorized Owner of the briefcase");
				$briefcase1->commitUpdate();
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
			<div class="ui-sys-panel-header ui-widget-header">Briefcase Editing Report</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Briefcase <?= $briefcase1->getBriefcaseName() ?> has been updated successful
		</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were no updates for the briefcase <br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
?>			
			</div>
			<div class="ui-sys-panel-footer"><a class="ui-sys-back" href="<?= $thispage ?>?page=managebriefcase">Back to Briefcase Manager</a></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managebriefcase_edit", "Edited ".$briefcase1->getBriefcaseName());
	} else if ($page == "managebriefcase_edit" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managebriefcase_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$briefcase1 = null;
		$extraFilter = System::getCodeString(8);
		try {
			$briefcase1 = new Briefcase($database, $_REQUEST['id'], $conn);
			if (is_null($briefcase1->getGroup()) || ! $briefcase1->getGroup()->isAuthorizedOwner($login1->getLoginId())) Object::shootException("You are not the Authorized Owner of the briefcase");
			$briefcase1->setExtraFilter($extraFilter);
			$briefcase1->commitUpdate();
		} catch (Exception $e)	{ die($e->getMessage()); }
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Edit Briefcase <?= $briefcase1->getBriefcaseName() ?></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managebriefcase_edit"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="id" value="<?= $briefcase1->getBriefcaseId() ?>"/>
					<input type="hidden" name="rndx" value="<?= $briefcase1->getExtraFilter() ?>"/>
					<div class="pure-control-group">
						<label for="briefcaseName">Briefcase Name </label>
						<input value="<?= $briefcase1->getBriefcaseName() ?>" type="text" name="briefcaseName" id="briefcaseName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Briefcase Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="groupId">Group </label>
						<select id="groupId" name="groupId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Group">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = BriefcaseGroup::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$group1 = new BriefcaseGroup($database, $alist1['id'], $conn);
		if (! $group1->isAuthorizedOwner($login1->getLoginId())) continue;  
		$selected = "";
		if (! is_null($briefcase1->getGroup()) && ($briefcase1->getGroup()->getGroupId() == $alist1['id'])) $selected = "selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label title="This feature operates on a ratio" for="weight">Results Weight</label>
						<input value="<?= $briefcase1->getWeight() ?>" class="text-can-not-be-zero" type="text" name="weight" id="weight" size="16" required pattern="<?= $expr2Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr2Number ?>" validate_message="Results Weight : <?= $msg2Number ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input value="<?= $briefcase1->getExtraInformation() ?>" type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Edit Record"/>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer"><a class="ui-sys-back" href="<?= $thispage ?>?page=managebriefcase">Back to Briefcase Manager</a></div>
		</div>
<script type="text/javascript">
(function($)	{
	$('#__add_record').on('click', function(event)	{
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
			$('<span/>').html('Weight CAN NOT BE ZERO')
				.appendTo($target1);
		}
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} /*END Briefcase Edit*/ else /*BEGIN of Delete Services*/if ($page == "managedeleteservices" && isset($_REQUEST['report']) && isset($_REQUEST['class']) && isset($_REQUEST['id']) && isset($_REQUEST['caption']) && isset($_REQUEST['key']) && Authorize::isAllowable($config, "manage".strtolower($_REQUEST['class'])."_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		/*You will need to verify the md5 key for 
			key = md5(class,id,hashText) --PACIH
		*/
		$enableUpdate = false;
		$object1 = null;
		$captionText = $_REQUEST['caption'];
		try {
			$key = md5($_REQUEST['class'].$_REQUEST['id'].Object::$hashText);
			if ($key != $_REQUEST['key']) Object::shootException("Perhaps, data is compromised");
			$object1 = ClassRegistry::getObjectReference($database, $conn, $_REQUEST['class'], $_REQUEST['id']);
			if (is_null($object1)) Object::shootException("Class could not be found in the Registry");
			$object1->commitDelete();
			$enableUpdate = true;
		} catch (Exception $e)	{
			$enableUpdate = false;
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Deletion (<?= $captionText ?>)</div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Congratulations, <?= $captionText ?> : Deletion is successful
		</div>
<?php	 
	} else {
?>
		<div class="ui-state-error">
			There were problems in deletion <br/>
			Details : <?= $promise1->getReason() ?> 
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>">Back to Main Page</a></div>
		</div>
<?php
		$contextName = "manage".strtolower($_REQUEST['class'])."_delete";
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), $contextName, "Deleted, $captionText");
	} else if ($page == "managedeleteservices" && isset($_REQUEST['class']) && isset($_REQUEST['id']) && isset($_REQUEST['caption']) && isset($_REQUEST['key']) && Authorize::isAllowable($config, "manage".strtolower($_REQUEST['class'])."_delete", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		/*You will need to verify the md5 key for 
			key = md5(class,id,hashText) --PACIH
		*/
		$object1 = null;
		try {
			$key = md5($_REQUEST['class'].$_REQUEST['id'].Object::$hashText);
			if ($key != $_REQUEST['key']) Object::shootException("Perhaps, data is compromised");
			$object1 = ClassRegistry::getObjectReference($database, $conn, $_REQUEST['class'], $_REQUEST['id']);
			if (is_null($object1)) Object::shootException("Class could not be found in the Registry");
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
		$captionText = $_REQUEST['caption'];
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Deletion (<?= $captionText ?>)</div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-sys-data-capture">
			<form method="POST" action="<?= $thispage ?>">
				<input type="hidden" name="page" value="managedeleteservices"/>
				<input type="hidden" name="class" value="<?= $_REQUEST['class'] ?>"/>
				<input type="hidden" name="id" value="<?= $_REQUEST['id'] ?>"/>
				<input type="hidden" name="caption" value="<?= $_REQUEST['caption'] ?>"/>
				<input type="hidden" name="key" value="<?= $_REQUEST['key'] ?>"/>
				<input type="hidden" name="report" value="1"/>
				<div class="ui-sys-inline-controls-center">
					<div class="ui-sys-warning">
						You are about to remove completely <?= $captionText ?> from the system. Are you sure you want to proceed? <br/><br/>
					</div>
					<div>
						<input type="submit" value="Yes (Proceed)"/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="button-link" href="<?= $thispage ?>">No (Cancel)</a>
					</div>
				</div>
			</form>
		</div>
<?php		
	} else {
?>
		<div class="ui-sys-error-message">
			There were problems in Carrying forward the Action <br/>
			Details : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>">Back to Main Page</a></div>
		</div>
<?php
		mysql_close($conn);
	}/*END of Delete Services*/else /*BEGIN of Flag Services*/if ($page == "manageflagservices" && isset($_REQUEST['rndx']) && isset($_REQUEST['report']) && isset($_REQUEST['pos']) && isset($_REQUEST['action']) && isset($_REQUEST['class']) && isset($_REQUEST['id']) && isset($_REQUEST['caption']) && isset($_REQUEST['key']) && Authorize::isAllowable($config, "manage".strtolower($_REQUEST['class'])."_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		/*You will need to verify the md5 key for 
			key = md5(pos,action,class,id,hashText) --PACIH
		*/
		$enableUpdate = false;
		$object1 = null;
		$captionText = $_REQUEST['caption'];
		try {
			$key = md5($_REQUEST['pos'].$_REQUEST['action'].$_REQUEST['class'].$_REQUEST['id'].Object::$hashText);
			if ($key != $_REQUEST['key']) Object::shootException("Perhaps, data is compromised");
			$object1 = ClassRegistry::getObjectReference($database, $conn, $_REQUEST['class'], $_REQUEST['id']);
			if (is_null($object1)) Object::shootException("Class could not be found in the Registry");
			if (trim($_REQUEST['rndx']) != $object1->getExtraFilter()) Object::shootException("The System has detected you are replaying data in your browser");
			$object1->setExtraFilter(System::getCodeString(8));
			//Update Values 
			if (! ((intval($_REQUEST['action']) == 1) xor $object1->isFlagSetAt($_REQUEST['pos']))) Object::shootException("There is no any update which has been done to properties");
			if (intval($_REQUEST['action']) == 1) $object1->setFlagAt($_REQUEST['pos']);
			else $object1->resetFlagAt($_REQUEST['pos']);
			$object1->commitUpdate();
			$enableUpdate = true;
		} catch (Exception $e)	{
			$enableUpdate = false;
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
		mysql_close($conn);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Properties Change (<?= $captionText ?>)</div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			Congratulations, <?= $captionText ?> : Properties has been updated successful
		</div>
<?php	 
	} else {
?>
		<div class="ui-state-error">
			There were problems in updating properties <br/>
			Details : <?= $promise1->getReason() ?> 
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>">Back to Main Page</a></div>
		</div>
<?php
		$contextName = "manage".strtolower($_REQUEST['class'])."_edit";
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), $contextName, "Edited [Flags], $captionText");
	} else if ($page == "manageflagservices" && isset($_REQUEST['pos']) && isset($_REQUEST['action']) && isset($_REQUEST['class']) && isset($_REQUEST['id']) && isset($_REQUEST['caption']) && isset($_REQUEST['key']) && Authorize::isAllowable($config, "manage".strtolower($_REQUEST['class'])."_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		/*You will need to verify the md5 key for 
			key = md5(pos,action,class,id,hashText) --PACIH
		*/
		$object1 = null;
		try {
			$key = md5($_REQUEST['pos'].$_REQUEST['action'].$_REQUEST['class'].$_REQUEST['id'].Object::$hashText);
			if ($key != $_REQUEST['key']) Object::shootException("Perhaps, data is compromised");
			$object1 = ClassRegistry::getObjectReference($database, $conn, $_REQUEST['class'], $_REQUEST['id']);
			if (is_null($object1)) Object::shootException("Class could not be found in the Registry");
			$object1->setExtraFilter(System::getCodeString(8));
			$object1->commitUpdate();
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
		$captionText = $_REQUEST['caption'];
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Properties Change (<?= $captionText ?>)</div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-sys-data-capture">
			<form method="POST" action="<?= $thispage ?>">
				<input type="hidden" name="page" value="manageflagservices"/>
				<input type="hidden" name="pos" value="<?= $_REQUEST['pos'] ?>"/>
				<input type="hidden" name="action" value="<?= $_REQUEST['action'] ?>"/>
				<input type="hidden" name="class" value="<?= $_REQUEST['class'] ?>"/>
				<input type="hidden" name="id" value="<?= $_REQUEST['id'] ?>"/>
				<input type="hidden" name="caption" value="<?= $_REQUEST['caption'] ?>"/>
				<input type="hidden" name="key" value="<?= $_REQUEST['key'] ?>"/>
				<input type="hidden" name="report" value="1"/>
				<input type="hidden" name="rndx" value="<?= $object1->getExtraFilter() ?>"/>
				<div class="ui-sys-inline-controls-center">
					<div class="ui-sys-warning">
						You are about to change some of the properties of the <?= $captionText ?>. Are you sure you want to proceed? <br/><br/>
					</div>
					<div>
						<input type="submit" value="Yes (Proceed)"/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="button-link" href="<?= $thispage ?>">No (Cancel)</a>
					</div>
				</div>
			</form>
		</div>
<?php		
	} else {
?>
		<div class="ui-sys-error-message">
			There were problems in Carrying forward the Action <br/>
			Details : <?= $promise1->getReason() ?>
		</div>
<?php
	}
?>
			</div>
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>">Back to Main Page</a></div>
		</div>
<?php
		mysql_close($conn);
	}/*END of Flag Services*/else /*BEGIN Add a New Briefcase*/if ($page == "managebriefcase_add" && isset($_REQUEST['report']) && Authorize::isAllowable($config, "managebriefcase_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$briefcaseName = mysql_real_escape_string($_REQUEST['briefcaseName']);
		$groupId = mysql_real_escape_string($_REQUEST['groupId']);
		$weight = mysql_real_escape_string($_REQUEST['weight']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel header ui-widget-header">Summary Report for <i><?= $briefcaseName ?></i></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	$extraFilter = System::getCodeString(8);
	$query="INSERT INTO briefcase (briefcaseName, groupId, weight, extraFilter, extraInformation) VALUES('$briefcaseName', '$groupId', '$weight', '$extraFilter', '$extraInformation')";
	try {
		mysql_db_query($database, $query, $conn) or Object::shootException("Briefcase[Adding]: Could not Add a Record into the System");
		$promise1->setPromise(true);
		$enableUpdate = true;
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">The Briefcase <?= $briefcaseName ?>, has successful Added to the system</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Adding the briefcase<br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
	$captionText = "Try Again";
	if ($enableUpdate) $captionText = "Add Another Briefcase";
?>
			</div>
			<div class="ui-sys-panel-footer"><a class="ui-sys-back" href="<?= $thispage ?>?page=managebriefcase_add&groupId=<?= $_REQUEST['groupId'] ?>"><?= $captionText ?></a>&nbsp;&nbsp;<a class="ui-sys-back" href="<?= $thispage ?>?page=managebriefcase">Back to Briefcase Manager</a></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managebriefcase_add", "Added $briefcaseName");
	} else if ($page == "managebriefcase_add" && isset($_REQUEST['groupId']) && Authorize::isAllowable($config, "managebriefcase_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Add a New Briefcase</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managebriefcase_add"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="groupId" value="<?= $_REQUEST['groupId'] ?>"/>
					<div class="pure-control-group">
						<label for="briefcaseName">Briefcase Name </label>
						<input type="text" name="briefcaseName" id="briefcaseName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Briefcase Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label title="This feature operates on a ratio" for="weight">Results Weight</label>
						<input class="text-can-not-be-zero" type="text" name="weight" id="weight" size="16" required pattern="<?= $expr2Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr2Number ?>" validate_message="Results Weight : <?= $msg2Number ?>"/>
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
			<div class="ui-sys-panel-footer"><a class="ui-sys-back" href="<?= $thispage ?>?page=managebriefcase">Back to Briefcase Manager</a></div>
		</div>
<script type="text/javascript">
(function($)	{
	$('#__add_record').on('click', function(event)	{
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
			$('<span/>').html('Weight CAN NOT BE ZERO')
				.appendTo($target1);
		}
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} /*ENDing Add a New Briefcase*/else /*BEGIN Edit an Existing Briefcase Group*/if ($page == "managebriefcasegroup_edit" && isset($_REQUEST['id']) && isset($_REQUEST['report']) && isset($_REQUEST['rndx']) && Authorize::isAllowable($config, "managebriefcasegroup_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$group1 = null;
		try {
			$group1 = new BriefcaseGroup($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$promise1 = new Promise();
		$promise1->setReason("Nothing Has been Updated");
		$enableUpdate = false;
		$groupName = mysql_real_escape_string($_REQUEST['groupName']);
		$courseId = mysql_real_escape_string($_REQUEST['courseId']);
		$year = mysql_real_escape_string($_REQUEST['year']);
		$batchId = mysql_real_escape_string($_REQUEST['batchId']);
		$examinationId = mysql_real_escape_string($_REQUEST['examinationId']);
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
		if ($groupName != $group1->getGroupName())	{
			$group1->setGroupName($groupName); $enableUpdate = true;
		}
		if (is_null($group1->getCourse()) || ($group1->getCourse()->getCourseId() != $courseId))	{
			$group1->setCourse($courseId); $enableUpdate = true;
		}
		if ($year != $group1->getYear())	{
			$group1->setYear($year); $enableUpdate = true;
		}
		if (is_null($group1->getBatch()) || ($group1->getBatch()->getAccademicYearId() != $batchId))	{
			$group1->setBatch($batchId); $enableUpdate = true;
		}
		if (is_null($group1->getExamination()) || ($group1->getExamination()->getExaminationId() != $examinationId))	{
			$group1->setExamination($examinationId); $enableUpdate = true;
		}
		if ($extraInformation != $group1->getExtraInformation())	{
			$group1->setExtraInformation($extraInformation); $enableUpdate = true;
		}
		//This is the Last 
		if (trim($_REQUEST['rndx']) != $group1->getExtraFilter())	{
			$enableUpdate = false;
			$promise1->setReason("The System has Detected you are replaying data in your Browser window");
			$promise1->setPromise(false);
		}
		//Now proceed with general find a new value of extra Filter 
		$group1->setExtraFilter(System::getCodeString(8));
		if ($enableUpdate)	{
			try {
				if (! $group1->isAuthorizedOwner($login1->getLoginId())) Object::shootException("You are not an Authorized owner of the group");
				$group1->commitUpdate();
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
			<div class="ui-sys-panel-header ui-widget-header">BriefcaseGroup Editing Report</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">
			BriefcaseGroup <?= $group1->getGroupName() ?> has been updated successful
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
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>?page=managebriefcase">Back to Briefcase Manager</a></div>
		</div>
<?php
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managebriefcasegroup_edit", "Edited ".$group1->getGroupName());
	} else if ($page == "managebriefcasegroup_edit" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managebriefcasegroup_edit", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$group1 = null;
		$extraFilter = System::getCodeString(8);
		try {
			$group1 = new BriefcaseGroup($database, $_REQUEST['id'], $conn);
			if (! $group1->isAuthorizedOwner($login1->getLoginId())) Object::shootException("You are not the owner of this group");
			$group1->setExtraFilter($extraFilter);
			$group1->commitUpdate();
		} catch (Exception $e)	{ die($e->getMessage()); }
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Edit BriefcaseGroup <?= $group1->getGroupName() ?></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managebriefcasegroup_edit"/>
					<input type="hidden" name="report" value="io"/>
					<input type="hidden" name="id" value="<?= $group1->getGroupId() ?>"/>
					<input type="hidden" name="rndx" value="<?= $group1->getExtraFilter() ?>"/>
					<input type="hidden" id="dataStore" value="0" data-current-year="<?= $group1->getYear() ?>"/>
					<div class="pure-control-group">
						<label for="groupName">Group Name </label>
						<input value="<?= $group1->getGroupName() ?>" type="text" name="groupName" id="groupName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="BriefcaseGroup Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="courseId">Course </label>
						<select id="courseId" name="courseId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Course">
							<option data-course-duration="0" value="_@32767@_">--select--</option>
<?php 
	$list1 = Course::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$course1 = null;
		try {
			$course1 = new Course($database, $alist1['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$selected = "";
		if (! is_null($group1->getCourse()) && ($group1->getCourse()->getCourseId() == $alist1['id'])) $selected = "selected";
?>
			<option <?= $selected ?> value="<?= $course1->getCourseId() ?>" data-course-duration="<?= $course1->getDuration() ?>"><?= $course1->getCourseName() ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="year" title="Results for Year">Year </label>
						<select id="year" name="year" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly select a Year this results belong to">
							<option value="_@32767@_">--select--</option>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="batchId" title="Current Accademic Year">Accademic Year </label>
						<select class="__do_perform_ui_maintainance" id="batchId" name="batchId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Academic Year (The Academic Year these results belongs to)">
							<option value="_@32767@_">--select--</option>
<?php 
	$controlAccademicCounter = $profile1->getCurrentAccademicYear()->getAccademicYearNumber();
	$list1 = AccademicYear::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		try {
			$accademicYear1 = new AccademicYear($database, $alist1['id'], $conn);
			if ($accademicYear1->getAccademicYearNumber() == $controlAccademicCounter) $selected = "selected";
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="examinationId">Examination </label>
						<select id="examinationId" name="examinationId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select an Examination">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Examination::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($group1->getExamination()) && ($group1->getExamination()->getExaminationId() == $alist1['id'])) $selected="selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="extraInformation">Extra Information </label>
						<input value="<?= $group1->getExtraInformation() ?>" type="text" name="extraInformation" id="extraInformation" size="48" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
					</div>
					<div class="pure-controls">
						<span id="perror" class="ui-sys-error-message"></span>
					</div>
					<div class="pure-controls">
						<input id="__add_record" type="button" value="Edit Record"/>
					</div>
				</form>
			</div>
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>?page=managebriefcase">Back to Briefcase Manager</a></div>
		</div>
<script type="text/javascript">
(function($)	{
	function uiMaitainance($course1, $currentYear1, $dataStore1)	{
		if (! $course1.length) return;
		if (! $currentYear1.length) return;
		if (! $dataStore1.length) return;
		var option1 = $course1.find('option:selected');
		var $option1 = $(option1);
		if (! $option1.length) return;
		var duration = $option1.attr('data-course-duration');
		if (! duration) return;
		duration = parseInt(duration);
		var currentYear = $dataStore1.attr('data-current-year');
		if (! currentYear) { currentYear = 0; }
		currentYear = parseInt(currentYear);
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
	}
	uiMaitainance($('#courseId'), $('#year'), $('#dataStore'));
	$('#courseId').on('change', function(event)	{
		uiMaitainance($(this), $('#year'), $('#dataStore'));
	});
	$('#__add_record').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	}/*END Editing an Existing Briefcase Group*/ else /*BEGIN Add a New Briefcase Group*/if ($page == "managebriefcasegroup_add" && isset($_REQUEST['report']) && Authorize::isAllowable($config, "managebriefcasegroup_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$enableUpdate = false;
		$promise1 = new Promise();
		$groupName = mysql_real_escape_string($_REQUEST['groupName']);
		$courseId = mysql_real_escape_string($_REQUEST['courseId']);
		$year = mysql_real_escape_string($_REQUEST['year']);
		$batchId = mysql_real_escape_string($_REQUEST['batchId']);
		$examinationId = mysql_real_escape_string($_REQUEST['examinationId']);
		$loginId = $_SESSION['login'][0]['id'];
		$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel header ui-widget-header">Summary Report for <i><?= $groupName ?></i></div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
<?php 
	$extraFilter = System::getCodeString(8);
	$query="INSERT INTO briefcaseGroup (groupName, courseId, _year, batchId, examinationId, loginId, listOfOwners, extraFilter, extraInformation) VALUES('$groupName', '$courseId', '$year', '$batchId', '$examinationId', '$loginId', '$loginId', '$extraFilter', '$extraInformation')";
	try {
		mysql_db_query($database, $query, $conn) or Object::shootException("BriefcaseGroup[Adding]: Could not Add a Record into the System ");
		$promise1->setPromise(true);
		$enableUpdate = true;
	} catch (Exception $e)	{
		$promise1->setReason($e->getMessage());
		$promise1->setPromise(false);
	}
	if ($promise1->isPromising())	{
?>
		<div class="ui-state-highlight">The BriefcaseGroup <?= $groupName ?>, has successful Added to the system</div>
<?php
	} else {
?>
		<div class="ui-state-error">
			There were problems in Adding the group<br/>
			Reason: <?= $promise1->getReason() ?>
		</div>
<?php		
	}
	$captionText = "Try Again";
	if ($enableUpdate) $captionText = "Add Another Briefcase Group";
?>
			</div>
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>?page=managebriefcasegroup_add"><?= $captionText ?></a>&nbsp;&nbsp;<a href="<?= $thispage ?>?page=managebriefcase">Back to Briefcase Manager</a></div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate) Accounting::addLog($config, $date, $login1->getLoginName(), "managebriefcasegroup_add", "Added $groupName");
	} else if ($page == "managebriefcasegroup_add" && Authorize::isAllowable($config, "managebriefcasegroup_add", "normal", "setlog", "-1", "-1"))	{
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Add a New BriefcaseGroup</div>
			<div class="ui-sys-panel-body ui-sys-data-capture">
				<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $thispage ?>">
					<input type="hidden" name="page" value="managebriefcasegroup_add"/>
					<input type="hidden" name="report" value="io"/>
					<div class="pure-control-group">
						<label for="groupName">Group Name </label>
						<input type="text" name="groupName" id="groupName" size="48" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="BriefcaseGroup Name : <?= $msgA48Name ?>"/>
					</div>
					<div class="pure-control-group">
						<label for="courseId">Course </label>
						<select id="courseId" name="courseId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Course">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Course::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$course1 = null;
		try {
			$course1 = new Course($database, $alist1['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
?>
			<option value="<?= $course1->getCourseId() ?>" data-course-duration="<?= $course1->getDuration() ?>"><?= $course1->getCourseName() ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="year" title="Results for Year">Year </label>
						<select id="year" name="year" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly select a Year this results belong to">
							<option value="_@32767@_">--select--</option>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="batchId" title="Current Accademic Year">Accademic Year </label>
						<select class="__do_perform_ui_maintainance" id="batchId" name="batchId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select a Academic Year (The Academic Year these results belongs to)">
							<option value="_@32767@_">--select--</option>
<?php 
	$controlAccademicCounter = $profile1->getCurrentAccademicYear()->getAccademicYearNumber();
	$list1 = AccademicYear::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		try {
			$accademicYear1 = new AccademicYear($database, $alist1['id'], $conn);
			if ($accademicYear1->getAccademicYearNumber() != $controlAccademicCounter) continue;
		} catch (Exception $e)	{
			die($e->getMessage());
		}
?>
			<option value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="examinationId">Examination </label>
						<select id="examinationId" name="examinationId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select an Examination">
							<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Examination::loadAllData($database, $conn);
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
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>?page=managebriefcase">Back to Briefcase Manager</a></div>
		</div>
<script type="text/javascript">
(function($)	{
	$('#courseId').on('change', function(event)	{
		var $select1 = $(this).closest('select');
		if (! $select1.length) return;
		var $option1 = $select1.find('option:selected');
		if (! $option1.length) return;
		var duration = $option1.attr('data-course-duration');
		if (! duration) return;
		duration = parseInt(duration);
		//Getting year select 
		$select1 = $('#year');
		if (! $select1.length) return;
		$select1.empty();
		$('<option/>').attr('value', '_@32767@_').html('--select--')
			.appendTo($select1);
		for (var i = 1; i <= duration; i++)	{
			$('<option/>').attr('value', i).html(i)
				.appendTo($select1);
		}
	});
	$('#__add_record').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else /*Group END Adding a New Briefcase Group*/if ($page == "managebriefcase" && Authorize::isAllowable($config, "managebriefcase", "normal", "setlog", "-1", "-1"))	{
		$blnBriefcaseManager = Authorize::isAllowable($config, "managebriefcase", "normal", "donotsetlog", "-1", "-1");
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$list = null;
		try {
			$criteriaArray1 = array();
			if (isset($_REQUEST['courseId']) && intval($_REQUEST['courseId']) != 0) $criteriaArray1['courseId'] = mysql_real_escape_string($_REQUEST['courseId']);
			if (isset($_REQUEST['batchId']) && intval($_REQUEST['batchId']) != 0) $criteriaArray1['batchId'] = mysql_real_escape_string($_REQUEST['batchId']);
			if (isset($_REQUEST['examinationId']) && intval($_REQUEST['examinationId']) != 0) $criteriaArray1['examinationId'] = mysql_real_escape_string($_REQUEST['examinationId']);
			if (isset($_REQUEST['year']) && intval($_REQUEST['year']) != 0) $criteriaArray1['_year'] = mysql_real_escape_string($_REQUEST['year']);
			$list = BriefcaseGroup::getBriefcaseGroupListFromCriteria($database, $conn, $login1->getLoginId(), $criteriaArray1);
			if (is_null($list)) Object::shootException("There were no briefcase results found for you");
		} catch (Exception $e)	{
			$promise1->setReason($e->getMessage());
			$promise1->setPromise(false);
		}
?>
		<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
			<div class="ui-sys-panel-header ui-widget-header">Briefcase Manager</div>
			<div class="ui-sys-panel-body">
<?php 
	if ($promise1->isPromising())	{
		$imagePath = "background-image: url('../sysimage/sprite1.png');";
		$hashText = Object::$hashText;
?>
				<!--Begin of Treetable-->
				<div>
					<table class="briefcase-treetable ui-sys-treetable">
						<caption>
							<a href="#" onclick="jQuery('table.briefcase-treetable').treetable('expandAll'); return false;">Expand all</a>
							<a href="#" onclick="jQuery('table.briefcase-treetable').treetable('collapseAll'); return false;">Collapse all</a>
						</caption>
						<thead>
							<tr>
								<th>Name</th>
								<th>Status</th>
								<th>Course</th>
								<th>Academic Year</th>
								<th>Examination</th>
								<th>Year</th>
								<th>Weight</th>
							</tr>
						</thead>
						<tbody>
<?php 
	foreach ($list as $groupId)	{
		$group1 = null;
		try {
			$group1 = new BriefcaseGroup($database, $groupId, $conn);
		} catch (Exception $e)	{
			continue;
		}
		$statusIcons = "";
		$courseName = "";
		if (! is_null($group1->getCourse())) $courseName = $group1->getCourse()->getCourseName();
		$academicYear = "";
		if (! is_null($group1->getBatch())) $academicYear = $group1->getBatch()->getAccademicYear();
		$examinationName = "";
		if (! is_null($group1->getExamination())) $examinationName = $group1->getExamination()->getExaminationName();
		$groupWeight = "100%"; //By Default Group is carrying full weight group 
		$lockedText = "Not Locked";
		$publishedText = "Not Published";
		$lockedPosition = "background-position: -609px -231px;";
		$publishedPosition = "background-position: -177px -231px;";
		if ($group1->isResultsLocked())	{
			$lockedText = "Locked";
			$lockedPosition = "background-position: -479px -231px;";
		}
		if ($group1->isEligibleForPublication())	{
			//Only Locked One Can be Actually Published 
			$publishedText = "Published";
			$publishedPosition = "background-position: -155px -231px;";
		}
		$statusIcons = "<span title=\"$lockedText\" class=\"ui-icon-customized\" style=\"$imagePath $lockedPosition\">&nbsp;</span>&nbsp;&nbsp;<span title=\"$publishedText\" class=\"ui-icon-customized\" style=\"$imagePath $publishedPosition\"></span>";
		$actionIcons = "";
		$id = $group1->getGroupId();
		$class = "BriefcaseGroup";
		$caption = $group1->getGroupName();
		if ($group1->canLockResultsNow())	{
			$nextpage = $thispage."?page=managebriefcasegroup_edit&service_compile=true&id=$id";
			$actionIcons .= "<a href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-compile\"></a>";
		}
		if ($group1->canUnlockResultsNow())	{
			$pos = BriefcaseGroup::$__IS_RESULTS_LOCKED;
			$action = 0;
			$key = $pos.$action.$class.$id.$hashText;
			$key = md5($key);			
			$nextpage = $thispage."?page=manageflagservices&pos=$pos&action=$action&class=$class&id=$id&caption=$caption&key=$key";
			$actionIcons .= "<a href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-unlock\"></a>";
		}
		if ($group1->canPublishNow())	{
			$pos = BriefcaseGroup::$__IS_PUBLISH_RESULTS;
			$action = 1;
			$key = $pos.$action.$class.$id.$hashText;
			$key = md5($key);			
			$nextpage = $thispage."?page=manageflagservices&pos=$pos&action=$action&class=$class&id=$id&caption=$caption&key=$key";
			$actionIcons .= "<a href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-publish\"></a>";
		}
		if ($group1->canUnpublishNow())	{
			$pos = BriefcaseGroup::$__IS_PUBLISH_RESULTS;
			$action = 0;
			$key = $pos.$action.$class.$id.$hashText;
			$key = md5($key);			
			$nextpage = $thispage."?page=manageflagservices&pos=$pos&action=$action&class=$class&id=$id&caption=$caption&key=$key";
			$actionIcons .= "<a href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-unpublish\"></a>";
		}
		if ($group1->canAddNewItemNow())	{
			$nextpage = $thispage."?page=managebriefcase_add&groupId=".$group1->getGroupId();
			$actionIcons .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a title=\"Add a New Briefcase in a Group\" href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-add-new\"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		if ($group1->canEditNow())	{
			$nextpage = $thispage."?page=managebriefcasegroup_edit&id=".$group1->getGroupId();
			$actionIcons .= "<a href=\"$nextpage\" title=\"Edit Briefcase Group\" class=\"ui-sys-icon-80-by-16 icon-edit\"></a>";
		} 
		if ($group1->canDeleteNow())	{
			$key = $class.$id.$hashText;
			$key = md5($key);
			$nextpage = $thispage."?page=managedeleteservices&class=$class&id=$id&caption=$caption&key=$key";
			$actionIcons .= "<a href=\"$nextpage\" title=\"Edit Briefcase Group\" class=\"ui-sys-icon-80-by-16 icon-delete\"></a>";
		}
		if ($group1->canShareNow())	{
			$nextpage = $thispage."?page=managebriefcasegroup_edit&id=$id&attach_login=true";
			$actionIcons .= "<a href=\"$nextpage\" title=\"Attach or Detach Owners\" class=\"ui-sys-icon-80-by-16 icon-share\"></a>";
		}
		if ($group1->canDownloadNow())	{
			$filename = "../data/briefcase/".$group1->getRawMarksCSVFile();
			if (! is_null($group1->getRawMarksCSVFile()) && file_exists($filename))	{
				$actionIcons .= "<a href=\"$filename\" title=\"Download Compiled Results\" class=\"ui-sys-icon-80-by-16 icon-download\"></a>";
			}
		}
		if ($group1->canViewResultsDetails())	{
			$folder = "briefcase";
			$mode = Results::$__BRIEFCASE_MODE;
			$key = $class.$id.$folder.$mode.$hashText;
			$key = md5($key);
			$classstring = strtolower($class);
			$nextpage = $thispage."?page=manageresultsviewservices&class=$class&id=$id&folder=$folder&mode=$mode&caption=$caption&key=$key";
			$actionIcons .= "<a href=\"$nextpage\" title=\"Results Details for Briefcase\" class=\"ui-sys-icon-80-by-16 icon-view\"></a>";
		}
		$ownerList = $group1->getListOfOwners();
		$primaryOwner1 = $group1->getLogin();
		$dataLoginListPopup="{}";
		if (! is_null($ownerList) && ! is_null($primaryOwner1))	{
			$dataLoginListPopup = '{"owners":[';
			$dataCount = 0;
			foreach ($ownerList as $vlogin1)	{
				$primary = 0;
				if ($vlogin1->getLoginId() == $primaryOwner1->getLoginId()) $primary = 1;
				$fullname = $vlogin1->getFullname();
				$lineToWrite = '{"name":"'.$fullname.'","primary":"'.$primary.'"}';
				if ($dataCount == 0)	$dataLoginListPopup .= $lineToWrite;
				else $dataLoginListPopup .= ",".$lineToWrite;
				$dataCount++;
			}
			$dataLoginListPopup .= "]}";
		}
?>
		<tr data-tt-id="_<?= $group1->getGroupId() ?>">
			<td><span class="folder"><span title="Login-List-Owner" class="ui-sys-login-list-tooltip" data-login-list-popup='<?= $dataLoginListPopup ?>'><?= $group1->getGroupName() ?></span></span></td>
			<td><?= $statusIcons ?></td>
			<td><?= $courseName ?></td>
			<td><?= $academicYear ?></td>
			<td><?= $examinationName ?></td>
			<td><?= $group1->getYear() ?></td>
			<td><b><?= $groupWeight ?></b></td>
		</tr>
		<!--First Child Should Carry Actions-->
		<tr data-tt-id="_a_<?= $group1->getGroupId() ?>" data-tt-parent-id="_<?= $group1->getGroupId() ?>">
			<td colspan="7">Group Actions [<?= $group1->getGroupName() ?>] : <?= $actionIcons ?></td>
		</tr>
<?php
		//Now Process the briefcases 
		$briefcaseList = BriefcaseGroup::getBriefcaseListBelongingToAGroup($database, $conn, $group1->getGroupId());
		if (! is_null($briefcaseList) && $blnBriefcaseManager)	{
			foreach ($briefcaseList as $briefcaseId)	{
				$briefcase1 = new Briefcase($database, $briefcaseId, $conn);
				$weight = "0";
				try {
					$weight = $briefcase1->getPercentageWeightInAGroup()."%";
				} catch (Exception $e)	{
					$weight = "#";
				}
				$lockedText = "Not Locked";
				$publishedText = "Not Published";
				$lockedPosition = "background-position: -609px -231px;";
				$publishedPosition = "background-position: -177px -231px;";
				if ($briefcase1->isResultsLocked())	{
					$lockedText = "Locked";
					$lockedPosition = "background-position: -479px -231px;";
				}
				if ($briefcase1->isEligibleForPublication())	{
					//Only Locked One Can be Actually Published 
					$publishedText = "Published";
					$publishedPosition = "background-position: -155px -231px;";
				}
				$statusIcons = "<span title=\"$lockedText\" class=\"ui-icon-customized\" style=\"$imagePath $lockedPosition\">&nbsp;</span>&nbsp;&nbsp;<span title=\"$publishedText\" class=\"ui-icon-customized\" style=\"$imagePath $publishedPosition\"></span>";
				$actionIcons = "";
				$id = $briefcase1->getBriefcaseId();
				$class = "Briefcase";
				$caption = $briefcase1->getBriefcaseName();
				if ($briefcase1->canLockResultsNow())	{
					$pos = Briefcase::$__IS_RESULTS_LOCKED;
					$action = 1;
					$key = $pos.$action.$class.$id.$hashText;
					$key = md5($key);			
					$nextpage = $thispage."?page=manageflagservices&pos=$pos&action=$action&class=$class&id=$id&caption=$caption&key=$key";
					$actionIcons .= "<a href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-lock\"></a>";
				}
				if ($briefcase1->canUnlockResultsNow())	{
					$pos = Briefcase::$__IS_RESULTS_LOCKED;
					$action = 0;
					$key = $pos.$action.$class.$id.$hashText;
					$key = md5($key);			
					$nextpage = $thispage."?page=manageflagservices&pos=$pos&action=$action&class=$class&id=$id&caption=$caption&key=$key";
					$actionIcons .= "<a href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-unlock\"></a>";
				}
				if ($briefcase1->canPublishNow())	{
					$pos = Briefcase::$__IS_PUBLISH_RESULTS;
					$action = 1;
					$key = $pos.$action.$class.$id.$hashText;
					$key = md5($key);			
					$nextpage = $thispage."?page=manageflagservices&pos=$pos&action=$action&class=$class&id=$id&caption=$caption&key=$key";
					$actionIcons .= "<a href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-publish\"></a>";
				}
				if ($briefcase1->canUnpublishNow())	{
					$pos = Briefcase::$__IS_PUBLISH_RESULTS;
					$action = 0;
					$key = $pos.$action.$class.$id.$hashText;
					$key = md5($key);			
					$nextpage = $thispage."?page=manageflagservices&pos=$pos&action=$action&class=$class&id=$id&caption=$caption&key=$key";
					$actionIcons .= "<a href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-unpublish\"></a>";
				}
				if ($briefcase1->canNullifyNow())	{
					$folder = "briefcase";
					$key = $class.$id.$folder.$hashText;
					$key = md5($key);
					$nextpage = $thispage."?page=managenullificationservices&class=$class&id=$id&caption=$caption&folder=$folder&key=$key";
					$actionIcons .= "<a title=\"Undo results upload\" href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-nullify\"></a>";
				} 
				if ($briefcase1->canEditNow())	{
					$nextpage = $thispage."?page=managebriefcase_edit&id=".$briefcase1->getBriefcaseId();
					$actionIcons .= "<a title=\"Edit Briefcase\" href=\"$nextpage\" class=\"ui-sys-icon-80-by-16 icon-edit\"></a>";
				}
				if ($briefcase1->canDeleteNow())	{
					$key = $class.$id.$hashText;
					$key = md5($key);
					$nextpage = $thispage."?page=managedeleteservices&class=$class&id=$id&caption=$caption&key=$key";
					$actionIcons .= "<a href=\"$nextpage\" title=\"Delete Briefcase\" class=\"ui-sys-icon-80-by-16 icon-delete\"></a>";
				}
				if ($briefcase1->canUploadNow())	{
					$folder = "briefcase";
					$mode = Results::$__BRIEFCASE_MODE;
					$key = $class.$id.$folder.$mode.$hashText;
					$key = md5($key);
					$nextpage = $thispage."?page=manageresultsuploadservices&class=$class&id=$id&folder=$folder&mode=$mode&caption=$caption&key=$key";
					$actionIcons .= "<a href=\"$nextpage\" title=\"Upload to Briefcase\" class=\"ui-sys-icon-80-by-16 icon-upload\"></a>";
				}
				if ($briefcase1->canViewResultsDetails())	{
					$folder = "briefcase";
					$mode = Results::$__BRIEFCASE_MODE;
					$key = $class.$id.$folder.$mode.$hashText;
					$key = md5($key);
					$classstring = strtolower($class);
					$nextpage = $thispage."?page=manageresultsviewservices&class=$class&id=$id&folder=$folder&mode=$mode&caption=$caption&key=$key";
					$actionIcons .= "<a href=\"$nextpage\" title=\"Results Details for Briefcase\" class=\"ui-sys-icon-80-by-16 icon-view\"></a>";
				}
?>
					<tr data-tt-id="_<?= $briefcase1->getBriefcaseId() ?>_<?= $group1->getGroupId() ?>" data-tt-parent-id="_<?= $group1->getGroupId() ?>">
						<td><span class="file"><?= $briefcase1->getBriefcaseName() ?></span></td>
						<td><?= $statusIcons ?></td>
						<td><?= $courseName ?></td>
						<td><?= $academicYear ?></td>
						<td><?= $examinationName ?></td>
						<td><?= $group1->getYear() ?></td>
						<td><?= $weight ?></td>
					</tr>
					<tr data-tt-id="_b_<?= $briefcase1->getBriefcaseId() ?>_<?= $group1->getGroupId() ?>" data-tt-parent-id="_<?= $briefcase1->getBriefcaseId() ?>_<?= $group1->getGroupId() ?>">
						<td colspan="7">Briefcase Actions [<?= $briefcase1->getBriefcaseName() ?>] : <?= $actionIcons ?></td>
					</tr>
<?php
			}
		}
	}
?>						
						</tbody>
					</table>
				</div>
				<!--End of Treetable-->
<?php 
	} else {
?>
				<div class="ui-state-highlight">
					It seems you do not have any briefcase, however you can add a New One by clicking the <b>ADD NEW</b> button below
				</div>
<?php
	}
?>
				<!--Add A New Briefcase Button Should be here-->
				<div class="ui-sys-inline-controls-right">
					<a href="<?= $thispage ?>?page=managebriefcasegroup_add" title="Add A New Briefcase Group" class="ui-sys-icon-80-by-16 icon-add-new"></a>
				</div>
			</div>
			<div class="ui-sys-panel-footer"><a href="<?= $thispage ?>">Back to Main Console Window</a></div>
		</div>
<?php
		mysql_close($conn);
	} else {
		if (Authorize::isSessionSet())	{
			$op=Authorize::getSessionValue();;
			$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
			$cid=ContextPosition::getContextIdFromName($database, $op, $conn);
			$context1=new ContextPosition($database, $cid, $conn);
			mysql_close($conn);
?>
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">Permission Alert</div>
		<div class="ui-sys-panel-body">
			<div class="ui-sys-authorize-syslog ui-state-error">
				<b><i>OPERATION DENIED!!</i></b> <br />
				You do not have enough rights to perform <b><?= $context1->getContextCaption() ?></b> operation
			</div>
		</div>
		<div class="ui-sys-panel-footer"></div>
	</div>
<?php 
			Authorize::clearSession();
		} //end-if
?>
<!--BEGIN: Testing Mode -->
	<div class="ui-sys-panel-container ui-sys-panel ui-widget ui-widget-content">
		<div class="ui-sys-panel-header ui-widget-header">HOME SCREEN</div>
		<div class="ui-sys-panel-body">
			<div style="font-size: 1.2em; font-weight: bold;">
				<u>Welcome to Results Management Console</u>
				<div style="padding: 1px; padding-left: 32px; font-size: 0.8em; font-weight: normal;">
					The Results Management Console gives an alternative way of dealing with the results. This console does not add any new 
					features, however it jus change the presentation from the native system presentation. This console is specifically for all types of system results.<br/>
					This console has advantages over the native system presentation, which are <br/>
					<ol>
						<li><b>Better Presentation</b> : It present in a tree like structure all results as they relate to each other as well as their corresponding groups</li>
						<li><b>No General Search Boxes</b> : This console is made in such a way, the minimum filter criterias are displayed. Example of filters includes Academic Years, Courses etc</li>
						<li><b>Specific for Results</b> : Since the system is a general purpose system, this console shift the user mind from other features of the system and to concentrate only with the results</li>
					</ol>
				</div>
			</div>
		</div>
		<div class="ui-sys-panel-footer"></div>
	</div>
<!--END: Testing Mode-->
<?php 
	} //End Default Landing Page
?>
<!--END MAIN CONTENT WINDOW-->
</div>
</div>
<div class="ui-sys-clearboth">&nbsp;</div>
<!--End Right Panel-->
	
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
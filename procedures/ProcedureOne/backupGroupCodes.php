/*Department BEGIN*/if ($page == "managedepartment_csv" && isset($_REQUEST['report']) && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managedepartment_csv", "normal", "setlog", "-1", "-1"))	{
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
			<div class="ui-sys-panel-footer"><a class="ui-sys-back" href="<?= $thispage ?>?page=managedepartment">Back to Department Manager</a></div>
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
				<input type="button" id="__add_record" value="Proceed"/><br/>
				<a class="ui-sys-back" href="<?= $thispage ?>?page=managedepartment">Back to Department Manager</a>
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
			<div class="ui-sys-panel-footer"><a class="ui-sys-back" href="<?= $thispage ?>?page=managedepartment">Back to Department Manager</a></div>
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
			<div class="ui-sys-panel-footer"><a class="ui-sys-back" href="<?= $thispage ?>?page=managedepartment">Back to Department Manager</a></div>
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
			<div class="ui-sys-panel-footer"><a class="ui-sys-back" href="<?= $thispage ?>?page=managedepartment">Back to Department Manager</a></div>
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
			<div>
				<a class="ui-sys-back" href="<?= $thispage ?>?page=managedepartment">Back to Department Manager</a>
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
			<div class="ui-sys-panel-footer">
<?php 
	$commentText = "Try Again";
	if ($enableUpdate) $commentText = "Add Another Department";
?>			
				<a class="ui-sys-back" href="<?= $thispage ?>?page=managedepartment_add"><?= $commentText ?></a>&nbsp;&nbsp;
				<a class="ui-sys-back" href="<?= $thispage ?>?page=managedepartment">Back to Department Manager</a>
			</div>
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
			<div class="ui-sys-panel-footer"><a class="ui-sys-back" href="<?= $thispage ?>?page=managedepartment">Back to Department Manager</a></div>
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
			<div class="ui-sys-panel-footer">
				
			</div>
		</div>
<?php
	}/*Department End*/ else
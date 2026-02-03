<?php 
	if ($page=="managemembershiptype_csv" && isset($_REQUEST['report']) && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managemembershiptype_csv", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-center-80-percent">
			<div class="ui-sys-panel">
				<div class="ui-sys-panel-header">CSV MEMBERSHIP TYPE<br/>(CSV file Download Report)</div>
				<div class="ui-sys-panel-body ui-sys-inline-controls-center">
<?php 
	$filename=$_REQUEST['report']; 
	if (unlink($filename))	{
?>
		The file has been removed from the server, and the system resources has been released
<?php
	} else {
?>
		There were a problem in removing the file from the server 
<?php	
	} //end-if-else
?>				
				</div>
				<div class="ui-sys-panel-footer"><a class="button-link" href="<?= $thispage ?>?page=managemembershiptype">Back to Membership Type</a></div>
			</div>
		</div>
		<!--Hidden control begin-->
		<div class="ui-sys-hidden">
			<input type="hidden" id="selected-level-one-item" value="id_membership"/>
		</div>
		<!--End of hidden controls-->
<?php	
	} else if ($page=="managemembershiptype_csv" && isset($_REQUEST['downloadable']) && Authorize::isAllowable($config, "managemembershiptype_csv", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-center-80-percent">
			<div class="ui-sys-panel">
				<div class="ui-sys-panel-header">CSV MEMBERSHIP TYPE<br/>(CSV file Download)</div>
				<div class="ui-sys-panel-body ui-sys-inline-controls-center">
<?php 
	$filename="";
	$tfname=$filename.$date;
	$tfname=$tfname.rand(100,999);
	$filename=sha1($tfname);
	$filename=$filename.".csv";
	$filename="../temp/".$filename;
	$searchText = Object::getSearchTextFromUserFieldData($_REQUEST['controlIndex'], $_REQUEST['fieldname'], $_REQUEST['op'], $_REQUEST['fieldvalue']);
	$lineCounter = 0;
	$file1 = fopen($filename, "w") or die("Could not Open the File for writing");
	$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
	$csvProcessor1 = null;
	try {
		$csvProcessor1 = new CSVProcessor($searchText, $file1);
	} catch (Exception $e)	{
		die($e->getMessage());
	}
	$query = MembershipType::getQueryText();
	$result = mysql_db_query($database, $query, $conn) or die("Could not execute query to the MembershipType ");
	while (list($id)=mysql_fetch_row($result))	{
		$membershiptype1 = null;
		try	{
			$membershiptype1 = new MembershipType($database, $id, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$csvProcessor1->reload(); //Clear the Previous Object Properties 
		if ($membershiptype1->processCSV($csvProcessor1)->evaluateResult())	{
			$lineCounter++;
		}
	}//end-while
	mysql_close($conn);
	fclose($file1);
?>				
					<span>Your file is Ready for Downloading, with <?= $lineCounter ?> lines of data in it</span><br />
					<a style="font-size: 1.1em; font-weight: bold;" href="<?= $filename ?>">Click Here</a> to Download the File<br />
					<br />
					<a title="After Download kindly Proceed to release system resources" class="button-link" href="<?= $thispage ?>?page=managemembershiptype_csv&downloadable=true&report=<?= $filename ?>">Proceed</a>
				</div>
				<div class="ui-sys-panel-footer"><a class="button-link" href="<?= $thispage ?>?page=managemembershiptype">Back to Membership Type</a></div>
			</div>
		</div>
		<!--Hidden control begin-->
		<div class="ui-sys-hidden">
			<input type="hidden" id="selected-level-one-item" value="id_membership"/>
		</div>
		<!--End of hidden controls-->
<?php	
	} else if ($page=="managemembershiptype_csv" && isset($_REQUEST['sortable']) && Authorize::isAllowable($config, "managemembershiptype_csv", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-center-80-percent">
			<div class="ui-sys-panel">
				<div class="ui-sys-panel-header">CSV MEMBERSHIP TYPE<br/>(Sotring Facility)</div>
				<div class="ui-sys-panel-body ui-sys-inline-controls-center">
					<form id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
						<input type="hidden" name="page" value="managemembershiptype_csv"/>
						<input type="hidden" name="downloadable" value="true"/>
<?php 
	$proceedControl="<input type=\"button\" id=\"command1\" value=\"Proceed\"/>";
	if (isset($_REQUEST['fieldname']) && isset($_REQUEST['fieldtype']) && isset($_REQUEST['fieldvalue']))	{
		$sortableContainer = Object::getUICSVDivSortable(Object::getLineToSortFromUserFieldData($_REQUEST['fieldname'], $_REQUEST['fieldtype'], $_REQUEST['fieldvalue']));
		echo $sortableContainer;
	} else {
?>
		Perhaps you have not selected any column
<?php
		$proceedControl="<a class=\"button-link\" href=\"".$thispage."?page=managemembershiptype\">Back to Membership Type</a>";
	}//end-if-else
?>				
					</form>
				</div>
				<div class="ui-sys-panel-footer"><input type="hidden" id="perror" value="for compliance only"/><?= $proceedControl ?></div>
			</div>
		</div>
		<!--Hidden control begin-->
		<div class="ui-sys-hidden">
			<input type="hidden" id="selected-level-one-item" value="id_membership"/>
		</div>
		<!--End of hidden controls-->
<script type="text/javascript">
(function($)	{
	$('#command1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
	} else if ($page=="managemembershiptype_csv" && Authorize::isAllowable($config, "managemembershiptype_csv", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-center-80-percent">
			<div class="ui-sys-panel">
				<div class="ui-sys-panel-header">CSV MEMBERSHIP TYPE<br/>(Available Columns)</div>
				<div class="ui-sys-panel-body ui-sys-inline-controls-center">
					<form id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
						<input type="hidden" name="page" value="managemembershiptype_csv"/>
						<input type="hidden" name="sortable" value="true"/>
<?php
	$listOfAvailableColumnsInTables = Object::getUICSVTables(MembershipType::getAvailableViewableColumns());
	echo $listOfAvailableColumnsInTables;
?>
						<div class="ui-sys-inline-controls-center ui-sys-error-message" id="perror"></div>
					</form>
				</div>
				<div class="ui-sys-panel-footer"><input type="button" value="Proceed" id="command1"/></div>
			</div>
		</div>
		<!--Hidden control begin-->
		<div class="ui-sys-hidden">
			<input type="hidden" id="selected-level-one-item" value="id_membership"/>
		</div>
		<!--End of hidden controls-->
<script type="text/javascript">
(function($)	{
	$('#command1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
	} else 
?>
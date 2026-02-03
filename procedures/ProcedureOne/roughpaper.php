<?php 

if ($page=="managejobtitle_delete" && isset($_REQUEST['report']) && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managejobtitle_delete", "normal", "setlog", "-1", "-1"))	{
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$jobTitle1 = null;
		$login1 = null;
		try	{
			$jobTitle1 = new JobTitle($database, $_REQUEST['id'], $conn);
			$login1 = new Login($database, $_SESSION['login'][0]['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$enableUpdate=false;
		$errorMessage="";
?>
		<div class="ui-sys-center-80-percent">
			<div class="ui-sys-panel">
				<div class="ui-sys-panel-header">Deleted <?= $jobTitle1->getJobName() ?></div>
				<div class="ui-sys-panel-body ui-sys-inline-controls-center">
<?php 
	try	{
		$jobTitle1->commitDelete(); $enableUpdate=true;
	} catch (Exception $e)	{
		$errorMessage=$e->getMessage();
		$enableUpdate=false;
	}
	if ($enableUpdate)	{
?>
		Job Title <i><?= $jobTitle1->getJobName() ?></i> has been deleted succesful
<?php	
	} else {
?>
		There were problems in deleting the Job Title <br />
		Error: <?= $errorMessage ?>
<?php	
	}//end-if-else 
?>				
				</div>
				<div class="ui-sys-panel-footer">
					<a class="button-link" href="<?= $thispage ?>?page=managejobtitle">Back to Job Title</a>
				</div>
			</div>
		</div>
<?php
		mysql_close($conn);
		$jobname=$jobTitle1->getJobName();
		if ($enableUpdate)	Accounting::addLog($config, $date, $login1->getLoginName(), "managejobtitle_delete", "Deleted $jobname");
?>
		<!--Hidden control begin-->
		<div class="ui-sys-hidden">
			<input type="hidden" id="selected-level-one-item" value="id_users"/>
		</div>
		<!--End of hidden controls-->
<?php		
	} else if ($page=="managejobtitle_delete" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managejobtitle_delete", "normal", "setlog", "-1", "-1"))	{
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$jobTitle1 = null;
		try	{
			$jobTitle1 = new JobTitle($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		mysql_close($conn);
?>
		<div class="ui-sys-center-80-percent">
			<div class="ui-sys-panel">
				<div class="ui-sys-panel-header">Delete <i><?= $jobTitle1->getJobName() ?></i></div>
				<div class="ui-sys-panel-body ui-sys-inline-controls-center">
					<div>Are you sure you want to delete <?= $jobTitle1->getJobName() ?>?</div>
					<div>
						<a title="WARNING: This action CAN NOT BE REVERSED" class="button-link" href="<?= $_SERVER['PHP_SELF'] ?>?page=managejobtitle_delete&id=<?= $jobTitle1->getJobId() ?>&report=yes">Yes</a>&nbsp;&nbsp;
						<a class="button-link" href="<?= $_SERVER['PHP_SELF'] ?>?page=managejobtitle">Cancel</a>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<!--Hidden control begin-->
		<div class="ui-sys-hidden">
			<input type="hidden" id="selected-level-one-item" value="id_users"/>
		</div>
		<!--End of hidden controls-->
<?php		
	} else if ($page=="managejobtitle_edit" && isset($_REQUEST['report']) && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managejobtitle_edit", "normal", "setlog", "-1", "-1"))	{
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$jobname=mysql_real_escape_string($_REQUEST['jobname']);
		$jobdetails=mysql_real_escape_string($_REQUEST['jobdetails']);
		$enableUpdate=false;
		$login1 = null;
		$jobTitle1 = null;
		try	{
			$login1 = new Login($database, $_SESSION['login'][0]['id'], $conn);
			$jobTitle1 = new JobTitle($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		if ($jobname != $jobTitle1->getJobName())	{
			$jobTitle1->setJobName($jobname); $enableUpdate=true;
		}
		if ($jobdetails != $jobTitle1->getJobDetails())	{
			$jobTitle1->setJobDetails($jobdetails); $enableUpdate=true;
		}
?>
		<div class="ui-sys-center-80-percent">
			<div class="ui-sys-panel">
				<div class="ui-sys-panel-header">Job Title -- Edited</div>
				<div class="ui-sys-panel-body ui-sys-inline-controls-center">
<?php 
	if ($enableUpdate)	{
		try	{
			$jobTitle1->commitUpdate();
		} catch (Exception $e)	{ die($e->getMessage()); }
?>
		Job Title <i><?= $jobTitle1->getJobName() ?></i> has been added succesful to the system
<?php
	} else 	{
?>
		Nothing were updated; You did not update Anything
<?php
	}//end-if-else
?>				
				</div>
				<div class="ui-sys-panel-footer">
					<a class="button-link" href="<?= $thispage ?>?page=managejobtitle">Back to Job Title</a>
				</div>
			</div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate)	Accounting::addLog($config, $date, $login1->getLoginName(), "managejobtitle_edit", "Edited $jobname");
?>
		<!--Hidden control begin-->
		<div class="ui-sys-hidden">
			<input type="hidden" id="selected-level-one-item" value="id_users"/>
		</div>
		<!--End of hidden controls-->
<?php		
	} else if ($page=="managejobtitle_edit" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managejobtitle_edit", "normal", "setlog", "-1", "-1"))	{
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$jobTitle1 = null;
		try	{
			$jobTitle1 = new JobTitle($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
?>
		<div class="ui-sys-center-80-percent">
			<div class="ui-sys-panel">
				<div class="ui-sys-panel-header">Job Title Edit</div>
				<div class="ui-sys-panel-body">
					<form id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
						<input type="hidden" name="page" value="managejobtitle_edit"/>
						<input type="hidden" name="report" value="ihkx"/>
						<input type="hidden" name="id" value="<?= $jobTitle1->getJobId() ?>"/>
						<table class="ui-sys-center-80-percent ui-sys-display-table">
							<tr>
								<td><label for="jobname">Job Title Name</label></td>
								<td><input value="<?= $jobTitle1->getJobName() ?>" type="text" id="jobname" name="jobname" size="32" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Job Title Name <?= $msgA48Name ?>"/></td>
							</tr>
							<tr>
								<td><label for="jobdetails">Job Title Details</label></td>
								<td><input value="<?= $jobTitle1->getJobDetails() ?>" type="text" id="jobdetails" name="jobdetails" size="32" required pattern="<?= $exprA64Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA64Name ?>" validate_message="Job Title Details <?= $msgA64Name ?>"/></td>
							</tr>
							<tr>
								<td id="perror" colspan="2" class="ui-sys-error-message"></td>
							</tr>
						</table>
					</form>
				</div>
				<div class="ui-sys-panel-footer">
					<div class="ui-sys-right">
						<input type="button" id="commandadd1" value="Commit Edit Job Title"/>
					</div>
				</div>
			</div>
		</div>
		<!--Hidden control begin-->
		<div class="ui-sys-hidden">
			<input type="hidden" id="selected-level-one-item" value="id_users"/>
		</div>
		<!--End of hidden controls-->
<script type="text/javascript">
	(function($)	{
		$('#commandadd1').on('click', function(event)	{
			event.preventDefault();
			generalFormSubmission(this, 'form1', 'perror');
		});
	})(jQuery);
</script>	
<?php		
	} else if ($page=="managejobtitle_detail" && isset($_REQUEST['id']) && Authorize::isAllowable($config, "managejobtitle_detail", "normal", "setlog", "-1", "-1"))	{
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$jobTitle1 = null;
		try	{
			$jobTitle1 = new JobTitle($database, $_REQUEST['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage());	}
		mysql_close($conn);
?>
		<div class="ui-sys-center-80-percent">
			<div class="ui-sys-panel">
				<div class="ui-sys-panel-header">Details of Job Title <i><?= $jobTitle1->getJobName() ?></i></div>
				<div class="ui-sys-panel-body">
					<table class="ui-sys-display-table ui-sys-center-80-percent">
						<tr>
							<td>Job Title Name</td>
							<td><?= $jobTitle1->getJobName() ?></td>
						</tr>
						<tr>
							<td>Job Title Details</td>
							<td><?= $jobTitle1->getJobDetails() ?></td>
						</tr>
					</table>
				</div>
				<div class="ui-sys-panel-footer">
					<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "managejobtitle_edit", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Edit <?= $jobTitle1->getJobName() ?>" href="<?= $thispage ?>?page=managejobtitle_edit&id=<?= $jobTitle1->getJobId() ?>"><img alt="DAT" src="../sysimage/buttonedit.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "managejobtitle_delete", "normal", "donotsetlog", "-1", "-1"))	{
?>
		<a title="Delete <?= $jobTitle1->getJobName() ?>" href="<?= $thispage ?>?page=managejobtitle_delete&id=<?= $jobTitle1->getJobId() ?>"><img alt="DAT" src="../sysimage/buttondelete.png"/></a>
<?php
	}
	if (Authorize::isAllowable($config, "managesystemfirewall", "normal", "donsetlog", "-1", "-1"))	{
?>
		<a title="Set System Firewall for <?= $jobTitle1->getJobName() ?>" href="<?= $thispage ?>?page=managesystemfirewall&type=jobtitle&id=<?= $jobTitle1->getJobId() ?>"><img alt="DAT" src="../sysimage/buttonfirewall.png"/></a>
<?php	
	}
?>
						<span class="ui-sys-clear-both">&nbsp;</span><br />
						<button id="backRedirect" data-redirect="<?= $thispage ?>?page=managejobtitle">Back To Job Title Management</button>
					</div>
				</div>
			</div>
		</div>
		<!--Hidden control begin-->
		<div class="ui-sys-hidden">
			<input type="hidden" id="selected-level-one-item" value="id_users"/>
		</div>
		<!--End of hidden controls-->
<?php		
	} else if ($page=="managejobtitle_add" && isset($_REQUEST['report']) && Authorize::isAllowable($config, "managejobtitle_add", "normal", "setlog", "-1", "-1"))	{
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		$jobname=mysql_real_escape_string($_REQUEST['jobname']);
		$jobdetails=mysql_real_escape_string($_REQUEST['jobdetails']);
		$enableUpdate=false; 
		$login1 = null;
		try	{
			$login1 = new Login($database, $_SESSION['login'][0]['id'], $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$query="INSERT INTO jobTitle(jobName, jobDetails) VALUES('$jobname', '$jobdetails')";
		$result=mysql_db_query($database, $query, $conn) or die("Could not execute query");
		$enableUpdate = true;
?>
		<div class="ui-sys-center-80-percent">
			<div class="ui-sys-panel">
				<div class="ui-sys-panel-header">Job Title --Added</div>
				<div class="ui-sys-panel-body ui-sys-inline-controls-center">
<?php 
	if ($enableUpdate)	{
?>
		The New Job Title <i><?= $jobname ?></i> has been added succesful
<?php
	} else {
?>
		There were problems in Adding a New Job Title
<?php
	}//end-if-else
?>
				</div>
				<div class="ui-sys-panel-footer">
					<a class="button-link" href="<?= $thispage ?>?page=managejobtitle">Back to Job Title</a>
				</div>
			</div>
		</div>
<?php
		mysql_close($conn);
		if ($enableUpdate)	Accounting::addLog($config, $date, $login1->getLoginName(), "managejobtitle_add", "Added $jobname");
?>
		<!--Hidden control begin-->
		<div class="ui-sys-hidden">
			<input type="hidden" id="selected-level-one-item" value="id_users"/>
		</div>
		<!--End of hidden controls-->
<?php		
	} else if ($page=="managejobtitle_add" && Authorize::isAllowable($config, "managejobtitle_add", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-center-80-percent">
			<div class="ui-sys-panel">
				<div class="ui-sys-panel-header">Job Title Add</div>
				<div class="ui-sys-panel-body">
					<form id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
						<input type="hidden" name="page" value="managejobtitle_add"/>
						<input type="hidden" name="report" value="ihkx"/>
						<table class="ui-sys-center-80-percent ui-sys-display-table">
							<tr>
								<td><label for="jobname">Job Title Name</label></td>
								<td><input type="text" id="jobname" name="jobname" size="32" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Job Title Name <?= $msgA48Name ?>"/></td>
							</tr>
							<tr>
								<td><label for="jobdetails">Job Title Details</label></td>
								<td><input type="text" id="jobdetails" name="jobdetails" size="32" required pattern="<?= $exprA64Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA64Name ?>" validate_message="Job Title Details <?= $msgA64Name ?>"/></td>
							</tr>
							<tr>
								<td id="perror" colspan="2" class="ui-sys-error-message"></td>
							</tr>
						</table>
					</form>
				</div>
				<div class="ui-sys-panel-footer">
					<div class="ui-sys-right">
						<input type="button" id="commandadd1" value="Commit Add Job Title"/>
					</div>
				</div>
			</div>
		</div>
		<!--Hidden control begin-->
		<div class="ui-sys-hidden">
			<input type="hidden" id="selected-level-one-item" value="id_users"/>
		</div>
		<!--End of hidden controls-->
<script type="text/javascript">
	(function($)	{
		$('#commandadd1').on('click', function(event)	{
			event.preventDefault();
			generalFormSubmission(this, 'form1', 'perror');
		});
	})(jQuery);
</script>
<?php		
	} else if ($page=="managejobtitle" && Authorize::isAllowable($config, "managejobtitle", "normal", "setlog", "-1", "-1"))	{
?>
		<div class="ui-sys-center-80-percent">
			<div class="ui-sys-panel">
				<div class="ui-sys-panel-header">System Job Title</div>
				<div class="ui-sys-panel-body">
					<!--Begin Search UIs now comes here-->
					<!--Block ONE BEGIN Search box-->
					<div class="ui-sys-search-container">
						<input title="Atleast Three Characters should be supplied" type="text" required placeholder="ABC" size="32" /> 
						<a title="Click To Search" class="click-to-search" data-next-page="<?= $thispage ?>?page=managejobtitle" data-min-character="3"><img src="../sysimage/buttonsearch.png" alt="DAT"/></a>
					</div>
					<!--Block ONE ENDS-->
<?php 
	if (isset($_REQUEST['searchtext']))	{
		$searchtext=$_REQUEST['searchtext'];
		//Saving the Authorization Rules, this will save time for not calling Authorization each time 
		$blnAuthorizationDetail = Authorize::isAllowable($config, "managejobtitle_detail", "normal", "donotsetlog", "-1", "-1");
?>
		<!--Block Two Begins: with search results-->
		<div class="ui-sys-search-results">
			<label id="statustextlabel"></label>
			<table>
				<thead>
					<tr>
						<th>S/N</th>
						<th>Icon</th>
						<th>Job Title Name</th>
						<th>Job Title Details</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
<?php 
	//Beginning of Loading Data 
	//End of Loading Data
	$count = 0;
	$bgcolor="";
	$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
	$query=JobTitle::getQueryText();
	$result=mysql_db_query($database, $query, $conn) or die("Could not execute query");
	while (list($id)=mysql_fetch_row($result))	{
		$jobTitle1 = null;
		try	{
			$jobTitle1 = new JobTitle($database, $id, $conn);
		} catch (Exception $e)	{ die($e->getMessage()); }
		$matrix1 = new SearchMatrix($searchtext); //Need to be recreated for each Object
		if ($jobTitle1->searchMatrix($matrix1)->evaluateResult())	{
			$count++;
			$bgcolor="";
			if (($count % 2) == 0) $bgcolor="style=\"background-color: #bcbcbc;\"";
?>
			<tr <?= $bgcolor ?>>
				<td><?= $count ?></td>
				<td><a class="ui-sys-search-results-icon"><img alt="DAT" src="../sysimage/jobtitle.png"/></a></td>
				<td><?= $jobTitle1->getJobName() ?></td>
				<td><?= $jobTitle1->getJobDetails() ?></td>
				<td>
<?php 
	if ($blnAuthorizationDetail)	{
?>
		<a href="<?= $_SERVER['PHP_SELF'] ?>?page=managejobtitle_detail&id=<?= $jobTitle1->getJobId() ?>" title="Click Here to View Details of <?= $jobTitle1->getJobName() ?>" class="ui-sys-search-results-controls"><img alt="DAT" src="../sysimage/buttondetail.png"/></a>
<?php		
	}
?>
				</td>
			</tr>
<?php
		} //end-if
	}//end-while
	mysql_close($conn);
?>				
					<input type="hidden" id="saveresultsstorage" value="<?= $count ?>"/>
				</tbody>
				<tfoot></tfoot>
			</table>
		</div>
		<!--Block Two Ends here-->
<?php
	}//end--if 
?>
					<!--Block Three Begins Search Controls CSV and Add-->
					<div class="ui-sys-search-controls ui-sys-right">
<?php 
	if (Authorize::isAllowable($config, "managejobtitle_csv", "normal", "donotsetlog", "-1", "-1"))	{
?>
						<a title="Download CSV/EXCEL Formatted Data" href=""><img src="../sysimage/buttoncsv.png" alt="DAT"/></a>
<?php 
	}
	if (Authorize::isAllowable($config, "managejobtitle_add", "normal", "donotsetlog", "-1", "-1"))	{
?>
						<a title="Add a New System Job Title" href="<?= $_SERVER['PHP_SELF'] ?>?page=managejobtitle_add"><img src="../sysimage/buttonadd.png" alt="DAT"/></a>
<?php 
	}
?>
					</div>
					<!--Block Three Ends -->
					<!--End of Search UIs-->
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<!--Hidden control begin-->
		<div class="ui-sys-hidden">
			<input type="hidden" id="selected-level-one-item" value="id_users"/>
		</div>
		<!--End of hidden controls-->
<?php	
	} else

?>
<?php 
//Expects profile1 and student1 , database and conn
//ExtraFilter should contains image path
if (is_null($profile1) || is_null($student1)) die("Source of Data Were not set");
//Returns div.ui-sys-display-block
//Since this is not used for saving anything you can use the extraFilter to get the absolute imagepath
$login1 = $student1->getLogin();
?>
<div class="ui-sys-printable-block ui-sys-printable-header">
	<div class="ui-sys-profile-logo">
		<img src="<?= $profile1->getExtraFilter() ?>" alt="PF"/>
	</div>
	<div class="ui-sys-header-content">
		<div><?= $profile1->getProfileName() ?></div>
<?php 
	if (! is_null($profile1->getWebsite()))	{
?>
		<span>Website <?=  $profile1->getWebsite() ?></span>&nbsp;&nbsp;
<?php
	}
	if (! is_null($profile1->getEmailList()))	{
?>
		<span>Email <?=  $profile1->getEmailList()[0] ?></span>&nbsp;&nbsp;
<?php
	}
	if (! is_null($profile1->getTelephoneList()))	{
?>
		<span>Telephone <?=  $profile1->getTelephoneList()[0] ?></span><br/>
<?php
	}
	if (! is_null($profile1->getPostalAddress()))	{
?>
		<span>Postal Address <?=  $profile1->getPostalAddress() ?></span>
<?php
	}
?>
		<br/><span class="ui-sys-admission-title">Admission For <?= $login1->getFullname() ?></span>
	</div>
	<div class="ui-sys-user-photo">
		<img src="<?= $student1->getExtraFilter() ?>" alt="SF"/>
	</div>
	<div class="ui-sys-clearboth">&nbsp;</div>
</div>
<!--Begin of Printable Block-->
<div class="ui-sys-printable-block">
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Firstname</span>
			<span class="ui-sys-printable-section-data"><?= $login1->getFirstname() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Middlename</span>
			<span class="ui-sys-printable-section-data"><?= $login1->getMiddlename() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Lastname</span>
			<span class="ui-sys-printable-section-data"><?= $login1->getLastname() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
	<div class="ui-sys-clearboth">&nbsp;</div>
</div>
<!--End of Printable Block-->
<!--Begin of Printable Block-->
<div class="ui-sys-printable-block">
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Date of Birth</span>
			<span class="ui-sys-printable-section-data"><?= DateAndTime::convertFromDateTimeObjectToGUIDateFormat($login1->getDOB()) ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php 
	if (! is_null($login1->getSex()))	{
?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Sex</span>
			<span class="ui-sys-printable-section-data"><?= $login1->getSex()->getSexName() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php 
	}
	if (! is_null($login1->getMarital()))	{
?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Marital Status</span>
			<span class="ui-sys-printable-section-data"><?= $login1->getMarital()->getMaritalName() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php 
	}
	if (! is_null($student1->getDisability()))	{
?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Disability</span>
			<span class="ui-sys-printable-section-data"><?= $student1->getDisability() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php 
	}
?>
	<div class="ui-sys-clearboth">&nbsp;</div>
</div>
<!--End of Printable Block-->
<!--Begin of Printable Block-->
<div class="ui-sys-printable-block">
<?php
	if (! is_null($student1->getCourse()))	{
?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Course Name</span>
			<span class="ui-sys-printable-section-data"><?= $student1->getCourse()->getCourseName() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php 
	}
	if (! is_null($student1->getCurrentYear()))	{
?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Current Year of Study</span>
			<span class="ui-sys-printable-section-data"><?= $student1->getCurrentYear() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php 
	}
	if (! is_null($student1->getCurrentSemester()))	{
?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Current Semester of Study</span>
			<span class="ui-sys-printable-section-data"><?= $student1->getCurrentSemester() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php 
	}
	if (! is_null($student1->getRegistrationNumber()))	{
?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Registration Number</span>
			<span class="ui-sys-printable-section-data"><?= $student1->getRegistrationNumber() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php 
	}
	if (! is_null($student1->getCurrentAccademicYear()))	{
?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Current Accademic Year</span>
			<span class="ui-sys-printable-section-data"><?= $student1->getCurrentAccademicYear()->getAccademicYear() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php 
	}
	if (! is_null($student1->getAdmissionBatch()))	{
?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Admitted On (Batch Of) </span>
			<span class="ui-sys-printable-section-data"><?= $student1->getAdmissionBatch()->getAccademicYear() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php
	}
	if (! is_null($student1->getListOfBatches()))	{
?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Member of Batch(es) </span>
			<span class="ui-sys-printable-section-data">
<?php 
		$list1 = $student1->getListOfBatches();
		$listString = "";
		for ($i=0; $i < sizeof($list1); $i++)	{
			$batch1 = $list1[$i];
			if ($i == 0) $listString = $batch1->getAccademicYear();
			else $listString .= ", ".$batch1->getAccademicYear();
		}
		echo $listString;
?>			
			</span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php
	}
?>
	<div class="ui-sys-clearboth">&nbsp;</div>
</div>
<!--End of Printable Block-->
<!--Begin of Academic History-->
<?php
	$displayList1 = new DisplayList();
	$displayList1->setDisplayName("-----------------Accademic History------------------");
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Name of Institution");$intent1->putExtra("tagName", "institutionName");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Specialization");$intent1->putExtra("tagName", "specialization");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Education Level");$intent1->putExtra("tagName", "levelId");
	$intent1->putExtra("className", "EducationLevel"); $intent1->putExtra("dataType", "Object");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Award");$intent1->putExtra("tagName", "award");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Grade");$intent1->putExtra("tagName", "grade");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "From (Year)");$intent1->putExtra("tagName", "startYear");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "To (Year)");$intent1->putExtra("tagName", "endYear");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	//Fetching History 
	$instutionList1 = $student1->getListOfAccademicInstitutions();
	$blockToDisplay = UIServices::displayUIPrintableBlocks($database, $conn, $instutionList1, $displayList1);
	echo $blockToDisplay;
?>
<!--End of Academic History-->
<div class="ui-sys-printable-block">
<?php 
	if (! is_null($student1->getCitizenship()))	{
?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Citizenship</span>
			<span class="ui-sys-printable-section-data"><?= $student1->getCitizenship()->getCountryName() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php 
	}
	if (! is_null($student1->getNativeplace()))	{
?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Native Place</span>
			<span class="ui-sys-printable-section-data"><?= $student1->getNativeplace() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php 
	}
	if (! is_null($student1->getOccupation()))	{
?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Occupation</span>
			<span class="ui-sys-printable-section-data"><?= $student1->getOccupation() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php 
	}
?>
	<div class="ui-sys-clearboth">&nbsp;</div>
</div>
<div class="ui-sys-printable-block">
<?php 
	if (! is_null($student1->getDenomination()))	{
?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Religion</span>
			<span class="ui-sys-printable-section-data"><?= $student1->getDenomination()->getReligion()->getReligionName() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Denomination</span>
			<span class="ui-sys-printable-section-data"><?= $student1->getDenomination()->getDenominationName() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php 
	}
?>
	<div class="ui-sys-clearboth">&nbsp;</div>
</div>
<div class="ui-sys-printable-block">
<?php 
	if (! is_null($student1->getPostalAddress()))	{
?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Postal Address</span>
			<span class="ui-sys-printable-section-data"><?= $student1->getPostalAddress() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php 
	}
	if (! is_null($student1->getPhysicalAddress()))	{
?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Physical Address</span>
			<span class="ui-sys-printable-section-data"><?= $student1->getPhysicalAddress() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php 
	}
	if (! is_null($login1->getPhone()))	{
?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Phone</span>
			<span class="ui-sys-printable-section-data"><?= $login1->getPhone() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php 
	}
	if (! is_null($login1->getEmail()))	{
?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Email</span>
			<span class="ui-sys-printable-section-data"><?= $login1->getEmail() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php 
	}
?>
	<div class="ui-sys-clearboth">&nbsp;</div>
</div>
<!--Begin of Employment History-->
<?php
if ($student1->isEmployed())	{
	$displayList1 = new DisplayList();
	$displayList1->setDisplayName("-----------------Employment History------------------");
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Name of Employer");$intent1->putExtra("tagName", "employerName");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Employer Postal Address");$intent1->putExtra("tagName", "postalAddress");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Employer Physical Address");$intent1->putExtra("tagName", "physicalAddress");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Employer Fax");$intent1->putExtra("tagName", "fax");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Employer Telephone");$intent1->putExtra("tagName", "telephone");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Employer Mobile");$intent1->putExtra("tagName", "mobile");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Employer Email");$intent1->putExtra("tagName", "email");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Employment From (Year)");$intent1->putExtra("tagName", "startYear");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Employment To (Year)");$intent1->putExtra("tagName", "endYear");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Employment Extra Information");$intent1->putExtra("tagName", "extraInformation");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	//Fetching History 
	$instutionList1 = $student1->getListOfEmployers();
	$blockToDisplay = UIServices::displayUIPrintableBlocks($database, $conn, $instutionList1, $displayList1);
	echo $blockToDisplay;
} //--end-if-employed
?>
<!--End of Employment History-->
<!--Begin of Sponsorship History-->
<?php
	$displayList1 = new DisplayList();
	$displayList1->setDisplayName("-----------------Financial Sponsor------------------");
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Name of Sponsor");$intent1->putExtra("tagName", "sponsorName");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Sponsor Category");$intent1->putExtra("tagName", "payerId");
	$intent1->putExtra("className", "FeePayer"); $intent1->putExtra("dataType", "Object");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Sponsor Postal Address");$intent1->putExtra("tagName", "postalAddress");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Sponsor Physical Address");$intent1->putExtra("tagName", "physicalAddress");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Sponsor Fax");$intent1->putExtra("tagName", "fax");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Sponsor Telephone");$intent1->putExtra("tagName", "telephone");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Sponsor Mobile");$intent1->putExtra("tagName", "mobile");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Sponsor Email");$intent1->putExtra("tagName", "email");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "What the Sponsor is Sponsoring");$intent1->putExtra("tagName", "financedItem");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Sponsor Extra Information");$intent1->putExtra("tagName", "extraInformation");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	//Fetching History 
	$instutionList1 = $student1->getListOfSponsors();
	$blockToDisplay = UIServices::displayUIPrintableBlocks($database, $conn, $instutionList1, $displayList1);
	echo $blockToDisplay;
?>
<!--End of Sponsorship History-->
<!--Begin of Bank Accounts-->
<?php
	$displayList1 = new DisplayList();
	$displayList1->setDisplayName("-----------------Bank Account------------------");
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Account Name");$intent1->putExtra("tagName", "accountName");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Account Number");$intent1->putExtra("tagName", "bankAccount");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Bank Name");$intent1->putExtra("tagName", "bankName");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Branch Name");$intent1->putExtra("tagName", "branchName");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	//Fetching History 
	$instutionList1 = $student1->getBankAccountList();
	$blockToDisplay = UIServices::displayUIPrintableBlocks($database, $conn, $instutionList1, $displayList1);
	echo $blockToDisplay;
?>
<!--End of Bank Accounts-->
<!--Begin of Next Of Kins History-->
<?php
	$displayList1 = new DisplayList();
	$displayList1->setDisplayName("-----------------Next Of Kins------------------");
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Name of Next Of Kin");$intent1->putExtra("tagName", "kinName");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Relationship with Next Of Kin");$intent1->putExtra("tagName", "relationship");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Next Of Kin Postal Address");$intent1->putExtra("tagName", "postalAddress");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Next Of Kin Physical Address");$intent1->putExtra("tagName", "physicalAddress");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Next Of Kin Fax");$intent1->putExtra("tagName", "fax");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Next Of Kin Telephone");$intent1->putExtra("tagName", "telephone");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Next Of Kin Mobile");$intent1->putExtra("tagName", "mobile");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Next Of Kin Email");$intent1->putExtra("tagName", "email");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	$intent1 = new Intent("Ztong");
	$intent1->putExtra("labelCaption", "Next of Kin Extra Information");$intent1->putExtra("tagName", "extraInformation");
	$intent1->putExtra("className", "None"); $intent1->putExtra("dataType", "text");
	$displayList1->add($intent1);
	//Fetching History 
	$instutionList1 = $student1->getListOfNextOfKins();
	$blockToDisplay = UIServices::displayUIPrintableBlocks($database, $conn, $instutionList1, $displayList1);
	echo $blockToDisplay;
?>
<!--End of Next of Kin History-->
<div class="ui-sys-printable-block">
<?php 
	if (! is_null($student1->getExtraInformation()))	{
?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Student Extra Information</span>
			<span class="ui-sys-printable-section-data"><?= $student1->getExtraInformation() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php 
	}
?>
	<div class="ui-sys-clearboth">&nbsp;</div>
</div>
<div class="ui-sys-printable-block">
<?php 
	if (! is_null($student1->getRegistrationTime()) && ($student1->getRegistrationTime()->getDateAndTimeString() != "0000:00:00:00:00:00"))	{
?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Registration Time </span>
			<span class="ui-sys-printable-section-data"><?= $student1->getRegistrationTime()->getDateAndTimeString() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php 
	}
	if ($login1->isAdmitted())	{
?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">ADMITTED, Admitted On </span>
			<span class="ui-sys-printable-section-data"><?= $login1->getAdmissionTime()->getDateAndTimeString() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">ADMITTED By </span>
			<span class="ui-sys-printable-section-data"><?= $login1->getAdmittedBy() ?></span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php 
	} else {
		?>
	<!--DIM Section Begin-->
	<div class="ui-sys-printable-section">
		<div class="ui-sys-printable-section-dimension">
			<span class="ui-sys-printable-section-label">Student Not Admitted</span>
			<span class="ui-sys-printable-section-data">NOT YET ADMITTED</span>
		</div>
	</div>
	<!--DIM Section Ends-->
<?php 
	}
?>
	<div class="ui-sys-clearboth">&nbsp;</div>
</div>

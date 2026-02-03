<?php 
	public function getMyGPADataStructure($subjectDataStructure1, $resultsDataStructure1)	{
		/*
		INPUT: 	subjectDataStructure1[cummulativeSemester][index] = transactionId 
				resultsDataStructure1[cummulativeSemester][index]['transactionId'] = transactionId 
																['rawMarks'] = 80 
																['gradeId'] = gradeId 
																[transactionId] = gradeId 
		OUTPUT: ds1[cummulativeSemester]['gpa'] = GPA
										['cleared'] = true //1 means cleared 0 failed 
										['awardId'] = null; reference to ClassOfAward
										['totalUnits'] = 0.0 
										['totalModulePoints'] = 0.0 
		*/
		if (is_null($subjectDataStructure1) || is_null($resultsDataStructure1)) return null;
		$course1 = $this->course;
		if (is_null($course1)) Object::shootException("The student is not linked to any course");
		if (is_null($course1->getEducationLevel())) Object::shootException("The Course is not linked to any level");
		$level1 = $course1->getEducationLevel();
		$ds1 = array();
		foreach ($subjectDataStructure1 as $cummulativeSemester => $subjectDataArray1)	{
			$ds1[$cummulativeSemester] = array();
			$ds1[$cummulativeSemester]['gpa'] = 0.0;
			$ds1[$cummulativeSemester]['cleared'] = true; //Default 1
			$ds1[$cummulativeSemester]['awardId'] = null;
			$ds1[$cummulativeSemester]['totalUnits'] = 0.0;
			$ds1[$cummulativeSemester]['totalModulePoints'] = 0.0;
			if (! isset($resultsDataStructure1[$cummulativeSemester])) continue; //No results for this semester
			$totalModulePoints = 0;
			$totalUnits = 0;
			$clearedSemester = true;
			foreach ($subjectDataArray1 as $index => $transactionId)	{
				//Units has to be taken as denominator even if results are not found 
				$transaction1 = new CourseAndSubjectTransaction($this->database, $transactionId, $this->conn);
				if (is_null($transaction1->getSubject())) Object::shootException("No Subject Attached to this Student");
				$subject1 = $transaction1->getSubject();
				$totalUnits = $totalUnits + intval($subject1->getTotalUnits());
				if (! isset($resultsDataStructure1[$cummulativeSemester][$index])) continue; //No results for this subject 
				if (! isset($resultsDataStructure1[$cummulativeSemester][$index][$transactionId])) continue; //Transaction not found 
				$gradeId = $resultsDataStructure1[$cummulativeSemester][$index][$transactionId];
				$grade1 = new Grade($this->database, $gradeId, $this->conn);
				if (($grade1->isIncomplete() || $grade1->isFailed()) && $clearedSemester)	$clearedSemester = false;
				$gradePoint = intval($grade1->getGradePoint());
				$totalModulePoints = $totalModulePoints + ($gradePoint * ($subject1->getTotalUnits()));
			}
			//Adjust only if totalUnits is not equal to zero 
			if ($totalUnits != 0)	{
				$gpa = ($totalModulePoints / $totalUnits);
				$gpa = Number::truncateFloatDecimal($gpa, Object::$__GPA_PRECISION);
				//Need to find its equivalent ClassOfAward 
				$awardId = ClassOfAward::getClassOfAwardFromGPAAndEducationLevel($this->database, $this->conn, $gpa, $level1->getLevelId());
				$gpa = Number::displayFloatDecimal($gpa, Object::$__GPA_PRECISION);
				$ds1[$cummulativeSemester]['gpa'] =  $gpa;
				$ds1[$cummulativeSemester]['cleared'] = $clearedSemester;
				if (! is_null($awardId)) $ds1[$cummulativeSemester]['awardId'] = $awardId;
				$ds1[$cummulativeSemester]['totalUnits'] = $totalUnits;
				$ds1[$cummulativeSemester]['totalModulePoints'] = $totalModulePoints;
			}
		}
		if (sizeof($ds1) == 0) $ds1 = null;
		return $ds1;
	}
	public function showMySubjectList($profile1)	{
		$student1 = $this;
		$database = $this->database;
		$conn = $this->conn;
		$ds1 = $this->getMySubjectListDataStructure($profile1);
		if (is_null($ds1)) Object::shootException("You do not have any subject the system returned an Empty Set");
		$accordion1 = "<div style=\"z-index: 0;\" class=\"ui-sys-accordion\">";
		for ($i=1; $i <= sizeof($ds1); $i++)	{
			//Semesterwise blocks 
			$yearAndSemesterArr = System::getYearAndSemesterFromACummulativeSemester($i, $profile1);
			if (is_null($yearAndSemesterArr)) continue;
			$year = $yearAndSemesterArr['year'];
			$semester = $yearAndSemesterArr['semester'];
			$titleText = System::scriptNumberAdjustment($year)." year, ".System::scriptNumberAdjustment($semester)." semester"."  (Semester Count is $i)";
			$accordion1 .= "<h3 style=\"z-index: 1;\">$titleText</h3><div style=\"z-index: 1;\"><table class=\"pure-table pure-table-horizontal\"><thead><tr><th></th><th>Subject</th><th>Code</th><th title=\"Lecture Hours\">LH</th><th title=\"Seminar and Tutorial Hours\">STH</th><th title=\"Assignment Hours\">AH</th><th title=\"Independent Study and Research Hours\">ISRH</th><th title=\"Practical Training Hours\">PTH</th><th title=\"Extra Hours\">EH</th><th title=\"CREDIT HOURS\">CRH</th><th title=\"TOTAL HOURS\">Total</th><th></th></tr></thead><tbody>";
			$transactionIdArr = $ds1[$i];
			$finalTotalHours = 0;
			$finalTotalCredits = 0;
			for ($index = 0; $index < sizeof($transactionIdArr); $index++)	{
				$transaction1 = new CourseAndSubjectTransaction($database, $transactionIdArr[$index], $conn); //We will catch Externally
				$subject1 = $transaction1->getSubject();
				$lectureHours = $transaction1->getLectureHours();
				$seminarAndTutorialHours = $transaction1->getSeminarAndTutorialHours();
				$assignmentHours = $transaction1->getAssignmentHours();
				$independentStudiesAndResearchHours = $transaction1->getIndependentStudiesAndResearchHours();
				$practicalTrainingHours = $transaction1->getPracticalTrainingHours();
				$extraHours = $transaction1->getExtraHours();
				$creditHours = $transaction1->getCreditHours();
				$totalHours = $transaction1->getTotalHours();
				$compulsoryText = "Optional Module";
				if ($transaction1->isCompulsory()) $compulsoryText = "Compulsory Module";
				$accordion1 .= "<tr><td>".($index + 1)."</td><td>".$subject1->getSubjectName()."</td><td>".$subject1->getSubjectCode()."</td><td>$lectureHours</td><td>$seminarAndTutorialHours</td><td>$assignmentHours</td><td>$independentStudiesAndResearchHours</td><td>$practicalTrainingHours</td><td>$extraHours</td><td><b>$creditHours</b></td><td><b>$totalHours</b></td><td>$compulsoryText</td></tr>";
				$finalTotalCredits += floatval($creditHours);
				$finalTotalHours += floatval($totalHours);
			}
			$accordion1 .= "<tr style=\"font-size: 1.1em;\"><td style=\"text-align: center;\" colspan=\"9\"><b>TOTAL</b></td><td><b>$finalTotalCredits</b></td><td><b>$finalTotalHours</b></td><td></td></tr></tbody></table></div>";
		}//end-foreach 
		
		$accordion1 .= "</div>";
		return $accordion1;
	}
	public function getMySubjectListDataStructure($profile1)	{
		$list1 = array();
		/*Lattest Cummulative Semester
		return list[cummulativeSemester][index] = mapId
		*/
		$student1 = $this;
		$database = $this->database;
		$conn = $this->conn;
		$cummulativeSemester = System::getCummulativeSemester($student1->getCurrentYear(), $student1->getCurrentSemester(), $profile1);
		$courseId = $student1->getCourse()->getCourseId();
		for ($i = 1; $i <= $cummulativeSemester; $i++)	{
			$list1[$i] = array(); //Now pack All Map Ids Here which
			$yearAndSemesterArr = System::getYearAndSemesterFromACummulativeSemester($i, $profile1);
			if (is_null($yearAndSemesterArr)) continue;
			$year = $yearAndSemesterArr['year'];
			$semester = $yearAndSemesterArr['semester'];
			$transactionIdArr = CourseAndSubjectTransaction::getTransactionListFromCourseYearAndSemester($database, $conn, $courseId, $year, $semester);
			for ($index = 0; $index < sizeof($transactionIdArr); $index++)	{
				$list1[$i][$index] = $transactionIdArr[$index];
			}
		}
		if (sizeof($list1) == 0) $list1 = null;
		return $list1;
	}
	public function showMyContinuousResults($profile1, $resultsFolder, $displayList1, $controlFlags)	{
		//Note profile1->extraFilter will be used
		$extraFilter = $profile1->getExtraFilter();
		$ds1 = $this->getMyContinuousResultsDataStructure($profile1, $resultsFolder);
		if (is_null($ds1)) return null;
		$student1 = $this;
		$technicalDataStructure1 = $profile1->getExtraFilter();
		$profile1->setExtraFilter($extraFilter); //Restore the original value
		
		/*
		DataStructure has the following format 
			list[cummulativeSemester][resultsId][transactionId]['rawMarks'] = marks [ie 80]
			list[cummulativeSemester][resultsId][transactionId]['gradeId'] = grades [ie A+]
			list[0][0][0]['rawMarks'] = 0 
			list[0][0][0]['gradeId'] = Technical Sup Info 
		*/
		$resultsUI = "<div style=\"z-index: 0;\" class=\"ui-sys-tabs\">";
		$tabHeader ="<ul style=\"z-index: 1;\">";
		$tabBody = "";
		$tabPrefix = "__zoomtong_company_limited_by_written_by_ndimangwa_fadhili_ngoya_";
		foreach ($ds1 as $cummulativeSemester => $resultsArray1)	{
			$yearAndSemesterArr = System::getYearAndSemesterFromACummulativeSemester($cummulativeSemester, $profile1);
			if (is_null($yearAndSemesterArr)) continue;
			$year = $yearAndSemesterArr['year'];
			$semester = $yearAndSemesterArr['semester'];
			$titleText = System::scriptNumberAdjustment($year)." year, ".System::scriptNumberAdjustment($semester)." semester"."  (Semester Count is $cummulativeSemester)";
			$myTabReference = $tabPrefix.$cummulativeSemester;
			$tabHeader .= "<li><a href=\"#$myTabReference\">$titleText</a></li>";
			$tabBody .= "<div id=\"$myTabReference\">";
			//We need to add our accordions inside the tabBody 
			$tabBody .= "<div style=\"z-index: 0;\" class=\"ui-sys-accordion\">";
			foreach ($resultsArray1 as $resultsId => $transactionArray1)	{
				$results1 = new Results($this->database, $resultsId, $this->conn);
				$titleText = "";
				if (! is_null($results1->getExamination())) $titleText = $results1->getExamination()->getExaminationName()." (".$results1->getExamination()->getExaminationCode().")";
				$tabBody .= "<h3 style=\"z-index: 1;\">$titleText</h3><div style=\"z-index: 1;\"><table class=\"pure-table pure-table-horizontal\"><thead><tr><th></th><th>Status</th><th>Module/Course Title</th><th>Marks</th><th>Grade</th><th>Grade Point</th><th>Credits</th><th>Module Points</th></tr></thead><tbody>";
				$transactionCount = 0;
				foreach ($transactionArray1 as $transactionId => $marksDataArray1)	{
					$transactionCount++;
					$rawMarks = $marksDataArray1['rawMarks'];
					$grade1 = null;
					$transaction1 = null;
					try {
						$grade1 = new Grade($this->database, $marksDataArray1['gradeId'], $this->conn);
						$transaction1 = new CourseAndSubjectTransaction($this->database, $transactionId, $this->conn);
					} catch (Exception $e)	{
						continue;
					}
					if (is_null($grade1)) continue;
					if (is_null($transaction1)) continue;
					$bgcolor = "blue";
					if ($grade1->isIncomplete())	{
						$bgcolor = "gold";
					} else if ($grade1->isFailed())	{
						$bgcolor = "red";
					}
					$subject1 = $transaction1->getSubject();
					if (is_null($subject1)) continue;
					$subjectName = $subject1->getSubjectName()."(".$subject1->getSubjectCode().")";
					$gradeName = $grade1->getGradeName();
					$gradePoint = $grade1->getGradePoint();
					$subjectCredits = $subject1->getTotalUnits();
					$modulePoints = intval($gradePoint) * intval($subjectCredits);
					$tabBody .= "<tr><td>$transactionCount</td><td><span style=\"background-color: $bgcolor;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td><td>$subjectName</td><td>$rawMarks</td><td>$gradeName</td><td>$gradePoint</td><td>$subjectCredits</td><td>$modulePoints</td></tr>";					
				}
				$tabBody .= "</tbody></table><br/><div class=\"ui-sys-inline-controls-center\">";
				$technicalInfoDataStructure1 = $this->getMyTechnicalResultsDataStructure($profile1, $resultsFolder, $resultsId);
				if (! is_null($technicalInfoDataStructure1))	{
					if (isset($technicalInfoDataStructure1[$cummulativeSemester][$resultsId]['technical']))	{
						$technicalData = $technicalInfoDataStructure1[$cummulativeSemester][$resultsId]['technical'];
						$commentText = "BEFORE SUPPLIMENTARY";
						if ($technicalInfoDataStructure1[$cummulativeSemester][$resultsId]['afterSupplimentary'] == ResultsGroup::$__IS_AFTER_SUPPLIMENTARY) $commentText="AFTER SUPPLIMENTARY";
						$technicalMessage = "";
						if ($technicalData == ResultsGroup::$__RESULTS_DISQUALIFIED)	{
							$technicalMessage = "<b>($commentText): FAILED</b>";
						} else if ($technicalData == ResultsGroup::$__RESULTS_FAILED)	{
							$technicalMessage = "<b>($commentText): DISQUALIFIED</b>";
						}
						$tabBody .= $technicalMessage; 
					}
				}
				$tabBody .= "</div><br/><div style=\"\">";
				if (! is_null($technicalDataStructure1) && isset($technicalDataStructure1[$cummulativeSemester][$resultsId]))	{
					$tabBody .= "<br/><table class=\"pure-table pure-table-horizontal\" style=\"font-size: 0.9em;\"><thead>";
					$tabBody .= "<tr><th colspan=\"4\" style=\"text-align: center;\">Technical Information</th></tr>";
					$tabBody .= "<tr><th></th><th></th><th>Module/Subject</th><th>Comment</th></tr>";
					$tabBody .= "</thead><tbody>";
					$rowCount = 0;
					$technicalDataStructureForSemesterAndResults1 = $technicalDataStructure1[$cummulativeSemester][$resultsId];
					foreach ($technicalDataStructureForSemesterAndResults1 as $transactionId => $cleared)	{
						$rowCount++;
						$commentText = "No Information";
						$bgcolor = "";
						if (intval($cleared) == ResultsGroup::$__RESULTS_DISQUALIFIED)	{
							$commentText = "Technically Disqualified (Core Module)";
							$bgcolor = "gold";
						} else if (intval($cleared) == ResultsGroup::$__RESULTS_FAILED)	{
							$commentText = "Technically Failed (General Module)";
							$bgcolor = "red";
						} else if (intval($cleared) == ResultsGroup::$__RESULTS_CLEARED)	{
							$commentText = "Technically Passed";
							$bgcolor = "blue";
						}
						$transaction1 = new CourseAndSubjectTransaction($this->database, $transactionId, $this->conn);
						$subjectName = $transaction1->getSubject()->getSubjectName()." (".$transaction1->getSubject()->getSubjectCode().")";
						$tabBody .= "<tr><td>$rowCount</td><td><span style=\"background-color: $bgcolor;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td><td>$subjectName</td><td>$commentText</td></tr>";
					}
					$tabBody .= "</tbody></table>";
				}
				$tabBody .= "</div></div>";
			}
			$tabBody .= "</div></div>";
		}
		$tabHeader .= "</ul>";
		$resultsUI .= $tabHeader.$tabBody;
		$resultsUI .= "</div>";
		return $resultsUI;
	}
	public function getMyTechnicalResultsDataStructure($profile1, $resultsFolder, $resultsId)	{
		/*
		RETURN: ds[cummulativeSemester][resultsId]['technical'] = ResultsGroup::$__CLEARED/FAILED/DISQUALIFIED/0
				ds[cummulativeSemester][resultsId]['afterSupplimentary'] = ResultsGroup::$__IS_AFTER_SUPPLIMENTARY
		*/
		$student1 = $this;
		$results1 = new Results($this->database, $resultsId, $this->conn);
		$filename = $resultsFolder.$results1->getRawMarksCSVFile();
		if (! file_exists($filename)) Object::shootException("Results File Does Not Exists");
		$resultsFile1 = new ResultsFile($this->database, $filename, $this->conn);
		$dataLine1 = $resultsFile1->getStudentResultsDataLine($this->studentId);
		if (is_null($dataLine1)) return null;
		$group1 = $results1->getGroup();
		if (is_null($group1)) Object::shootException("The Group for the results were not found");
		$currentYear = 0;
		$currentYear = $group1->getYear();
		$currentSemester = 0;
		if (is_null($results1->getExamination())) Object::shootException("There is no examination for the results");
		$examination1 = $results1->getExamination();
		//If Primary/Supplimentary put it here 
		$currentSemester = $examination1->getSemester();
		$cummulativeSemester = System::getCummulativeSemester($currentYear, $currentSemester, $profile1);
		$ds1 = array();
		$ds1[$cummulativeSemester] = array();
		$ds1[$cummulativeSemester][$resultsId] = array();
		$ds1[$cummulativeSemester][$resultsId]['technical'] = 0;
		$ds1[$cummulativeSemester][$resultsId]['afterSupplimentary'] = 0;
		if ($group1->isAfterSupplimentary()) $ds[$cummulativeSemester][$resultsId]['afterSupplimentary'] = ResultsGroup::$__IS_AFTER_SUPPLIMENTARY;
		if ($dataLine1->isDisqualified())	{
			$ds1[$cummulativeSemester][$resultsId]['technical'] = ResultsGroup::$__RESULTS_DISQUALIFIED;
		} else if ($dataLine1->isFailed())	{
			$ds1[$cummulativeSemester][$resultsId]['technical'] = ResultsGroup::$__RESULTS_FAILED;
		} else if ($dataLine1->isCleared())	{
			$ds1[$cummulativeSemester][$resultsId]['technical'] = ResultsGroup::$__RESULTS_CLEARED;
		} else {
			$ds1 = null;
		}
		return $ds1;
	}
	public function getMyContinuousResultsDataStructure($profile1, $resultsFolder)	{
		/*
		Note for Non Graduated Students Only, a Graduated Student is offered a transcript only 
		for only ResultsGroup which are not locked only 
		return 	
				list[cummulativeSemester][resultsId][transactionId]['rawMarks'] = marks [ie 80]
				list[cummulativeSemester][resultsId][transactionId]['gradeId'] = grades [ie A+]
				or return null 
				
			The above dataStructure is achieved by two dataStructures 
			1. list[cummulativeSemester][index]['transactionId']
				...............................['rawMarks']
				...............................['gradeId']
				AND 
			2. list[cummulativeSemester][resultsId][index] = index  , called resultsDs1
			//You can pack some data on $profile1->setExtraFilter to return to the calling function 
			You can format technicalDs1[cummulativeSemester][resultsId][transactionId] = cleared
		*/ 
		//Collecting Extra Information 
		$student1 = $this;
		if ($student1->isAlreadyGraduated()) return null;
		//Step 1: Get Subject List for this Student
		$subjectListDs1 = $this->getMySubjectListDataStructure($profile1);
		//Pick All semester even if it is current one what matters is the group is locked or not 
		if (is_null($subjectListDs1)) return null;
		//Step 2: Preparing data Structures
		$resultsDs1 = array();
		foreach ($subjectListDs1 as $cummulativeSemester => $subjectDataArray1)	{
			$resultsDs1[$cummulativeSemester] = array();
			$technicalDs1[$cummulativeSemester] = array();
			foreach ($subjectDataArray1 as $transactionId)	{
				$index = sizeof($resultsDs1[$cummulativeSemester]);
				$resultsDs1[$cummulativeSemester][$index] = array();
				$resultsDs1[$cummulativeSemester][$index]['transactionId'] = $transactionId;
			}
		}
		if (sizeof($resultsDs1) == 0) return null;
		//Step 3: Get List Of Batches 
		$batchListArray1 = $student1->getListOfBatches();
		if (is_null($batchListArray1)) Object::shootException("Student does not belong to any Available Batches");
		//Step 4: Get Course 
		$course1 = $student1->getCourse();
		if (is_null($course1)) Object::shootException("There is no course attached to this Student");
		//Step 5: Initialize data Structure 
		$ds1 = array(); 
		$helperTechnicalDs1 = array(); //Carries Actual Output  resultsId
		$technicalDs1 = array(); //Track per Examination examinationId
		//Step 6: Now filling the data Structure 
		foreach ($resultsDs1 as $cummulativeSemester => $subjectDataArray1)	{
			$yearAndSemesterArr = System::getYearAndSemesterFromACummulativeSemester($cummulativeSemester, $profile1);
			$year = $yearAndSemesterArr['year'];
			$semester = $yearAndSemesterArr['semester'];
			$ds1[$cummulativeSemester] = array();
			$helperTechnicalDs1[$cummulativeSemester] = array();
			$technicalDs1[$cummulativeSemester] = array();
			//Traverse all batches for this student, the last one can overwrite the previous
			foreach ($batchListArray1 as $batch1)	{
				$resultsGroup1 = null;
				try {
					$resultsGroupId = ResultsGroup::getResultsGroupFromCourseBatchYearAndSemester($this->database, $this->conn, $course1->getCourseId(), $batch1->getAccademicYearId(), $year, $semester);
					$resultsGroup1 = new ResultsGroup($this->database, $resultsGroupId, $this->conn);
				} catch (Exception $e)	{
					continue; 
				}
				if (is_null($resultsGroup1)) continue;
				if ($resultsGroup1->isResultsLocked()) continue; // We are dealing with Non Locked Results 
				$resultsList1 = ResultsGroup::getResultsBelongingToAGroup($this->database, $this->conn, $resultsGroup1->getGroupId(), false);
				if (is_null($resultsList1)) continue;
				foreach ($resultsList1 as $resultsId)	{
					$results1 = new Results($this->database, $resultsId, $this->conn);
					//You should deal with Only Locked Results 
					if (! $results1->isResultsLocked()) continue;
					$rawResultsFile1 = $results1->getRawMarksResultsFile($resultsFolder);
					if (is_null($rawResultsFile1)) continue;
					$gradedResultsFile1 = $results1->getGradedMarksResultsFile($resultsFolder);
					if (is_null($gradedResultsFile1)) continue;
					$ds1[$cummulativeSemester][$results1->getResultsId()] = array();
					$helperTechnicalDs1[$cummulativeSemester][$results1->getResultsId()] = array();
					$examinationId = $results1->getExamination()->getExaminationId();
					$technicalDs1[$cummulativeSemester][$examinationId] = array();
					//Load DataLine for this student, just use raw Marks, just for technical Info collection 
					$studentDataLine1 = $rawResultsFile1->getStudentResultsDataLine($student1->getStudentId());
					foreach ($subjectDataArray1 as $subjectData1)	{
						$transactionId = $subjectData1['transactionId'];
						if (! isset($technicalDs1[$cummulativeSemester][$examinationId][$transactionId]))	{
							$technicalDs1[$cummulativeSemester][$examinationId][$transactionId] = 0;
						}
						try {
							//Get Input Symbol 
							if (! is_null($studentDataLine1))	{
								$clearedDetailsDs1 = $studentDataLine1->getClearedDetails();
								if (! is_null($clearedDetailsDs1) && isset($clearedDetailsDs1[$transactionId]))	{
									if (($technicalDs1[$cummulativeSemester][$examinationId][$transactionId] == ResultsGroup::$__RESULTS_FAILED) && (intval($clearedDetailsDs1[$transactionId]) == ResultsGroup::$__RESULTS_FAILED))	{
										//Consecutive fails hold this student he/she has to clear without moving forward 
										$technicalDs1[$cummulativeSemester][$examinationId][$transactionId] = ResultsGroup::$__RESULTS_DISQUALIFIED;
									} else if ($technicalDs1[$cummulativeSemester][$examinationId][$transactionId] == ResultsGroup::$__RESULTS_CLEARED)	{
										//You were Already Cleared, Do Nothing Enjoy
									} else {
										$technicalDs1[$cummulativeSemester][$examinationId][$transactionId] = intval($clearedDetailsDs1[$transactionId]);
									}
									//Now store that in a real Array 
									$helperTechnicalDs1[$cummulativeSemester][$resultsId][$transactionId] = $technicalDs1[$cummulativeSemester][$examinationId][$transactionId];
								}
							}
							//End with transaction Loading
							$rawMarks = $rawResultsFile1->getMarksAt($student1->getStudentId(), $transactionId);
							$gradeId = $gradedResultsFile1->getMarksAt($student1->getStudentId(), $transactionId);
							//Now put data 
							$ds1[$cummulativeSemester][$results1->getResultsId()][$transactionId] = array();
							$ds1[$cummulativeSemester][$results1->getResultsId()][$transactionId]['rawMarks'] = $rawMarks;
							$ds1[$cummulativeSemester][$results1->getResultsId()][$transactionId]['gradeId'] = $gradeId; 
						} catch (Exception $e)	{
							continue;
						}
					}
				}
			}
		}
		//Step 7: Cleaning the Array 
		$tempDs1 = array();
		$tempTechnicalDs1 = array();
		foreach ($ds1 as $cummulativeSemester => $resultsArray1)	{
			$tempResultsArray1 = array();
			$tempTechnicalCummulativeSemester1 = array();
			foreach ($resultsArray1 as $resultsId => $transactionArray1)	{
				$tempTransactionArray1 = array();
				$tempTechnicalTransaction1 = array();
				foreach ($transactionArray1 as $transactionId => $transactionDataArray1)	{
					if ((trim($transactionDataArray1['rawMarks']) != "") && (trim($transactionDataArray1['gradeId']) != ""))	{
						$tempTransactionArray1[$transactionId] = array();
						$tempTransactionArray1[$transactionId]['rawMarks'] = $transactionDataArray1['rawMarks'];
						$tempTransactionArray1[$transactionId]['gradeId'] = $transactionDataArray1['gradeId'];
						//Only those whose grades are collected we can be sure, they grades 
						if (isset($helperTechnicalDs1[$cummulativeSemester]) && isset($helperTechnicalDs1[$cummulativeSemester][$resultsId]) && isset($helperTechnicalDs1[$cummulativeSemester][$resultsId][$transactionId]) && ($helperTechnicalDs1[$cummulativeSemester][$resultsId][$transactionId] != ResultsGroup::$__RESULTS_CLEARED))	{
							//Drop records which were not touched and those which are already cleared 
							$tempTechnicalTransaction1[$transactionId] = $helperTechnicalDs1[$cummulativeSemester][$resultsId][$transactionId];
						}
					}
				}
				if (sizeof($tempTransactionArray1) > 0)	{
					$tempResultsArray1[$resultsId] = $tempTransactionArray1;
				}
				if (sizeof($tempTechnicalTransaction1) > 0)	{
					$tempTechnicalCummulativeSemester1[$resultsId] = $tempTechnicalTransaction1;
				}
			}
			if (sizeof($tempResultsArray1) > 0)	{
				$tempDs1[$cummulativeSemester] = $tempResultsArray1;
			}
			if (sizeof($tempTechnicalCummulativeSemester1) > 0)	{
				$tempTechnicalDs1[$cummulativeSemester] = $tempTechnicalCummulativeSemester1;
			}
		}
		$ds1 = $tempDs1;
		//$technicalDs1 = $tempTechnicalDs1;
		$technicalDs1 = $tempTechnicalDs1;
		if (sizeof($technicalDs1) == 0) $technicalDs1 = null;
		$profile1->setExtraFilter($technicalDs1);
		if (sizeof($ds1) == 0) $ds1 = null;
		return $ds1;	
	}
	public function showMyCompiledResults($profile1, $resultsFolder, $displayList1, $controlFlags)	{
		//Display List is used to collect information back to the calling function 
		/*
		Since this function return string to be displayed an accordion ,
		an empty DisplayList object is passed and it is returned with data
		controlFlags ie showRawMarks etc
		*/
		$ds1 = $this->getMyCompiledResultsDataStructure($profile1, $resultsFolder);
		if (is_null($ds1)) return null;
		$subjectDs1 = $this->getMySubjectListDataStructure($profile1);
		if (is_null($subjectDs1)) return null;
		$gpaDs1 = $this->getMyGPADataStructure($subjectDs1, $ds1);
		if (is_null($gpaDs1)) return null;
		$overalGPADs1 = $this->getMyOveralGPADataStructure($gpaDs1);
		if (is_null($overalGPADs1)) return null;
		$student1 = $this;
		$accordion1 = "<div style=\"z-index: 0;\" class=\"ui-sys-accordion\">";
		for ($i = 1; $i <= sizeof($ds1); $i++)	{
			$yearAndSemesterArr = System::getYearAndSemesterFromACummulativeSemester($i, $profile1);
			if (is_null($yearAndSemesterArr)) continue;
			$year = $yearAndSemesterArr['year'];
			$semester = $yearAndSemesterArr['semester'];
			$titleText = System::scriptNumberAdjustment($year)." year, ".System::scriptNumberAdjustment($semester)." semester"."  (Semester Count is $i)";
			$accordion1 .= "<h3 style=\"z-index: 1;\">$titleText</h3><div style=\"z-index: 1;\"><table class=\"pure-table\"><thead><tr><th></th><th>Status</th><th>Module/Course Title</th><th>Units</th><th>Marks</th><th>Grade</th><th>Grade Point</th><th>Module Points</th></tr></thead><tbody>";
			$transactionBlockArr1 = $ds1[$i];
			for ($index = 0; $index < sizeof($transactionBlockArr1); $index++)	{
				$transactionArr1 = $transactionBlockArr1[$index];
				$transactionId = $transactionArr1['transactionId'];
				$gradeId = $transactionArr1['gradeId'];
				if (is_null($gradeId)) continue;
				$rawMarks = $transactionArr1['rawMarks'];
				$transaction1 = new CourseAndSubjectTransaction($this->database, $transactionId, $this->conn);
				$grade1 = new Grade($this->database, $gradeId, $this->conn);
				$subjectName = "Unknown";
				$subjectCredits = 0;
				$subject1 = $transaction1->getSubject();
				if (! is_null($subject1))	{
					$subjectName = $subject1->getSubjectName()."(".$subject1->getSubjectCode().")";
					$subjectUnits = $subject1->getTotalUnits();
				}
				$gradeName = $grade1->getGradeName();
				$gradePoint = $grade1->getGradePoint();
				$statusColor = "blue";
				if ($grade1->isIncomplete()) {
					$statusColor = "gold"; 
				} else if ($grade1->isFailed())	{
					$statusColor = "red";
				}
				$modulePoints = intval($gradePoint) * intval($subjectUnits);
				$count = $index + 1;
				$accordion1 .= "<tr><td>$count</td><td><span style=\"background-color: $statusColor;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td><td>$subjectName</td><td>$subjectUnits</td><td>$rawMarks</td><td>$gradeName</td><td>$gradePoint</td><td>$modulePoints</td></tr>";
			}
			$gpa = "##";
			$comment = "No Information";
			$accordion1 .= "</tbody><tfoot>";
			$footer = "<tr><td colspan=\"8\"></td></tr>";
			if (isset($gpaDs1[$i]))	{
				$footer = "";
				$gpa = $gpaDs1[$i]['gpa'];
				$awardId = $gpaDs1[$i]['awardId'];
				$comment = "FAILED";
				if ($gpaDs1[$i]['cleared']) $comment = "PASSED";
				$awardName = "";
				if (! is_null($awardId))	{
					$award1 = new ClassOfAward($this->database, $awardId, $this->conn);
					if ($award1->isFailed())	$comment = "DISCONTINUED";
					$awardName = "(".$award1->getAwardName().")";
				}
				$totalUnits = $gpaDs1[$i]['totalUnits'];
				$totalModulePoints = $gpaDs1[$i]['totalModulePoints'];
				$footer .= "<tr style=\"font-size: 0.9em; font-weight: bold; text-align: center;\"><td colspan=\"3\">TOTAL</td><td>$totalUnits</td><td colspan=\"3\"></td><td>$totalModulePoints</td></tr>";
				$footer .= "<tr style=\"font-size: 0.9em; font-weight: bold; text-align: center;\"><td colspan=\"8\">$comment , GPA = $gpa &nbsp;&nbsp; $awardName</td></tr>";
			}
			$accordion1 .= $footer."</tfoot></table></div>";
		}
		$accordion1 .= "</div><div style=\"margin-top: 2px; margin-bottom: 2px; border: 1px dotted black; padding: 1px; padding-left: 32px;\">";
		$gpa = $overalGPADs1['gpa'];
		$isCleared = $overalGPADs1['cleared'];
		$isDisqualified = $overalGPADs1['disqualified'];
		$awardId = $overalGPADs1['awardId'];
		$awardName = "OVERALL GPA is <b>$gpa</b>";;
		if (! is_null($awardId))	{
			$award1 = new ClassOfAward($this->database, $awardId, $this->conn);
			$awardName = $awardName."  (<b><i>".$award1->getAwardName()."</i></b>)";			
		}
		$commentText = "COMMENT : <b>You have Passed and You have cleared all the Subjects</b>";
		if ($isDisqualified) {
			$commentText = "COMMENT : <b style=\"background-color: red; color: white;\">You have beed Disqualified (DISCO)</b>";
		} else if (! $isCleared)	{
			$commentText = "COMMENT : <b style=\"\">You have Passed, however you have some fails in some of your subjects/modules</b>";
		}
		$accordion1 .= "$awardName <br/> $commentText</div>";
		return $accordion1;
	}
	public function getMyCompiledResultsDataStructure($profile1, $resultsFolder)	{
		/*
		Note for Non Graduated Students Only, a Graduated Student is offered a transcript only 
		for only ResultsGroup which are locked only 
		return 	list[cummulativeSemester][index]['transactionId'] = transactionId
				list[cummulativeSemester][index]['rawMarks'] = marks [ie 80]
				list[cummulativeSemester][index]['gradeId'] = grades [ie A+]
				list[cummulativeSemester][index][transactionId] = gradeId 
				or return null 
		*/
		$student1 = $this; 
		//if ($student1->isAlreadyGraduated()) return null; //We need Non Graduated Students --removed it is used for transcripts
		//Step 1: Get Subject List for this Student
		$subjectListDs1 = $this->getMySubjectListDataStructure($profile1);
		//Pick All semester even if it is current one what matters is the group is locked or not 
		if (is_null($subjectListDs1)) return null;
		//Step 2: Preparing data Structure 
		$ds1 = array();
		foreach ($subjectListDs1 as $cummulativeSemester => $subjectDataArray1)	{
			$ds1[$cummulativeSemester] = array();
			foreach ($subjectDataArray1 as $index => $transactionId)	{
				//Changed to match indices
				$ds1[$cummulativeSemester][$index] = array();
				$ds1[$cummulativeSemester][$index]['transactionId'] = $transactionId;
				$ds1[$cummulativeSemester][$index]['rawMarks'] = "";
				$ds1[$cummulativeSemester][$index]['gradeId'] = null;
				$ds1[$cummulativeSemester][$index][$transactionId] = null; //Used for GPA
			}
		}
		//Step 3: Get BatchList Array 
		$batchListArray1 = $student1->getListOfBatches();
		if (is_null($batchListArray1)) Object::shootException("Student does not belong to any Available Batches");
		//Step 4: Get Course 
		$course1 = $student1->getCourse();
		if (is_null($course1)) Object::shootException("There is not related course attached to this Student");
		//Step 5: Use My Data Structure to proceed 
		foreach ($ds1 as $cummulativeSemester => $subjectDataArray1)	{
			$yearAndSemesterArr = System::getYearAndSemesterFromACummulativeSemester($cummulativeSemester, $profile1);
			if (is_null($yearAndSemesterArr)) continue;
			$year = $yearAndSemesterArr['year'];
			$semester = $yearAndSemesterArr['semester'];
			//Traverse all batches for this student, the last one can overwrite the previous 
			foreach ($batchListArray1 as $batch1)	{
				$resultsGroup1 = null;
				try {
					$resultsGroupId = ResultsGroup::getResultsGroupFromCourseBatchYearAndSemester($this->database, $this->conn, $course1->getCourseId(), $batch1->getAccademicYearId(), $year, $semester);
					$resultsGroup1 = new ResultsGroup($this->database, $resultsGroupId, $this->conn);
				} catch (Exception $e)	{
					continue;
				}
				if (is_null($resultsGroup1)) continue; 
				if (! $resultsGroup1->isResultsLocked()) continue; //We are dealing with Only Locked Results 
				$rawResultsFile1 = $resultsGroup1->getRawMarksResultsFile($resultsFolder);
				if (is_null($rawResultsFile1)) continue;
				$rawResultsFile1 = $rawResultsFile1->getSemesterResultsFile($semester);
				if (is_null($rawResultsFile1)) continue;
				$gradedResultsFile1 = $resultsGroup1->getGradedMarksResultsFile($resultsFolder);
				if (is_null($gradedResultsFile1)) continue;
				$gradedResultsFile1 = $gradedResultsFile1->getSemesterResultsFile($semester);
				if (is_null($gradedResultsFile1)) continue;
				foreach ($subjectDataArray1 as $index => $subjectData1)	{
					$transactionId = $subjectData1['transactionId'];
					try {
						$rawMarks = $rawResultsFile1->getMarksAt($student1->getStudentId(), $transactionId);
						$gradeId = $gradedResultsFile1->getMarksAt($student1->getStudentId(), $transactionId);
						$ds1[$cummulativeSemester][$index]['rawMarks'] = $rawMarks;
						$ds1[$cummulativeSemester][$index]['gradeId'] = $gradeId;
						$ds1[$cummulativeSemester][$index][$transactionId] = $gradeId;
					} catch (Exception $e)	{
						continue;
					}
				}
			}
		} 
		//Step 6, you need to only records whose grade is not null 
		$newDs1 = array();
		foreach ($ds1 as $cummulativeSemester => $subjectDataArray1)	{
			$tempSubjectArray1 = array();
			foreach ($subjectDataArray1 as $subjectData1)	{
				if (! is_null($subjectData1['gradeId']))	{
					$tempSubjectArray1[sizeof($tempSubjectArray1)] = $subjectData1;
				}
			}
			if (sizeof($tempSubjectArray1) > 0)	{
				//There is something 
				$newDs1[$cummulativeSemester] = $tempSubjectArray1;
			}
		}
		$ds1 = $newDs1;
		if (sizeof($ds1) == 0) $ds1 = null;
		return $ds1;		
	}
	
	/*Changing Class ResultsGroup */
	public final static function getSupplimentaryResultsForThisGroup($database, $conn, $groupId)	{
		$query = "SELECT resultsId FROM results WHERE groupId='$groupId'";
		$results = mysql_db_query($database, $query, $conn) or Object::shootException("Could not fetch supplimentary Results for this a Group");
		$supplimentaryResults1 = null;
		while (list($id)=mysql_fetch_row($results))	{
			$results1 = new Results($database, $id, $conn);
			if (is_null($results1->getExamination())) Object::shootException("Reference Examination were not set");
			if ($results1->getExamination()->isSupplimentary())	{
				$supplimentaryResults1 = $results1;
				break;
			}
		}
		return $supplimentaryResults1;
	}
	public final static function groupResultsCompilation($database, $conn, $profile1, $systemTime1, $groupId, $folderPath, $constantValueIfPassedSupplimentary)	{
		$rawGroupResultsFile1 = null;
		$promise1 = new Promise();
		$group1 = new ResultsGroup($database, $groupId, $conn);
		$group1->setAfterSupplimentary("0");
		if ($group1->getExaminationGroup()->getSemester() == ExaminationGroup::$__ALL_SEMESTERS)	{
			for ($semester = 1; $semester <= $profile1->getNumberOfSemestersPerYear(); $semester++)	{
				$promise1 = self::evaluateGroupResults($database, $conn, $groupId, $folderPath, $semester, false);
				if (! $promise1->isPromising()) Object::shootException($promise1->getReason());
				$tempResultsFile1 = $promise1->getResults();
				//simply merge, there is no header collision 
				if (! is_null($rawGroupResultsFile1)) $tempResultsFile1 = $rawGroupResultsFile1->merge($tempResultsFile1, $systemTime1, false, true);
				$rawGroupResultsFile1 = $tempResultsFile1;
			}
		} else {
			//Specific Semester
			$semester = $group1->getExaminationGroup()->getSemester();
			$promise1 = self::evaluateGroupResults($database, $conn, $groupId, $folderPath, $semester, false);
			if (! $promise1->isPromising()) Object::shootException($promise1->getReason());
			$tempResultsFile1 = $promise1->getResults();
			//simply merge, there is no header collision 
			if (! is_null($rawGroupResultsFile1)) $tempResultsFile1 = $rawGroupResultsFile1->merge($tempResultsFile1, $systemTime1, false, true);
			$rawGroupResultsFile1 = $tempResultsFile1;
		}
		if (is_null($rawGroupResultsFile1)) Object::shootException("The compilation Algorithm returned an Empty Data");
		//We need now to check for Supplimentary Results --
		$supplimentaryResults1 = null;
		if ($group1->isAfterSupplimentary()) $supplimentaryResults1 = self::getSupplimentaryResultsForThisGroup($database, $conn, $groupId);
		if (! is_null($supplimentaryResults1))	{
			$filename = $folderPath.$supplimentaryResults1->getRawMarksCSVFile();
			$supplimentaryResultsFile1 = new ResultsFile($database, $filename, $conn);
			$rawGroupResultsFile1 = ResultsFile::combineGroupResultsFileWithSupplimentaryResultsFile($database, $conn, $rawGroupResultsFile1, $supplimentaryResultsFile1, $constantValueIfPassedSupplimentary);
			if (is_null($rawGroupResultsFile1)) Object::shootException("The compilation returned an Empty data after combining with supplimentary results");
			$group1->setAfterSupplimentary("1");
		}//end-of-supplimentary
		//Apply MRI Rules -- Special Rules ---kd788--special
		//now-proceed
		//Entering saving mode 
		$groupfilename = "resultsgroup_".$group1->getGroupId().".csv";
		if (! is_null($group1->getRawMarksCSVFile())) $groupfilename = $group1->getRawMarksCSVFile();
		$filename = $folderPath.$groupfilename;
		//RawDataSaving 
		$rawGroupResultsFile1->setFilename($filename);
		$rawGroupResultsFile1->round();
		$rawGroupResultsFile1->save();
		$group1->setRawMarksCSVFile($groupfilename);
		$checksum = md5_file($filename);
		$group1->setRawMarksCSVFileChecksum($checksum);
		//GradedData 
		$groupfilename = $groupfilename.".graded";
		$filename = $folderPath.$groupfilename;
		$rawGroupResultsFile1->grade();
		$rawGroupResultsFile1->setFilename($filename);
		$rawGroupResultsFile1->save();
		$group1->setGradedMarksCSVFile($groupfilename);
		$checksum = md5_file($filename);
		$group1->setGradedMarksCSVFileChecksum($checksum);
		//Commit Saving 
		$group1->commitUpdate();
		//Finalizing Algorithm
		$promise1->setPromise(true);
		return $promise1;
	}
	public final static function evaluateGroupResults($database, $conn, $groupId, $folderPath, $semester, $enableSaving)	{
		//folderPath = data/results/
		//semester false, 1,2 ..
		//constant Value false or value
		$promise1 = new Promise();
		$promise1->setPromise(true);
		$group1 = new ResultsGroup($database, $groupId, $conn);
		$resultsList1 = self::getResultsBelongingToAGroup($database, $conn, $groupId, $semester);
		if (is_null($resultsList1)) Object::shootException("There were not Results Found in a selected Results Group");
		$rawGroupResultsFile1 = null;
		/*
		All Primary Examination Results Should Exists , including their data 
		*/
		$primaryExaminationResults1 = array(); //Index is resultsId 
		$mergedHeaderLine1 = null;
		foreach ($resultsList1 as $resultsId)	{
			$results1 = new Results($database, $resultsId, $conn);
			if ($results1->getExamination()->isPrimary())	{
				if (is_null($results1->getRawMarksCSVFile())) Object::shootException("One of the results is not yet set");
				$filename = $folderPath.$results1->getRawMarksCSVFile();
 				if (! file_exists($filename)) Object::shootException("One of the results is not available");
				$resultsFile1 = new ResultsFile($database, $filename, $conn);
				$primaryExaminationResults1[$results1->getResultsId()] = $resultsFile1;
				$mergedHeaderLine1 = ResultsHeaderLine::mergeHeader($mergedHeaderLine1, $resultsFile1->getHeaderLine());
			}
		}
		if (is_null($mergedHeaderLine1)) Object::shootException("The system could not merge headers, or Individual Results are missing headers");
		if (sizeof($primaryExaminationResults1) == 0) Object::shootException("The system could not get any Individual Results Data");
		foreach ($primaryExaminationResults1 as $resultsId => $resultsFile1)	{
			$results1 = new Results($database, $resultsId, $conn);
			/*
			SIMPLE: Just put your results above percentage weight in a semester kwisha acha story mingi
			$relativeMaximumScore = 0;
			$olderMaximumScore = $resultsFile1->getMaximumScore();
			if ($olderMaximumScore == 0) Object::shootException("The Denominator can not be zero");
			$percentageWeightOfResults = 0;
			*/
			$percentageWeightOfResults = Examination::percentageWeightInAGroupInASemester($database, $conn, $results1->getExamination());
			//$relativeMaximumScore = round((($percentageWeightOfResults * 100)/ $olderMaximumScore),2);
			$resultsFile1->updateScoresBasedOnMaximumScore($percentageWeightOfResults, $resultsFile1->getCompilationTime());
			//Now resultsFile1 has a weighted Results , merging should just involve adding those Res
			$rawGroupResultsFile1 = ResultsFile::mergeWeighted($database, $conn, $rawGroupResultsFile1, $resultsFile1, $mergedHeaderLine1, $resultsFile1->getCompilationTime(), $semester);
		} //end--foreach (resultsId, ResultsFile1)
		if (is_null($rawGroupResultsFile1)) Object::shootException("Group Results File could not be combined");
		$rawGroupResultsFile1->sort(); //We need to preserve our sorting 
		//Now saving
		if ($enableSaving)	{
			$trawGroupResultsFile1 = $rawGroupResultsFile1->cloneMe();
			$groupfilename = "resultsgroup_".$group1->getGroupId().".csv";
			if (! is_null($group1->getRawMarksCSVFile())) $groupfilename = $group1->getRawMarksCSVFile();
			$filename = $folderPath.$groupfilename.".semester.".$semester;
			$trawGroupResultsFile1->setFilename($filename);
			$trawGroupResultsFile1->save();
			$filename = $filename.".graded";
			$trawGroupResultsFile1->grade();
			$trawGroupResultsFile1->setFilename($filename);
			$trawGroupResultsFile1->save();
		} 
		//Finalizing Business 
		$promise1->setPromise(true);
		$promise1->setResults($rawGroupResultsFile1);
		return $promise1;
	}

	//RESULTS FILE NEED TO A DEEPER ANALYSIS
class ResultsFile {
	private $headers;
	private $headerLine;
	private $dataLines;
	private $filename;
	private $controlIndexArray;
	private $database;
	private $conn;
	private $dataHolder;
	static $__IS_CLONE_MODE = "__is_clone_mode";
	public final static function drawMarksCard($database, $conn, $profile1, $paper1, $resultsFolder, $results1, $resultsName, $transactionList, $controlFlags)	{
		/*
		Input
			database , conn 
			paper1 : Paper Object {paperName, width, height}
			profile1 : System Profile 
			results1 : we will use its getRawMarksCSVFile , getRawMarksCSVFileChecksum, getGradedMarksCSVFile and getGradedMarksCSVFileChecksum
			resultName : Name of Examination, can be briefcase name, examinationGroup name etc 
			controlFlags : For future enhancement
		*/
		//Step 1: Check if files are set
		if (is_null($results1->getRawMarksCSVFile())) Object::shootException("The Raw Marks Data Are Empty");
		if (is_null($results1->getGradedMarksCSVFile())) Object::shootException("The Graded Marks Data Are Empty");
		//Step 2: Check if file exists 
		$rawMarksFile = $resultsFolder.$results1->getRawMarksCSVFile();
		if (! file_exists($rawMarksFile)) Object::shootException("The Raw Marks were not found");
		$gradedMarksFile = $resultsFolder.$results1->getGradedMarksCSVFile();
		if (! file_exists($gradedMarksFile)) Object::shootException("The Graded Marks were not found");
		//Step 3: Checking Data Integrity 
		if (md5_file($rawMarksFile) != $results1->getRawMarksCSVFileChecksum()) Object::shootException("The Data File Could not pass Integrity check");
		if (md5_file($gradedMarksFile) != $results1->getGradedMarksCSVFileChecksum()) Object::shootException("The Graded File Could not pass Integrity check");
		//Step 4 : Start writing window 
		$width = $paper1->getWidth();
		$height = $paper1->getHeight();
		if ($paper1->isOrientationFlipped())	{
			$width = $paper1->getHeight();
			$height = $paper1->getWidth();
		}
		$window1 = "<div syyle=\"width: $width; height: $height; position: relative;\">";
		$profileName = $profile1->getProfileName();
		$window1 .= "<div><div style=\"font-size: 1.2em; font-weight: bold;\">$profileName</div></div>";
		$window1 .="</div>";
		return $window1;
	}
	public function debug()	{
		if (! is_null($this->dataLines))	{
			foreach ($this->dataLines as $dataLine1)	{
				echo "<br/>";
				$dataLine1->debug();
			}
		} else {
			echo "<br/>No Data Nulled";
		}
	}
	public function setData($key, $value)	{
		$this->dataHolder[$key] = $value;
	}
	public function getData($key)	{
		$val = null;
		if (isset($this->dataHolder[$key])) $val = $this->dataHolder[$key];
		return $val;
	}
	public function getInstruction()	{ return $this->headers['instruction']; }
	public function getColumnCount()	{ return $this->headers['columnCount']; }
	public function getMaximumScore()	{ return $this->headers['maximumScore']; }
	public function setMaximumScore($maximumScore)	{ $this->headers['maximumScore'] = $maximumScore; }
	public function getFileType()	{ return $this->headers['fileType']; }
	public function getFileTypeCode()	{ return $this->headers['fileTypeCode']; }
	public function getCourse()	{
		$course1 = null;
		try {
			$course1 = new Course($this->database, trim($this->headers['courseId']), $this->conn);
		} catch (Exception $e)	{
			$course1 = null;
		}
		return $course1;
	}
	public function getYear()	{ return $this->headers['year']; }
	public function getSemester()	{ return $this->headers['semester']; }
	public function getBatch()	{
		$batch1 = null;
		try {
			$batch1 = new AccademicYear($this->database, trim($this->headers['batchId']), $this->conn);
		} catch (Exception $e)	{ $batch1 = null; }
		return $batch1;
	}
	public function getCompilationTime()	{
		$time1 = null;
		try {
			$time1 = new DateAndTime("zoomtong", trim($this->headers['compilationTime']), "zk4001"); 
		} catch (Exception $e)	{
			$time1 = null;
		}
		return $time1;
	}
	public function setCompilationTime($dtString)	{
		$this->headers['compilationTime'] = $dtString;
	}
	public function getHeaders()	{ return $this->headers; }
	public function setHeaders($headers)	{ $this->headers = $headers; }
	public function getHeaderLine()	{ return $this->headerLine; }
	public function setHeaderLine($headerLine)	{ $this->headerLine = $headerLine; }
	public function getDataLines()	{ return $this->dataLines; }
	public function setDataLines($dataLines)	{ $this->dataLines = $dataLines; }
	public function setFilename($filename)	{ $this->filename = $filename; }
	public function getFilename()	{ return $this->filename; }
	public function getControlIndexArray()	{ return $this->controlIndexArray; }
	public function setDatabase($database)	{ $this->database = $database; }
	public function setConnectionString($conn)	{ $this->conn = $conn; }
	public function assignData($database, $conn, $headers, $headerLine, $dataLines, $filename, $controlIndexArray)	{
		$this->database = $database;
		$this->conn = $conn;
		$this->headers = $headers;
		$this->headerLine = $headerLine;
		$this->dataLines = $dataLines;
		$this->filename = $filename;
		$this->controlIndexArray = $controlIndexArray;
	}
	public function cloneMe()	{
		$data1 = new ResultsFile($this->database, ResultsFile::$__IS_CLONE_MODE, $this->conn);
		$headers = array();
		foreach ($this->headers as $key => $val)	{
			$headers[$key] = $val;
		}
		$headerLine = $this->headerLine->cloneMe();
		$dataLines = array();
		foreach ($this->dataLines as $key => $dataLine1)	{
			$dataLines[$key] = $dataLine1->cloneMe();
		}
		$controlIndexArray = array();
		foreach ($this->controlIndexArray as $key => $val)	{
			$controlIndexArray[$key] = $val;
		}
		$data1->assignData($this->database, $this->conn, $headers, $headerLine, $dataLines, $this->filename, $controlIndexArray);
		return $data1;
	}
	public function isRawDataValid()	{
		$maximumScore = $this->getMaximumScore();
		if (($maximumScore == "") || (intval($maximumScore) == 0)) return false;
		$isValid = true;
		foreach ($this->dataLines as $dataLine1)	{
			$marksArray1 = $dataLine1->getMarksArray();
			if (is_null($marksArray1)) Object::shootException("Perhaps Inconsistent format");
			foreach ($marksArray1 as $marks)	{
				$isValid = $isValid && (($marks == "") || (floatval($marks) <= floatval($maximumScore)));
				if (! $isValid) break;
			}
		}
		return $isValid;
	}
	public function setMarksAt($studentId, $transactionId, $marks)	{
		if (! isset($this->controlIndexArray[$studentId])) Object::shootException("Student is not in the source File");
		$dataLine1 = $this->dataLines[$this->controlIndexArray[$studentId]];
		$dataLine1->setMarksForTransaction($transactionId, $marks);
	}
	public function getMarksAt($studentId, $transactionId)	{
		if (! isset($this->controlIndexArray[$studentId])) Object::shootException("Student is not in the source File");
		$dataLine1 = $this->dataLines[$this->controlIndexArray[$studentId]];
		return $dataLine1->getMarksForTransaction($transactionId);
	}
	public function getStudentAndTransactionResultsFile($studentId, $transactionId)	{
		$data1 = $this->getStudentResultsFile($studentId);
		if (is_null($data1)) Object::shootException("Student is not found in the source Object");
		$data1 = $data1->getTransactionResultsFile($transactionId);
		if (is_null($data1)) Object::shootException("Subject is not found in the source Object");
		return $data1;
	}
	public function getStudentResultsDataLine($studentId)	{
		if (! isset($this->controlIndexArray[$studentId])) return null;
		return ($this->dataLines[$this->controlIndexArray[$studentId]]);
	}
	public function getStudentResultsFile($studentId)	{
		$data1 = new ResultsFile($this->database, ResultsFile::$__IS_CLONE_MODE, $this->conn);
		if (! isset($this->controlIndexArray[$studentId])) return null; //Empty data 
		$dataLines = array();
		$dataLines[0] = $this->dataLines[$this->controlIndexArray[$studentId]];
		$controlIndexArray[$studentId] = 0; //By default now zero 
		$data1->assignData($this->database, $this->conn, $this->headers, $this->headerLine, $dataLines, $this->filename, $controlIndexArray);
		return $data1;
	}
	public function getTransactionResultsFile($transactionId)	{
		$data1 = new ResultsFile($this->database, ResultsFile::$__IS_CLONE_MODE, $this->conn);
		$headerLine1 = $this->headerLine->getTransactionHeader($transactionId);
		if (is_null($headerLine1)) return null;
		//Working With Headers 
		$this->headers["columnCount"] = $headerLine1->getNumberOfColumns();
		//Working With data Now 
		$dataLines = array();
		$controlIndexArray = array();
		foreach ($this->dataLines as $dataLine1)	{
			$initialString = $dataLine1->getInitialString();
			$marks = $dataLine1->getMarksForTransaction($transactionId);
			$line = $initialString.",".$marks;
			$newDataLine1 = new ResultsDataLine($line, $headerLine1);
			$datasize = sizeof($dataLines);
			$dataLines[$datasize] = $newDataLine1;
			$controlIndexArray[$newDataLine1->getStudentId()] = $datasize; 
		}
		$data1->assignData($this->database, $this->conn, $this->headers, $headerLine1, $dataLines, $this->filename, $controlIndexArray);
		return $data1;
	}
	public function getCustomTransactionResultsFile($transactionList)	{
		$data1 = new ResultsFile($this->database, ResultsFile::$__IS_CLONE_MODE, $this->conn);
		$headerLine1 = $this->headerLine->getCustomTransactionHeader($transactionList);
		if (is_null($headerLine1)) return null;
		$headerArray1 = $headerLine1->getHeaderArray();
		//Working With Headers 
		$this->headers["columnCount"] = $headerLine1->getNumberOfColumns();
		//Working With data Now 
		$dataLines = array();
		$controlIndexArray = array();
		foreach ($this->dataLines as $dataLine1)	{
			$initialString = $dataLine1->getInitialString();
			$line = $initialString;
			foreach ($headerArray1 as $transactionId)	{
				$marks = $dataLine1->getMarksForTransaction($transactionId);
				$line = $line.",".$marks;
			}			
			$newDataLine1 = new ResultsDataLine($line, $headerLine1);
			$datasize = sizeof($dataLines);
			$dataLines[$datasize] = $newDataLine1;
			$controlIndexArray[$newDataLine1->getStudentId()] = $datasize; 
		}
		$data1->assignData($this->database, $this->conn, $this->headers, $headerLine1, $dataLines, $this->filename, $controlIndexArray);
		return $data1;
	}
	public function getAccademicResultsFileForResults($resultsId)	{
		$resultsFile1 = $this;
		//Make sure the supplied Results File is rawMarks
		$data1 = new ResultsFile("Ndimangwa", ResultsFile::$__IS_CLONE_MODE, "Fadhili");
		$dataLines = array();
		$controlIndexArray = array();
		$headerLine1 = $resultsFile1->getHeaderLine();
		if (is_null($headerLine1)) Onject::shootException("Empty Header were found");
		$headerArray1 = $headerLine1->getHeaderArray(); 
		$maximumScore = $resultsFile1->getMaximumScore();
		$results1 = new Results($this->database, $resultsId, $this->conn);
		$examination1 = $results1->getExamination();
		if (is_null($examination1)) Object::shootException("Reference Examination were not found");
		if (intval($maximumScore) == 0) Object::shootException("Maximum Score Can not be Zero");
		$course1 = $resultsFile1->getCourse();
		if (is_null($course1)) Object::shootException("Could not find Course Reference");
		$gradePolicyArray1 = Grade::getGradePolicyForEducationLevel($this->database, $this->conn, $course1->getEducationLevel()->getLevelId());
		foreach ($this->dataLines as $dataLine1)	{
			$gradedDataLine1 = $dataLine1->cloneMe();
			$gradedDataLine1 = $gradedDataLine1->grade($gradePolicyArray1, $maximumScore);
			$marksArray1 = $dataLine1->getMarksArray();
			$gradeArray1 = $gradedDataLine1->getMarksArray();
			foreach ($headerArray1 as $transactionId)	{
				$transaction1 = new CourseAndSubjectTransaction($this->database, $transactionId, $this->conn);
				//Using Raw Data to get Cleared Status -- Called Technical Sup
				$percentage = 0;
				if (trim($marksArray1[$transactionId]) != 0)	{
					$percentage = ($marksArray1[$transactionId] * 100) / $maximumScore;
				}
				$gradeId = $gradeArray1[$transactionId];
				$grade1 = new Grade($this->database, $gradeId, $this->conn);
				if (! $dataLine1->isDisqualified() && ! $dataLine1->isFailed() && ($percentage >= $examination1->getMinimumScorePercentage()))	{
					$dataLine1->setCleared("1");
				} else if (! $dataLine1->isDisqualified()) {
					if ($transaction1->isCoreModule())	{
						$dataLine1->setDisqualified("1");
					} else {
						//Non -- Core Module
						$dataLine1->setFailed("1");
					}
				}
				//Specific Technical Details 
				if ($percentage < $examination1->getMinimumScorePercentage())	{
					if ($transaction1->isCoreModule())	{
						$dataLine1->addClearedDetails($transactionId, ResultsGroup::$__RESULTS_DISQUALIFIED);
					} else {
						$dataLine1->addClearedDetails($transactionId, ResultsGroup::$__RESULTS_FAILED);
					}
				}
				//Using Grading Policy to get Cleared Status -- Normal Sup
				if (! $dataLine1->isDisqualified() && $grade1->isIncomplete())	{
					$dataLine1->setDisqualified("1");
				} else if (! $dataLine1->isDisqualified() && $grade1->isFailed())	{
					if ($transaction1->isCoreModule())	{
						$dataLine1->setDisqualified("1");
					} else {
						//Non -Core Module
						$dataLine1->setFailed("1");
					}
				}
			}
			$dataLine1 = $dataLine1->synchronize();
			$datasize = sizeof($dataLines);
			$dataLines[$datasize] = $dataLine1;
			$controlIndexArray[$dataLine1->getStudentId()] = $datasize; 
		}
		$data1->assignData($this->database, $this->conn, $this->headers, $this->headerLine, $dataLines, $this->filename, $controlIndexArray);
		return $data1;
	}
	public function getSemesterResultsFile($semester)	{
		$transactionList = array();
		$headerLine1 = $this->headerLine;
		$headerArray1 = $headerLine1->getHeaderArray();
		if (is_null($headerArray1)) Object::shootException("Course/Subject Transaction List were not found");
		$this->headers["semester"] = $semester;
		foreach ($headerArray1 as $transactionId)	{
			$transaction1 = new CourseAndSubjectTransaction($this->database, $transactionId, $this->conn);
			if (intval($transaction1->getSemester()) == intval($semester))	{
				$transactionList[sizeof($transactionList)] = $transaction1->getTransactionId();
			}
		}
		return $this->getCustomTransactionResultsFile($transactionList);
	}
	public function resequenceDataLines()	{
		$dataLines = array();
		$controlIndexArray = array();
		foreach ($this->dataLines as $dataLine1)	{
			$datasize = sizeof($dataLines);
			$dataLines[$datasize] = $dataLine1;
			$controlIndexArray[$dataLine1->getStudentId()] = $datasize;
		}
		$this->dataLines = $dataLines;
		$this->controlIndexArray = $controlIndexArray;
	}
	public final static function buildResultsFileFromCollection($database, $conn, $studentCollection1, $headerLine1, $controlFlags)	{
		$resultsFile1 = new ResultsFile($database, ResultsFile::$__IS_CLONE_MODE, $conn);
		$resultsFile1->setHeaderLine($headerLine1);
		$studentCollection1 = $studentCollection1->makeUnique();
		if (is_null($studentCollection1->getCollection())) Object::shootException("The Results Collection returned an empty data");
		foreach ($studentCollection1->getCollection() as $studentId)	{
			$student1 = new Student($database, $studentId, $conn);
			$dataLine1 = new ResultsDataLine(ResultsDataLine::$__IS_CLONE_MODE, $headerLine1);
			//Add Content to dataLine 
			$dataLine1->setStudentId($studentId);
			$dataLine1->setRegistrationNumber($student1->getRegistrationNumber());
			$dataLine1->setRepeating("0");
			$resultsFile1->addAndBuildDataLine($dataLine1);
		}
		$resultsFile1->setDatabase($database);
		$resultsFile1->setConnectionString($conn);
		return $resultsFile1; 
	}
	public function addAndBuildDataLine($dataLine1)	{
		if (is_null($dataLine1)) return;
		$dataLine1->setResultsHeader($this->headerLine);
		$dataLine1 = $dataLine1->build();
		$datasize = sizeof($this->dataLines);
		$this->dataLines[$datasize] = $dataLine1;
		$this->controlIndexArray[$dataLine1->getStudentId()] = $datasize; 
	}
	public function getListOfStudents()	{
		$list = array();
		if (! is_null($this->dataLines))	{
			foreach ($this->dataLines as $dataLine1)	{
				$list[sizeof($list)] = $dataLine1->getStudentId();
			}
		}
		if (is_null($list)) return null;
		return $list;
	}
	public function __construct($database, $filename, $conn)	{
		$this->headers = array();
		if ($filename == ResultsFile::$__IS_CLONE_MODE) return;
		if (! file_exists($filename)) Object::shootException("File does not exists");
		$this->dataHolder = array(); //Hold any data 
		$this->filename = $filename;
		$this->database = $database;
		$this->conn = $conn;
		if (is_null($filename)) Object::shootException("The filename is not yet set");
		$file1 = fopen($filename, "r") or Object::shootException("Could not create file reference");
		$lineCount = 0;
		$this->dataLines = array();
		$this->controlIndexArray = array(); //index is studentId value is dataLine index 
		while (($line = fgets($file1)) !== false)	{
			$lineCount++;
			if (trim($line) == "") continue;
			$lineArr = explode(",", $line);
			if (sizeof($lineArr) < 2) continue;
			/*  
			Dealing with line 1 .. 12 
			*/
			if (($lineCount >= 1) && ($lineCount <= 12))	{
				$headerBlock = $lineArr[1]; // ie instruction: dsdads 
				$keyValueArr = explode("_!_", $headerBlock);
				if (sizeof($keyValueArr) != 2) continue;
				$key = trim($keyValueArr[0]);
				$value = trim($keyValueArr[1]);
				$this->headers[$key] = $value;
			} else if (($lineCount >= 13) && ($lineCount <= 14))	{
				continue; //Reserved Lines
			} else if ($lineCount == 15)	{
				$this->headerLine = new ResultsHeaderLine($line);
			} else if ($lineCount == 16)	{
				continue; //Caption for user
			} else if ($lineCount > 16)	{
				//Data Handling
				$dataLine1 = new ResultsDataLine($line, $this->headerLine);
				$datasize = sizeof($this->dataLines);
				$this->dataLines[$datasize] = $dataLine1;
				$this->controlIndexArray[$dataLine1->getStudentId()] = $datasize; 
			}
		} //end-while
		fclose($file1);
	}
	public function grade()	{
		$course1 = $this->getCourse();
		if (is_null($course1)) Object::shootException("Grading, Could not find course for this Results File");
		$gradePolicyArray1 = Grade::getGradePolicyForEducationLevel($this->database, $this->conn, $course1->getEducationLevel()->getLevelId());
		for ($i =0; $i < sizeof($this->dataLines); $i++)	{
			$this->dataLines[$i] = $this->dataLines[$i]->grade($gradePolicyArray1, $this->getMaximumScore()); 
		}
		return $this;
	}
	public function save()	{
		$promise1 = new Promise();
		$promise1->setPromise(true);
		if (file_exists($this->filename))	$promise1 = FileFactory::createBackupFile($this->filename);
		if (! $promise1->isPromising()) return $promise1;
		//If file not exists create
		$file1 = fopen($this->filename, "w") or Object::shootException("Could not create file for writing");
		$currentLineNumber = 0;
		$numberOfColumns = intval($this->headers["columnCount"]);
		foreach ($this->headers as $key => $val)	{
			$currentLineNumber++;
			$line = "@-header,$key"."_!_ $val";
			for ($i = 2; $i < $numberOfColumns; $i++)	{
				$line .= ",";
			}
			$line .= "\n";
			fwrite($file1, $line) or Object::shootException("Could not write to Line $currentLineNumber");
		}
		$currentLineNumber++;
		if ($currentLineNumber == 13)	{
			$line = "";
			for ($i = 0; $i < $numberOfColumns - 1; $i++)	{
				$line .= ",";
			}
			$line .= "\n";
			fwrite($file1, $line) or Object::shootException("Could not write to Line $currentLineNumber");
			$currentLineNumber++;
		}
		if ($currentLineNumber == 14)	{
			$line = "";
			for ($i = 0; $i < $numberOfColumns - 1; $i++)	{
				$line .= ",";
			}
			$line .= "\n";
			fwrite($file1, $line) or Object::shootException("Could not write to Line $currentLineNumber");
			$currentLineNumber++;
		}
		if ($currentLineNumber == 15)	{
			$line = "";
			for ($i=1; $i < Object::$numberOfResultsFileLeadingColumn; $i++)	{
				$line .= ",";
			}
			$headerLine1 = $this->headerLine->getHeaderArray();
			for ($i=0; $i < sizeof($headerLine1); $i++)	{
				$line .= ",".$headerLine1[$i];
			}
			$line = str_replace("\n","", str_replace("\r", "", $line));
			$line .= "\n";
			fwrite($file1, $line) or Object::shootException("Could not write to Line $currentLineNumber");
			$currentLineNumber++;
		}
		if ($currentLineNumber == 16)	{
			$line = "Ref,Repeat,Reg Number,res,res,res";
			$headerLine1 = $this->headerLine->getHeaderArray();
			for ($i=0; $i < sizeof($headerLine1); $i++)	{
				$transaction1 = new CourseAndSubjectTransaction($this->database, $headerLine1[$i], $this->conn);
				$line .= ",".str_replace(",", " ", $transaction1->getSubject()->getSubjectName());
			}
			$line = str_replace("\n","", str_replace("\r", "", $line));
			$line .= "\n";
			fwrite($file1, $line) or Object::shootException("Could not write to Line $currentLineNumber");
			$currentLineNumber++;
		}
		//Now writing data 
		for ($i=0; ($i < sizeof($this->dataLines)) && ($currentLineNumber >= 17); $i++, $currentLineNumber++)	{
			$dataLine1 = $this->dataLines[$i];
			$dataLine1 = $dataLine1->synchronize();
			$line = $dataLine1->getLine();
			$line = str_replace("\n","", str_replace("\r", "", $line));
			$line .= "\n";
			fwrite($file1, $line) or Object::shootException("Could not write to Line $currentLineNumber");
		}
		fclose($file1);
	}
	public function updateScoresBasedOnMaximumScore($newMaximumScore, $systemTime1)	{
		$oldMaximumScore = floatval($this->headers['maximumScore']);
		$newMaximumScore = floatval($newMaximumScore);
		$datasize = sizeof($this->dataLines);
		for ($i=0; $i < $datasize; $i++)	{
			$this->dataLines[$i] = ResultsDataLine::getResultsLineBasedOnNewMaximumScore($this->dataLines[$i], $oldMaximumScore, $newMaximumScore);
		}
		//We need to update to this one
		$this->headers['maximumScore'] = $newMaximumScore;
		$this->headers['compilationTime'] = $systemTime1->getDateAndTimeString();
		return $this;
	}
	public function sort()	{
		//We need to sort the dataLines Obj 
		$datasize = sizeof($this->dataLines);
		for ($i=0; $i < ($datasize - 1); $i++)	{
			$swapped = false;
			for ($j=0; $j < ($datasize - $i - 1); $j++)	{
				if (intval($this->dataLines[$j]->getStudentId()) > intval($this->dataLines[$j+1]->getStudentId()))	{
					$temp = $this->dataLines[$j];
					$this->dataLines[$j] = $this->dataLines[$j+1];
					$this->dataLines[$j+1] = $temp;
					$swapped = true;
				}
			}
			if (! $swapped)	{
				//No need to continue already sorted 
				break;
			}
		}
		return $this;
	}
	public function round()	{
		$datasize = sizeof($this->dataLines);
		for ($i=0; $i < $datasize; $i++)	{
			$this->dataLines[$i]->round();
		}
	}
	public final static function combineGroupResultsFileWithSupplimentaryResultsFile($database, $conn, $rawGroupResultsFile1, $supplimentaryResultsFile1, $constantValueIfPassedSupplimentary)	{
		/* constantValueIfPassedSupplimentary false or a non Non zero value */
		$gradedGroupResultsFile1 = $rawGroupResultsFile1->cloneMe(); //Make a copy first 
		$gradedGroupResultsFile1->grade();
		//We will update the rawGroupResultsFile1 based upon gradedGroupResultsFile1 
		//We need to make sure the supplimentary Examination is also graded to 100  which is default 
		if ($supplimentaryResultsFile1->getMaximumScore() != Object::$__DEFAULT_MAXIMUM_SCORE)	{
			$supplimentaryResultsFile1->updateScoresBasedOnMaximumScore(Object::$__DEFAULT_MAXIMUM_SCORE, $supplimentaryResultsFile1->getCompilationTime());
		}
		$gradedDataLineArray1 = $gradedGroupResultsFile1->getDataLines();
		$gradedSupplimentaryResultsFile1 = null;
		if ($constantValueIfPassedSupplimentary)	{
			$gradedSupplimentaryResultsFile1 = $supplimentaryResultsFile1->cloneMe();
			$gradedSupplimentaryResultsFile1->grade();
		}
		foreach ($gradedDataLineArray1 as $gradedDataLine1)	{
			$studentId = $gradedDataLine1->getStudentId();
			$gradedMarksArray1 = $gradedDataLine1->getMarksArray();
			foreach ($gradedMarksArray1 as $transactionId => $gradeId)	{
				$grade1 = new Grade($database, $gradeId, $conn);
				if (! $grade1->isIncomplete() && $grade1->isFailed())	{
					/*
					Only Grades which are failed and which student attempted exam, incompletes and passed marks should not be supplimented 
					*/
					try {
						//Supplimentary
						$supplimentaryMarks = $supplimentaryResultsFile1->getMarksAt($studentId, $transactionId);
						if ($constantValueIfPassedSupplimentary)	{
							$supplimentaryGradeId = $gradedSupplimentaryResultsFile1->getMarksAt($studentId, $transactionId);
							$supplimentaryGrade1 = new Grade($database, $supplimentaryGradeId, $conn);
							if (! $supplimentaryGrade1->isIncomplete() && $supplimentaryGrade1->isFailed())	{
								$rawGroupResultsFile1->setMarksAt($studentId, $transactionId, $supplimentaryMarks);
							} else if ($supplimentaryGrade1->isIncomplete())	{
								//Do Nothing 
							} else {
								//Passed 
								$rawGroupResultsFile1->setMarksAt($studentId, $transactionId, $constantValueIfPassedSupplimentary);
							}
						} else {
							$rawGroupResultsFile1->setMarksAt($studentId, $transactionId, $supplimentaryMarks);
						}
						//Supplimentary has no Incomplete is just Failed 
						
					} catch (Exception $e)	{
						continue;
					}
				}
			}
		}
		return $rawGroupResultsFile1;
	}
	public final static function mergeWeighted($database, $conn, $resultsFile1, $resultsFile2, $headerLine1, $systemTime1, $semester)	{
		if (is_null($resultsFile1) && is_null($resultsFile2)) Object::shootException("Individual Results Files are Empty");
		$mergedResultsFile1 = new ResultsFile($database, ResultsFile::$__IS_CLONE_MODE, $conn);
		/*
		Student should be present in all examination, if missing any mark for removal (will remove the student)
		if column [transactionId ] is mission, fill it empty
		*/
		//Writing headers 
		$headers1 = null; $headers2 = null;
		$dataLineArr1 = null;
		$dataLineArr2 = null;
		$combinedControlIndexArray1 = null;
		$combinedDataLineArray1 = null;
		if (! is_null($resultsFile1)) {
			$headers1 = $resultsFile1->getHeaders();
			$resultsFile1->resequenceDataLines(); //Make sure the dataLines begin at Index 0
			$dataLineArr1 = $resultsFile1->getDataLines();
			$combinedControlIndexArray1 = $resultsFile1->getControlIndexArray();
			$combinedDataLineArray1 = $resultsFile1->getDataLines();
		}
		if (! is_null($resultsFile2)) {
			$headers2 = $resultsFile2->getHeaders();
			$resultsFile2->resequenceDataLines(); //Make sure the dataLines begin at Index 0
			$dataLineArr2 = $resultsFile2->getDataLines();
			$combinedControlIndexArray1 = $resultsFile2->getControlIndexArray();
			$combinedDataLineArray1 = $resultsFile2->getDataLines();
		}
		$combinedHeaders = array();
		if (! is_null($headers1) && ! is_null($headers2))	{
			$maximumScore = 0;
			foreach ($headers1 as $key => $val)	{
				if ($key == "compilationTime") continue;
				if ($key == "columnCount") continue;
				if ($key == "maximumScore") {
					$combinedHeaders[$key] = floatval($headers1[$key]) + floatval($headers2[$key]);
					//.i.think.$combinedHeaders[$key] = $val;
					continue;
				}
				if (! $semester && ($key == "semester")) {
					$combinedHeaders[$key] = "0";
					continue; //To Avoid Mismatch
				}
				if ($headers1[$key] != $headers2[$key]) Object::shootException("Headers Mismatch has occurred in $key"); 
				$combinedHeaders[$key] = $headers1[$key];
			}
		} else if (! is_null($headers1))	{
			$combinedHeaders = $headers1;
		} else if (! is_null($headers2))	{
			$combinedHeaders = $headers2;
		}
		$combinedHeaders["compilationTime"] = $systemTime1->getDateAndTimeString();
		$combinedHeaders["columnCount"] = $headerLine1->getNumberOfColumns();
		//Constructing headerLine 
		/*
		There is no need to think about combinedHeaderLine, the headerLine1 in the argument is the merged HeaderLine
		*/
		/*Combined Data Line */
		if (! is_null($resultsFile1) && ! is_null($resultsFile2))	{
			$combinedControlIndexArray1 = array(); //Now is merged 
			$combinedDataLineArray1 = array();
			for ($i=0, $j=0; ($i < sizeof($dataLineArr1) && $j < sizeof($dataLineArr2)); )	{
				$dataLine1 = $dataLineArr1[$i];
				$dataLine2 = $dataLineArr2[$j];
				//Add only records which matches both ways 
				if ($dataLine1->getStudentId() == $dataLine2->getStudentId())	{
					$mergedDataLine1 = ResultsDataLine::mergeDataWeighted($dataLine1, $dataLine2, $headerLine1);
					$listsize = sizeof($combinedDataLineArray1);
					$combinedDataLineArray1[$listsize] = $mergedDataLine1;
					$combinedControlIndexArray1[$mergedDataLine1->getStudentId()] = $listsize;
					$i++; $j++;
				} else if ($dataLine1->getStudentId() < $dataLine2->getStudentId())	{
					$i++;
				} else {
					/* $dataLine2->getStudentId() < $dataLine1->getStudentId() */
					$j++;
				}
			}
		}
		$mergedResultsFile1->assignData($database, $conn, $combinedHeaders, $headerLine1, $combinedDataLineArray1, "my_temp_file", $combinedControlIndexArray1);
		return $mergedResultsFile1;
	}
	public function merge($resultsFile1, $systemTime1, $overwrite, $allowSemesterMixing)	{
		/* We do Assume the two files are sorted */
		if (is_null($overwrite)) $overwrite = false;
		/* Merge resultsFile1 to this resultsFile 	
		*/
		$headers = null;
		$headerLine1  = null;
		$dataLines = null;
		if (is_null($resultsFile1)) Object::shootException("One of the Merging Results File is not set");
		//Working With Headers 
		$headers1 = $this->headers;
		$headers2 = $resultsFile1->getHeaders();
		if (is_null($headers1) || is_null($headers2)) Object::shootException("One of the Merging headers is not set");
		$headers = array();
		foreach ($headers1 as $key => $val)	{
			if ($key == "columnCount") continue;
			if ($key == "compilationTime") continue;
			if (($key == "semester") && $allowSemesterMixing) continue;
			if ($headers1[$key] != $headers2[$key]) Object::shootException("Could not match the $key. Make sure the data matches the supplied format");
			$headers[$key] = $headers1[$key];
		}
		$headers["compilationTime"] = $systemTime1->getDateAndTimeString();
		if (is_null($resultsFile1->getHeaderLine())) Object::shootException("The uploaded file has no Header Lines or are Empty");
		if (is_null($this->headerLine)) Object::shootException("The stored Header Lines are Empty");
		$headerLine1 = ResultsHeaderLine::mergeHeader($this->headerLine, $resultsFile1->getHeaderLine());
		$headers["columnCount"] = $headerLine1->getNumberOfColumns();
		if ($allowSemesterMixing) $headers["semester"] = 0;
		//Working with data -- assume sorted 
		$dataLineArr1 = $this->dataLines;
		$dataLineArr2 = $resultsFile1->getDataLines();
		$dataLines = array();
		$controlIndexArray = array();
		$i = 0;
		$j = 0;
		for (; ($i < sizeof($dataLineArr1)) && ($j < sizeof($dataLineArr2)); )	{
			/*
			i and j would be advanced depends on the situation
			*/
			$dataLine1 = $dataLineArr1[$i];
			$dataLine2 = $dataLineArr2[$j];
			$mergedDataLine1 = null;
			if ($dataLine1->getStudentId() == $dataLine2->getStudentId())	{
				$mergedDataLine1 = ResultsDataLine::mergeData($dataLine1, $dataLine2, $headerLine1, $overwrite);
				$i++; $j++;
			} else if ($dataLine1->getStudentId() < $dataLine2->getStudentId())	{
				$mergedDataLine1 = ResultsDataLine::mergeData($dataLine1, null, $headerLine1, $overwrite);
				$i++;
			} else {
				/* $dataLine2->getStudentId() < $dataLine1->getStudentId() */
				$mergedDataLine1 = ResultsDataLine::mergeData(null, $dataLine2, $headerLine1, $overwrite);
				$j++;
			}
			$listsize = sizeof($dataLines);
			$dataLines[$listsize] = $mergedDataLine1;
			$controlIndexArray[$mergedDataLine1->getStudentId()] = $listsize;
		}
		//if Array 1 is still having data just append coz the array 2 was exhausted above 
		for (; $i < sizeof($dataLineArr1); $i++)	{
			$dataLine1 = $dataLineArr1[$i];
			$mergedDataLine1 = ResultsDataLine::mergeData($dataLine1, null, $headerLine1, $overwrite);
			$listsize = sizeof($dataLines);
			$dataLines[$listsize] = $mergedDataLine1;
			$controlIndexArray[$mergedDataLine1->getStudentId()] = $listsize;
		}
		//if Array 2 is still having data just append coz array 1 was exhausted 
		for (; $j < sizeof($dataLineArr2); $j++)	{
			$dataLine2 = $dataLineArr2[$j];
			$mergedDataLine1 = ResultsDataLine::mergeData(null, $dataLine2, $headerLine1, $overwrite);
			$listsize = sizeof($dataLines);
			$dataLines[$listsize] = $mergedDataLine1;
			$controlIndexArray[$mergedDataLine1->getStudentId()] = $listsize;
		}
		/* We need to update this ResultsFile Object  */
		$this->headers = $headers;
		$this->headerLine = $headerLine1;
		$this->dataLines = $dataLines;
		$this->controlIndexArray = $controlIndexArray;
		return $this;
	}
}
class ResultsHeaderLine	{
	/* Map Key File  */
	private $line;
	private $transactionArr;
	static $__IS_CLONE_MODE = "__is_clone_mode";
	public final static function mergeHeader($headerLine1, $headerLine2)	{
		if (is_null($headerLine1) && is_null($headerLine2)) Object::shootException("Both Headers are Empty");
		$list1 = null; $list2 = null;
		if (! is_null($headerLine1))	$list1 = $headerLine1->getHeaderArray();
		if (! is_null($headerLine2))	$list2 = $headerLine2->getHeaderArray();
		$mergedList = array();
		if (! is_null($list1))	{
			foreach ($list1 as $id1)	{
				$mergedList[sizeof($mergedList)] = $id1;
			}
		}
		if (! is_null($list2))	{
			foreach ($list2 as $id1)	{
				$mergedList[sizeof($mergedList)] = $id1;
			}
		}
		$mergedList = Object::getUniqueArrayFromArray($mergedList);
		//Construct a line 
		$line = "";
		for ($i = 1; $i < Object::$numberOfResultsFileLeadingColumn; $i++)	$line .= ",";
		for ($i = 0; $i < sizeof($mergedList); $i++)	{
			$transactionId = $mergedList[$i];
			$line .= ",$transactionId";
		}
		return (new ResultsHeaderLine($line));
	}
	public function getTransactionHeader($transactionId)	{
		$list1 = $this->transactionArr;
		//check if transactionId in array 
		$found = false;
		foreach ($list1 as $alist1)	{
			if ($alist1 == $transactionId)	{
				$found = true;
				break;
			}
		}
		if (! $found) return null;
		$line = "";
		for ($i = 1; $i < Object::$numberOfResultsFileLeadingColumn; $i++)	$line .= ",";
		$line .= ",$transactionId";
		return (new ResultsHeaderLine($line));
	}
	public function getCustomTransactionHeader($transactionList)	{
		$list1 = $this->transactionArr;
		//Found List 
		$foundList1 = array();
		//Add Only found Headers 
		foreach ($transactionList as $transactionId)	{
			foreach ($list1 as $alist1)	{
				if ($alist1 == $transactionId)	{
					$foundList1[sizeof($foundList1)] = $transactionId;
					break; //Break Inner Loop
				}
			}
		}
		if (sizeof($foundList1) == 0) return null;
		$foundList1 = Object::getUniqueArrayFromArray($foundList1);
		//We need to make sure the
		$line = "";
		for ($i = 1; $i < Object::$numberOfResultsFileLeadingColumn; $i++)	$line .= ",";
		foreach ($foundList1 as $transactionId)	$line .= ",$transactionId";
		return (new ResultsHeaderLine($line));
	}
	public function __construct($line)	{
		if ($line == ResultsHeaderLine::$__IS_CLONE_MODE) return;
		$this->line = $line;
		$lineArr = explode(",", $line);
		if (sizeof($lineArr) < Object::$numberOfResultsFileLeadingColumn) {
			$this->transactionArr = null;
			return;
		}
		$this->transactionArr = array();
		for ($i = Object::$numberOfResultsFileLeadingColumn; $i < sizeof($lineArr); $i++)	{
			$this->transactionArr[sizeof($this->transactionArr)] = intval($lineArr[$i]);
		}
	}
	public function getNumberOfColumns()	{
		return (intval(Object::$numberOfResultsFileLeadingColumn) + sizeof($this->transactionArr));
	}
	public function getLine()	{ return $this->line; }
	public function getHeaderArray()	{
		return $this->transactionArr;
	}
	public function assignData($line, $transactionArr)	{
		$this->line = $line;
		$this->transactionArr = $transactionArr;
	}
	public function cloneMe()	{
		//Create a new Reference space 
		$resultsHeaderLine1 = new ResultsHeaderLine(ResultsHeaderLine::$__IS_CLONE_MODE);
		$transactionArr = array();
		foreach ($this->transactionArr as $key => $val)	{
			$transactionArr[$key] = $val;
		}
		$resultsHeaderLine1->assignData($this->line, $transactionArr);
		return $resultsHeaderLine1;
	}
}
class ResultsDataLine	{
	private $studentId;
	private $isRepeating;
	private $registrationNumber;
	private $cleared;
	private $clearedDetails;
	private $initialString;
	private $leadingData;
	private $line;
	private $itemCount;
	private $marksArray;
	private $resultsHeader;
	private $specialInstructions;
	static $__IS_CLONE_MODE = "__is_clone_mode";
	static $__NO_INSTRUCTION = 0;
	static $__FAILED_INSTRUCTION = 1;
	public function debug()	{ echo $this->line; }
	public function setMarksForTransaction($transactionId, $marks)	{
		if (! isset($this->marksArray[$transactionId])) Object::shootException("Subject was not found in the source File");
		$this->marksArray[$transactionId] = $marks;
	}
	public function getMarksForTransaction($transactionId)	{
		if (! isset($this->marksArray[$transactionId])) Object::shootException("Subject was not found in the source File");
		return $this->marksArray[$transactionId];
	}
	public final static function getResultsLineBasedOnNewMaximumScore($dataLine1, $oldMaximumScore, $newMaximumScore)	{
		$headerLine1 = $dataLine1->getResultsHeader();
		$headerArray1 = $headerLine1->getHeaderArray();
		$initialString = "";
		$list1 = array();
		//Initialization 
		for ($i = 0; $i < sizeof($headerArray1); $i++)	{
			$list1[$headerArray1[$i]] = ""; //Nothing During Initialization procedures
		}
		foreach ($list1 as $key => $val)	{
			$marksArray1 = $dataLine1->getMarksArray();
			if (isset($marksArray1[$key]) && (trim($marksArray1[$key]) != "")) {
				$list1[$key] = round((floatval($marksArray1[$key]) * $newMaximumScore) / $oldMaximumScore ,2);
			}
		}
		$initialString = $dataLine1->getInitialString();
		//We need to write a Line 
		for ($i = 0; $i < sizeof($headerArray1); $i++)	{
			$initialString .= ",".$list1[$headerArray1[$i]];
		}
		return (new ResultsDataLine($initialString, $headerLine1));
	}
	public function round()	{
		if (is_null($this->marksArray)) return;
		foreach ($this->marksArray as $key => $val)	{
			$this->marksArray[$key] = round($val);
		}
	}
	public final static function mergeDataWeighted($dataLine1, $dataLine2, $headerLine1)	{
		if (is_null($dataLine1) || is_null($dataLine2)) Object::shootException("Both Data Lines should be set");
		$list1 = array();
		$headerArray1 = $headerLine1->getHeaderArray();
		$initialString = "";
		//Initialization 
		for ($i = 0; $i < sizeof($headerArray1); $i++)	{
			$list1[$headerArray1[$i]] = ""; //Just Initialization Procedures 
		}
		//Both DataLines Should be present 
		$marksArray1 = $dataLine1->getMarksArray();
		$marksArray2 = $dataLine2->getMarksArray();
		if ($dataLine1->getStudentId() != $dataLine2->getStudentId()) Object::shootException("You are merging non-compatible students");
		if ($dataLine1->getRegistrationNumber() != $dataLine2->getRegistrationNumber()) Object::shootException("You are merging non-compatible students");
		foreach ($list1 as $key => $val)	{
			/*
			Both Marks from both ends should be present to calculate, if any is missing then leave the default.empty
			if we are in the same semester
			*/
			
			if (isset($marksArray1[$key]) && isset($marksArray2[$key]))	{
				if ((trim($marksArray1[$key]) != "") && (trim($marksArray2[$key]) != ""))	{
					$list1[$key] = floatval($marksArray1[$key]) + floatval($marksArray2[$key]);
				}
			} 
		}
		$mergedResultsDataLine1 = new ResultsDataLine(ResultsDataLine::$__IS_CLONE_MODE, "Ndimangwa Fadhili Ngoya");
		$mergedResultsDataLine1->setResultsHeader($headerLine1);
		$mergedResultsDataLine1->setStudentId($dataLine1->getStudentId());
		$mergedResultsDataLine1->setRepeating("0");
		$mergedResultsDataLine1->setRegistrationNumber($dataLine1->getRegistrationNumber());
		$mergedResultsDataLine1->setMarksArray($list1);
		$mergedResultsDataLine1->setCleared("1");
		if ($dataLine1->isDisqualified() || $dataLine2->isDisqualified())	{
			//Technical Disqualified 
			$mergedResultsDataLine1->setDisqualified("1");
		} else if ($dataLine1->isFailed() || $dataLine2->isFailed())	{
			//Technical Failure 
			$mergedResultsDataLine1->setFailed("1");
		}
		$mergedResultsDataLine1 = $mergedResultsDataLine1->synchronize();
		return $mergedResultsDataLine1;
	}
	public final static function mergeData($dataLine1, $dataLine2, $headerLine1, $overwrite)	{
		if (is_null($dataLine1) && is_null($dataLine2)) return null;
		$list1 = array();
		$headerArray1 = $headerLine1->getHeaderArray();
		$initialString = "";
		//Initialization 
		for ($i = 0; $i < sizeof($headerArray1); $i++)	{
			$list1[$headerArray1[$i]] = ""; //Nothing During Initialization procedures
		}
		if (is_null($dataLine1))	{
			//Use dataLine2 
			$marksArray1 = $dataLine2->getMarksArray();
			foreach ($list1 as $key => $val)	{
				if (isset($marksArray1[$key]))	$list1[$key] = $marksArray1[$key];
			}
			$initialString = $dataLine2->getInitialString();
		} else if (is_null($dataLine2))	{
			//User dataLine1
			$marksArray1 = $dataLine1->getMarksArray();
			foreach ($list1 as $key => $val)	{
				if (isset($marksArray1[$key]))	$list1[$key] = $marksArray1[$key];
			}
			$initialString = $dataLine1->getInitialString();
		} else {
			$marksArray1 = $dataLine1->getMarksArray();
			$marksArray2 = $dataLine2->getMarksArray();
			foreach ($list1 as $key => $val)	{
				$enableOverwrite = false;
				if (isset($marksArray1[$key])) {	
					$list1[$key] = $marksArray1[$key]; $enableOverwrite = true;
				}
				if (! $enableOverwrite && isset($marksArray2[$key]))	{
					$list1[$key] = $marksArray2[$key]; //array 1 has was not set 
				}
				if ($overwrite && $enableOverwrite && isset($marksArray2[$key]))	{
					$list1[$key] = $marksArray2[$key];
				}				
			}
			if ($dataLine1->getStudentId() != $dataLine2->getStudentId())	Object::shootException("Leading Columns are not matching for the two lines refering to the same student");
			$initialString = $dataLine1->getInitialString();
		}	
		//We need to write a Line 
		for ($i = 0; $i < sizeof($headerArray1); $i++)	{
			$initialString .= ",".$list1[$headerArray1[$i]];
		}
		return (new ResultsDataLine($initialString, $headerLine1));
	}
	public final static function getClearedDetailsArrayFromString($clearedDetails)	{
		$list1 = array();
		/*
		INPUT transactionId.cleared#transactionId.cleared ie 7.2 or 7.2#8.1
		OUTPUT list[transactionId] = cleared
		*/
		$clearedDetails = trim($clearedDetails);
		if ($clearedDetails != "")	{
			$blockArray1 = explode("#", $clearedDetails); //Now we have 7.2 
			foreach ($blockArray1 as $singleblockArray1)	{
				$blockArray2 = explode(".", $singleblockArray1);
				if (sizeof($blockArray2) != 2) Object::shootException("Cleared Details : Format error [$clearedDetails]");
				$transactionId = $blockArray2[0];
				$cleared = $blockArray2[1];
				$list1[$transactionId] = $cleared;
			}
		}
		if (sizeof($list1) == 0) $list1 = null;
		return $list1;
	}
	public final static function getClearedDetailsStringFromArray($list1)	{
		if (is_null($list1)) return ""; //Empty String 
		$count = 0;
		$string1 = "";
		foreach ($list1 as $transactionId => $cleared)	{
			if ($count == 0)	{
				$string1 = $transactionId.".".$cleared;
			} else {
				$string1 .= "#".$transactionId.".".$cleared;
			}
			$count++;
		}
		return $string1;
	}
	public final static function mergeClearedDetailsArrays($list1, $list2)	{
		$mergedList1 = null;
		if (! is_null($list1) && ! is_null($list2))	{
			$mergedList1 = array();
			//Checking for individual Transaction 
			//We are making sure only disqualified and failed are logged the rest i.e.cleared do ignore 
			foreach ($list1 as $transactionId => $cleared)	{
				//Keep Add if disqualified OR failed 
				if (($cleared == ResultsGroup::$__RESULTS_DISQUALIFIED) || ($cleared == ResultsGroup::$__RESULTS_FAILED))	{
					$mergedList1[$transactionId] = $cleared;
				}
			}
			//Now List 2 
			foreach ($list2 as $transactionId => $cleared)	{
				//If already precent you need to do precedence add, else add as you did in list above 
				if (isset($mergedList1[$transactionId]))	{
					//No need to write a lot of if else just use max function 
					$mergedList1[$transactionId] = max($mergedList1[$transactionId], $cleared);
				} else {
					//Not set 
					if (($cleared == ResultsGroup::$__RESULTS_DISQUALIFIED) || ($cleared == ResultsGroup::$__RESULTS_FAILED))	{
						$mergedList1[$transactionId] = $cleared;
					}
				}
			}
		} else if (! is_null($list1))	{
			$mergedList1 = $list1;
		} else if (! is_null($list2))	{
			$mergedList1 = $list2;
		}
		if (is_null($mergedList1) || (sizeof($mergedList1) == 0)) $mergedList1 = null;
		return $mergedList1;
	}
	public function build()	{
		//Make sure the line is having the changes made in the object 
		// This should be called before saving 
		$headerLine1 = $this->resultsHeader;
		$initialString = $this->studentId;
		$initialString .= ",".$this->isRepeating;
		$initialString .= ",".$this->registrationNumber;
		$initialString .= ",".$this->cleared;
		$initialString .= ",".ResultsDataLine::getClearedDetailsStringFromArray($this->clearedDetails);
		$initialString .= ",#";
		$headerArray1 = $headerLine1->getHeaderArray();
		$marksArray1 = array(); 
		for ($i = 0; $i < sizeof($headerArray1); $i++)	{
			$marksArray1[$headerArray1[$i]] = 0;
			$initialString .= ",".$marksArray1[$headerArray1[$i]];
		}
		return (new ResultsDataLine($initialString, $headerLine1));
	}
	public function synchronize()	{
		//Make sure the line is having the changes made in the object 
		// This should be called before saving 
		$headerLine1 = $this->resultsHeader;
		$initialString = $this->studentId;
		$initialString .= ",".$this->isRepeating;
		$initialString .= ",".$this->registrationNumber;
		$initialString .= ",".$this->cleared;
		$initialString .= ",".ResultsDataLine::getClearedDetailsStringFromArray($this->clearedDetails);
		$initialString .= ",#";
		$headerArray1 = $headerLine1->getHeaderArray();
		$marksArray1 = $this->marksArray;
		for ($i = 0; $i < sizeof($headerArray1); $i++)	{
			$initialString .= ",".$marksArray1[$headerArray1[$i]];
		}
		return (new ResultsDataLine($initialString, $headerLine1));
	}
	public function __construct($line, $resultsHeader1)	{
		if ($line == ResultsDataLine::$__IS_CLONE_MODE) return;
		$this->line = $line;
		$lineArr = explode(",", $line);
		$this->itemCount = sizeof($lineArr);
		if (sizeof($lineArr) < Object::$numberOfResultsFileLeadingColumn) return;
		$this->studentId = trim($lineArr[0]);
		$this->isRepeating = trim($lineArr[1]);
		$this->registrationNumber = trim($lineArr[2]);
		$this->cleared = trim($lineArr[3]);
		$this->clearedDetails = ResultsDataLine::getClearedDetailsArrayFromString($lineArr[4]);
		$this->resultsHeader = $resultsHeader1;
		$headerArray1 = $resultsHeader1->getHeaderArray();
		if (is_null($headerArray1)) return;
		if ((sizeof($lineArr) - Object::$numberOfResultsFileLeadingColumn) != sizeof($headerArray1)) return;
		$this->marksArray = array();
		//Initial Data 
		for ($i=0; $i< Object::$numberOfResultsFileLeadingColumn; $i++)	{
			if ($i == 0)	{
				$this->initialString = $lineArr[$i];
			} else {
				$this->initialString .= ",".$lineArr[$i];
			}
		}
		//Index of marksArray is transaction Array 
		for ($i=0; $i < sizeof($headerArray1); $i++)	{
			$transactionId = $headerArray1[$i];
			$marks = $lineArr[Object::$numberOfResultsFileLeadingColumn + $i];
			$this->marksArray[$transactionId] = $marks;
		}
		$this->leadingData = explode(",", $this->initialString);
		$this->specialInstructions = ResultsDataLine::$__NO_INSTRUCTION;
	}
	public function grade($gradePolicyArray1, $maximumScore)	{
		//Index of marksArray is transaction Array 
		$headerLine1 = $this->getResultsHeader();
		$headerArray1 = $headerLine1->getHeaderArray();
		$initialString = "";
		$list1 = array();
		//Initialization 
		for ($i = 0; $i < sizeof($headerArray1); $i++)	{
			$list1[$headerArray1[$i]] = ""; //Nothing During Initialization procedures
		}
		foreach ($list1 as $key => $val)	{
			$marksArray1 = $this->getMarksArray();
			if (isset($marksArray1[$key])) {
				/*We need to convert marks to base 100 */
				$marks = "1001";
				if (trim($marksArray1[$key]) != "")	{
					$marks = (floatval($marksArray1[$key]) * 100) / $maximumScore;
				}
				$list1[$key] = Grade::getGradeFromMarks($marks, $gradePolicyArray1);
			}
		}
		$initialString = $this->getInitialString();
		//We need to write a Line 
		for ($i = 0; $i < sizeof($headerArray1); $i++)	{
			$initialString .= ",".$list1[$headerArray1[$i]];
		}
		return (new ResultsDataLine($initialString, $headerLine1));
	}
	public function assignData($studentId, $isRepeating, $registrationNumber, $cleared, $initialString, $leadingData, $line, $itemCount, $marksArray, $resultsHeader1, $specialInstructions)	{
		$this->studentId = $studentId;
		$this->isRepeating = $isRepeating;
		$this->registrationNumber = $registrationNumber;
		$this->cleared = $cleared;
		$this->initialString = $initialString;
		$this->leadingData = $leadingData;
		$this->line = $line;
		$this->itemCount = $itemCount;
		$this->marksArray = $marksArray;
		$this->resultsHeader = $resultsHeader1;
		$this->specialInstructions = $specialInstructions;
	}
	public function cloneMe()	{
		$data1 = new ResultsDataLine(ResultsDataLine::$__IS_CLONE_MODE, null);
		$leadingData = array();
		foreach ($this->leadingData as $key => $val)	{
			$leadingData[$key] = $val;
		}
		$marksArray = array();
		foreach ($this->marksArray as $key => $val)	{
			$marksArray[$key] = $val;
		}
		$resultsHeader1 = $this->resultsHeader->cloneMe();
		$data1->assignData($this->studentId, $this->isRepeating, $this->registrationNumber, $this->cleared, $this->initialString, $leadingData, $this->line, $this->itemCount, $marksArray, $resultsHeader1, $this->specialInstructions);
		return $data1;
	}
	public function getStudentId()	{ return $this->studentId; }
	public function setStudentId($studentId)	{ $this->studentId = $studentId; }
	public function getRegistrationNumber()	{ return $this->registrationNumber; }
	public function setRegistrationNumber($registrationNumber)	{ $this->registrationNumber = $registrationNumber; }
	public function isRepeating()	{ return (intval($this->isRepeating) == 1); }
	public function setRepeating($repeating)	{
		$this->isRepeating = $repeating;
	}
	public function getLine()	{ return $this->line; }
	public function setLine($line)	{ $this->line = $line; }
	public function getItemCount()	{ return $this->itemCount; }
	public function getMarksArray()	{ return $this->marksArray; }
	public function setMarksArray($marksArray) { $this->marksArray = $marksArray; }
	public function getInitialString()	{ return $this->initialString; }
	public function setInitialString($initialString)	{ $this->initialString = $initialString; }
	public function getResultsHeader()	{ return $this->resultsHeader; }
	public function setResultsHeader($resultsHeader)	{ $this->resultsHeader = $resultsHeader; }
	public function getLeadingData()	{ return $this->leadingData; }
	public function setSpecialInstruction($spec)	{ $this->specialInstructions = $spec; }
	public function getSpecialInstruction()	{ return $this->specialInstructions; }
	public function setCleared($cleared)	{
		if (intval($cleared) == 1)	{
			$this->cleared = ResultsGroup::$__RESULTS_CLEARED;
		}
	}
	public function isCleared()	{ return (intval($this->cleared) == ResultsGroup::$__RESULTS_CLEARED); }
	public function setFailed($failed)	{
		if (intval($failed) == 1)	{
			$this->cleared = ResultsGroup::$__RESULTS_FAILED;
		}
	}
	public function isFailed()	{ return (intval($this->cleared) == ResultsGroup::$__RESULTS_FAILED); }
	public function setDisqualified($failed)	{
		if (intval($failed) == 1)	{
			$this->cleared = ResultsGroup::$__RESULTS_DISQUALIFIED;
		}
	}
	public function isDisqualified()	{  return (intval($this->cleared) == ResultsGroup::$__RESULTS_DISQUALIFIED); }
	public function setClearedDetails($clearedDetails)	{
		$this->clearedDetails = $clearedDetails;
	}
	public function getClearedDetails()	{ return $this->clearedDetails; }
	public function addClearedDetails($transactionId, $cleared)	{
		$list1 = array();
		$list1[$transactionId] = $cleared;
		$this->clearedDetails = ResultsDataLine::mergeClearedDetailsArrays($this->clearedDetails, $list1);
	}
}
	
?>
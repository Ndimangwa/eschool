<?php 
class Authorize	{
/*
Requirements 
1. loginId, available through session 
2. Operation name, ie add users
3. type of operation
	{ normal, targetUserAttention, targetGroupAttention }
4. targetUserId [Option, if type of operation is targetUserAttention ]
5. targetGroupId [Option, if type of operation is targetGroupAttention ]
*/
	public final static function getAuthorizationGraphDataStructure($database, $conn, $object1, $objecttype, $searchstring)	{
		/*
		NOTE: Before calling this function, make sure the User is not Root
		INPUT:
		$object1 of type Student, User or Group [JobTitle is not supported as input]
		$objecttype user or group , note student is also a user since it supports both getJobTitle as well as getGroup 
		$searchstring , since we are going to traverse against contextPosition objects available in our database 
		
		OUTPUT:
		datastructure Two Dimension Array 
		<Empty>:Header1:Header2: .... :Headern [Headern-1 is for System and Headern is status 1 Accept 0 Reject]
		<Empty>:Caption1:Caption2:.....:Captionn [Additional Info for Headers]
		contextId:X:X:1..........:1
		contextId:0:.............:0
		contextId:X:X:X:X:X:U....:1
		...
		
		*/
		$ds1 = array();
		$ds1['header']=array();
		$ds1['caption']=array();
		$ds1['header'][0] = "";
		$ds1['caption'][0] = "";
		$group1 = null;
		//A. Preparing Headers 
		if ($objecttype=="login")	{
			$ds1['header'][1]=$object1->getFullname();
			$ds1['header'][2]=$object1->getJobTitle()->getJobName();
			$ds1['caption'][1]="Login";
			$ds1['caption'][2]="Job Title";
			$group1 = $object1->getGroup();
		} else if ($objecttype == "group")	{
			$group1 = $object1;
		} else 	{
			return null; 
		}
		//We are now dealing with group 
		while (! is_null($group1))	{
			$len = sizeof($ds1['header']);
			$ds1['header'][$len] = $group1->getGroupName();
			$ds1['caption'][$len] = "Group";
			$group1 = $group1->getParentGroup();
		} //end-while
		$len = sizeof($ds1['header']);
		$ds1['header'][$len] = "System";
		$ds1['caption'][$len] = "System";
		//Status 
		$len = sizeof($ds1['header']);
		$ds1['header'][$len]="";
		$ds1['caption'][$len]="";
		//Row Width//Number Of Columns 
		$numberOfColumns = sizeof($ds1['header']);
		//B. Dealing with Data 
		$row="row";
		$count = 0;
		$query="SELECT cId FROM contextPosition";
		$result=mysql_db_query($database, $query, $conn) or die("Could not Consult context services");
		while (list($contextId)=mysql_fetch_row($result))	{
			$context1 = null;
			try	{
				$context1 = new ContextPosition($database, $contextId, $conn);
			} catch (Exception $e)	{
				die($e->getMessage());
			}
			$matrix1 = new SearchMatrix($searchstring);
			if ($context1->searchMatrix($matrix1)->evaluateResult())	{
				$pos = $context1->getCharacterPosition();
				$rowIndex=$row.$count;
				//$rowIndex="'".$row.$count."'";
				$count++;
				$ds1[$rowIndex] = array();
				$ds1[$rowIndex][0] = $context1->getContextId();
				$group1 = null;
				if ($objecttype == "login")	{
					$context = $object1->getContext();
					$ctxChar=self::getContextCharacter($context, $pos);
					$ds1[$rowIndex][1] = $ctxChar;
					if ($ctxChar != "X")	{
						//Finalize this row 
						$ds1[$rowIndex][$numberOfColumns-1]=0;
						if ($ctxChar != "0")	{
							$ds1[$rowIndex][$numberOfColumns-1]=1;
						}
						continue;
					}
					//Proceed to JobTitle 
					$job1 = $object1->getJobTitle();
					$context = $job1->getContext();
					$ctxChar=self::getContextCharacter($context, $pos);
					$ds1[$rowIndex][2] = $ctxChar;
					if ($ctxChar != "X")	{
						//Finalize this row 
						$ds1[$rowIndex][$numberOfColumns-1]=0;
						if ($ctxChar != "0")	{
							$ds1[$rowIndex][$numberOfColumns-1]=1;
						}
						continue;
					}
					$group1 = $object1->getGroup();
				} else if ($objecttype == "group")	{
					$group1 = $object1;
				} else	{
					return null;
				}
				$systemDefault = true;
				while (! is_null($group1))	{
					$len = sizeof($ds1[$rowIndex]);
					$context=$group1->getContext();
					$ctxChar=self::getContextCharacter($context, $pos);
					$ds1[$rowIndex][$len] = $ctxChar;
					if ($ctxChar != "X")	{
						//Finalize this row 
						$ds1[$rowIndex][$numberOfColumns-1]=0;
						if ($ctxChar != "0")	{
							$ds1[$rowIndex][$numberOfColumns-1]=1;
						}
						$systemDefault=false;
						break;
					}
					$group1 = $group1->getParentGroup();
				} //end-inner-while
				if ($systemDefault)	{
					//Load Default X Value 
					$query="SELECT defaultXValue FROM contextManager";
					$sysresult = mysql_db_query($database, $query, $conn) or die("Could not load default security context");
					if (mysql_num_rows($sysresult) != 1) die("Problem with System security context");
					list($defaultSystemContext)=mysql_fetch_row($sysresult);
					$ds1[$rowIndex][$numberOfColumns-2] = $defaultSystemContext;
					$ds1[$rowIndex][$numberOfColumns-1] = $defaultSystemContext;
				}
			} //end-if-searchtext
		}//end-of-while
		return $ds1;
	}
	final public static function isSessionSet()	{
		return isset($_SESSION['auth']);
	}
	final public static function setSession($op)	{
		/* This will keep track of which operation has been denied */
		$_SESSION['auth']=$op;
	}
	final public static function getSessionValue()	{ return $_SESSION['auth']; }
	final public static function clearSession()	{
		/* Immediately after display an error message clear this message */
		unset($_SESSION['auth']);
	}
	final public static function getContextCharacter($context, $pos)	{
		return substr($context, $pos, 1);	/*zero based string, */
	}
	final public static function getGroupContextCharacter($group1, $pos)	{
		if (is_null($group1)) return "X"; /* Simply do not care we have reached top of the ladder and still we are facing do not care */
		$groupContext1=$group1->getContext();
		$groupContextChar1=self::getContextCharacter($groupContext1, $pos);
		if ($groupContextChar1 != "X") return $groupContextChar1;
		/* We still have X */
		return self::getGroupContextCharacter($group1->getParentGroup(), $pos);
	}
	final private static function isAuthorizeTarget($database, $conn, $loginId, $originalContextChar, $pos, $optype, $defaultSystemContext, $targetUserId, $targetGroupId)	{
		$originalDefinition1 = new ContextDefinition($database, $originalContextChar, $conn);
		$originalContextValue = intval($originalDefinition1->getContextValue());
		/* Also add additional optype, which is targetJobTitleAttention */
		if ($optype == "normal")	{
			/* 3.6.3 Other operations */
			return true;
		} else if ($optype == "targetUserAttention")	{
			/* 3.6.1 deals with user */
			/* 3.6.1.0 We need to check if the target user is a super user */
			$user1 = new User($database, $targetUserId, $conn);
			if ($user1->getLogin()->isRoot())	{
				/*Target user is a root user, only a root user can operate on this type of a user */
				/* Fetch now the source user login infor */
				$login1 = new Login($database, $loginId, $conn);
				if ( ! $login1->isRoot())	{
					/* Note, if the target user is a root, then a normal user can not do any operation upon this user */
					return false;
				}
			}
			/*3.6.1.1 context value*/
			$userContext1 = $user1->getContext();
			/*3.6.1.3 extract context character */
			$userContextChar1 = self::getContextCharacter($userContext1, $pos);
			if ($userContextChar1 == "X")	{
				/*3.6.1.4 character is X*/
				/*At this time Job Title was not considered insert it here*/
				/*3.6.1.4.1 context value of group*/
				$group1 = $user1->getGroup();
				$groupContextChar1 = self::getGroupContextCharacter($group1, $pos);
				/*3.6.1.4.3 if X*/
				if ($groupContextChar1 == "X")	{
					$sysContextValue = intval($defaultSystemContext);
					return ($originalContextValue > $sysContextValue);				
				} else	{
					/* 3.6.1.5 any character this is just for reference 0,1,2 --- U*/
					$targetDefinition1 = new ContextDefinition($database, $groupContextChar1, $conn);
					$targetContextValue = intval($targetDefinition1->getContextValue());
					return ($originalContextValue > $targetContextValue);
				}
			} else {
				/*3.6.1.5 any character this is just for reference 0,1,2 --- U */
				$targetDefinition1 = new ContextDefinition($database, $userContextChar1, $conn);
				$targetContextValue = intval($targetDefinition1->getContextValue());
				return ($originalContextValue > $targetContextValue);
			}
		} else if ($optype == "targetGroupAttention")	{
				/* 3.6.2 Deals with existing groups */
			$group1 = new Group($database, $targetGroupId, $conn);
			/* 3.6.2.1 Context for this group */
			$groupContext1 = $group1->getContext();
			/* 3.6.2.3 get context character */
			$groupContextChar1 = self::getContextCharacter($groupContext1, $pos);
			if ($groupContextChar1 == "X")	{
				$sysContextValue = intval($defaultSystemContext);
				return ($originalContextValue > $sysContextValue);
			} else	{
				/* 3.6.1.5 any character this is just for reference 0,1,2 --- U*/
					$targetDefinition1 = new ContextDefinition($database, $groupContextChar1, $conn);
					$targetContextValue = intval($targetDefinition1->getContextValue());
					return ($originalContextValue > $targetContextValue);
			}
		} else { return false;}
	} 
	final public static function isAllowable($config, $op, $optype, $setlog, $targetUserId, $targetGroupId)	{
		$loginId = $_SESSION['login'][0]['id'];
		$accept = false;
		/*up to this point we have loginId, op and optype*/
		include($config);
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database server");
		$login1 = null;
		try	{
			$login1 = new Login($database, $loginId, $conn);
		} catch (Exception $e)	{ die("Login Info ".$e->getMessage()); }
		/*Make sure you do not lock yourself out if you already identified with targetUserId */
		if ($optype == "targetUserAttention")	{
			/* check if sourceUserId = targetUserId */
			if ($login1->getUser()->getUserId() == $targetUserId) {
				mysql_close($conn);
				if ($setlog=="setlog") self::setSession($op);
				return false;
			}
		}
		/* Step 1 Marked as root */
		if ($login1->isRoot())	{ mysql_close($conn); return true; }
		/* Step 2, load security context */
		$query="SELECT defaultXValue FROM contextManager";
		$result = mysql_db_query($database, $query, $conn) or die("Could not load default security context");
		if (mysql_num_rows($result) != 1) die("Problem with System security context");
		list($defaultSystemContext)=mysql_fetch_row($result);
		/*3.1 context value of this user*/
		$userContext1 = $login1->getContext();
		/* 3.2 position for this operation */
		$pos=ContextPosition::getPositionFromName($database, $op, $conn); /* Mark this position */
		/* 3.3 Extract character from context for position pos */
		$userContextChar1 = self::getContextCharacter($userContext1, $pos);
		/*3.4 context character is X*/	
		if ($userContextChar1 == "X")	{
			/* code were added to support job title */
			$job1 = $login1->getJobTitle();
			/* context value of this job */
			$jobContext1 = $job1->getContext();
			/* context character for this job */
			$jobContextChar1 = self::getContextCharacter($jobContext1, $pos);
			if ($jobContextChar1 == "X")	{
			/* job title code */
				$group1 = $login1->getGroup();
				$groupContextChar1 = self::getGroupContextCharacter($group1, $pos);
				if ($groupContextChar1 == "X")	{
					mysql_close($conn);
					if ( (! ($defaultSystemContext == "1")) && ($setlog=="setlog")) self::setSession($op);
					return ($defaultSystemContext == "1");
				} else	if ($groupContextChar1 == "0")	{
					/* 3.4.4 */
					mysql_close($conn);
					if ($setlog=="setlog") self::setSession($op);
					return false;
				} else	{
					/* 3.4.5  1,2,3 -- U */
					$accept=self::isAuthorizeTarget($database, $conn, $loginId, $groupContextChar1, $pos, $optype, $defaultSystemContext, $targetUserId, $targetGroupId);	
					mysql_close($conn);
					if ((! $accept) && ($setlog=="setlog")) self::setSession($op);
					return $accept;
				}
			} else if ($jobContextChar1 == "0")	{
				mysql_close($conn);
				if ($setlog=="setlog") self::setSession($op);
				return false;
			} else	{
				/* 1,2,3 -- U */
				$accept=self::isAuthorizeTarget($database, $conn, $loginId, $jobContextChar1, $pos, $optype, $defaultSystemContext, $targetUserId, $targetGroupId);
				mysql_close($conn);
				if ((! $accept) && ($setlog=="setlog")) self::setSession($op);
				return $accept;
			}
		} else if ($userContextChar1 == "0")	{
			/* Now 3.5  deny value of 0 */
			mysql_close($conn);
			if ($setlog=="setlog") self::setSession($op);
			return false;
		} else {
			/*3.6 1,2 -- U*/
			$accept=self::isAuthorizeTarget($database, $conn, $loginId, $userContextChar1, $pos, $optype, $defaultSystemContext, $targetUserId, $targetGroupId);
			mysql_close($conn);
			if ((! $accept) && ($setlog=="setlog")) self::setSession($op);
			return $accept;
		}		
		/*The follwing codes will never be executed but just let them be there*/
		mysql_close($conn);
		if ((! $accept) && ($setlog=="setlog")) self::setSession($op);
		return $accept;
	}
}
?>

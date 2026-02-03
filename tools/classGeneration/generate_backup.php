<?php 
/* V1021 on FEBRUARY 11, 2016 */
/*
	The Input file should have the following format
	{
		class: {ClassName}
		table: {tableName}
		column: {columnName}; type: {text|boolean|object|list|listobject}; property: {propertyName[,objectName]}[;role: {primary|value|others}]
		column: {columnName}; type: {text|boolean|object|list|listobject}; property: {propertyName[,objectName]}[;role: {primary|value|others}]
		column: {columnName}; type: {text|boolean|object|list|listobject}; property: {propertyName[,objectName]}[;role: {primary|value|others}]
		.
		.
		.
		.
	}
	Any_Word_As_A_Separator
	{
		class: {ClassName}
		table: {tableName}
		column: {columnName}; type: {text|boolean|object|list|listobject}; property: {propertyName[,objectName]}[;role: {primary|value|others}]
		column: {columnName}; type: {text|boolean|object|list|listobject}; property: {propertyName[,objectName]}[;role: {primary|value|others}]
		column: {columnName}; type: {text|boolean|object|list|listobject}; property: {propertyName[,objectName]}[;role: {primary|value|others}]
		.
		.
		.
		.
	}
	.
	.
	.
	Any_Word_As_A_Separator
	
*/
class Tools	{
	//static methods
	public final static function getIndexOfTableColumn($columnArray, $tableColumn)	{
		$index = -1;
		for ($i=0; $i < sizeof($columnArray); $i++)	{
			if ($columnArray[$i]['column'] == $tableColumn)	{
				$index = $i;
				break;
			}
		}
		return $index;
	}
	public final static function getIndexOfObjectProperty($columnArray, $objectProperty)	{
		$index = -1;
		for ($i=0; $i < sizeof($columnArray); $i++)	{
			if ($columnArray[$i]['property'] == $objectProperty)	{
				$index = $i;
				break;
			}
		}
		return $index;
	}
	public final static function getIndexOfPrimaryColumn($columnArray)	{
		$index = -1;
		for ($i=0; $i < sizeof($columnArray); $i++)	{
			if (strtolower($columnArray[$i]['role']) == "primary")	{
				$index = $i;
				break;
			}
		}
		return $index;
	}
	public final static function getIndexOfValueColumn($columnArray)	{
		$index = -1;
		for ($i=0; $i < sizeof($columnArray); $i++)	{
			if (strtolower($columnArray[$i]['role']) == "value")	{
				$index = $i;
				break;
			}
		}
		return $index;
	}
	public final static function getIndicesOfNonPrimaryColumns($columnArray)	{
		$list = array();
		$indexOfPrimary = self::getIndexOfPrimaryColumn($columnArray);
		for ($i=0; $i < sizeof($columnArray); $i++)	{
			if ($i != $indexOfPrimary)	{
				$list[sizeof($list)] = $i;
			}
		}
		return $list;
	}
	public final static function getIndicesOfColumnType($columnArray, $columntype)	{
		$list = array();
		for ($i=0; $i < sizeof($columnArray); $i++)	{
			if ($columnArray[$i]['type'] == $columntype)	{
				$list[sizeof($list)] = $i;
			}
		}
		return $list;
	}
	public final static function getListOfAllColumns($columnArray)	{
		$list = "";
		for ($i=0; $i < sizeof($columnArray); $i++)	{
			if ($i == 0)	{
				$list = $columnArray[$i]['column'];
			} else	{
				$list .= ", ".$columnArray[$i]['column'];
			}
		}
		return $list;
	}
	public final static function getListOfAllColumnsWithDollarSign($columnArray)	{
		$list = "";
		for ($i=0; $i < sizeof($columnArray); $i++)	{
			if ($i == 0)	{
				$list = "\$".$columnArray[$i]['column'];
			} else	{
				$list .= ", \$".$columnArray[$i]['column'];
			}
		}
		return $list;
	}
	public final static function getListOfAllColumnsWithDollarAndApostropheSign($columnArray)	{
		$list = "";
		$controlArray = self::getIndicesOfNonPrimaryColumns($columnArray);
		for ($i=0; $i < sizeof($controlArray); $i++)	{
			$index = $controlArray[$i];
			if ($i == 0)	{
				$list = "'\$".$columnArray[$index]['column']."'";
			} else	{
				$list .= ", '\$".$columnArray[$index]['column']."'";
			}
		}
		return $list;
	}
	public final static function getListOfAllNonPrimaryColumnsWithDollarSign($columnArray)	{
		$list = "";
		$controlArray = self::getIndicesOfNonPrimaryColumns($columnArray);
		for ($i=0; $i < sizeof($controlArray); $i++)	{
			$index = $controlArray[$i];
			if ($i == 0)	{
				$list = "\$".$columnArray[$index]['column'];
			} else	{
				$list .= ", \$".$columnArray[$index]['column'];
			}
		}
		return $list;
	}
	public final static function getListOfAllNonPrimaryColumns($columnArray)	{
		$list = "";
		$controlArray = self::getIndicesOfNonPrimaryColumns($columnArray);
		for ($i=0; $i < sizeof($controlArray); $i++)	{
			$index = $controlArray[$i];
			if ($i == 0)	{
				$list = $columnArray[$index]['column'];
			} else	{
				$list .= ", ".$columnArray[$index]['column'];
			}
		}
		return $list;
	}
	public final static function mergeArray($array1, $array2)	{
		$list = array();
		foreach ($array1 as $arr)	{
			$list[sizeof($list)] = $arr;
		}
		foreach ($array2 as $arr)	{
			$list[sizeof($list)] = $arr;
		}
		return $list;
	}
	public final static function getAdvancedSearchStringText($columnArray, $namespaceTag)	{
		$textIndices = self::getIndicesOfColumnType($columnArray, "text");
		$booleanIndices = self::getIndicesOfColumnType($columnArray, "boolean");
		$listIndices = self::getIndicesOfColumnType($columnArray, "list");
		$combinedIndices = self::mergeArray($textIndices, $booleanIndices);
		$combinedIndices = self::mergeArray($combinedIndices, $listIndices);
		$str1 = "";
		for ($i=0; $i<sizeof($combinedIndices); $i++)	{
			$index = $combinedIndices[$i];
			if ($i == 0)	{
				if ($columnArray[$index]['type'] == "list")	{
					$str1 = "Object::getAdvancedPropertiesFromArray(\"".$columnArray[$index]['property']."\", \$this->".$columnArray[$index]['property'].", ".$namespaceTag.")";
				} else	{
					$str1 = "\"".$namespaceTag.".".$columnArray[$index]['property']."=\".str_replace(\"@\", \"_kdAt_\", str_replace(\"=\", \"_kdEq_\", \$this->".$columnArray[$index]['property']."))";
				}
			} else	{
				if ($columnArray[$index]['type'] == "list")	{
					 $str1 .= ".\"@\".Object::getAdvancedPropertiesFromArray(\"".$columnArray[$index]['property']."\", \$this->".$columnArray[$index]['property'].", ".$namespaceTag.")";
				} else	{
					$str1 .= ".\"@".$namespaceTag.".".$columnArray[$index]['property']."=\".str_replace(\"@\", \"_kdAt_\", str_replace(\"=\", \"_kdEq_\", \$this->".$columnArray[$index]['property']."))";
				}
			}
		}//endfor
		return $str1;
	}
	public final static function getSearchStringText($columnArray)	{
		$textIndices = self::getIndicesOfColumnType($columnArray, "text");
		$booleanIndices = self::getIndicesOfColumnType($columnArray, "boolean");
		$listIndices = self::getIndicesOfColumnType($columnArray, "list");
		$combinedIndices = self::mergeArray($textIndices, $booleanIndices);
		$combinedIndices = self::mergeArray($combinedIndices, $listIndices);
		$str1 = "";
		for ($i=0; $i<sizeof($combinedIndices); $i++)	{
			$index = $combinedIndices[$i];
			if ($i == 0)	{
				if ($columnArray[$index]['type'] == "list")	{
					$str1 = "Object::getPropertiesFromArray(\"".$columnArray[$index]['property']."\", \$this->".$columnArray[$index]['property'].")";
				} else	{
					$str1 = "\"".$columnArray[$index]['property']."=\$this->".$columnArray[$index]['property']."\"";
				}
			} else	{
				if ($columnArray[$index]['type'] == "list")	{
					 $str1 .= ".\"@\".Object::getPropertiesFromArray(\"".$columnArray[$index]['property']."\", \$this->".$columnArray[$index]['property'].")";
				} else	{
					$str1 .= ".\"@".$columnArray[$index]['property']."=\$this->".$columnArray[$index]['property']."\"";
				}
			}
		}//endfor
		return $str1;
	}
	public final static function capitalizeFirstLetter($text)	{
		return ucfirst($text);
	}
	public final static function getListOfObjectsIAmLinkedTo($classname, $columnArray)	{
		//remember this is per the current object in the map 
		$list = array();
		foreach ($columnArray as $arr1)	{
			// for type=object OR type=listobject
			if ((strtolower($arr1['type']) == "object") || (strtolower($arr1['type']) == "listobject"))	{
				//To make sure there is no loop, consider group
				//and its parent group 
				if (strtolower($classname) != strtolower($arr1['object']))	{
					$list[sizeof($list)] = $arr1['object'];
				}
			}
		} //end foreach
		return $list;
	}
	public final static function getAvailableViewableColumns($classname, $columnArray, $namespaceTag)	{
		$list = "@".$classname."/";
		$itemsFound = 0;
		foreach ($columnArray as $arr1)	{
			if (strtolower($arr1['role']) != "primary")	{
				//Only None Primary column 
				if ((strtolower($arr1['type'])=="boolean") || (strtolower($arr1['type'])=="text") || (strtolower($arr1['type'])=="list"))	{
					//strip out all kinds of objects 
					
					if ($itemsFound == 0)	{
						$list .= $arr1['property'].",".$arr1['type'].",".$namespaceTag;
					} else	{
						$list .= ";".$arr1['property'].",".$arr1['type'].",".$namespaceTag;
					}
					$itemsFound++;
				}
			}
		}
		$list = "\"".$list."\"";
		return $list;
	}
}
if (! (isset($argv[1]) && isset($argv[2]) && isset($argv[3]) && isset($argv[4]))) die("Command Syntax, \"php generate.php initializationfile.php input_data_file.dat NAMESPACETAGCOUNTER output_classfile.php\"");
if (isset($argv[1]) && $argv[1] == "--help")	{ die("Command Syntax, \"php generate.php input_data_file output_classfile\""); }
$initCodeFile=$argv[1];
$infile=$argv[2];
$namespaceTag=intval($argv[3]);
$outfile=$argv[4];
$prefix = "AGHZ_";
if (! file_exists($infile)) die("\nInput file is not found\n");
if (! file_exists($initCodeFile)) die("\nInitialization Code file is not found\n");
$sourcefile1=fopen($infile, "r") or die("Could not Open the Source File");
$finalfile1=fopen($outfile, "w") or die("Could not Open the destination File");
//Preparing Initialization Codes
$initFile1 = fopen($initCodeFile, "r") or die("Could not Open $initCodeFile for READING");
while (($line1 = fgets($initFile1)) !== false)	{
	//$classLine = $line1."\n";
	$classLine=$line1;
	fwrite($finalfile1, $classLine);
} //end of initFile1
//End of Preparing Initialization Codes
fclose($initFile1);
$state = 0;
$columnArray = null;
$classname = "";
$tablename = "";
$propertyfile=$prefix."property.dat";
$methodfile=$prefix."method.dat";
$setmefile=$prefix."setme.dat";
$clonefile=$prefix."cloneme.dat";
$classregistry=$prefix."classregistry.dat";
$generalfile=$prefix."general.dat";
$classLine="<?php\n";
fwrite($finalfile1, $classLine);
//We need to prepare the  
$classRegistryFile1 = fopen($classregistry, "w") or die("Could not open $classregistry for writing");
$registryLine="class ClassRegistry	{\n";
fwrite($classRegistryFile1, $registryLine);
$registryLine="\tpublic final static function getAvailableClassList()	{\n";
fwrite($classRegistryFile1, $registryLine);
$registryLine="\t\t\$list = array();\n";
fwrite($classRegistryFile1, $registryLine);
//Important Arrays
$listOfClassNames = array();
$listOfTableNames = array();
$listOfPrimaryKeys = array();
while (($line = fgets($sourcefile1)) !== false)	{
	//Reading Line by Line from the original file
	if ($state == 0)	{
		$columnArray = array();
		$classname="";
		$tablename = "";
	}
	if (($state == 0) && (trim($line) == "{"))	{
		$state = 1; continue;
	}
	if ($state == 1)	{
		//Exit this step after seeing the } 
		if (trim($line) == "}")	{
			$state = 2; continue;
		}
		$keyvalue = explode(";", $line);
		if (sizeof($keyvalue) == 1)	{
			//This must be header Information classname OR tablename 
			//separate key and value 
			$keyAndValue = explode(":", $keyvalue[0]);
			if (sizeof($keyAndValue) == 2)	{
				$key = $keyAndValue[0];
				$value = $keyAndValue[1];
				//Assigning
				if (trim($key) == "class")	{ 
					$classname= trim($value); 
				} else if (trim($key) == "table")	{
					$tablename = trim($value);
				}				
			}
		} else if (sizeof($keyvalue) >= 3)	{
			//This must be column Information
			//At least column, property and type should be present
			$columnIndex = sizeof($columnArray);
			$columnArray[$columnIndex] = array();
			foreach ($keyvalue as $aKeyVal)	{
				$keyAndValue = explode(":", $aKeyVal);
				if (sizeof($keyAndValue) == 2)	{
					$key = $keyAndValue[0];
					$value = $keyAndValue[1];
					//Assigning 					
					if (trim($key) == "column")	{
						$columnArray[$columnIndex]['column'] = trim($value);
					}
					if (trim($key) == "type")	{
						$columnArray[$columnIndex]['type'] = trim($value);
					}
					if (trim($key) == "property")	{
						$valueArr = explode(",", $value);
						$columnArray[$columnIndex]['property'] = trim($valueArr[0]);
						$columnArray[$columnIndex]['object'] = trim($valueArr[0]);
						if (sizeof($valueArr) == 2)	$columnArray[$columnIndex]['object'] = trim($valueArr[1]);
					}
					if (trim($key) == "role")	{
						$columnArray[$columnIndex]['role'] = trim($value);
					}
				}				
			} //end foreach 
			//Expect all values are set 
			if (! isset($columnArray[$columnIndex]['role'])) { $columnArray[$columnIndex]['role'] = "others"; }
		}
		//Variable population Now
	} //end of state 1
	if ($state == 2)	{
		$namespaceTag++; //Update the TAG
		//Main Writing Thread 
		//This is a long process it will terminate once we have cleared all temporary files 
		$propertyfile1 = fopen($propertyfile, "w") or die("Failed to open $propertyfile");
		$methodfile1 = fopen($methodfile, "w") or die("Failed to open $methodfile");
		$setmefile1 = fopen($setmefile, "w") or die("Failed to open $setmefile");
		$clonefile1 = fopen($clonefile, "w") or die("Failed to open $clonefile");
		$generalfile1 = fopen($generalfile, "w") or die("Failed to open $generalfile");
		//Preparing clone function 
		$primaryKey = Tools::getIndexOfPrimaryColumn($columnArray);
		$primaryKey = $columnArray[$primaryKey];
		$cloneLine = "\tpublic function cloneMe(\$updateDataArray)	{\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t\$masterDataArray = array();\n";
		fwrite($clonefile1, $cloneLine);
		$columnList = Tools::getListOfAllNonPrimaryColumns($columnArray);
		$columnListWithDollarSign = Tools::getListOfAllNonPrimaryColumnsWithDollarSign($columnArray);
		$cloneLine="\t\t\$query=\"SELECT ".$columnList." FROM ".$tablename." WHERE ".$primaryKey['column']." = '\$this->".$primaryKey['column']."'\";\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine="\t\t\$result=mysql_db_query(\$this->database, \$query, \$this->conn) or \$this->throwMe('".$classname.", [Clone] Data Pulling Failed');\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine="\t\tif (mysql_num_rows(\$result) != 1) \$this->throwMe('".$classname.", [Clone] Duplicate or No Record');\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine="\t\tlist(".$columnListWithDollarSign.")=mysql_fetch_row(\$result);\n";
		fwrite($clonefile1, $cloneLine);
		$columnListArr = explode(",", $columnList);
		$columnListWithDollarSignArr = explode(",", $columnListWithDollarSign);
		for ($i = 0; ($i < sizeof($columnListArr) && ($i < sizeof($columnListWithDollarSignArr))); $i++)	{
			$key = trim($columnListArr[$i]);
			$val = trim($columnListWithDollarSignArr[$i]);
			$cloneLine = "\t\t\$masterDataArray['".$key."'] = ".$val.";\n";
			fwrite($clonefile1, $cloneLine);
		} //end--for
		$cloneLine = "\t\tforeach (\$updateDataArray as \$dtKey => \$dtVal)	{\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t\tif (isset(\$masterDataArray[\$dtKey]))	{\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t\t\t\$masterDataArray[\$dtKey] = \$dtVal;\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t\t}\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t}\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t\$columnList = \"\";\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t\$dataList = \"\";\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t\$cloneCounter = 0;\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\tforeach (\$masterDataArray as \$__key => \$__val)	{\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t\tif (! is_null(\$__val))	{\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t\t\tif (\$cloneCounter == 0)	{\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t\t\t\t\$columnList = \$__key;\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t\t\t\t\$dataList = \"'\".\$__val.\"'\";\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t\t\t} else {\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t\t\t\t\$columnList = \$columnList.\", \".\$__key;\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t\t\t\t\$dataList = \$dataList.\", '\".\$__val.\"'\";\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t\t\t}\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t\t\t\$cloneCounter++;\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t\t}\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t}\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t\$query=\"INSERT INTO ".$tablename." (\$columnList) VALUES (\$dataList)\";\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t\$result = mysql_db_query(\$this->database, \$query, \$this->conn) or \$this->throwMe(\"".$classname." [Clone] Could not push record to database\");\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t\$query = \"SELECT LAST_INSERT_ID()\";\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\t\$result = mysql_db_query(\$this->database, \$query, \$this->conn) or \$this->throwMe(\"".$classname." [Clone] Could not Extract Last Insert Id\");\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\tif (mysql_num_rows(\$result) != 1) \$this->throwMe(\"".$classname." [Clone] Last Insert Id not unique\");\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\tlist(\$lastInsertId)=mysql_fetch_row(\$result);\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t\treturn \$lastInsertId;\n";
		fwrite($clonefile1, $cloneLine);
		$cloneLine = "\t}\n";
		fwrite($clonefile1, $cloneLine);
		//Preparing setMe function 
		//Primary key 
		$primaryKey = Tools::getIndexOfPrimaryColumn($columnArray);
		$primaryKey = $columnArray[$primaryKey];
		$setLine = "\tprotected function setMe(\$database, \$".$primaryKey['column'].", \$conn) {\n";
		fwrite($setmefile1, $setLine);
		$setLine="\t\t\$query=\"SELECT ".Tools::getListOfAllColumns($columnArray)." FROM ".$tablename." WHERE ".$primaryKey['column']." = '\$".$primaryKey['column']."'\";\n";
		fwrite($setmefile1, $setLine);
		$setLine="\t\t\$result=mysql_db_query(\$database, \$query, \$conn) or \$this->throwMe('".$classname.", Object Creation Failed');\n";
		fwrite($setmefile1, $setLine);
		$setLine="\t\tif (mysql_num_rows(\$result) != 1) \$this->throwMe('".$classname.", Duplicate or No Record');\n";
		fwrite($setmefile1, $setLine);
		$setLine="\t\tlist(".Tools::getListOfAllColumnsWithDollarSign($columnArray).")=mysql_fetch_row(\$result);\n";
		fwrite($setmefile1, $setLine);
		
		//Adding more lines to setMe function 
		$setLine = "\t\t\$this->database=\$database;\n";
		fwrite($setmefile1, $setLine);
		$setLine = "\t\t\$this->conn=\$conn;\n";
		fwrite($setmefile1, $setLine);
		//Primary column
		$setLine="\t\t\$this->".$primaryKey['property']." = \$".$primaryKey['column'].";\n";
		fwrite($setmefile1, $setLine);
		//preparing getQueryText for Object
		$generalLine="\tpublic final static function getQueryText()	{\n";
		fwrite($generalfile1, $generalLine);
		$generalLine="\t\t\$query=\"SELECT ".$primaryKey['column']." FROM ".$tablename."\";\n";
		fwrite($generalfile1, $generalLine);
		$generalLine="\t\treturn \$query;\n";
		fwrite($generalfile1, $generalLine);
		$generalLine="\t}\n";
		fwrite($generalfile1, $generalLine);
		//Preparing the constructor
		$generalLine = "\tpublic function __construct(\$database, \$".$primaryKey['column'].", \$conn) {\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\tif (! SystemPolicy::isCreateObjectEnabled(\$database, \$conn, \"".$classname."\")) Object::shootException(\"".$classname." System Policy could not allow creation of this kind of Object\");\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\$this->setMe(\$database, \$".$primaryKey['column'].", \$conn);\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t}\n";
		fwrite($generalfile1, $generalLine);
		//Preparing the destructor 
		$generalLine = "\tpublic function __destruct()	{\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t/*Release the System Resources at this point @Ndimangwa*/\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t}\n";
		fwrite($generalfile1, $generalLine);
		//Preparing reload
		$generalLine = "\tpublic function reload() {\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\$this->setMe(\$this->database, \$this->".$primaryKey['property'].", \$this->conn);\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t}\n";
		fwrite($generalfile1, $generalLine);
		//Preparing commitUpdate
		$generalLine = "\tpublic function commitUpdate()	{\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\tif (! SystemPolicy::isEditRecordEnabled(\$this->database, \$this->conn, \"".$classname."\")) Object::shootException(\"".$classname." System Policy could not allow modification of this type of Object\");\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\$setList = \$this->getUpdateList();\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\tif (\$this->getUpdateListLength() > 0)	{\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\t\$query=\"UPDATE ".$tablename." SET \$setList WHERE ".$primaryKey['column']." = '\$this->".$primaryKey['column']."'\";\n";
		fwrite($generalfile1, $generalLine);
		$generalLine="\t\t\t\$result=mysql_db_query(\$this->database, \$query, \$this->conn) or \$this->throwMe('".$classname.", Row Updation Failed');\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t}\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t}\n";
		fwrite($generalfile1, $generalLine);
		//Preparing commitDelete
		$generalLine = "\tpublic function commitDelete()	{\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\tif (! SystemPolicy::isDeleteRecordEnabled(\$this->database, \$this->conn, \"".$classname."\")) Object::shootException(\"".$classname." System Policy could not allow delete of this kind of Object\");\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\$query=\"DELETE FROM ".$tablename." WHERE ".$primaryKey['column']." = '\$this->".$primaryKey['column']."'\";\n";
		fwrite($generalfile1, $generalLine);
		$generalLine="\t\t\$result=mysql_db_query(\$this->database, \$query, \$this->conn) or \$this->throwMe('".$classname.", Row Deletion Failed');\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t}\n";
		fwrite($generalfile1, $generalLine);
		//Preparing getClassName
		$generalLine = "\tpublic function getClassName()	{\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\treturn \"".$classname."\";\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t}\n";
		fwrite($generalfile1, $generalLine);
		//Preparing getProperties
		$generalLine = "\tprotected function getProperties()	{\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\treturn ".Tools::getSearchStringText($columnArray).";\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t}\n";
		fwrite($generalfile1, $generalLine);
		//Preparing getAdvancedProperties
		$generalLine = "\tprotected function getAdvancedProperties()	{\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\treturn ".Tools::getAdvancedSearchStringText($columnArray, $namespaceTag).";\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t}\n";
		fwrite($generalfile1, $generalLine);
		
		//Preparing debug
		$generalLine = "\tpublic function debug()	{\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\$string1 = \$this->getProperties();\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\$keyValueArr = explode(\"@\",\$string1);\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\techo \"********************************************************************************\\n\";\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\$displayLine = \"****** CLASSNAME: $classname (NAMESPACETAG = $namespaceTag) \";\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\$linesize = strlen(\$displayLine);\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\tfor (\$i=\$linesize; \$i < 80; \$i++)	{\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\t\$displayLine = \$displayLine.\"*\";\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t}\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\techo \$displayLine.\"\\n\";\n";
		fwrite($generalfile1, $generalLine);		
		$generalLine = "\t\tforeach (\$keyValueArr as \$aKeyVal)	{\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\t\$kvArr = explode(\"=\",\$aKeyVal);\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\t\$val = \"---------\";\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\t\$key = \$kvArr[0];\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\tif (isset(\$kvArr[1])) \$val=\$kvArr[1];\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\t\$displayLine=\"** \".\$key.\"  =  \".\$val;\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\t\$linesize=strlen(\$displayLine);\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\tfor (\$i=\$linesize; \$i < 79; \$i++)	{\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\t\t\$displayLine = \$displayLine.\" \";\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\t}\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\t\$displayLine = \$displayLine.\"*\";\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\techo \$displayLine.\"\\n\";\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t}\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\techo \"********************************************************************************\\n\";\n";
		fwrite($generalfile1, $generalLine);
		
		
		//check with referencing Objects
		$indices = Tools::getIndicesOfColumnType($columnArray, "object");
		foreach ($indices as $index)	{
			$generalLine = "\t\tif (! is_null(\$this->".$columnArray[$index]['property']."))	{\n";
			fwrite($generalfile1, $generalLine);
			$generalLine = "\t\t\t\$this->".$columnArray[$index]['property']."->debug();\n";
			fwrite($generalfile1, $generalLine);
			$generalLine = "\t\t}\n";
			fwrite($generalfile1, $generalLine);
		} //end foreach
		//End of checking with referencing Objects
		// check with type = listobject 
		$indices = Tools::getIndicesOfColumnType($columnArray, "listobject");
		foreach ($indices as $index)	{
			$property = $columnArray[$index]['property'];
			$object = $columnArray[$index]['object'];
			$generalLine = "\t\tif (! is_null(\$this->".$property."))	{\n";
			fwrite($generalfile1, $generalLine);
			$generalLine = "\t\t\tforeach (\$this->".$property." as \$".$property."SingleObject)	{\n";
			fwrite($generalfile1, $generalLine);
			$generalLine = "\t\t\t\t\$".$property."SingleObject->debug();\n";
			fwrite($generalfile1, $generalLine);
			$generalLine = "\t\t\t}\n";
			fwrite($generalfile1, $generalLine);
			$generalLine = "\t\t}\n";
			fwrite($generalfile1, $generalLine);
		} //end for
		// End with check of type = listobject
		$generalLine = "\t}\n"; 
		fwrite($generalfile1, $generalLine);
		
		//Preparing searchMatrix
		$generalLine = "\tpublic function searchMatrix(\$matrix1)	{\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\$string1=\$this->getProperties();\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\$matrix1->searchText(\$string1);\n";
		fwrite($generalfile1, $generalLine);
		//check with referencing Objects
		$indices = Tools::getIndicesOfColumnType($columnArray, "object");
		foreach ($indices as $index)	{
			$generalLine = "\t\tif (! is_null(\$this->".$columnArray[$index]['property']."))	{\n";
			fwrite($generalfile1, $generalLine);
			$generalLine = "\t\t\t\$matrix1=\$this->".$columnArray[$index]['property']."->searchMatrix(\$matrix1);\n";
			fwrite($generalfile1, $generalLine);
			$generalLine = "\t\t}\n";
			fwrite($generalfile1, $generalLine);
		} //end foreach
		//End of checking with referencing Objects
		// check with type = listobject 
		$indices = Tools::getIndicesOfColumnType($columnArray, "listobject");
		foreach ($indices as $index)	{
			$property = $columnArray[$index]['property'];
			$object = $columnArray[$index]['object'];
			$generalLine = "\t\tif (! is_null(\$this->".$property."))	{\n";
			fwrite($generalfile1, $generalLine);
			$generalLine = "\t\t\tforeach (\$this->".$property." as \$".$property."SingleObject)	{\n";
			fwrite($generalfile1, $generalLine);
			$generalLine = "\t\t\t\t\$matrix1=\$".$property."SingleObject->searchMatrix(\$matrix1);\n";
			fwrite($generalfile1, $generalLine);
			$generalLine = "\t\t\t}\n";
			fwrite($generalfile1, $generalLine);
			$generalLine = "\t\t}\n";
			fwrite($generalfile1, $generalLine);
		} //end for
		// End with check of type = listobject
		$generalLine = "\t\treturn \$matrix1;\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t}\n"; 
		fwrite($generalfile1, $generalLine);
		//Preparing CSV Processor
		$generalLine = "\tpublic function processCSV(\$csvProcessor1)	{\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\$string1=\$this->getAdvancedProperties();\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t\t\$csvProcessor1->appendCSVData(\$string1);\n";
		fwrite($generalfile1, $generalLine);
		//check with referencing Objects
		$indices = Tools::getIndicesOfColumnType($columnArray, "object");
		foreach ($indices as $index)	{
			$generalLine = "\t\tif (! is_null(\$this->".$columnArray[$index]['property']."))	{\n";
			fwrite($generalfile1, $generalLine);
			$generalLine = "\t\t\t\$csvProcessor1=\$this->".$columnArray[$index]['property']."->processCSV(\$csvProcessor1);\n";
			fwrite($generalfile1, $generalLine);
			$generalLine = "\t\t}\n";
			fwrite($generalfile1, $generalLine);
		} //end foreach
		//End of checking with referencing Objects
		// check with type = listobject 
		$indices = Tools::getIndicesOfColumnType($columnArray, "listobject");
		foreach ($indices as $index)	{
			$property = $columnArray[$index]['property'];
			$object = $columnArray[$index]['object'];
			$generalLine = "\t\tif (! is_null(\$this->".$property."))	{\n";
			fwrite($generalfile1, $generalLine);
			$generalLine = "\t\t\tforeach (\$this->".$property." as \$".$property."SingleObject)	{\n";
			fwrite($generalfile1, $generalLine);
			$generalLine = "\t\t\t\t\$csvProcessor1=\$".$property."SingleObject->processCSV(\$csvProcessor1);\n";
			fwrite($generalfile1, $generalLine);
			$generalLine = "\t\t\t}\n";
			fwrite($generalfile1, $generalLine);
			$generalLine = "\t\t}\n";
			fwrite($generalfile1, $generalLine);
		} //end for
		// End with check of type = listobject
		$generalLine = "\t\treturn \$csvProcessor1;\n";
		fwrite($generalfile1, $generalLine);
		$generalLine = "\t}\n"; 
		fwrite($generalfile1, $generalLine);
		//End of CSV Processor
		//Our Source here is the columnArray;
		for ($i=0; $i < sizeof($columnArray); $i++)	{
			$column = $columnArray[$i]['column'];
			$type = $columnArray[$i]['type'];
			$property = $columnArray[$i]['property'];
			$role = $columnArray[$i]['role'];
			$object = $columnArray[$i]['object'];
			//special for listobject type 
			//Property Assembling 
			$propertyLine = "\tprivate \$".$property.";\n";
			fwrite($propertyfile1, $propertyLine);
			//Now Dealing With Settler And Getter Method 
			//Begin with getter
			//Special getId function, this should be in every Object 
			if (strtolower($role) == "primary")	{
				$methodLine = "\tpublic function getId() {\n";
				fwrite($methodfile1, $methodLine);
				$methodLine = "\t\treturn \$this->".$property.";\n";
				fwrite($methodfile1, $methodLine);
				$methodLine = "\t}\n";
				fwrite($methodfile1, $methodLine);
			}
			//End of Special getId function
			$functionname = "get";
			if ($type == "boolean") $functionname="is";
			$functionname = $functionname.Tools::capitalizeFirstLetter($property);
			$methodLine = "\tpublic function ".$functionname."() {\n";
			fwrite($methodfile1, $methodLine);
			$methodLine = "\t\treturn \$this->".$property.";\n";
			fwrite($methodfile1, $methodLine);
			$methodLine = "\t}\n";
			fwrite($methodfile1, $methodLine);
			//Begin with settler
			if (strtolower($role) != "primary")	{
				$functionname = "set".Tools::capitalizeFirstLetter($property);
				$methodLine = "\tpublic function ".$functionname."(\$".$column.") {\n";
				fwrite($methodfile1, $methodLine);
				$methodLine = "\t\tif (! is_null(\$".$column."))	{\n";
				fwrite($methodfile1, $methodLine);
				fwrite($setmefile1, $methodLine);
				$methodLine = "\t\t\t\$this->addToUpdateList(\"".$column."\", \$".$column.");\n";
				fwrite($methodfile1, $methodLine);
				if (strtolower($type) == "text")	{
					$methodLine = "\t\t\t\$this->".$property." = \$".$column.";\n";
					fwrite($methodfile1, $methodLine);
					fwrite($setmefile1, $methodLine);
				} else if (strtolower($type) == "list")	{
					$methodLine = "\t\t\t\$this->".$property." = explode(\",\", \$".$column.");\n";
					fwrite($methodfile1, $methodLine);
					fwrite($setmefile1, $methodLine);
				} else if (strtolower($type) == "boolean")	{
					$methodLine = "\t\t\t\$this->".$property." = (\$".$column." == \"1\");\n";
					fwrite($methodfile1, $methodLine);
					fwrite($setmefile1, $methodLine);
				} else if (strtolower($type) == "listobject")	{
					//Note the argument is a comma separated of ids
					//Add only those whose references are valid objects
					$methodLine="\t\t\t\$this->".$property." = array();\n";
					fwrite($methodfile1, $methodLine);
					fwrite($setmefile1, $methodLine);
					$methodLine="\t\t\t\$idListArr = explode(\",\", \$".$column.");\n";
					fwrite($methodfile1, $methodLine);
					fwrite($setmefile1, $methodLine);
					$methodLine="\t\t\tforeach (\$idListArr as \$id)	{\n";
					fwrite($methodfile1, $methodLine);
					fwrite($setmefile1, $methodLine);
					$methodLine="\t\t\t\t\$id=trim(\$id);\n";
					fwrite($methodfile1, $methodLine);
					fwrite($setmefile1, $methodLine);
					$methodLine="\t\t\t\t\$temp=null;\n";
					fwrite($methodfile1, $methodLine);
					fwrite($setmefile1, $methodLine);
					$methodLine="\t\t\t\ttry {\n";
					fwrite($methodfile1, $methodLine);
					fwrite($setmefile1, $methodLine);
					$methodLine="\t\t\t\t\t\$temp=new ".Tools::capitalizeFirstLetter($object)."(\$this->database, \$id, \$this->conn);\n";
					fwrite($methodfile1, $methodLine);
					fwrite($setmefile1, $methodLine);
					$methodLine="\t\t\t\t\t\$this->".$property."[sizeof(\$this->".$property.")] = \$temp;\n";
					fwrite($methodfile1, $methodLine);
					fwrite($setmefile1, $methodLine);
					$methodLine="\t\t\t\t} catch (Exception \$e)	{\n";
					fwrite($methodfile1, $methodLine);
					fwrite($setmefile1, $methodLine);
					$methodLine="\t\t\t\t\t/*Do Nothing*/\n";
					fwrite($methodfile1, $methodLine);
					fwrite($setmefile1, $methodLine);
					$methodLine="\t\t\t\t}\n";
					fwrite($methodfile1, $methodLine);
					fwrite($setmefile1, $methodLine);
					$methodLine="\t\t\t}\n";
					fwrite($methodfile1, $methodLine);
					fwrite($setmefile1, $methodLine);
				} else if (strtolower($type) == "object")	{
					$methodLine = "\t\t\t\$this->".$property." = null;\n";
					fwrite($methodfile1, $methodLine);
					fwrite($setmefile1, $methodLine);
					$methodLine = "\t\t\ttry	{\n";
					fwrite($methodfile1, $methodLine);
					fwrite($setmefile1, $methodLine);
					$methodLine = "\t\t\t\t\$this->".$property." = new ".Tools::capitalizeFirstLetter($object)."(\$this->database, \$".$column.", \$this->conn);\n";
					fwrite($methodfile1, $methodLine);
					fwrite($setmefile1, $methodLine);
					$methodLine = "\t\t\t} catch (Exception \$e)	{\n";
					fwrite($methodfile1, $methodLine);
					fwrite($setmefile1, $methodLine);
					$methodLine = "\t\t\t\t\$this->".$property." = null;\n";
					fwrite($methodfile1, $methodLine);
					fwrite($setmefile1, $methodLine);
					$methodLine = "\t\t\t}\n";
					fwrite($methodfile1, $methodLine);
					fwrite($setmefile1, $methodLine);
				} 
				$methodLine = "\t\t}\n";
				fwrite($methodfile1, $methodLine);
				fwrite($setmefile1, $methodLine);
				$methodLine = "\t}\n";
				fwrite($methodfile1, $methodLine);
			}
		} //end for
		//Adding more data to the properyfile
		$propertyLine = "\tprivate \$database;\n";
		fwrite($propertyfile1, $propertyLine);
		$propertyLine = "\tprivate \$conn;\n";
		fwrite($propertyfile1, $propertyLine);
		//Now Adding custom comments line 
		$staticLine = "\t/* BEGIN::   You should Add your custom made static methods from this point */\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine = "\t/* END  ::   Your custom made static methods should be above this line      */\n";
		fwrite($propertyfile1, $staticLine);
		//Now adding a static function for availableViewable 
		$staticLine = "\tpublic final static function getAvailableViewableColumns()	{\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine = "\t\t/*format of data @classname/propertyname,propertytype,namespaceTag;propertyname,propertytype,namespaceTag;*/\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine = "\t\t\$lineToView = ".Tools::getAvailableViewableColumns($classname, $columnArray, $namespaceTag).";\n";
		fwrite($propertyfile1, $staticLine);
		//Now a bit of Mathematics to include linked Objects 
		$linkedObjects = Tools::getListOfObjectsIAmLinkedTo($classname, $columnArray);
		foreach ($linkedObjects as $lnObj)	{
			$staticLine="\t\t\$lineToView .= ".Tools::capitalizeFirstLetter($lnObj)."::getAvailableViewableColumns();\n";
			fwrite($propertyfile1, $staticLine);
		}//end foreach
		$staticLine = "\t\treturn \$lineToView;\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine = "\t}\n";
		fwrite($propertyfile1, $staticLine);
		//Now get the data folder path, data/foldername with name of a class
		$contextname=strtolower($classname);
		$staticLine="\tpublic final static function getDataFolder()	{\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine="\t\treturn \"".$contextname."\";\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine="\t}\n";
		fwrite($propertyfile1, $staticLine);
		//Now get a context name "manageclassname"
		$contextname=strtolower("manage".$classname);
		$staticLine="\tpublic final static function getClassContextName()	{\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine="\t\treturn \"".$contextname."\";\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine="\t}\n";
		fwrite($propertyfile1, $staticLine);
		//Now getting the Unique id, if only one record allowed
		//Useful at startup, you can have a dummy of only one record
		//And hold its reference 
		$primaryColumnIndex=Tools::getIndexOfPrimaryColumn($columnArray);
		$staticLine="\tpublic final static function getUnique".Tools::capitalizeFirstLetter($columnArray[$primaryColumnIndex]['property'])."(\$database, \$conn)	{\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine="\t\t\$query=\"SELECT ".$columnArray[$primaryColumnIndex]['column']." FROM ".$tablename."\";\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine="\t\t\$result=mysql_db_query(\$database, \$query, \$conn) or Object::shootException('".$classname.", Static Get Unique Row Query Failed');\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine="\t\tif (mysql_num_rows(\$result) != 1)  Object::shootException('".$classname.", None or Duplicate Row/Multiple Rows Found');\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine="\t\tlist(\$".$columnArray[$primaryColumnIndex]['property'].")=mysql_fetch_row(\$result);\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine="\t\treturn \$".$columnArray[$primaryColumnIndex]['property'].";\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine="\t}\n";
		fwrite($propertyfile1, $staticLine);
		//Now adding a static function for loadAllData
		$primaryColumnIndex = Tools::getIndexOfPrimaryColumn($columnArray);
		$valueColumnIndex = Tools::getIndexOfValueColumn($columnArray);
		if ($valueColumnIndex != -1)	{
			$staticLine = "\tpublic final static function loadAllData(\$database, \$conn)	{\n";
			fwrite($propertyfile1, $staticLine);
			$staticLine = "\t\t\$query=\"SELECT ".$columnArray[$primaryColumnIndex]['column'].", ".$columnArray[$valueColumnIndex]['column']." FROM ".$tablename."\";\n";
			fwrite($propertyfile1, $staticLine);
			$staticLine="\t\t\$result=mysql_db_query(\$database, \$query, \$conn) or Object::shootException('".$classname.", Static Load All Data Failed');\n";
			fwrite($propertyfile1, $staticLine);
			$staticLine="\t\t\$list=array();\n";
			fwrite($propertyfile1, $staticLine);
			$staticLine="\t\twhile(list(\$__id, \$__val)=mysql_fetch_row(\$result))	{\n";
			fwrite($propertyfile1, $staticLine);
			$staticLine="\t\t\t\$listsize=sizeof(\$list);\n";
			fwrite($propertyfile1, $staticLine);
			$staticLine="\t\t\t\$list[\$listsize]=array();\n";
			fwrite($propertyfile1, $staticLine);
			$staticLine="\t\t\t\$list[\$listsize]['id']=\$__id;\n";
			fwrite($propertyfile1, $staticLine);
			$staticLine="\t\t\t\$list[\$listsize]['val']=\$__val;\n";
			fwrite($propertyfile1, $staticLine);
			$staticLine="\t\t}\n";
			fwrite($propertyfile1, $staticLine);
			$staticLine="\t\treturn \$list;\n";
			fwrite($propertyfile1, $staticLine);
			$staticLine = "\t}\n";
			fwrite($propertyfile1, $staticLine);
		} //end-if-value-column-index
		//Now adding a static function for add 
		$staticLine = "\tpublic final static function add(\$database, \$conn, ".Tools::getListOfAllNonPrimaryColumnsWithDollarSign($columnArray).")	{\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine = "\t\t\$query=\"INSERT INTO ".$tablename." (".Tools::getListOfAllNonPrimaryColumns($columnArray).") VALUES(".Tools::getListOfAllColumnsWithDollarAndApostropheSign($columnArray).")\";\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine="\t\t\$result=mysql_db_query(\$database, \$query, \$conn) or Object::shootException('".$classname.", Static Addition Failed');\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine="\t\t\$query=\"SELECT LAST_INSERT_ID()\";\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine="\t\t\$result=mysql_db_query(\$database, \$query, \$conn) or Object::shootException('".$classname.", Could not Extract Last Inserted ID');\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine="\t\tif (mysql_num_rows(\$result) != 1) Object::shootException('".$classname.", Last Inserted ID is Not Unique');\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine="\t\tlist(\$lastId)=mysql_fetch_row(\$result);\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine="\t\treturn \$lastId;\n";
		fwrite($propertyfile1, $staticLine);
		$staticLine = "\t}\n";
		fwrite($propertyfile1, $staticLine);
		$setLine = "\t}\n";
		fwrite($setmefile1, $setLine);
		fclose($generalfile1);
		fclose($clonefile1);
		fclose($setmefile1);
		fclose($methodfile1);
		fclose($propertyfile1);
		//Now merge to permanent final file 
		//Open the files now for reading 
		$file1 = fopen($propertyfile, "r") or die("Could not open $propertyfile for READING");
		$file2 = fopen($methodfile, "r") or die("Could not open $methodfile for READING");
		$file3 = fopen($generalfile, "r") or die("Could not open $generalfile for READING");
		$file4 = fopen($setmefile, "r") or die("Could not open $setmefile for READING");
		$file5 = fopen($clonefile, "r") or die("Could not open $clonefile for READING");
		
		$classLine = "class ".$classname." extends Object {\n";
		fwrite($finalfile1, $classLine);
		//Read one file after the other and write
		while (($line1 = fgets($file1)) !== false)	{
			//$classLine = $line1."\n";
			$classLine=$line1;
			fwrite($finalfile1, $classLine);
		} //end file1
		while (($line1 = fgets($file2)) !== false)	{
			//$classLine = $line1."\n";
			$classLine=$line1;
			fwrite($finalfile1, $classLine);
		} //end file2
		while (($line1 = fgets($file3)) !== false)	{
			//$classLine = $line1."\n";
			$classLine=$line1;
			fwrite($finalfile1, $classLine);
		} //end file3
		while (($line1 = fgets($file4)) !== false)	{
			//$classLine = $line1."\n";
			$classLine=$line1;
			fwrite($finalfile1, $classLine);
		} //end file4
		while (($line1 = fgets($file5)) !== false)	{
			//$classLine = $line1."\n";
			$classLine=$line1;
			fwrite($finalfile1, $classLine);
		} //end file5
		fclose($file5);
		fclose($file4);
		fclose($file3);
		fclose($file2);
		fclose($file1);
		$classLine = "}\n";
		fwrite($finalfile1, $classLine);
		//Add Now Lines to Registry 
		$registryLine="\t\t\$list[sizeof(\$list)] = \"".$classname."\";\n";
		fwrite($classRegistryFile1, $registryLine);
		//Write to array things which occurs onces in a class 
		$nextIndex = sizeof($listOfClassNames);
		$listOfClassNames[$nextIndex] = $classname;
		$listOfTableNames[$nextIndex] = $tablename;
		$listOfPrimaryKeys[$nextIndex] = $primaryKey['column'];
		//The Work has been finished Let us go to state 0 
		$state = 0;
	} //end of state 2
} //end while for sourcefile1
$registryLine="\t\treturn \$list;\n";
fwrite($classRegistryFile1, $registryLine);
$registryLine="\t}\n";
fwrite($classRegistryFile1, $registryLine);
$registryLine="\tpublic final static function isClassRegistered(\$__classname)	{\n";
fwrite($classRegistryFile1, $registryLine);
$registryLine="\t\t\$listOfRegisteredClasses = self::getAvailableClassList();\n";
fwrite($classRegistryFile1, $registryLine);
$registryLine="\t\treturn in_array(\$__classname, \$listOfRegisteredClasses);\n";
fwrite($classRegistryFile1, $registryLine);
$registryLine="\t}\n";
fwrite($classRegistryFile1, $registryLine);
//Add a getQueryText 
$registryLine="\tpublic final static function getQueryText(\$__classname)	{\n";
fwrite($classRegistryFile1, $registryLine);
$registryLine = "\t\t\$line = null;\n";
fwrite($classRegistryFile1, $registryLine);
for ($i=0; $i < sizeof($listOfClassNames); $i++)	{
	$class1 = $listOfClassNames[$i];
	$table1 = $listOfTableNames[$i];
	$id1 = $listOfPrimaryKeys[$i];
	if ($i == 0)	{
		$registryLine = "\t\tif (\$__classname == \"".$class1."\")	{\n";
		fwrite($classRegistryFile1, $registryLine);
		$registryLine = "\t\t\t\$line =  \"SELECT ".$id1." FROM ".$table1."\";\n";
		fwrite($classRegistryFile1, $registryLine);
		$registryLine = "\t\t}\n";
		fwrite($classRegistryFile1, $registryLine);
	} else	{
		$registryLine = "\t\telse if (\$__classname == \"".$class1."\")	{\n";
		fwrite($classRegistryFile1, $registryLine);
		$registryLine = "\t\t\t\$line =  \"SELECT ".$id1." FROM ".$table1."\";\n";
		fwrite($classRegistryFile1, $registryLine);
		$registryLine = "\t\t}\n";
		fwrite($classRegistryFile1, $registryLine);
	} //end if else ladder
} //end for
$registryLine = "\t\treturn \$line;\n";
fwrite($classRegistryFile1, $registryLine);
$registryLine = "\t}\n";
fwrite($classRegistryFile1, $registryLine);
//Adding a getObjectReference method
$registryLine="\tpublic final static function getObjectReference(\$__database, \$__conn, \$__classname, \$__id)	{\n";
fwrite($classRegistryFile1, $registryLine);
$registryLine = "\t\t\$refobj = null;\n";
fwrite($classRegistryFile1, $registryLine);
for ($i=0; $i < sizeof($listOfClassNames); $i++)	{
	$class1 = $listOfClassNames[$i];
	$table1 = $listOfTableNames[$i];
	$id1 = $listOfPrimaryKeys[$i];
	if ($i == 0)	{
		$registryLine = "\t\tif (\$__classname == \"".$class1."\")	{\n";
		fwrite($classRegistryFile1, $registryLine);
		$registryLine = "\t\t\t\$refobj = new ".$class1."(\$__database, \$__id, \$__conn) or Object::shootException('".$class1.", creation failed');\n";
		fwrite($classRegistryFile1, $registryLine);
		$registryLine = "\t\t}\n";
		fwrite($classRegistryFile1, $registryLine);
	} else	{
		$registryLine = "\t\telse if (\$__classname == \"".$class1."\")	{\n";
		fwrite($classRegistryFile1, $registryLine);
		$registryLine = "\t\t\t\$refobj = new ".$class1."(\$__database, \$__id, \$__conn) or Object::shootException('".$class1.", creation failed');\n";
		fwrite($classRegistryFile1, $registryLine);
		$registryLine = "\t\t}\n";
		fwrite($classRegistryFile1, $registryLine);
	} //end if else ladder
} //end for
$registryLine = "\t\treturn \$refobj;\n";
fwrite($classRegistryFile1, $registryLine);
$registryLine = "\t}\n";
fwrite($classRegistryFile1, $registryLine);
$registryLine="}\n";
fwrite($classRegistryFile1, $registryLine);
fclose($classRegistryFile1);
$file5 = fopen($classregistry, "r") or die("Unable to OPEN $classregistry for READING");
while (($line1 = fgets($file5)) !== false)	{
			//$classLine = $line1."\n";
	$classLine=$line1;
	fwrite($finalfile1, $classLine);
}
fclose($file5); //end of file 5
$classLine="?>\n";
fwrite($finalfile1, $classLine);
fclose($finalfile1);
fclose($sourcefile1);
//Temporary files need to be deleted 
unlink($propertyfile);
unlink($methodfile);
unlink($setmefile);
unlink($generalfile);
unlink($classregistry);
unlink($clonefile);
?>
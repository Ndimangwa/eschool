<?php 
abstract class Object	{
	/*
	RELEASED on 20th January, 2016
	It is a mandatory every object that fetches data from the database to inherit from this object, object with zero properties
	All methods of this object/class should be protected
	Designer: Ndimangwa Fadhili Ngoya
	Code Writer: Ndimangwa Fadhili Ngoya
	Phone: +255 787 101 808
	Box: P.O Box 7436, Moshi Tanzania
	Email: ndimangwa@gmail.com 
	
	zoomtong company limited
	*/
	static $defaultContextValue = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
	static $hashText="Developed by: Ndimangwa Fadhili Ngoya";
	static $iconPath="../sysimage/";
	static $xmlVersion="1.0";
	public final static function summarizeString($string, $summaryLength)	{
		$summaryLength = intval($summaryLength);
		if (strlen($string) > $summaryLength)	{
			//We need to process this string 
			$string = substr($string, 0, $summaryLength);
			$string .= "...";
		}
		return $string;
	}
	public final static function getPropertiesValueFromAnObjectProperty($properties, $propertyname)	{
		//if property is @field1=val1@field2=val2@field3=val3....@fieldn=valn@field3=val3i
		//and the propertyname=field3, arr[0]['propertyname'] = val3, arr[1]['propertyname'] =val3i
		$dataArray = array();
		$propArray = explode("@", $properties);
		foreach ($propArray as $aprop)	{
			//In the form of propertyname=fieldvalue 
			$keyValue = explode("=", $aprop);
			$key = $aprop[0];
			$value = $aprop[1];
			if ($key == $propertyname) {
				//$wordIndex = "'".$key."'";
				$wordIndex = $key;
				$dataArray[sizeof($dataArray)][$wordIndex] = $value;
			}
		}
		if (sizeof($dataArray) == 0) $dataArray=null;
		return $dataArray;
	}
	public final static function getSearchTextFromUserFieldData($controlIndexArray, $fieldNameArr, $opArr, $fieldValueArr)	{
		$lineToSearch = "";
		foreach ($controlIndexArray as $index)	{
			$fieldname = $fieldNameArr[$index];
			$op = $opArr[$index];
			$fieldvalue = $fieldValueArr[$index];
			$lineToSearch .= ";".$fieldname.$op.$fieldvalue;
		}
		$lineToSearch = substr($lineToSearch, 1);
		$lineToSearch=trim($lineToSearch);
		return $lineToSearch;
	}
	public final static function getLineToSortFromUserFieldData($fieldNameArr, $fieldTypeArr, $fieldValueArr)	{
		$lineToSort = "";
		//We need to use fieldValueArr as a control 
		foreach ($fieldValueArr as $i => $fieldvalue)	{
			$fieldname=$fieldNameArr[$i];
			$fieldtype=$fieldTypeArr[$i];
//fieldvalue might be an array too			
			if ($fieldtype == "list")	{
				foreach ($fieldvalue as $afieldvalue)	{
					if ($afieldvalue == "") $afieldvalue="emptydata";
					$lineToSort .= ";".$fieldname.",".$afieldvalue;
				}
			} else if ($fieldtype == "boolean")	{
				if ($fieldvalue == "_@32767@_") $fieldvalue="emptydata";
				$lineToSort .= ";".$fieldname.",".$fieldvalue;
			} else {
				//Default Text 
				if ($fieldvalue == "") $fieldvalue="emptydata";
				$lineToSort .= ";".$fieldname.",".$fieldvalue;
			}
		}
		//Remove the leading ;
		$lineToSort = substr($lineToSort, 1);
		$lineToSort=trim($lineToSort);
		return $lineToSort;
	}
	public final static function getUICSVDivSortable($lineToSort)	{
		//Format to expect
		//fieldname,fieldvalue;fieldname,fieldvalue;....
		//if fieldvalue == "emptydata" means hakuna data in the fieldvalue
		$uiDPanel="<div class='ui-sortable-container ui-sys-center-80-percent' data-control-index-prefix='controlIndex'><div>";
		$uiDPanel .= "<table class='ui-sys-display-table ui-sys-center-80-percent ui-sortable-table'><tr><th></th><th>Field Name</th><th>Op</th><th>Field Value</th></tr>";
		$trFieldArray = explode(";", $lineToSort);
		for ($i=0; $i<sizeof($trFieldArray); $i++)	{
			//fieldname,fieldtype,fieldvalue
			$cellValueArray = explode(",", $trFieldArray[$i]);
			$fieldname=$cellValueArray[0];
			$fieldvalue=$cellValueArray[1];
			$serialNumber = $i + 1;
			$op = "="; //Default There is data 
			if ($fieldvalue == "emptydata") { $op=""; $fieldvalue=""; }
			$uiDPanel .= "<tr class='ui-sortable-row'><td><button class='ui-sortable-button'>".$serialNumber."</button><input type='hidden' class='sortable-control' name='controlIndex[".$i."]' value='".$i."'/></td><td><input type='hidden' name='fieldname[".$i."]' value='".$fieldname."'/>".$fieldname."</td><td><input type='hidden' name='op[".$i."]' value='".$op."'/>".$op."</td><td><input type='hidden' name='fieldvalue[".$i."]' value='".$fieldvalue."'/>".$fieldvalue."</td></tr>";
		}//end-for
		$uiDPanel .= "</table>";
		$uiDPanel .= "</div><div class='ui-sortable-controls ui-sys-search-controls ui-sys-right'>";
		$uiDPanel .= "<a class='ui-sys-move-up ui-sys-hidden' title='Move Selected Item Up'><img alt='DAT' src='".self::$iconPath."up.png'/></a>";
		$uiDPanel .= "<a class='ui-sys-move-down ui-sys-hidden' title='Move Selected Item Down'><img alt='DAT' src='".self::$iconPath."down.png'/></a>";
		$uiDPanel .= "<a class='ui-sys-clear ui-sys-hidden' title='Clear Selection'><img alt='DAT' src='".self::$iconPath."clear.png'/></a>";
		$uiDPanel .= "</div></div>";
		return $uiDPanel;
	}
	public final static function getUICSVTables($lineToView)	{
		//Step One, separate at class boundary
		$exprDL255Name="^(\w|\W){0,255}$";
		$msgDL255Name="0 - 255 characters";
		//Remove the leading @
		$lineToView = substr($lineToView, 1);
		$lineToView=trim($lineToView);
		$lineToViewPerClassArray = explode("@", $lineToView);
		$uiDPanel="";
		$controlVariableIndex = 0; //global Increment
		//die($lineToView);
		foreach ($lineToViewPerClassArray as $lineclass)	{
			$tempArr = explode("/", $lineclass);
			$classname = $tempArr[0]; //classname
			$uiDPanel .="<table class='ui-sys-display-table ui-sys-center-80-percent'>";
			$uiDPanel .= "<tr><th colspan='4'><b>".$classname."</b></th></tr>";
			$uiDPanel .= "<tr><th></th><th></th><th>Field Name</th><th>Field Value</th></tr>";
			$completeFieldArr = explode(";", $tempArr[1]); 
			$counterIndex=0;
			foreach ($completeFieldArr as $aCompleteField)	{
				$counterIndex++;
				//aCompleteField= fieldname,text 
				$tempArr = explode(",", $aCompleteField);
				$fieldname = $tempArr[0];
				$fieldtype = $tempArr[1]; //type=boolean,text,list 
				$namespaceTag = $tempArr[2];
				$bgcolor="";
				if (($counterIndex % 2) == 1) $bgcolor="background-color: #bcbcbc;";
				$uiDPanel .= "<tr style='".$bgcolor."' class='field-container'><td>".$counterIndex."</td><td><input title='Include OR Exclue ".$fieldname."' type='checkbox' class='field-checkbox' value='0'/></td><td>".$fieldname."</td><td class='ui-sys-list-parent' data-control-to-add='text' data-closest-parent-of-collection='td' data-error-control='perror' data-image-path='".self::$iconPath."' data-prefix='fieldvalue[".$controlVariableIndex."]' data-message-length='You have exceeded number of ".$fieldname."' data-message-error='".$fieldname." at' validate_expression='".$exprDL255Name."' validate_message='".$msgDL255Name."' validate_length='255'>";
				$uiDPanel .= "<input type='hidden' name='fieldname[".$controlVariableIndex."]' value='".$fieldname."'/>";
				$uiDPanel .= "<input type='hidden' name='fieldtype[".$controlVariableIndex."]' value='".$fieldtype."'/>";
				$uiDPanel .= "<input type='hidden' name='namespaceTag[".$controlVariableIndex."]' value='".$namespaceTag."'/>";
				if ($fieldtype=="boolean")	{
					$uiDPanel .= "<select name='fieldvalue[".$controlVariableIndex."]' class='field-capturedata' validate_control='select' validate_expression='select' validate_message='Kindly Select ".$fieldname."' disabled>";
					$uiDPanel .= "<option value='_@32767@_'>--select--</option>";
					$uiDPanel .= "<option value='1'>true</option>";
					$uiDPanel .= "<option value='0'>false</option>";
					$uiDPanel .= "</select>";
				} else if ($fieldtype=="list")	{
					$uiDPanel .= "<div class='fieldvalue_".$controlVariableIndex."_ ui-sys-list-edit' data-index='0'>";
					$uiDPanel .= "<input type='text' name='fieldvalue[".$controlVariableIndex."][0]' class='field-capturedata' size='32' required pattern='".$exprDL255Name."' validate='true' validate_control='text' validate_expression='".$exprDL255Name."' validate_message='".$fieldname." at row 0: ".$msgDL255Name."' disabled/>";
					$uiDPanel .= "<a class='ui-sys-control-icon ui-sys-hidden' title='Delete ".$fieldname."'><img alt='DAT' src='".self::$iconPath."buttondelete.png'/></a>";
					$uiDPanel .= "</div>";
					$uiDPanel .= "<div class='ui-sys-list-add'><a class='ui-sys-control-icon ui-sys-hidden' title='Add a New ".$fieldname."'><img alt='DAT' src='".self::$iconPath."buttonadd.png'/></a></div>";
				} else {
					//default type=text
					$uiDPanel .= "<input type='text' name='fieldvalue[".$controlVariableIndex."]' class='field-capturedata' size='32' required pattern='".$exprDL255Name."' validate='true' validate_control='text' validate_expression='".$exprDL255Name."' validate_message='".$fieldname." : ".$msgDL255Name."' disabled/>";
				}
				$uiDPanel .= "</td></tr>";
				$controlVariableIndex++; //index of all variables
			}
			$uiDPanel .= "</table>";
		}
		return $uiDPanel;
	}
	public final static function staticSearchAlgorithm($sub, $string1)	{
		/* Search existence of $sub in $string1 
		@username=2@sasa=7
		*/
		$searchArray = explode(";", $sub);
		$found = true;
		for ($i=0; $i < sizeof($searchArray); $i++)	{
			$blnNOTCorrection=true;
			/* Check to see if this element has a NOT ! */
			if (strpos($searchArray[$i], "!") === 0)	{
				/*NOT found in 1st location*/
				$blnNOTCorrection = false;
				$searchArray[$i]=substr($searchArray[$i], 1);
			}
			$pos = stripos($string1, $searchArray[$i]);
			if ($pos === false) $bln = false;
			else $bln = true;
			$found = $found && (($bln && $blnNOTCorrection) || (! ($bln || $blnNOTCorrection)));
			if (! $found) break;
		}
		return $found;
	}
	public final static function getUniqueArrayFromArray($listInArray)	{
		$uniqueArray = array();
		foreach ($listInArray as $alist)	{
			//Add Only If Not Exist in Array 
			if (! in_array($alist, $uniqueArray))	{
				$uniqueArray[sizeof($uniqueArray)] = $alist;
			}//end-if
		}
		return $uniqueArray;
	}
	public final static function getCommaSeparatedListFromObjectArray($listOfObjects)	{
		//We Assume these Object supports a getId function 
		$list = "";
		$counter=0;
		foreach ($listOfObjects as $item1)	{
			if ($counter == 0)	$list = $item1->getId();
			else $list = $list.",".$item1->getId();
			$counter++;
		}
		return $list;
	}
	public final static function getCommaSeparatedListFromArray($listInArrayFormat)	{
		$list = "";
		$counter = 0;
		foreach ($listInArrayFormat as $item)	{
			if ($counter == 0)	$list = $item;
			else $list = $list.",".$item;
			$counter++;
		}
		return $list;
	}
	public final static function getAdvancedPropertiesFromArray($property, $dataArray, $namespaceTag)	{
		$list = "";
		$count = sizeof($dataArray); // number of array elements
		for ($i = 0; $i < $count; $i++)	{
			$dataValue = str_replace("@", "_kdAt_", str_replace("=", "_kdEq_", $dataArray[$i]));
			//dataArray index 
		/*	for ($j = 0; $j < $count; $j++)	{
				//data count 
				if (($i + $j) == 0)	{
					$list .= "".$property.$j."=".$dataValue;
				} else	{
					$list .= "@".$property.$j."=".$dataValue;
				}
			} //end for inner */
			if ($i==0)	{
				$list .= "".$namespaceTag.".".$property."=".$dataValue;
			} else {
				$list .= "@".$namespaceTag.".".$property."=".$dataValue;
			}
		} //end for outer
		return $list;
	}
	public final static function getPropertiesFromArray($property, $dataArray)	{
		$list = "";
		for ($i = 0; $i < sizeof($dataArray); $i++)	{
			if ($i ==	0)	{
				$list .= "".$property."=".$dataArray[$i];
			} else	{
				$list .= "@".$property."=".$dataArray[$i];
			}
		} //end for
		return $list;
	}
	public final static function arrayToList($arr)	{
		$list = "";
		for ($i=0; $i < sizeof($arr); $i++)	{
			if ($i == 0)	{
				$list=$arr[$i];
			} else	{
				$list .= ", ".$arr[$i];
			}
		}
		return $list;
	}
	public final static function shootException($message)	{
		throw new Exception($message);
	}
	private $update = array();	/*Used to keep update list */
	protected function throwMe($message)	{
		throw new Exception($message);
	}
	protected function addToUpdateList($key, $value)	{
		$this->update[$key] = $value;
	}
	protected function getUpdateListLength()	{ return sizeof($this->update); }
	public function getUpdateList()	{
		/* return where criteria 
		Caution, ID of objects are not allowed to be updated
		*/
		$setList="";
		$counter = 0;
		foreach ($this->update as $key => $value)	{
			if ($counter == 0)	{
				$setList = $key."='".$value."'";
			} else {
				$setList = $setList.", ".$key."='".$value."'";
			}
			$counter++;
		}
		return $setList;
	}
	protected function searchAlgorithim($sub, $string1)	{
		/* Search existence of $sub in $string1 
		@username=2@sasa=7
		*/
		$searchArray = explode(";", $sub);
		$found = true;
		for ($i=0; $i < sizeof($searchArray); $i++)	{
			$blnNOTCorrection=true;
			/* Check to see if this element has a NOT ! */
			if (strpos($searchArray[$i], "!") === 0)	{
				/*NOT found in 1st location*/
				$blnNOTCorrection = false;
				$searchArray[$i]=substr($searchArray[$i], 1);
			}
			$pos = stripos($string1, $searchArray[$i]);
			if ($pos === false) $bln = false;
			else $bln = true;
			$found = $found && (($bln && $blnNOTCorrection) || (! ($bln || $blnNOTCorrection)));
			if (! $found) break;
		}
		return $found;
	}
	private function getPowerOfTwoFor($pos)	{
		//This is a 16 bit 
		//if greater than 15 return, accept 0 .. 15 
		$pos = intval("".$pos);
		if (($pos < 0) || ($pos > 15)) return 0;
		$powerOfTwo = 1;
		for ($i = 0; $i < $pos; $i++)	{
			$powerOfTwo = $powerOfTwo * 2;
		}
		return $powerOfTwo;
	}
	public function setFlagAt($pos)	{
		$powerOfTwo = $this->getPowerOfTwoFor($pos);
		if ($powerOfTwo != 0)	{
			$flagValue = intval("".$this->getFlags());
			$flagValue = $flagValue | $powerOfTwo;
			$this->setFlags($flagValue);
		}
	}
	public function resetFlagAt($pos)	{
		$powerOfTwo = $this->getPowerOfTwoFor($pos);
		if ($powerOfTwo != 0)	{
			$flagValue = intval("".$this->getFlags());
			$powerOfTwo = 65535 - $powerOfTwo; //This is a 16bit operation 
			$flagValue = $flagValue & $powerOfTwo;
			$this->setFlags($flagValue);
		}
	}
	public function isFlagSetAt($pos)	{
		$powerOfTwo = $this->getPowerOfTwoFor($pos);
		$blnSet = false;
		if ($powerOfTwo != 0)	{
			$flagValue = intval("".$this->getFlags());
			$blnSet = (($flagValue & $powerOfTwo) == $powerOfTwo);
		} 
		return $blnSet;
	}
	abstract protected function setMe($database, $id, $conn);
	abstract public function cloneMe($updateDataArray);
	abstract public function reload();
	abstract public function getClassName();
	abstract protected function getAdvancedProperties();
	abstract protected function getProperties();
	abstract public function debug();
	abstract public function searchMatrix($matrix);
	abstract public function processCSV($csvProcessor1);
	abstract public function commitUpdate();
	abstract public function commitDelete();
}
class Intent {
	private $dataHolder;
	/*--name--status--extra--extra--*/
	public function __construct($name)	{
		$this->dataHolder = array();
		$this->dataHolder['name'] = $name;
		$this->dataHolder['status'] = false; //default to false
	}
	public function putExtra($fieldname, $fieldvalue)	{
		$this->dataHolder[$fieldname] = $fieldvalue;
	}
	public function getExtra($fieldname)	{
		$val = null;
		if (isset($this->dataHolder[$fieldname]))	{
			$val = $this->dataHolder[$fieldname];
		}
		return $val;
	}
	public function isStatusSet()	{
		return $this->dataHolder['status'];
	}
}
class DisplayList {
	//Used to Hold Intents 
	private $intentHolder;
	public function __construct()	{
		$this->intentHolder = array();
	}
	public function add($intent1)	{
		$this->intentHolder[sizeof($this->intentHolder)] = $intent1;
	}
	public function getIntentAt($index)	{
		$intent1 = null;
		if ($index < sizeof($this->intentHolder))	{
			$intent1 = $this->intentHolder[$index];
		}
		return $intent1;
	}
	public function getAllIntents()	{
		$intentList = $this->intentHolder;
		if (sizeof($this->intentHolder) == 0) $intentList = null;
		return $intentList;
	}
	public function getPositiveIntents()	{
		$intentList = array();
		foreach ($this->intentHolder as $anIntent1)	{
			if ($anIntent1->getExtra('status'))	{
				$intentHolder[sizeof($intentHolder)] = $anIntent1;
			}
		}
		if (sizeof($intentList) == 0) $intentList = null;
		return $intentList;
	}
	public function getNegativeIntents()	{
		$intentList = array();
		foreach ($this->intentHolder as $anIntent1)	{
			if (! $anIntent1->getExtra('status'))	{
				$intentHolder[sizeof($intentHolder)] = $anIntent1;
			}
		}
		if (sizeof($intentList) == 0) $intentList = null;
		return $intentList;
	}
	public function setAllStatus($bln)	{
		foreach ($this->intentHolder as $anIntent1)	{
			$anIntent1->putExtra('status', $bln);
		}
	}
}
class SearchMatrix	{
	//This is a New type of search
	/*
	Each row corresponds to a search keyword
	Columns are as follows
	 col 0 : signs, if words contains NOT or otherwise
	 col 1: reserved for future
	 col 2: reserved for future
	*/
	private $keywords;
	private $keyvalue; //search of the form key=value
	private $matrix;
	private $searchText;
	private $filepath;
	private function init($searchText)	{
		$this->searchText = $searchText;
		// Split original words
		$words = explode(";", $searchText);
		$this->matrix = array(); 
		$this->keyvalue = array();
		$this->keywords = array();
		// we need to check for presence of ! 
		foreach ($words as $word)	{
			$sign = true;
			if (strpos($word, "!") === 0)	{
				//! found
				$word=substr($word, 1);
				$sign = false;
			}
			$wordIndex = "'".$word."'";
			$this->matrix[$wordIndex] = array();
			$this->matrix[$wordIndex][sizeof($this->matrix[$wordIndex])] = $sign; // col 0 sorted
			$this->matrix[$wordIndex][sizeof($this->matrix[$wordIndex])] = true; // col 1 reserved
			$this->matrix[$wordIndex][sizeof($this->matrix[$wordIndex])] = true; // col 2 reserved
			$this->keywords[sizeof($this->keywords)] = $word;
			// Now is keyvalue turn
			$kvs = explode("=", $word);
			if (sizeof($kvs) == 2)	{
				$index = sizeof($this->keyvalue);
				$this->keyvalue[$index] = array();
				$this->keyvalue[$index]['key']=$kvs[0];
				$this->keyvalue[$index]['value']=$kvs[1];
				$this->keyvalue[$index]['keyvalue']=$word;
			}
		}
	}
	public function evaluateResult()	{
		$bln = true;
		foreach ($this->matrix as $amatrix)	{
			$invertedAnd = true;
			$orGate1 = false;
			$sign = $amatrix[0];
			for ($i=3; $i< sizeof($amatrix); $i++)	{
				// start fro pos = 3 onward
				$invertedAnd = $invertedAnd && (! $amatrix[$i]);
				$orGate1 = $orGate1 || $amatrix[$i];
			}
			$lineLogic = (! $sign) && $invertedAnd;
			$lineLogic = $lineLogic || ($sign && $orGate1);
			// combine to overall product
			$bln = $bln && $lineLogic;
			if (! $bln) break;
		}
		return $bln;
	}
	public function setFilepath($filepath)	{
		$this->filepath = $filepath;
	}
	public function getFilepath()	{ return $this->filepath; }
	public function searchText($string1)	{
		foreach ($this->keywords as $aword)	{
			$pos = stripos($string1, $aword);
			if ($pos === false)	{
				$this->append($aword, false);
			} else	{
				$this->append($aword, true);
			}
		}
	}
	public function searchFile($filename)	{
		$path = $this->filepath;
		$filename = $path.$filename;
		if (! file_exists($filename)) return;
		$doc = new DOMDocument(Object::$xmlVersion);
		$doc->load($filename);
		foreach ($this->keyvalue as $kv)	{
			$key = $kv['key'];
			$value = $kv['value'];
			$word = $kv['keyvalue'];
			$node1 = $doc->getElementsByTagName($key);
			if (is_null($node1)) continue;
			$nodeValue = $node1->item(0)->nodeValue;
			if (is_null($nodeValue)) continue;
			if (strtolower($nodeValue) == strtolower($value))	{
				$this->append($word, true);
			} else	{
				$this->append($word, false);
			}
		}
	}
	public function append($keyword, $blnFound)	{
		$keywordIndex = "'".$keyword."'";
		$this->matrix[$keywordIndex][sizeof($this->matrix[$keywordIndex])] = $blnFound; //col > 2
	}
	public function reset()	{ $this->init($this->searchText); }
	public function getKeywords()	{ return $this->keywords; }
	public function getMatrix()	{ return $this->matrix; }
	public function __construct($searchText)	{
		$this->init($searchText);
	}
}
class CSVProcessor	{
	private $searchTextDataArray;
	private $searchTextSignArray;
	private $objectPropertiesDataArray;
	private $filehandler;
	public function evaluateResult()	{
		$bln = true;
		//var_dump($this->searchTextDataArray);
		//var_dump($this->searchTextSignArray);
		//var_dump($this->objectPropertiesDataArray);
		foreach ($this->searchTextDataArray as $index => $val)	{
			//
			//$wordIndex = "'".$index."'";
			$wordIndex = $index;
			//XOR
			$tempBln = ! $this->searchTextSignArray[$wordIndex] && ($this->objectPropertiesDataArray[$wordIndex] == "_@32767@_") || $this->searchTextSignArray[$wordIndex] && ($this->objectPropertiesDataArray[$wordIndex] != "_@32767@_"); 
			$bln = $bln && $tempBln;
			if (! $bln) break;
		}
		//Hello Now write to file this is a data, You need to use objectPropertiesDataArray if bln=true 
		if ($bln)	{
			//ctd The objectPropertiesDataArray contains data for writing 
			//Nb Arrays Are Organised in the same order the user specified
			$lineToWrite = "";
			foreach ($this->objectPropertiesDataArray as $adata)	{
				$adata = str_replace(",", " ", $adata); //Remove commas in the data
				$lineToWrite .= ",".$adata;
			}
			//Remove the leading comma(,)
			$lineToWrite = substr($lineToWrite, 1)."\n";
			fwrite($this->filehandler, $lineToWrite) or Object::shootException("CSVProcessor -> Could not write $lineToWrite");
		}
		return $bln;
	}
	public function clearData()	{
		//Just Initialize to null
		foreach ($this->objectPropertiesDataArray as $index => $val)	{
			//$wordIndex = "'".$index."'"; 
			$wordIndex = $index;
			$this->objectPropertiesDataArray[$wordIndex] = "_@32767@_";
		}
	}
	public function reload()	{
		$this->clearData();
	}
	private function isColumnInSearchTextData($columnname)	{
		//$wordIndex = "'".$columnname."'";
		$wordIndex=$columnname;
		return isset($this->searchTextDataArray[$wordIndex]);
	}
	public function appendCSVData($properties)	{
		//Properties should be loaded for each found objects
		//The keypoint here is
		// Any objectPropertiesDataArray which has any value apart from _@32767@_ is suitable for writing
		// If proved the sign is fine
		$tempArr = explode("@", $properties);
		foreach ($tempArr as $aprop)	{
			//field=value 
			$keyAndValue = explode("=", $aprop);
			$key = trim($keyAndValue[0]);
			$value = "";
			if (isset($keyAndValue[1]))	{
				$value = trim($keyAndValue[1]);
				//This value from the properties might contain _kdAt_ and _kdEq_
				//Restore at their original meaning 
				$value = str_replace("_kdAt_", "@", str_replace("_kdEq_", "=", $value));
			}
			//$wordIndex = "'".$key."'";
			$wordIndex = $key;
			//We need to deal with only columns which we are interested with 
			//Check if the key and the value matches 
			if ($this->isColumnInSearchTextData($key))	{
				//$this->objectPropertiesDataArray[$wordIndex] = $value;
				$referenceValue = trim($this->searchTextDataArray[$wordIndex]);
				if ($referenceValue == "")	{
					// Non Specified so fetch the content 
					$this->objectPropertiesDataArray[$wordIndex] = $value;
				} else {
					//Since specified value is not empty, it has to match with the one in the object 
					if (strtolower($value) == strtolower($referenceValue))	{
						$this->objectPropertiesDataArray[$wordIndex] = $value;
					}
				}
			}
		}
	}
	public function init($searchText, $filehandler)	{ 
		$this->searchTextDataArray = array();
		$this->searchTextSignArray = array(); //Search Text does not alter
		/* Dealing with searchText */
		//We need to intialize the object properties array to null/_@32767@_
		$this->objectPropertiesDataArray = array();
		$tempArr = explode(";", $searchText);
		$columnsToWrite = "";
		foreach ($tempArr as $asearch)	{
			$keyAndValue = explode("=", $asearch);
			$key = trim($keyAndValue[0]);
			$value = "";
			if (sizeof($keyAndValue) > 1)	{
				$value = trim($keyAndValue[1]);
			}
			$sign = true;
			if (strpos($key, "!") === 0)	{
				//! found
				$key=substr($key, 1);
				$sign = false;
			}
			//$wordIndex = "'".$key."'";
			$wordIndex = $key;
			$this->searchTextDataArray[$wordIndex] = $value;
			$this->searchTextSignArray[$wordIndex] = $sign;
			$this->objectPropertiesDataArray[$wordIndex] = "_@32767@_";
			//Every key represents a columnName 
			$columnsToWrite .= ",".$key;
		}
		$this->filehandler = $filehandler;
		$columnsToWrite = substr($columnsToWrite, 1)."\n"; //Remove the leading comma(,)
		fwrite($this->filehandler, $columnsToWrite) or Object::shootException("CSVProcessor -> Could not write headers");
	}
	public function __construct($searchText, $filehandler)	{
		$this->init($searchText, $filehandler);
	}
	public function __destruct()	{
		/*Compiled by Ndimangwa Fadhili Ngoya*/
	}
}
class ViawableColumnsDigestor	{
	private $classlist;
	private $propertylist;
	public function getClassList()	{ return $this->classlist; }
	public function getColumnStructure()	{
		/*
		Return arr['classname']['propertyname'] = propertype
		*/
		return $this->propertylist;
	}
	public function __constuct($line)	{
		$this->classlist = array();
		$this->propertylist = array();
		//Input @classname/prop1,type1;prop2,type2....;propn,typen@classname/prop1,type1...;typen,typen...@classname/..
		$arrayOfClasses = explode("@", $line);  
		foreach ($arrayOfClasses as $ln)	{
			//classname/prop1,type1;prop2,type2...;propn,typen 
			$classAndProperties = explode("/",$ln); //Index 0 = classname; Index 1 = prop1,type1;prop2,type2----;propn,typen
			if (sizeof($classAndProperties) != 2) Object::shootException('Class List (1) Not well formed');
			$classname = $classAndProperties[0];
			$argumentList = $classAndProperties[1];
			$classsize = sizeof($this->classlist);
			$this->classlist[$classsize] = $classname;
			$classText = "'".$classname."'";
			//Dealing with Arguments 
			$propertyType = explode(";", $argumentList);
			$this->propertylist[$classText] = array();
			foreach ($propertyType as $pt)	{
				//pt is prop1,type1
				$ptArr = explode(",", $pt);
				if (sizeof($ptArr) != 2) Object::shootException("Class List (2) Not well formed");
				$property1 = $ptArr[0];
				$type1 = $ptArr[1];
				$propertyText = "'".$property1."'";
				$this->propertylist[$classText][$propertyText] = $type1;
			}
		} //foreach
	} //construct ends here
}
class Date	extends Object {
	private $year;
	private $month;
	private $day;
	private $hour;
	private $minute;
	private $second;
	private $dateValue;
	private $dateTimeString;
	final public static function getCurrentDateAndTime()	{
		date_default_timezone_set("Africa/Dar_es_Salaam");
		return date("Y:m:d:H:i:s");
	}
	public function __construct($datetime)	{
		/* Format yyyy:mm:dd:hh:mm:ss */
		$this->setDateAndTime($datetime);
	}
	public function setDateAndTime($datetime)	{
		$dt = explode(":", $datetime);
		if (sizeof($dt) != 6) {
			$this->year = "-1";
			$this->month = "-1";
			$this->day = "-1";
			$this->hour = "-1";
			$this->minute = "-1";
			$this->second = "-1";
			$this->dateValue = array();
			$this->dateTimeString=$datetime;
			return;
		}
		$this->year = $dt[0];
		$this->month = $dt[1];
		$this->day = $dt[2];
		$this->hour = $dt[3];
		$this->minute = $dt[4];
		$this->second = $dt[5];
		$this->dateValue = $dt;
		$this->dateTimeString=$datetime;
	}
	protected function setMe($database, $id, $conn)	{}
	public function cloneMe($updateDataArray)	{ return -1; }
	public function reload()	{}
	public function getClassName()	{ return "Date"; }
	protected function getAdvancedProperties()	{ return $this->getProperties();}
	protected function getProperties()	{ return "@year=$this->year@month=$this->month@day=$this->day@hour=$this->hour@minute=$this->minute@second=$this->second"; }
	public function getDateAndTimeString()	{ return $this->dateTimeString; }
	public function debug()	{
		
	}
	public function searchMatrix($matrix1)	{
		$string1 = $this->getProperties();
		$matrix1->searchText($string1);
		return $matrix1;
	}
	public function processCSV($csvProcessor1)	{
		$string1=$this->getProperties();
		$csvProcessor1->appendCSVData($string1);
		return $csvProcessor1;
	}
	public function commitUpdate()	{}
	public function commitDelete()	{}
	public function getYear()	{ return $this->year; }
	public function getMonth()	{ return $this->month; }
	public function getDay()	{ return $this->day; }
	public function getHour()	{ return $this->hour; }
	public function getMinute()	{ return $this->minute; }
	public function getSecond()	{ return $this->second; }
	public function getDateAndTimeValue()	{ return $this->dateValue; }
	public function compareDateAndTime($date1)	{
		/*
			Return -1 if this date is less than $date1
			Return 0 if this date is equal to $date1
			Return 1 if this date is greater than $date1
		*/
		$cmp = 0;
		$dt1 = $this->getDateAndTimeValue();
		$dt2 = $date1->getDateAndTimeValue();
		for ($i=0; ($i<sizeof($dt1))||($i<sizeof($dt2)); $i++)	{
			if (intval($dt1[$i]) < intval($dt2[$i]))	{
				$cmp = -1; break;
			} else if (intval($dt1[$i]) > intval($dt2[$i]))	{
				$cmp = 1; break;
			}
		}
		return $cmp;
	}
	public function dateDifference($__date)	{
		/*  
			if this date is less than date1 then 
		*/
		$date1 = $this;
		$date2 = $__date;
		$positive = true;
		if ($date1->compareDate($date2) < 0)	{
			$positive = false;
			$date1 = $__date;
			$date2 = $this;
		}
		$yearComplement = 9999;
		$monthComplement = 11; //0-11 month, 0-30 days setup 31 days default
		$dayComplement = 30;
		$hourComplement = 23;
		$minuteComplement = 59;
		$secondComplement = 59;
		//Complement - date2 , we need to adjust month and date one value less 
		$yearDiff = $yearComplement - intval($date2->getYear());
		$monthDiff = $monthComplement - (intval($date2->getMonth()) - 1);
		$dayDiff = $dayComplement - (intval($date2->getDay()) - 1);
		$hourDiff = $hourComplement - intval($date2->getHour());
		$minuteDiff = $minuteComplement - intval($date2->getMinute());
		$secondDiff = $secondComplement - intval($date2->getSecond());
		//Add diff to date1 , date 1 should be adjusted too 
		//Since this was a complement addition, we need to add 1
		$yearDiff = 1 + $yearDiff + intval($date1->getYear());
		$monthDiff = 1 + $monthDiff + (intval($date1->getMonth()) - 1);
		$dayDiff = 1 + $dayDiff + (intval($date1->getDay()) - 1);
		$hourDiff = 1 + $hourDiff + intval($date1->getHour());
		$minuteDiff = 1+ $minuteDiff + intval($date1->getMinute());
		$secondDiff = 1+ $secondDiff + intval($date1->getSecond());
		//Difference correction and take over marker, working right to left 
		$markMinute = false;
		$markHour = false;
		$markDay = false;
		$markMonth = false;
		$markYear = false;
		if ($secondDiff >= ($secondComplement + 1))	{
			$secondDiff = $secondDiff - ($secondComplement + 1);
		} else {
			$markMinute = true; //We have alredy borrowed
		}
		if ($minuteDiff >= ($minuteComplement + 1))	{
			$minuteDiff = $minuteDiff - ($minuteComplement + 1);
		} else {
			$markHour = true; //We have alredy borrowed
		}
		if ($hourDiff >= ($hourComplement + 1))	{
			$hourDiff = $hourDiff - ($hourComplement + 1);
		} else {
			$markDay = true; //We have alredy borrowed
		}
		if ($dayDiff >= ($dayComplement + 1))	{
			$dayDiff = $dayDiff - ($dayComplement + 1);
		} else {
			$markMonth = true; //We have alredy borrowed
		}
		if ($monthDiff >= ($monthComplement + 1))	{
			$monthDiff = $monthDiff - ($monthComplement + 1);
		} else {
			$markYear = true; //We have alredy borrowed
		}
		if ($yearDiff >= ($yearComplement + 1))	{
			$yearDiff = $yearDiff - ($yearComplement + 1);
		}
		//We have already add marks and adjusted
		if ($markMinute)	{			
			$minuteDiff = $minuteComplement + $minuteDiff; //same as substract one 
			if ($minuteDiff >= ($minuteComplement + 1)) { $minuteDiff = $minuteDiff - ($minuteComplement + 1); }
			//Transitive borrow 
			if ($date1->getMinute() == $date2->getMinute())	{ $markHour = true; }
		}
		if ($markHour)	{			
			$hourDiff = $hourComplement + $hourDiff; //same as substract one 
			if ($hourDiff >= ($hourComplement + 1)) { $hourDiff = $hourDiff - ($hourComplement + 1); }
			//Transitive borrow 
			if ($date1->getHour() == $date2->getHour())	{ $markDay = true; }
		}
		if ($markDay)	{			
			$dayDiff = $dayComplement + $dayDiff; //same as substract one 
			if ($dayDiff >= ($dayComplement + 1)) { $dayDiff = $dayDiff - ($dayComplement + 1); }
			//Transitive borrow 
			if ($date1->getDay() == $date2->getDay()) { $markMonth = true; }
		}
		if ($markMonth)	{
			$monthDiff = $monthComplement + $monthDiff; //same as substract one 
			if ($monthDiff >= ($monthComplement + 1)) { $monthDiff = $monthDiff - ($monthComplement + 1); }
			//Transitive borrow 
			if ($date1->getMonth() == $date2->getMonth())	{ $markYear = true; }
		}
		if ($markYear)	{
			$yearDiff = $yearComplement + $yearDiff; //same as substract one 
			if ($yearDiff >= ($yearComplement + 1)) { $yearDiff = $yearDiff - ($yearComplement + 1); }
		}
		//No need to adjust anything because this is just a difference 
		$dateString = $yearDiff.":".$monthDiff.":".$dayDiff.":".$hourDiff.":".$minuteDiff.":".$secondDiff;
		$mydate = new Date($dateString);
		$mydate->setPositive($positive);
		return $mydate;
	}
	public function inDateAndTimeRange($date1, $date2)	{
		return ($this->compareDateAndTime($date1) > 0) && ($this->compareDateAndTime($date2) < 0);
	}
}
class DateAndTime extends Date	{
	public final static function getAvailableViewableColumns()	{	
		$lineToView = "@DateAndTime/year,text,k100;month,text,k100;day,text,k100;hour,text,k100;minute,text,k100;second,text,k100;dateTimeValue,text,k100;dateTimeString,text,k100";
		return $lineToView;
	}
	public final static function convertFromGUIDateFormatToSystemDateAndTimeFormat($__date)	{
		$dtArr = explode("/", $__date);
		$dtString = intval($dtArr[2]).":".intval($dtArr[1]).":".intval($dtArr[0]).":00:00:00";
		return $dtString;
	}
	public final static function convertFromSystemDateAndTimeFormatToGUIDateFormat($__date)	{
		$dtArr = explode(":", $__date);
		//System::convertIntegerToStringOfAGivenLength($__data, $__len)
		$dtString = System::convertIntegerToStringOfAGivenLength($dtArr[2], 2)."/".System::convertIntegerToStringOfAGivenLength($dtArr[1], 2)."/".System::convertIntegerToStringOfAGivenLength($dtArr[0], 4);
		return $dtString;
	}
	public final static function convertFromDateTimeObjectToGUIDateFormat($dateObject1)	{
		return self::convertFromSystemDateAndTimeFormatToGUIDateFormat($dateObject1->getDateAndTimeString());
	}
	public function __construct($emptydatabase, $date, $emptyconn)	{
		parent::__construct($date);
	}
	//Overriding function 
	public function getClassName()	{ return "DateAndDate"; }
}
class System {
	public final static function convertToTelephoneFormat($telno)	{
		$tno = $telno;
		$len = strlen($tno);
		$tarr = array();
		while ($len > 0)	{
			if ($len > 3)	{
				$tarr[sizeof($tarr)] = substr($tno, $len - 3, 3);
				$tno = substr($tno, 0, $len-3);
			} else	{
				$tarr[sizeof($tarr)] = $tno;
				$tno = ""; //Just to make sure the loop terminate
			}
			$len = strlen($tno);
		} //end-of-while-loop
		$telno = $tarr[0];
		for ($i=1; $i < sizeof($tarr) - 1; $i++)	{
			$telno = $tarr[$i]."-".$telno;
		}
		$tdata = $tarr[sizeof($tarr)-1];
		if (strlen($tdata) == 1)	{
			$telno = $tdata.$telno;
		} else	{
			$telno = $tdata."-".$telno;
		}
		return $telno;
	}
	public final static function getCodeString($codeLength)	{
		 $lk[1] = "A"; $lk[2] = "B"; $lk[3] = "C"; $lk[4] = "D";
        $lk[5] = "E"; $lk[6] = "F"; $lk[7] = "G"; $lk[8] = "H";
        $lk[9] = "I"; $lk[10] = "J"; $lk[11] = "K"; $lk[12] = "L";
        $lk[13] = "M"; $lk[14] = "N"; $lk[15] = "O"; $lk[16] = "P";
        $lk[17] = "Q"; $lk[18] = "R"; $lk[19] = "S"; $lk[20] = "T";
        $lk[21] = "U"; $lk[22] = "V"; $lk[23] = "W"; $lk[24] = "X";
        $lk[25] = "Y" ; $lk[26] = "Z";
        $lk[27] = "0"; $lk[28] = "1"; $lk[29] = "2"; $lk[30] = "3";
        $lk[31] = "4"; $lk[32] = "5"; $lk[33] = "6"; $lk[34] = "7";
        $lk[35] = "8"; $lk[36] = "9";
        $lk[37] = "a"; $lk[38] = "b"; $lk[39] = "c"; $lk[40] = "d";
        $lk[41] = "e"; $lk[42] = "f"; $lk[43] = "g"; $lk[44] = "h";
        $lk[45] = "i"; $lk[46] = "j"; $lk[47] = "k"; $lk[48] = "l";
        $lk[49] = "m"; $lk[50] = "n"; $lk[51] = "o"; $lk[52] = "p";
        $lk[53] = "q"; $lk[54] = "r"; $lk[55] = "s"; $lk[56] = "t";
        $lk[57] = "u"; $lk[58] = "v"; $lk[59] = "w"; $lk[60] = "x";
        $lk[61] = "y"; $lk[62] = "z";
		$codeLength = intval($codeLength);
        $code = "";
        for ($i = 0; $i < $codeLength; $i++)      {
                $code = $code.$lk[rand(1, 62)];
        }
        return $code;
	}
	public final static function convertIntegerToStringOfAGivenLength($__data, $__len)	{
		$__data = "".$__data; //toString 
		$tempVal = $__data;
		for ($i=strlen($__data); $i < $__len; $i++) $tempVal = "0".$tempVal;
		return $tempVal;
	}
}
class Number	{
	final public static function commaSeparatorHelper($num)	{
		$num = intval($num);
		/* Exit condition, always this will be a leading part */
		$remainder = $num % 1000;
		$multiple = $num / 1000;
		if ($multiple < 1)	{
			return $remainder;
		}
		/* Now multiple is 1 or great, adjust length of string to be three */
		$remainder="".$remainder;
		for ($i=strlen($remainder); $i < 3; $i++)	$remainder = "0".$remainder;
		return self::commaSeparatorHelper($multiple).",".$remainder;
	}
	final public static function getCommaSeparatorFormat($num)	{
		return self::commaSeparatorHelper($num);
	}
	final public static function convertToWord($number)	{
		/*
		This function convertToWord was sourced from the link below, on August 06, 2014
		The original function was named convert_number_to_words
		http://www.karlrixon.co.uk/writing/convert-numbers-to-words-with-php/
		*/
		$hyphen      = '-';
		$conjunction = ' and ';
		$separator   = ', ';
		$negative    = 'negative ';
		$decimal     = ' point ';
		$dictionary  = array(
			0                   => 'zero',
			1                   => 'one',
			2                   => 'two',
			3                   => 'three',
			4                   => 'four',
			5                   => 'five',
			6                   => 'six',
			7                   => 'seven',
			8                   => 'eight',
			9                   => 'nine',
			10                  => 'ten',
			11                  => 'eleven',
			12                  => 'twelve',
			13                  => 'thirteen',
			14                  => 'fourteen',
			15                  => 'fifteen',
			16                  => 'sixteen',
			17                  => 'seventeen',
			18                  => 'eighteen',
			19                  => 'nineteen',
			20                  => 'twenty',
			30                  => 'thirty',
			40                  => 'fourty',
			50                  => 'fifty',
			60                  => 'sixty',
			70                  => 'seventy',
			80                  => 'eighty',
			90                  => 'ninety',
			100                 => 'hundred',
			1000                => 'thousand',
			1000000             => 'million',
			1000000000          => 'billion',
			1000000000000       => 'trillion',
			1000000000000000    => 'quadrillion',
			1000000000000000000 => 'quintillion'
		);
   
		if (!is_numeric($number)) {
			return false;
		}
   
		if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
			// overflow
			trigger_error(
				'convertToWord only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
				E_USER_WARNING
			);
			return false;
		}

		if ($number < 0) {
			return $negative . self::convertToWord(abs($number));
		}
   
		$string = $fraction = null;
   
		if (strpos($number, '.') !== false) {
			list($number, $fraction) = explode('.', $number);
		}
   
		switch (true) {
			case $number < 21:
				$string = $dictionary[$number];
				break;
			case $number < 100:
				$tens   = ((int) ($number / 10)) * 10;
				$units  = $number % 10;
				$string = $dictionary[$tens];
				if ($units) {
					$string .= $hyphen . $dictionary[$units];
				}
				break;
			case $number < 1000:
				$hundreds  = $number / 100;
				$remainder = $number % 100;
				$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
				if ($remainder) {
					$string .= $conjunction . self::convertToWord($remainder);
				}
				break;
			default:
				$baseUnit = pow(1000, floor(log($number, 1000)));
				$numBaseUnits = (int) ($number / $baseUnit);
				$remainder = $number % $baseUnit;
				$string = self::convertToWord($numBaseUnits) . ' ' . $dictionary[$baseUnit];
				if ($remainder) {
					$string .= $remainder < 100 ? $conjunction : $separator;
					$string .= self::convertToWord($remainder);
				}
				break;
		}
   
		if (null !== $fraction && is_numeric($fraction)) {
			$string .= $decimal;
			$words = array();
			foreach (str_split((string) $fraction) as $number) {
				$words[] = $dictionary[$number];
			}
			$string .= implode(' ', $words);
		}
   
		return $string;
	}
	final public static function convertToWordByNdimangwa($num)	{
		$num=intval($num);
		if ($num == 0) return "Zero";
		/* Level One conversion, table lookup */
		$wordArray=array();
		$tenArray=array();
		$position=array();
		$wordArray[0] = "Zero"; 	$tenArray[0] = "Zero";    	$position[0]="";
		$wordArray[1] = "One";  	$tenArray[1] = "Ten";		$position[1]="Ten";
		$wordArray[2] = "Two";		$tenArray[2] = "Twenty";	$position[2]="Hundred and";
		$wordArray[3] = "Three";	$tenArray[3] = "Thirty";	$position[3]="Thousand,";
		$wordArray[4] = "Four";		$tenArray[4] = "Forty";		$position[4]="Ten";
		$wordArray[5] = "Five";		$tenArray[5] = "Fifty";		$position[5]="Hundred and";
		$wordArray[6] = "Six";		$tenArray[6] = "Sixty";		$position[6]="Million,";
		$wordArray[7] = "Seven";	$tenArray[7] = "Seventy";	$position[7]="Ten";
		$wordArray[8] = "Eight";	$tenArray[8] = "Eighty";	$position[8]="Hundred and";
		$wordArray[9] = "Nine";		$tenArray[9] = "Ninety";	$position[9]="Billion";
		$position[10] = "Ten"; $position[11]="Hundred and"; $position[12]="Trillion";
		$position[13]="Ten"; $position[14]="Hundred and";
		$toWord="";
		$pos=0;
		$originalNumber=$num;
		while (($num > 1) && ($pos < 15))	{
			$posWeight = $position[$pos];
			$remainder=($num % 10);
			$num = ($num / 10);
			if ($remainder == 0)	{	
				if ((($pos==3) && (($originalNumber % 1000000) != 0)) || (($pos==6) && ($originalNumber % 1000000000) != 0) || (($pos==9) && ($originalNumber % 1000000000000) != 0) || (($pos==12) && ($originalNumber % 1000000000000000)) != 0) $toWord= $posWeight." ".$toWord;
			} else {
				$toWord = $wordArray[$remainder]." ".$posWeight." ".$toWord;
			}
			$pos++;
		}
		
		/* Preparing Substitution tables */
		$old=array(); $new=array();
		$old[0]="One Ten Zero"; $new[0]="Ten";
		$old[1]="One Ten One"; $new[1]="Eleven";
		$old[2]="One Ten Two"; $new[2]="Twelve";
		$old[3]="One Ten Three"; $new[3]="Thirteen";
		$old[4]="One Ten Four"; $new[4]="Fourteen";
		$old[5]="One Ten Five"; $new[5]="Fifteen";
		$old[6]="One Ten Six"; $new[6]="Sixteen";
		$old[7]="One Ten Seven"; $new[7]="Seventeen";
		$old[8]="One Ten Eight"; $new[8]="Eighteen";
		$old[9]="One Ten Nine"; $new[9]="Nineteen";
		$old[10]="Two Ten"; $new[10]="Twenty";
		$old[11]="Three Ten"; $new[11]="Thirty";
		$old[12]="Four Ten"; $new[12]="Forty";
		$old[13]="Five Ten"; $new[13]="Fifty";
		$old[14]="Six Ten"; $new[14]="Sixty";
		$old[15]="Seven Ten"; $new[15]="Seventy";
		$old[16]="Eight Ten"; $new[16]="Eighty";
		$old[17]="Nine Ten"; $new[17]="Ninety";
		$toWord=str_replace($old, $new, $toWord);
		
		/* Replace All Occurance of a word Zero if any, and a word One Ten should read Ten*/
		$toWord=str_replace("One Ten", "Ten", $toWord);
		$toWord=str_replace("Zero", "", $toWord);
		/* Replace now a word and , with nothing */
		$toWord=str_replace("and ,", "", $toWord);
		/* Now I need to replace the last comma with And */
		$toWordArray = explode(",", $toWord);
		$toWord=$toWordArray[0];
		$len = sizeof($toWordArray);
		for ($i=1; $i< $len; $i++)	{
			if ($i == ($len -1)) {
				$toWord = $toWord." and ".$toWordArray[$i];
			} else	{
				$toWord = $toWord.", ".$toWordArray[$i];
			}
			
		}
		/*Treat the trailing and*/
		$toWord = $toWord."ikAsoft";
		//$toWord = str_replace("and ikAsoft", "", $toWord);
		//$toWord = str_replace("andikAsoft", "", $toWord);
		//$toWord = str_replace("ikAsoft", "", $toWord);
		return $toWord;
	}
}
class GPSCoordinate	{
	private $syscoordinate;
	private $coordinateClean=false;
	private function convertFromNorthEastFormatToSystemFormat($gpscoordinate)	{
		//Input N 71 27.826
		$tempArr = array();
		$gpsArr = explode(" ",$gpscoordinate);
		if (sizeof($gpsArr) < 2) Object::shootException("GPS Coordinate Conversion, Source Coordinate has no enough data");
		if (sizeof($gpsArr) > 3) Object::shootException("GPS Coordinate Conversion, Source Coordinate has extra data");
		if ($gpsArr[0] == "N" || $gpsArr[0] == "n" || $gpsArr[0] == "E" || $gpsArr[0] == "e")	{
			$tempArr[0] = "";
		} else if ($gpsArr[0] == "S" || $gpsArr[0] == "s" || $gpsArr[0] == "W" || $gpsArr[0] == "w")	{
			$tempArr[0] = "-";
		} else	{
			Object::shootException("GPS Coordinate Conversion, Invalid direction NnEeSsWw");
		}
		$tempArr[1] = $gpsArr[1]; //Number Part As it is 
		//Dealing with decimal point part
		//Set Default 
		$tempArr[2] = 0.000000;
		if (sizeof($gpsArr) == 3)	{
			//Decimal Part Exists
			$decimalPartNumber = floatval($gpsArr[2]) / 60; 
			$tempArr[2] = round($decimalPartNumber, 6);
		} //end-if
		$decimalPartNumber = floatval($tempArr[1]) + floatval($tempArr[2]);
		$decimalPartNumber = $tempArr[0]."".$decimalPartNumber;
		$decimalPartNumber=trim($decimalPartNumber);
		return floatval($decimalPartNumber);
	}
	public function __construct($gpscoordinate)	{
		$exprGPSCoordinateSubFormat1="/^(\-)?\d+(\.\d+)?\,(\-)?\d+(\.\d+)?$/";
		$exprGPSCoordinateSubFormat2="/^[NnSs]\s\d+\s\d+(\.\d+)?\,[EeWw]\s\d+\s\d+(\.\d+)?$/";
		if (preg_match($exprGPSCoordinateSubFormat1, $gpscoordinate))	{
			//Already in the correct format 
			$this->syscoordinate = $gpscoordinate;
			$this->coordinateClean = true;
		} else if (preg_match($exprGPSCoordinateSubFormat2, $gpscoordinate))	{
			//Convert first 
			$tempArr = explode(",", $gpscoordinate);
			$syscoordinate = "";
			$count = 0;
			foreach ($tempArr as $coord1)	{
				if ($count == 0)	{
					$syscoordinate = $this->convertFromNorthEastFormatToSystemFormat($coord1);
				} else {
					$syscoordinate .= ",".$this->convertFromNorthEastFormatToSystemFormat($coord1);
				}				
				$count++;
			}//end-for-each
			$this->syscoordinate=$syscoordinate;
			$this->coordinateClean=true;
		}
	}
	public function getSystemCoordinate()	{ return $this->syscoordinate; }
	public function isCoordinateClean()	{ return $this->coordinateClean; }
}
class Promise	{
	public static $COMPLETED = 1;
	public static $NOT_YET = 2;
	private $success;
	private $reason;
	private $extraInfo;
	private $filename;	//If there is a corresponding file
	private $status;
	private $position;
	public function __construct()	{
		$this->success = false;
	}
	public function setPromise($bln)	{ $this->success = $bln; }
	public function getPromise()	{ return $this->success; }
	public function isPromising()	{ return $this->success; }
	public function setReason($reason)	{ $this->reason = $reason; }
	public function getReason()	{ return $this->reason; }
	public function setExtraInformation($extraInfo)	{ $this->extraInfo = $extraInfo; }
	public function getExtraInformation()	{ return $this->extraInfo; }	
	public function setFileName($filename)	{ $this->filename = $filename; }
	public function getFileName()	{ return $this->filename; }
	public function setStatus($status)	{ $this->status = $status; }
	public function getStatus()	{ return $this->status; }
	public function setPosition($position)	{ $this->position = $position; }
	public function getPosition()	{ return $this->position; }
}
class Compiler {
	/* This is a service class, it is an object since we need to trace changes */
	private $nextTokenIndex;
	private $backupOfNextTokenIndex;
	private $originalText;
	private $tokenMatrix; //hold the token matrix 
	private $lastTokenWord = null;
	private $greaterThanSymbol = false;
	private $negationSymbol = false;
	private function getNextToken()	{
		$returnedCharacter = $this->originalText[$this->nextTokenIndex];
		$this->backupOfNextTokenIndex = $this->nextTokenIndex;
		$this->nextTokenIndex++;
	}
	private function readNextToken()	{
		//None Advance
		$returnedCharacter = $this->originalText[$this->nextTokenIndex];
	}
	private function undoGetNextToken()	{
		$this->nextTokenIndex = $this->backupOfNextTokenIndex;
	}
	private function optionSpace()	{
		$nextToken = $this->readNextToken();
		while (true)	{
			if ($nextToken != " ") break;
			$nextToken = $this->getNextToken(); //Skip All Spaces
		}
		return true;
	}
	private function optionGreaterThanSymbol()	{
		$nextToken = $this->readNextToken();
		if ($nextToken == ">")	{
			$this->greaterThanSymbol = true;
			$nextToken = $this->getNextToken();
		}
		return true;
	}
	private function optionNegationSymbol()	{
		$nextToken = $this->readNextToken();
		if ($nextToken == "!")	{
			$this->negationSymbol = true;
			$nextToken = $this->getNextToken();
		}
		return true;
	}
	private function getWordToken()	{
		$state = 0;
		$wordToken = "";
		$nextToken = $this->readNextToken();
		while (true)	{
			if ($nextTokenIndex >= strlen($this->originalText)) break;
			if (($state == 0) && (preg_match("/^[A-Za-z]$/", $nextToken)))	{
				$state = 1;  $wordToken .= $nextToken; $nextToken = $this->getNextToken(); continue;
			} 
			if (($state == 1) && (preg_match("/^[A-Za-z0-9]$/", $nextToken)))	{
				$wordToken .= $nextToken; $nextToken = $this->getNextToken(); continue;
			}
			if ($state == 1) {
				//Another character has been found 
				$state=2;
			}
			if ($state == 2)	{
				/*We need to 
				*/
				$index = sizeof($this->tokenMatrix);
				$this->tokenMatrix[$index] = array();
				$this->tokenMatrix[$index][0] = $wordToken;
				$this->tokenMatrix[$index][1] = $greaterThanSymbol;
				$this->tokenMatrix[$index][2] = $negationSymbol;
				break;
			}
		}
		return true;
	}
	private function firstWordToken()	{
		return ($this->optionSpace()) && ($this->optionNegationSymbol()) && ($this->optionSpace()) && ($this->getWordToken());
	}
	private function otherWordToken()	{
		return ($this->optionSpace()) && ($this->optionGreaterThanSymbol()) && ($this->optionSpace()) && ($this->optionNegationSymbol()) && ($this->optionSpace()) && ($this->getWordToken());
	}
	public function __construct($originalText)	{
		$this->originalText = $originalText;
		$this->nextTokenIndex = 0;
		$this->backupOfNextTokenIndex = 0;
		$this->tokenMatrix = array();
	}
	public function compile()	{
		return ($this->firstWordToken()) && ($this->otherWordToken());
	}
	public function getTokenMatrix()	{
		return $this->tokenMatrix;
	}
}
class FileFactory {
	/*
	These are statically defined file Manager
	If not supposed to return data, they should return a promise object with data 
	A function which does file editing should create a backup file with 
	Append with .backup extension 
	The promise object should have the position of the next element, this means the length of returned records
		since this is a zero based
	*/
	public final static function updateExistingFile($filename, $doc)	{
		//Create a backup first
		//Input, the Updated DOMDocument 
		//This applies during deleting and editing 
		/*checksum begins here*/
		$doc = self::createDOMChecksum($doc);
		if (is_null($doc))	{
			$promise1 = new Promise();
			$promise1->setReason("Duplicate Checksum keys were found");
			return $promise1;
		}
			/*checksum ends here*/
		$promise1 = self::createBackupFile($filename);
		if ($promise1->isPromising())	{
			//Reset the promise Object 
			$promise1->setPromise(false);
			$doc->formatOutput = true;
			if ($doc->save($filename))	{
				$promise1->setPromise(true);
			} else {
				$promise1->setReason("Could not create update a file, perpahs the server policy or timeout has occured");
			}
		}
		return $promise1;
	}
	public final static function createBackupFile($filename)	{
		$promise1 = new Promise();
		if (copy($filename, $filename.".backup"))	{
			$promise1->setPromise(true); //successful
		} else {
			$promise1->setReason("Could not make a backup file");
		}
		return $promise1;
	}
	public final static function createFile($filename, $rootnodename)	{
		$doc=new DOMDocument(Object::$xmlVersion);
		$doc->formatOutput = true;
		$doc->appendChild($doc->createElement($rootnodename));
		$promise1 = new Promise();
		if ($doc->save($filename))	{
			$promise1->setPromise(true);
		} else {
			$promise1->setReason("Could not create a file, perhaps you do not have permission to this server");
		}
		return $promise1;
	}
	public final static function readFile($filename)	{
		//return xmlDocumenRoot or null 
		$doc = null;
		if (file_exists($filename))	{
			$doc = new DOMDocument();
			$doc->load($filename);
		}
		return $doc;
	}
	public final static function findNodes($doc, $nodename, $pos)	{
		/*
		doc refer to the outer node 
		if pos = *, you will get all matched nodes from the outer node 
		else specific position
		*/
		$pos = trim("".$pos);
		$nodeList = array();
		$tempList = $doc->getElementsByTagName($nodename);
		if ($pos == "*")	{
			//All records 
			foreach ($tempList as $alist)	{
				$nodeList[sizeof($nodeList)] = $alist;
			}
		} else {
			//Specific position 
			$pos = intval($pos);
			if ($pos < intval($tempList->length))	{
				$nodeList[sizeof($nodeList)] = $tempList->item($pos);
			}
		}
		if (sizeof($nodeList) == 0) $nodeList = null;
		return $nodeList;
	}
	public final static function deleteNodeCollections($filename, $doc, $collectionToDelete)	{
		/*
		INPUT filename, DOMDocument and collectionToBe Deleted 
		OUTPUT promise Object 
		*/
		$promise1 = new Promise();
		$enableUpdate = false;
		if (is_null($collectionToDelete))	{
			$promise1->setReason("Collection to delete were not found");
			return $promise1;
		}
		foreach ($collectionToDelete as $anode1)	{
			$parent1 = $anode1->parentNode;
			if (! is_null($parent1))	{
				$parent1->removeChild($anode1); $enableUpdate=true;
			}
		}
		if ($enableUpdate)	{
			$promise1 = self::updateExistingFile($filename, $doc);
		} else {
			$promise1->setReason("There is nothing to delete from the collection");
		}
		return $promise1;
	}
	public final static function addNodeAfterCollections($filename, $doc, $referenceCollection, $nodeToAdd)	{
		/*
		The node should have been created with the same doc Object
		*/
		$promise1 = new Promise();
		if (is_null($referenceCollection))	{
			$promise1->setReason("Reference Collection is Empty");
			return $promise1;
		}
 		$enableUpdate = false;
		foreach ($referenceCollection as $anode1)	{
			$parent1 = $anode1->parentNode;
			if (! is_null($parent1))	{
				$clonedNode = $nodeToAdd->cloneNode(true);
				$parent1->appendChild($clonedNode); $enableUpdate = true;
			}
		}
		if ($enableUpdate)	{
			$promise1 = self::updateExistingFile($filename, $doc);
		} else {
			$promise1->setReason("There is nothing to Append in the NodeTree");
		}
		return $promise1;
	}
	public final static function addNodeBeforeCollections($filename, $doc, $referenceCollection, $nodeToAdd)	{
		/*
		The node should have been created with the same doc Object
		*/
		$promise1 = new Promise();
		if (is_null($referenceCollection))	{
			$promise1->setReason("Reference Collection is Empty");
			return $promise1;
		}
		$enableUpdate = false;
		foreach ($referenceCollection as $anode1)	{
			$parent1 = $anode1->parentNode;
			if (! is_null($parent1))	{
				$clonedNode = $nodeToAdd->cloneNode(true);
				$parent1->insertBefore($clonedNode, $anode1); $enableUpdate = true;
			}
		}
		if ($enableUpdate)	{
			$promise1 = self::updateExistingFile($filename, $doc);
		} else {
			$promise1->setReason("There is nothing to Append in the NodeTree");
		}
		return $promise1;
	}
	public final static function appendNode($filename, $doc, $refnodename, $pos, $nodeToAppend)	{
		/*
		if pos is * append to All Matched nodes 
		nodeToAppend is an xml node, create a backup 
		*/
		$promise1 = new Promise();
		$promise1->setReason("No need to Implement this function");
		$promise1->setPromise(false);
		return $promise1;
	}
	public final static function replaceContentOfNodeCollections($filename, $doc, $referenceCollection, $contentText)	{
		/*
		if pos is * replace all matched nodes
		newNode is a new XML Node , create a backup
		NodeToReplace should be a member created by the same DOMDocument ($doc)
		*/
		$promise1 = new Promise();
		if (is_null($referenceCollection))	{
			$promise1->setReason("Reference Collection is Empty");
			return $promise1;
		}
		$enableUpdate = false;
		foreach ($referenceCollection as $anode1)	{
			if ($anode1->hasChildNodes())	{
				//Clear All Childres 
				foreach ($anode1->childNodes as $child1)	{
					$anode1->removeChild($child1);
				}
			}
			//Now Add the content Node 
			$anode1->appendChild($doc->createTextNode($contentText)); $enableUpdate=true;
		}
		if ($enableUpdate)	{
			$promise1 = self::updateExistingFile($filename, $doc);
		} else {
			$promise1->setReason("There is nothing to Replace in the Collection");
		}
		return $promise1;
	}
	public final static function replaceNodeCollections($filename, $doc, $referenceCollection, $nodeToReplace)	{
		/*
		if pos is * replace all matched nodes
		newNode is a new XML Node , create a backup
		NodeToReplace should be a member created by the same DOMDocument ($doc)
		*/
		$promise1 = new Promise();
		if (is_null($referenceCollection))	{
			$promise1->setReason("Reference Collection is Empty");
			return $promise1;
		}
		$enableUpdate = false;
		foreach ($referenceCollection as $anode1)	{
			$parent1 = $anode1->parentNode;
			if (! is_null($parent1))	{
				$clonedNode = $nodeToReplace->cloneNode(true);
				$parent1->replaceChild($clonedNode, $anode1);
				$enableUpdate = true;
			}
		}
		if ($enableUpdate)	{
			$promise1 = self::updateExistingFile($filename, $doc);
		} else {
			$promise1->setReason("There is nothing to Replace in the Collection");
		}
		return $promise1;
	}
	public final static function getListOfNodesWithValueFromCollection($listOfAllNodes, $pos, $value, $iscasesensitive)	{
		if (is_null($listOfAllNodes)) return null;
		$pos = trim("".$pos);
		$listOfMatchedNodes = array();
		foreach ($listOfAllNodes as $anode1)	{
			//anode1 is doc 
			$nodeValue = $anode1->nodeValue;
			//Lower value equivalent
			$ivalue = strtolower($value);
			$inodeValue = strtolower($nodeValue);
			//Expression B + !A!BC 
			$logic_A = $iscasesensitive;
			$logic_B = ($value == $nodeValue);
			$logic_C = ($ivalue == $inodeValue);
			if ($logic_B || (! $logic_A)&& (! $logic_B) && $logic_C)	{
				//Add to matched list 
				$listOfMatchedNodes[sizeof($listOfMatchedNodes)] = $anode1;
			}
		}
		/*
		We need to filter Accoring to the matched Nodes 
		*/
		if ($pos == "*")	{
			//What is matched is Alrite 
		} else {
			$pos = intval($pos);
			if ($pos < sizeof($listOfMatchedNodes))	{
				$tempVal = $listOfMatchedNodes[$pos];
				$listOfMatchedNodes = array();
				$listOfMatchedNodes[0] = $tempVal;
			} else {
				$listOfMatchedNodes = array(); //clear array
			}
		}
		/* Return now the results */
		if (sizeof($listOfMatchedNodes) == 0) $listOfMatchedNodes = null;
		return $listOfMatchedNodes;
	}
	private final static function convertCustomStringToArray($customString1)	{
		/*
		INPUT 0:nodename1;3:nodename2;*:nodename3 
		OUTPUT array[0][0]=0
				array[0][1]=nodename1 
				array[1][0]=3
				
				so nodename1 is/are a parent of nodename2 which is/are parent of nodename3 
		*/
		$customArray = array();
		$nodeListArray = explode(";", $customString1);
		foreach ($nodeListArray as $aListArray)	{
			//0:nodename1 
			$listsize = sizeof($customArray);
			$customArray[$listsize] = array();
			$splitNodesArray = explode(":", $aListArray);
			if (sizeof($splitNodesArray) != 2) return null;
			$customArray[$listsize][0] = $splitNodesArray[0]; 
			$customArray[$listsize][1] = $splitNodesArray[1]; 
		}
		return $customArray;
	}
	private final static function packAllCustomNodes($doc, $nodeCollectionArray, $customArray, $currentArrayPosition)	{
		//Terminating Condition
		if (is_null($customArray)) return null;
		if (($currentArrayPosition + 1)> sizeof($customArray)) return $nodeCollectionArray;
		if (($currentArrayPosition != 0) && (sizeof($nodeCollectionArray) == 0)) return null;
		//Fetch the position and the nodename 
		$pos = trim("".$customArray[$currentArrayPosition][0]);
		$nodename = trim($customArray[$currentArrayPosition][1]);
		//Initially the nodeCollectionArray would be null
		if ($currentArrayPosition == 0)	{
			$nodeCollectionArray = array();
			$tempList = $doc->getElementsByTagName($nodename);
			if ($tempList->length == 0) return null;
			if ($pos == "*")	{
				//All records 
				foreach ($tempList as $alist)	{
					$nodeCollectionArray[sizeof($nodeCollectionArray)] = $alist;
				}
			} else {
				//Specific position 
				$pos = intval($pos);
				if ($pos < intval($tempList->length))	{
					$nodeCollectionArray[sizeof($nodeCollectionArray)] = $tempList->item($pos);
				}
			}
			return self::packAllCustomNodes($doc, $nodeCollectionArray, $customArray, $currentArrayPosition + 1);
		} //end-if-pos-zero
		/*Proceed Now with the list */
		$tempNodeCollection = array();
		foreach ($nodeCollectionArray as $node1)	{
			$tempList = $node1->getElementsByTagName($nodename);
			if ($tempList->length == 0) return null;
			if ($pos == "*")	{
				//All records 
				foreach ($tempList as $alist)	{
					$tempNodeCollection[sizeof($tempNodeCollection)] = $alist;
				}
			} else {
				//Specific position 
				$pos = intval($pos);
				if ($pos < intval($tempList->length))	{
					$tempNodeCollection[sizeof($tempNodeCollection)] = $tempList->item($pos);
				}
			}
		}//end-foreach-anode 
		return self::packAllCustomNodes($doc, $tempNodeCollection, $customArray, $currentArrayPosition + 1);
	}
	public final static function findCustomNodes($doc, $customExpression)	{
		/*
		Input: pos:nodename;pos:nodename.....;pos:nodename
		*/
		$nodeListArray = self::convertCustomStringToArray($customExpression);
		$nodeListArray = self::packAllCustomNodes($doc, null, $nodeListArray, 0);
		if (sizeof($nodeListArray) == 0) $nodeListArray = null;
		return $nodeListArray;
	}
	public final static function findParentOfNode($doc, $refnodename, $pos)	{
		/*
			return xml parent or null 
			parent of pos-th node 
			pack this in an array too
		*/
		$pos = trim("".$pos);
		if ($pos == "*") return null; //does not allow all, should be specific 
		$nodeList = self::findNodes($doc, $refnodename, $pos);
		if (is_null($nodeList)) return null;
		// For compliance , pack in array
		$parentArray = array();
		$parentArray[0] = $nodeList[0]->parentNode;
		return $parentArray;
	}
	public final static function findParentOfCustomNode($doc, $customExpression)	{
		$nodeList = self::findCustomNodes($doc, $customExpression);
		if (is_null($nodeList)) return null;
		//Return ONLY the parent of the first march 
		$parentArray = array();
		$parentArray[0] = $nodeList[0]->parentNode;
		return $parentArray;
	}
	public final static function calculateFileChecksum($filename)	{
		/*
		isaacompletechecksum
		*/
		$doc = new DOMDocument(Object::$xmlVersion);
		$doc->load($filename);
		return self::calculateDOMChecksum($doc);
	}
	public final static function calculateDOMChecksum($doc)	{
		$referenceHash = md5(Object::$hashText);
		return self::documentHash($doc, $referenceHash);
	}
	public final static function createDOMChecksum($doc)	{
		/*
		INPUT A New DOMDocument, can have checksum or not 
		OUTPUT Return a DOMDocument with a valid checksum , same original DOM 
		*/
		$checksumvalue = self::calculateDOMChecksum($doc);
		$rootElement = $doc->documentElement;
		$checksum = $rootElement->getElementsByTagName('isaacompletechecksum');
		if (intval($checksum->length) == 0)	{
			//Not Existing
			$checksum = $doc->createElement('isaacompletechecksum');
			$checksum->appendChild($doc->createTextNode($checksumvalue));
			$rootElement->appendChild($checksum);
		} else if (intval($checksum->length) == 1)	{
			$checksum->item(0)->nodeValue = $checksumvalue;
		} else {
			//Incase of duplicate destroy the DOM 
			$doc = null;
		}
		return $doc;
	}
	public final static function getFileChecksum($filename)	{
		/*
		return null or read from isaacompletechecksum
		*/
		if (! file_exists($filename)) return null;
		$doc = new DOMDocument(Object::$xmlVersion);
		$doc->load($filename);
		return self::getDOMChecksum($doc);
	}
	public final static function getDOMChecksum($doc)	{
		$checksumvalue = null;
		$checksum = $doc->getElementsByTagName('isaacompletechecksum');
		if (intval($checksum->length) == 1)	{
			//Must be a unique existence 
			$checksumvalue = $checksum->item(0)->nodeValue; 
		}
		return $checksumvalue;
	}
	public final static function isDOMIntegrityPassed($doc)	{
		$storedDocumentHashValue = trim(self::getDOMChecksum($doc));
		$calculatedDocumentHashValue = trim(self::calculateDOMChecksum($doc));
		return (strcmp($calculatedDocumentHashValue, $storedDocumentHashValue) == 0);
	}
	public final static function isFileIntegrityPassed($filename)	{
		/*
		return true or false compare calculated and from get 
		*/
		//Load DOM Once 
		if (! file_exists($filename)) return false;
		$doc = new DOMDocument(Object::$xmlVersion);
		$doc->load($filename);
		return self::isDOMIntegrityPassed($doc);
	}
	private static final function documentHash($node1, $referenceHash)	{
		$hashCode = $referenceHash;
		$applyLock = false;
		//Pre Order for Non Binary Tree 
		//Deal with this Node first 
		if ($node1->nodeType == XML_ELEMENT_NODE)	{
			if (ctype_space($node1->tagName))	{
				//retain the value of hashCode, same change not 
			} else if ($node1->tagName == 'isaacompletechecksum')	{
				//Make sure the value of a checksum is not part of the calculation 
				//This is to avoid recursive logic which will not end, logic error avoidance
				$applyLock = true;
			} else	{
				//Other Nodes 
				$val = trim($node1->tagName);
				$hashCode = $hashCode.$val;
				$hashCode=md5($hashCode);
			}
		} else if ($node1->nodeType == XML_TEXT_NODE)	{
			$val = trim($node1->nodeValue);
			$hashCode = $hashCode.$val;
			$hashCode = md5($hashCode);
		}
		//We are done with this Node, Check if child nodes present 
		if (! $applyLock && $node1->hasChildNodes())	{
			foreach ($node1->childNodes as $child1)	{
				$hashCode = self::documentHash($child1, $hashCode);
			}
		}
		return $hashCode;
	}
	public final static function restoreFile($filename)	{
		/*
		restore from a .backup file
		*/
		$promise1 = new Promise();
		$backupfile = $filename.".backup";
		if (file_exists($backupfile))	{
			if (copy($backupfile, $filename))	{
				$promise1->setPromise(true);
			} else {
				$promise1->setReason("Could not create a backup file");
			}			
		} else {
			$promise1->setReason("There is no backup file for supplied file");
		}		
		return $promise1;
	}
}
class ContextManager	{
	public final static function isSystemDefaultAllowed($database, $conn)	{
		$query="SELECT defaultXValue FROM contextManager";
		$result=mysql_db_query($database, $query, $conn) or Object::shootException("Context Manager: Could not load the default Do not care Definition");
		if (mysql_num_rows($result) != 1) Object::shootException("Context Manager: None or Duplicate Final Definition of Do not were found");
		list($defaultXValue)=mysql_fetch_row($result);
		return (intval($defaultXValue) == 1);
	}
	public final static function setSystemDefaultAllowed($database, $conn, $sysAllowedValue)	{
		$query="UPDATE contextManager SET defaultXValue='$sysAllowedValue'";
		$result = mysql_db_query($database, $query, $conn) or Object::shootException("Context Manager: Could not update the Default Definition of Do not Care");
	}
}
class ContextDefinition extends Object {
	private $contextId;
	private $contextCharacter;
	private $contextValue;
	private $database;
	private $conn;
	final public static function getValueFromCharacter($database, $char1, $conn)	{
		$query="SELECT cVal FROM contextDefinition WHERE cChar='$char1'";
		$result = mysql_db_query($database, $query, $conn) or $this->throwMe("Could not fetch value");
		if (mysql_num_rows($result) != 1) $this->throwMe("Multiple or duplicate records for context definitions");
		list($val)=mysql_fetch_row($result);
		return $val;
	}
	final public static function getCharacterFromValue($database, $val, $conn)	{
		$query="SELECT cChar FROM contextDefinition WHERE cVal='$val'";
		$result = mysql_db_query($database, $query, $conn) or $this->throwMe("Could not fetch Character");
		if (mysql_num_rows($result) != 1) $this->throwMe("Multiple or duplicate records for context definitions");
		list($ch1)=mysql_fetch_row($result);
		return $ch1;
	}
	final public static function getDefinitionList($database, $conn)	{
		/*  
		Returns an array of characters based on value as index
		*/
		$list1 = array();
		$query = "SELECT cChar, cVal FROM contextDefinition";
		$result=mysql_db_query($database, $query, $conn) or $this->throwMe("Could not load list");
		while(list($char1, $val)=mysql_fetch_row($result))	{
			$list1[$val] = $char1;
		}
		return $list1;
	}
	public final static function getAvailableViewableColumns()	{
		/*format of data @classname/propertyname,propertytype;propertyname,propertytype;*/
		$lineToView = "@ContextDefinition/contextCharacter,text,k101;contextValue,text,k101";
		return $lineToView;
	}
	public final static function getClassContextName()	{
		return "managecontextdefinition";
	}
	public final static function getUniqueContextId($database, $conn)	{
		$query="SELECT cId FROM contextDefinition";
		$result=mysql_db_query($database, $query, $conn) or Object::shootException('ContextDefinition, Static Get Unique Row Query Failed');
		if (mysql_num_rows($result) != 1)  Object::shootException('ContextDefinition, None or Duplicate Row/Multiple Rows Found');
		list($contextId)=mysql_fetch_row($result);
		return $contextId;
	}
	public final static function add($database, $conn, $cChar, $cVal)	{
		$query="INSERT INTO contextDefinition (cChar, cVal) VALUES('$cChar', '$cVal')";
		$result=mysql_db_query($database, $query, $conn) or Object::shootException('ContextDefinition, Static Addition Failed');
		$query="SELECT LAST_INSERT_ID()";
		$result=mysql_db_query($database, $query, $conn) or Object::shootException('ContextDefinition, Could not Extract Last Inserted ID');
		if (mysql_num_rows($result) != 1) Object::shootException('ContextDefinition, Last Inserted ID is Not Unique');
		list($lastId)=mysql_fetch_row($result);
		return $lastId;
	}
	public function getContextId() {
		return $this->contextId;
	}
	public function getContextCharacter() {
		return $this->contextCharacter;
	}
	public function setContextCharacter($cChar) {
		if (! is_null($cChar))	{
			$this->addToUpdateList("cChar", $cChar);
			$this->contextCharacter = $cChar;
		}
	}
	public function getContextValue() {
		return $this->contextValue;
	}
	public function setContextValue($cVal) {
		if (! is_null($cVal))	{
			$this->addToUpdateList("cVal", $cVal);
			$this->contextValue = $cVal;
		}
	}
	public function getQueryText()	{
		$query="SELECT cId FROM contextDefinition";
		return $query;
	}
	public function __construct($database, $cId, $conn) {
		$this->setMe($database, $cId, $conn);
	}
	public function __destruct()	{
		/*Release the System Resources at this point @Ndimangwa*/
	}
	public function reload() {
		$this->setMe($this->database, $this->contextId, $this->conn);
	}
	public function commitUpdate()	{
		$setList = $this->getUpdateList();
		if ($this->getUpdateListLength() > 0)	{
			$query="UPDATE contextDefinition SET $setList WHERE cId = '$this->cId'";
			$result=mysql_db_query($this->database, $query, $this->conn) or $this->throwMe('ContextDefinition, Row Updation Failed');
		}
	}
	public function commitDelete()	{
		$query="DELETE FROM contextDefinition WHERE cId = '$this->cId'";
		$result=mysql_db_query($this->database, $query, $this->conn) or $this->throwMe('ContextDefinition, Row Deletion Failed');
	}
	public function getClassName()	{
		return "ContextDefinition";
	}
	protected function getProperties()	{
		return "contextId=$this->contextId"."@contextCharacter=$this->contextCharacter"."@contextValue=$this->contextValue";
	}
	protected function getAdvancedProperties()	{
		return "contextId=$this->contextId"."@contextCharacter=$this->contextCharacter"."@contextValue=$this->contextValue";
	}
	public function debug()	{
		
	}
	public function searchMatrix($matrix1)	{
		$string1=$this->getProperties();
		$matrix1->searchText($string1);
		return $matrix1;
	}
	public function processCSV($csvProcessor1)	{
		$string1=$this->getAdvancedProperties();
		$csvProcessor1->appendCSVData($string1);
		return $csvProcessor1;
	}
	protected function setMe($database, $cId, $conn) {
		$query="SELECT cId, cChar, cVal FROM contextDefinition WHERE cId = '$cId'";
		$result=mysql_db_query($database, $query, $conn) or $this->throwMe('ContextDefinition, Object Creation Failed');
		if (mysql_num_rows($result) != 1) $this->throwMe('ContextDefinition, Duplicate or No Record');
		list($cId, $cChar, $cVal)=mysql_fetch_row($result);
		$this->database=$database;
		$this->conn=$conn;
		$this->contextId = $cId;
		if (! is_null($cChar))	{
			$this->contextCharacter = $cChar;
		}
		if (! is_null($cVal))	{
			$this->contextValue = $cVal;
		}
	}
	public function cloneMe($updateDataArray)	{ return -1; }
}
class ContextPosition extends Object {
	private $contextId;
	private $contextName;
	private $characterPosition;
	private $contextCaption;
	private $database;
	private $conn;
	final public static function getContextIdFromName($database, $name, $conn)	{
		$query = "SELECT cId FROM contextPosition WHERE cName='$name'";
		$result = mysql_db_query($database, $query, $conn) or die("Could not fetch this index");
		if (mysql_num_rows($result) != 1) die("Multiple or No Records for $name");
		list($cid) = mysql_fetch_row($result);
		return $cid;
	}
	final public static function getPositionFromName($database, $name, $conn)	{
		$query = "SELECT cPosition FROM contextPosition WHERE cName='$name'";
		$result = mysql_db_query($database, $query, $conn) or die("Could not fetch this position");
		if (mysql_num_rows($result) != 1) die("Multiple or No Records for $name");
		list($pos) = mysql_fetch_row($result);
		return $pos;
	}
	final public static function getNameFromPosition($database, $pos, $conn)	{
		$query = "SELECT cName FROM contextPosition WHERE cPosition='$pos'";
		$result = mysql_db_query($database, $query, $conn) or $this->throwMe("Could not fetch this name");
		if (mysql_num_rows($result) != 1) $this->throwMe("Multiple or records");
		list($cname) = mysql_fetch_row($result);
		return $cname;
	}
	public final static function getAvailableViewableColumns()	{
		/*format of data @classname/propertyname,propertytype;propertyname,propertytype;*/
		$lineToView = "@ContextPosition/contextName,text,k102;characterPosition,text,k102;contextCaption,text,k102";
		return $lineToView;
	}
	public final static function getClassContextName()	{
		return "managecontextposition";
	}
	public final static function getUniqueContextId($database, $conn)	{
		$query="SELECT cId FROM contextPosition";
		$result=mysql_db_query($database, $query, $conn) or Object::shootException('ContextPosition, Static Get Unique Row Query Failed');
		if (mysql_num_rows($result) != 1)  Object::shootException('ContextPosition, None or Duplicate Row/Multiple Rows Found');
		list($contextId)=mysql_fetch_row($result);
		return $contextId;
	}
	public final static function loadAllData($database, $conn)	{
		$query="SELECT cId, cName FROM contextPosition";
		$result=mysql_db_query($database, $query, $conn) or Object::shootException('ContextPosition, Static Load All Data Failed');
		$list=array();
		while(list($__id, $__val)=mysql_fetch_row($result))	{
			$listsize=sizeof($list);
			$list[$listsize]=array();
			$list[$listsize]['id']=$__id;
			$list[$listsize]['val']=$__val;
		}
		return $list;
	}
	public final static function add($database, $conn, $cName, $cPosition, $caption)	{
		$query="INSERT INTO contextPosition (cName, cPosition, caption) VALUES('$cName', '$cPosition', '$caption')";
		$result=mysql_db_query($database, $query, $conn) or Object::shootException('ContextPosition, Static Addition Failed');
		$query="SELECT LAST_INSERT_ID()";
		$result=mysql_db_query($database, $query, $conn) or Object::shootException('ContextPosition, Could not Extract Last Inserted ID');
		if (mysql_num_rows($result) != 1) Object::shootException('ContextPosition, Last Inserted ID is Not Unique');
		list($lastId)=mysql_fetch_row($result);
		return $lastId;
	}
	public function getContextId() {
		return $this->contextId;
	}
	public function getContextName() {
		return $this->contextName;
	}
	public function setContextName($cName) {
		if (! is_null($cName))	{
			$this->addToUpdateList("cName", $cName);
			$this->contextName = $cName;
		}
	}
	public function getCharacterPosition() {
		return $this->characterPosition;
	}
	public function setCharacterPosition($cPosition) {
		if (! is_null($cPosition))	{
			$this->addToUpdateList("cPosition", $cPosition);
			$this->characterPosition = $cPosition;
		}
	}
	public function getContextCaption() {
		return $this->contextCaption;
	}
	public function setContextCaption($caption) {
		if (! is_null($caption))	{
			$this->addToUpdateList("caption", $caption);
			$this->contextCaption = $caption;
		}
	}
	public final static function getQueryText()	{
		$query="SELECT cId FROM contextPosition";
		return $query;
	}
	public function __construct($database, $cId, $conn) {
		$this->setMe($database, $cId, $conn);
	}
	public function __destruct()	{
		/*Release the System Resources at this point @Ndimangwa*/
	}
	public function reload() {
		$this->setMe($this->database, $this->contextId, $this->conn);
	}
	public function commitUpdate()	{
		$setList = $this->getUpdateList();
		if ($this->getUpdateListLength() > 0)	{
			$query="UPDATE contextPosition SET $setList WHERE cId = '$this->cId'";
			$result=mysql_db_query($this->database, $query, $this->conn) or $this->throwMe('ContextPosition, Row Updation Failed');
		}
	}
	public function commitDelete()	{
		$query="DELETE FROM contextPosition WHERE cId = '$this->cId'";
		$result=mysql_db_query($this->database, $query, $this->conn) or $this->throwMe('ContextPosition, Row Deletion Failed');
	}
	public function getClassName()	{
		return "ContextPosition";
	}
	protected function getProperties()	{
		return "contextId=$this->contextId"."@contextName=$this->contextName"."@characterPosition=$this->characterPosition"."@contextCaption=$this->contextCaption";
	}
	protected function getAdvancedProperties()	{
		return "contextId=$this->contextId"."@contextName=$this->contextName"."@characterPosition=$this->characterPosition"."@contextCaption=$this->contextCaption";
	}
	public function debug()	{
		
	}
	public function searchMatrix($matrix1)	{
		$string1=$this->getProperties();
		$matrix1->searchText($string1);
		return $matrix1;
	}
	public function processCSV($csvProcessor1)	{
		$string1=$this->getAdvancedProperties();
		$csvProcessor1->appendCSVData($string1);
		return $csvProcessor1;
	}
	protected function setMe($database, $cId, $conn) {
		$query="SELECT cId, cName, cPosition, caption FROM contextPosition WHERE cId = '$cId'";
		$result=mysql_db_query($database, $query, $conn) or $this->throwMe('ContextPosition, Object Creation Failed');
		if (mysql_num_rows($result) != 1) $this->throwMe('ContextPosition, Duplicate or No Record');
		list($cId, $cName, $cPosition, $caption)=mysql_fetch_row($result);
		$this->database=$database;
		$this->conn=$conn;
		$this->contextId = $cId;
		if (! is_null($cName))	{
			$this->contextName = $cName;
		}
		if (! is_null($cPosition))	{
			$this->characterPosition = $cPosition;
		}
		if (! is_null($caption))	{
			$this->contextCaption = $caption;
		}
	}
	public function cloneMe($updateDataArray)	{ return -1; }
}
class SystemLogs extends Object {
	private $logId;
	private $logName;
	private $database;
	private $conn;
	public final static function searchFromSystemLogs($database, $conn, $searchtext, $count)	{
		$count=intval($count);
		$query="SELECT logName FROM systemlogs ORDER BY logName DESC";
		$result=mysql_db_query($database, $query, $conn) or die("Could not extract from system logs");
		$list=array();
		while (($count > 0) &&(list($logdata)=mysql_fetch_row($result)))	{
			$tlist=explode("%", $logdata);
			$mydate=$tlist[0];
			$username=$tlist[1];
			$target=$tlist[3];
			$actionId=$tlist[2];
			$query="SELECT caption FROM contextPosition WHERE cId='$actionId'";
			$actionResult=mysql_db_query($database, $query, $conn) or die("Could not get a reference action");
			list($action)=mysql_fetch_row($actionResult);
			try	{
				$mydate=new Date($mydate);
			} catch (Exception $e)	{ die("Date could not be Assembled"); }
			$mydate = $mydate->getDay()."/".$mydate->getMonth()."/".$mydate->getYear()." at ".$mydate->getHour().":".$mydate->getMinute().":".$mydate->getSecond();
			$originalString = "@when=$mydate@who=$username@what=$action@towhom=$target";
			if (Object::staticSearchAlgorithm($searchtext, $originalString))	{
				$count--;
				$index=sizeof($list);
				$list[$index]=array();
				$list[$index]['when']=$mydate;
				$list[$index]['who']=$username;
				$list[$index]['what']=$action;
				$list[$index]['towhom']=$target;
			}
		}
		return $list;
	}
	public final static function getAvailableViewableColumns()	{
		/*format of data @classname/propertyname,propertytype;propertyname,propertytype;*/
		$lineToView = "@SystemLogs/logName,text,k103";
		return $lineToView;
	}
	public final static function getClassContextName()	{
		return "managesystemlogs";
	}
	public final static function getUniqueLogId($database, $conn)	{
		$query="SELECT logId FROM systemlogs";
		$result=mysql_db_query($database, $query, $conn) or Object::shootException('SystemLogs, Static Get Unique Row Query Failed');
		if (mysql_num_rows($result) != 1)  Object::shootException('SystemLogs, None or Duplicate Row/Multiple Rows Found');
		list($logId)=mysql_fetch_row($result);
		return $logId;
	}
	public final static function loadAllData($database, $conn)	{
		$query="SELECT logId, logName FROM systemlogs";
		$result=mysql_db_query($database, $query, $conn) or Object::shootException('SystemLogs, Static Load All Data Failed');
		$list=array();
		while(list($__id, $__val)=mysql_fetch_row($result))	{
			$listsize=sizeof($list);
			$list[$listsize]=array();
			$list[$listsize]['id']=$__id;
			$list[$listsize]['val']=$__val;
		}
		return $list;
	}
	public final static function add($database, $conn, $logName)	{
		$query="INSERT INTO systemlogs (logName) VALUES('$logName')";
		$result=mysql_db_query($database, $query, $conn) or Object::shootException('SystemLogs, Static Addition Failed');
		$query="SELECT LAST_INSERT_ID()";
		$result=mysql_db_query($database, $query, $conn) or Object::shootException('SystemLogs, Could not Extract Last Inserted ID');
		if (mysql_num_rows($result) != 1) Object::shootException('SystemLogs, Last Inserted ID is Not Unique');
		list($lastId)=mysql_fetch_row($result);
		return $lastId;
	}
	public function getLogId() {
		return $this->logId;
	}
	public function getLogName() {
		return $this->logName;
	}
	public function setLogName($logName) {
		if (! is_null($logName))	{
			$this->addToUpdateList("logName", $logName);
			$this->logName = $logName;
		}
	}
	public function getQueryText()	{
		$query="SELECT logId FROM systemlogs";
		return $query;
	}
	public function __construct($database, $logId, $conn) {
		$this->setMe($database, $logId, $conn);
	}
	public function __destruct()	{
		/*Release the System Resources at this point @Ndimangwa*/
	}
	public function reload() {
		$this->setMe($this->database, $this->logId, $this->conn);
	}
	public function commitUpdate()	{
		$setList = $this->getUpdateList();
		if ($this->getUpdateListLength() > 0)	{
			$query="UPDATE systemlogs SET $setList WHERE logId = '$this->logId'";
			$result=mysql_db_query($this->database, $query, $this->conn) or $this->throwMe('SystemLogs, Row Updation Failed');
		}
	}
	public function commitDelete()	{
		$query="DELETE FROM systemlogs WHERE logId = '$this->logId'";
		$result=mysql_db_query($this->database, $query, $this->conn) or $this->throwMe('SystemLogs, Row Deletion Failed');
	}
	public function getClassName()	{
		return "SystemLogs";
	}
	protected function getProperties()	{
		return "logId=$this->logId"."@logName=$this->logName";
	}
	protected function getAdvancedProperties()	{
		return "logId=$this->logId"."@logName=$this->logName";
	}
	public function debug()	{
		
	}
	public function searchMatrix($matrix1)	{
		$string1=$this->getProperties();
		$matrix1->searchText($string1);
		return $matrix1;
	}
	public function processCSV($csvProcessor1)	{
		$string1=$this->getAdvancedProperties();
		$csvProcessor1->appendCSVData($string1);
		return $csvProcessor1;
	}
	protected function setMe($database, $logId, $conn) {
		$query="SELECT logId, logName FROM systemlogs WHERE logId = '$logId'";
		$result=mysql_db_query($database, $query, $conn) or $this->throwMe('SystemLogs, Object Creation Failed');
		if (mysql_num_rows($result) != 1) $this->throwMe('SystemLogs, Duplicate or No Record');
		list($logId, $logName)=mysql_fetch_row($result);
		$this->database=$database;
		$this->conn=$conn;
		$this->logId = $logId;
		if (! is_null($logName))	{
			$this->logName = $logName;
		}
	}
	public function cloneMe($updateDataArray)	{ return -1; }
}
class SystemPolicy extends Object {
	private $policyId;
	private $policyClassName;
	private $policyCaption;
	private $root;
	private $extraFilter;
	private $extraInformation;
	private $flags;
	private $database;
	private $conn;
	static $ENABLE_ADD_RECORD = 4;
	static $ENABLE_EDIT_RECORD = 5;
	static $ENABLE_DELETE_RECORD = 6;
	static $ENABLE_VIEW_RECORD = 7;
	static $ENABLE_CREATE_OBJECT = 8;
	/* BEGIN::   You should Add your custom made static methods from this point */
	public final static function getClassPolicyReference($database, $conn, $classname)	{
		$query = "SELECT policyId FROM systemPolicy WHERE className='$classname'";
		$result = mysql_db_query($database, $query, $conn) or Object::shootException("$classname, ClassNameReferencePolicy Failed");
		$policy1 = null;
		if (mysql_num_rows($result) == 1)	{
			list($policyId) = mysql_fetch_row($result);
			try {
				$policy1 = new SystemPolicy($database, $policyId, $conn);
			} catch (Exception $e)	{
				$policy1 = null;
			}
		}
		return $policy1;
	}
	public final static function enableAddRecord($database, $conn, $classname)	{
		$policy1 = SystemPolicy::getClassPolicyReference($database, $conn, $classname);
		if (! is_null($policy1))	{
			$policy1->setFlagAt(SystemPolicy::$ENABLE_ADD_RECORD);
			try {
				$policy1->commitUpdate();
			} catch (Exception $e)	{
				
			}
		}
	}
	public final static function disableAddRecord($database, $conn, $classname)	{
		$policy1 = SystemPolicy::getClassPolicyReference($database, $conn, $classname);
		if (! is_null($policy1))	{
			$policy1->resetFlagAt(SystemPolicy::$ENABLE_ADD_RECORD);
			try {
				$policy1->commitUpdate();
			} catch (Exception $e)	{
				
			}
		}
	}
	public final static function isAddRecordEnabled($database, $conn, $classname)	{
		//If not defined return true 
		$policy1 = SystemPolicy::getClassPolicyReference($database, $conn, $classname);
		return (is_null($policy1) || $policy1->isFlagSetAt(SystemPolicy::$ENABLE_ADD_RECORD));
	}
	public final static function enableEditRecord($database, $conn, $classname)	{
		$policy1 = SystemPolicy::getClassPolicyReference($database, $conn, $classname);
		if (! is_null($policy1))	{
			$policy1->setFlagAt(SystemPolicy::$ENABLE_EDIT_RECORD);
			try {
				$policy1->commitUpdate();
			} catch (Exception $e)	{
				
			}
		}
	}
	public final static function disableEditRecord($database, $conn, $classname)	{
		$policy1 = SystemPolicy::getClassPolicyReference($database, $conn, $classname);
		if (! is_null($policy1))	{
			$policy1->resetFlagAt(SystemPolicy::$ENABLE_EDIT_RECORD);
			try {
				$policy1->commitUpdate();
			} catch (Exception $e)	{
				
			}
		}
	}
	public final static function isEditRecordEnabled($database, $conn, $classname)	{
		//If not defined return true 
		$policy1 = SystemPolicy::getClassPolicyReference($database, $conn, $classname);
		return (is_null($policy1) || $policy1->isFlagSetAt(SystemPolicy::$ENABLE_EDIT_RECORD));
	}
	public final static function enableDeleteRecord($database, $conn, $classname)	{
		$policy1 = SystemPolicy::getClassPolicyReference($database, $conn, $classname);
		if (! is_null($policy1))	{
			$policy1->setFlagAt(SystemPolicy::$ENABLE_DELETE_RECORD);
			try {
				$policy1->commitUpdate();
			} catch (Exception $e)	{
				
			}
		}
	}
	public final static function disableDeleteRecord($database, $conn, $classname)	{
		$policy1 = SystemPolicy::getClassPolicyReference($database, $conn, $classname);
		if (! is_null($policy1))	{
			$policy1->resetFlagAt(SystemPolicy::$ENABLE_DELETE_RECORD);
			try {
				$policy1->commitUpdate();
			} catch (Exception $e)	{
				
			}
		}
	}
	public final static function isDeleteRecordEnabled($database, $conn, $classname)	{
		//If not defined return true 
		$policy1 = SystemPolicy::getClassPolicyReference($database, $conn, $classname);
		return (is_null($policy1) || $policy1->isFlagSetAt(SystemPolicy::$ENABLE_DELETE_RECORD));
	}
	public final static function enableViewRecord($database, $conn, $classname)	{
		$policy1 = SystemPolicy::getClassPolicyReference($database, $conn, $classname);
		if (! is_null($policy1))	{
			$policy1->setFlagAt(SystemPolicy::$ENABLE_VIEW_RECORD);
			try {
				$policy1->commitUpdate();
			} catch (Exception $e)	{
				
			}
		}
	}
	public final static function disableViewRecord($database, $conn, $classname)	{
		$policy1 = SystemPolicy::getClassPolicyReference($database, $conn, $classname);
		if (! is_null($policy1))	{
			$policy1->resetFlagAt(SystemPolicy::$ENABLE_VIEW_RECORD);
			try {
				$policy1->commitUpdate();
			} catch (Exception $e)	{
				
			}
		}
	}
	public final static function isViewRecordEnabled($database, $conn, $classname)	{
		//If not defined return true 
		$policy1 = SystemPolicy::getClassPolicyReference($database, $conn, $classname);
		return (is_null($policy1) || $policy1->isFlagSetAt(SystemPolicy::$ENABLE_VIEW_RECORD));
	}
	public final static function enableCreateObject($database, $conn, $classname)	{
		$policy1 = SystemPolicy::getClassPolicyReference($database, $conn, $classname);
		if (! is_null($policy1))	{
			$policy1->setFlagAt(SystemPolicy::$ENABLE_CREATE_OBJECT);
			try {
				$policy1->commitUpdate();
			} catch (Exception $e)	{
				
			}
		}
	}
	public final static function disableCreateObject($database, $conn, $classname)	{
		$policy1 = SystemPolicy::getClassPolicyReference($database, $conn, $classname);
		if (! is_null($policy1))	{
			$policy1->resetFlagAt(SystemPolicy::$ENABLE_CREATE_OBJECT);
			try {
				$policy1->commitUpdate();
			} catch (Exception $e)	{
				
			}
		}
	}
	public final static function isCreateObjectEnabled($database, $conn, $classname)	{
		//If not defined return true 
		$policy1 = SystemPolicy::getClassPolicyReference($database, $conn, $classname);
		return (is_null($policy1) || $policy1->isFlagSetAt(SystemPolicy::$ENABLE_CREATE_OBJECT));
	}
	/* END  ::   Your custom made static methods should be above this line      */
	public final static function getAvailableViewableColumns()	{
		/*format of data @classname/propertyname,propertytype,namespaceTag;propertyname,propertytype,namespaceTag;*/
		$lineToView = "@SystemPolicy/policyClassName,text,k104;policyCaption,text,k104;root,boolean,k104;extraFilter,text,k104;extraInformation,text,k104;flags,text,k104";
		return $lineToView;
	}
	public final static function getClassContextName()	{
		return "managesystempolicy";
	}
	public final static function getUniquePolicyId($database, $conn)	{
		$query="SELECT policyId FROM systemPolicy";
		$result=mysql_db_query($database, $query, $conn) or Object::shootException('SystemPolicy, Static Get Unique Row Query Failed');
		if (mysql_num_rows($result) != 1)  Object::shootException('SystemPolicy, None or Duplicate Row/Multiple Rows Found');
		list($policyId)=mysql_fetch_row($result);
		return $policyId;
	}
	public final static function loadAllData($database, $conn)	{
		$query="SELECT policyId, className FROM systemPolicy";
		$result=mysql_db_query($database, $query, $conn) or Object::shootException('SystemPolicy, Static Load All Data Failed');
		$list=array();
		while(list($__id, $__val)=mysql_fetch_row($result))	{
			$listsize=sizeof($list);
			$list[$listsize]=array();
			$list[$listsize]['id']=$__id;
			$list[$listsize]['val']=$__val;
		}
		return $list;
	}
	public final static function add($database, $conn, $className, $policyCaption, $root, $extraFilter, $extraInformation, $flags)	{
		$query="INSERT INTO systemPolicy (className, policyCaption, root, extraFilter, extraInformation, flags) VALUES('$className', '$policyCaption', '$root', '$extraFilter', '$extraInformation', '$flags')";
		$result=mysql_db_query($database, $query, $conn) or Object::shootException('SystemPolicy, Static Addition Failed');
		$query="SELECT LAST_INSERT_ID()";
		$result=mysql_db_query($database, $query, $conn) or Object::shootException('SystemPolicy, Could not Extract Last Inserted ID');
		if (mysql_num_rows($result) != 1) Object::shootException('SystemPolicy, Last Inserted ID is Not Unique');
		list($lastId)=mysql_fetch_row($result);
		return $lastId;
	}
	public function getId() {
		return $this->policyId;
	}
	public function getPolicyId() {
		return $this->policyId;
	}
	public function getPolicyClassName() {
		return $this->policyClassName;
	}
	public function setPolicyClassName($className) {
		if (! is_null($className))	{
			$this->addToUpdateList("className", $className);
			$this->policyClassName = $className;
		}
	}
	public function getPolicyCaption() {
		return $this->policyCaption;
	}
	public function setPolicyCaption($policyCaption) {
		if (! is_null($policyCaption))	{
			$this->addToUpdateList("policyCaption", $policyCaption);
			$this->policyCaption = $policyCaption;
		}
	}
	public function isRoot() {
		return $this->root;
	}
	public function setRoot($root) {
		if (! is_null($root))	{
			$this->addToUpdateList("root", $root);
			$this->root = ($root == "1");
		}
	}
	public function getExtraFilter() {
		return $this->extraFilter;
	}
	public function setExtraFilter($extraFilter) {
		if (! is_null($extraFilter))	{
			$this->addToUpdateList("extraFilter", $extraFilter);
			$this->extraFilter = $extraFilter;
		}
	}
	public function getExtraInformation() {
		return $this->extraInformation;
	}
	public function setExtraInformation($extraInformation) {
		if (! is_null($extraInformation))	{
			$this->addToUpdateList("extraInformation", $extraInformation);
			$this->extraInformation = $extraInformation;
		}
	}
	public function getFlags() {
		return $this->flags;
	}
	public function setFlags($flags) {
		if (! is_null($flags))	{
			$this->addToUpdateList("flags", $flags);
			$this->flags = $flags;
		}
	}
	public final static function getQueryText()	{
		$query="SELECT policyId FROM systemPolicy";
		return $query;
	}
	public function __construct($database, $policyId, $conn) {
		$this->setMe($database, $policyId, $conn);
	}
	public function __destruct()	{
		/*Release the System Resources at this point @Ndimangwa*/
	}
	public function reload() {
		$this->setMe($this->database, $this->policyId, $this->conn);
	}
	public function commitUpdate()	{
		$setList = $this->getUpdateList();
		if ($this->getUpdateListLength() > 0)	{
			$query="UPDATE systemPolicy SET $setList WHERE policyId = '$this->policyId'";
			$result=mysql_db_query($this->database, $query, $this->conn) or $this->throwMe('SystemPolicy, Row Updation Failed');
		}
	}
	public function commitDelete()	{
		$query="DELETE FROM systemPolicy WHERE policyId = '$this->policyId'";
		$result=mysql_db_query($this->database, $query, $this->conn) or $this->throwMe('SystemPolicy, Row Deletion Failed');
	}
	public function getClassName()	{
		return "SystemPolicy";
	}
	protected function getProperties()	{
		return "policyId=$this->policyId"."@policyClassName=$this->policyClassName"."@policyCaption=$this->policyCaption"."@extraFilter=$this->extraFilter"."@extraInformation=$this->extraInformation"."@flags=$this->flags"."@root=$this->root";
	}
	protected function getAdvancedProperties()	{
		return "1002.policyId=".str_replace("@", "_kdAt_", str_replace("=", "_kdEq_", $this->policyId))."@1002.policyClassName=".str_replace("@", "_kdAt_", str_replace("=", "_kdEq_", $this->policyClassName))."@1002.policyCaption=".str_replace("@", "_kdAt_", str_replace("=", "_kdEq_", $this->policyCaption))."@1002.extraFilter=".str_replace("@", "_kdAt_", str_replace("=", "_kdEq_", $this->extraFilter))."@1002.extraInformation=".str_replace("@", "_kdAt_", str_replace("=", "_kdEq_", $this->extraInformation))."@1002.flags=".str_replace("@", "_kdAt_", str_replace("=", "_kdEq_", $this->flags))."@1002.root=".str_replace("@", "_kdAt_", str_replace("=", "_kdEq_", $this->root));
	}
	public function debug()	{
		$string1 = $this->getProperties();
		$keyValueArr = explode("@",$string1);
		echo "********************************************************************************\n";
		$displayLine = "****** CLASSNAME: SystemPolicy (NAMESPACETAG = 1002) ";
		$linesize = strlen($displayLine);
		for ($i=$linesize; $i < 80; $i++)	{
			$displayLine = $displayLine."*";
		}
		echo $displayLine."\n";
		foreach ($keyValueArr as $aKeyVal)	{
			$kvArr = explode("=",$aKeyVal);
			$val = "---------";
			$key = $kvArr[0];
			if (isset($kvArr[1])) $val=$kvArr[1];
			$displayLine="** ".$key."  =  ".$val;
			$linesize=strlen($displayLine);
			for ($i=$linesize; $i < 79; $i++)	{
				$displayLine = $displayLine." ";
			}
			$displayLine = $displayLine."*";
			echo $displayLine."\n";
		}
		echo "********************************************************************************\n";
	}
	public function searchMatrix($matrix1)	{
		$string1=$this->getProperties();
		$matrix1->searchText($string1);
		return $matrix1;
	}
	public function processCSV($csvProcessor1)	{
		$string1=$this->getAdvancedProperties();
		$csvProcessor1->appendCSVData($string1);
		return $csvProcessor1;
	}
	protected function setMe($database, $policyId, $conn) {
		$query="SELECT policyId, className, policyCaption, root, extraFilter, extraInformation, flags FROM systemPolicy WHERE policyId = '$policyId'";
		$result=mysql_db_query($database, $query, $conn) or $this->throwMe('SystemPolicy, Object Creation Failed');
		if (mysql_num_rows($result) != 1) $this->throwMe('SystemPolicy, Duplicate or No Record');
		list($policyId, $className, $policyCaption, $root, $extraFilter, $extraInformation, $flags)=mysql_fetch_row($result);
		$this->database=$database;
		$this->conn=$conn;
		$this->policyId = $policyId;
		if (! is_null($className))	{
			$this->policyClassName = $className;
		}
		if (! is_null($policyCaption))	{
			$this->policyCaption = $policyCaption;
		}
		if (! is_null($root))	{
			$this->root = ($root == "1");
		}
		if (! is_null($extraFilter))	{
			$this->extraFilter = $extraFilter;
		}
		if (! is_null($extraInformation))	{
			$this->extraInformation = $extraInformation;
		}
		if (! is_null($flags))	{
			$this->flags = $flags;
		}
	}
	public function cloneMe($updateDataArray)	{
		$masterDataArray = array();
		$query="SELECT className, policyCaption, root, extraFilter, extraInformation, flags FROM systemPolicy WHERE policyId = '$this->policyId'";
		$result=mysql_db_query($this->database, $query, $this->conn) or $this->throwMe('SystemPolicy, [Clone] Data Pulling Failed');
		if (mysql_num_rows($result) != 1) $this->throwMe('SystemPolicy, [Clone] Duplicate or No Record');
		list($className, $policyCaption, $root, $extraFilter, $extraInformation, $flags)=mysql_fetch_row($result);
		$masterDataArray['className'] = $className;
		$masterDataArray['policyCaption'] = $policyCaption;
		$masterDataArray['root'] = $root;
		$masterDataArray['extraFilter'] = $extraFilter;
		$masterDataArray['extraInformation'] = $extraInformation;
		$masterDataArray['flags'] = $flags;
		foreach ($updateDataArray as $dtKey => $dtVal)	{
			if (isset($masterDataArray[$dtKey]))	{
				$masterDataArray[$dtKey] = $dtVal;
			}
		}
		$columnList = "";
		$dataList = "";
		$cloneCounter = 0;
		foreach ($masterDataArray as $__key => $__val)	{
			if (! is_null($__val))	{
				if ($cloneCounter == 0)	{
					$columnList = $__key;
					$dataList = "'".$__val."'";
				} else {
					$columnList = $columnList.", ".$__key;
					$dataList = $dataList.", '".$__val."'";
				}
				$cloneCounter++;
			}
		}
		$query="INSERT INTO systemPolicy ($columnList) VALUES ($dataList)";
		$result = mysql_db_query($this->database, $query, $this->conn) or $this->throwMe("SystemPolicy [Clone] Could not push record to database");
		$query = "SELECT LAST_INSERT_ID()";
		$result = mysql_db_query($this->database, $query, $this->conn) or $this->throwMe("SystemPolicy [Clone] Could not Extract Last Insert Id");
		if (mysql_num_rows($result) != 1) $this->throwMe("SystemPolicy [Clone] Last Insert Id not unique");
		list($lastInsertId)=mysql_fetch_row($result);
		return $lastInsertId;
	}
}
?>
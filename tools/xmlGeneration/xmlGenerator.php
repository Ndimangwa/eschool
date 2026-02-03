<?php 
/*
This progrum was written by Ndimangwa Fadhili

Symbols

element 
{
	element
		[
			text
		]
		<
			variable ie $good
		>
}
Algorithm

*/
class Stack	{
	private $pointer;
	private $data;
	public function __construct()	{
		$this->pointer=0;
		$this->data=array();
	}
	public function push($ele)	{
		$this->data[$this->pointer]=$ele;
		$this->pointer++;
	}
	public function pop()	{
		$this->pointer--;
		return $this->data[$this->pointer];
	}
	public function getTopElement()	{ 
		if ($this->pointer == 0) return null;
	return 	$this->data[$this->pointer-1]; }
	public function getPointer()	{ return $this->pointer; }
}
if (! (isset($argv[1]) && isset($argv[2]))) die("Command Syntax \"php xmlGenerator.php inputfile outputfile\"");
$infile=$argv[1];
$outfile=$argv[2];
if (! file_exists($infile)) die("\nInput file not found\n");
$file1=fopen($infile, "r");
$file2=fopen($outfile, "w");
$iselement=true; //istext
$closingLine="";
$lastElement="doc";
$mytext="";
$stack1=new Stack();
$stack1->push("doc");
$blockStack1=new Stack();
$textVariable=false;
$preserveCode=false;
if ($file1) {
    while (($line = fgets($file1)) !== false) {
        $line=trim($line);
		if ($line=="<?php")	{
			$preserveCode=true;
		} else if ($line=="?>")	{
			$preserveCode=false;
		} else if ($preserveCode)	{
			$myline=$line."\n";
			fwrite($file2, $myline);
		} else if ($line=="{")	{
			$iselement=true;
			$stack1->push($lastElement);
			$blockStack1->push($closingLine);
			$closingLine="";
		} else if ($line=="}")	{
			$ele1=$stack1->pop();
			/*Need to save the previous closing line before overwrite*/
			if ($closingLine != "") {
				$closingLine .= ";\n";
				fwrite($file2, $closingLine);
			}
			$closingLine=$blockStack1->pop();
		} else if ($line=="[")	{
			$textVariable=false;
			$iselement=false;
			$stack1->push($lastElement);
		} else if ($line=="]")	{
			$iselement=true;
		} else if ($line=="<")	{
			$textVariable=true;
			$iselement=false;
			$stack1->push($lastElement);
		} else if ($line==">")	{
			$iselement=true;
		} else	{
			if($iselement)	{
				if ($closingLine != "") { 
					$closingLine .= ";\n";
					fwrite($file2, $closingLine);
				}
				$mytext="\$".$line."=\$"."doc->createElement('".$line."');\n";
				fwrite($file2, $mytext);
				$closingLine="\$".$stack1->getTopElement()."->appendChild("."\$".$line.");";
				$lastElement=$line;
			} else if ($textVariable) {
				$ele1=$stack1->pop();
				$mytext="\$".$ele1."->appendChild(\$"."doc->createTextNode(".$line."));\n";
				fwrite($file2, $mytext);
			} else	{
				$ele1=$stack1->pop();
				$mytext="\$".$ele1."->appendChild(\$"."doc->createTextNode(\"".$line."\"));\n";
				fwrite($file2, $mytext);
			}
		}
    }
} else {
    die("\nError opening a file\n");
} 
fwrite($file2, $closingLine);
fclose($file1);
fclose($file2);
 ?>
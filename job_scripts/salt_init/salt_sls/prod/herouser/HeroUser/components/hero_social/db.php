<?php
function GetCurPath($AddPath)
{
	$curpath = $_SERVER["DOCUMENT_ROOT"].$_SERVER["PHP_SELF"];
	
	$curpath = str_replace("\\\\","\\",$curpath);
	$curpath = str_replace("//","/",$curpath);
	
	$Index = strrpos($curpath, "/");
	$result = $curpath;
	if($Index != 0)
	{
		$result = substr($curpath, 0, $Index);
	}
	if($AddPath != NULL)
		return $result."/".$AddPath;
	return $result;
}

$conn = mysql_connect("127.0.0.1", "CKing", "batian");
$state = mysql_select_db("hero", $conn);
mysql_query("set names 'utf8'");
?>
<?php

include(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');

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

$conn = mysql_connect($_SC['dbhost'], $_SC['dbuser'], $_SC['dbpw']);
$state = mysql_select_db($_SC['dbname'], $conn);
mysql_query("set names 'utf8'");
//设置时区
date_default_timezone_set('Asia/Shanghai');
function mkPostTime($date,$isNow2= '') {
  $dateInfo = preg_split('/[- :]/',$date);
  $year=intval($dateInfo[0]);//取得年份
  $month=intval($dateInfo[1]);//取得月份
  $day=intval($dateInfo[2]);//取得几号  
  if ($isNow2 == 1) {
     $date_now = mktime(23,59,59,$month,$day,$year);
  } else {
     $date_now = mktime(0,0,0,$month,$day,$year);
  }
  return $date_now;
}

function getSearChDate($typeid) {
	if($typeid == 2) {
		$rq1 = date("Y-m-d",time()-24*60*60);
		$rq2 = date("Y-m-d",time()-24*60*60);
		//echo date("Y-m-d",time()-2*24*60*60);
	} elseif ($typeid == 3) {
		$rq1 = date("Y-m-d",time()-10*24*60*60);
		$rq2 = date("Y-m-d",time());
	} elseif ($typeid == 4) {
		$rq1 = date("Y-m-d",time()-20*24*60*60);
		$rq2 = date("Y-m-d",time());
	} elseif ($typeid == 5) {//本月
		//echo date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),1,date("Y"))),"\n";
		//echo date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("t"),date("Y"))),"\n"; 
		//exit;
		$rq1 = date("Y-m-d",mktime(0, 0 , 0,date("m"),1,date("Y")));
		$rq2 = date("Y-m-d",mktime(23,59,59,date("m"),date("t"),date("Y")));
	} elseif ($typeid == 6) {
		//echo date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m")-1,1,date("Y"))),"\n";
		//echo date("Y-m-d H:i:s",mktime(23,59,59,date("m") ,0,date("Y"))),"\n"; 
		$rq1 = date("Y-m-d",mktime(0, 0 , 0,date("m")-1,1,date("Y")));
		$rq2 = date("Y-m-d",mktime(23,59,59,date("m") ,0,date("Y")));
	} elseif ($typeid == 7) {
		$rq1 = date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1-7,date("Y")));
		$rq2 = date("Y-m-d",mktime(23,59,59,date("m"),date("d")-date("w")+7-7,date("Y")));
	} else {
	    $rq1 = date("Y-m-d",time());
		$rq2 = date("Y-m-d",time());
	}
	$value['rq1'] = $rq1;
	$value['rq2'] = $rq2;
	return $value;
}
?>
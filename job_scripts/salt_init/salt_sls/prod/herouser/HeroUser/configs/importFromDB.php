<?php
set_time_limit(0);
//程序目录
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR.'../');
include(S_ROOT.'config.php');

mysql_connect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw']);
mysql_select_db($_SC['dbname']);
mysql_query("set names 'utf8'");

function GetStringFromTable($tbname, $varname, $keynames) {
  $sql = "SELECT * FROM `".$tbname."`";
  $res = mysql_query($sql);
  if(!$res){
  	echo "select $tbname fails\n<BR>";
  	return ;
  }

  $content = "<?php\n\$GLOBALS['$varname'] = array(\n";
  $fieldcount = mysql_num_fields($res);
  $rowcount = mysql_num_rows($res);
  $rowidx = 0;
  while ($row = mysql_fetch_array($res)) {
    $keyarray = array();
    for($i=0,$l=count($keynames);$i<$l;++$i) {
      $keyarray[] = $row[$keynames[$i]];
    }
    $content .= "  '".implode('_', $keyarray)."'=>array(";
    for($i=0;$i<$fieldcount;++$i) {
      $content .= "'".mysql_field_name($res,$i) ."'=>";//mysql_escape_string
      if('ol_item_odds' == $tbname){
      	$row[$i] = is_null($row[$i])?0:$row[$i];
      }
      $tp = mysql_field_type($res, $i) ;
      if($tp != "int" && $tp != "real") $content .= "'";
      $content .= $row[$i];
      if($tp != "int" && $tp != "real") $content .= "'";
      if($i < $fieldcount-1) $content .= ",";
    }
    $content .= "  )";
    if($rowidx < $rowcount - 1) $content .= ",\n";
    else $content .= "\n";
    ++$rowidx;
  }
  $content .= ");\n?>";
  file_put_contents($varname.'.php', $content);
  unset($content);
}

GetStringFromTable('ol_items', "G_Items", array('ItemID'));
//GetStringFromTable('ol_quests', "G_Quests", array('QuestID'));
GetStringFromTable('ol_card_odds', "G_StageCardOdds_tb", array('difficulty','stage','substage'));
GetStringFromTable('ol_item_odds', "G_ItemOdds_tb", array('typeId'));

include "G_ItemOdds_tb.php";
//$stageodds = $GLOBALS['G_StageCardOdds_tb'];
$itemodds = $GLOBALS['G_ItemOdds_tb'];
$tmpArray = array();
foreach($itemodds as $id=>$arr) {
	for($i=1;$i<=20;++$i) {
    $itemid = $arr['item'.$i];
    if($itemid == 0 ) continue;
		if(isset($tmpArray[$id][$itemid])){
			$tmpArray[$id][$itemid] += $arr['odds'.$i];
		}else{
			$tmpArray[$id][$itemid] = $arr['odds'.$i];
		}
	}
}

$content = var_export($tmpArray,true);
file_put_contents('G_ItemOdds.php', "<?php \n\$GLOBALS['G_ItemOdds'] =".$content.";\n?>");


include "G_StageCardOdds_tb.php";
$cardodds = $GLOBALS['G_StageCardOdds_tb'];
$tmpArray = array();
foreach($cardodds as $id=>$arr) {
	for($i=1;$i<=6;++$i){
		if(!isset($arr['card'.$i]))	continue;	
		$tmpArray[$id][$arr['card'.$i]]= $arr['odds'.$i];
	}
}

$content = var_export($tmpArray,true);
file_put_contents('G_StageCardOdds.php', "<?php \n\$GLOBALS['G_StageCardOdds'] =".$content.";\n?>");
?>
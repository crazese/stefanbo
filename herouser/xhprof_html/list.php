<?php
$pergroupnum = 30; //  nginx默认4k，chrome可以8k，ie2k，ff 65535。这里用4k做上限.4000/14= 285
$path = dirname(__FILE__) . '/../xhprof_log/';
$dir='';
if(isset($_REQUEST['dir'])) {
  $dir= $_REQUEST['dir'];
  $path.=$dir.'/';
}

if(isset($_REQUEST['delete'])) {
  system('rm -f '.$path.'*.*');
  header("Location: list.php?dir=$dir");
  die;
}

if(isset($_REQUEST['action'])  && $_REQUEST['action']=='del' && isset($_REQUEST['name'])) {
  system('rm -f '.$path.$_REQUEST['name']);
  header("Location: list.php?dir=$dir");
  die;
}

$group = true;
if(isset($_REQUEST['showall']))  $group = false;
$showall = !$group;

if(isset($_REQUEST['action'])  && $_REQUEST['action']=='delgroup' && isset($_REQUEST['name'])) {
  system('rm -f '.$path.'*.'.$_REQUEST['name']);
  header("Location: list.php?dir=$dir".($showall?'&showall=1':''));
  die;
}

if($dir)  {
  echo '当前文件夹为['.$dir.']&nbsp;&nbsp;';
  echo '<a href="list.php">回日志主目录</a>&nbsp;&nbsp;';
}
else  
  echo '当前文件夹为日志主目录&nbsp;&nbsp;';

if($group)  {
  echo '当前为分组显示, <a href="list.php?dir='.$dir.'&showall=1">切换为全部显示</a>';
}
else {
  echo '当前为全部显示, <a href="list.php?dir='.$dir.'">切换为分组显示</a>&nbsp;&nbsp;&nbsp;&nbsp;<button onclick="compare()">对比所选项</button>&nbsp;&nbsp;<button onclick="summary()">汇总所选项</button>';
}
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script>
function compare(){
  var files = document.getElementsByName("chk_file");
  var url="index.php?dir=<?php echo $dir ?>&";
  var j=1;
  for (var i = 0; i < files.length; i++) {
    if(!files[i].checked)  continue;
    var file = files[i].value;
    if(j==1)  url += "run1="+file.split(".")[0];
    else if(j==2) {
      url += "&run2="+file.split(".")[0]+"&source="+file.split(".")[1];
      window.open(url,'prof');
      break;
    }
    ++j;
  }
}

function summary(){
  var files = document.getElementsByName("chk_file");
  var url="index.php?dir=<?php echo $dir ?>&";
  var j=0;
  var source = "";
  for (var i = 0; i < files.length; i++) {
    if(!files[i].checked)  continue;
    var file = files[i].value;
    if(j==0)  {
      source = file.split(".")[1];
      url += "source="+source+"&run=";
    }
    else {
      if(file.split(".")[1] != source)  continue;
    }
    ++j;
    url += file.split(".")[0] +",";
  }
  if(j<1) return;
  url = url.substring(0,url.length-1);
  window.open(url,'prof');
}

function summaryByName(source,page) {
  var files = document.getElementsByName("chk_file");
  var url="index.php?dir=<?php echo $dir ?>&source="+source+"&run=";
  var j=0;
  var pergroupnum = <?php echo $pergroupnum ?>;
  var max=files.length;//Math.min(files.length,pergroupnum*(page+1));
  var checkednum = 0;
  for (var i = 0; i < max; ++i) {
    var file = files[i].value;
    if(file.split(".")[1] != source)  continue;
    ++j;
    if(j>page*pergroupnum) {
      url += file.split(".")[0] + ",";
      ++checkednum;
      if(checkednum>=pergroupnum)  break;
    }
  }
  if(checkednum<1) return;
  url = url.substring(0,url.length-1);
  window.open(url,'prof');
}
</script>
&nbsp;&nbsp;&nbsp;&nbsp;<a href='list.php?dir=<?php echo $dir ?>&delete=1'>删除全部</a><br/><br/>
<?php
$files = scandir($path);
$arrshow = array();
$fileignore = 0;
if($group)  {
  foreach($files as $file){
    $filename = $path . $file;
    if(is_file($filename)) {
        $arr = explode('.',$file);
        $ftime = filemtime($filename);
        if(isset($arrshow[$arr[1]])) { 
          ++ $fileignore; 
          if($ftime > $arrshow[$arr[1]]['time_max']) {
            $arrshow[$arr[1]]['time_max'] = $ftime;
            $arrshow[$arr[1]]['sample'] = $arr[0];
          }
          if($ftime < $arrshow[$arr[1]]['time_min'])  $arrshow[$arr[1]]['time_min'] = $ftime;
          $arrshow[$arr[1]]['num'] +=1;
          $arrshow[$arr[1]]['ids'][] = $arr[0];
          $arrshow[$arr[1]]['times'][] = $ftime;
        }
        else  {
          $arrshow[$arr[1]] = array('time_min'=>$ftime,'time_max'=>$ftime,'num'=>1,'sample'=>$arr[0],'ids'=>array($arr[0]),'times'=>array($ftime));
        }
    }
    else {
      if($file=='.' || $file=='..') continue;
      echo "<a href=list.php?dir=$file>$file</a><br/>";
    }
  }
  $arrnum = array();
  foreach($arrshow as $groupname=>$groupinfo) {
    $arrnum[] = $groupinfo['num'];
  }
  array_multisort($arrnum,SORT_DESC,$arrshow);
  foreach($arrshow as $groupname=>$groupinfo) {
    $num = count($groupinfo['ids']);
    echo  '<a target=prof href=index.php?dir='.$dir.'&run='.$groupinfo['sample'].'&source='.$groupname.'>'.$groupname.'</a>&nbsp;&nbsp;'.date("m-d H:i:s", $groupinfo['time_min']).' ~ '. date("m-d H:i:s", $groupinfo['time_max']) .'&nbsp;&nbsp;('. $groupinfo['num'].'结果)&nbsp;&nbsp;<a href=?dir='.$dir.'&group=1&action=delgroup&name='.$arr[1].'>删除同类</a><br>';
    for($i=0,$l=ceil($num/$pergroupnum);$i<$l;++$i) {
      $i1 = $i*$pergroupnum+1;
      $i2 = min($i*$pergroupnum+$pergroupnum,$num);
      echo '&nbsp;&nbsp;&nbsp;&nbsp;'.date("H:i:s", $groupinfo['times'][$i1-1]).'&nbsp;~&nbsp;'.date("H:i:s", $groupinfo['times'][$i2-1]).'&nbsp;&nbsp;<a href="index.php?dir='.$dir.'&source='.$groupname.'&run='.implode(',',array_slice($groupinfo['ids'],$i*$pergroupnum,$pergroupnum)).'" target="prof">汇总('.$i1.'~'.$i2.')</a><br/>';
    }
    echo '<br/>';
  }
  echo "<br/>有$fileignore 个文件未显示 &nbsp;&nbsp;<a href='list.php?dir=$dir&showall=1'>切换为全部显示</a><br/>";
}
else {
  $arrshow = array();
  foreach($files as $file){
    $filename = $path . $file;
    if(is_file($filename)) {
        $arr = explode('.',$file);
        
        $ftime = filemtime($filename);
        if(isset($arrshow[$arr[1]])) { 
          ++ $fileignore; 
          if($ftime > $arrshow[$arr[1]]['time_max']) {
            $arrshow[$arr[1]]['time_max'] = $ftime;
            $arrshow[$arr[1]]['sample'] = $arr[0];
          }
          if($ftime < $arrshow[$arr[1]]['time_min'])  $arrshow[$arr[1]]['time_min'] = $ftime;
          $arrshow[$arr[1]]['num'] +=1;
          $arrshow[$arr[1]]['ids'][] = $arr[0];
        }
        else  {
          $arrshow[$arr[1]] = array('time_min'=>$ftime,'time_max'=>$ftime,'num'=>1,'sample'=>$arr[0],'ids'=>array($arr[0]));
        }
        $num = count($arrshow[$arr[1]]['ids']);
        $i = ceil($num/$pergroupnum)-1;
        
        echo  '<input type="checkbox" name="chk_file" check=false value="'.$file.'"/>&nbsp;'.date("m-d H:i:s", filemtime($filename)).'&nbsp;&nbsp;<a target=prof href=index.php?dir='.$dir.'&run='.$arr[0].'&source='.$arr[1].'>'.$arr[1].'</a>&nbsp;&nbsp;<a href="javascript:summaryByName(\''.$arr[1].'\','.$i.');">汇总('.($i*$pergroupnum+1).'-'.($i*$pergroupnum+$pergroupnum).')</a>&nbsp;&nbsp;<a href=?dir='.$dir.'&action=del&name='.$file.'>删除单个</a>&nbsp;&nbsp;<a href=?dir='.$dir.'&action=delgroup&name='.$arr[1].'>删除同类</a><br/>';
    }
    else {
      if($file=='.' || $file=='..') continue;
      echo "<a href=list.php?dir=$file>$file</a><br/>";
    }
  }
}
?>
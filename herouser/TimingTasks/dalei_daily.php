<?php
// 打擂开放等级
$dalei_level = 7;

// 必需保证处理程序不会假死在系统中
set_time_limit(1800);
$time1 = microtime(true);
date_default_timezone_set('PRC');

$debug = false;
if(isset($_REQUEST['debug'])) $debug = true;
include(dirname(__FILE__) . DIRECTORY_SEPARATOR .'../config.php');
require(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR .'includes/class_memcacheAdapter.php');

$current_time = time();
$log_path = $_LOG_INFO['path'] . 'dalei_' . date('Y_m_d_H_i_s', $current_time);
$logHandle = fopen($log_path, 'a');

mysql_connect($_SC['dbhost'],$_SC['dbuser'],$_SC['dbpw']);
mysql_select_db($_SC['dbname']);

$mc = new MemcacheAdapter_Memcached;
$mc->addServer($MemcacheList[0], $Memport);

if(!$debug){
	$flag = $mc->add(MC.'_dalei_process_lock', 1, false, 1800);
	if(!$flag){
		sleep(5);
		$flag = $mc->add(MC.'_dalei_process_lock', 1, false, 1800);
	}

	if(!$flag){
		echo "process mem lock";
		exit;
	}

	// 28分到30分之间为时间缓冲
	$dalei_process_time = mktime(LEITAI_JIESUAN_SHIJIAN -1 , 28, 0, date('m'), date('d'), date('Y'));
	if(time()<$dalei_process_time){
		echo "time limit";
		while(!$mc->delete(MC.'_dalei_process_lock')){
			sleep(1);
		};
		exit;
	}

	$lck_day = date('Y-m-d');
	$lck_time = date('Y-m-d H:i:s');
	$sql = "insert into ol_dalei_process_lock values('{$lck_day}', '{$lck_time}')";
	$result = mysql_query($sql);
	if(!$result){
		echo 'process day lock';
		while(!$mc->delete(MC.'_dalei_process_lock')){
			sleep(1);
		};
		exit;
	}

	// 保证玩家没有打擂,等待玩家请求处理完成
	// 设置时间缓冲
	sleep(120);
}

//先修正一下数据
$sql = "update ol_dalei_jf_count a set a.num=(select count(*) from ol_dalei_jf b where b.jf=a.jf)";
mysql_query($sql) or die(mysql_error());
//echo "time1:", microtime(true) - $time1,"\n";

$md = date("m月d日", $current_time);
// 按积分和流水号排出前2000名
$sql = "select  a.playersid, jf, lsh_m, pmsj, b.last_dalei_time dalei_time ";
$sql .= "from ol_dalei_jf a inner join ol_player b on b.playersid=a.playersid ";
$sql .= "order by jf desc, lsh_m desc limit 2000";
$result = mysql_query($sql) or die(mysql_error());
//echo "time2:", microtime(true) - $time1,"\n";

$pids = array();          //进入排名的玩家id号
$insertSql = array();

$sltCount = mysql_num_rows($result);

$_dalei_rows = array();
$_dalei_pids = array();
$pm = 1;		// 当前排名
while ($_row = mysql_fetch_array($result)) {
	$_dalei_rows[$pm] = $_row;
	$_dalei_pids[] = array('pid'=>$_row['playersid'],'jf'=>$_row['jf'],'lsh'=>$_row['lsh_m'],'t'=>$_row['dalei_time']);
	$pm ++;
}

$daleiJson = json_encode($_dalei_pids);
$w_long = fwrite($logHandle, "dalei|allList|{$daleiJson}\n");

$pmsj2 = strtotime(date('Y-m-d 00:00:00', $current_time)) + 24*3600;
$dalei_end_time = mktime(LEITAI_JIESUAN_SHIJIAN, 0, 0, date('m'), date('d'), date('Y'));
$dalei_start_time = mktime(LEITAI_JIESUAN_SHIJIAN-1, 35, 0, date('m'), date('d')-1, date('Y'));
foreach ($_dalei_rows as $pm=>$row) {
	// 当天未打擂过虑
	if($row['dalei_time'] < $dalei_start_time 
	|| $row['dalei_time'] > $dalei_end_time){
		continue;
	}
	
	$jfMin = $row['jf'];
	$lshMax = -1*$row['lsh_m'];

	$prestige = 100;
	if($pm==1)  $prestige = 2000;
	else if($pm==2)  $prestige = 1200;
	else if($pm==3)  $prestige = 800;
	else if($pm<11)  $prestige = 800-($pm-3)*20;
	else if($pm<31)  $prestige = 660-($pm-10)*5;
	else if($pm<101)  $prestige = 560-($pm-30)*2;
	else if($pm<501)  $prestige = 401-ceil(($pm-100)/4);
	else if($pm<1001)  $prestige = 301-ceil(($pm-500)/5);
	else if($pm<2001)  $prestige = 201-ceil(($pm-1000)/10);
	
	$pid = $row['playersid'];
	$sql = "update ol_dalei_jf a,ol_player b";
	$sql .= " set a.pm=$pm,a.pmsj=$current_time,a.pmsjf=jf,a.dlcs=0,a.yzjcs=0,a.yb_ref=0";
	$sql .= " ,b.prestige=b.prestige+$prestige,b.last_dalei_time=0 where a.playersid=$pid and b.playersid=a.playersid";

	mysql_query($sql);
	$w_long = fwrite($logHandle, "dalei|and|{$pid},{$pm},{$prestige}\n");

	// 插入信件需要的数据
	$message[] = array('sj'=>$md,'hdsw'=>$prestige,'pm'=>$pm);
	$pids[] = $pid;
	$mc->delete(MC.$pid);
}

// 更新信件和信件状态
if(count($pids)>0){
	$sqlbase = 'insert into ol_letters (playersid,type,genre,message,status,create_time) values ';
	$insLtrSql = $sqlbase;
	$index = 0;

	foreach($pids as $k=>$pid){
		$messageStr = addcslashes(json_encode($message[$k]),'\\');
		$insLtrSql .= "($pid,1,28,'$messageStr',0,$current_time)";
		$index++;
		if($index%200== 0 || $index >= count($pids)) {
			mysql_query($insLtrSql) or die(mysql_error());
			$insLtrSql = $sqlbase;
			
			// 修改对应玩家的信件状态
			// 0战斗，1好友，2系统
			$pid_str = implode(',', $pids);
			$sttSql = "INSERT INTO ol_message_status (`playersid`, `messageCount`, `xzdrz`, `xhyrz`, `xxxrz`, `zxlx`)";
			$sttSql .= " select `playersid`, '1', '0', '0', '1', '2' from ol_player where `playersid` in ({$pid_str})";
			$sttSql .= " ON DUPLICATE KEY UPDATE `messageCount`=(case messageCount when 99 then 99 else messageCount+1 end),";
			$sttSql .= "xxxrz=1,zxlx=2";
			mysql_query($sttSql);
		}else{
			$insLtrSql .= ',';
		}
		$mc->delete(MC.$pid.'_messageStatus');
	}
	//echo "time3:", microtime(true) - $time1,"\n";
}

// 周处理
if(date('w') == LEITAI_WEEK_DAY){
	sleep(5);

	$sql = "update ol_dalei_jf set dlcs=0,jf=0,lsh=0,lxsl=0";
	mysql_query($sql);

	sleep(2);
	$sql = "update `ol_dalei_jf_count` set num=0,lsh=0";
	mysql_query($sql);

	sleep(2);
	$sql = "update `ol_dalei_jf_count` set num=(select count(*) from `ol_dalei_jf` where jf=0),lsh=num where jf=0";
	mysql_query($sql);
}

while(!$mc->delete(MC.'_dalei_process_lock')){
	sleep(1);
};
<?php
$header = "Content-Type: application/octet-stream \r\n";
header($header);
//服务器列表在这里添加
$server_list = array(
	//sj是服务器ID，1服为1,2服为2，以此类推
	//mc是服务器名称
	//url是子目录，需要带上斜杠
	//tj是是否推荐，通常应该推荐最新开的服务器，1为推荐，默认为0
	//zt是服务器状态，0为良好，1为火爆
	//js直接给0
	array('sj'=>1,'mc'=>'hero1', 'url'=>'1', 'tj'=>'0', 'zt'=>'1', 'js'=>0),
	array('sj'=>2,'mc'=>'hero2', 'url'=>'2', 'tj'=>'1', 'zt'=>'1', 'js'=>0)
);

$ret_val = array('status'=>'sl', 'serverList'=>$server_list);
echo json_encode($ret_val);
?>
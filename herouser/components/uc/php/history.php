<?php
include(dirname(__FILE__)."/db.php");
require dirname(dirname(dirname(dirname(__FILE__)))) . '/ucapi/libs/UzoneRestApi.php';
require dirname(__FILE__).'/UcPayAccessAPI.php';

echo "<html>";
echo "<head>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
echo "<meta name=\"viewport\" content=\"width=device-width,minimum-scale=1.0, maximum-scale=2.0\"/>";
echo "<title>Q将水浒</title>";
echo "<style type=\"text/css\">
<!--
.STYLE1 {color: #FF0000}
body,td,th {
	font-size: 14px;
}

.background_1{background-color:#d7eeee;}
			.background_8{background-color:#8bbaf2; width:100%; height:1px;}
			.background_16{background-color:#92D14F;}
			.color_1{color:#8bbaf2;}
			.color_3{color:#0e3c7c;}
			.align_2{text-align:left;}
			.font-size_1{font-size:13px;}
			.font-size_2{font-size:23px; font-weight:bold;}
			.a{border:none;}
			.xu{border-top:1px dashed #f0cf36;height: 1px;overflow:hidden;}
			a{color:#0189ff;}
			.background_1 div{color:#0e3c7c}
table{border-left: 1px solid #666; border-bottom: 1px solid #666;}
td{border-right:1px solid #666;border-top: 1px solid #666;}
-->
</style>";
echo "</head>";
echo "<body>";
echo "<div class='color_3 background_1 font-size_1 '> ";
echo "<div class='background_16 font-size_2' align='center' style='height:30px'>Q将水浒</div>";

echo '兑换记录';
$uzone_token  = isset($_GET['uzone_token']) ? str_replace(' ','+',urldecode($_GET['uzone_token'])) : '';
$return_url = $_SC['backUrl'] . '/index.php?uzone_token=' . $uzone_token;
if($uzone_token <> '') {
	$UzoneRestApi = new UzoneRestApi($uzone_token);
	$ucid = $UzoneRestApi->getAuthUid();
}else{
	echo "非法操作，请返回游戏首页</p><br/>";
	echo "<a href='{$return_url}'>返回游戏首页</a>";
	exit;
}

if($ucid == '') {
	echo "非法操作，请返回游戏首页</p><br/>";
	echo "<a href='{$return_url}'>返回游戏首页</a>";
	exit;
}

$page  = isset($_GET['page']) ? $_GET['page'] : '1';
$sql = "select count(*) from ol_uc_order_notify where ucid = '".$ucid."'";
$arr = mysql_query($sql);
$tmp_count = mysql_fetch_array($arr);
echo '<br/>共 '.$tmp_count[0].' 条支付历史清单<br/>';

$zs = $tmp_count[0];
$curPage = $page;
if($page > 1) {
	$upPage = $page - 1;
}else{
	$upPage = 1;
}
$totalPage = ceil($zs/5);

if($page < $totalPage) {
	$nextPage = $page + 1;
}else{
	$nextPage = $totalPage;
}

$_start = ($page-1) * 5;
$_end = 0;
if($_start + 5 < $zs) {
	$_end = 5;
}else{
	$_end = $zs;
}
		
$sql = "select * from ol_uc_order_notify where ucid = '".$ucid."' order by create_time desc  LIMIT {$_start},{$_end};";
$tempList = array();
$arrlist = mysql_query($sql);
while ($rows = mysql_fetch_array($arrlist)) {
	$rows['create_time'] = date('Y-m-d H:i:s',$rows['create_time']);
	$rows['status'] = ($rows['status'] == '0') ? '正在处理' : (($rows['status'] == '1') ? '支付成功' : '支付失败');
	$tempList[] = $rows;
}

if(count($tempList) > 0) {
	echo '<table border="0" cellspacing="0" cellpadding="0">';
	for($i = 0 ; $i < count($tempList); $i++) {
	  echo '<tr>
		<td >充值时间</td>
		<td>'.$tempList[$i]['create_time'].'</td>
	  </tr>
	  <tr>
		<td>U点数</td>
		<td >状态</td>
	  </tr>
	  <tr>
		<td>'.$tempList[$i]['amount'].'</td>
		<td>'.$tempList[$i]['status'].'</td>
	  </tr>';
	}
	echo '</table>';
	if($curPage > 1)
		echo '<a href=history.php?uzone_token='.$uzone_token.'&page='.$upPage.'>上一页</a><br/>';
	if($curPage < $totalPage) {
		echo '                           ';
		echo '<a href=history.php?uzone_token='.$uzone_token.'&page='.$nextPage.'>下一页</a><br/>';
	}
}
echo '<br>客服电话：027-59715110<br/>';
echo "<a href='index.php?uzone_token=$uzone_token'>充值首页</a>";

echo "<div align='center'><a href='{$return_url}'>返回</a></div>";
echo '</div>';
echo '</body>';
echo '</html>';

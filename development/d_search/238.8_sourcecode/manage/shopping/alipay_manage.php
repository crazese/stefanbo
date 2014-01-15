<?php
//check authenticate
include ($_SERVER['DOCUMENT_ROOT']."/manage/islogin.php"); 
include "dbconn.php";
?>
<title>支付宝订单管理</title>
<style type="text/css">
<!--
body,td,th {
	font-size: 12px;
	font-family: 宋体;
}
body {
	background-color: #BDCBE7;
}
-->
</style>
<?
	$nStart = escapeshellcmd($_GET["start"]);
	if(!$nStart)
		$nStart = escapeshellcmd($_POST["start"]);
		
	$s = escapeshellcmd($_GET["s"]);
	if(!$s)
		$s = escapeshellcmd($_POST["s"]);
 
 $buyer_email = trim($_GET['buyer_email']);
 $return_time = trim($_GET['return_time']);
 $order_num = trim($_GET['order_num']);
 $url = $_GET['url'];
 
$filter1="select * from receive where order_num LIKE '%$order_num%' 
								 && buyer_email LIKE '%$buyer_email%' 
								 && return_time LIKE '%$return_time%' && url LIKE '%$url%' ";
								 
$filter4="order by ID desc";
$query=$filter1.$filter4;
$result = mysql_query($query,$dbconn);
$nCount=mysql_num_rows($result);

?>
<script type="text/JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
//-->
</script>
<table width="96%"  border="0" align="center" cellpadding="3" cellspacing="0" >
      <form action="alipay_manage.php" method="get">
        <tr>
          <td width="10%"  height="20">订单号:</td>
          <td width="32%" ><input name="order_num" type="text" value="<?php echo $order_num;?>"></td>
		  <td >支付方式:</td>
          <td colspan="3"><select name="url" id="url">
            <option value="">All</option>
            <option value="99bill" <?php if($url == '99bill') echo "selected=\"selected\"";?>>99bill</option>
            <option value="alipay" <?php if($url == 'alipay') echo "selected=\"selected\"";?>>alipay</option>
          </select>
          </td>
        </tr>
        <tr>
          <td> 付款时间:</td>
          <td><input name="return_time" type="text" id="return_time" value="<?php echo $return_time;?>"></td>
          <td >用户Email:</td>
          <td colspan="3"><input name="buyer_email" type="text" id="buyer_email" value="<?php echo $buyer_email;?>">
              <input  type="submit" value="Search"></td>
        </tr>
      </form>
</table>
	<table width="96%"  border="0" align="center" cellpadding="3" cellspacing="0" class="cleartable"> 
        <tr> 
          <td height="20">Total Records: <?php echo($nCount); ?></td> 
        </tr> 
</table>
    <table width="96%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#BBBBBB" class="cleartable">
      <tr align="left" >
        <td width="52" height="22" align="center" bgcolor="#BDCBE7" ><strong>序号</strong></td>
        <td width="99" height="22" align="center" bgcolor="#BDCBE7" ><strong>订单号</strong></td>
        <td width="122" height="22" align="center" bgcolor="#BDCBE7"><strong>产品名称</strong></td>
        <td width="74" height="22" align="center" bgcolor="#BDCBE7"  ><strong>总价格</strong></td>
        <td width="140" height="22" align="center" bgcolor="#BDCBE7"   ><strong>邮箱地址</strong></td>
        <td width="80" height="22" align="center" bgcolor="#BDCBE7" ><strong>用户名</strong></td>
        <td width="111" height="22" align="center" bgcolor="#BDCBE7"  ><strong>内部编号</strong></td>
        <td width="147" height="22" align="center" bgcolor="#BDCBE7"><strong>付款时间</strong></td>
        <td width="88" height="22" align="center" bgcolor="#BDCBE7"><strong>支付方式</strong></td>
      </tr>
      <?php
$nLimit = 20; 
$PageLimit = 20;
if(!$nStart) 
	$nStart = 0; 
if(!$s) 
	$s = 0; 
$arrayCount = 0;

$filter1="select * from receive where order_num LIKE '%$order_num%' 
								 && buyer_email LIKE '%$buyer_email%' 
								 && return_time LIKE '%$return_time%' 
								 && url LIKE '%$url%' ";
						

$filter4="order by ID desc limit $nStart,$nLimit";
$query1=$filter1.$filter2.$filter3.$filter4;
//echo $query1;
$result1 = mysql_query($query1,$dbconn);
@$rows = mysql_num_rows($result1);
for($i=0 ; $i <$rows;$i++)
{	
$ID = mysql_result($result1,$i,"ID");
$order_num_1 = mysql_result($result1,$i,"order_num");  
$ProductName = mysql_result($result1,$i,"ProductName");
$buyer_email_1 = mysql_result($result1,$i,"buyer_email");  
$username = mysql_result($result1,$i,"username");
$total_fee = mysql_result($result1,$i,"total_fee");  
$return_time_1 = mysql_result($result1,$i,"return_time"); 
$url2 = mysql_result($result1,$i,"url");
$out_trade_no = mysql_result($result1,$i,'out_trade_no');
?>
      <tr align="left">
        <td height="25" align="center" bgcolor="#BDCBE7"><?php echo $ID; ?></td>
        <td bgcolor="#BDCBE7"><?php echo $order_num_1; ?> </td>
        <td bgcolor="#BDCBE7"><?php echo $ProductName; ?></td>
        <td bgcolor="#BDCBE7" >￥<?php echo $total_fee;	?></td>
        <td bgcolor="#BDCBE7" ><?php echo $buyer_email_1;?></td>
        <td bgcolor="#BDCBE7" ><?php echo $username;?></td>
        <td bgcolor="#BDCBE7" ><?php echo $out_trade_no;?></td>
        <td align="center" bgcolor="#BDCBE7" ><?php echo $return_time_1;?></td>
        <td align="center" bgcolor="#BDCBE7" ><?php echo $url2;?> </td>
      </tr>
      <?php  }  ?>
      <tr align="center" >
        <td colspan="11" bgcolor="#BDCBE7"><?php
		echo("<table>"); 
		echo("<tr><td><p>Pages:</p></td>"); 
		if($s > 20)
		{ 
			if($s == 21)
			{ 
				$st = $s - 21; 
			}
			else
			{ 
				$st = $s - 20; 
			} 
			$pstart = $st * $nLimit; 
			echo("<td>");
			echo ("<a href=alipay_manage.php?order_num=$order_num&buyer_email=$buyer_email&start=$pstart&s=$st&url=$url&return_time=$return_time>"); 
			echo ("<< </a></td>"); 
		} 
		
		
		$star = $nStart; 
		for($page = $s; $page < ($nCount/$nLimit); $page++)
		{ 
			$nStart = $page * $nLimit; 
			echo("<td>"); 
		
			if($page != $star / $nLimit)
			{ 
				echo("<a href=alipay_manage.php?order_num=$order_num&buyer_email=$buyer_email&start=$nStart&s=$s&url=$url&return_time=$return_time>");			
				echo($page + 1);			 
				echo("</a>"); 
			}
			else
			{
				echo("<p>");
				echo($page + 1);
				echo("</p>");
			} 
		
			echo("</td>"); 
			if ($page>0 && ($page%$PageLimit)==0)
			{ 
				if($s == 0)
				{ 
					$s = $s + 21; 
				}
				else
				{ 
					$s = $s + 20; 
				} 
			
				$nStart = $nStart + $nLimit; 
			
				if((($nCount / $nLimit) - 1) > $page)
				{ 
					echo("<td><a href=alipay_manage.php?order_num=$order_num&buyer_email=$buyer_email&start=$nStart&s=$s&url=$url&return_time=$return_time> >> </a></td>"); 
				}	
				break; 
			} 
		
		} 
		echo("</tr></table>"); 
	
	?></td>
      </tr>
    </table>

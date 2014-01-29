<?php
//check authenticate
include ($_SERVER['DOCUMENT_ROOT']."/manage/islogin.php"); 
include "dbconn.php";

$sql="select * from product";
$res=mysql_query($sql,$dbconn);
$nCount=mysql_num_rows($res);
?>
<title>Product manage</title>
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
<table width="100%"  border="0" align="center" cellpadding="3" cellspacing="0" > 
        <tr> 
          <td width="72%" height="20">Total Records: <?php echo($nCount); ?></td> 
          <td width="28%" ><a href="product_add.php">Add Product</a> </td>
        </tr> 
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="2" bgcolor="3f5e9e">
  <tr>
    <td bgcolor="#FFFFFF">
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#DDDDDD" > 
  <tr  > 
    <td width="5%" height="29" align="center" bgcolor="#BDCBE7" ><strong>编号</strong></td> 
    <td width="18%" bgcolor="#BDCBE7" ><strong>产品名称</strong></td> 
    <td width="5%" align="center" bgcolor="#BDCBE7" ><strong>价格</strong></td> 
    <td width="55%" align="center" bgcolor="#BDCBE7" ><strong>产品描述</strong></td> 
    <td width="7%" align="center" bgcolor="#BDCBE7" ><strong>是否显示</strong></td> 
    <td width="6%" align="center" bgcolor="#BDCBE7" ><strong>折扣</strong></td> 
	<td width="4%" bgcolor="#BDCBE7"  ><strong>编辑</strong></td> 
    <!--    <td width="5%"  >Delete</td>
--></tr> 
<?php
while ($NoteRow=mysql_fetch_array($res))
	{
?> 
	<tr bgcolor="#ffffff" style="padding-left:" 10; padding-top:="padding-top:" 0; padding-bottom:="padding-bottom:" 0 onMouseOver="this.style.backgroundColor='#bfd8ee'" onMouseOut="this.style.backgroundColor='#ffffff'" > 
    <td  height="25" align="center" bgcolor="#BDCBE7"><?php echo(stripcslashes($NoteRow["ProductID"])); ?></td> 
    <td bgcolor="#BDCBE7" ><?php echo $NoteRow["ProductName"]; ?></td> 
    <td bgcolor="#BDCBE7" >￥<?php echo $NoteRow["ProductPrice"]; ?></td> 
    <td bgcolor="#BDCBE7"  ><?php echo $NoteRow["Description"]; ?></td> 
	<td align="center" bgcolor="#BDCBE7" ><?php echo $NoteRow["IsShow"]; ?></td>
    <td align="center" bgcolor="#BDCBE7" ><?php echo $NoteRow["volumn_discount"]; ?></td>
     <td bgcolor="#BDCBE7"  > <a href="product_modify.php?ProductID=<?php echo $NoteRow["ProductID"];?>">Edit</a>   </td> 
<!--    <td  align="center"><a href="product_delete.php?action=del&ProductID=<?php //echo $NoteRow["ProductID"];?>">Del</a>	
</td> -->
   </tr> 
 <?php
  $i++;
  }
  ?>   
</table></td></tr></table>
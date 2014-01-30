<?php
  	include $_SERVER['DOCUMENT_ROOT']."/manage/islogin.php";
	//include "header.php";
	include "dbconn.php";

?>
<title>Product manage</title>
<style type="text/css">
<!--
body,td,th {
	font-family: 宋体;
	font-size: 12px;
}
body {
	background-color: #BDCBE7;
}
-->
</style><table width="775" border="0" align="center" cellpadding="2">            
      <tr>            
        <td width="100%" align="center">     
	<form  name="product" method="post" action="product_add.php" ENCTYPE="MultiPart/form-data" >
                 <table width="90%" border="0" cellpadding="0" cellspacing="1" bgcolor="#DDDDDD" >      
				  
		<tr>
		<td height="25" colspan="2" bgcolor="#BDCBE7"><strong>产品信息设置：</strong>		</td>
		</tr> 

					<tr>     
                      <td width="16%" bgcolor="#BDCBE7" >产品名称:</td>      
                      <td width="84%" bgcolor="#BDCBE7"><input type="text" name="ProductName" size="40"> * </td>      
                    </tr>
					<tr>
					  <td bgcolor="#BDCBE7" >是否有折扣:</td>
					  <td bgcolor="#BDCBE7"><input type="radio" name="volumn_discount" value="y" />Yes
				        <input name="volumn_discount" type="radio" value="n" checked="checked" />
			          No</td>
				   </tr>	
					<tr>
					<td bgcolor="#BDCBE7" >是否显示:</td>     
                      <td bgcolor="#BDCBE7"  >
                          <input name="bIsShow"type="radio"  value="y">Yes
                          <input name="bIsShow" type="radio" value="n" checked="checked">
                      No					 </td>     
                    </tr>		
                       
                    <tr>    
                      <td bgcolor="#BDCBE7" >产品价格:</td>     
                      <td bgcolor="#BDCBE7" > <input name="ProductPrice" type="text" size="10"> 
                      RMB*</td>     
                    </tr> 
					   <tr>    
                      <td bgcolor="#BDCBE7">产品描述:</td>     
                      <td bgcolor="#BDCBE7" >     
                        <textarea rows="4" name="Description" cols="40"></textarea> </td>     
                    </tr>     
                
                    <tr>    
                      <td  colspan="2" bgcolor="#BDCBE7">    
                        <input type="submit" value="add" name="add" onClick="return FormCheck()">&nbsp;&nbsp;    
                      <input type="reset" value="clear" name="reset"> </td>     
                    </tr>     
        </table>     
		</form>
 
        </td>     
  </tr>     
</table>
<script language="javascript1.4" type="text/javascript">
function FormCheck()
{
 var strbuff1,strbuff2,strbuff3,strbuff4,strbuff5,strbuff6;
 strbuff1=document.forms["product"].elements["ProductName"].value;
 strbuff3=document.forms["product"].elements["ProductPrice"].value;

if(strbuff1.length==0)
 {
   alert("Please input your product name!");
   return false;
 }
 
  if(strbuff3.length==0)
 {
   alert("Please input your product USD Price!");
   return false;
 }
 return true;
}
</script>
<?php
if($_POST['add']=="add"){
	$ProductName=$_POST['ProductName'];
	$productPrice=$_POST['ProductPrice'];
	$volumn_discount=$_POST['volumn_discount'];
	$bIsShow = $_POST['bIsShow'];
	$Description=$_POST['Description'];
	
	$Regdate=date("Y-m-d,H:i:s");
	
	$sql = "insert into product (ProductName,ProductPrice,IsShow,volumn_discount,Description,Regdate)values ('$ProductName','$ProductPrice','$bIsShow','$volumn_discount','$Description','$Regdate')";
	$result3 = mysql_query($sql,$dbconn);
	
	if($result3)
	{
		echo ("	<meta http-equiv='refresh' content='0; url=product_manage.php'>");
	}
	else
	{
			echo("
					<script>
					window.alert('Please try again.')
					history.go(-1)
					</script>
					");
			exit;
	}
}
?>
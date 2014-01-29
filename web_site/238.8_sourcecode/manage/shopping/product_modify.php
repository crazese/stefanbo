<?php 
  	include $_SERVER['DOCUMENT_ROOT']."/manage/islogin.php"; 
	include "dbconn.php";
	$ProductID = $_GET["ProductID"];
	
	$sql="select * from product where ProductID=$ProductID";
	$result = mysql_query($sql,$dbconn);
	$NoteRow=mysql_fetch_array($result);
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
	<form method="post" action="product_modify.php?ProductID=<?php echo $NoteRow["ProductID"];?>" ENCTYPE="MultiPart/form-data" name="Info">
      <table width="90%" border="0" cellpadding="0" cellspacing="1" bgcolor="#DDDDDD">      
		<tr ><td height="25" colspan="2" bgcolor="#BDCBE7"><strong>产品信息设置：</strong></td>
		</tr> 
		 <tr>     
            <td width="13%" height="25" bgcolor="#BDCBE7" >产品编号：</td>      
            <td width="87%" height="25" bgcolor="#BDCBE7" ><input type="text" name="ProductID" value="<?php echo $NoteRow["ProductID"];?>" readonly="true"></td>      </tr>      
				    <tr>     
                      <td height="25" bgcolor="#BDCBE7" >产品名称：</td>      
                      <td height="25" bgcolor="#BDCBE7" >      
                      <input type="text" name="ProductName" size="40" value="<?php echo $NoteRow["ProductName"];?>">                      </td>      
                    </tr>     
                       
                   <tr>    
                      <td height="25" bgcolor="#BDCBE7" >产品价格：</td>     
                      <td height="25" bgcolor="#BDCBE7" ><input name="ProductPrice" type="text" value="<?php echo $NoteRow["ProductPrice"];?>" >
                     RMB  </td>                  
                   </tr>
 <tr>    
                      <td height="25" bgcolor="#BDCBE7">是否显示：</td>     
                      <td height="25" bgcolor="#BDCBE7"  >
                          <input name="bIsShow"type="radio"  value="y"<?php if($NoteRow["IsShow"]== 'y')	echo(" checked");?>>Yes
            <input name="bIsShow" type="radio" value="n"<?php if($NoteRow["IsShow"]== 'n')echo(" checked");?>>No					 </td>     
          </tr>  
 <tr>
   <td height="25" bgcolor="#BDCBE7" >是否折扣:</td>
   <td height="25" bgcolor="#BDCBE7"><input type="radio" name="volumn_discount" value="y" <?php if($NoteRow["volumn_discount"]== 'y')	echo(" checked");?>/>
     Yes
     <input name="volumn_discount" type="radio" value="n" <?php if($NoteRow["volumn_discount"]== 'n')	echo(" checked");?>/>
     No</td>
 </tr>
 
					 <tr>    
                      <td height="25" bgcolor="#BDCBE7" >描述: </td>     
                      <td height="25" bgcolor="#BDCBE7" >    

                  <textarea name="Description" cols="50" rows="3"><?php echo $NoteRow['Description'];?></textarea>
                       </td>     
                    </tr>					 
                    <tr>    
                      <td height="25" colspan="2" bgcolor="#BDCBE7">    
						<input type="submit" value="modify" name="modify">&nbsp;&nbsp;&nbsp;    
                      <input type="reset" value="reset" name="reset">                   </td>     
                    </tr>     
        </table>     
	</form>	         
    </td>              
  </tr>              
</table>
<?
if($_POST['modify'] == "modify"){
	
	//$ProductID = $_POST['ProductID'];
	$ProductName = $_POST['ProductName'];
	$ProductPrice =$_POST['ProductPrice'];
	$IsShow = $_POST['bIsShow'];
	$volumn_discount=$_POST['volumn_discount'];
	$Description =$_POST['Description'];
	
	if(!$ProductName){
		echo("
			<script>
			window.alert('Please input the Product Name.')
			history.go(-1)
			</script>
			");
		exit;
	}

	$sql="update product set ProductName='$ProductName',ProductPrice='$ProductPrice',Description='$Description',IsShow='$IsShow',volumn_discount='$volumn_discount' where ProductID='$ProductID'";
	
	$result=mysql_query($sql,$dbconn);
	if($result){
		echo ("	<meta http-equiv='refresh' content='0; url=product_manage.php'>");
	}
	else{
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
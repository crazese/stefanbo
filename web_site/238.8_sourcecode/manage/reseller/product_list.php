<?php
  include ("class/reseller.php"); 
  include("islogin_admin.php");
  $class = new reseller();
  $class->dbconn();  
  if($_GET['act']=="delete")
  {
     $intID = $_GET['intID'];
	 $class->DeleteProduct($intID);
  }
  $rows = $class->GetProductlist();
  include("header.php");
?>
<script language="javascript">
   function Del(intID)
   {
     result=confirm("Are you sure to delete this product?");
	 if(result)
	 {
	    location.href = 'product_list.php?act=delete&intID='+intID;
	 }
   }
</script>
<table width="98%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="right"><a href="add_product.php">Add Product </a></td>
  </tr>
  <tr>
    <td align="right">&nbsp;</td>
  </tr>
</table>
<table align="center" border="1" bordercolor="#999999" cellpadding="2" cellspacing="0" width="96%">          
          <tr bgcolor="#cccccc">
            <td width="26%" bgcolor="#cccccc"><strong>Product Name</strong></td>
            <td width="11%" bgcolor="#cccccc"><strong>SoftwareName</strong></td>
            <td width="8%" bgcolor="#cccccc"><strong>Version</strong></td>
            <td width="10%" bgcolor="#cccccc"><strong>File Name</strong></td>
            <td width="10%" bgcolor="#cccccc"><strong>UnlockType</strong></td>
            <td width="12%" bgcolor="#cccccc"><strong>Product Price</strong></td>
            <td width="6%" bgcolor="#cccccc"><strong>Website</strong></td>
            <td width="13%" bgcolor="#cccccc"><strong>Regdate</strong></td>
            <td width="4%" align="center" bgcolor="#cccccc"><strong>DEL</strong></td>
          </tr>
       <?php
	      for($i=0;$i<count($rows);$i++)
		  {
	   ?>  
		  <tr>
            <td width="26%">&nbsp;<a href="javascript:;" onClick="window.open('update_product.php?intID=<?php echo $rows[$i]['intID'];?>','go','scrollbars=no,resizable=no,width=500,height=230,left=200,top=200,screenX=200,screenY=200')"><?php echo $rows[$i]['ProductName'];?></a></td>
            <td width="11%">&nbsp;<?php echo $rows[$i]['SoftwareName'];?></td>
            <td width="8%">&nbsp;<?php echo $rows[$i]['Version'];?></td>
            <td width="10%">&nbsp;<?php echo $rows[$i]['FileName'];?></td>
            <td width="10%">&nbsp;<?php echo $class->GetUnlockTpye($rows[$i]['UnlockType']);?></td>
            <td width="12%">$&nbsp;<?php echo $rows[$i]['PriceUSD'];?></td>
            <td width="6%">&nbsp;<?php echo $class->GetWebsite($rows[$i]['Website']);?></td>
            <td width="13%">&nbsp;<?php echo $rows[$i]['Regdate'];?></td>
            <td width="4%" align="center"><a href="javascript:;" onClick="Del(<?php echo $rows[$i]['intID']?>)">DEL</a></td>
		  </tr>
		  <?php
		  }
		  ?>
</table>
<?php include("bottom.php");?>
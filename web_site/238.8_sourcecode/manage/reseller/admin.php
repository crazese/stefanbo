<?php
  include ("class/reseller.php"); 
  include("islogin_admin.php");
  $class = new reseller();
  $class->dbconn();
  $order_show = $_GET['order'];
  if(!$order_show)
    $order_show = "DESC";
  $itemname = $_GET['item'];
  if($_GET['act']=="delete")
  {
     $intID = $_GET['intID'];
	 $class->DeleteReseller($intID);
  }

  $rows_update = $class->ResellerList();
  for($j=0;$j<count($rows_update);$j++)
  {
       $class->UpdateSales($rows_update[$j]['intID']);
  }
  
  $rows = $class->ResellerList("",$order_show,$itemname);
  //print_r($rows);
  if($order_show == "DESC")
    $order = "ASC";
  if($order_show == "ASC")
    $order = "DESC";
  //echo $order;
  include("header.php");
?>
<script language="javascript">
   function Del(intID)
   {
     result=confirm("Are you sure to delete this reseller?");
	 if(result)
	 {
	    location.href = 'admin.php?act=delete&intID='+intID;
	 }
   }
</script>
<table width="98%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="right"><a href="add_reseller.php">Add Reseller </a></td>
  </tr>
  <tr>
    <td align="right">&nbsp;</td>
  </tr>
</table>

<table align="center" border="1" bordercolor="#999999" cellpadding="2" cellspacing="0" width="96%">          
          <tr bgcolor="#cccccc">
            <td width="13%" bgcolor="#cccccc"><strong>Reseller Name</strong></td>
            <td width="9%" bgcolor="#cccccc"><strong>Username</strong></td>
            <td width="11%" bgcolor="#cccccc"><strong>Password</strong></td>
            <td width="11%" bgcolor="#cccccc"><strong><a href="admin.php?item=strStatus&amp;order=<?php echo $order;?>">Status</a></strong></td>
            <td width="11%" bgcolor="#cccccc"><strong>Email</strong></td>
            <td width="12%" bgcolor="#cccccc"><strong><a href="admin.php?item=country&amp;order=<?php echo $order;?>">Country</a></strong></td>
            <td width="15%" bgcolor="#cccccc"><strong><a href="admin.php?item=regdate&amp;order=<?php echo $order;?>">RegDate</a></strong></td>
            <td width="5%" bgcolor="#cccccc"><strong><a href="admin.php?item=sales&order=<?php echo $order;?>">Sales</a></strong></td>
            <td width="7%" align="center" bgcolor="#cccccc"><strong><a href="admin.php?item=Discount&order=<?php echo $order;?>">Discount</a></strong></td>
            <td width="6%" align="center" bgcolor="#cccccc"><strong>DEL</strong></td>
          </tr>
       <?php
	      for($i=0;$i<count($rows);$i++)
		  {
		     //$rows_sales = $class->TotalSales($rows[$i]['intID'],"","","","");
			 //$sales = $rows_sales[0];
	   ?>  
		  <tr>
            <td width="13%">&nbsp;<a href="javascript:;" onClick="window.open('update_reseller.php?intID=<?php echo $rows[$i]['intID'];?>','go','scrollbars=no,resizable=no,width=500,height=230,left=200,top=200,screenX=200,screenY=200')"><?php echo $rows[$i]['strName'];?></a></td>
            <td width="9%">&nbsp;<?php echo $rows[$i]['uname'];?></td>
            <td width="11%">&nbsp;<?php echo $rows[$i]['passwd'];?></td>
            <td width="11%">&nbsp;<?php echo $rows[$i]['strStatus'];?></td>
            <td width="11%">&nbsp;<?php echo $rows[$i]['email'];?></td>
            <td width="12%">&nbsp;<?php echo $rows[$i]['country'];?></td>
            <td width="15%">&nbsp;<?php echo $rows[$i]['regdate'];?></td>
            <td width="5%">&nbsp;$<a href="admin_purchased.php?ResellerID=<?php echo $rows[$i]['intID'];?>&uname=<?php echo $rows[$i]['uname'];?>"><?php echo $rows[$i]['sales'];?></a></td>
            <td width="7%" align="center"><?php echo $rows[$i]['Discount'];?></td>
            <td width="6%" align="center"><a href="javascript:;" onClick="Del(<?php echo $rows[$i]['intID'];?>)">DEL</a></td>
		  </tr>
		  <?php
		  }
		  ?>
</table>
<?php include("bottom.php");?>
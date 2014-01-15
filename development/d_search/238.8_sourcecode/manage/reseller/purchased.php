<?php
  include ("class/reseller.php"); 
  include("islogin.php");
  include("SubPage.php");
  include("date.php");
  $class = new reseller();
  $class->dbconn();   
  $uname = $_COOKIE['cookie']['username'];
  $psw = $_COOKIE['cookie']['password'];
  $page = $_GET['page'];
  if (!$page)
  {
     $page = $_GET['PB_Page_Select'];
     if(!$page)
     {
       $page = 1; 
     }
  }
  $ProductID = $_REQUEST['ProductName'];
  $email = trim($_REQUEST['email']);
  $from = trim($_REQUEST['from']);
  $from2 = $from;
  $end = trim($_REQUEST['end']);
  $end2=$end;
  if($from=="" && $end!="")
    $from = $end;
  if($from!="" && $end=="")
    $end = $from;
  if ($end > $from)
     $end = $end ." 23:59:59";  
  if ($end<$from)
  {
     $end = $from." 23:59:59";
	 $from = $end2;
  }
  $pageshow = $page;
  $perpage = 30;
  $start = ($page-1)*$perpage;  
  $userinfo = $class->GetUserinfo($uname,$psw);
  $ResellerID =$userinfo['intID'];
  $rows = $class->GetPurchased($ResellerID,$start,$perpage,$ProductID,$email,$from,$end);
  $rows_total = $class->GetPurchased($ResellerID,"","",$ProductID,$email,$from,$end);
  $total = count($rows_total);
  $from = substr($from,0,10); 
  $end = substr($end,0,10); 
  $page=new page(array('total'=>intval($total),'perpage'=>intval($perpage),'url'=>'purchased.php','page_name'=>'ProductName='.$ProductID.'&email='.$email.'&from='.$from.'&end='.$end.'&page','nowindex'=>$page));
  $rows_product = $class->GetProductlist();
  include("header.php");
  //print_r($rows);
?>
<script language="JavaScript" type="text/javascript">
   function purchased()
   {
      document.form2.action='purchased.php';
   }
   function exportemail()
   {
      document.form2.action='export.php';
   } 

</script>
<form name="form2" method="post" id="form2" action="">
  <table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td>Product Name:
        <select name="ProductName" id="ProductName">
         <option value="">Please Select Product</option>
		    <?php 
	   for($i=0;$i<count($rows_product);$i++)
	   {
	      if($rows_product[$i]['intID']==$ProductID)
		  {
		      echo "<option value=\"".$rows_product[$i]['intID']."\" selected>".$rows_product[$i]['ProductName']."</option>";
		  }
		  else
		  {
		      echo "<option value=\"".$rows_product[$i]['intID']."\">".$rows_product[$i]['ProductName']."</option>"; 
		  }
	   }
	   ?>
        </select>
        &nbsp;Register Name:
        <input name="email" type="text" id="email" value="<?php echo $email;?>">
        &nbsp;Regdate From:
        <input name="from" type="text" id="from" onclick='showCalender(this)' value="<?php echo $from;?>" size="20" />
        End:
        <input name="end" type="text" id="end" onclick='showCalender(this)' value="<?php echo $end;?>" size="20" />
        <input name="Search" type="submit" id="Search" value="Search" onClick="purchased()">
        <input name="Export" type="submit" id="Export" value="Export" onClick="exportemail()"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td height="25">Total Sales: $<?php $totalsale = $class->TotalSales($ResellerID,$ProductID,$email,$from,$end); echo $totalsale[0]; echo ", total ".$totalsale[1]." products";?></td>
    </tr>
  </table>
</form>
<table align="center" border="1" bordercolor="#999999" cellpadding="2" cellspacing="0" width="96%">          
          <tr bgcolor="#cccccc">
            <td width="14%" height="22" bgcolor="#cccccc"><strong>Product Name </strong></td>
            <td width="12%" bgcolor="#cccccc"><strong>Register Name(Email)</strong></td>
            <td width="45%" bgcolor="#cccccc"><strong>Regcode</strong></td>
            <td width="12%" bgcolor="#cccccc"><strong>RegDate</strong></td>
            <td width="5%" bgcolor="#cccccc"><strong>Sales</strong></td>
            <td width="6%" align="center" bgcolor="#cccccc"><strong>Discount</strong></td>
          </tr>
       <?php
	      for($i=0;$i<count($rows);$i++)
		  {
	   ?>  
		  <tr>
            <td>&nbsp;<?php echo $rows[$i]['ProductName'];?></td>
            <td>&nbsp;<?php echo $rows[$i]['email'];?></td>
            <td>&nbsp;<?php echo $rows[$i]['Regcode'];?></td>
            <td>&nbsp;<?php echo $rows[$i]['Regdate'];?></td>
            <td>$<?php echo $rows[$i]['PriceUSD'];?></td>
            <td align="center"><?php echo $rows[$i]['Discount'];?>%</td>
          </tr>
		  <?php
		  }
		  ?>
</table><table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="left"><form id="form1" name="form1" method="get" action="">
      <?php echo $page->show();?>
        <input type="submit" style="height:22px" value="Go" />
        <input name="ProductName" type="hidden" id="ProductName" value="<?php echo $ProductID;?>">
        <input name="email" type="hidden" id="email" value="<?php echo $email;?>">
        <input name="from" type="hidden" id="from" value="<?php echo $from;?>">
        <input name="end" type="hidden" id="end" value="<?php echo $end;?>">
    </form></td>
  </tr>
  <tr>
    <td align="left">&nbsp;</td>
  </tr>
  <tr>
    <td align="center">Contact <a href="mailto:reseller@sothink.com">reseller@sothink.com</a> if you have any             questions.</td>
  </tr>
</table>

<?php include("bottom.php");?>
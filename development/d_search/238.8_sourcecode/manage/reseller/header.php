<!--<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="font.css">
<title>Sothink Media Control Panel</title>
</HEAD>
<body>
<table width="100%" height="30"  border="0" align="center">
  <tr bgcolor="#FFFFFF">
    <td align="center" ><font size="4"  face="Verdana, Arial, Helvetica, sans-serif">Sothink Media Control Panel</font></td>
  </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" >
      <tr bgcolor="#FFFFFF">
        <td width="95%" height="30" align="center" ><p>
		 <a href="class_manage.php"><strong>Class</strong></a> | 
		 <a href="product_manage.php"><strong>Product</strong></a> | 
		 <a href="cross_manage.php"><strong>Cross Product</strong></a> |
		 <a href="2co_manage.php"><strong>2checkout</strong></a> | 
		 <a href="2co_statistics.php"><strong>2checkout	statistics</strong></a> | 
		 <a href="paypal_manage.php"><strong>paypal</strong></a> | 
		 <a href="logout.php"><strong>Logout</strong></a> </p></td>
      </tr>
</table>-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Sothink Media Control Panel</title>
<link href="style.css" type="text/css" rel="stylesheet">
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
</head>
<body>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td valign="bottom" nowrap></td>
<td valign="bottom" width="100%" align="right">

<!--TOP NAV-->
<div><img src="images/p_t.gif" alt="" border="0" width="1" height="4"></div>
<table cellpadding="1" cellspacing="0" border="0" width="<?php if($_COOKIE['cookie']['level']==1) echo "300"; else echo "530";?>" class="outerborder">
<tr>
<td>
<table width="100%" border="0" align="right" cellpadding="2" cellspacing="0" class="content">
<tr>
<?php if($_COOKIE['cookie']['level']==1) {?>
<td align="center"><a href="admin.php" class="navigation">Reseller Admin </a></td>
<td width="2%" align="center">&nbsp;</td>
<td align="center"><a href="product_list.php" class="navigation">Product Manage </a></td>
<td width="1%" align="center">&nbsp;</td><?php } if($_COOKIE['cookie']['level']==2) {?>
<td align="center"><a href="generate.php" class="navigation">Generate Code </a></td>
<td width="2%" align="center" nowrap>&nbsp;</td>
<td align="center" nowrap><a href="batchgenerate.php" class="navigation">Batch Code Generate</a></td>
<td width="2%" align="center" nowrap>&nbsp;</td>
<td width="2%" align="center" nowrap><a href="purchased.php" class="navigation">Purchased List</a></td>
<td width="2%" align="center" nowrap>&nbsp;</td>
<td width="2%" align="center" nowrap><a href="profile.php" class="navigation">Profile Update </a></td>
<td width="2%" align="center" nowrap>&nbsp;</td><?php }?>
<td align="center" nowrap><a href="logout.php" class="navigation">Logout</a></td>
<td width="5%"></td>
</tr>
</table>
</td>
</tr>
</table>
<!-- //TOP NAV-->

</td>
</tr>
</table>
<div><img src="images/p_t.gif" alt="" border="0" width="1" height="4"></div>
<!--OUTER BORDER-->
<!--TOP ROW-->
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td rowspan="2" class="innerborder"><img src="images/o_edge_lo.gif" border="0" alt="" width="6" height="6"></td>
<td width="100%" class="outerborder"><img src="images/p_t.gif" border="0" alt="" width="1" height="1"></td>
<td rowspan="2" class="innerborder"><img src="images/o_edge_ro.gif" border="0" alt="" width="6" height="6"></td>
</tr>
<tr>
<td width="100%" class="innerborder"><img src="images/p_t.gif" border="0" alt="" width="1" height="5"></td>
</tr>
</table>
<!--/TOP ROW-->

<!--MIDDLE ROW-->
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td width="1" class="outerborder"><img src="images/p_t.gif" border="0" alt="" width="1" height="1"></td>
<td width="5" class="innerborder"><img src="images/p_t.gif" border="0" alt="" width="5" height="1"></td>
<td valign="top" width="100%" class="innerborder">
<!--CONTENT-->
<!--INNER BORDER-->
<!--TOP ROW-->
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>

<td width="6" rowspan="2"><img src="images/i_edge_lo.gif" border="0" alt="" width="6" height="6"></td>
<td width="100%" class="outerborder"><img src="images/p_t.gif" border="0" alt="" width="1" height="1"></td>
<td width="6" rowspan="2"><img src="images/i_edge_ro.gif" border="0" alt="" width="6" height="6"></td>
</tr>
<tr>
<td width="100%" class="content"><img src="images/p_t.gif" border="0" alt="" width="1" height="5"></td>
</tr>
</table>
<!--/TOP ROW-->
<!--MIDDLE ROW-->
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td width="1" class="outerborder"><img src="images/p_t.gif" border="0" alt="" width="1" height="1"></td>
<td width="100%" valign="top" class="content">
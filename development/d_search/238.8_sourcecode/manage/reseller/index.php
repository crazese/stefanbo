<?php 
  	include ("class/reseller.php"); 
	if($_POST['action'] == 'Login')
	{
	$username=$_POST['username'];
	$password=$_POST['password'];
	$class = new reseller();
	$class->dbconn();
	$detail = $class->login($username, $password);
    //echo $detail;
	if ($detail!="failed")
	{
		setcookie("cookie[username]", $username, time()+3600);
		setcookie("cookie[password]", $password, time()+3600);
		setcookie("cookie[level]", $detail, time()+3600);
		if($detail==1)
		   header("Location: admin.php");
		if($detail==2)
		   header("Location:purchased.php");
	}
	else 
	{
		//header("Location:failed.php ");
		header("Location:index.php?err=1");
	}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Sothink Reseller Control Panel</title>
<link href="style.css" rel="stylesheet" type="text/css">
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
</head>
<body>

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
<td valign="top" class="innerborder"></td>
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
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td valign="top" width="8"><img src="images/p_t.gif" border="0" alt="" width="8" height="1"></td>
<td valign="top" width="100%"><h1>Sothink Reseller Control Panel</h1>
  <!--LINE-->

<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td class="outerborder"><img src="images/p_t.gif" border="0" alt="" width="1" height="1"></td>
</tr>
</table>
<!--LINE-->
<p class="error">
<?php 
$err = $_GET['err'];
if($err==1)
{
$err_msg = "Your username and password does not match.Please login again.Thank you.";
} 
print $err_msg;
?></p>
<p>Welcome to the sothink Reseller Control Panel. Please log in by entering your user name and password.</p>
<p>
<table cellpadding="2" cellspacing="2" border="0" width="100%" class="active">
<form action="" method="post" name="loginform">

<tr>
<td valign="top" nowrap>User name:</td>

<td valign="top" width="100%"><INPUT TYPE="TEXT" NAME="username" VALUE="" SIZE="20" MAXLENGTH="15" >&nbsp;

</td>
</tr>
<tr>
<td valign="top" nowrap>Password:</td>
<td valign="top" width="100%"><INPUT TYPE="password" NAME="password" VALUE="" SIZE="20" MAXLENGTH="200" >&nbsp;</td>
</tr>
<tr>
<td valign="top" nowrap>&nbsp;</td>
<td valign="top" width="100%"><input type="submit" name="action" value="Login"></td>
</tr>
</form>
</table>
</p>
</td>
<td valign="top" width="8"><img src="images/p_t.gif" border="0" alt="" width="8" height="1"></td>
</tr>
<tr>
<td valign="top" width="8"><img src="images/p_t.gif" border="0" alt="" width="8" height="1"></td>
<td valign="top" width="100%">
<!--LINE-->
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td class="outerborder"><img src="images/p_t.gif" border="0" alt="" width="1" height="1"></td>
</tr>
</table>
<!--LINE-->
</td>
<td valign="top" width="8"><img src="images/p_t.gif" border="0" alt="" width="8" height="1"></td>
</tr>
</table>
</td>
<td width="1" class="outerborder"><img src="images/p_t.gif" border="0" alt="" width="1" height="1"></td>
</tr>
</table>
<!--/MIDDLE ROW-->

<!--BOTTOM ROW-->
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td width="6" rowspan="2"><img src="images/i_edge_lu.gif" border="0" alt="" width="6" height="6"></td>
<td width="100%" class="content"><img src="images/p_t.gif" border="0" alt="" width="1" height="5"></td>
<td width="6" rowspan="2"><img src="images/i_edge_ru.gif" border="0" alt="" width="6" height="6"></td>
</tr>
<tr>
<td width="100%" class="outerborder"><img src="images/p_t.gif" border="0" alt="" width="1" height="1"></td>
</tr>
</table>
<!--/BOTTOM ROW-->
<!--/INNER BORDER-->
<!--/CONTENT-->
</td>
<td width="5" class="innerborder"><img src="images/p_t.gif" border="0" alt="" width="5" height="1"></td>
<td width="1" class="outerborder"><img src="images/p_t.gif" border="0" alt="" width="1" height="1"></td>

</tr>
</table>
<!--/MIDDLE ROW-->
<!--BOTTOM ROW-->
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td width="6" rowspan="2" class="innerborder"><img src="images/o_edge_lu.gif" border="0" alt="" width="6" height="6"></td>
<td width="100%" class="innerborder"><img src="images/p_t.gif" border="0" alt="" width="1" height="5"></td>
<td width="6" rowspan="2" class="innerborder"><img src="images/o_edge_ru.gif" border="0" alt="" width="6" height="6"></td>
</tr>
<tr>
<td width="100%" class="outerborder"><img src="images/p_t.gif" border="0" alt="" width="1" height="1"></td>
</tr>
</table>
<!--BOTTOM ROW-->
<!--/OUTER BORDER-->

</body>

</html>
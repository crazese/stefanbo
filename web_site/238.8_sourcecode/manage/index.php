<?php 
header ("Location:/manage/main/Admin/login.php");
/*  	include ("auth.php"); 
	if($_POST['action'] == 'Login')
	{
	$username=$_POST['username'];
	$password=$_POST['password'];
	$Auth = new auth();
	$detail = $Auth->authenticate($username, $password);

	if ($detail==1)
	{
		setcookie("cookie[username]", $username, time()+3600);
		setcookie("cookie[password]", $password, time()+3600);
		header("Location: ces/ces_manage.php");
	}
	else 
	{
		//header("Location:failed.php ");
		header("Location:index.php?err=1");
	}
	}
	if ($_COOKIE['cookie'])
	{
	   header("Location: ces/ces_manage.php");
	}*/
?><style type="text/css">
<!--
body,td,th {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #000000;
}
-->
</style>
<table width="60%" border="0" align="center" cellpadding="0" cellspacing="0">
<tr>
<td width="1" class="outerborder"><img src="images/p_t.gif" border="0" alt="" width="1" height="1"></td>
<td width="5" class="innerborder"><img src="images/p_t.gif" border="0" alt="" width="5" height="1"></td>
<td valign="top" class="innerborder"></td>
<td valign="top" width="100%" class="innerborder">
<!--CONTENT-->
<!--INNER BORDER-->
<!--TOP ROW-->
<!--/TOP ROW-->
<!--MIDDLE ROW-->
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td width="1" class="outerborder"><img src="images/p_t.gif" border="0" alt="" width="1" height="1"></td>
<td width="100%" valign="top" class="content">
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td valign="top" width="8"><img src="images/p_t.gif" border="0" alt="" width="8" height="1"></td>
<td valign="top" width="100%"><h1>Sothink  Control Panel</h1>
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
<p>Welcome to the sothink Control Panel. Please log in by entering your user name and password.</p>
<p>
<table cellpadding="2" cellspacing="2" border="0" width="100%" class="active">
<form action="" method="post" name="loginform">

<tr>
<td valign="top" nowrap bgcolor="#DDDDDD">User name:</td>

<td width="100%" valign="top" bgcolor="#DDDDDD"><INPUT TYPE="TEXT" NAME="username" VALUE="" SIZE="20" MAXLENGTH="15" >&nbsp;

</td>
</tr>
<tr>
<td valign="top" nowrap bgcolor="#DDDDDD">Password:</td>
<td width="100%" valign="top" bgcolor="#DDDDDD"><INPUT TYPE="password" NAME="password" VALUE="" SIZE="20" MAXLENGTH="200" >&nbsp;</td>
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
<!--/BOTTOM ROW-->
<!--/INNER BORDER-->
<!--/CONTENT--></td>
<td width="5" class="innerborder"><img src="images/p_t.gif" border="0" alt="" width="5" height="1"></td>
<td width="1" class="outerborder"><img src="images/p_t.gif" border="0" alt="" width="1" height="1"></td>

</tr>
</table>
<!--/MIDDLE ROW-->
<!--BOTTOM ROW-->
<!--BOTTOM ROW-->
<!--/OUTER BORDER-->
</body>

</html>
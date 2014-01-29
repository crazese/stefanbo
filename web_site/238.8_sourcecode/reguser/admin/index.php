<? include ("authconfig.php"); 
/*   if ($_COOKIE['cookie']['username']!="")
   {
      echo "<script language=\"javascript\">location.href='reseller.php'</script>";
	  exit;
   }*/
?>
<html>
<head>
<title>Reseller Control Panel Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF" text="#000000">
<table width="600" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td>
      <p align="center"><font face="Arial, Helvetica, sans-serif" size="5"><b><font size="4">SourceTec 
        Software Reseller Control Panel Login</font></b></font></p>
      <form name="Sample" method="post" action="<? print $resultpage ?>">
        <table width="60%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" align="center">
          <tr> 
            <td colspan="2" bgcolor="#FFFFCC" valign="middle"> 
              <div align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="3"><b>Login</b></font></div>
            </td>
          </tr>
          <tr> 
            <td width="32%" bgcolor="#CCCCCC" valign="middle"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Reseller 
              Name </font></b></td>
            <td width="68%" valign="middle"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2"> 
              &nbsp; 
              <input type="text" name="username" size="15" maxlength="15">
              </font></b></td>
          </tr>
          <tr> 
            <td width="32%" bgcolor="#CCCCCC" valign="middle"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Password</font></b></td>
            <td width="68%" valign="middle"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2"> 
              &nbsp; 
              <input type="password" name="password" size="15" maxlength="15">
              </font></b></td>
          </tr>
          <tr valign="middle" bgcolor="#CCCCCC"> 
            <td colspan="2"> 
              <div align="center"> 
                <input type="submit" name="Login" value="Login">
                <input type="reset" name="Clear" value="Clear">
              </div>
            </td>
          </tr>
        </table>
      </form>
      <p>&nbsp;</p>
</td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>

<html>
<head>
<script type="text/javascript" src="../JS/MD5.js"></script>
<title>Login</title><style type="text/css">
<!--
body,td,th {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #000000;
}
-->
</style><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>

<body background="images/bj_logo.GIF">
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr align="center">
    <td height="80%" align="center" valign="middle" background=""><form name="form1" method="post" action="checkUser.php" onSubmit="return LoginOK();">
      <table width="600" height="400" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td>
		  <table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td align="center"><h1>Sothink Control Panel</h1>
</td>
              </tr>
            </table>
		  <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td width="35%" align="right" class="midWhite">Username&#65306;&nbsp;</td>
              <td width="56%" valign="middle"><div align="left">
                  <input name="userName" type="text" style="font-size:12px;width:160px">
              </div></td>
              <td width="9%">&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td align="right" class="midWhite">Password&#65306;&nbsp;</td>
              <td valign="middle"><div align="left">
                  <input type="password" name="userPass" style="font-size:12px;width:160px" />
                  <input type="hidden" value="check" name="menu" />
              </div></td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td valign="middle">&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td colspan="2" align="center"><input type="reset" name="Submit2" value="Reset">
                  <input type="submit" name="Submit" value="Submit"></td>
              <td>&nbsp;</td>
            </tr>
          </table>
            </td>
        </tr>
      </table>
    </form>
    </td>
  </tr>
</table>
</body>
</html>
<script language="javascript" for="document" event="onkeydown"> 
<!-- 
if(event.keyCode==13){ 
	LoginOK();
}
-->
</script>
<script language="javascript">
<!--
function LoginOK(){
	with(form1){
		var re;
		re=/ /g;
		user=(userName.value).replace(re,"");
		if(user == ""){
			alert("Please input Username!");
			userName.focus();
			return false;
		}else if(userPass.value == ""){
			alert("Please input password!");
			userPass.focus();
			return false;
		}
		else{
			//userPass.value=MD5(userPass.value);
			submit();
		}
	}
}
--> 
</script>
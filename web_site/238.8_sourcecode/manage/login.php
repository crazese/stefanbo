<?php 	include("include/header.php");?> 
<table width="760"border=0   align="center" cellpadding=0 cellspacing=0> 
          <tr> 
    <td width="760" ><table width="760" border="0" align="center" cellpadding="3" cellspacing="0" background="images/title/1X30.jpg"> 
        <tr> 
          <td width="376" height="30"><p> Login </p></td> 
          <td width="372" height="30" align="right" nowrap><p>&nbsp;</p></td> 
        </tr> 
      </table></td> 
  </tr> 
        </table> 
<table width="760" border=0 align="center" cellpadding=0 cellspacing=0> 
          <tr> 
    <td width="100%" class="tableborders"> <table cellpadding="3" cellspacing="1" width="100%" border=0> 
        <tr class="darktable"> 
          <td> <p>Enter your Username and Password to login. </p> 
            <br></td> 
        </tr> 
        <tr> 
          <td class="lighttable"> <form name="LoginInfo" method="post" action="loginprocess.php"> 
              <p>Username: <br> 
                <input name=UserName type=text class="formboxes" id="UserName" size="20"> 
              <p> Password:<br> 
                <input type=password name = Password class="formboxes" size="22"> 
              <p> 
                <input name="bKeeppassword" type="checkbox" class="formboxes" id="bKeeppassword" value="checkbox"> 
                Remember my login name and password.
              <p> 
                <input type=submit name=Submit1 value="Login" class="buttons"> 
            </form></td> 
        </tr> 
      </table></td> 
    <td width="24" height="10"> 
  </tr> 
 </table> 
<?php	include("include/footer.php");?>		
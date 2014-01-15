<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>OC Soft Web Admin - User admin</title>
<link href="../CSS/admin.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../JS/MD5.js"></script>
</head>

<body>
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$oc_dbStr="../../";
require_once("../../Inc/Conn.inc");

$linkid = $_GET['id'] ? $_GET['id'] : $_POST['id'];
$linkid = intval($linkid);
if($linkid != ""){
	$action = "alter";
}else{
	$action = "add";
}

$sql="select * from admin_userinfo where id='".$linkid."' limit 1";
$result = mysql_query($sql) or die("Invalid query : ". mysql_error() . "<br/>");
$row = mysql_fetch_row($result);
$username = $row[1];
$password = $row[2];
$truthname = $row[4];
$masterQx = $row[3];
$workId = $row[5];
$dptment = $row[6];
mysql_free_result($result);
?>
<table width="90%" height="245" border="0" align="center" cellpadding="0" class="SmallWhiteText">
        <form method="post" name="alterform" id="alterform" action="saveUser.php">
          <tr align="center">
            <td colspan="3">帐号管理</td>
          </tr>
          <tr>
            <td width="310" align="right">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td align="right">用名:</td>
            <td colspan="2"><input name="username" type="text" size="15" value="<?php echo $username?>" /></td>
          </tr>
          <tr>
            <td align="right">密码:</td>
            <td width="139"><input name="password" type="password" size="20" value="<?php echo $password?>" onfocus="javascript:this.value='';" /></td>
            <td width="262"><input name="ifalterpswd" type="checkbox" onclick="alterpswd()" value="1" />
              (请选择)</td>
          </tr>
          <tr>
            <td align="right">姓名:</td>
            <td colspan="2"><input name="truthname" type="text" size="15" value="<?php echo $truthname?>" /></td>
          </tr>
          <tr>
            <td align="right">权限:</td>
            <td colspan="2"><select name="masterQx">
			<?php 
			$oc_adminQx[0]="超级管理员";
			$oc_adminQx[1]="高级管理员";
			$oc_adminQx[2]="普通管理员";
			
			for($i=0; $i<3; $i++){
				if($masterQx == $i and $action == "alter"){
					echo "<option value='".$i."' selected=true>".$oc_adminQx[$i]."</option>";
				}elseif($i == 2 and $action == "add"){
					echo "<option value='".$i."' selected=true>".$oc_adminQx[$i]."</option>";
				}else{
					echo "<option value='".$i."'>".$oc_adminQx[$i]."</option>";
				}
			}
			?></select></td>
          </tr>
          <tr>
            <td align="right">工号:</td>
            <td colspan="2"><input type="text" name="workId" value="<?php echo $workId?>" /></td>
          </tr>
          <tr>
            <td align="right">部门:</td>
            <td colspan="2"><input type="text" name="dptment" value="<?php echo $dptment?>" /></td>
          </tr>
          <tr>
            <td colspan="3" align="right">&nbsp;</td>
            </tr>
          <tr>
            <td colspan="3"><hr size="1" /></td>
          </tr>
          <tr>
            <td colspan="3">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="3"><table width="40%"  border="0" align="center" cellpadding="0" cellspacing="0">
                <tr align="center">
                  <td><input type="button" name="cancelbutton" value="取消" onclick="javascript:self.close();" />
                      <input type="hidden" name="id" value="<?php echo $linkid?>" />
                  </td>
                  <td><input type="button" name="savebutton" value="保存" onclick="savealter()" />
                      <input type="hidden" name="action" value="<?php echo $action?>" />
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </form>
      </table>
</body>
</html>
<script language="javascript">
<!--
function savealter(){
	with(alterform){
		if(action.value=="add"){
			action.value="saveadd";
		}
		if(action.value=="alter"){
			action.value="savealter";
		}
		password.value=MD5(password.value);
		submit();
	}
}

function alterpswd(){
	with(alterform){
		if(ifalterpswd.checked==false){
			password.disabled=true;
		}
		else if(ifalterpswd.checked==true){
			password.disabled=false;
		}
	}	
}
alterpswd();
-->
</script>
<script language="javascript" for="document" event="onkeydown"> 
<!-- 
if(event.srcElement.type!='reset' && event.keyCode==13){ 
	savealter();
}
--> 
</script>
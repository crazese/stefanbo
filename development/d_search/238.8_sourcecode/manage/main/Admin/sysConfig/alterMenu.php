<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>Sothinkcn web admin - alter system menu</title>
<link href="../CSS/admin.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$oc_dbStr="../../";
require_once("../../Inc/Conn.inc");

$linkid = intval($_GET['id'] ? $_GET['id'] : $_POST['id']);
if($linkid != ""){
	$action = "alter";
}else{
	$action = "add";
}

$sql="select * from sys_menu where id='".$linkid."' limit 1";
$result = mysql_query($sql) or die("Invalid query : ". mysql_error() . "<br/>");
$row = mysql_fetch_row($result);

$higherMenu=$row[1];
$menuName=$row[2];
$linkPage=$row[3];
$picpath=$row[4];
$masterQx=$row[5];
$menuLevel=$row[6];
$ordernum=$row[7];
$target = $row[8];
//echo $target;

mysql_free_result($result);
?>
<table width="96%" height="245" border="0" align="center" cellpadding="0" class="SmallWhiteText">
      <form action="saveMenu.php" method="post" enctype="multipart/form-data" name="alterform" id="alterform">
        <tr align="center">
          <td colspan="4">�˵��޸�</td>
          </tr>
        <tr>
          <td align="right">&nbsp;</td>
          <td>&nbsp;</td>
          <td align="center">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td width="396" align="right">�˵���:</td>
          <td width="148"><input type="text" name="menuName" value="<?php echo $menuName?>" /></td>
          <td width="164" rowspan="3" align="right" valign="top"><?php
	  if($picpath != ""){
		echo "<img src=\"".$oc_dbStr."Admin/".$picpath."\" border=\"0\">";
	  }else{
		echo "&nbsp;";
	  }?></td>
          <td width="251" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td align="right">�ϼ��˵�:</td>
          <td><input type="text" name="higherMenu" value="<?php echo $higherMenu?>" /></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td align="right">����Ȩ��:</td>
          <td><select name="masterQx">
			<?php 
			$oc_adminQx[0]="��������Ա";
			$oc_adminQx[1]="�߼�����Ա";
			$oc_adminQx[2]="��ͨ����Ա";
			
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
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td align="right">�ϴ�ͼ��:</td>
          <td colspan="3"><input type="file" name="file" /></td>
        </tr>
		<tr>
          <td align="right">����ҳ��:</td>
          <td colspan="3"><input name="linkPage" type="text" value="<?php echo $linkPage?>" size="40" /></td>
        </tr>
        <tr>
          <td align="right">�˵�����:</td>
          <td><select name="menuLevel">
			<?php 
			$oc_menuLevel[0]="һ���˵�";
			$oc_menuLevel[1]="�����˵�";
			$oc_menuLevel[2]="�����˵�";
			
			for($i=0; $i<$sysMenu_level; $i++){
				if($menuLevel == $i and $action == "alter"){
					echo "<option value='".$i."' selected=true>".$oc_menuLevel[$i]."</option>";
				}else{
					echo "<option value='".$i."'>".$oc_menuLevel[$i]."</option>";
				}
			}
			?></select></td>
          <td colspan="2">����:
          <input type="text" name="ordernum" value="<?php echo $ordernum?>" style="width:40px; " /></td>
        </tr>
        <tr>
          <td align="right">�򿪷�ʽ:</td>
          <td><select name="target" id="target">
              <option value="mainFrame" <?php if ($target=='mainFrame' || $target=='') echo "selected";?> >mainFrame</option>
              <option value="_blank" <?php if ($target=='_blank') echo "selected";?>>_blank</option>
          </select></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4"><hr size="1" /></td>
          </tr>
        <tr>
          <td colspan="4">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4"><table width="40%"  border="0" align="center" cellpadding="0" cellspacing="0">
              <tr align="center">
                <td><input type="button" name="cancelbutton" value="ȡ��" onClick="javascript:self.close();" />
                    <input type="hidden" name="id" value="<?php echo $linkid?>" />				</td>
                <td><input type="button" name="savebutton" value="����" onClick="savealter()" />
                    <input type="hidden" name="action" value="<?php echo $action?>" />
					<input type="hidden" name="MAX_FILE_SIZE" value="10000" />				</td>
              </tr>
          </table>		  </td>
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
		submit();
	}
}
-->
</script>
<script language="javascript" for="document" event="onkeydown"> 
<!-- 
if(event.srcElement.type!='reset' && event.keyCode==13){ 
	savealter();
}
--> 
</script>
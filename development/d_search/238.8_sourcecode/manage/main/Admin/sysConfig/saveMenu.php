<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>file upload</title>
</head>

<body>
<?Php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$oc_dbStr="../../";
require_once("../../Inc/Conn.inc");

$f=$_FILES['file'];
if($f['name'] != ""){	//upload file
	if($f['type'] != "image/pjpg" && $f['type'] != "image/gif"){
		echo "���ϴ�Gif����JpgͼƬ��";
	}else{
		$dest_dir='../images/upPics';
		$dest=$dest_dir."/".$f['name'];			//�ļ���+�ļ���
		$r=move_uploaded_file($f['tmp_name'],$dest); 	//move_upload_file(�ϴ��ļ���,�ϴ�Ŀ¼)
		chmod($dest,0755);  				//�趨�ļ�Ȩ��chmod(�ļ���,Ȩ��һ���ǰ˽��Ƶ�0755)
		echo "ͼƬ�ϴ��ɹ���<a href=".$dest." target=_blank>����鿴</a><br/>";
	}
}

$higherMenu=addslashes($_GET['higherMenu'] ? $_GET['higherMenu'] : $_POST['higherMenu']);
$menuName=addslashes($_GET['menuName'] ? $_GET['menuName'] : $_POST['menuName']);
$linkPage=addslashes($_GET['linkPage'] ? $_GET['linkPage'] : $_POST['linkPage']);
$picPath=str_replace("../","",$dest);
$masterQx=intval($_GET['masterQx'] ? $_GET['masterQx'] : $_POST['masterQx']);
$menuLevel=intval($_GET['menuLevel'] ? $_GET['menuLevel'] : $_POST['menuLevel']);
$orderNum=intval($_GET['ordernum'] ? $_GET['ordernum'] : $_POST['ordernum']);

$action=$_GET['action'] ? $_GET['action'] : $_POST['action'];
$linkid=intval($_GET['id'] ? $_GET['id'] : $_POST['id']);
$page = intval($_GET['page'] ? $_GET['page'] : $_POST['page']);
$target = $_GET['target'] ? $_GET['target'] : $_POST['target'];

if($action == "saveadd"){
	$sql="insert into sys_menu(higherMenu,menuName,linkPage,picPath,masterQx,menuLevel,orderNum,target) 
	     	values('$higherMenu','$menuName','$linkPage','$picPath','$masterQx','$menuLevel','$orderNum','$target')";

	$conn->exec($sql);	//insert
	echo "<script language=javascript>
		<!--
		alert(\"��ӳɹ���\");
		history.back(0);
		-->
		</script>";
}elseif($action == "savealter"){
	$sql="select * from sys_menu where id='$linkid'";
	$result = mysql_query($sql) or die("Invalid query : ". mysql_error() . "<br/>");
	$row = mysql_fetch_row($result);
	$or_menuName=$row[2];

	$sql="update sys_menu set higherMenu='$menuName' where higherMenu='$or_menuName'";
	$conn->exec($sql);	//update higherMenu

	//update menu
	$sql="update sys_menu set higherMenu='$higherMenu',
		menuName='$menuName',
		linkPage='$linkPage',
		masterQx='$masterQx',
		menuLevel='$menuLevel',
		orderNum='$orderNum',
		target='$target'";

	if($picPath != ""){
		$sql=$sql.",picPath='$picPath' ";
	}

	$sql=$sql." where id='$linkid'";

	$conn->exec($sql);
	echo "<script language=javascript>
		<!--
		alert(\"�޸ĳɹ���\");
		window.opener.location.reload();
		self.close();
		-->
		</script>";
}elseif($action == "del"){
	$sql="delete from sys_menu where id='$linkid'";
	$conn->exec($sql);
	echo "<script language=javascript>
		<!--
		alert(\"ɾ���ɹ���\");
		location.href='showMenu.php?page=$page';
		-->
		</script>";
}
?>
</body>
</html>

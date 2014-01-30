<?php
/*
	file name:check user
	author:shmeily
	created:2007-03-13
	alter:
*/
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$oc_dbStr="../";
require_once "../Inc/Conn.inc";
require_once "../Config/Config.php";

$menu= $_POST['menu'] ? $_POST['menu'] : $_GET['menu'];
$userName= $_POST['userName'] ? $_POST['userName'] : $_GET['userName'];
$userPass= $_POST['userPass'] ? $_POST['userPass'] : $_GET['userPass'];
$userPass = md5($userPass);
/*echo $userPass;
exit;*/
if($menu=="check"){	//check user
	$sql="select * from admin_userinfo where userName='".addslashes($userName)."' limit 1";
	$result=mysql_query($sql) or die("Invalid query : ". mysql_error() . "<br/>");
	$num_rows = mysql_num_rows($result);

	if($num_rows == 0){	//if wrong username then back
		echo "<script language='javascript'>alert(\"Your username and password does not match.Please login again.Thank you£¡\");
			history.back(0);</script>";		
	}
	elseif ($num_rows == 1){
		$row = mysql_fetch_row($result);
		if($row[2] == $userPass){	//if username and userpass are correct
			session_start();
			$_SESSION['userId']=$row[0];
			$_SESSION['userQx']=$row[3];
			$_SESSION['userName']=$userName;
		    setcookie("cookie[sothinkusername]", $userName, time()+3600,"/");
		    setcookie("cookie[sothinkpassword]", $userPass, time()+3600,"/");
			//print_r($_COOKIE['cookie']);

			$sql="update admin_userinfo set ifOnline='1' where id='".$row[0]."'";
			$conn->exec($sql);
			header("Location: index.htm");
		}else{
			echo "<script language='javascript'>alert(\"Your username and password does not match.Please login again.Thank you.\");
				history.back(0);</script>";
		}
	}

}elseif($menu=="out"){	//user quit
	session_start();
	$sql="update admin_userinfo set ifOnline='0' where id='".$_SESSION['userId']."'";
	$conn->exec($sql);
	session_unset();
	setcookie("cookie[sothinkusername]", "", time()-3600,"/");
	setcookie("cookie[sothinkpassword]", "", time()-3600,"/");	
	header("Location: login.php");
}
?>
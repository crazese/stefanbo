<?php
	define('IN_PAGE',true);	
	require_once("./inc/init.php");

$msg = $_GET['msg'];
switch ($msg) {
	case "PASSWORD_RECOVERY":
		$title = "Password Recovery";
		$nav = "Password Recovery";
		break;
	case "ERROR_USER_PASSWORD":
		$title = "Incorrect Password";
		$nav = "Error";
		break;
	case "ERROR_USER_NOT_EXISTS":
		$title = "User Not Exist";
		$nav = "Error";
		break;
	case "stufferror":
		$title = "Tip";
		$nav = "Stuff";
		break;
	case "AlreadyLogin":
		$title = "Welcome";
		$nav = "Tip";
		break;
	case "UserAlreadyRegisted":
		$title = "You Already Registed";
		$nav = "Tip";
		break;
	case "SendMailSuccess":
		$title = "Send Mail Success";
		$nav = "Tip";
		break;
	case "SendMailFailure":
		$title = "Send Mail Failure";
		$nav = "Error";
		break;
	default:
		$title = "Error";
		$nav = "Error";
}

$smart_error = new Smarty();
$smart_error->templates_dir = './templates/';
$smart_error->compile_dir = './templates_c/';
$smart_error->cache_dir = './cache/';
$smart_error->assign('categories',$categories);
$smart_error->assign('categOptions',$categOptions);
$smart_error->assign('purchased',$purchased);
$smart_error->assign('msg',$msg);
$smart_error->assign('nav',$nav);
$smart_error->assign('title',$title);
$smart_error->display('error.htm');
?>
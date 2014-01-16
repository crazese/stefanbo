<?

$resultpage = "Authenticate.php";	// THIS IS THE PAGE THAT WOULD CHECK FOR AUTHENTICITY

$admin = "admin/index.php";	// THIS IS THE PATH TO THE ADMIN INTERFACE
$success = "../protected/test.zip";	// THIS IS THE PAGE TO BE SHOWN IF USER IS AUTHENTICATED
$inactive = "inactive.php";	// THIS IS THE PAGE TO BE SHOWN IF USER'S STATUS IS SET TO INACTIVE
$failure = "failed.php";	// THIS IS THE PAGE TO BE SHOWN IF USERNAME-PASSWORD COMBINATION DOES NOT MATCH
$downurl = "/reguser/download.php?fid="; 	// Download URL

$admin_email = "lili@sothink.com.cn";
$mail_sender = "/usr/bin/sm.php";
$temp_file = "/var/www/temp/res.txt";
$reg_gen = "/usr/bin/regcode";

//DB SETTINGS
$dbhost = "localhost";  // DB Host name
$dbusername = "sothink";        // DB User
$dbpass = "K2v3P494";    // DB User password
$dbname = "authuser";   // DB Name


?>
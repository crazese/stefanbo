<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>PWeb 2.0 PHP�� ���ݿⰲװ</title>
</head>

<body>
<?php
/*
	[Oncecode!] (C)2007-2012
	This is a freeware!

	file: install.php
	version: 1.0
	author:shemily
	created:2007-3-7
*/
$oc_dbStr = "../";
require_once "../Config/Config.inc";
require_once "../Inc/Conn.inc";

//---install database from file-----
set_magic_quotes_runtime(0);

$sqlfile=realpath("../Install/Pweb.txt");
$fp = fopen($sqlfile, 'rb');
$sql = fread($fp, 2048000);

//or replace them
//$sql = preg_replace("/\\\'/","'",$sql);
fclose($fp);

set_magic_quotes_runtime(1);
$conn->query($sql);		//excute sql query
//----------------------------------

echo "PWeb 2.0 PHP�� ���ݿⰲװ�ɹ���<br/><br/>";
echo "��������".$conn->getHostStr()."<br/>";
echo "���ݿ⣺".$dataBase."<br/>";
echo "�����û���".$conn->getUserName()."<br/>";
echo "�������ڣ�".date('Y-m-d H:i:s');
?>
</body>
</html>
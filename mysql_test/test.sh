$mysqlhost="localhost";
$mysqluser="root";
$mysqlpwd="123456";
$mysqldb="authserver";
$conn=@mysql_connect($mysqlhost,$mysqluser,$mysqlpwd) or die('wrong');

mysql_select_db($mysqldb,$conn);

#!/usr/bin/env python
#get the html contents with authorization 
import urllib2, base64
import re

user = 'admin'
pw = 'password'

html = 'http://xxx.xxx.xxx'

def get_html(login_user,login_pw,html):
    auth = 'Basic' + base64.b64encode(login_user+':'+login_pw)
    log_heads = {'Referer': html,
                 'Authorization' : auth}
    log_request = urllib2.Request(html,None,log_heads)
    log_response = urllib2.urlopen(log_request)
    result = log_response.read()
    return result

test = get_html(user,pw,html)
$query=mysql_query("select * from ol_serverlist");

while($rs=mysql_fetch_array($query)){
        echo $rs["servername"];
}



<?php
    $pagesize = 5; //每页显示5条记录
    $host="localhost";
    $user="auth";
    $password="123456";
    $dbname="authserver"; //所查询的库表名；
    //连接MySQL数据库
    mysql_connect（"$host"，"$user"，"$password"） or die（"无法连接MySQL数据库服务器！"）;
    $db = mysql_select_db（"$dbname"） or die（"无法连接数据库！"）;
    $sql = "select * from ol_serverlist";//生成查询记录数的SQL语句
    $rst = mysql_query（$sql） or die（"无法执行SQL语句：$sql ！"）; //查询记录数
    $row = mysql_fetch_array（$rst） or die（"没有更多的记录！"）; /取出一条记录
    echo $row
    ?>

<?php
$con = mysql_connect("localhost","auth","123456");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("authserver", $con);

$result = mysql_query("SELECT * FROM ol_serverlist");
echo $result
while($row = mysql_fetch_array($result))
  {
  echo $row;
  echo "<br />";
  }

mysql_close($con);
?>

<?php
$con = mysql_connect("localhost","auth","123456");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

if (mysql_query("CREATE DATABASE my_db",$con))
  {
  echo "Database created";
  }
else
  {
  echo "Error creating database: " . mysql_error();
  }

mysql_close($con);
?>


<?php
$con = mysql_connect("127.0.0.1","hero","123456");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

if (mysql_query("CREATE DATABASE my_db",$con))
  {
  echo "Database created";
  }
else
  {
  echo "Error creating database: " . mysql_error();
  }

mysql_close($con);
?>

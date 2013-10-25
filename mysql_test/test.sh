$mysqlhost="localhost";
$mysqluser="root";
$mysqlpwd="123456";
$mysqldb="authserver";
$conn=@mysql_connect($mysqlhost,$mysqluser,$mysqlpwd) or die('wrong');

mysql_select_db($mysqldb,$conn);

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
<?
$q="select * from Reseller where uname='sothink'";
                $connection = mysql_connect("192.168.0.3","webtest" , "batian")
or die("Connect Failed");
print("seccess");

                $result = mysql_db_query("authuser", $q);
                $numrows = mysql_num_rows($result);
                $row = mysql_fetch_array($result);

                // CHECK IF THERE ARE RESULS

print("number = $numrows");




?>

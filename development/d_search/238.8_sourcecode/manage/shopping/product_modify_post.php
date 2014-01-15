<?php
include "dbconn.php";

$ProductID = $_POST['ProductID'];
$ProductName = $_POST['ProductName'];
$Shortening = $_POST['Shortening'];
$Element5ID = $_POST['Element5ID'];
$PriceUSD =$_POST['PriceUSD'];
$PriceUSD1 =$_POST['PriceUSD1'];
$ClassName = $_POST['ClassName'];
$IsShow = $_POST['bIsShow'];
$IsFullPriceShow = $_POST['bIsFullPriceShow'];
$IsRegcode = $_POST['IsRegcode'];
$SoftwareName = $_POST['SoftwareName'];
$Version = $_POST['Version'];
$Zipfile = $_POST['Zipfile'];
$Link = $_POST['Link'];
$Description =$_POST['Description'];
$track_name=$_POST['track_name'];
$email_file=$_POST['email_file'];
$volumn_discount=$_POST['volumn_discount'];
$command_line = $_POST['command'];
if(!$ProductName)
{
	echo("
		<script>
		window.alert('Please input the Product Name.')
		history.go(-1)
		</script>
		");
	exit;
}


$sql="update tbProduct set ProductName='$ProductName',PriceUSD='$PriceUSD',PriceUSD1='$PriceUSD1',Description='$Description',Shortening='$Shortening',Link='$Link',Element5ID='$Element5ID',ClassName='$ClassName',IsShow='$IsShow',IsFullPriceShow='$IsFullPriceShow',IsRegcode='$IsRegcode',SoftwareName='$SoftwareName',Version='$Version',Zipfile='$Zipfile',track_name='$track_name',email_file='$email_file',volumn_discount='$volumn_discount' where ProductID='$ProductID'";

$result=mysql_query($sql,$dbconn);
if($result)
{
	echo ("	<meta http-equiv='refresh' content='0; url=product_manage.php'>");
}
else
{
        echo("
                <script>
                window.alert('Please try again.')
                history.go(-1)
                </script>
                ");
        exit;
}
mysql_close($dbconn);
?>
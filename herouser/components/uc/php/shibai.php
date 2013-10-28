<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,minimum-scale=1.0, maximum-scale=2.0"/>
<title>充值失败</title>
</head>
<style type="text/css">
body{padding:0px; margin:0px; font-size:13px; background-color:#331509; color:#4f1a00;}
p,span,ul,h3{padding:0px; margin:0px;}
ul li{list-style:none;}
img{border:0px;}
a{color:#4f1a00; text-decoration:none;}
.Wrap{width:100%; height:auto; margin:0 auto;}
.Header{background:url(Images/bg.jpg) repeat-x; width:100%; height:46px; float:left; text-align:center;}
.Header span{width:48px; height:46px; text-align:center;}
.Main{background-color:#fcd59a; border:3px solid #e6a731; width:96.5%; height:auto; margin:0px 0px 10px 1%; float:left; padding-bottom:10px;}
.Tips{width:100%; height:30px; line-height:30px; margin-left:6px;}
.CZ{width:auto; height:auto; margin:6px 6px 0 6px;}
.ListTable{float:left; background-color:#fef0de; border:1px solid #cf7a27; margin-bottom:1px;}
.jine{line-height:22px; text-align:center; margin-bottom:10px; margin-top:5px;}
.green{color:#399b03;}
.tips2{margin:10px 6px;}
.tips2 span{ color:#ff0000;}
h3{margin:10px 0;}
.czleft{margin:0 5px 5px 0;}
.box{width:90%; height:19px; line-height:19px; margin-bottom:5px;}
.fhbtn{width:50px; height:28px; margin:6px 0 10px 6px;}
.qd{background:url(Images/qd.jpg) no-repeat; width:50px; height:28px; border:0px; margin-bottom:5px;}
</style>
<body>
<div class="Wrap">
	<div class="Header"><span><img src="Images/cz.jpg" width="48" height="46" /></span></div>
    <div class="Main">
        <div class="CZ">
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="ListTable">
          <tr>
			<?php 
				if(isset($_GET['err'])) {
					echo '<td align="center"><h3>' . urldecode($_GET['err']) . '</h3></td>';
				} else {
					echo '<td align="center"><h3>充值失败，序列号或密码位数不正确</h3></td>';
				}
			?>
          </tr>
          <tr>
            <td align="center"><a href="index.php?uzone_token=<?php echo isset($_GET['uzone_token']) ? base64_decode($_GET['uzone_token']): ''; ?>"><img class="fhbtn" src="Images/fh.jpg" width="50" height="28" /></a></td>
          </tr>
        </table>
        </div>
    </div>
</div>
</body>
</html>

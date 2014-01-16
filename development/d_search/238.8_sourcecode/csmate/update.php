<?
	$the_lang = $_REQUEST["lang"];
	$the_version = (float)$_REQUEST["version"];
	$the_version_latest = 1.9;
	
	if ($_REQUEST["version"] == "" || $the_version > $the_version_latest)
	{
		print("Error: invalid version information.");
		exit();
	}
	
	$current_version_label;
	$latest_version_label;
	$new_vesion_available;
	$no_new_version_available;
	$download_label;
?>
<html>
<head>
<?
if($the_lang == 0x0402)
{
	$current_version_label = "您安装的版本:";
	$latest_version_label = "最新版本:";
	$new_vesion_available = "有新版本!";
	$no_new_version_available = "目前没有新版本。";
	$download_label = "立即下载";
	$the_download_url = "http://www.sothink.com.cn/csmate/"
?>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>CSMate - 检查软件更新</title>
<?
}
else
{
	$current_version_label = "Your current version is:";
	$latest_version_label = "The latest version is:";
	$new_vesion_available = "New version is available!";
	$no_new_version_available = "No new version available.";
	$download_label = "Download now";
	$the_download_url = "http://www.sothink.com/csmate/"
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>CSMate - Check for Updates</title>
<?
}
?>
<style type="text/css">
<!--
.text_12px {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; }
.text_18px {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 18px; }
.red {color: #FF0000; }
.style2 {font-size: 12px}
-->
</style>
</head>

<body bgcolor="#FFFFFF" text="#000000">
<p>&nbsp;</p>
<table width="500"  border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#CCCCCC">
  <tr>
    <td><table width="100%"  border="0" align="center" cellpadding="8" cellspacing="1">
      <tr valign="middle" bgcolor="#FFFFFF">
        <td colspan="2" class="text_18px"><div align="center">CSMate</div></td>
      </tr>
      <tr bgcolor="#FFFFFF" class="text_12px">
        <td width="50%"><? print $current_version_label ?></td>
        <td width="50%"><? printf("%1.1f", $the_version) ?></td>
      </tr>
      <tr bgcolor="#FFFFFF" class="text_12px">
        <td width="50%"><? print $latest_version_label ?></td>
        <td width="50%"><? printf("%1.1f", $the_version_latest) ?></td>
      </tr>
      <tr bgcolor="#FFFFFF" class="text_12px">
        <td colspan="2"><p>
          <? if ($the_version == $the_version_latest) { ?>
          <? print $no_new_version_available ?>
          <? } else { ?>
          <span class="red"><? print $new_vesion_available ?></span> [ <a href="<?=$the_download_url?>"><? print $download_label ?></a> ]
          <? } ?>
        </p></td>
      </tr>
    </table></td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>

<?php
	$product = $_GET['product'];
	$version = $_GET['version'];
	$build = $_GET['build'];
	
	$the_product = (int)$product;
	$the_version = (float)$version;
	$the_build = (int)$build;
	
	if($the_product == 21)
    {
	   header("location:http://www.sothink.com/product/dhtmlmenu/checkforupdate.php?the_product=$the_product&the_version=$the_version&the_build=$the_build");
	   exit;
    }
	if($the_product == 29)
    {
	   header("location:http://www.sothink.com/product/treemenu/checkforupdate.php?the_product=$the_product&the_version=$the_version&the_build=$the_build");
	   exit;
    }
	if($the_product == 46)
    {
	   header("location:http://www.sothink.com/product/dhtmlmenulite/checkforupdate.php?the_product=$the_product&the_version=$the_version&the_build=$the_build");
	   exit;
    }
	
	if($the_product == 26)
    {
	   header("location:http://www.sothinkmedia.com/flash-video-encoder/checkforupdate.php?the_product=$the_product&the_version=$the_version&the_build=$the_build");
	   exit;
    }
	
	if($the_product == 36)
	{
	header("location:http://www.sothinkmedia.com/swf-to-video/checkforupdate.php?the_product=$the_product&the_version=$the_version&the_build=$the_build");
	   exit;
	}
	
	if($the_product == 44)
	{
	header("location:http://www.sothinkmedia.com/flv-player/checkforupdate.php?the_product=$the_product&the_version=$the_version&the_build=$the_build");
	   exit;
	}
	if($the_product == 47)
	{
	header("location:http://www.sothinkmedia.com/web-video-downloader/checkforupdate.php?the_product=$the_product&the_version=$the_version&the_build=$the_build");
	   exit;
	}
	if($the_product == 48)
	{
	header("location:http://www.sothink.com/product/javascriptwebscroller/checkforupdate.php?the_product=$the_product&the_version=$the_version&the_build=$the_build");
	   exit;
	}	
	if($the_product == 51)
	{
	   header("location:http://www.sothinkmedia.com/flv-converter/checkforupdate.php?the_product=$the_product&the_version=$the_version&the_build=$the_build");
	   exit;
	}
	if($the_product == 52)
	{
	   header("location:http://www.sothinkmedia.com/video-converter/checkforupdate.php?the_product=$the_product&the_version=$the_version&the_build=$the_build");
	   exit;
	}	
	if($the_product == 42)
	{
	   header("location:http://www.sothinkmedia.com/movie-dvd-maker/checkforupdate.php?the_product=$the_product&the_version=$the_version&the_build=$the_build");
	   exit;
	}	
	if($the_product == 6)
	{
	   header("location:http://www.sothink.com/product/flashdecompiler/checkforupdate.php?the_product=$the_product&the_version=$the_version&the_build=$the_build");
	   exit;
	}
	if($the_product == 28)
	{
	   header("location:http://www.sothink.com/product/swfeasy/checkforupdate.php?the_product=$the_product&the_version=$the_version&the_build=$the_build");
	   exit;
	}	
	if($the_product == 22)
	{
	   header("location:http://www.sothink.com/product/swfquicker/checkforupdate.php?the_product=$the_product&the_version=$the_version&the_build=$the_build");
	   exit;
	}	
	if($the_product == 57)
	{
	   header("location:http://www.sothinkmedia.com/hd-movie-maker/checkforupdate.php?the_product=$the_product&the_version=$the_version&the_build=$the_build");
	   exit;
	}	
	if($the_product == 33)
	{
	   header("location:http://www.sothinkmedia.com/ipod-video-converter/checkforupdate.php?the_product=$the_product&the_version=$the_version&the_build=$the_build");
	   exit;
	}	
	if($the_product == 59)
	{
	   header("location:http://www.sothinkmedia.com/iphone-video-converter/checkforupdate.php?the_product=$the_product&the_version=$the_version&the_build=$the_build");
	   exit;
	}
	if($the_product == 38)
	{
	   header("location:http://www.sothinkmedia.com/video-converter/checkforupdate.php?the_product=$the_product&the_version=$the_version&the_build=$the_build");
	   exit;
	}
								
	if($the_product == 40)
	{
	   header("location:http://www.sothinkmedia.com/video-converter/checkforupdate.php?the_product=$the_product&the_version=$the_version&the_build=$the_build");
	   exit;
	}	
	if($the_product == 37)
	{
	   header("location:http://www.sothinkmedia.com/dvd-ripper/checkforupdate.php?the_product=$the_product&the_version=$the_version&the_build=$the_build");
	   exit;
	}	
	if($the_product == 41)
	{
	   header("location:http://www.sothinkmedia.com/dvd-ripper/checkforupdate.php?the_product=$the_product&the_version=$the_version&the_build=$the_build");
	   exit;
	}					
	$current_version_label;
	$latest_version_label;
	$new_vesion_available;
	$whats_new_label;
	$download_label;
	$no_new_version_label;
?>
<?
	// fix the Product ID error in Glanda 2004 Prelease, Build 40927
	if ($the_product == 22 && $the_version == 2.0 && $the_build == 40927)
	{
		$the_product = 28;
	}
	// fix the product id error in Glanda 2005 Build 50718 Simplified Chinese version
	if ($the_product == 28 && $the_version == 2.4 && $the_build == 50718)
	{
		$the_product = 11;
	}
?>
<html>
<head>
<?
if($the_product == 8 || $the_product == 9 || $the_product == 11 || $the_product == 23)
{
	$current_version_label = "您当前的版本:";
	$latest_version_label = "最新版本：";
	$new_vesion_available = "最新版已经发布";
	$whats_new_label = "版本更新";
	$download_label = "立即下载";
	$no_new_version_label = "没有新版本更新";
?>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>?ì2éèí?t?üD?</title>
<?
}
else
{
	$current_version_label = "Your current version is:";
	$latest_version_label = "The latest version is:";
	$new_vesion_available = "New version is available!";
	$whats_new_label = "What's New";
	$download_label = "Download now";
	$no_new_version_label = "No new version available.";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Check for Updates</title>
<?
}
?>
<style type="text/css">
<!--
.text_12px {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; }
.text_18px {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 18px; }
.red {color: #FF0000; }
-->
</style>
</head>

<body style="background-color:#FFFFFF; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; color:#000000">

<?
$the_product_name;
$the_version_latest;
$the_build_latest;
$the_product_url;
$the_download_url;

// Begin all products
if ($the_product == 6)
{
	$the_product_name = "Sothink SWF Decompiler 4.3";
	$the_version_latest = 4.3;
	$the_build_latest = 80806;
	$the_product_url = "http://www.sothink.com/product/flashdecompiler/";
	$the_download_url = "http://www2.sothink.com/download/swfdec.zip";
}
else if ($the_product == 8)
{
	$the_product_name = "硕思闪客精灵 3";
	$the_version_latest = 3.3;
	$the_build_latest = 60720;
	$the_product_url = "http://www.sothink.com.cn/flashdecompiler/index.htm";
	$the_download_url = "http://www.sothink.com.cn/download/swfdecchspro.zip";
}
else if($the_product == 9)
{
	$the_product_name = "硕思魔法菜单";
	$the_version_latest = 4.3;
	$the_build_latest = 31226;
	$the_product_url = "http://www.sothink.com.cn/product/dhtmlmenu/";
	$the_download_url = "http://www.sothink.com.cn/download/sdmenu.zip";
}
else if($the_product == 22)
{
	$the_product_name = "Sothink SWF Quicker";
	$the_version_latest = 3.0;
	$the_build_latest = 71120;
	$the_product_url = "http://www.sothink.com/product/swfquicker/";
	$the_download_url = "http://www2.sothink.com/download/swfquicker.zip";
}

else if($the_product == 23)
{
	$the_product_name = "硕思闪客之锤";
	$the_version_latest = 1.5;
	$the_build_latest = 41230;
	$the_product_url = "http://www.sothink.com.cn/product/swfquicker/";
	$the_download_url = "http://www.sothink.com.cn/download/swfquicker.zip";
}
else if($the_product == 28)
{
	$the_product_name = "Sothink SWF Easy";
	$the_version_latest = 5.1;
	$the_build_latest = 80201;
	$the_product_url = "http://www.sothink.com/product/swfeasy/index.htm";
	$the_download_url = "http://www2.sothink.com/download/swfeasy.zip";
}
else if($the_product == 11)
{
	$the_product_name = "硕思闪客巫师2005";
	$the_version_latest = 2.4;
	$the_build_latest = 50718;
	$the_product_url = "http://www.sothink.com.cn/product/swfeasy/index.htm";
	$the_download_url = "http://www.sothink.com.cn/download/glandachs.zip";
}
else if($the_product == 26)
{
	$the_product_name = "Sothink Video Encoder for Adobe Flash";
	$the_version_latest = 2.1;
	$the_build_latest = 70313;
	$the_product_url = "http://www.sothink.com/product/flashvideoencoder/";
	$the_download_url = "http://www2.sothink.com/download/fvencoder.zip";
}
else if($the_product == 27)
{
	$the_product_name = "Sothink DVD EZWorkshop";
	$the_version_latest = 1.3;
	$the_build_latest = 60530;
	$the_product_url = "http://www.sothinkmedia.com/index.htm";
	$the_download_url = "http://www2.sothink.com/download/dvdezws.zip";
}
else if($the_product == 29)
{
	$the_product_name = "Sothink Tree Menu";
	$the_version_latest = 2.00;
	$the_build_latest = 61102;
	$the_product_url = "http://www.sothink.com/product/treemenu/";
	$the_download_url = "http://www2.sothink.com/download/stmenu.zip";
}
else if ($the_product == 30)
{
	$the_product_name = "Sothink SWF Decompiler 3.6 (Japanese Version)";
	$the_version_latest = 3.6;
	$the_build_latest = 70208;
	$the_product_url = "http://www.webnomoto.com/sothink/product/flashdecompiler/index.htm";
	$the_download_url = "http://www.webnomoto.com/sothink/product/download.htm";
}
else if ($the_product == 31)
{
	$the_product_name = "Sothink SWF Decompiler 3.6 (Traditional Chinese)";
	$the_version_latest = 3.6;
	$the_build_latest = 70208;
	$the_product_url = "http://www.sothink.com/product/flashdecompiler/";
	$the_download_url = "http://www2.sothink.com/download/swfdec.zip";
}
else if ($the_product == 32)
{
	$the_product_name = "Sothink DVD Ripper";
	$the_version_latest = 1.3;
	$the_build_latest = 70119;
	$the_product_url = "http://www.sothinkmedia.com/dvd-ripper/";
	$the_download_url = "http://www2.sothink.com/download/dvdripper.zip";
}
else if ($the_product == 33)
{
	$the_product_name = "Sothink iPod Video Converter";
	$the_version_latest = 3.1;
	$the_build_latest = 70315;
	$the_product_url = "http://www.sothinkmedia.com/ipod-video-converter/";
	$the_download_url = "http://www2.sothink.com/download/ipodvideoconverter.zip";
}
else if ($the_product == 34)
{
	$the_product_name = "Sothink SWF Decompiler 3.3 (Germany Version)";
	$the_version_latest = 3.3;
	$the_build_latest = 60720;
	$the_product_url = "http://www.sothink.com/product/flashdecompiler/";
	$the_download_url = "http://www2.sothink.com/download/swfdecger.zip";
}

else if ($the_product == 37)
{
	$the_product_name = "Sothink DVD to iPod Converter";
	$the_version_latest = 2.5;
	$the_build_latest = 70208;
	$the_product_url = "http://www.sothinkmedia.com/dvd-to-ipod/";
	$the_download_url = "http://www2.sothink.com/download/dvdtoipodconverter.zip";
}

else if ($the_product == 38)
{
	$the_product_name = "Sothink PSP Video Converter";
	$the_version_latest = 1.0;
	$the_build_latest = 70308;
	$the_product_url = "http://www.sothinkmedia.com/psp-video-converter/";
	$the_download_url = "http://www2.sothink.com/download/pspvc.zip";
}

else if ($the_product == 39)
{
	$the_product_name = "Sothink DVD to PSP Converter";
	$the_version_latest = 1.0;
	$the_build_latest = 70402;
	$the_product_url = "http://www.sothinkmedia.com/dvd-to-psp/";
	$the_download_url = "http://www2.sothink.com/download/dvdtopsp.zip";
}

else if ($the_product == 40)
{
	$the_product_name = "Sothink 3GP Video Converter";
	$the_version_latest = 1.0;
	$the_build_latest = 70215;
	$the_product_url = "http://www.sothinkmedia.com/3gp-video-converter/";
	$the_download_url = "http://www2.sothink.com/download/3gpvideoconverter.zip";
}

else if ($the_product == 41)
{
	$the_product_name = "Sothink DVD to 3GP Converter";
	$the_version_latest = 1.0;
	$the_build_latest = 70402;
	$the_product_url = "http://www.sothinkmedia.com/dvd-to-3gp/";
	$the_download_url = "http://www2.sothink.com/download/dvdto3gp.zip";
}

else if($the_product == 42)
{
	$the_product_name = "Sothink Movie DVD Maker";
	$the_version_latest = 1.0;
	$the_build_latest = 70424;
	$the_product_url = "http://www.sothinkmedia.com/movie-dvd-maker/index.htm";
	$the_download_url = "http://www2.sothink.com/download/dvdmaker.zip";
}
else if($the_product == 55)
{
	$the_product_name = "Sothink Photo Album Maker";
	$the_version_latest = 1.0;
	$the_build_latest = 80504;
	$the_product_url = "http://www.sothink.com/product/photo-album-maker/index.htm";
	$the_download_url = "http://www2.sothink.com/download/photo-album-maker.zip";
}

else
{
	print("Error: can not find this product in our database.");
	exit();
}

if ($the_version > $the_version_latest || (($the_version == $the_version_latest) && ($the_build > $the_build_latest)))
{
	print("Error: can not find this product version in our database.");
	exit();
}
// End all products
?>

<p>&nbsp;</p>
<table width="500"  border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#CCCCCC">
  <tr>
    <td><table width="100%"  border="0" align="center" cellpadding="8" cellspacing="1">
      <tr valign="middle" bgcolor="#FFFFFF">
        <td colspan="2" class="text_18px"><? print($the_product_name) ?></td>
      </tr>
      <tr bgcolor="#FFFFFF" class="text_12px">
        <td width="50%"><span class="style3"><? print $current_version_label ?></span></td>
        <td width="50%"><span class="style3"><? printf("%1.2f", $the_version) ?> (Build: <? print $the_build ?>)</span></td>
      </tr>
      <tr bgcolor="#FFFFFF" class="text_12px">
        <td width="50%"><span class="style3"><? print $latest_version_label ?></span></td>
        <td width="50%"><span class="style3"><? printf("%1.2f", $the_version_latest) ?> (Build: <? print $the_build_latest ?>)</span></td>
      </tr>
      <tr bgcolor="#FFFFFF" class="text_12px">
        <td colspan="2">
          <span class="style3">
      <? if ($the_version == $the_version_latest && $the_build == $the_build_latest) { ?>
      <? print $no_new_version_label ?>
      <? } else { ?>
      </span><span class="red"><? print $new_vesion_available ?></span><span class="style3"> [ <a href="<?=$the_product_url?>"><? print $whats_new_label ?></a> | <a href="<?=$the_download_url?>"><? print $download_label ?></a> ]
      <? } ?>
          </span></td>
      </tr>
    </table></td>
  </tr>
</table>

   <script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-199040-2");
pageTracker._initData();
pageTracker._trackPageview();
</script>
</body>
</html>



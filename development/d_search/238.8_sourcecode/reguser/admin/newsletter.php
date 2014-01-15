<?
  	include ("auth.php"); 
	include ("authconfig.php");  	

	$Auth = new auth();
	$nname = $_COOKIE['cookie']['username'];
	$passwd = $_COOKIE['cookie']['password'];
	// check cookie 
	if (isset ($_COOKIE['cookie']['username'])) 
	{
		// print "Cookie Set";
		// get password and username and check in database		
		$detail = $Auth->authenticate($_COOKIE['cookie']['username'], $_COOKIE['cookie']['password']);		
	}
	else
	{
		$detail = 0;
	}

	if ($detail != 2)
	{
		header("Location: $failure");
	}

	// Get reseller information
	$query = "SELECT * FROM Reseller WHERE uname='$nname' AND passwd='$passwd'";
	
	$connection = mysql_connect($dbhost, $dbusername, $dbpass);
	$result = mysql_db_query($dbname, $query);
	$numrows = mysql_num_rows($result);
	$row = mysql_fetch_array($result);
	
	// print $query;
	
?>
<html>
<head>
<title>Reseller Control Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
td {  font-family: "Verdana", "Arial", "Helvetica", "sans-serif"; font-size: 12px}
body {
	background-color: #BDCBE7;
}
-->
</style>
</head>

<body text="#000000">
<table width="800" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td> 
      <div align="center"> 
        <table width="90%" border="0" cellspacing="0" cellpadding="4">
          <tr bgcolor="#EEEEEE"> 
            <td colspan="5" bgcolor="#DDDDDD"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Hello, 
              <?=$row["strName"]?>
              </font></td>
          </tr>
          <tr bgcolor="#FFFFEE"> 
            <td width="20%" bgcolor="#DDDDDD"><a href="register.php">Register Software</a></td>
            <td width="20%" bgcolor="#DDDDDD"><a href="query.php">Query Registration</a></td>
            <td width="20%" bgcolor="#DDDDDD"><a href="newsletter.php">Send Newsletter</a></td>
            <td width="20%" bgcolor="#DDDDDD"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><a href="profile.php">Profile</a></font></td>
            <td bgcolor="#DDDDDD"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><a href="logout.php">Logout</a></font></td>
          </tr>
        </table>
        <br>
		<form method="post" action="preview-newsletter.php">
          <table width="90%" border="1" cellspacing="0" cellpadding="5" bordercolor="#999999" bgcolor="#DDDDDD">
            <tr> 
            <td>
              <p><b>Send newsletter to your customers</b></p>
              <span><b>Select customers</b></span> 
              <hr noshade size="1">
              
                <p>The newsletter should be sent to customers, who have purchased 
                  the following products:</p>
              <p>
              <?
              	// Get Product List
              	$qPro = "select * from Product";
				$presult = mysql_db_query($dbname, $qPro);
				// $numrows = mysql_num_rows($result);
				// print $numrows;
				$prow = mysql_fetch_array($presult);
              ?>
                <select name="Product">
                  <?
                while ($prow) {
                  	print "<option value=\"".$prow["intID"]."\" >".$prow["strProductName"]."</option>\n";
                	$prow = mysql_fetch_array($presult);
                }
                ?>
                </select>
				</p>
              <span><b>Select Time Range</b></span> 
              <hr size="1" noshade>
              <p>The newsletter should be sent to customers who ordered during the 
              time period from:<br>
              <SELECT NAME="DAY_FROM" >
              <OPTION VALUE="1" selected>1</OPTION>
				<OPTION VALUE="2">2</OPTION>
				<OPTION VALUE="3">3</OPTION>
				<OPTION VALUE="4">4</OPTION>
				<OPTION VALUE="5">5</OPTION>
				<OPTION VALUE="6">6</OPTION>
				<OPTION VALUE="7">7</OPTION>
				<OPTION VALUE="8">8</OPTION>
				<OPTION VALUE="9">9</OPTION>
				<OPTION VALUE="10">10</OPTION>
				<OPTION VALUE="11">11</OPTION>
				<OPTION VALUE="12">12</OPTION>
				<OPTION VALUE="13">13</OPTION>
				<OPTION VALUE="14">14</OPTION>
				<OPTION VALUE="15">15</OPTION>
				<OPTION VALUE="16">16</OPTION>
				<OPTION VALUE="17">17</OPTION>
				<OPTION VALUE="18">18</OPTION>
				<OPTION VALUE="19">19</OPTION>
				<OPTION VALUE="20">20</OPTION>
				<OPTION VALUE="21">21</OPTION>
				<OPTION VALUE="22">22</OPTION>
				<OPTION VALUE="23">23</OPTION>
				<OPTION VALUE="24">24</OPTION>
				<OPTION VALUE="25">25</OPTION>
				<OPTION VALUE="26">26</OPTION>
				<OPTION VALUE="27">27</OPTION>
				<OPTION VALUE="28">28</OPTION>
				<OPTION VALUE="29">29</OPTION>
				<OPTION VALUE="30">30</OPTION>
				<OPTION VALUE="31">31</OPTION>
				</SELECT>&nbsp;<SELECT NAME="MONTH_FROM" ><OPTION VALUE="1">January</OPTION>
				<OPTION VALUE="2">February</OPTION>
				<OPTION VALUE="3">March</OPTION>
				<OPTION VALUE="4">April</OPTION>
				<OPTION VALUE="5">May</OPTION>
				<OPTION VALUE="6">June</OPTION>
				<OPTION VALUE="7">July</OPTION>
				<OPTION VALUE="8">August</OPTION>
				<OPTION VALUE="9">September</OPTION>
				<OPTION VALUE="10">October</OPTION>
				<OPTION VALUE="11">November</OPTION>
				<OPTION VALUE="12">December</OPTION>
				</SELECT>&nbsp;
				<SELECT NAME="YEAR_FROM" >
				<OPTION VALUE="2001">2001</OPTION>
				<OPTION VALUE="2002">2002</OPTION>
				<OPTION VALUE="2003">2003</OPTION>
				<OPTION VALUE="2004">2004</OPTION>
				<OPTION VALUE="2005">2005</OPTION>
				<OPTION VALUE="2006">2006</OPTION>
				<OPTION VALUE="2007">2007</OPTION>
				<OPTION VALUE="2008">2008</OPTION>
				</SELECT>
				  to:
				  <SELECT NAME="DAY_TO" >
				<OPTION VALUE="1" <? if (date("d")==1) print selected;?>>1</OPTION>
				<OPTION VALUE="2" <? if (date("d")==2) print selected;?>>2</OPTION>
				<OPTION VALUE="3" <? if (date("d")==3) print selected;?>>3</OPTION>
				<OPTION VALUE="4" <? if (date("d")==4) print selected;?>>4</OPTION>
				<OPTION VALUE="5" <? if (date("d")==5) print selected;?>>5</OPTION>
				<OPTION VALUE="6" <? if (date("d")==6) print selected;?>>6</OPTION>
				<OPTION VALUE="7" <? if (date("d")==7) print selected;?>>7</OPTION>
				<OPTION VALUE="8" <? if (date("d")==8) print selected;?>>8</OPTION>
				<OPTION VALUE="9" <? if (date("d")==9) print selected;?>>9</OPTION>
				<OPTION VALUE="10" <? if (date("d")==10) print selected;?>>10</OPTION>
				<OPTION VALUE="11" <? if (date("d")==11) print selected;?>>11</OPTION>
				<OPTION VALUE="12" <? if (date("d")==12) print selected;?>>12</OPTION>
				<OPTION VALUE="13" <? if (date("d")==13) print selected;?>>13</OPTION>
				<OPTION VALUE="14" <? if (date("d")==14) print selected;?>>14</OPTION>
				<OPTION VALUE="15" <? if (date("d")==15) print selected;?>>15</OPTION>
				<OPTION VALUE="16" <? if (date("d")==16) print selected;?>>16</OPTION>
				<OPTION VALUE="17" <? if (date("d")==17) print selected;?>>17</OPTION>
				<OPTION VALUE="18" <? if (date("d")==18) print selected;?>>18</OPTION>
				<OPTION VALUE="19" <? if (date("d")==19) print selected;?>>19</OPTION>
				<OPTION VALUE="20" <? if (date("d")==20) print selected;?>>20</OPTION>
				<OPTION VALUE="21" <? if (date("d")==21) print selected;?>>21</OPTION>
				<OPTION VALUE="22" <? if (date("d")==22) print selected;?>>22</OPTION>
				<OPTION VALUE="23" <? if (date("d")==23) print selected;?>>23</OPTION>
				<OPTION VALUE="24" <? if (date("d")==24) print selected;?>>24</OPTION>
				<OPTION VALUE="25" <? if (date("d")==25) print selected;?>>25</OPTION>
				<OPTION VALUE="26" <? if (date("d")==26) print selected;?>>26</OPTION>
				<OPTION VALUE="27" <? if (date("d")==27) print selected;?>>27</OPTION>
				<OPTION VALUE="28" <? if (date("d")==28) print selected;?>>28</OPTION>
				<OPTION VALUE="29" <? if (date("d")==29) print selected;?>>29</OPTION>
				<OPTION VALUE="30" <? if (date("d")==30) print selected;?>>30</OPTION>
				<OPTION VALUE="31" <? if (date("d")==31) print selected;?>>31</OPTION>
				</SELECT>&nbsp;<SELECT NAME="MONTH_TO" >
                  <option value="1" <? if (date("m")==1) print selected;?>>January</option>
                  <option value="2" <? if (date("m")==2) print selected;?>>February</option>
                  <option value="3" <? if (date("m")==3) print selected;?>>March</option>
                  <option value="4" <? if (date("m")==4) print selected;?>>April</option>
                  <option value="5" <? if (date("m")==5) print selected;?>>May</option>
                  <option value="6" <? if (date("m")==6) print selected;?>>June</option>
                  <option value="7" <? if (date("m")==7) print selected;?>>July</option>
                  <option value="8" <? if (date("m")==8) print selected;?>>August</option>
                  <option value="9" <? if (date("m")==9) print selected;?>>September</option>
                  <option value="10" <? if (date("m")==10) print selected;?>>October</option>
                  <option value="11" <? if (date("m")==11) print selected;?>>November</option>
                  <option value="12" <? if (date("m")==12) print selected;?>>December</option>

				</SELECT>&nbsp;<SELECT NAME="YEAR_TO" >
                  <option value="2002" <? if (date("Y")==2002) print selected;?>>2002</option>
                  <option value="2003" <? if (date("Y")==2003) print selected;?>>2003</option>
                  <option value="2004" <? if (date("Y")==2004) print selected;?>>2004</option>
                  <option value="2005" <? if (date("Y")==2005) print selected;?>>2005</option>
                  <option value="2006" <? if (date("Y")==2006) print selected;?>>2006</option>
                  <option value="2007" <? if (date("Y")==2007) print selected;?>>2007</option>
                  <option value="2008" <? if (date("Y")==2008) print selected;?>>2008</option>
			</SELECT>
                
				</p>
              <span><b>Define newsletter details</b></span> 
              <hr noshade size="1">
                <p>Please select language:<br>
                  <select name="langselect">
                    <option value="iso-8859-1" selected>English</option>
                    <option value="gb2312">Simplified Chinese</option>
                    <option value="big5">Traditional Chinese</option>
                  </select>
                </p>
                <p>  
                  Please enter the subject line of the newsletter e-mail here:<br>
                  <input type="text" name="emailsubject" size="60" maxlength="128">
                </p>
              <p> <br>
                Newsletter text:</p>
              You can use <b>placeholders</b> in your mail sent to users. The 
                placeholder will be automatically replaced by user's information 
                while sending. 
                <p>Placeholders can be used<br>
                  &lt;%USERNAME%&gt; - User's name<br>
                  &lt;%USEREMAIL%&gt; - User's email<br>
                  &lt;%REGCODE%&gt; - Register Code<br>
                  &lt;%DOWNLINK%&gt; - Download URL for the product that the user 
                  ordered. <br>
                  &lt;%PRODUCTNAME%&gt; - The product name the user ordered.<br>
                  &lt;%EXPIREDATE%&gt; - The download entry expire date<br>
                  <textarea name="emailtext" cols="60" rows="20"></textarea>
                </p>
              <p> 
                What e-mail address should be used as the sender of the mail?
				<br> 
                  <input type="text" name="emailfrom" size="30" maxlength="60">
			    </p>
                <p>
				If you would prefer that customers reply to a different address 
                than specified above, please enter the Reply-To address here:
				<br>
                  <input type="text" name="emailreplyto" size="30" maxlength="60">
                </p>
                <hr noshade size="1">
                <p align="center"> 
                  <input type="submit" name="Submit" value="Preview Your Newsletter">
                </p>
            </td>
          </tr>
        </table>
		</form>
        <p><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Contact 
          <a href="mailto:info@sothink.com">info@sothink.com</a> if you have any 
          questions.</font></p>
      </div>
    </td>
  </tr>
</table>
<p>&nbsp; </p>
</body>
</html>

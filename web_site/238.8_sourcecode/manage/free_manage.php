<?php
define('IN_PAGE',true);
include('init.php');
$nStart = isset($_GET['start'])?escapeshellcmd($_GET['start']):"0";
$pageLimit = 30;

$count = $user->Getcountfree();
$Strfree = $user->GetFreeInfo($nStart,$pageLimit,$ID);
//print_r($Strfree);
//exit;
$pages = array();
?>
<table width="775" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#99CCFF">
  <tr>
    <td colspan="8" align="right" bgcolor="#FFFFFF">
      <input type="button" name="Submit22" value="Add FreeUser" onclick="javascript:window.location.href='add_free.php'">
&nbsp;&nbsp;&nbsp;
      <input type="button" name="Submit2" value="Del overdue" onclick="javascript:window.location.href='overdue.php'">
&nbsp;&nbsp;&nbsp;
      <input type="button" name="Submit" value="data export" onclick="javascript:window.location.href='export.php'">    </td>
  </tr>
  <tr>
    <td width="5%" bgcolor="#FFFFFF"><strong>InfoId</strong></td>
    <td width="7%" bgcolor="#FFFFFF"><strong>Name</strong></td>
    <td width="12%" bgcolor="#FFFFFF"><strong>Email</strong></td>
  </tr>
	<?php
for($j=0;$j<=ceil($count/$pageLimit);$j++){
 $free = $Strfree[$j];
?>
	<tr>
    <td height="20" bgcolor="#FFFFFF"><?php echo $free[id];?></td>
    <td height="20" bgcolor="#FFFFFF"><?php echo $free[name];?></td>
    <td height="20" bgcolor="#FFFFFF"><?php echo $free[email];?></td>
  </tr>
	<?php }?>
</table>
<table width="775" border="0" align="center">
  <tr>
    <td>Page <?php for($i=1; $i<=ceil($count/$pageLimit); $i++)
{
	$pages[$i]['start'] = ($i-1)*$pageLimit;
	$start = $pages[$i]['start'];
	$pageNo = $i;
?><a href="free_manage.php?start=<? echo $start?>"><? echo $pageNo?></a>&nbsp;<? }?>
 </td>
  </tr>
</table>
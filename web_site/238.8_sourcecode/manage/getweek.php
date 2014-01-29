<?php
    $begin_day = $_POST['BEGIN_DAY'];
	$begin_month = $_POST['BEGIN_MONTH'];
	$begin_year = $_POST['BEGIN_YEAR'];
	$end_day = $_POST['END_DAY'];
	$product_name = $_POST['PRODUCT'];
	if (!$end_day )
	{
	   $end_day = date("d",time());
	   if (strlen($end_day)==1)
	   {
	       $end_day = "0".$end_day;
	   }
	}
	$end_month = $_POST['END_MONTH'];
	if(!$end_month)
	{
	    $end_month = date("m",time());
	}
	$end_year = $_POST['END_YEAR'];
	if (!$end_year)
	{
	    $end_year = date("Y",time());
	}
	
function mktimes($aTime)
{
   $aTime = $aTime." 00:00:00";
   list($ayear , $amonth , $aday , $ahour , $aminute , $asecond) = split('[- :]',$aTime);
   $a   =   mktime ( $ahour , $aminute , $asecond , $amonth , $aday , $ayear );
   return $a;
}

function timeDiff($aTime , $bTime)
{
// substr $aTime
$aTime = $aTime." 00:00:00";
$bTime = $bTime." 00:00:00";
list($ayear , $amonth , $aday , $ahour , $aminute , $asecond) = split('[- :]',$aTime);
 // substr $bTime
list($byear , $bmonth , $bday , $bhour , $bminute , $bsecond) = split('[- :]',$bTime);
 // 
    $a   =   mktime ( $ahour , $aminute , $asecond , $amonth , $aday , $ayear );
    $b   =   mktime ( $bhour , $bminute , $bsecond , $bmonth , $bday , $byear );
    $timeDiff ['second']  =   $a - $b ;
 // 
   $timeDiff ['mintue']  =   ceil ( $timeDiff ['second'] / 60 );
   $timeDiff ['hour']  =   ceil ( $timeDiff ['mintue'] / 60 );
   $timeDiff ['day']  =   ceil ( $timeDiff ['hour'] / 24 );
   $timeDiff ['week']  =   ceil ( $timeDiff ['day'] / 7 );
   return   $timeDiff ;
}

function getweek ($date)
{
   $year = substr($date,0,4)."-01-01 :00:00:00";
   $first_time =  mktimes($year);   
   $first_w = date('w',$first_time);
   $begin_week = (7-$first_w)*24*60*60+$first_time;
   $next_week = date('z',mktimes($date))*24*60*60+$first_time;
   $diff_week = ceil(($next_week-$begin_week)/(7*24*60*60)+1);
   return $diff_week;   
}
?>

<!-- main content -->
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td valign="top" width="8"><img src="images/p_t.gif" border="0" alt="" width="8" height="1"></td>
<td valign="top" width="100%">
<h1>Statistics</h1>
<!--LINE-->
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td class="outerborder"><img src="images/p_t.gif" border="0" alt="" width="1" height="1"></td>
</tr>
</table>
<!--LINE-->
<p>Here, you can display your products' statistical figures as a function of year, quarter, and product.</p>
<table border="0" cellpadding="2" cellspacing="0" width="100%" class="active">
<form action="getweek.php" method="post" name="statistics">
<input type="hidden" name="TYPE" value="sellstats">
<input type="hidden" name="action" value="sellstats">
<tr>
<td colspan="2" width="100%">Please select range </td>
</tr>
<tr>
<td valign="top" nowrap>From</td>
<td valign="top" width="100%">
<SELECT NAME="BEGIN_DAY"  id="e5_frm_begin_day"><OPTION VALUE=""></OPTION>
<OPTION VALUE="1" <?php if($begin_day==1 || $begin_day=='') echo "selected";?>>1.</OPTION>
<OPTION VALUE="2" <?php if($begin_day==2) echo "selected";?>>2.</OPTION>
<OPTION VALUE="3" <?php if($begin_day==3) echo "selected";?>>3.</OPTION>
<OPTION VALUE="4" <?php if($begin_day==4) echo "selected";?>>4.</OPTION>
<OPTION VALUE="5" <?php if($begin_day==5) echo "selected";?>>5.</OPTION>
<OPTION VALUE="6" <?php if($begin_day==6) echo "selected";?>>6.</OPTION>
<OPTION VALUE="7" <?php if($begin_day==7) echo "selected";?>>7.</OPTION>
<OPTION VALUE="8" <?php if($begin_day==8) echo "selected";?>>8.</OPTION>
<OPTION VALUE="9" <?php if($begin_day==9) echo "selected";?>>9.</OPTION>
<OPTION VALUE="10" <?php if($begin_day==10) echo "selected";?>>10.</OPTION>
<OPTION VALUE="11" <?php if($begin_day==11) echo "selected";?>>11.</OPTION>
<OPTION VALUE="12" <?php if($begin_day==12) echo "selected";?>>12.</OPTION>
<OPTION VALUE="13" <?php if($begin_day==13) echo "selected";?>>13.</OPTION>
<OPTION VALUE="14" <?php if($begin_day==14) echo "selected";?>>14.</OPTION>
<OPTION VALUE="15" <?php if($begin_day==15) echo "selected";?>>15.</OPTION>
<OPTION VALUE="16" <?php if($begin_day==16) echo "selected";?>>16.</OPTION>
<OPTION VALUE="17" <?php if($begin_day==17) echo "selected";?>>17.</OPTION>
<OPTION VALUE="18" <?php if($begin_day==18) echo "selected";?>>18.</OPTION>
<OPTION VALUE="19" <?php if($begin_day==19) echo "selected";?>>19.</OPTION>
<OPTION VALUE="20" <?php if($begin_day==20) echo "selected";?>>20.</OPTION>
<OPTION VALUE="21" <?php if($begin_day==21) echo "selected";?>>21.</OPTION>
<OPTION VALUE="22" <?php if($begin_day==22) echo "selected";?>>22.</OPTION>
<OPTION VALUE="23" <?php if($begin_day==23) echo "selected";?>>23.</OPTION>
<OPTION VALUE="24" <?php if($begin_day==24) echo "selected";?>>24.</OPTION>
<OPTION VALUE="25" <?php if($begin_day==25) echo "selected";?>>25.</OPTION>
<OPTION VALUE="26" <?php if($begin_day==26) echo "selected";?>>26.</OPTION>
<OPTION VALUE="27" <?php if($begin_day==27) echo "selected";?>>27.</OPTION>
<OPTION VALUE="28" <?php if($begin_day==28) echo "selected";?>>28.</OPTION>
<OPTION VALUE="29" <?php if($begin_day==29) echo "selected";?>>29.</OPTION>
<OPTION VALUE="30" <?php if($begin_day==30) echo "selected";?>>30.</OPTION>
<OPTION VALUE="31" <?php if($begin_day==31) echo "selected";?>>31.</OPTION>
</SELECT>&nbsp;<SELECT NAME="BEGIN_MONTH"  id="e5_frm_begin_month">
<OPTION VALUE=""></OPTION>
<OPTION VALUE="1" <?php if($begin_month==1 || $begin_month=='') echo "selected";?>>January</OPTION>
<OPTION VALUE="2" <?php if($begin_month==2) echo "selected";?>>February</OPTION>
<OPTION VALUE="3" <?php if($begin_month==3) echo "selected";?>>March</OPTION>
<OPTION VALUE="4" <?php if($begin_month==4) echo "selected";?>>April</OPTION>
<OPTION VALUE="5" <?php if($begin_month==5) echo "selected";?>>May</OPTION>
<OPTION VALUE="6" <?php if($begin_month==6) echo "selected";?>>June</OPTION>
<OPTION VALUE="7" <?php if($begin_month==7) echo "selected";?>>July</OPTION>
<OPTION VALUE="8" <?php if($begin_month==8) echo "selected";?>>August</OPTION>
<OPTION VALUE="9" <?php if($begin_month==9) echo "selected";?>>September</OPTION>
<OPTION VALUE="10" <?php if($begin_month==10) echo "selected";?>>October</OPTION>
<OPTION VALUE="11" <?php if($begin_month==11) echo "selected";?>>November</OPTION>
<OPTION VALUE="12" <?php if($begin_month==12 ) echo "selected";?>>December</OPTION>
</SELECT>&nbsp;<SELECT NAME="BEGIN_YEAR"  id="e5_frm_begin_year">
<OPTION VALUE=""></OPTION>
<OPTION VALUE="1995" <?php if($begin_year=='1995') echo "selected";?>>1995</OPTION>
<OPTION VALUE="1996" <?php if($begin_year=='1996') echo "selected";?>>1996</OPTION>
<OPTION VALUE="1997" <?php if($begin_year=='1997') echo "selected";?>>1997</OPTION>
<OPTION VALUE="1998" <?php if($begin_year=='1998') echo "selected";?>>1998</OPTION>
<OPTION VALUE="1999" <?php if($begin_year=='1999') echo "selected";?>>1999</OPTION>
<OPTION VALUE="2000" <?php if($begin_year=='2000') echo "selected";?>>2000</OPTION>
<OPTION VALUE="2001" <?php if($begin_year=='2001') echo "selected";?>>2001</OPTION>
<OPTION VALUE="2002" <?php if($begin_year=='2002') echo "selected";?>>2002</OPTION>
<OPTION VALUE="2003" <?php if($begin_year=='2003') echo "selected";?>>2003</OPTION>
<OPTION VALUE="2004" <?php if($begin_year=='2004') echo "selected";?>>2004</OPTION>
<OPTION VALUE="2005" <?php if($begin_year=='2005') echo "selected";?>>2005</OPTION>
<OPTION VALUE="2006" <?php if($begin_year=='2006') echo "selected";?>>2006</OPTION>
<OPTION VALUE="2007" <?php if($begin_year=='2007' || $begin_year=='') echo "selected";?>>2007</OPTION>
<OPTION VALUE="2008" <?php if($begin_year=='2008') echo "selected";?>>2008</OPTION>
<OPTION VALUE="2009" <?php if($begin_year=='2009') echo "selected";?>>2009</OPTION>
</SELECT>
</td>
</tr>
<tr>
<td valign="top" nowrap>To</td>
<td valign="top" width="100%"><SELECT NAME="END_DAY"  id="e5_frm_end_day"><OPTION VALUE=""></OPTION>
<OPTION VALUE="01" <?php if($end_day == '01') echo "selected";?>>1.</OPTION>
<OPTION VALUE="02" <?php if($end_day == '02') echo "selected";?>>2.</OPTION>
<OPTION VALUE="03" <?php if($end_day == '03') echo "selected";?>>3.</OPTION>
<OPTION VALUE="04" <?php if($end_day == '04') echo "selected";?>>4.</OPTION>
<OPTION VALUE="05" <?php if($end_day == '05') echo "selected";?>>5.</OPTION>
<OPTION VALUE="06" <?php if($end_day == '06') echo "selected";?>>6.</OPTION>
<OPTION VALUE="07" <?php if($end_day == '07') echo "selected";?>>7.</OPTION>
<OPTION VALUE="08" <?php if($end_day == '08') echo "selected";?>>8.</OPTION>
<OPTION VALUE="09" <?php if($end_day == '09') echo "selected";?>>9.</OPTION>
<OPTION VALUE="10" <?php if($end_day == '10') echo "selected";?>>10.</OPTION>
<OPTION VALUE="11" <?php if($end_day == '11') echo "selected";?>>11.</OPTION>
<OPTION VALUE="12" <?php if($end_day == '12') echo "selected";?>>12.</OPTION>
<OPTION VALUE="13" <?php if($end_day == '13') echo "selected";?>>13.</OPTION>
<OPTION VALUE="14" <?php if($end_day == '14') echo "selected";?>>14.</OPTION>
<OPTION VALUE="15" <?php if($end_day == '15') echo "selected";?>>15.</OPTION>
<OPTION VALUE="16" <?php if($end_day == '16') echo "selected";?>>16.</OPTION>
<OPTION VALUE="17" <?php if($end_day == '17') echo "selected";?>>17.</OPTION>
<OPTION VALUE="18" <?php if($end_day == '18') echo "selected";?>>18.</OPTION>
<OPTION VALUE="19" <?php if($end_day == '19') echo "selected";?>>19.</OPTION>
<OPTION VALUE="20" <?php if($end_day == '20') echo "selected";?>>20.</OPTION>
<OPTION VALUE="21" <?php if($end_day == '21') echo "selected";?>>21.</OPTION>
<OPTION VALUE="22" <?php if($end_day == '22') echo "selected";?>>22.</OPTION>
<OPTION VALUE="23" <?php if($end_day == '23') echo "selected";?>>23.</OPTION>
<OPTION VALUE="24" <?php if($end_day == '24') echo "selected";?>>24.</OPTION>
<OPTION VALUE="25" <?php if($end_day == '25') echo "selected";?>>25.</OPTION>
<OPTION VALUE="26" <?php if($end_day == '26') echo "selected";?>>26.</OPTION>
<OPTION VALUE="27" <?php if($end_day == '27') echo "selected";?>>27.</OPTION>
<OPTION VALUE="28" <?php if($end_day == '28') echo "selected";?>>28.</OPTION>
<OPTION VALUE="29" <?php if($end_day == '29') echo "selected";?>>29.</OPTION>
<OPTION VALUE="30" <?php if($end_day == '30') echo "selected";?>>30.</OPTION>
<OPTION VALUE="31" <?php if($end_day == '31') echo "selected";?>>31.</OPTION>
</SELECT>&nbsp;<SELECT NAME="END_MONTH"  id="e5_frm_end_month"><OPTION VALUE=""></OPTION>
<OPTION VALUE="1" <?php if($end_month == 1) echo "selected";?>>January</OPTION>
<OPTION VALUE="2" <?php if($end_month == 2) echo "selected";?>>February</OPTION>
<OPTION VALUE="3" <?php if($end_month == 3) echo "selected";?>>March</OPTION>
<OPTION VALUE="4" <?php if($end_month == 4) echo "selected";?>>April</OPTION>
<OPTION VALUE="5" <?php if($end_month == 5) echo "selected";?>>May</OPTION>
<OPTION VALUE="6" <?php if($end_month == 6) echo "selected";?>>June</OPTION>
<OPTION VALUE="7" <?php if($end_month == 7) echo "selected";?>>July</OPTION>
<OPTION VALUE="8" <?php if($end_month == 8) echo "selected";?>>August</OPTION>
<OPTION VALUE="9" <?php if($end_month == 9) echo "selected";?>>September</OPTION>
<OPTION VALUE="10" <?php if($end_month == 10) echo "selected";?>>October</OPTION>
<OPTION VALUE="11" <?php if($end_month == 11) echo "selected";?>>November</OPTION>
<OPTION VALUE="12" <?php if($end_month == 12) echo "selected";?>>December</OPTION>
</SELECT>&nbsp;<SELECT NAME="END_YEAR"  id="e5_frm_end_year"><OPTION VALUE=""></OPTION>
<OPTION VALUE="1995" <?php if($end_year == '1995') echo "selected";?>>1995</OPTION>
<OPTION VALUE="1996" <?php if($end_year == '1996') echo "selected";?>>1996</OPTION>
<OPTION VALUE="1997" <?php if($end_year == '1997') echo "selected";?>>1997</OPTION>
<OPTION VALUE="1998" <?php if($end_year == '1998') echo "selected";?>>1998</OPTION>
<OPTION VALUE="1999" <?php if($end_year == '1999') echo "selected";?>>1999</OPTION>
<OPTION VALUE="2000" <?php if($end_year == '2000') echo "selected";?>>2000</OPTION>

<OPTION VALUE="2001" <?php if($end_year == '2001') echo "selected";?>>2001</OPTION>
<OPTION VALUE="2002" <?php if($end_year == '2002') echo "selected";?>>2002</OPTION>
<OPTION VALUE="2003" <?php if($end_year == '2003') echo "selected";?>>2003</OPTION>
<OPTION VALUE="2004" <?php if($end_year == '2004') echo "selected";?>>2004</OPTION>
<OPTION VALUE="2005" <?php if($end_year == '2005') echo "selected";?>>2005</OPTION>
<OPTION VALUE="2006" <?php if($end_year == '2006') echo "selected";?>>2006</OPTION>
<OPTION VALUE="2007" <?php if($end_year == '2007') echo "selected";?>>2007</OPTION>
<OPTION VALUE="2008" <?php if($end_year == '2008') echo "selected";?>>2008</OPTION>
<OPTION VALUE="2009" <?php if($end_year == '2009') echo "selected";?>>2009</OPTION>
</SELECT></td>
</tr>
<tr>
<td colspan="2"></td>
</tr>
<tr>
<td colspan="2" valign="top" width="100%">Please choose a group for your statistics. The data is displayed in groups per day, week or month, or not grouped at all. Please note that the maximum number of columns displayed side-by-side is 12.</td>
</tr>
<tr>
<td valign="top" nowrap>Group by</td>
<td valign="top" width="100%">
<INPUT NAME="SELECT_PERIOD_GROUP" TYPE="RADIO"  id="e5_frm_select_period_group_d" VALUE="D" <?php if($_POST['SELECT_PERIOD_GROUP'] == 'D') echo "checked";?>>
day   <INPUT TYPE="RADIO" NAME="SELECT_PERIOD_GROUP" VALUE="W"  id="e5_frm_select_period_group_w" <?php if($_POST['SELECT_PERIOD_GROUP'] == 'W') echo "checked";?>>week   <INPUT TYPE="RADIO" NAME="SELECT_PERIOD_GROUP" VALUE="M"  id="e5_frm_select_period_group_m" <?php if($_POST['SELECT_PERIOD_GROUP'] == 'M' || $_POST['SELECT_PERIOD_GROUP'] == '') echo "checked";?>>
month   <INPUT TYPE="RADIO" NAME="SELECT_PERIOD_GROUP" VALUE="N"  id="e5_frm_select_period_group_n" <?php if($_POST['SELECT_PERIOD_GROUP'] == 'N') echo "checked";?>>not grouped   </td>
</tr>
<tr><td colspan="2" width="100%"><input type="submit" name="SHOW_STATISTIC" value="Display"></td></tr>
</form>
</table>
</p>

<p>
<?php 
//get the recent week day
if (!$_POST['SHOW_STATISTIC'])
{
	$week_w = 7;
	$day_begin = time()-24*60*60*($week_w-1);
	$count=$week_w-1;
	for ($i=0;$i<$week_w;$i++)
	{
	    $day_next = $day_begin+24*60*60*$i;
		$day_next = date("Y-m-d",$day_next);
		$nextday_show[] = $day_next;
		//echo $day_next."<br>";
	}
   //print_r($_SERVER['PHP_SELF']);
}

if($_POST['SHOW_STATISTIC']=='Display')
{
  if($_POST['SELECT_PERIOD_GROUP'] == 'M')
  {
    if (($end_year-$begin_year)<0) 
	{
	   echo "<font color='red'>Year is incorrect!</font>"; 
	   exit;
	} 
	if (($end_year-$begin_year)>1) 
	{
	   echo "<font color='red'>Please check your entry. The maximum number of columns that can be displayed side by side is 12.</font>"; 
	   exit;
	} 
	if (($end_year-$begin_year)==1) 
	{
	   $count = ($end_month+13-$begin_month); 
	}
	if ($count>12) 
	{
	   echo "<font color='red'>Please check your entry. The maximum number of columns that can be displayed side by side is 12.</font>"; 
	   exit;
	}	
	if (($end_year-$begin_year)==0)
	{
	   $count = ($end_month-$begin_month); 
	}
	if($count<0)
	{
	   echo "<font color='red'>Month is incorrect!</font>"; 
	   exit;	
	}
	//echo $count;
	

	  for($i=0;$i<$count+1;$i++)
	     {
		     $month_show = $begin_month+$i; 
	         if ($month_show>12) 
		     {
			    $month_show = $month_show-12;
			 } 
		    if (strlen($month_show)==1)
			{
			    $month_show = "0".$month_show;
			}
			//$month_show1[] = $month_show;
			$year_show=$begin_year; 
			if($begin_month+$i>12)
		    {
			   $year_show = $begin_year+1;
			} 
			//$year_show1[] = $year_show;
			$date_show1 = $year_show.'-'.$month_show;
			$date_show[] = $date_show1;
			//echo '<td width="152" align="right" class="innerborder">'.$year_show.'-'.$month_show.'</td>';
		  }
	 
	}
	

	//print_r($date_show);
	$from_day = $begin_year."-".$begin_month."-".$begin_day;
	$to_day = $end_year."-".$end_month."-".$end_day;
	$date = date("Y-m-d");
	$month = date("Y-m");
	
	
    if ($_POST['SELECT_PERIOD_GROUP'] == 'W')
	{
	     if ($end_year==$begin_year)
		 {
		     $begin_week = getweek ($from_day);
			 $end_week = getweek ($to_day);
		     $count = $end_week - $begin_week;
			 if ($count>11)
			 {
			     echo "<font color='red'>Please check your entry. The maximum number of columns that can be displayed side by side is 12.</font>";
				$count = 11	;		 
			 }
		  }
		  
		  if ($end_year!=$begin_year)
		  {
		    $begin_week_1 = getweek ($from_day);
			$end_week_1 = getweek ($begin_year."-12-31");
			$count_1 = $end_week_1 - $begin_week_1;
			$begin_week_2 = getweek ($end_year."-01-01");
		    $end_week_2 = getweek ($to_day);
			$count_2 = $end_week_2-$begin_week_2;
			$count = $count_1 + $count_2;
			 if ($count>11)
			 {
			     echo "<font color='red'>Please check your entry. The maximum number of columns that can be displayed side by side is 12.</font>";
				$count = 11	;		 
			 }
		  }
		  
		  for ($i=0;$i<$count+1;$i++)
		  {
		      if($end_year==$begin_year)
			  {
			      $week = $begin_week+$i;
			      $week_show[]=$week;
				  $year_show[] = $begin_year;
			  }
			  else
			  {
			      if ($count_1 >= $i)
				  {
				     $week = $begin_week_1+$i;
					 $showyear = $begin_year;
				  }
                  else
				  {
				    $week = $i - $count_1;	
					$showyear = $end_year;				
				  }
				  $week_show[]=$week;
				  $year_show[] = $showyear;
			  }
		  }
	   
	   //print_r($week_show);
	}
	
	
	
	
	if ($_POST['SELECT_PERIOD_GROUP'] == 'D')
	{
	     $timeDiff=timeDiff($to_day,$from_day);
		//print_r($timeDiff['day']);
		 if ($timeDiff['day']>11)
		 {
		   echo "<font color='red'>Please check your entry. The maximum number of columns that can be displayed side by side is 12.</font>";
		   $count = 11;
		 }
		 
		 if ($timeDiff['day']<0)
		 {
		   echo "<font color='red'>Datetime format is incorrect!</font>";
		   $count = 0;		     		  
		 }
		 if ($timeDiff['day']>=0 && $timeDiff['day']<=11)
		 {
		    $count = $timeDiff['day'];
		 }
		 $mkbegin = mktimes($from_day);
		 for ($i=0;$i<$count+1;$i++)
		 {
		    $nextday = $mkbegin+24*60*60*$i;
			$nextday_yy=date("Y-m-d",$nextday);
			$nextday_show[] = $nextday_yy;
		 }
		 //print_r($nextday_show);	
	}

	//show dates by selected group
	if ($_POST['SELECT_PERIOD_GROUP'] == 'M')
	{
		echo $begin_day.".".$begin_month.".".$begin_year." - ".$end_day.".".$end_month.".".$end_year."<br>";
		for($i=0;$i<$count+1;$i++){echo $date_show[$i]."  ";}
	}
	if ($_POST['SELECT_PERIOD_GROUP'] == 'D')
	{
		echo $begin_day.".".$begin_month.".".$begin_year." - ".$end_day.".".$end_month.".".$end_year."<br>";
		for($i=0;$i<$count+1;$i++){ echo $nextday_show[$i]."  ";}
	}
	if($_POST['SELECT_PERIOD_GROUP'] == 'W')
	{
	 echo $begin_day.".".$begin_month.".".$begin_year." - ".$end_day.".".$end_month.".".$end_year."<br>";
	 for($i=0;$i<$count+1;$i++){ echo $week_show[$i]."/".$year_show[$i];}
	}
	
	if ($_POST['SELECT_PERIOD_GROUP'] == 'N')
	{
	echo $begin_day.".".$begin_month.".".$begin_year." - ".$end_day.".".$end_month.".".$end_year;
	}

}

if (!$_POST['SHOW_STATISTIC'])
{
   echo date('Y-m-d',$day_begin)." -- ".$end_year."-".$end_month."-".$end_day."<br>";
   for($i=0;$i<$count+1;$i++) {echo $nextday_show[$i]."  ";}
}
?>
<!-- main content -->
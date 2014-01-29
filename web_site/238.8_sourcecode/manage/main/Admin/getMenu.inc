<?php
/*-------------------------------------------
class Name:web admin menus
version:1.0
author:shemily
created:2007-03-11
tips:oc web admin system
alter:
--------------------------------------------*/
class oc_sysMenu{
	var $levels;		//system menu levels
	var $masterQx;		//admin security level
	var $callTimes;		//how many times showSubMenus called

	function oc_sysMenu($levels=2,$masterQx=2){
		$this->levels=$levels;
		$this->masterQx=$masterQx;
	}

	function setLevels($levels=2){
		$this->levels=$levels;
	}

	function getLevels(){
		return $this->levels;
	}
	
	function setMasterQx($masterQx=2){
		$this->masterQx=$masterQx;
	}

	function showTopMenus(){
		$sql="select * from sys_menu where menuLevel=0 order by orderNum";
		$result=mysql_query($sql) or die("Invalid query : ". mysql_error() . "<br/>");
		$i=0;
		while ($row = mysql_fetch_row($result)) {
			$menu[$i++]=$row[2];
		}

		if($this->levels==3){	//for 3 levels menu
?>
<div align="center" style="height:4%;OVERFLOW:auto;">
<table width="200" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><table width="100%" border="1" cellpadding="0" cellspacing="0" class="leftTable">
		  <tr><td align="center"><div id="ocTopMenu1" onclick="showTopMenu('1')" 
			class="topMenu"><?php echo $menu[0]?></div></td></tr></table></td>
            	      <td><table width="100%" border="1" cellpadding="0" cellspacing="0" class="leftTable">
		  <tr><td align="center"><div id="ocTopMenu2" onclick="showTopMenu('2')" 
			class="topMenu"><?php echo $menu[1]?></div></td></tr></table></td>
        </tr>
    </td>
  </tr>
</table>
</div>
<div align="center" style="height:93%; OVERFLOW:auto;">
<table width="150" border="0" cellpadding="0" cellspacing="0">
        <tr><td><?php
		for ($i=0; $i<count($menu); $i++){
			$this->showSubMenus($menu[$i],3);
		}
		?><!--menu show function--></td></tr>
</table>
</div>
<div align="center" style="height:4%;OVERFLOW:auto;">
<table width="200"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="50%"><table width="100%" border="1" cellpadding="0" cellspacing="0" class="leftTable">
		  <tr><td align="center"><div id="ocTopMenu3" onclick="showTopMenu('3')" 
			class="topMenu"><?php echo $menu[2]?></div></td></tr></table></td>
          	      <td><table width="100%" border="1" cellpadding="0" cellspacing="0" class="leftTable">
		  <tr><td align="center"><div id="ocTopMenu4" onclick="showTopMenu('4')" 
			class="topMenu"><?php echo $menu[3]?></div></td></tr></table></td>
        </tr>
      </table>
</div>
<?php
		}elseif($this->levels==2){	//for 2 levels menu
?>
<div align="center" style="height:100%;OVERFLOW:auto;">
<table width="150" border="0" cellpadding="0" cellspacing="0">
        <tr><td><?php
		for ($i=0; $i<count($menu); $i++){
			$this->showSubMenus($menu[$i],3);
		}
		?><!--menu show function--></td></tr>
</table>
</div>
<?php
		}
	}

	function showSubMenus($topMenu,$levels){
		$levels=$this->levels;
		$masterQx=$this->masterQx;
		$sql="select * from sys_menu ";
		if($levels==2){
			$sql=$sql."where menuLevel=0";
		}elseif($levels==3){
			$sql=$sql."where higherMenu='".$topMenu."' and menuLevel=1";
		}
		$sql=$sql." and masterQx >= ".$masterQx." order by orderNum";	//master security setting
			
		$result=mysql_query($sql) or die("Invalid query : ". mysql_error() . "<br/>");
?>
<div id="topmenushow<?php echo ++$this->callTimes?>" <?php
	if($this->callTimes<>1){echo "style=\"display:none;\"";}
?>>
<table cellspacing="0" cellpadding="0" width="145" align="center">
<!--level 2 menu start-->
<?php
		while ($row = mysql_fetch_row($result)) {
?>
  <tr><td><table cellspacing="0" cellpadding="0" border="0" width="200">
		<tr>
			<td class="menuTitle" id="menuTitle" onclick="showSubMenu(<?php echo $row[0]?>)" height="24">
			<table cellspacing="0" cellpadding="0" boder="0" with="100%"><tr align="center">
				<td width="40" align="center"><img id="topimg<?php echo $row[0]?>" src="images/menu/arrow_show.gif" border="0" alt="showmenu"></img></td>	
				<td><?php echo $row[2]?></td>
			</tr></table>
			</td>
		</tr>
		<tr>
			<td><div id="menushow<?php echo $row[0]?>" class="sec_menu">
				<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<!--level 3 menu start-->
				<?php
				$sql="select * from sys_menu";
				if($levels==2){
					$sql=$sql." where higherMenu='".$row[2]."' and menuLevel=1";
				}elseif($levels==3){
					$sql=$sql." where higherMenu='".$row[2]."' and menuLevel=2";
				}
				$sql=$sql." order by orderNum";
				
				$rsSub=mysql_query($sql) or die("Invalid query : ". mysql_error() . "<br/>");
				while($subM=mysql_fetch_row($rsSub)){
				?><tr><td height="20"><a target="<?php echo $subM[8];?>" href="<?php echo $subM[3]?>"><img src="<?php echo $subM[4]?>" 
					border="0" alt="<?php echo $subM[2]?>"></img><br/><?php echo $subM[2]?></a></td>
				  </tr><?php
				}
				?><!--level 3 menu end-->
				</table><br>
			</div></td>
		</tr>
  	</table></td></tr>
<?php
		}
?>
<!--level 2 menu end-->
</table></div>
<?php
	}

	function echoItem(){		//print class name and functions
		echo "Class Name:",get_class($this),"<br/>";
		echo "Functions:<br/>";		
		$class_methods = get_class_methods(get_class($this));
		foreach ($class_methods as $method_name) {
    			echo $method_name,"(),";
		}
		echo "<br/>";
		echo "Menu Levels:",$this->levels,"<br/>";	
	}

}
?>
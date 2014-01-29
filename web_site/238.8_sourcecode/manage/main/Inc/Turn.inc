<?php
require_once "Conn.inc";

class TurnTable {
	var $database;	//database type:mysql or mssql
	var $data;	//select db
	var $link;	//database connection
	var $sql;	//sql query string

	var $showNum;	//the record num to show every page
	var $alterStr;	//the name of alterLink example:"show-alter-delete"
	var $alterLink;	/*--show,alter,delete,etc. link example:
			"del,delpage.php" for delete 
			"show.php,400,300,160,200" for alter in a sub window
			"show.php" for show in mainFrame window--*/
	var $showStyle;	//style of the table,include if need checkBox and if need alert sub window etc.
	var $thisPage;	//name of the just show table page
	var $page;	//the absolute page of just show page
	
	/*-- constructor --*/
    	function TurnTable($database="mysql",$data="test",$link="",$sql="",$alterStr="",$alterLink="",$showStyle="noCheckBox",$thisPage="",$showNum=15,$page=1){
		$this->database=strtolower($database);
		$this->data=strtolower($data);
		$this->link=$link;
		$this->sql=$sql;

		if($showNum<=0){$showNum=1;}
		$this->showNum=$showNum;
		$this->alterLink=$alterLink;
		$this->alterStr=$alterStr;
		$this->showStyle=$showStyle;
		$this->thisPage=$thisPage;
		if($page<=0){$page=1;}
		$this->page=$page;
	}
	/*-- constructor --*/

	function setDataBase($database="mysql"){
		$this->database=$database;
	}
	
	function getDataBase(){
		return $this->database;
	}

	function setData($data){
		$this->data=$data;
	}
	
	function getData(){
		return $this->data;
	}

	function setConn($link){
		$this->link=$link;
	}
	
	function getConn(){
		return $this->link;
	}

	function setSql($sql){
		$this->sql=$sql;
	}
	
	function getSql(){
		return $this->sql;
	}

	function setShowNum($showNum=15){
		if($showNum<=0){$showNum=1;}
		$this->showNum=$showNum;
	}
	
	function getShowNum(){
		return $this->showNum;
	}

	function setAlterLink($alterLink=""){
		$this->alterLink=$alterLink;
	}
	
	function getAlterLink(){
		return $this->alterLink;
	}
	
	function setAlterStr($alterStr=""){
		$this->alterStr=$alterStr;
	}
	
	function getAlterStr(){
		return $this->alterStr;
	}

	function setShowStyle($showStyle="noCheckBox"){
		$this->showStyle=$showStyle;
	}
	
	function getShowStyle(){
		return $this->showStyle;
	}
			
	function setThisPage($thisPage){
		$this->thisPage=$thisPage;
	}
	
	function getThisPage(){
		return $this->thisPage;
	}	

	function setPage($page=1){
		$pageNum=$this->getPageNum();
		if($page<=0){
			$page=1;
		}
		elseif($page>$pageNum){
			$page=$pageNum;
		}
		
		$this->page=$page;
	}
	
	function getPage(){
		return $this->page;
	}
	
	function getRecordNum(){
		$sql=$this->sql;
		$database=$this->database;
		if($database=="mysql"){
			$recordcount=mysql_num_rows($this->getResult($sql));
		}
		elseif($database=="mssql"){
			$recordcount=mssql_num_rows($this->getResult($sql));
		}
		
		return $recordcount;
	}

	function getPageNum(){
		$recordcount=$this->getRecordNum();
		$showNum=$this->showNum;
		if($recordcount % $showNum >0){
			$pageNum=(int)($recordcount/$showNum) + 1;
		}
		else{
			$pageNum=$recordcount/$showNum;
		}
		return $pageNum;
	}	
	
	function getResult($sql){
		$link=$this->link;
		$database=$this->database;
		$data=$this->data;
		
		if($database=="mysql"){
			mysql_select_db($data, $link) or die ("Can\'t use Database : " . mysql_error() . "<br/>");
			$result=mysql_query($sql) or die("Invalid query : ". mysql_error() . "<br/>");
		}
		elseif($database=="mssql"){
			mssql_select_db($data, $link) or die ("Can\'t use Database!<br/>");
			$result=mssql_query($sql) or die("Invalid query!<br/>");
		}
		return $result;
	}
	
	function getTableArr(){		//get table array
		$database=$this->database;
		$sql=$this->sql;
		$page=$this->page;
		$showNum=$this->showNum;
		$pageNum=$this->getPageNum();
		$i=0;
		$j=0;
		$k=0;

		if($database=="mysql"){
			$sql=$sql." limit ".strval(($page-1)*$showNum)." , ".strval($showNum);
			$result=$this->getResult($sql);
			while ($row = mysql_fetch_row($result)) {
				if($k==0){
					for($j=0;$j<mysql_num_fields($result);$j++){
						$tableArr[$k][mysql_field_name($result,$j)]=mysql_field_name($result,$j);
					}
					$k++;
				}
				for($j=0;$j<mysql_num_fields($result);$j++){
					$tableArr[$k][mysql_field_name($result,$j)]=$row[$j];
				}
				$k++;
			}
			mysql_free_result($result);
		}
		elseif($database=="mssql"){
			$sql=$sql;
			$i=($page-1)*$showNum;		//start seek row
			
			$result=$this->getResult($sql);
			if($i>mssql_num_rows($result)){		//if seek row num > row num
				$i=mssql_num_rows($result)-$showNum;
			}
			mssql_data_seek($result,$i);
			while ($row = mssql_fetch_row($result)) {
				if($k==0){
					for($j=0;$j<mssql_num_fields($result);$j++){
						$tableArr[$k][mssql_field_name($result,$j)]=mssql_field_name($result,$j);
					}
					$k++;
				}
				if($i>=($page-1)*$showNum+$showNum){break;}
				for($j=0;$j<mssql_num_fields($result);$j++){
					$tableArr[$k][mssql_field_name($result,$j)]=$row[$j];
				}
				$k++;
				$i++;
			}
			mssql_free_result($result);
		}
		
		return $tableArr;
	}
	
	function showTable(){		//show turn table
		$arr=$this->getTableArr();
		echo "<table id=turnTable class=turnTable>\n";
		echo "<tr>\n";
		$subarr=$arr[0];
		foreach($subarr as $key => $value){
			if($value!=""){echo "<th>$value</th>\n";}
			else{echo "<th>&nbsp;</th>\n";}
		}
		if($this->checkAlterStr()){
			echo "<th colspan='".$this->checkAlterStr()."'>操作</th>\n";
		}
		echo "</tr>\n";
		next($arr);		
			
		while($subarr = current($arr)){
			echo "<tr onMouseOver=\"this.style.backgroundColor='#E6E6E6'\" onMouseOut=\"this.style.backgroundColor=''\">\n";
			$subarr=$arr[key($arr)];
			foreach($subarr as $key => $value){
				if($value!=""){echo "<td>$value</td>\n";}
				else{echo "<td>&nbsp;</td>\n";}
			}
			reset($subarr);
			$this->showAlterStr($subarr[key($subarr)]);	//show alter strings
			echo "</tr>\n";
	
			next($arr);
		}
		echo "</table>\n";
	}
	
	function showTurnLink(){
		$pageNum=$this->getPageNum();
		$page=$this->page;
		$thisPage=$this->thisPage;
	
		echo "<table id=turnLink class=turnLink><tr><td>\n";
		echo "共".$this->getRecordNum()."条记录 ";
		if($page==1){
			echo "<a href=$thisPage?page=".strval($page+1).">下页</a>\n";
			echo "<a href=$thisPage?page=".strval($pageNum).">尾页</a>\n";
		}
		elseif($page>1 and $page<$pageNum){
			echo "<a href=$thisPage?page=1>首页</a>\n";
			echo "<a href=$thisPage?page=".strval($page-1).">上页</a>\n";
			echo "<a href=$thisPage?page=".strval($page+1).">下页</a>\n";
			echo "<a href=$thisPage?page=".strval($pageNum).">尾页</a>\n";
		}
		elseif($page==$pageNum){
			echo "<a href=$thisPage?page=1>首页</a>\n";
			echo "<a href=$thisPage?page=".strval($page-1).">上页</a>\n";
		}
		echo "当前第".$page."页&nbsp;共".$pageNum."页&nbsp;\n";
		echo "<input type=text name=turn_page class=turnLink_text onChange=\"javascript:checkNum('turn_page')\" />";
		echo "<input type=button name=turn_button value=Go class=turnLink_button onclick=\"goPage('$thisPage','turn_page')\" />\n";
		echo "</td></tr></table>\n";
	}
	
	function showAlterStr($id){	//-------show alter strings-------
		if($this->checkAlterStr()){
			$alterStr=split("-",$this->alterStr);
			$alterLink=split("-",$this->alterLink);
			$strNum=count($alterStr);
			$linkNum=count($alterLink);

			for($i=0;$i<$strNum;$i++){
				if(strpos($alterLink[$i],"del")!==false){
					$linkArr=split(",",$alterLink[$i]);
					echo "<td class='turnTable_cmd'><a href='#' 
					onclick=\"ClickDel('$linkArr[1]?id=$id&action=del&page=$this->page')\">$alterStr[$i]</a></td>\n";
				}
				elseif(strpos($alterLink[$i],",")!==false){
					$linkArr=split(",",$alterLink[$i]);
					echo "<td class='turnTable_cmd'><a href='#' 
						onclick='openWithSubwindow(\"$linkArr[0]?id=$id\",$linkArr[1],$linkArr[2],$linkArr[3],$linkArr[4])'>$alterStr[$i]</a></td>\n";
				}
				else{
					echo "<td class='turnTable_cmd'><a href='$alterLink[$i]?id=$id&page=$this->page'>$alterStr[$i]</a></td>\n";
				}
			}
		}
	}

	function checkAlterStr(){
		if($this->alterStr==""){	//if alter string is null then return null
			return NULL;
		}
		$alterStr=split("-",$this->alterStr);
		$alterLink=split("-",$this->alterLink);
		$strNum=count($alterStr);
		$linkNum=count($alterLink);
		if($strNum!=$linkNum){
			return false;
		}
		return $strNum;
	}

	function echoTurnTable(){	//print class name and functions
		echo "Class Name:".get_class($this)."<br/>\n";
		echo "functions:<br/>\n";		
		$class_methods = get_class_methods(get_class($this));
		foreach ($class_methods as $method_name) {
    		echo $method_name.",";
		}
		echo "<br/>\n";
	}
}//---end

/*--Test Code--*/
/*
$sql="select * from test";
//$sql="select id,vip_firstName,vip_lastName,vip_Num,vip_fmobile,vip_tel from vip_user_record";
$tb=new TurnTable($database,$data,$conn->getConn(),$sql,"show-alter","show.php-alter.php");
//$tb->echoTurnTable();
$tb->setShowNum(5);
$page=$_REQUEST["page"];
$tb->setPage($page);
$tb->setThisPage("Turn.php");
$tb->showTable();
$tb->showTurnLink();
//$arr=$tb->getTableArr();
//print_r(array_values($arr));
//echo "page: ",$tb->getPage();
//echo "<br/>pageNum: ",$tb->getPageNum();
*/
/*--Test Code--*/
?>
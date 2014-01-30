<?php
/*-------------------------------------------
class Name:get connection for Database
version:1.1
author:shemily
created:2006-11-04
tips:Connection for MySQL,MSSQL
alter:
add selectDB,query function @2007-03-07
add exec function @07-03-14
--------------------------------------------*/
class Conn{
	var $hostStr;
	var $userName;
	var $userPass;
	var $link;
	
	function Conn($hostStr="Localhost",$userName="root",$userPass="root"){	// constructor
		$this->hostStr=$hostStr;
		$this->userName=$userName;
		$this->userPass=$userPass;
	}

	function getConn($dataBaseType="mysql"){		//get database link,default for mysql
		if(strtolower($dataBaseType)=="mysql"){		//mysql
			$link=mysql_connect($this->hostStr, $this->userName, $this->userPass)
			  		or die("Could not connect: " . mysql_error() . "<br/>");
			$this->link=$link;
			return $link;
		}
		elseif(strtolower($dataBaseType)=="mssql"){	//mssql
			$link=mssql_connect($this->hostStr, $this->userName, $this->userPass)
			  		or die("Could not connect mssql server!<br/>");
			$this->link=$link;
			return $link;
		}
		else{
			return NULL;
		}
	}

/*-------added at 2007-03-07------*/
	function selectDB($dataBase="test"){
		$link=$this->link;
		mysql_select_db($dataBase, $link) or die ("Can't use Database : " . mysql_error() . "<br/>");
	}

	function query($sql="select now()") {		//run a sql query
		$sql = preg_replace("/\/\*.*\*\/;?/","",$sql);
		$sql = preg_replace("/--.*\r/","",$sql);
		$sql = str_replace("\r", "\n", $sql);

		foreach(explode(";\n", trim($sql)) as $query) {
			$query = preg_replace("/[\n|;]/", "", $query);
			mysql_query($query) or die("Invalid query : ". mysql_error() . "<br/>");
		}

	}

	function exec($sql="select now()"){
		$this->query($sql);
	}
/*-------added at 2007-03-07------*/

	function setHostStr($hostStr = "Localhost"){
		$this->hostStr=$hostStr;
	}
	
	function getHostStr(){
		return $this->hostStr;
	}
	
	function setUserName($userName = "root"){
		$this->userName=$userName;
	}
	
	function getUserName(){
		return $this->userName;
	}
	
	function setUserPass($userPass = "root"){
		$this->userPass=$userPass;
	}
	
	function getUserPass(){
		return $this->userPass;
	}

	function testConn($data,$sql,$database="mysql"){		//test connection
		$link=$this->link;
		if($database=="mysql"){
			mysql_select_db($data, $link) or die ("Can\'t use Database : " . mysql_error() . "<br/>");
			$result=mysql_query($sql) or die("Invalid query : ". mysql_error() . "<br/>");
			while ($row = mysql_fetch_row($result)) {
				printf ("%s: %s  %s: %s<br/>",mysql_field_name($result,0),$row[0],mysql_field_name($result,1),$row[1]);
			}
			mysql_free_result($result);
		}
		elseif($database=="mssql"){
			mssql_select_db($data, $link) or die ("Can\'t use Database!<br/>");
			$result=mssql_query($sql) or die("Invalid query!<br/>");
			while ($row = mssql_fetch_row($result)) {
				printf ("%s: %s  %s: %s<br/>",mssql_field_name($result,0),$row[0],mssql_field_name($result,1),$row[1]);
			}
			mssql_free_result($result);
		}
	}
	
	function echoConn(){		//print class name and functions
		echo "Class Name:",get_class($this),"<br/>";
		echo "functions:<br/>";		
		$class_methods = get_class_methods(get_class($this));
		foreach ($class_methods as $method_name) {
    			echo $method_name,",";
		}
		echo "<br/>";
		echo "hostStr:",$this->hostStr,"<br/>";	
		echo "userName:",$this->userName,"<br/>";	
		echo "userPass:",$this->userPass,"<br/>";	
	}
}

/*----connection init----*/
require_once $oc_dbStr."Config/Config.php";
$conn=new conn();
$conn->setHostStr($admin_HostStr);
$conn->setUserName($admin_UserName);
$conn->setUserPass($admin_UserPass);
$conn->getConn($dataBaseType);
$conn->selectDB($dataBase);
?>
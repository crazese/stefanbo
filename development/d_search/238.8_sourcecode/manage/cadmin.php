<?php
include_once('../Class/TemplateUser.php');
class Admin extends TemplateUser
{
	function getNonTemplates()
	{
		$query = "select * from product where cg_id is null order by type";
		global $db;
		$result = $db->query($query);
		$err = mysql_error();
		mysql_close();
		$nonTemplates = array();
		if($err)
		{
			$warningMsg = 'DB error!';
			return $nonTemplates;
		}
		while($rows = mysql_fetch_assoc($result))
		{
			$nonTemplates[] = $rows;
		}
		return $nonTemplates;
	}
	//Add category
	function AddCategory($name)
	{
        global $db;
		$db->query("INSERT INTO $this->m_strCategoryTable (`id`, `name`) VALUES (null, '$name'");
		return $dn->insertId();
	}
	
	//update category
	function UpdateCategory( $id, $name)
	{
        global $db;
		$db->query("UPDATE $this->m_strCategoryTable SET categoryName = '$strCategoryName' WHERE id = $id");
		return $db->affectedRows();
	}

	//delete a category and all templates belong to it
	function DeleteCateGory($id)
	{
		global $db;
		//delete the products in this category
		$query = "DELETE FROM $this->m_strTemplateTable WHERE cg_id = $id";
		$result = $db->query($query);		
		$query = "DELETE FROM $this->m_strCategoryTable WHERE cg_id = $id";
		$result = $db->query($query);
		return $db->affectedRows();		
	}
	

	//add template
	function AddTemplate($id, $categoryId, $name, $strKeyword, $regTime, 
						$priceUSD,$priceUSD1,$priceEUR,$priceEUR1,$exclusivePriceUSD,$exclusivePriceEUR,
						$swfWidth,$swfHeight,$swfBgcolor,$swfBgimage,
						$strAvailableSources,$strRequiredSoftware,
						$strDownloadPath,$strSelloutId,$strState,$E5_id,$E5_selloutId)
	{
        global $db;
		$spaceCharacters = array("\r","\n","
");
		if(empty($priceUSD) || empty($priceEUR) || empty($exclusivePriceUSD) || empty($exclusivePriceEUR))
		{
			echo("
			<script>
			window.alert('All price infomation is required');
			history.go(-1);
			</script>
			");
			exit;
		}
		$currentTime = time();
		$regTime = empty($regTime) ? $currentTime : $regTime;
		$priceUSD1 = empty($priceUSD1) ? $priceUSD : $priceUSD1;
		$priceEUR1 = empty($priceEUR1) ? $priceEUR : $priceEUR1;
		$swfWidth = empty($swfWidth) ? 766 : $swfWidth;
		$swfHight = empty($swfHight) ? 766 : $swfHight;
		$swfBgcolor = empty($swfBgcolor) ? 'FFFFFF' : $swfBgcolor;
		$strAvailableSources = empty($strAvailableSources) ? '.PSD;.HTML;.FLA;.SWF;.SQF;.WAV;.MP3;' : str_replace($spaceCharcters,'',$strAvailableSources);
		$strRequiredSoftware = empty($strRequiredSoftware) ? 'Adobe Photoshop 7+;Macromedia Dreamweaver MX+;Adobe GoLive 7+;Microsoft Frontpage XP;Macromedia Flash MX2004+;Sothink Quicker 2.0b60310+;' : str_replace($spaceCharcters,'',$strRequiredSoftware);
		$strState = empty($strState) ? 'Selling' : $strState;
		$E5_id = empty($E5_id) ? 'null' : $E5_id;
		$E5_selloutId = empty($E5_selloutId) ? 'null' : $E5_selloutId;
		$strTemplateId = sprintf("%04s",$strTemplateId);
		$strSelloutId = sprintf("%04s",$strSelloutId);

        //insert as a prouct at first
        //`pd_id`,productName as `name`, `type`,`priceUSD`,`priceUSD1`,`priceEUR`,`priceEUR1`,`E5_id`
        $db->query("insert into $this->m_strProducttable values (null, '$id','$name','template',$priceUSD,'$priceUSD1',$priceEUR,$priceEUR1,E5_id)");
        //insert template
		$query = "INSERT INTO $this->m_strTemplateTable ( `pd_id` , `cg_id`, `keyword` ,  `regTime`,
		`exclusivePriceUSD`, `exclusivePriceEUR`,
		`swfWidth`, `swfHeight`, `swfBgcolor`, `swfBgimage`,
        `availableSources`, `requiredSoftware`, 
        `downloadPath`, `selloutId`, `state`, `E5_selloutId`) 
		VALUES ('$id', $categoryId, '$strKeyword', $regTime,
			$exclusivePriceUSD,$exclusivePriceEUR,
			$swfWidth,$swfHeight,'$swfBgcolor','$swfBgimage',
			'$strAvailableSources','$strRequiredSoftware',
			'$strDownloadPath','$strSelloutId','$strState',$E5_selloutId)";
		$result = $db->query($query);
		return $db->insertId();
	}

	//update tempalte Info
	function updateTemplate($id, $categoryId, $name, $strKeyword, $regTime, 
						$priceUSD,$priceUSD1,$priceEUR,$priceEUR1,$exclusivePriceUSD,$exclusivePriceEUR,
						$swfWidth,$swfHeight,$swfBgcolor,$swfBgimage,
						$strAvailableSources,$strRequiredSoftware,
						$strDownloadPath,$strSelloutId,$strState,$E5_id,$E5_selloutId)
	{
        global $db;
        if(empty($priceUSD) || empty($priceEUR) || empty($exclusivePriceUSD) || empty($exclusivePriceEUR))
		{
			echo("
			<script>
			window.alert('All price infomation is required');
			history.go(-1);
			</script>
			");
			exit;
		}
		$currentTime = time();		
		$regTime = empty($regTime) ? $currentTime : $regTime;
		$priceUSD1 = empty($priceUSD1) ? $priceUSD : $priceUSD1;
		$priceEUR1 = empty($priceEUR1) ? $priceEUR : $priceEUR1;
		$swfWidth = empty($swfWidth) ? 766 : $swfWidth;
		$swfHight = empty($swfHight) ? 766 : $swfHight;
		$swfBgcolor = empty($swfBgcolor) ? 'FFFFFF' : $swfBgcolor;
		$strAvailableSources = empty($strAvailableSources) ? '.PSD;.HTML;.FLA;.SWF;.SQF;.WAV;.MP3;' : $strAvailableSources;
		$strRequiredSoftware = empty($strRequiredSoftware) ? 'Adobe Photoshop 7+;Macromedia Dreamweaver MX+;Adobe GoLive 7+;Microsoft Frontpage XP;Macromedia Flash MX2004+;Sothink Quicker 2.0b60310+;' : $strRequiredSoftware;
		$strState = empty($strState) ? 'Selling' : $strState;
		$E5_id = empty($E5_id) ? 'null' : $E5_id;
		$E5_selloutId = empty($E5_selloutId) ? 'null' : $E5_selloutId;
		$strTemplateId = sprintf("%04s",$strTemplateId);
		$strSelloutId = sprintf("%04s",$strSelloutId);		
        $query = "update $this->m_strProductTable set `name`='$strTemplateName',
        `priceUSD`=$priceUSD,`priceUSD1`=$priceUSD1,`priceEUR`=$priceEUR,`priceEUR1`=$priceEUR1
        where `pd_id`='$strTemplateId'";
        $db->query($query);
        $affetedRows1 = $db->affectedRows();
		$query = "update $this->m_strTemplateTable set 
        `cg_id`=$nCategoryId, `keyword`='$strKeyword', `regTime`='$regTime',		
		`exclusivePriceUSD`=$exclusivePriceUSD,`exclusivePriceEUR`=$exclusivePriceEUR,
		`swfWidth`=$swfWidth,`swfHeight`=$swfHeight,`swfBgcolor`='$swfBgcolor', `swfBgimage`='$swfBgimage',`swfFilePath`='$swfFilePath',
        `availableSources`='$strAvailableSources', `requiredSoftware`='$strRequiredSoftware', `downloadPath`='$strDownloadPath',`selloutId`='$strSelloutId',`E5_id`=$E5_id,`E5_selloutId` = $E5_selloutId
		 where `pd_id`='$strTemplateId'";
		$result = $db->query($query);
        $affectedRows2 = $db->affectedRows();
		return $affetctedRows1 + $affectedRows2;
	}

	//delete tempalte
	function DeleteTemplate($strTemplateId,&$warningMsg)
	{
        global $db;
		$strTemplateId = sprintf("%04s",$strTemplateId);
		$db->query("DELETE FROM $this->m_strTemplateTable WHERE pd_id = '$strTemplateId'");
        $db->query("delete from $this->m_strProductTable Where pd_id = '$strTemplateId'");
		return $db->affectedRows();
	}	

	//detele user
	function DeleteUser($userId)
	{
		global $db;
		$query = "DELETE FROM $this->m_strUserTable WHERE US_id = $userId";
		$result = $db->query($query);
		return $db->affectedRows();
	}

	function getUsers($nStart,$limit)
	{
		$nStart = empty($nStart) ? 0 : $nStart;
		$limit = empty($limit) ? 50 : $limit;
		global $db;
		$query = "select * FROM $this->m_strUserTable order by US_id DESC limit $nStart,$limit ";
		$result = $db->query($query);
		$err = mysql_error(); 
		$users = array();
		if($err)
		{
			return $users;
		}
		$userNum = mysql_num_rows($result);
		while($rows = mysql_fetch_assoc($result))
		{
			$rows['avalidPurchasedCount'] = $this->getValidPurchasedCount($rows['US_id']);
			$rows['purchasedCount'] = $this->getPurchasedCount($rows['US_id']);
			$users[] = $rows;
		}
		return $users;
	}

	function AdminUpdateUserInfo($userId,$strUserName,$strTrueName,$strPassword,$strEmail,$bIsAdmin,$bIsStop,&$warningMsg)
    {
        if(strlen($strUserName) >= 3 && strlen($strUserName) <= 16 &&
             strlen($strPassword) >= 4 && strlen($strPassword) <= 20 &&
             strlen($strEmail) >= 1 && strlen($strEmail) <= 255)
        {
            global $db;
            $query = "UPDATE $this->m_strUserTable SET userName = '$strUserName',trueName = '$strTrueName',password = '$strPassword',Email = '$strEmail',isAdministrator='$bIsAdmin',isStop = '$bIsStop' WHERE US_id = $userId";
            $result = mysql_db_query($this->m_strDatabaseName,$query);
            $err = mysql_error();
            mysql_close();
			return true;
        }
        else
        {
            return false;
        }
    }
		
		function stopPurchased($purchasedId)
		{
			global $db;
			$query = "update purchased set isStop='Y' where PC_id = $purchasedId";
			$result = $db->query($query);
			$err = mysql_error(); 
			if($err)
			{
				mysql_close();
				return false;
			}
			return true;
		}
		
		function resumePurchased($purchasedId)
		{
			global $db;
			$query = "update purchased set isStop='N' where PC_id = $purchasedId";
			$result = $db->query($query);
			$err = mysql_error(); 
			if($err)
			{
				mysql_close();
				return false;
			}
			return true;
		}
		
		function DeletePurchased($purchasedId)
		{
			global $db;
			$query = "DELETE FROM purchased WHERE PC_id = $purchasedId";
			$result = $db->query($query);
			$err = mysql_error(); 
			if($err)
			{
				mysql_close();
				return false;
			}
			return true;
		}
		
		function GetOrderLogsCount()
		{
			$query = "select count(*) as count FROM orderlog";
			global $db;
			$result = $db->query($query);
			$err = mysql_error(); 
			mysql_close();
			$row = mysql_fetch_assoc($result);
			$count = $row['count'];
			return $count;
		}
		
		function getOrderLogs($nStart,$limit)
		{
			$nStart = empty($nStart) ? 0 : $nStart;
			$limit = empty($limit) ? 50 : $limit;
			global $db;
			$query = "select orderlog.*,user.userName 
								  FROM orderlog left join user on orderlog.US_id=user.US_id 
						  order by OL_id limit $nStart,$limit ";
			$result = $db->query($query);
			$err = mysql_error(); 
			mysql_close();
			$orders = array();
			if($err)
			{
				return $orders;
			}
			while($rows = mysql_fetch_assoc($result))
			{
				$orders[] = $rows;
			}
			return $orders;
	}
	
	function DeleteOrderLog($snCode)
	{
		global $db;
		$query = "DELETE FROM orderlog WHERE snCode = '$snCode'";
		$result = $db->query($query);
		$err = mysql_error(); 
		mysql_close();
		if($err)
		{
			
			return false;
		}
		return true;
	}
	
	function GetDownloadLogsCount($productId,$userId,$hidenAdmin = true)
		{
			$query = "select count(*) as count FROM downloadlog,user 
								 where downloadlog.US_id=user.US_id ";
			if($userId)
			{
				$query .= "&& downloadlog.US_id=$userId ";
			}
			if($productId)
			{
				$query .= "&& downloadlog.pd_id=$productId";
			}
			if(!$hideAdmin)
			{
				$query .= "&& user.isAdministrator!='y' ";
			}
			global $db;
			$result = $db->query($query);
			$err = mysql_error(); 
			mysql_close();
			$row = mysql_fetch_assoc($result);
			$count = $row['count'];
			return $count;
		}
		
	function GetDownloadLogs($productId,$userId, $start, $pageLimit,$hidenAdmin = true)
	{
		$start = isset($start) ? $start : 0;
		$pageLimit = isset($pageLimit) ? $pageLimit : 30;
		global $db;
		$query = "select downloadlog.pd_id as pd_id, downloadlog.downloadTime as downloadTime, user.userName as userName FROM downloadlog,user where user.US_id=downloadlog.US_id ";
		if($userId)
		{
			$query .= "&& downloadlog.US_id=$userId ";
		}
		if($productId)
		{
			$query .= "&& downloadlog.pd_id=$productId";
		}
		if(!$hideAdmin)
		{
			$query .= "&& user.isAdministrator!='y' ";
		}
		$query .= " order by downloadlog.downloadTime DESC limit $start,$pageLimit";
		$result = $db->query($query);
		$err = mysql_error(); 
		mysql_close();
		$downloads = array();
		if($err)
		{
			echo $err;
			return $downloads;
		}
		while($rows = mysql_fetch_assoc($result))
		{
			$downloads[] = $rows;
		}
		return $downloads;
	}
	function searchPurchasedCount($userName,$templateId,$startTime,$endTime,$hidenAdmin=true)
	{
		$query = "select count(*) as count from purchased,user where purchased.US_id=user.US_id ";
		if(!empty($userName))
		{
			$query .= "&& user.userName='$userName' ";
		}
		if(!empty($templateId))
		{
			$query .= "&& purchased.pd_id='$templateId' ";
		}
		if(!empty($startTime))
		{
			if(empty($endTime) || $startTime==$endTime)
			{
				$query .= "&& purchased.regTime like '$startTime%' ";
			}
			else
			{
				$query .= "&& ((purchased.regTime between '$startTime%' and '$endTime%')
									 ||purchased.regTime like '$ensTime')";
			}
		}
		if(!$hideAdmin)
		{
			$query .= "&& user.isAdministrator!='y' ";
		}
		global $db;
		$result = $db->query($query);
		$err = mysql_error(); 
		mysql_close();
		$row = mysql_fetch_assoc($result);
		$count = $row['count'];
		return $count;
	}
	
	function searchPurchased($userName,$templateId,$startTime,$endTime,$hideAdmin=true)
	{
		$query = "select user.userName as userName,
										 purchased.pd_id as pd_id, purchased.regTime as regTime, purchased.overdue as overdue
							  from purchased,user where purchased.US_id=user.US_id ";
		if(!empty($userName))
		{
			$query .= "&& user.userName='$userName' ";
		}
		if(!empty($templateId))
		{
			$query .= "&& purchased.pd_id='$templateId' ";
		}
		if(!empty($startTime))
		{
			if(empty($endTime) || $startTime==$endTime)
			{
				$query .= "&& purchased.regTime like '$startTime%' ";
			}
			else
			{
				$query .= "&& ((purchased.regTime between '$startTime%' and '$endTime%')
									 || purchased.regTime like '$endTime%')";
			}
		}
		if($hideAdmin)
		{
			$query .= "&& user.isAdministrator!='y' ";
		}
		$query .= "order by purchased.regTime DESC";
		global $db;
		$result = $db->query($query);
		$err = mysql_error(); 
		mysql_close();
		$purchases = array();
		if($err)
		{
			return $purchases;
		}		
		
		while($rows = mysql_fetch_assoc($result))
		{
			$purchases[] = $rows;
		}
		return $purchases;
	}
	
	function GetPaypalSearchCount($txn_id, $email, $regdate)
	{
		$query = "select count(*) as count from paypalorder where
	                 txn_id LIKE '%$txn_id%' 
									 && payer_email LIKE '%$email%' 
									 && Regdate LIKE '%$regdate%' ";
		global $db;
		$result = $db->query($query);
		$err = mysql_error(); 
		mysql_close();
		$row = mysql_fetch_assoc($result);
		$count = $row['count'];
		return $count;
	}
	
	function GetPaypalSearch($txn_id, $email, $regdate, $start, $limit)
	{
		$query = "select *
							  from paypalorder
							 where txn_id LIKE '%$txn_id%' 
									&& payer_email LIKE '%$email%' 
							 	  && Regdate LIKE '%$regdate%' 
					  order by ID DESC limit $start,$limit";
		global $db;
		$result = $db->query($query);
		$err = mysql_error(); 
		mysql_close();
		$paypals = array();
		if($err)
		{
			return $paypals;
		}
		
		
		while($rows = mysql_fetch_assoc($result))
		{
			$paypals[] = $rows;
		}
		return $paypals;
	}
	
	function adminSearch($intCategoryId,$strKeyword,$time,$nStart,$nLimit,$strOrderBy,$strOrder)
    {
        global $db;
        global $db;
		$query = "select * from $this->m_strTemplateTable
				  where type='template' ";
		if(!empty($intCategoryId) && $intCategoryId!=0)
		{
			$query .= "&& cg_id=$intCategoryId ";
		}
		if(!empty($strKeyword) && $strKeyword!="")
		{
			$query .= "&& (productName like '%$strKeyword%' || keyword like '%$strKeyword%' || pd_id like '%$strKeyword%') ";
		}
		if(!empty($time) && $time!=0)
		{
			$regTime = date('Y-m-d H:i:s',time() - $time*86400);
			 $query .= "&& regTime >= '$regTime' ";
		}
		
		if(!empty($strOrderBy))
		{
			$query.="order by $strOrderBy ";
			if(empty($strOrder))
			{
				$strOrder = "DESC";
			}
			$query .= "$strOrder ";
		}		
		if(empty($nStart))
		{
			$nStart = 0;
		}
		$query .= "limit $nStart,$nLimit ";
        $result = mysql_db_query($this->m_strDatabaseName,$query);
        $err = mysql_error();
        mysql_close();
		$searchRows = array();
        if($err)
        {
            //db error
        }
        else
        {
            while($rows = mysql_fetch_assoc($result))
            {
				$rows['downloadHit'] = $this->GetDownloadLogsCount($rows['pd_id'],0);
				//exit;
                $searchRows[] = $rows;
            }
        }
        return $searchRows;
    }
	
	function GetPaypalDetail($txn_id)
	{
	  $query = "select * from paypalorder where txn_id='$txn_id'";
		global $db;
		$result = $db->query($query);
		$err = mysql_error(); 
		mysql_close();
		$paypal = mysql_fetch_assoc($result);
		return $paypal;
	}
	
	function getPaypalProducts($txn_id)
	{
		$query = "select * from paypalordersp where txn_id='$txn_id'";
		global $db;
		$result = $db->query($query);
		$err = mysql_error(); 
		mysql_close();
		$products = array();
		while($rows = mysql_fetch_assoc($result))
		{
			$products[] = $rows;
		}
		return $products;
	}
	
	function searchTrueHitsCount($query)
	{
		extract($query);
		$query = "select count(*) from trueHit where 1=1 ";
		if($productId)
		{
			$query .= " && pd_id = $productId ";
		}
		if($startTime)
		{
		  if(!$endTime)
		  {
			 $query.=" && time like '$startTime%' ";
		  }
		  else
		  {
			 if($endTime==$startTime)
			 {
				 $query.=" && time like '$startTime%' ";
			 }
			 else
				$query.=" && ((time between '$startTime%' and '$endTime%') || time like '$endTime%') ";
		  }
		}
		global $db;
		$result = $db->query($query);
		$err = mysql_error(); 
		mysql_close();
		return mysql_result($result,0,0);
	}
	
	function searchTrueHits($query)
	{
		extract($query);
		$query = "select * from trueHit where 1=1 ";
		if($productId)
		{
			$query .= " && pd_id = $productId ";
		}
		if($startTime)
		{
		  if(!$endTime)
		  {
			 $query.=" && time like '$startTime%' ";
		  }
		  else
		  {
			 if($endTime==$startTime)
			 {
				 $query.=" && time like '$startTime%' ";
			 }
			 else
				$query.=" && ((time between '$startTime%' and '$endTime%') || time like '$endTime%') ";
		  }
		}		
		$query .= ' order by id DESC ';
		$query .= " limit $start,$limit ";
		global $db;
		$result = $db->query($query);
		$err = mysql_error(); 
		mysql_close();
		$hits = array();
		while($rows = mysql_fetch_assoc($result))
		{
			$rows['ip'] = long2ip($rows['ip']);
			$hits[] = $rows;
		}
		return $hits;
	}
	
	function searchTrueHitsGroup($query)
	{
		extract($query);
		$query = "select pd_id, count(*) as count  from trueHit where 1=1 ";
		if($productId)
		{
			$query .= " && pd_id = $productId ";
		}
		if($startTime)
		{
		  if(!$endTime)
		  {
			 $query.=" && time like '$startTime%' ";
		  }
		  else
		  {
			 if($endTime==$startTime)
			 {
				 $query.=" && time like '$startTime%' ";
			 }
			 else
				$query.=" && ((time between '$startTime%' and '$endTime%') || time like '$endTime%') ";
		  }
		}		
		$query .= " group by pd_id order by count DESC ";
		global $db;
		$result = $db->query($query);
		$err = mysql_error(); 
		mysql_close();
		$hits = array();
		while($rows = mysql_fetch_assoc($result))
		{
			$hits[] = $rows;
		}
		return $hits;
	}
	
	function searchKeywordCount($query)
	{
		extract($query);
		$query = "select count(*) from searchStat where 1=1 ";
		if($keyword)
		{
			$query .= " && keyword like '%$keyword%' ";
		}
		if($startTime)
		{
		  if(!$endTime)
		  {
			 $query.=" && time like '$startTime%' ";
		  }
		  else
		  {
			 if($endTime==$startTime)
			 {
				 $query.=" && time like '$startTime%' ";
			 }
			 else
				$query.=" && ((time between '$startTime%' and '$endTime%') || time like '$endTime%') ";
		  }
		}
		global $db;
		$result = $db->query($query);
		$err = mysql_error(); 
		mysql_close();
		return mysql_result($result,0,0);
	}
	
	function searchKeyword($query)
	{
		extract($query);
		$query = "select * from searchStat where 1=1 ";
		if($keyword)
		{
			$query .= " && keyword like '%$keyword%' ";
		}
		if($startTime)
		{
		  if(!$endTime)
		  {
			 $query.=" && time like '$startTime%' ";
		  }
		  else
		  {
			 if($endTime==$startTime)
			 {
				 $query.=" && time like '$startTime%' ";
			 }
			 else
				$query.=" && ((time between '$startTime%' and '$endTime%') || time like '$endTime%') ";
		  }
		}
	    $query .= ' order by id DESC ';
		$query .= " limit $start,$limit ";
		global $db;
		$result = $db->query($query);
		$err = mysql_error(); 
		mysql_close();
		$keylogs = array();
		while($rows = mysql_fetch_assoc($result))
		{
			$keylogs[] = $rows;
		}
		return $keylogs;
	}
	
	function searchKeywordGroup($query)
	{
		extract($query);
		$query = "select *, count(*) as count from searchStat where 1=1 ";
		if($keyword)
		{
			$query .= " && keyword like '%$keyword%' ";
		}
		if($startTime)
		{
		  if(!$endTime)
		  {
			 $query.=" && time like '$startTime%' ";
		  }
		  else
		  {
			 if($endTime==$startTime)
			 {
				 $query.=" && time like '$startTime%' ";
			 }
			 else
				$query.=" && ((time between '$startTime%' and '$endTime%') || time like '$endTime%') ";
		  }
		}
	    $query .= ' Group by keyword order by count DESC ';
		$query .= " limit $start,$limit ";
		global $db;
		$result = $db->query($query);
		$err = mysql_error(); 
		mysql_close();
		$keylogs = array();
		while($rows = mysql_fetch_assoc($result))
		{
			$keylogs[] = $rows;
		}
		return $keylogs;		
	}
}

?>
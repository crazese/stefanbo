<?php
class Player {
  public function __construct($id, $baseinfo=null) {
    $this->id_ = $id;
    $this->baseinfo_ = $baseinfo;
    $this->baseinfoChanged_ = false;
    
    $this->items_ = null;
    $this->slotsdelete_ = array();
    $this->slotsupdate_ = array();
    $this->slotsadd_ = array();
    $this->idsnew_= array();
    
    $this->itemdelayed_ = false;
    $this->context_ = null;
    $this->contextchanged_ = false;
  }

  public function GetId() {
    return $this->id_;
  }
  
  public function &GetBaseInfo() {
    if($this->baseinfo_ ) return $this->baseinfo_ ;
    
    global $mc;
	$pid = $this->id_;
    $roleInfo = $mc->get ( MC . $pid );
    if(empty($roleInfo)) {
      $roleInfo = roleModel::getTableRoleInfo ( $pid );
      $mc->set ( MC . $pid, $roleInfo, 0, 3600);
    }
    $return = null;
    if (empty ( $roleInfo ))  return $return;
    //$roleInfo = roleModel::setRoleVip($roleInfo);
	$this->baseinfo_ = $roleInfo;
	return $this->baseinfo_;
  }
  
  public function ModifyBaseInfo($attrs,$updatenow = true) {
  	$baseinfo = &$this->GetBaseInfo();
  	foreach($attrs as $attr=>$value) {
  		$baseinfo[$attr]= $value;
  	}
  	if($updatenow) {
  		//global $common;
  		//$common->updateMemCache ( MC . $this->id_, $baseinfo);
  		$this->baseinfoChanged_ = false;
  		//$updateRoleWhere ['playersid'] = $this->id_;
  		//$common->updatetable ( 'player', $updatenow, $updateRoleWhere );
  	}
  	else {
  		$this->baseinfoChanged_ = true;
  	}
  }
  
  public function &GetItems() {
    if($this->items_) return $this->items_;
    $key = 'items_'.$this->id_;
    global $mc;
    $this->items_ = $mc->get(MC.$key);
    if($this->items_) return $this->items_;
		$this->items_ = array();
    
    global $db,$common;
    $resultItems = $db->query("SELECT * FROM ".$common->tname('player_items')." WHERE Playersid = ".$this->id_." order by ItemID asc,ID asc");
    while (($rows = $db->fetch_array($resultItems))!==false) {
      $this->items_[$rows['ID']] = $rows;
    }
    $mc->set(MC.$key, $this->items_, 0, 3600);
    return $this->items_;
  }
  
  public function &GetItem($id) {
    $this->GetItems();
    if(empty($this->items_[$id]))  {
    	$anull = null;
    	return $anull;
    }
    return $this->items_[$id];
  }
  
  public function AddItems($addarr, $delay=false, $arrdeletefirst=null, $mustdel=false, &$status=0, &$itemidsnew=null) {
    //需要添加的物品id不重复，否则会有bug
    if(!$this->GetBaseInfo())  return false;
    $this->GetItems();
    global $common,$mc;
    $items = $this->items_;
    $slotmax = $this->baseinfo_['bagpack'];
	// 最少需要一个空格
	$bagItems = $this->GetClientBag();
	if($slotmax - count($bagItems) < 1){
		$status = 2;
		return false;
	}
    $slotsadd = array();
    $slotsupdate = array();
    $slotsdelete = array();
    $newIDs = array();

    $olditemid = array();
    foreach($items as $id=>$item) {
      $olditemid[$item['ItemID']] = 1; 
    }
    $olditemid = array_keys($olditemid);

    if($arrdeletefirst) {
      foreach($arrdeletefirst as $itemid=>$itemnum) {
        $itemproto = ConfigLoader::GetItemProto($itemid);
        $itemsr = array_reverse($items, true);
        foreach($itemsr as $id=>$item) {
          if($item['IsEquipped']) continue;
          if($item['ItemID'] != $itemid)  continue;
          $numdelete = min($itemnum,$item['EquipCount'] );
          $item['EquipCount']  -= $numdelete;
          if($item['EquipCount']  <= 0) $slotsdelete[] = $id;
          else  $slotsupdate[$id] = $items[$id]['EquipCount'] = $item['EquipCount'];
          $itemnum -= $numdelete;
          if($itemnum<1)  break;
        }
        if($itemnum > 0 && $mustdel){
        	// 需要删除的物品不足
        	$status = 1;
        	return false; 
        }
      }	
      foreach($slotsdelete as $id) {
        unset($items[$id]);
      }
    }

    foreach($addarr as $itemid=>$itemnum) {
      $slotuse = count($slotsadd) ;
      $itemproto = ConfigLoader::GetItemProto($itemid);
      foreach($items as $id=>$item) {
        $itemproto2 = ConfigLoader::GetItemProto($item['ItemID']);
        if($item['IsEquipped'] || $itemproto2['ItemType'] == 5) continue;
        ++$slotuse ;
        if($item['ItemID'] != $itemid)  continue;
        if($item['EquipCount'] < $itemproto['DiejiaLimit']) {
          $numadd = min($itemnum, $itemproto['DiejiaLimit']-$item['EquipCount']);
          $item['EquipCount'] += $numadd;
          $slotsupdate[$id] = $items[$id]['EquipCount'] = $item['EquipCount'];  //
          $itemnum -= $numadd;
        }
        if($itemnum<1)  break;
      }
      if($itemnum>0) {
        $slotneed = $itemnum/$itemproto['DiejiaLimit'];
        if($slotmax - $slotuse < $slotneed) {
        	// 没有足够的背包空间
        	$status = 2;
        	return false;
        }
        for($i=0;$i<$slotneed;++$i) {
          $numadd = min($itemnum, $itemproto['DiejiaLimit']);
          $slotsadd[] = array('itemid'=>$itemid,'numadd'=>$numadd);
          $itemnum -= $numadd;
          $itemidsadd[$itemid] = 1;
        }
      }
    }
    
    $itemidsadd = array_keys($addarr);
    $itemidsnew = array_diff($itemidsadd, $olditemid);
    $this->idsnew_ = array_merge($this->idsnew_, $itemidsnew);
    
    $newIDs = $this->UpdateItems($slotsupdate,$slotsadd,$slotsdelete,$delay);

    return $newIDs;
  }
  
  public function DeleteItemByProto($deletearr, $delay=false, $mustdel=false) {
    $items = $this->GetItems();
    if(empty($items))  return false;
    global $common, $mc;
    $slotsdelete = array();
    $slotsupdate = array();
    $items = $this->items_;
    foreach($deletearr as $itemid=>$itemnum) {
      $itemproto = ConfigLoader::GetItemProto($itemid);
      $itemsr = array_reverse($items, true);
      foreach($itemsr as $id=>$item) {
        if($item['IsEquipped']) continue;
        if($item['ItemID'] != $itemid)  continue;
        $numdelete = min($itemnum,$item['EquipCount'] );
        $item['EquipCount']  -= $numdelete;
        if($item['EquipCount']  <= 0) $slotsdelete[] = $id;
        else  $slotsupdate[$id] = $item['EquipCount'];
        $itemnum -= $numdelete;
        if($itemnum<1)  break;
      }
      if($itemnum>0 && $mustdel)  return false;
    }
    
    $this->UpdateItems($slotsupdate,null,$slotsdelete,$delay);
    return true;
  }
  
  public function DeleteItemByInst($deletearr, $delay=false, $mustdel=false) {
    $items = $this->GetItems();
	if(empty($items))  return false;
    global $common;
    $slotsdelete = array();
    $slotsupdate = array();
    $items = $this->items_;
    foreach($deletearr as $id=>$itemnum) {
      if(empty($items[$id]))  continue;
      $item = $items[$id];

      $numdelete = min($itemnum,$item['EquipCount'] );
      $item['EquipCount']  -= $numdelete;
      if($item['EquipCount']  <= 0) $slotsdelete[] = $id;
      else  $slotsupdate[$id] = $item['EquipCount'];
      $itemnum -= $numdelete;
      if($itemnum>0 && $mustdel)  return false;
    }
    $this->UpdateItems($slotsupdate,null,$slotsdelete,$delay);
    return true;
  }
  
  public function UpdateItems($slotsupdate,$slotsadd,$slotsdelete,$delay=false) {
    global $mc, $common;
    $newIDs = array();
    if($slotsupdate) {
      foreach($slotsupdate as $id=>$num) {
		if($this->items_[$id]['EquipCount'] < $num) toolsModel::addItemBroadcast($this->id_, $this->items_[$id]['ItemID']);
        if(!$delay) {		  
          $whereArray['ID']          = $id;
          $updateArray['EquipCount'] = $num;
          $common->updatetable('player_items', $updateArray, $whereArray);
        }else{
          $this->slotsupdate_[$id]['EquipCount'] = $num;
        }
        $this->items_[$id]['EquipCount']  = $num;        
      }
    }
    if($slotsadd) {
      foreach($slotsadd as $slotadd) {
        $numadd = $slotadd['numadd'];
        $itemid = $slotadd['itemid'];
        $itemInfo = ConfigLoader::GetItemProto($itemid);
        $arrinsert = array('ItemID'=>$itemid,'IsEquipped' => 0,'Playersid'  => $this->id_,
          'SortNo' => 0,'EquipCount' => $numadd, 'QhLevel'=>0, 'luck'=>0);
        $newID = $common->inserttable('player_items', $arrinsert);
        $newIDs[] = $newID;
        $arrinsert['ID'] = $newID;
        $this->items_[$newID] = $arrinsert;
        toolsModel::addItemBroadcast($this->id_, $itemid);
      }
	}
    if($slotsdelete) {
      foreach($slotsdelete as $id) {
        if(!$delay) {
          $whereItem['ID'] = $id;
          $common->deletetable('player_items', $whereItem);
        }
        else{
          $this->slotsdelete_[] = $id;
        }
        unset($this->items_[$id]);
      }
    }
    if(!$delay) {
      $key = 'items_'.$this->id_;
      $mc->set(MC.$key, $this->items_, 0, 3600);
    }
    $this->itemdelayed_ = $delay;
	return $newIDs;
  }
  
  public function SaveItems() { //delay处理要主动调用
    if(!$this->itemdelayed_)  return;
  	global $common, $mc;
    foreach($this->slotsupdate_ as $id=>$updateArray) {
      $whereArray['ID']          = $id;
      $common->updatetable('player_items', $updateArray, $whereArray);
    }
    // $arrinsert = array();
    // foreach($this->slotsadd_ as $slotadd) {
      // $numadd = $slotadd['numadd'];
      // $itemid = $slotadd['itemid'];
      // $itemInfo = ConfigLoader::GetItem($itemid);
      // $arrinsert[] = array('ItemID'=>$itemid,'IsEquipped' => 0,'Playersid'  => $this->id_,
        // 'SortNo' => 0,'EquipCount' => $numadd);
      // //$this->items_[$newID] = $numadd;
    // }
    // if(!empty($arrinsert))  $common->insertMutilArray('player_items', $arrinsert);
    foreach($this->slotsdelete_ as $id) {
      $whereItem['ID'] = $id;
      $common->deletetable('player_items', $whereItem);
    }
    $key = 'items_'.$this->id_;
    $mc->set(MC.$key, $this->items_, 0, 3600);
    $this->itemdelayed_ = false;
  }
  
  public function GetZBSX($zbid) {
    $item = $this->GetItem($zbid);
    if(!$item)  return null;
    $itemproto = ConfigLoader::GetItemProto($item['ItemID']);
    if(empty($itemproto)) return null;
	
	$equipReinforceRule = getEquipReinforceRule();
	$equipReinforceRule = $equipReinforceRule[$item['QhLevel']];			
	$qhValue = $equipReinforceRule['effect'];	
	$except_array = array(3=>10, 6=>20, 9=>30, 12=>40, 15=>50, 18=>60, 21=>70);		
	$zs_except_array = array(49000=>1, 49001=>1, 49002=>1, 49003=>1, 49004=>1, 41001=>1, 42001=>1, 43001=>1, 44001=>1, 41002=>1, 42002=>1, 43002=>1, 44002=>1);	// 专属武器和圣诞套装 新年套装
	$show_raw_value = false;
	if(array_key_exists($item['QhLevel'], $except_array)) {
		if($except_array[$item['QhLevel']] == $itemproto['LevelLimit']) 
			$show_raw_value = true;
	}
	if(array_key_exists($item['ItemID'], $zs_except_array)) $show_raw_value = true;
	$Physical_value = $Agility_value = $Defense_value = $Attack_value = 0;
	if($itemproto['EquipType'] == 1) {
		$Physical_value = $show_raw_value ? intval($itemproto['Physical_value']) : $qhValue;			
	} else if($itemproto['EquipType'] == 2) {
		$Defense_value = $show_raw_value ? intval($itemproto['Defense_value']) : $qhValue;
	} else if($itemproto['EquipType'] == 3) {
		$Attack_value = $show_raw_value ? intval($itemproto['Attack_value']) : $qhValue;	
	} else if($itemproto['EquipType'] == 4) {
		$Agility_value  = $show_raw_value ? intval($itemproto['Agility_value']) : $qhValue;
	}
	

    return array('gj'=>$Attack_value,'fy'=>$Defense_value,'tl'=>$Physical_value,'mj'=>$Agility_value,'mztl'=>$itemproto['Addition_agility_value'],'shts'=>$itemproto['Addition_attack_value'],'shjs'=>$itemproto['Addition_defense_value'],'smts'=>$itemproto['Addition_physical_value']);
  }
  
  //返回给客户端的数据
  public function GetClientBag($sendfull=false) {
	global $mc;

	// 资源点获取的道具返回完整背包数据
	$xsbb = $mc->get(MC.$_SESSION['playersid'].'_xsbb');
	if ($xsbb == 1) {
		$sendfull = true;
		$mc->delete(MC.$_SESSION['playersid'].'_xsbb');
	}
	
    $items = $this->GetItems();
	if(empty($items))  return array();
    // 内存排序
    $playerItmes = $this->items_;
    foreach ($playerItmes as $key=>$value) {
      $ItemID[] = $value['ItemID'];
      $bgId[] = $value['ID'];
    }
    array_multisort($ItemID, $bgId, $playerItmes);

    $array = array();
	$unlock_array = array();
    for ($i = 0, $l=count($playerItmes); $i < $l; ++$i) {	  
      $item = $playerItmes[$i];
      if($item['IsEquipped']) continue;
      $packfullinfo = $sendfull || in_array($item['ItemID'], $this->idsnew_);
      toolsModel::PackItem2Array($this->id_, $item, $array, $packfullinfo);
	  
	  $array_tmp = array();
	  toolsModel::PackItem2Array($this->id_, $item, $array_tmp, true);
	  if(isset($array_tmp[$item['ID']]['lx']) && isset($array_tmp[$item['ID']]['st'])) {
		  if($array_tmp[$item['ID']]['lx'] == 2 && $array_tmp[$item['ID']]['st'] == 87) { // 闯关解锁道具添加到背包最前
			/*$unlock_array[$item['ID']] = $array[$item['ID']];
			unset($array[$item['ID']]);
			array_unshift($array, $unlock_array[$item['ID']]);*/
			$firstID = $item['ID'];
			$firstData = $array[$item['ID']];		  	
		  }
	  }
    }
  	if (isset($firstID)) {
		unset($array[$firstID]);
		array_unshift($array, $firstData);
	}    
    //unset($unlock_array);
    $array = array_values($array);
    return $array;
  }
  
  public function ModifyItem($id,$attrs,$delay = false) {
    global $mc, $common;	
    $items = &$this->GetItems();
    if(!isset($items[$id]))	return false;
    $item = &$items[$id];
    foreach($attrs as $attr=>$value) {
      if($delay)  $this->slotsupdate_[$id][$attr] = $value;
      if($attr == 'ItemID') { //如果改了模板id，检测下是否为新道具
        $olditemid = array();
        foreach($items as $id2=>$item2) {
          $olditemid[$item2['ItemID']] = 1; 
        }
        $olditemid = array_keys($olditemid);
        if(!in_array($value,$olditemid))
          $this->idsnew_[] = $value;
      }
      $item[$attr] = $value;
    }
    if(!$delay) {
      $whereArray['ID']          = $id;
      $common->updatetable('player_items', $attrs, $whereArray);
      
      $key = 'items_'.$this->id_;
	  if(isset($attrs['QhLevel'])) {
		  if(($this->items_[$id]['ItemID'] == $attrs['ItemID']) && ($this->items_[$id]['QhLevel'] != $attrs['QhLevel']) && $attrs['QhLevel'] > 0) 
			$this->items_[$id]['QhLevel'] = $attrs['QhLevel'];		
	  }
      $mc->set(MC.$key, $this->items_, 0, 3600);
    }
    return true;
  }
  
  public function GetBagBlocksMax() {
    if(!$this->GetBaseInfo())  return 0;
    return intval($this->baseinfo_['bagpack']);
  }
  
  public function &GetGenerals() {
    if($this->generals_) return $this->generals_;
    return null;
  }
  
  //1好友关系，4占领领地，3仇人关系
  public function &GetFriends() {
    if($this->friends_) return $this->friends_;
    //MC . $playersid . '_enemys' : MC . $playersid . '_occupys');
    $memKey = MC . $this->id_ . '_friends';
    $type = 1;
    
    global $mc;
    $this->friends_ = $mc->get($memKey);
    if($this->friends_)   return $this->friends_;
    $this->friends_ = roleModel::getSocialPlayersInfo($this->id_,$type);
    $mc->set ( $memKey, $this->friends_, 0, 3600 );
    return $this->friends_;
  }
  
  public function &GetEnemys() {
    if($this->enemys_)  return $this->enemys_; 
    $memKey = MC . $this->id_ . '_enemys';
    $type = 3;
    
    global $mc;
    $this->enemys_ = $mc->get($memKey);
    if($this->enemys_)   return $this->enemys_;
    $this->enemys_ = roleModel::getSocialPlayersInfo($this->id_,$type);
    $mc->set ( $memKey, $this->enemys_, 0, 3600 );
    return $this->enemys_;
  }
  
  public function GetOccupys() {
    //if($this->occupys_) return $this->occupys_;
    //目前占领列表数据是读高政平的数据，没有添加memcache
    fightModel::ldsl($this->id_, $ldplayers);
    $ids = array();
    foreach($ldplayers as $pid){
      $ids[$pid] = 0;
    }
    return $ids;
  }
  
  public function &GetContext() {
    if($this->context_) return $this->context_;
    global $mc;
    $this->context_ = $mc->get(MC.$this->id_.'_context');
    if($this->context_ === false) $this->context_=array();
    return  $this->context_;
  } 
  
  public function PrepareSendContext($pid,$versionlast=0) {
    $context =$this->context_;
    if(empty($context)) return null;
    $contextold = $context['oldversion'];
    $oldversion = $contextold['version'] ;
    $newversion = $oldversion+1;
    unset($context['oldversion']);
    $returncontext = $context;  //先返回新的
    
    if($versionlast != $oldversion) { //客户端有数据没收到
      $returncontext['olddata'] = $contextold['data'];  //补发旧版本
      $this->MergeContext($context,$contextold['data']); //旧版本合并到新版本
    }
    $this->context_ = array();  //重置数据，清空新版本
    if(!empty($context)) {
      $this->context_['oldversion']['version'] = $newversion; //  将最新版本存为旧版本
      $this->context_['oldversion']['data'] = $context; //  将最新版本存为旧版本
      $returncontext['v'] = $newversion;  // 返给客户端新的版本号
    }

    $this->contextchanged_ = true;
    return $returncontext;
  }
  
  public function SetContext($key,$value)  {
    //修改时需同步更改MergeContext方法
    /*keys
      jl:军粮
      hy_add
      hy_del
      hy_stat
      cr_add
      cr_del
      bzl:被占领
      msgnew:
    */
    $context = &$this->GetContext();
    switch($key) {
      case 'msgnew':  //($pid,'msgnew',array('sys'=>3))
        foreach($value as $group=>$num)
          $context['msgnew'][$group] = $num==-1?(isset($context['msgnew'][$group])?$context['msgnew'][$group]+1:1) :$num;
        break;
      case 'hy_add':  //($pid,'hy_add',1234)
        $context['hy_change'][$value] = 'add';
        break;
      case 'hy_del':  //($pid,'hy_del',1234)
        $context['hy_change'][$value] = 'del';
        break;
      case 'hy_stat': //($pid,'hy_stat',array('123'=>array('dj'=>1,'jj'=>0)));  //好友123的可打劫=1,可解救=0
        foreach($value as $pid=>$stats) {
          foreach($stats as $statkey=>$stat)
            $context['hy_stat'][$pid][$statkey] = $stat;
        }
        break;
      default:  //($pid,'jl',108),etc.
        $context[$key] = $value;
    }
    $this->contextchanged_ = true;
  }
  
   private function MergeContext(&$contextnew,$contextold){
    // 重要函数！！修改时需和SetContext匹配
    foreach($contextold as $key=>$olddata) {
      switch($key) {
        case 'msgnew': 
          foreach($olddata as $group=>$num) {
            if(isset($contextnew['msgnew'][$group]))  continue; //跳过新context已有的消息状态
            $contextnew['msgnew'][$group] = $num;
          }
          break;
        case 'hy_change':
          foreach($olddata as $pid=>$addordel) {
            if(isset($contextnew['hy_change'][$pid]))  continue;  //跳过新context已有的好友增删
            $contextnew['hy_change'][$pid] = $addordel;
          }
          break;
        case 'hy_stat': 
          foreach($olddata as $pid=>$stats) {
            if(!isset($contextnew['hy_stat'][$pid]))  { //没有好友状态。全部添加
              $contextnew['hy_stat'][$pid] = $stats;
              continue;
            }
            //有好友状态
            foreach($stats as $statkey=>$stat) {
              if(isset($contextnew['hy_stat'][$pid][$statkey]))  continue;  //跳过新context已有的状态
              $contextnew['hy_stat'][$pid][$statkey] = $stat;
            }
          }
          break;
        default: 
          if(isset($contextnew[$key]))  continue; //不覆盖新context的属性
          $contextnew[$key] = $value;
      }
    }
  }
  
  public function SaveContext() {
    if(!$this->contextchanged_)   return;
    global $mc; 
    $mc->set(MC.$this->id_.'_context', $this->context_);
    $this->contextchanged_=false;
  }
  
  private $id_;
  public $baseinfo_;
  private $baseinfoChanged_;
  
  private $items_;
  private $slotsdelete_;
  private $slotsupdate_;
  private $slotsadd_;
  private $idsnew_;
  
  private $generals_;
  private $enemys_;
  public $friends_;
  private $occupys_;
  
  public $itemdelayed_;
  public $context_;
  public $contextchanged_;
}
?>
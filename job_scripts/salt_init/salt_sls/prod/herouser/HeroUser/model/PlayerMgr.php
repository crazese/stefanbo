<?php
require_once(S_ROOT.'model'.DIRECTORY_SEPARATOR.'Player.php');
class PlayerMgr {
  public function __construct($db,$mc){
    $this->db_ = $db;
    $this->mc_ = $mc;
    $this->players_ = array();
    $this->contextchangedPlayerids_ = array();    
  }
  
  public function GetPlayer($pid, $withbaseinfo=true) {
    if(!empty($this->players_[$pid])) return $this->players_[$pid];
    
    $player = new Player($pid);
    if($withbaseinfo && !$player->GetBaseInfo())  return null;

    $this->players_[$pid] = $player;
    return $player;
  }
  
  public function removeplayer($playersid) {
  	unset($this->players_[$playersid]);
  }
  /**
   * Enter description here...
   *
   * @param int $pids
   * @return Player
   */
  public function GetPlayers($pids) {
    $returnarr = array();
    $memgetkeys = array();
    foreach($pids as $pid) {
      if(!empty($this->players_[$pid])) 
        $returnarr[$pid] = $this->players_[$pid];
      else
        $memgetkeys[]=MC . $pid;
    }
    
    if(empty($memgetkeys))  return $returnarr;

    $memgeted = $this->mc_->getMulti($memgetkeys,$cas);
    if(!is_array($cas)) $cas = array();
    $memnotfoundkey = array_diff($memgetkeys, array_keys($cas));
    
    foreach($memgeted as $pinfo) {
      $pid = $pinfo['playersid'];
      $player = new Player($pid,$pinfo);
      $this->players_[$pid] = $player;
      $returnarr[$pid] = $player;
    }
    
    if (! empty ( $memnotfoundkey )) {
      global $common;
      $ids = array();
      foreach($memnotfoundkey as $key) {
        $pid = substr($key,strlen(MC));
        $ids[] = $pid;
      }
      $inids = implode (',',$ids);
      $sql = "select * from " . $common->tname ( 'player' ) . " where playersid in ({$inids})";
      $result = $this->db_->query ( $sql );

      while ( ($roleInfo = $this->db_->fetch_array ( $result )) !== false ) {
        $pid = $roleInfo ['playersid'];
        //$roleInfo = roleModel::setRoleVip($roleInfo);
        $this->mc_->set ( MC . $pid, $roleInfo, 0, 3600);
        $player = new Player($pid,$roleInfo);
        $this->players_[$pid] = $player;
        $returnarr[$pid] = $player;
      }
      $this->db_->free_result ( $result );
    }

    return $returnarr;
  }
  
  public function SetContext($pid,$key,$value) {
    /*详细参数说明在Player::SetContext*/
    $player = $this->GetPlayer($pid,false);
    $player->SetContext($key,$value);
    $this->contextchangedPlayerids_[$pid] = 1;
  }
  
  public function &GetContext($pid) {
    $player = $this->GetPlayer($pid,false);
    return $player->GetContext();
  }
  
  public function PrepareSendContext($pid) {
    $player = $this->GetPlayer($pid,false);
    return $player->PrepareSendContext($pid);
  }

  public function DeleteOldContext($pid) {
    $player = $this->GetPlayer($pid,false);
    return $player->DeleteOldContext($pid);
  }
  
  public function SaveAll() {
    foreach($this->players_ as $player) {
      $player->SaveItems();
      $player->SaveContext();
    }
  }
  
  private $players_;
  private $db_;
  private $mc_;
  public $dbcommit;
}
?>
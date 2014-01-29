<?php
// 判断要使用的语言文件

$lang_path = '/lang_' . LANG_FLAG;
$configs_path = LANG_FLAG . '/';

if(!defined('S_ROOT')) {
        define('S_ROOT', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
}
include(S_ROOT.$lang_path.DIRECTORY_SEPARATOR.'lang_lang.php');
include(S_ROOT.$lang_path.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'alipay'.DIRECTORY_SEPARATOR.'lang.php');
include(S_ROOT.$lang_path.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'alipay_self'.DIRECTORY_SEPARATOR.'lang.php');
include(S_ROOT.$lang_path.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'hero_chongzhi'.DIRECTORY_SEPARATOR.'lang.php');
include(S_ROOT.$lang_path.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'hero_city'.DIRECTORY_SEPARATOR.'lang.php');
include(S_ROOT.$lang_path.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'hero_dalei'.DIRECTORY_SEPARATOR.'lang.php');
include(S_ROOT.$lang_path.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'hero_fight'.DIRECTORY_SEPARATOR.'lang.php');
include(S_ROOT.$lang_path.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'hero_hd'.DIRECTORY_SEPARATOR.'lang.php');
include(S_ROOT.$lang_path.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'hero_letters'.DIRECTORY_SEPARATOR.'lang.php');
include(S_ROOT.$lang_path.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'hero_quests'.DIRECTORY_SEPARATOR.'lang.php');
include(S_ROOT.$lang_path.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'hero_rank'.DIRECTORY_SEPARATOR.'lang.php');
include(S_ROOT.$lang_path.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'hero_role'.DIRECTORY_SEPARATOR.'lang.php');
include(S_ROOT.$lang_path.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'hero_social'.DIRECTORY_SEPARATOR.'lang.php');
include(S_ROOT.$lang_path.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'hero_tools'.DIRECTORY_SEPARATOR.'lang.php');
include(S_ROOT.$lang_path.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'hero_user'.DIRECTORY_SEPARATOR.'lang.php');
include(S_ROOT.$lang_path.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'hero_zyd'.DIRECTORY_SEPARATOR.'lang.php');
include(S_ROOT.$lang_path.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'hero_achievements'.DIRECTORY_SEPARATOR.'lang.php');
include(S_ROOT.$lang_path.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'tenpay'.DIRECTORY_SEPARATOR.'lang.php');

class ConfigLoader {
  public static function GetConfig(&$globalval, $file) {
	global $configs_path;
	if(!$globalval) {
		if('G_Items' == $file || 'G_Quests' == $file) {
			require(S_ROOT.'configs/'.$configs_path.$file.'.php');
		} else {
			require(S_ROOT.'configs/'.$file.'.php');
		}
	}
  }

  public static function GetItemProto($id) {
    global $G_Items;
    //--以后可以对id分段或hash。一次只加载单个小文件
    ConfigLoader::GetConfig($G_Items,'G_Items');
    if(isset($G_Items[$id]))  return $G_Items[$id];
    return null;
  }

  public static function GetQuestProto($id) {
    global $G_Quests;
    ConfigLoader::GetConfig($G_Quests,'G_Quests');
    if(isset($G_Quests[$id]))  return $G_Quests[$id];
    return null;
  }
  
  public static function GetAllQuests() {
  	global $G_Quests;
    ConfigLoader::GetConfig($G_Quests,'G_Quests');
    return $G_Quests;	
  }

  public static function GetItemOdd($id){
    global $G_ItemOdds;
    ConfigLoader::GetConfig($G_ItemOdds,'G_ItemOdds');
    if(isset($G_ItemOdds[$id]))  return $G_ItemOdds[$id];
    return null;
  }

  public static function GetStageOdd($difficulty, $stage,$substage) {
    global $G_StageCardOdds;
    ConfigLoader::GetConfig($G_StageCardOdds,'G_StageCardOdds');
    $key = $difficulty.'_'.$stage.'_'.$substage;
    if(isset($G_StageCardOdds[$key]))  return $G_StageCardOdds[$key];
    return null;
  }

  public static function GetCombinRule($equipType){
	  global $G_CombinRule;
	  ConfigLoader::GetConfig($G_CombinRule, 'G_CombinRule');
	  if(isset($G_CombinRule[$equipType])){
		  return $G_CombinRule[$equipType];
	  }else{
		  return null;
	  }
  }

  // 道具使用配置
  public static function GetUseItemCfg($itemId){
	  global $G_Use_Item_Cfg;

	  ConfigLoader::GetConfig($G_Use_Item_Cfg, 'G_Use_Item_Cfg');
	  if(isset($G_Use_Item_Cfg[$itemId])){
		  return $G_Use_Item_Cfg[$itemId];
	  }else{
		  return null;
	  }
  }
}

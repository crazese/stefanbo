<?php 
$GLOBALS['zbslots'] = array('helmet','carapace','arms','shoes');
class actModel {
  //获取技能触发几率
  public static function getJnJl($jnid,$jn_level) {
    if ($jnid == 0 || $jn_level > 8) {
      return 0;
    }
    static $jnjl=null;
    if(!$jnjl)$jnjl = array(
        1=>array(1=>5,2=>7,3=>9,4=>11,5=>13,6=>15,7=>17,8=>19),    //神来一击
        2=>array(1=>5,2=>7,3=>9,4=>11,5=>13,6=>15,7=>17,8=>19),    //左右开弓
        3=>array(1=>5,2=>6,3=>7,4=>8,5=>9,6=>10,7=>10,8=>10),      //玄阴七伤
        4=>array(1=>5,2=>6,3=>7,4=>8,5=>9,6=>10,7=>11,8=>12),      //钩挂连环
        5=>array(1=>5,2=>6,3=>7,4=>8,5=>9,6=>9,7=>10,8=>11),       //十面绞杀
        6=>array(1=>5,2=>6,3=>7,4=>8,5=>9,6=>10,7=>11,8=>12),      //迫在眉睫
        7=>array(1=>5,2=>6,3=>7,4=>8,5=>9,6=>10,7=>11,8=>12),      //连环闪电
        8=>array(1=>5,2=>8,3=>11,4=>14,5=>17,6=>20,7=>23,8=>26),   //金钟之罩  
        9=>array(1=>5,2=>8,3=>11,4=>14,5=>17,6=>20,7=>23,8=>26),   //护佑全军
        10=>array(1=>5,2=>8,3=>11,4=>14,5=>17,6=>20,7=>23,8=>26),  //战神
        11=>array(1=>5,2=>8,3=>11,4=>14,5=>17,6=>20,7=>23,8=>26),  //战鼓震天
        12=>array(1=>2,2=>4,3=>6,4=>8,5=>10,6=>12,7=>14,8=>16),    //疑兵四伏 
        13=>array(1=>5,2=>7,3=>9,4=>11,5=>13,6=>15,7=>17,8=>19),   //夺魂一击
        14=>array(1=>5,2=>7,3=>9,4=>11,5=>13,6=>15,7=>17,8=>19),   //妙手回春 
        15=>array(1=>10,2=>11,3=>12,4=>13,5=>14,6=>15,7=>16,8=>17),//雨露春风 
        16=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0),        //偷天换日
        17=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0)         //烈火焚身
    );
      return $jnjl[$jnid][$jn_level];    
  }
  
  //获取技能触发几率
  public static function getJnJl2($jnid,$jn_level) {
    if ($jnid == 0 || $jn_level > 7) {
      return 0;
    }
    static $jnjl=null;
    if(!$jnjl)$jnjl = array(
        1=>array(1=>45,2=>7,3=>9,4=>11,5=>13,6=>15,7=>17),  //神来一击
        2=>array(1=>45,2=>7,3=>9,4=>11,5=>13,6=>15,7=>17),  //左右开弓
        3=>array(1=>45,2=>6,3=>7,4=>8,5=>9,6=>10,7=>10),    //玄阴七伤
        4=>array(1=>45,2=>6,3=>7,4=>8,5=>9,6=>10,7=>11),    //钩挂连环
        5=>array(1=>45,2=>6,3=>7,4=>8,5=>9,6=>9,7=>10),     //十面绞杀
        6=>array(1=>45,2=>6,3=>7,4=>8,5=>9,6=>10,7=>11),    //迫在眉睫
        7=>array(1=>45,2=>6,3=>7,4=>8,5=>9,6=>10,7=>11),    //连环闪电
        8=>array(1=>45,2=>8,3=>11,4=>14,5=>17,6=>20,7=>23), //金钟之罩  
        9=>array(1=>45,2=>8,3=>11,4=>14,5=>17,6=>20,7=>23), //护佑全军
        10=>array(1=>45,2=>8,3=>11,4=>14,5=>17,6=>20,7=>23),//战神
        11=>array(1=>45,2=>8,3=>11,4=>14,5=>17,6=>20,7=>23),//战鼓震天
        12=>array(1=>45,2=>4,3=>6,4=>8,5=>10,6=>12,7=>14),  //疑兵四伏 
        13=>array(1=>45,2=>7,3=>9,4=>11,5=>13,6=>15,7=>17), //夺魂一击
        14=>array(1=>45,2=>7,3=>9,4=>11,5=>13,6=>15,7=>17), //妙手回春 
        15=>array(1=>45,2=>11,3=>12,4=>13,5=>14,6=>15,7=>16),//雨露春风 
        16=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0),//偷天换日
        17=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0) //烈火焚身       
    );
      return $jnjl[$jnid][$jn_level];    
  }  
  
  //获取4级时技能触发几率
  public static function getJnJl4($jnid,$jn_level) {
    if ($jnid == 0 || $jn_level > 7) {
      return 0;
    }
    static $jnjl=null;
    if(!$jnjl)$jnjl = array(
        1=>array(1=>1,2=>7,3=>9,4=>11,5=>13,6=>15,7=>17),  //神来一击
        2=>array(1=>1,2=>7,3=>9,4=>11,5=>13,6=>15,7=>17),  //左右开弓
        3=>array(1=>1,2=>6,3=>7,4=>8,5=>9,6=>10,7=>10),    //玄阴七伤
        4=>array(1=>1,2=>6,3=>7,4=>8,5=>9,6=>10,7=>11),    //钩挂连环
        5=>array(1=>1,2=>6,3=>7,4=>8,5=>9,6=>9,7=>10),     //十面绞杀
        6=>array(1=>1,2=>6,3=>7,4=>8,5=>9,6=>10,7=>11),    //迫在眉睫
        7=>array(1=>1,2=>6,3=>7,4=>8,5=>9,6=>10,7=>11),    //连环闪电
        8=>array(1=>1,2=>8,3=>11,4=>14,5=>17,6=>20,7=>23), //金钟之罩  
        9=>array(1=>1,2=>8,3=>11,4=>14,5=>17,6=>20,7=>23), //护佑全军
        10=>array(1=>1,2=>8,3=>11,4=>14,5=>17,6=>20,7=>23),//战神
        11=>array(1=>1,2=>8,3=>11,4=>14,5=>17,6=>20,7=>23),//战鼓震天
        12=>array(1=>1,2=>4,3=>6,4=>8,5=>10,6=>12,7=>14),  //疑兵四伏 
        13=>array(1=>1,2=>7,3=>9,4=>11,5=>13,6=>15,7=>17), //夺魂一击
        14=>array(1=>1,2=>7,3=>9,4=>11,5=>13,6=>15,7=>17), //妙手回春 
        15=>array(1=>1,2=>11,3=>12,4=>13,5=>14,6=>15,7=>16),//雨露春风 
        16=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0),//偷天换日
        17=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0) //烈火焚身        
    );
      return $jnjl[$jnid][$jn_level];    
  }
  //获取6级时技能触发几率
  public static function getJnJl7($jnid,$jn_level) {
    if ($jnid == 0 || $jn_level > 7) {
      return 0;
    }
    static $jnjl=null;
    if(!$jnjl)$jnjl = array(
        1=>array(1=>35,2=>7,3=>9,4=>11,5=>13,6=>15,7=>17),  //神来一击
        2=>array(1=>35,2=>7,3=>9,4=>11,5=>13,6=>15,7=>17),  //左右开弓
        3=>array(1=>35,2=>6,3=>7,4=>8,5=>9,6=>10,7=>10),    //玄阴七伤
        4=>array(1=>35,2=>6,3=>7,4=>8,5=>9,6=>10,7=>11),    //钩挂连环
        5=>array(1=>35,2=>6,3=>7,4=>8,5=>9,6=>9,7=>10),     //十面绞杀
        6=>array(1=>35,2=>6,3=>7,4=>8,5=>9,6=>10,7=>11),    //迫在眉睫
        7=>array(1=>35,2=>6,3=>7,4=>8,5=>9,6=>10,7=>11),    //连环闪电
        8=>array(1=>35,2=>8,3=>11,4=>14,5=>17,6=>20,7=>23), //金钟之罩  
        9=>array(1=>35,2=>8,3=>11,4=>14,5=>17,6=>20,7=>23), //护佑全军
        10=>array(1=>35,2=>8,3=>11,4=>14,5=>17,6=>20,7=>23),//战神
        11=>array(1=>35,2=>8,3=>11,4=>14,5=>17,6=>20,7=>23),//战鼓震天
        12=>array(1=>35,2=>4,3=>6,4=>8,5=>10,6=>12,7=>14),  //疑兵四伏 
        13=>array(1=>35,2=>7,3=>9,4=>11,5=>13,6=>15,7=>17), //夺魂一击
        14=>array(1=>35,2=>7,3=>9,4=>11,5=>13,6=>15,7=>17), //妙手回春 
        15=>array(1=>35,2=>11,3=>12,4=>13,5=>14,6=>15,7=>16),//雨露春风 
        16=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0),//偷天换日
        17=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0) //烈火焚身        
    );
      return $jnjl[$jnid][$jn_level];    
  }
  
  //获取7级时技能触发几率
  public static function getJnJl6($jnid,$jn_level) {
    if ($jnid == 0 || $jn_level > 7) {
      return 0;
    }
    static $jnjl=null;
    if(!$jnjl)$jnjl = array(
        1=>array(1=>25,2=>7,3=>9,4=>11,5=>13,6=>15,7=>17),  //神来一击
        2=>array(1=>25,2=>7,3=>9,4=>11,5=>13,6=>15,7=>17),  //左右开弓
        3=>array(1=>25,2=>6,3=>7,4=>8,5=>9,6=>10,7=>10),    //玄阴七伤
        4=>array(1=>25,2=>6,3=>7,4=>8,5=>9,6=>10,7=>11),    //钩挂连环
        5=>array(1=>25,2=>6,3=>7,4=>8,5=>9,6=>9,7=>10),     //十面绞杀
        6=>array(1=>25,2=>6,3=>7,4=>8,5=>9,6=>10,7=>11),    //迫在眉睫
        7=>array(1=>25,2=>6,3=>7,4=>8,5=>9,6=>10,7=>11),    //连环闪电
        8=>array(1=>25,2=>8,3=>11,4=>14,5=>17,6=>20,7=>23), //金钟之罩  
        9=>array(1=>25,2=>8,3=>11,4=>14,5=>17,6=>20,7=>23), //护佑全军
        10=>array(1=>25,2=>8,3=>11,4=>14,5=>17,6=>20,7=>23),//战神
        11=>array(1=>25,2=>8,3=>11,4=>14,5=>17,6=>20,7=>23),//战鼓震天
        12=>array(1=>25,2=>4,3=>6,4=>8,5=>10,6=>12,7=>14),  //疑兵四伏 
        13=>array(1=>25,2=>7,3=>9,4=>11,5=>13,6=>15,7=>17), //夺魂一击
        14=>array(1=>25,2=>7,3=>9,4=>11,5=>13,6=>15,7=>17), //妙手回春 
        15=>array(1=>25,2=>11,3=>12,4=>13,5=>14,6=>15,7=>16),//雨露春风 
        16=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0),//偷天换日
        17=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0) //烈火焚身        
    );
      return $jnjl[$jnid][$jn_level];    
  }
  
  //获取武将攻击距离
  public static function getGjjl($bzid) {
    switch ($bzid) {
      case 1:
        $jl = 2;
      break;
      case 2:
        $jl = 3;
      break;
      case 3:
        $jl = 12;
      break;
      case 4:
        $jl = 1;
      break;
      case 5:
        $jl = 23;
      break;
      default:
        $jl = 0;
      break;
    }
    return $jl;
  }    
  //获取兵种移动速度值，$arm兵种
  public static function getMoveSpeedValue($arm) {
    switch ($arm) {
      case 1:
        $move_speed_value = 5;    //重甲
        break;
      case 2:
        $move_speed_value = 7;    //长枪
        break;
      case 3:
        $move_speed_value = 3;    //弓箭
        break;
      case 4:
        $move_speed_value = 9;    //轻骑
        break;
      case 5:
        $move_speed_value = 0;    //连弩
        break;  
      default:        
        $move_speed_value = 0;    //无移动值
        break;
    }
    return $move_speed_value;
  }  

  //获取兵种机动力值，$leadertype将领职业、$level将领级别、$forcelv内力等级
  public static function getMobility($leadertype,$level) {
    //机动力公式
    //initmobi[leadertype]+lv-1+5*forcelv
    switch ($leadertype) {
      case 1:
        $base_value = 25;   //重甲
        break;
      case 2:
        $base_value = 28;   //长枪
        break;
      case 3:
        $base_value = 18;   //弓箭
        break;
      case 4:
        $base_value = 30;   //轻骑
        break;
      case 5:
        $base_value = 15;    //连弩
        break;  
      default:
        $base_value = 0;   //无机动力值
        break;
    }
    if ($base_value != 0) {
      $priority_value = $base_value + $level-1 + 5;
    } else {
      $priority_value = 0;
    }  
    return $priority_value;
  }  

  //战斗$attack_data(攻击方 数据)$defend_data（防守方玩家数据）$isGw（是否为打怪战）$gjcl（攻方策略）$fscl（防守策略）$isDalei（是否打擂战）$gf_jwdj(攻方爵位等级)$sf_jwdj（守方爵位等级）
  public static function fight($attack_data,$defend_data,$isGw = 0,$gjcl = 0,$fscl = 0,$isDalei = 0,$gf_jwdj = 1,$sf_jwdj = 1,$is_ql=0) {
    global $G_PlayerMgr,$zbslots;
    //print_r($defend_data);
    $xk = actModel::clxk($gjcl,$fscl);
    if ($xk == 0) {
      $gj = 1;
      $sm = 1;
    } elseif ($xk == 2) {
      $gj = 1;
      $sm = 1.15;
    } else {
      $gj = 1.15;
      $sm = 1;
    }
	
	// 如果是强掠攻击力增加100%
	if($is_ql == 1) {
		$gj += 1;		
	}
	
    $gf_jwInfo = jwmc($gf_jwdj);
    $gf_jwjc = 1 + $gf_jwInfo['jc'] / 100;
    for ($i = 0; $i < count($attack_data); $i++) {
      $attackplayer = $G_PlayerMgr->GetPlayer($attack_data[$i]['playerid']);
      //$attack_arm = $attack_data[$i]['arm'];                                                           //兵种
      //$attack_arm_level = $attack_data[$i]['arm_level'];                                               //兵阶  
      $attack_dj = $attack_data[$i]['general_level'];                              //将领等级
      $attack_zy = $attack_data[$i]['professional'];                               //职业    
      //$forcelv  = $attack_data[$i]['professional_level'];                          //内力    
      $zbgjjcArray = array();
      $zbfyjcArray = array();
      $zbtljcArray = array();
      $zbmjjcArray = array();
      foreach ($zbslots as $slot) {
        $zbid = $attack_data[$i][$slot];  
        if ($zbid != 0) {
          $zbInfo = $attackplayer->GetZBSX($zbid);
          $zbgjjcArray[] = $zbInfo['gj'];
          $zbfyjcArray[] = $zbInfo['fy'];
          $zbtljcArray[] = $zbInfo['tl'];
          $zbmjjcArray[] = $zbInfo['mj'];
        }
      }   
      $zbgjjc = array_sum($zbgjjcArray);
      $zbfyjc = array_sum($zbfyjcArray);
      $zbtljc = array_sum($zbtljcArray);
      $zbmjjc = array_sum($zbmjjcArray);
      $sxxs = genModel::sxxs($attack_zy);
      $atlz = genModel::hqwjsx($attack_dj,$attack_data[$i] ['understanding_value'],$attack_data[$i] ['professional_level'],$attack_data[$i] ['llcs'],$gf_jwjc,$zbtljc,$sxxs ['tl'],$attack_data[$i] ['py_tl']);      
      if ($isDalei == 1) {
        $asmz = round($atlz,0) * 10;
      } else {
        $asmz = $attack_data[$i]['general_life'];
        $asmsx = round($atlz,0) * 10;
        if ($asmz > $asmsx) {
          $asmz = $asmsx;
        }
      }
      $dataArray[] = array(
      'id'=>$attack_data[$i]['intID'],
      'general_name'=>$attack_data[$i]['general_name'],
      'priority'=>actModel::getMobility($attack_zy,$attack_dj),  // 机动力
      'sort_id'=>$attack_data[$i]['general_sort'],                               //排序
      'role'=>1,                                                                 //攻击方标识
      'army_type'=>$attack_data[$i]['professional'],                                                  //兵种
      
      'command_soldier'=>$asmz,
      'attack_value'=>(genModel::hqwjsx($attack_dj,$attack_data[$i] ['understanding_value'],$attack_data[$i] ['professional_level'],$attack_data[$i] ['llcs'],$gf_jwjc,$zbgjjc,$sxxs ['gj'],$attack_data[$i] ['py_gj'])) * $gj,  //攻击值
      'defense_value'=>genModel::hqwjsx($attack_dj,$attack_data[$i] ['understanding_value'],$attack_data[$i] ['professional_level'],$attack_data[$i] ['llcs'],$gf_jwjc,$zbfyjc,$sxxs ['fy'],$attack_data[$i] ['py_fy']),  //防御值      
      'physical_value'=>$atlz,                      //生命值
      'agility_value'=>genModel::hqwjsx($attack_dj,$attack_data[$i] ['understanding_value'],$attack_data[$i] ['professional_level'],$attack_data[$i] ['llcs'],$gf_jwjc,$zbmjjc,$sxxs ['mj'],$attack_data[$i] ['py_mj']),  //防御值      
      'understanding_value'=>$attack_data[$i]['understanding_value'],            //悟性
      'attack_move_speed_soldier'=>actModel::getMoveSpeedValue($attack_zy),                //士兵移动速度
      'general_level'=>$attack_data[$i]['general_level'],                                    //将领级别  
      'professional'=>$attack_data[$i]['professional'],                                      //将领职业
      'distance'=>0,                                                                         //默认当前格数，默认为0  
      'animation'=>0,                                                                        //攻击或防御特技
      'loss'=>0,                                                                             //损失人数  
      'jlgj'=>0,                                                                             //箭楼攻击值
      'cqfy'=>0,                                                                           //城墙防御
      'lldj'=>0,
      'pz'=>actModel::hqwjpz($attack_data[$i]['understanding_value'] - $attack_data[$i]['llcs']),
      'jn1'=>$attack_data[$i]['jn1'],
      'jn1_level'=>$attack_data[$i]['jn1_level'],
      'jn2'=>$attack_data[$i]['jn2'],
      'jn2_level'=>$attack_data[$i]['jn2_level'],
      'xx'=>actModel::hqwjxj($attack_data[$i]['understanding_value'] - $attack_data[$i]['llcs']),
      'iid'=>$attack_data[$i]['avatar']
      ); 
      $atlz = null;
      $asmz = null;
      unset($sxxs);
    }  
    for ($j = 0; $j < count($defend_data); $j++) {
      $defend_dj = $defend_data[$j]['general_level'];                              //将领等级
      $defend_zy = $defend_data[$j]['professional'];                               //职业
      $sxxs = genModel::sxxs($defend_zy);
      if (!empty($defend_data[$j]['jlgj'])) {
        $jlgj = $defend_data[$j]['jlgj'];
      } else {
        $jlgj = 0;
      }
       if (!empty($defend_data[$j]['cqfy'])) {
        $cqfy = $defend_data[$j]['cqfy'];
      } else {
        $cqfy = 0;
      }
      if ($isGw == 1) {
        $dataArray[] = array(
        'id'=>$defend_data[$j]['intID'],
        'general_name'=>$defend_data[$j]['general_name'],
        'priority'=>$defend_data[$j]['mobility'],   //机动力
        'sort_id'=>$defend_data[$j]['general_sort'],                               //排序
        'role'=>2,                                                                 //防守方标识        
        'army_type'=>$defend_data[$j]['professional'],                                                  //兵种
        'command_soldier'=>$defend_data[$j]['physical_value'] * 10,                    //士兵数量
        //'command_solder'=>$defend_data[$j]['general_life'],
        'attack_value'=>$defend_data[$j]['attack_value'],                          //攻击值
        'defense_value'=>$defend_data[$j]['defense_value'],                        //防御值
        'physical_value'=>$defend_data[$j]['physical_value'],                      //生命值
        'agility_value'=>$defend_data[$j]['agility_value'],                        //敏捷值
        'understanding_value'=>$defend_data[$j]['understanding_value'],            //悟性
        'attack_move_speed_soldier'=>actModel::getMoveSpeedValue($defend_zy),                //士兵移动速度
        'general_level'=>$defend_data[$j]['general_level'],                                     //将领级别  
        'professional'=>$defend_data[$j]['professional'],                                      //将领职业
        'distance'=>0,                                                                         //默认当前格数，默认为0  
        'animation'=>0,                                                                        //攻击或防御特技
        'loss'=>0,                                                                             //损失人数
        'jlgj'=>$jlgj,
        'cqfy'=>$cqfy,
        'lldj'=>0,
        'pz'=>actModel::hqwjpz($defend_data[$j]['understanding_value']),
        'jn1'=>0,
        'jn1_level'=>0,
        'jn2'=>0,
        'jn2_level'=>0,
        'xx'=>actModel::hqwjxj($defend_data[$j]['understanding_value']),
        'iid'=> $defend_data[$j]['avatar']      
        );     
      } elseif ($isGw == 2) {
        $dataArray[] = array(
        'id'=>$defend_data[$j]['intID'],
        'general_name'=>$defend_data[$j]['general_name'],
        'priority'=>$defend_data[$j]['mobility'],   //机动力
        'sort_id'=>$defend_data[$j]['general_sort'],                               //排序
        'role'=>2,                                                                 //防守方标识        
        'army_type'=>$defend_data[$j]['professional'],                                                  //兵种
        'command_soldier'=>$defend_data[$j]['bossLife'],                           //士兵数量        
        //'command_solder'=>$defend_data[$j]['general_life'],
        'attack_value'=>$defend_data[$j]['attack_value'],                          //攻击值
        'defense_value'=>$defend_data[$j]['defense_value'],                        //防御值
        'physical_value'=>$defend_data[$j]['physical_value'],                      //生命值
        'agility_value'=>$defend_data[$j]['agility_value'],                        //敏捷值
        'understanding_value'=>$defend_data[$j]['understanding_value'],            //悟性
        'attack_move_speed_soldier'=>actModel::getMoveSpeedValue($defend_zy),                //士兵移动速度
        'general_level'=>$defend_data[$j]['general_level'],                                     //将领级别  
        'professional'=>$defend_data[$j]['professional'],                                      //将领职业
        'distance'=>0,                                                                         //默认当前格数，默认为0  
        'animation'=>0,                                                                        //攻击或防御特技
        'loss'=>0,                                                                             //损失人数
        'jlgj'=>$jlgj,
        'cqfy'=>$cqfy,
        'lldj'=>0,
        'pz'=>actModel::hqwjpz($defend_data[$j]['understanding_value']),
        'jn1'=>0,
        'jn1_level'=>0,
        'jn2'=>0,
        'jn2_level'=>0,
        'xx'=>actModel::hqwjxj($defend_data[$j]['understanding_value']),
        'iid'=>$defend_data[$j]['avatar']     
        );               
      } else {
        $sf_jwInfo = jwmc($sf_jwdj);
            $sf_jwjc = 1 + $sf_jwInfo['jc'] / 100;
        $dzbgjjcArray = array();
        $dzbfyjcArray = array();
        $dzbtljcArray = array();
        $dzbmjjcArray = array();
        $defenseplayer = $G_PlayerMgr->GetPlayer($defend_data[$j]['playerid']);
        foreach ($zbslots as $slot) {
          $zbid = $defend_data[$j][$slot];  
          if ($zbid != 0) {
            $zbInfo = $defenseplayer->GetZBSX($zbid);
            $dzbgjjcArray[] = $zbInfo['gj'];
            $dzbfyjcArray[] = $zbInfo['fy'];
            $dzbtljcArray[] = $zbInfo['tl'];
            $dzbmjjcArray[] = $zbInfo['mj'];
          }
        }  
        $dzbgjjc = array_sum($dzbgjjcArray);
        $dzbfyjc = array_sum($dzbfyjcArray);
        $dzbtljc = array_sum($dzbtljcArray);
        $dzbmjjc = array_sum($dzbmjjcArray);          

        $tlz = genModel::hqwjsx($defend_dj,$defend_data[$j] ['understanding_value'],$defend_data[$j] ['professional_level'],$defend_data[$j] ['llcs'],$sf_jwjc,$dzbtljc,$sxxs ['tl'],$defend_data[$j] ['py_tl']);          
        $dataArray[] = array(
        'id'=>$defend_data[$j]['intID'],
        'general_name'=>$defend_data[$j]['general_name'],
        'priority'=>actModel::getMobility($defend_zy,$defend_dj),   //机动力
        'sort_id'=>$defend_data[$j]['general_sort'],                               //排序
        'role'=>2,                                                                 //防守方标识        
        'army_type'=>$defend_data[$j]['professional'],                                                  //兵种
        'command_soldier'=>round($tlz,0) * 10,                    //士兵数量
        'attack_value'=>genModel::hqwjsx($defend_dj,$defend_data[$j] ['understanding_value'],$defend_data[$j] ['professional_level'],$defend_data[$j] ['llcs'],$sf_jwjc,$dzbgjjc,$sxxs ['gj'],$defend_data[$j] ['py_gj']),    //攻击值    
        'defense_value'=>(genModel::hqwjsx($defend_dj,$defend_data[$j] ['understanding_value'],$defend_data[$j] ['professional_level'],$defend_data[$j] ['llcs'],$sf_jwjc,$dzbfyjc,$sxxs ['fy'],$defend_data[$j] ['py_fy'])) * $sm,    //防御值          
        'physical_value'=>$tlz,                      //生命值
        'agility_value'=>genModel::hqwjsx($defend_dj,$defend_data[$j] ['understanding_value'],$defend_data[$j] ['professional_level'],$defend_data[$j] ['llcs'],$sf_jwjc,$dzbmjjc,$sxxs ['mj'],$defend_data[$j] ['py_mj']),        
        'understanding_value'=>$defend_data[$j]['understanding_value'],            //悟性
        'attack_move_speed_soldier'=>actModel::getMoveSpeedValue($defend_zy),                //士兵移动速度
        'general_level'=>$defend_data[$j]['general_level'],                                     //将领级别  
        'professional'=>$defend_data[$j]['professional'],                                      //将领职业
        'distance'=>0,                                                                         //默认当前格数，默认为0  
        'animation'=>0,                                                                        //攻击或防御特技
        'loss'=>0,                                                                             //损失人数
        'jlgj'=>$jlgj,
        'cqfy'=>$cqfy,
        'lldj'=>0,
        'pz'=>actModel::hqwjpz($defend_data[$j]['understanding_value'] - $defend_data[$j]['llcs']),
        'jn1'=>$defend_data[$j]['jn1'],
        'jn1_level'=>$defend_data[$j]['jn1_level'],
        'jn2'=>$defend_data[$j]['jn2'],
        'jn2_level'=>$defend_data[$j]['jn2_level'],
        'xx'=>actModel::hqwjxj($defend_data[$j]['understanding_value'] - $defend_data[$j]['llcs']),
        'iid'=>$defend_data[$j]['avatar']      
        ); 
        $tlz = null;                  
      }
      $jlgj = null;
      $cqfy = null;      
            unset($sxxs);
    }  
    //获取出手顺序数据
    $fight_sort = actModel::sortArray($dataArray);  
    //echo '初始:<br>';
    for ($k=0;$k<count($fight_sort);$k++) {
         $begin['hid'] = $fight_sort[$k]['role'].$fight_sort[$k]['sort_id'];        //将领代号
         $begin['sac'] = intval($fight_sort[$k]['command_soldier']);                //血量
         $begin['st'] = $fight_sort[$k]['professional'];                            //将领职业
         $begin['bj'] = $fight_sort[$k]['general_level'];                           //将领等级
         $begin['hpmax'] = round($fight_sort[$k]['physical_value'],0) * 10;  
         $begin['name'] = $fight_sort[$k]['general_name'];
         $begin['pj'] = $fight_sort[$k]['pz'];
       	 $begin['xx'] = $fight_sort[$k]['xx'];
       	 $begin['iid'] = $fight_sort[$k]['iid'];
         $array[] = $begin;
      //echo '阵营:'  .$fight_sort[$k]['role'].',排序:'.$fight_sort[$k]['sort_id'].',士兵数:'.$fight_sort[$k]['command_soldier'].',当前所处格数:'.$fight_sort[$k]['distance'].',ID:'.$fight_sort[$k]['id'].';<br><br>';        
    }
    $showValue['begin'] = $array;
    $log = array();
      actModel::attack($fight_sort,$log,0);
      $showValue['result'] = $fight_sort;
      $showValue['round'] = $log;
      return $showValue;
  }    
  
  //获取武将星级
  public static function hqwjxj($tf) {
      $xj = $tf % 5;
      if ($xj == 0) {
          $xj = 5;
      }
      return $xj; 
  }
  
  
  public static function attack(&$fight_sort,&$log=array(),$i=0) {  
    $i++;
    //echo $i.'<br>';    
    for ($k = 0 ; $k < count($fight_sort); $k++) {
      /*if ($i == 3) {
        break;
      }*/
      //$array = array();    
        //$army_type = $fight_sort[$k]['army_type'];          //兵种
      $army_num = $fight_sort[$k]['command_soldier'];     //士兵数量  
      //echo '编号'.$k.'血量:'.$army_num.'<br>';          
      $id = $fight_sort[$k]['id'];                        //士兵ID      
      $zy = $fight_sort[$k]['professional'];              //职业
      $sm = $fight_sort[$k]['physical_value'];            //生命
      $role = $fight_sort[$k]['role'];                    //攻击方还是防守方
      $distance = $fight_sort[$k]['distance'];            //移动格数
      $attack_move_speed_soldier = $fight_sort[$k]['attack_move_speed_soldier']; //移动速度    
      $general_sort = $fight_sort[$k]['sort_id'];  //攻击方排序
      $value = array();
      if ($army_num == 0) {
        continue;
      }
      //$choseLV = '';
      $jn = actModel::cftx($k,$fight_sort,$i);
      if (empty($jn)) {
        continue;
      } else {
        if ($jn == 20) {
          $value = jn::ptgj($k,$fight_sort,$i);
        } else {
          $function = "jn_".$jn[0];
          $value = jn::$function($k,$fight_sort,$i,$jn[1]);
          //8,9,10,11,14,15
          $value2 = null;
          if ($jn[0] == 8 || $jn[0] == 9 || $jn[0] == 10 || $jn[0] == 11 || $jn[0] == 14 || $jn[0] == 15) {
          	 $value2 = jn::ptgj($k,$fight_sort,$i); 
          }
          if (!empty($value2)) {
          	 $value = array($value,$value2);
          }
        }
        if (empty($value)) {
          continue;
        }          
        $array[] = $value;                                       
      }      
    }
    //print_r($array);
    if (!empty($array)) {
      array_push($log,$array);
    }  
    $check = actModel::checkIfContinue($fight_sort);
    //echo 'lun_'.$i.'_'.$check.'<br>';
    if ($check == true)  {
      //echo 'zhixing';
      $fight_sort = actModel::attack($fight_sort,$log,$i);              
    }      
    return $fight_sort;    
  }  
  
    //判断能否继续战斗
  public static function checkIfContinue($fight_sort) {    
    $soldier_defend_num = $soldier_attack_num = array(0);
    for ($i=0 ; $i < count($fight_sort); $i++) {
      if ($fight_sort[$i]['command_soldier'] <= 0) {
        continue;
      }          
      if ($fight_sort[$i]['role'] == 2) {
        $soldier_defend_num[] = $fight_sort[$i]['command_soldier'];
      } else {
        $soldier_attack_num[] = $fight_sort[$i]['command_soldier'];      
      }
    }
    if (array_sum($soldier_attack_num) == 0 || array_sum($soldier_defend_num)==0) {
      return false;
      } else {
        return true;
      }
  }

  public static function attacking(&$fight_sort,$can_attack_object,$attack_tag,$round,$kill_value,&$dtx = 0) {
    if ($can_attack_object === 'none') {
      return $fight_sort;
    } elseif ($can_attack_object === '') {
      return 'over';
    } elseif ($can_attack_object === 'continue') {
      return 'continue';
    } else {
       //初始属性*（1+总悟性*（武将当前等级-1）/100）
       $defend_info = $fight_sort[$can_attack_object];                                                                                       //防守方信息数据 
           //print_r($defend_info);
       //$defend_forcelv = $defend_info['forcelv'];    
       $defend_life_value = $defend_info['physical_value'];    //防守方生命值       
       $defend_attack_value = $defend_info['attack_value'];    //防守方攻击值
       $defend_agility_value = $defend_info['agility_value'];  //防守方敏捷值       
       $defend_value = $defend_info['defense_value'];          //防守方防御值 
       $defend_army_type = $defend_general_professional = $defend_info['professional'];                                                      //防守方将领职业
       $defend_soldier_num = $defend_info['command_soldier'];         //血量
          
       $attack_info = $fight_sort[$attack_tag];          
       //$attack_forcelv = $attack_info['forcelv'];                                                                                          //攻击方信息数据       
       $attack_life_value = $attack_info['physical_value'];          //攻击方生命值(即体力值)       
       $attack_value = $attack_info['attack_value'];                 //攻击方攻击值
       $attack_agility_value = $attack_info['agility_value'];        //攻方敏捷值
       $attack_defend_value = $attack_info['defense_value'];          //攻击方防御值
       $attack_army_type = $attack_general_professional = $attack_info['professional'];                                                            //攻方兵种
       //$attack_fight_level = ($attack_life_value + $attack_value + $attack_agility_value + $attack_defend_value) / 100 + $attack_info['understanding_value'] / 100 + $attack_info['lldj'] * 2 / 100;;  //攻击方战斗等级
           $attack_soldier_num = $attack_info['command_soldier'];        //血量
       $hit_probability_value = ($attack_agility_value / $defend_agility_value - 1) / 2 + 0.8; //命中率

       /*if (!empty($_SESSION['mz'])) {
            $hit_probability_value = 1;
            //$_SESSION['mz'] = NULL;
       } else {*/
       if ($hit_probability_value >= 0.9) {
           $hit_probability_value = 0.9; 
       } elseif ($hit_probability_value <= 0.1) {
           $hit_probability_value = 0.1;
       }           
       //}         
       if (rand(1,100) <= $hit_probability_value * 100) {
            $hit_probability = 1;
       } else {
            $hit_probability = 0;
       }          
       //unset($fight_sort[$attack_tag]['animation']);                 //初始化攻击方特效
       $dtx = 0;
       if ($hit_probability != 1) {       
            $kill_value = 0;                                          //当未命中时，杀伤力为0
            $dtx = 1;
       }
       $loss_soldier =  ceil($kill_value); 
       $defend_left_soldier_num = $defend_soldier_num - $loss_soldier;
       //echo '剩余血量'.$defend_left_soldier_num.'<br>';
       if ($defend_left_soldier_num < 0) {
            $defend_left_soldier_num = 0;
       }
       //初始化损失人数
       //$fight_sort[$attack_tag]['loss'] = 0; 
       //$fight_sort[$can_attack_object]['loss'] = 0; 
       //END初始化损失人数
       //$fight_sort[$can_attack_object]['loss'] = $loss_soldier;                                          //更新防守方损失人数
       $fight_sort[$can_attack_object]['command_soldier'] = $defend_left_soldier_num;                    //更新防守方士兵数         
       //$sybl = $defend_left_soldier_num;
       //$sponsor = $fight_sort[$can_attack_object]['role'].$fight_sort[$can_attack_object]['sort_id'];    //攻击者身份
          /*if (!empty($fight_sort[$can_attack_object][$gtx])) {
         $fight_sort[$can_attack_object][$gtx] = $fight_sort[$can_attack_object][$gtx].'&'.$attack_tag.':'.$gtx.':'.$loss_soldier.':'.$sybl.':'.$dtx.':'.$round;
       } else {
         $fight_sort[$can_attack_object][$gtx] = $attack_tag.':'.$gtx.':'.$loss_soldier.':'.$sybl.':'.$dtx.':'.$round;
       }*/
              
       return $fight_sort;
    }
  }  
  
    //是否触发特效
  public static function cftx($tag,&$fight_sort,$round) {
    if (!empty($fight_sort[$tag]['hm'])) {
      if ($round - $fight_sort[$tag]['hm'] > 1) {
         unset($fight_sort[$tag]['hm']);
         if (!empty($fight_sort[$tag]['ybsf'])) {
            unset($fight_sort[$tag]['ybsf']);
         } else {
            unset($fight_sort[$tag]['dhyj']);
         }        
      } else {
         return false;
      }
    }    
    if ($fight_sort[$tag]['role'] == 1) {
      $enemy = 2;
    } else {
      $enemy = 1;
    }  
    $enemyBlood = array();  
    for ($i = 0; $i < count($fight_sort); $i++) {
      if ($fight_sort[$i]['role'] == $enemy) {
        $enemyBlood[] = $fight_sort[$i]['command_soldier'];
      }
    }
    $enemyTotalBlood = array_sum($enemyBlood);
    if ($enemyTotalBlood > 0) {
    	if ((in_array($fight_sort[$tag]['jn1'],array(16,17)) || in_array($fight_sort[$tag]['jn2'],array(16,17))) && $round > 0 && $round % 3 == 0) {
    		if (in_array($fight_sort[$tag]['jn1'],array(16,17))) {
    			return array($fight_sort[$tag]['jn1'],$fight_sort[$tag]['jn1_level']);
    		} else {
    			return array($fight_sort[$tag]['jn2'],$fight_sort[$tag]['jn2_level']);
    		}
    	}
        $jn = array($fight_sort[$tag]['jn1'],$fight_sort[$tag]['jn2']);
        $jn_level = array($fight_sort[$tag]['jn1_level'],$fight_sort[$tag]['jn2_level']);
        //$choseLV = '';
        $jnidInfo = actModel::zdjn($jn,$jn_level);
        if (!empty($jnidInfo)) {
           return array($jnidInfo[0],$jnidInfo[1]);
        } else {
           return 20;
        }
    } else {
        return false;
    }
  }
  
  //寻找可攻击对象
  public static function findAttackObject($tag,&$fight_sort,&$act='') {
    $arrive = '';
    $total_distance = 23;                                            //士兵间最大间隔
    $distance_array2 = array();
    $distance_array1 = array();
    //$soldier_attack_num = $soldier_defend_num = array(0);
    for ($i=0 ; $i < count($fight_sort); $i++) {
      if ($fight_sort[$i]['command_soldier'] <= 0) {
        continue;
      }      
      if ($fight_sort[$i]['role'] == 2) {
        //$fight_sort_defend_array[] = $fight_sort[$i];
        //$soldier_defend_num[] = $fight_sort[$i]['command_soldier'];        
        $distance_array2[] = $fight_sort[$i]['distance'];   //防守方的可攻击距离数据      
      } else {
        //$fight_sort_attack_array[] = $fight_sort[$i];
        //$soldier_attack_num[] = $fight_sort[$i]['command_soldier'];
        $distance_array1[] = $fight_sort[$i]['distance'];   //攻击方的可攻击距离数据              
      }
    }
    //对方已无兵,则战斗结束    
    /*if (array_sum($soldier_attack_num) == 0 || array_sum($soldier_defend_num)==0) {
      return 'over';
      }  */  
    $role = $fight_sort[$tag]['role'];            //发起攻击的是攻击方还是防守方
    $army_type = $fight_sort[$tag]['professional'];  //发起攻击方兵种
    $attack_tag = 'none';    
    //防守方进行攻击
    if ($role == 2) {
      if ($army_type == 5) {
        $objectID1 = actModel::findID(1,min($distance_array1),$fight_sort);
        if ($objectID1 === 'nodata') {
          $can_attack = 0;
        } else {          
          $attack_tag = $objectID1;
          $can_attack = 1;
        }
      } else {
          $obj_distance = max($distance_array1); //找出最近的目标
          //heroCommon::insertLog('最近目标+'.$obj_distance);
          //攻击方理论该回合可移动的格数
          $gjjy = actModel::getGjjl($army_type);  
          $move_num = $fight_sort[$tag]['attack_move_speed_soldier'] + $gjjy;
          $attact_distance = $fight_sort[$tag]['distance'] + $move_num; //攻击方目前所移动的总格数          
          if (($obj_distance + $attact_distance) >= $total_distance) {
            if ($act != 3) {
              //heroCommon::insertLog('初始位置+'.$fight_sort[$tag]['distance'].'+职业+'.$army_type);
              $dis = $total_distance - $fight_sort[$tag]['distance'] - $obj_distance - 1; //离最近目标距离
              if ($dis >= $fight_sort[$tag]['attack_move_speed_soldier']) {
                $fight_sort[$tag]['distance'] = $fight_sort[$tag]['distance'] + $fight_sort[$tag]['attack_move_speed_soldier'];      //当对方在自己的移动距离内，则先移动再打击
              } else {
                $fight_sort[$tag]['distance'] = $fight_sort[$tag]['distance'] + $dis;
              }  
              //heroCommon::insertLog('移动后距离+'.$fight_sort[$tag]['distance']);            
            }
            $arrive = 'ok';
          } else {
            $arrive = 'fail';
          }  
          //$objectID1 = actModel::findID(2,$obj_distance,$fight_sort);              
          if ($arrive == 'ok') {
            $objectID1 = actModel::findID(1,$obj_distance,$fight_sort);
            $attack_tag = $objectID1;
            $can_attack = 1;              
          } else {
            $can_attack = 0;
          }      
      }      
    } else {
      if ($army_type == 5) {
        $objectID2 = actModel::findID(2,min($distance_array2),$fight_sort);
        if ($objectID2 === 'nodata') {
          $can_attack = 0;
        } else {          
          $attack_tag = $objectID2;
          $can_attack = 1;
        }        
      } else {
          $obj_distance1 = max($distance_array2); //找出最近的目标          
          //攻击方理论该回合可移动的格数
          $move_num = $fight_sort[$tag]['attack_move_speed_soldier'];          
          $gjjy2 = actModel::getGjjl($army_type);
          $attact_distance = $fight_sort[$tag]['distance'] + $move_num + $gjjy2;             //攻击方目前所移动的总格数
          if (($obj_distance1 + $attact_distance) >= $total_distance) {
            if ($act != 3) {
                $dis2 = $total_distance - $fight_sort[$tag]['distance'] - $obj_distance1 - 1; //离最近目标距离
                if ($dis2 >= $fight_sort[$tag]['attack_move_speed_soldier']) {
                $fight_sort[$tag]['distance'] = $fight_sort[$tag]['distance'] + $fight_sort[$tag]['attack_move_speed_soldier'];      //当对方在自己的移动距离内，则先移动再打击
                } else {
                  $fight_sort[$tag]['distance'] = $fight_sort[$tag]['distance'] + $dis2;
                }
              }
              $arrive = 'ok';
          } else {
            $arrive = 'fail';
          }
          if ($arrive == 'ok') {
            $objectID2 = actModel::findID(2,$obj_distance1,$fight_sort);              
            $attack_tag = $objectID2;
            $can_attack = 1;
            //heroCommon::insertLog('真实攻击目标2+'.$objectID2);                         
          } else {
            $can_attack = 0;
          }
          $gjjy2 = null;
          $objectID2 = null;              
      }      
    }
    if ($can_attack == 0) {
      //如果没有攻击对象则往前移动
      if ($army_type != 5) {
        if ($arrive != 'ok') {
          $fight_sort[$tag]['distance'] = $fight_sort[$tag]['distance'] + $fight_sort[$tag]['attack_move_speed_soldier'];  
        }
        $act = 2;  
      } else {
        $act = 3;
      }    
      return 20;                //只移动不攻击
    } else {
      if (empty($act)) {
        $act = 1;
      }
      return $attack_tag;      //具有攻击对象
    }    
  }  
  
  //特效目标选择
  public static function txmb($fight_sort,$tag) {
    //$distance_array2 = array(0);
    //$distance_array1 = array(0);
    for ($i=0 ; $i < count($fight_sort); $i++) {
      if ($fight_sort[$i]['command_soldier'] <= 0) {
        continue;
      }      
      if ($fight_sort[$i]['role'] == 2) {
        $distance_array2[] = $fight_sort[$i]['distance'];   //防守方的可攻击距离数据      
      } else {
        $distance_array1[] = $fight_sort[$i]['distance'];   //攻击方的可攻击距离数据              
      }
    }
    if ($fight_sort[$tag]['role'] == 2) {      
      $distance = max($distance_array2);
    } else {
      $distance = max($distance_array1);
    }  
    $id = actModel::findID($fight_sort[$tag]['role'],$distance,$fight_sort);
    return $id;    
  }
  
  //妙手回春目标选择
  public static function txmb14($fight_sort,$role) {
    $object = array();
    for ($i = 0; $i < count($fight_sort); $i++) {
      if ($fight_sort[$i]['command_soldier'] > 0 && intval($fight_sort[$i]['role']) === intval($role)) {
        $object[] = array('key'=>$i,'sort_id'=>$fight_sort[$i]['sort_id'],'jl'=>$fight_sort[$i]['distance'],'smz'=>round($fight_sort[$i]['command_soldier'] / $fight_sort[$i]['physical_value'],2));
        //break;
      } else {
        continue; 
      }
    }  
    if (!empty($object)) {
          foreach ($object as $key => $jlvalue) {
              $sort_jl[$key] = $jlvalue['jl'];
              $sort_id[$key] = $jlvalue['sort_id'];
              $sort_smz[$key] = $jlvalue['smz'];
              //$sort_id[$key] = $jlvalue['key'];
          }
          array_multisort($sort_smz,SORT_ASC,SORT_NUMERIC,$sort_jl,SORT_DESC,SORT_NUMERIC,$sort_id,SORT_ASC,SORT_NUMERIC,$object);      
      //$keys = array_keys($object);
      foreach ($object as $objectValue) {
        $id = $objectValue['key'];
        break;
      }
      return $id;
    } else {
      return 'nodata';
    }  
  }
  
  //特效目标选择2(攻击对方)$jllx(1攻击最近2攻击最远)
  public static function txmb2($fight_sort,$tag,$jllx) {
    //$distance_array2 = array();
    //$distance_array1 = array();
    for ($i=0 ; $i < count($fight_sort); $i++) {
      if ($fight_sort[$i]['command_soldier'] <= 0) {
        continue;
      }      
      if ($fight_sort[$i]['role'] == 2) {
        $distance_array2[] = $fight_sort[$i]['distance'];   //防守方的可攻击距离数据      
      } else {
        $distance_array1[] = $fight_sort[$i]['distance'];   //攻击方的可攻击距离数据              
      }
    }
    if ($fight_sort[$tag]['role'] == 2) {  
      if ($jllx == 2) {    
        $distance = min($distance_array1);
      } else {
        $distance = max($distance_array1);
      }
      $tagRole = 1;
    } else {
      if ($jllx == 2) {
        $distance = min($distance_array2);
      } else {
        $distance = max($distance_array2);
      }  
      $tagRole = 2;    
    }  
    $id = actModel::findID($tagRole,$distance,$fight_sort);
    return $id;    
  }
  
  //对出手顺序进行排序
  public static function sortArray($data) {
        foreach ($data as $key => $value) {
            $role[$key] = $value['role'];
            $sort_id[$key] = $value['sort_id'];
            $priority[$key] = $value['priority'];
        }
        array_multisort($priority,SORT_DESC,$sort_id,$role,$data); 
        return $data;
  }  
  //获取武将品质$tf：天赋
  public static function hqwjpz($tf) {
        if ($tf >= 0 && $tf <= 15) {
          $pz = 0;
        } elseif ($tf >= 16 && $tf <= 20) {
          $pz = 1;
        } elseif ($tf >= 21 && $tf <= 25) {
          $pz = 2;
        } elseif ($tf >= 26 && $tf <= 30) {
          $pz = 3;
        } else {
          $pz = 4;
        }
        return $pz;
  }
  //战斗技能触发$jnid(技能ID,array格式)$jnjb(技能级别array格式)
  public static function zdjn($jnid,$jnjb,&$rate='') {
    $rate1 = actModel::getJnJl($jnid[0],$jnjb[0]);
    $rate2 = actModel::getJnJl($jnid[1],$jnjb[1]);    
    $rand = rand(0,99);
    if($rand < $rate1) {
      return array($jnid[0],$jnjb[0]);
    }
    elseif($rand < $rate1 + $rate2) {
      return array($jnid[1],$jnjb[1]);
    }
    return false;
  }
  
  //找出可攻击目标ID
  public static function findID($role,$distance,$fight_sort) {
    $object = array();
    for ($i = 0; $i < count($fight_sort); $i++) {
      if ($fight_sort[$i]['command_soldier'] > 0 && intval($fight_sort[$i]['role']) === intval($role) && $fight_sort[$i]['distance'] === $distance) {
        $object[$i] = $fight_sort[$i]['sort_id'];
        //break;
      }
    }  
    if (!empty($object)) {
      asort($object);
      $keys = array_keys($object);
      return $keys[0];
    } else {
      return 'nodata';
    }
  }
  
  //策略相克
  public static function clxk($glcl,$fscl) {
    if($glcl == $fscl)  return 0; //平局
    if ($glcl !=0 && $fscl == 0)  return 1;   //攻击方战略有效
    if ($glcl == 0 && $fscl != 0) return 2;   //防守策略有效

    if ($glcl == 1) {
      if ($fscl == 2) return 1;  //攻击方战略有效
      return 2;  //防守策略有效
    }
    elseif ($glcl == 2) {
      if ($fscl == 1) return 2;  //防守策略有效
      return 1;  //攻击方战略有效
    }
    else {
      if ($fscl == 1) return 1; //攻击方战略有效
      return 2; //防守策略有效        
    }
  }
}
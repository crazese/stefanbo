<?php
set_time_limit(0);
setlocale(LC_ALL, 'zh_CN.UTF-8');
//程序目录
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR.'../');
include(S_ROOT.'config.php');
$base_fields_template =  array(
	'G_Items'=>array ('ItemID','ItemType','EquipType','Name','Description','Rarity','LevelLimit', 'SellPirce','BuyPrice','DiejiaLimit','can_Qh','can_Dz','Attack_value','Defense_value','Physical_value','Agility_value','Addition_attack_value','Addition_defense_value','Addition_physical_value','Addition_agility_value','IsSuit','SuitID','ItemScript','ItemScript_Parameter','Zhiye','IconID','IsMallItem')
	/*'G_ItemOdds'=>array(),
	'G_ItemOdds_tb'=>array(),
	'G_Quests'=>array(),
	'G_StageCardOdds'=>array(),
	'G_StageCardOdds_tb'=>array()*/
	// ,
	// 'G_Quests'=>array (
		// 'QuestID',
		// 'QType',
		// 'QTitle',
		// 'Description',
		// 'Description1',
		// 'Region_ID',
		// 'Level_Min',
		// 'Level_Max',
		// 'Depended_Quest_ID',
		// 'AcceptVar1',
		// 'AcceptVar2',
		// 'AcceptVar3',
		// 'AcceptVar4',
		// 'FinishVar1',
		// 'FinishVar2',
		// 'FinishVar3',
		// 'FinishVar4',
		// 'AcceptScript',
		// 'FinishScript',
		// 'Qstart',
		// 'OnAcceptParameter',
		// 'OnFinishParameter',
		// 'OnAwardParameter',
		// 'RepeatInterval',
		// 'ShortcutID',
		// 'completeDesc',
		// 'completeAwardDesc',
		// 'Qaward',
		// 'Is_Top',
		// 'Is_Guide',
		// 'Guide_Script',
		// 'showProcess',
		// 'npc_dialog',
		// 'acceptTimes',
		// 'public_status',
		// 'domain'
	// )
);

foreach ($base_fields_template as $base_name=>$fields) {
	$file_name = '';
	$item_csv_array = array();
	switch($base_name) {
		case 'G_Items':
			$file_name = 'items';
			break;
		case 'G_Quests':
			$file_name = 'quests';			
			break;			
		/*case 'G_ItemOdds':
			$file_name = 'ItemOdds';
			break;
		case 'G_ItemOdds_tb':
			$file_name = 'ItemOdds_tb';
			break;
		case 'G_StageCardOdds':
			$file_name = 'StageCardOdds';
			break;
		case 'G_StageCardOdds_tb':
			$file_name = 'StageCardOdds_tb';
			break;*/
	}
	if($file_name == '') break;
	$csv_handle = fopen(dirname(__FILE__)."/csv/{$file_name}.csv", 'r');
	while($item_csv_array[] = fgetcsv($csv_handle));		
	fclose($csv_handle);
	array_shift($item_csv_array);
	array_pop($item_csv_array);
	
	$contents = array();
	$field_count = count($fields);
	$row_count = count($item_csv_array);
	for($row = 0; $row < $row_count; $row++) {
		if($base_name == 'G_Quests') {
			if($_SC['kaifangchongzhi'] != 1 && in_array($item_csv_array[$row]['QuestID'],array(1001308,1001309,1001310,1001311,1001312,1001313,1001314,1001305,1001306,3000000,3000001,3000002,3001000,3002000,3003000,1001401,1001402,1001403,1001409,1001410,1001508)))
			continue;
			if($_SC['kaifangchongzhi'] == 1 && in_array($item_csv_array[$row]['QuestID'],array(1001301,1001302,1001303,1001304,3000000,3000001,3000002,3001000,3002000,3003000,1001401,1001402,1001403,1001409,1001410,1001508)))
			continue;			
		}		
		for($field = 0; $field < $field_count; $field++) {
			$contents[$item_csv_array[$row][0]][$fields[$field]] = $item_csv_array[$row][$field];
		}
	}

	$contents = var_export($contents, true);
	file_put_contents(dirname(__FILE__) ."/cn/{$base_name}.php", "<?php\n\$GLOBALS['G_Items'] = ".$contents.";\n?>");
}

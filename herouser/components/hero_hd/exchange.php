<?php

class exchangeEx{

	public static function getExchangeInfo(){
		global $mc, $common, $db;
		
		$ex_key = MC.'exchange_data';
		$exInfo = $mc->get($ex_key);
		$curr_time = time();

		$memflag = false;
		$exShowList = array();
		if(empty($exInfo) || $exInfo['timeout']<$curr_time){
			$memflag = true;

			$sltSql = "select * from ".$common->tname('exchange');
			$sltSql .= " where endTime>'{$curr_time}' and public=1";

			$result = $db->query($sltSql);

			while($line = mysql_fetch_assoc($result)){
				$_info = array('exid'=>$line['exid'],
							   'title'=>$line['title'],
							   'desc'=>$line['desc'],
							   'exObj'=>$line['exObj'],
							   'showTime'=>$line['showTime'],
							   'startTime'=>$line['startTime'],
							   'endTime'=>$line['endTime'],
							   'exCount'=>$line['exCount'],
							   'cond'=>unserialize($line['cond']),
							   'result'=>unserialize($line['result']),
							   'secTimes'=>unserialize($line['secTimes'])
							   );

				$exShowList[$line['exid']] = $_info;
			}
			
		}else{
			$exShowList = $exInfo['info'];
		}

		// 清理过期兑换信息
		$return_ex_info = array();
		foreach($exShowList as $key=>$_exInfo){
			if($_exInfo['endTime']<$curr_time){
				unset($exShowList[$key]);
				$delSql = "delete from ".$common->tname('exchange');
				$delSql .= " where endTime<'{$curr_time}'";
				$db->query($delSql);

				unset($exShowList[$key]);
				$memflag = true;
				continue;
			}

			if($_exInfo['showTime'] < $curr_time){
				$return_ex_info[$key] = $_exInfo;
			}
		}

		if($memflag){
			$timeout = $curr_time + 24*3600;
			$exInfo = array('info'=>$exShowList, 'timeout'=>$timeout);

			$mc->set($ex_key, $exInfo, 0, 24*3600);
		}

		return $return_ex_info;
	}
}
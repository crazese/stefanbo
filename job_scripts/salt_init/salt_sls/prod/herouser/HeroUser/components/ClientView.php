<?php
class ClientView {
  //显示消息
  static function show($message) {
    if(empty($message) || !is_array($message)) {
      echo json_encode(array('status'=>25,'message'=>'error'));
      return;
    }
    global $mc;
    $rsn = intval(_get('ssn'));
    $userid = _get('userId');
    //======DEBUG白名单======>
    global $task;
    if($task == 'register' && $message['status'] == 0) {
      $userid  = $message['userId'];
      $str = file_get_contents(OP_ROOT.'./whitelist.txt');
      if($str !== false) {
        $whitelist = explode(',',$str); //array(228104,54841) 
        if(in_array($userid, $whitelist))   $message['_DEBUG'] = 1;
      }
    }
    //<=====DEBUG白名单=======
    
    $message['rsn'] = $rsn;
    heroCommon::writelog($_SERVER['REQUEST_URI'], $message);
    $message = json_encode($message);
    $mc->set(MC.'UserLastRequest_'.$userid, array('ssn'=>$rsn, 'message'=>$message), 0, 300);
    echo $message;
  }
  //login分段显示
  static function loginShow($message) {
    global $mc;
    $rsn = intval(_get('ssn'));
    $userid = _get('userId');    
    $message['rsn'] = $rsn;
    heroCommon::writelog($_SERVER['REQUEST_URI'], $message);
    if (SYPT == 1) {
	    $message = json_encode($message);
	    $mc->set(MC.'UserLastRequest_'.$userid, array('ssn'=>$rsn, 'message'=>$message), 0, 300);
    } else {
        $ginfo = $message['generals'];
	    $bbinfo = $message['list'];
	    $maininfo = $message;
	    unset($maininfo['generals']);
	    unset($maininfo['list']);
	    //todo:分隔符要使用绝对不会出现的(就是json会转义的，并且转义后不含原字符(" => \")的),比如\n之类
	    $message = json_encode($maininfo).'__'.json_encode($ginfo).'__'.json_encode($bbinfo);	    
	    $mc->set(MC.'UserLastRequest_'.$userid, array('ssn'=>$rsn, 'message'=>$message), 0, 300); 	      	
    }
    echo $message;    
  }	
}
?>
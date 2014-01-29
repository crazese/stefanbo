<?php
class adminController {
	//删除用户内存数据
	function clearMemory() {
	   $playersid = _get('playersid');
	   $ucid = _get('ucid');
	   adminModel::clearMemory($playersid,$ucid);
	   $returnValue['status'] = 0;
	   ClientView::show($returnValue);   
	}
	
	function updateMessageMemory() {
	   $ucid = _get('ucid');
	   adminModel::updateMessageMemory($ucid);
	   $returnValue['status'] = 0;
	   ClientView::show($returnValue);   
	}
}
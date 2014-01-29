<?php
class PlayerItem{
  public function __construct($id, $template_id, $count){
    $id_ =$id;
    $template_id_ = $template_id;
    $count_ = $count;
  }
  
  public $id_;
  public $template_id_;
  public $count_;
}
?>
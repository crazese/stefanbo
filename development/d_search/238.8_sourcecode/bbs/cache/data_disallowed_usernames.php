<?php
if (!defined('IN_PHPBB')) exit;
$expired = (time() > 1286960098) ? true : false;
if ($expired) { return; }

$data =  unserialize('a:3:{i:0;s:10:"sothink.*?";i:1;s:11:"customer.*?";i:2;s:10:".*?service";}');

?>
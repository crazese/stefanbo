<?php
if (!defined('IN_PHPBB')) exit;
$expired = (time() > 1285121804) ? true : false;
if ($expired) { return; }

$data =  unserialize('a:2:{s:7:"modules";a:29:{i:0;a:11:{s:9:"module_id";s:3:"134";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:0:"";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:1:"0";s:7:"left_id";s:1:"1";s:8:"right_id";s:2:"10";s:15:"module_langname";s:8:"MCP_MAIN";s:11:"module_mode";s:0:"";s:11:"module_auth";s:0:"";}i:1;a:11:{s:9:"module_id";s:3:"147";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:4:"main";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"134";s:7:"left_id";s:1:"2";s:8:"right_id";s:1:"3";s:15:"module_langname";s:14:"MCP_MAIN_FRONT";s:11:"module_mode";s:5:"front";s:11:"module_auth";s:0:"";}i:2;a:11:{s:9:"module_id";s:3:"148";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:4:"main";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"134";s:7:"left_id";s:1:"4";s:8:"right_id";s:1:"5";s:15:"module_langname";s:19:"MCP_MAIN_FORUM_VIEW";s:11:"module_mode";s:10:"forum_view";s:11:"module_auth";s:10:"acl_m_,$id";}i:3;a:11:{s:9:"module_id";s:3:"149";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:4:"main";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"134";s:7:"left_id";s:1:"6";s:8:"right_id";s:1:"7";s:15:"module_langname";s:19:"MCP_MAIN_TOPIC_VIEW";s:11:"module_mode";s:10:"topic_view";s:11:"module_auth";s:10:"acl_m_,$id";}i:4;a:11:{s:9:"module_id";s:3:"150";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:4:"main";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"134";s:7:"left_id";s:1:"8";s:8:"right_id";s:1:"9";s:15:"module_langname";s:21:"MCP_MAIN_POST_DETAILS";s:11:"module_mode";s:12:"post_details";s:11:"module_auth";s:31:"acl_m_,$id || (!$id && aclf_m_)";}i:5;a:11:{s:9:"module_id";s:3:"135";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:0:"";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:1:"0";s:7:"left_id";s:2:"11";s:8:"right_id";s:2:"18";s:15:"module_langname";s:9:"MCP_QUEUE";s:11:"module_mode";s:0:"";s:11:"module_auth";s:0:"";}i:6;a:11:{s:9:"module_id";s:3:"153";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:5:"queue";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"135";s:7:"left_id";s:2:"12";s:8:"right_id";s:2:"13";s:15:"module_langname";s:27:"MCP_QUEUE_UNAPPROVED_TOPICS";s:11:"module_mode";s:17:"unapproved_topics";s:11:"module_auth";s:14:"aclf_m_approve";}i:7;a:11:{s:9:"module_id";s:3:"154";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:5:"queue";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"135";s:7:"left_id";s:2:"14";s:8:"right_id";s:2:"15";s:15:"module_langname";s:26:"MCP_QUEUE_UNAPPROVED_POSTS";s:11:"module_mode";s:16:"unapproved_posts";s:11:"module_auth";s:14:"aclf_m_approve";}i:8;a:11:{s:9:"module_id";s:3:"155";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:5:"queue";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"135";s:7:"left_id";s:2:"16";s:8:"right_id";s:2:"17";s:15:"module_langname";s:25:"MCP_QUEUE_APPROVE_DETAILS";s:11:"module_mode";s:15:"approve_details";s:11:"module_auth";s:45:"acl_m_approve,$id || (!$id && aclf_m_approve)";}i:9;a:11:{s:9:"module_id";s:3:"136";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:0:"";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:1:"0";s:7:"left_id";s:2:"19";s:8:"right_id";s:2:"26";s:15:"module_langname";s:11:"MCP_REPORTS";s:11:"module_mode";s:0:"";s:11:"module_auth";s:0:"";}i:10;a:11:{s:9:"module_id";s:3:"156";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:7:"reports";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"136";s:7:"left_id";s:2:"20";s:8:"right_id";s:2:"21";s:15:"module_langname";s:16:"MCP_REPORTS_OPEN";s:11:"module_mode";s:7:"reports";s:11:"module_auth";s:13:"aclf_m_report";}i:11;a:11:{s:9:"module_id";s:3:"157";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:7:"reports";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"136";s:7:"left_id";s:2:"22";s:8:"right_id";s:2:"23";s:15:"module_langname";s:18:"MCP_REPORTS_CLOSED";s:11:"module_mode";s:14:"reports_closed";s:11:"module_auth";s:13:"aclf_m_report";}i:12;a:11:{s:9:"module_id";s:3:"158";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:7:"reports";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"136";s:7:"left_id";s:2:"24";s:8:"right_id";s:2:"25";s:15:"module_langname";s:18:"MCP_REPORT_DETAILS";s:11:"module_mode";s:14:"report_details";s:11:"module_auth";s:43:"acl_m_report,$id || (!$id && aclf_m_report)";}i:13;a:11:{s:9:"module_id";s:3:"137";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:0:"";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:1:"0";s:7:"left_id";s:2:"27";s:8:"right_id";s:2:"32";s:15:"module_langname";s:9:"MCP_NOTES";s:11:"module_mode";s:0:"";s:11:"module_auth";s:0:"";}i:14;a:11:{s:9:"module_id";s:3:"151";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:5:"notes";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"137";s:7:"left_id";s:2:"28";s:8:"right_id";s:2:"29";s:15:"module_langname";s:15:"MCP_NOTES_FRONT";s:11:"module_mode";s:5:"front";s:11:"module_auth";s:0:"";}i:15;a:11:{s:9:"module_id";s:3:"152";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:5:"notes";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"137";s:7:"left_id";s:2:"30";s:8:"right_id";s:2:"31";s:15:"module_langname";s:14:"MCP_NOTES_USER";s:11:"module_mode";s:10:"user_notes";s:11:"module_auth";s:0:"";}i:16;a:11:{s:9:"module_id";s:3:"138";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:0:"";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:1:"0";s:7:"left_id";s:2:"33";s:8:"right_id";s:2:"42";s:15:"module_langname";s:8:"MCP_WARN";s:11:"module_mode";s:0:"";s:11:"module_auth";s:0:"";}i:17;a:11:{s:9:"module_id";s:3:"159";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:4:"warn";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"138";s:7:"left_id";s:2:"34";s:8:"right_id";s:2:"35";s:15:"module_langname";s:14:"MCP_WARN_FRONT";s:11:"module_mode";s:5:"front";s:11:"module_auth";s:11:"aclf_m_warn";}i:18;a:11:{s:9:"module_id";s:3:"160";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:4:"warn";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"138";s:7:"left_id";s:2:"36";s:8:"right_id";s:2:"37";s:15:"module_langname";s:13:"MCP_WARN_LIST";s:11:"module_mode";s:4:"list";s:11:"module_auth";s:11:"aclf_m_warn";}i:19;a:11:{s:9:"module_id";s:3:"161";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:4:"warn";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"138";s:7:"left_id";s:2:"38";s:8:"right_id";s:2:"39";s:15:"module_langname";s:13:"MCP_WARN_USER";s:11:"module_mode";s:9:"warn_user";s:11:"module_auth";s:11:"aclf_m_warn";}i:20;a:11:{s:9:"module_id";s:3:"162";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:4:"warn";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"138";s:7:"left_id";s:2:"40";s:8:"right_id";s:2:"41";s:15:"module_langname";s:13:"MCP_WARN_POST";s:11:"module_mode";s:9:"warn_post";s:11:"module_auth";s:28:"acl_m_warn && acl_f_read,$id";}i:21;a:11:{s:9:"module_id";s:3:"139";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:0:"";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:1:"0";s:7:"left_id";s:2:"43";s:8:"right_id";s:2:"50";s:15:"module_langname";s:8:"MCP_LOGS";s:11:"module_mode";s:0:"";s:11:"module_auth";s:0:"";}i:22;a:11:{s:9:"module_id";s:3:"144";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:4:"logs";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"139";s:7:"left_id";s:2:"44";s:8:"right_id";s:2:"45";s:15:"module_langname";s:14:"MCP_LOGS_FRONT";s:11:"module_mode";s:5:"front";s:11:"module_auth";s:17:"acl_m_ || aclf_m_";}i:23;a:11:{s:9:"module_id";s:3:"145";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:4:"logs";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"139";s:7:"left_id";s:2:"46";s:8:"right_id";s:2:"47";s:15:"module_langname";s:19:"MCP_LOGS_FORUM_VIEW";s:11:"module_mode";s:10:"forum_logs";s:11:"module_auth";s:10:"acl_m_,$id";}i:24;a:11:{s:9:"module_id";s:3:"146";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:4:"logs";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"139";s:7:"left_id";s:2:"48";s:8:"right_id";s:2:"49";s:15:"module_langname";s:19:"MCP_LOGS_TOPIC_VIEW";s:11:"module_mode";s:10:"topic_logs";s:11:"module_auth";s:10:"acl_m_,$id";}i:25;a:11:{s:9:"module_id";s:3:"140";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:0:"";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:1:"0";s:7:"left_id";s:2:"51";s:8:"right_id";s:2:"58";s:15:"module_langname";s:7:"MCP_BAN";s:11:"module_mode";s:0:"";s:11:"module_auth";s:0:"";}i:26;a:11:{s:9:"module_id";s:3:"141";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:3:"ban";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"140";s:7:"left_id";s:2:"52";s:8:"right_id";s:2:"53";s:15:"module_langname";s:17:"MCP_BAN_USERNAMES";s:11:"module_mode";s:4:"user";s:11:"module_auth";s:9:"acl_m_ban";}i:27;a:11:{s:9:"module_id";s:3:"142";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:3:"ban";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"140";s:7:"left_id";s:2:"54";s:8:"right_id";s:2:"55";s:15:"module_langname";s:11:"MCP_BAN_IPS";s:11:"module_mode";s:2:"ip";s:11:"module_auth";s:9:"acl_m_ban";}i:28;a:11:{s:9:"module_id";s:3:"143";s:14:"module_enabled";s:1:"1";s:14:"module_display";s:1:"1";s:15:"module_basename";s:3:"ban";s:12:"module_class";s:3:"mcp";s:9:"parent_id";s:3:"140";s:7:"left_id";s:2:"56";s:8:"right_id";s:2:"57";s:15:"module_langname";s:14:"MCP_BAN_EMAILS";s:11:"module_mode";s:5:"email";s:11:"module_auth";s:9:"acl_m_ban";}}s:7:"parents";a:29:{i:134;a:0:{}i:147;a:1:{i:134;s:1:"0";}i:148;a:1:{i:134;s:1:"0";}i:149;a:1:{i:134;s:1:"0";}i:150;a:1:{i:134;s:1:"0";}i:135;a:0:{}i:153;a:1:{i:135;s:1:"0";}i:154;a:1:{i:135;s:1:"0";}i:155;a:1:{i:135;s:1:"0";}i:136;a:0:{}i:156;a:1:{i:136;s:1:"0";}i:157;a:1:{i:136;s:1:"0";}i:158;a:1:{i:136;s:1:"0";}i:137;a:0:{}i:151;a:1:{i:137;s:1:"0";}i:152;a:1:{i:137;s:1:"0";}i:138;a:0:{}i:159;a:1:{i:138;s:1:"0";}i:160;a:1:{i:138;s:1:"0";}i:161;a:1:{i:138;s:1:"0";}i:162;a:1:{i:138;s:1:"0";}i:139;a:0:{}i:144;a:1:{i:139;s:1:"0";}i:145;a:1:{i:139;s:1:"0";}i:146;a:1:{i:139;s:1:"0";}i:140;a:0:{}i:141;a:1:{i:140;s:1:"0";}i:142;a:1:{i:140;s:1:"0";}i:143;a:1:{i:140;s:1:"0";}}}');

?>
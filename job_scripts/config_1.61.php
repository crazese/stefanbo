<?php
define('SYPT', 0);   //定义是否闪游平台 1是0否
define('SY_LOGIN_RESTRICT', 0); // 定义闪游平台是否需要打开登录限制 1是0否
define('MC','3_'); //多服下要改这个前缀
 $allowuser = array();
$_SC = array();

ini_set('session.cookie_domain', '127.0.0.1');
ini_set('session.save_handler', 'memcached');
ini_set('session.save_path', 'PERSISTENT=ms'.MC.' 127.0.0.1:11211');  //, PERSISTENT=ms2 127.0.0.1:11211 //memcached.so
// ini_set('session.save_handler', 'memcache');
// ini_set('session.save_path', 'tcp://127.0.0.1:11211?persistent=1'); //&weight=1&timeout=1??&retry_interval=15  //memcache.so 

$_SC['dbhost']= '127.0.0.1'; //数据库服务器地址
$_SC['dbuser']= 'sothink';   //数据库用户名
$_SC['dbpw']= 'src8351';   //数据库密码
$_SC['fwqdm']           = 'test61';  //自定义服务器代码 (九游 1服)
$_SC['dbname']= '136new';      //数据库名称
if(isset($_SERVER['HTTP_HOST'])) {
        $_SC['jfpay'] = 'http://'.$_SERVER['HTTP_HOST'].'/ucpay/jforder.php'; //机锋支付请求url
} else {
        $_SC['jfpay'] = '';
}

$_SC['backUrl'] = 'http://hud.jofgame.com:9000'; //uc回传地址，一般与当前服务器域名一致即可
$_SC['port']= 80;
$_SC['domain'] = '119.79.232.99';        //cookie域,与当前服务器域名一致即可，不可带http://

$_SC['chongzhidizhi'] = 'http://119.79.232.99/app.php';  //充值地址
$_SC['shouyedizhi'] = 'http://hut.jofgame.com';    //首页地址
$_SC['kfhdjs'] = 1338307199;    //开服活动结束时间点，unix时间戳
$_SC['apkvs'] = '1.33';  //apk版本信息
$MemcacheList = array('127.0.0.1'); //Memcache服务器列表
$Memport = 11211;
$_SC['sggg'] = '0,0,0';  //松岗广告
define('WS_SERVER', 'http://119.79.232.99/');
define('USER_SERVER', 'http://192.168.1.61:8080/');
$_LOG_INFO['path'] = '/data/http_log/';
$_LOG_INFO['prefix'] = 'log_';

define('SY_PROXY_INTRANET_IP', '10.144.133.56:8099'); // 闪游充值网关内网IP
define('SY_PROXY_INTERNET_IP', '221.181.193.84:8099'); // 闪游充值网关外网IP 还需要同步修改q.jofgame.com的zfb_check.php
define('ALIPAY_SELF_IP', '117.135.138.248:8080');

$_SC['alipay_callback'] ='http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy_alipay-1.php';
$_SC['tenpay_callback'] ='http://'.SY_PROXY_INTERNET_IP.'/proxy/reqProxy_tenpay-1.php';
$_SC['alipay_callback_self'] ='http://'. ALIPAY_SELF_IP .'/components/alipay_self/index.php';
$_SC['yeepay_callback_self'] = 'http://'.ALIPAY_SELF_IP.'/components/hero_chongzhi/yp_cbk.php';

$_SC['cv'] = '1.31';
$_SC['paycardurl']='http://117.135.139.30:17000/index.html';
$_SC['loadlimit']=3.8;
$_SC['yp_log'] = '/usr/local/app/logs/786/fgc_10000016_{log_date}_10.144.133.54_1_0.log';

/*数值版本配置模块(随着数值需求的变更此处慢慢完善)*/
$_SZ['jwbb'] = 1;        //爵位数值版本
/*数值版本配置模块END*/

/*乐逗支付相关配置*/
$_SC['secret'] = '860bfcf35e71cefa6a95';
/*乐逗支付相关配置结束*/

// 测试用参数，表示是否可以跳过闯关战斗，正式发布时必须为0，0 否  1 是
define('TGZD', 1);

/*TOM相关配置*/
/*BID：13339990532
KEY：aoXUgP2w01MqTiCD3v8rZuBjEmVxO7H1
 * */
$_SC['TOM_BID'] = '13339990532';
$_SC['TOM_KEY'] = 'aoXUgP2w01MqTiCD3v8rZuBjEmVxO7H1';
$_SC['TOM_NOTICE_URL'] = 'http://hud.jofgame.com:9000/xsyd/ucpay/tompay.php';
/*TOM相关配置结束*/
/*微博接入相关配置*/
$_WB['source'] = '938920110';
$_WB['secret'] = '241845fb579770f662dcb55b3ac6ba88';
$_WB['zfid'] = 1301006;
$_SC['helpdocumenturl'] = 'http://q.jofgame.com/helpcenter_V1.37/index.html'; // 游戏帮助文档的URL地址
//===================================================
$_SC['kaifangchongzhi'] = 1;  //是否开放充值 1 是 0 否
$_SC['swf_version']= '1.0';                  //swf发布版本号

$_SC['dwhdkssj'] = 1343183400;  //7.1活动开始时间      1341072000（官方时间）
$_SC['dwhdjssj'] = 1343811600;  //7.1活动结束时间      1341331199（官方时间）
$_SC['fankuidizhi'] = 'http://119.97.226.138:808/new_feedback/send_feedback/feedback.php';    //反馈地址
// =========================

ini_set('memcached.compression_type', 'fastlz');
ini_set('memcached.compression_factor', 1.3);
ini_set('memcached.compression_threshold', 8000);
ini_set('memcached.serializer', 'igbinary');
ini_set('memcached.sess_binary', 'On');

// 压力测试用DEBUG标志
//define('PRESSURE_TEST_DEBUG', false);
// 停服公告
define('SERVER_CLOSE_NOTICE', '服务器正在停机维护,稍后开放.');

if (isset($_REQUEST['client'])) {
        $client = $_REQUEST['client'];
} else {
        $client = null;
}
//HeroOL配置参数
$_SC['dbcharset']               = 'utf8';      //字符集
$_SC['pconnect']                = 0;           //是否持续连接

$_SC['tablepre']                = 'ol_';       //表名前缀
$_SC['charset']                 = 'utf-8';     //页面字符集

/*UC游戏接口相关信息*/
$_SC['cpId'] = 690;
$_SC['gameId'] = 64431;
$_SC['channelId'] = 2;
$_SC['serverId'] = 949;
$_SC['apiKey'] = 'f23e3bdea56048a71c5100ee824ca699';
$_SC['apiUrl'] = 'sdk.g.uc.cn';  //sdk.g.uc.cn   sdk.test4.g.uc.cn
/*UC游戏接口相关信息结束*/
/*9游页游测试环境发布时须修改为正式环境*/
$_SC['9ycpId'] = 672;
$_SC['9ygameId'] = 64679;
$_SC['9ychannelId'] = 2;
$_SC['9yserverId'] = 1010;
$_SC['9yapiKey'] = '60f4599b3ed1462da741af543a5c6761';
$_SC['9yapiUrl'] = 'api.test2.g.uc.cn';
$_SC['redirectUrl'] = 'http://117.135.138.196/index.php';
$_SC['callbackUrl'] = 'http://117.135.138.196/ucpay/9yPagepay.php';
/*91游戏接口相关信息*/
$_SC['AppId'] = 108003;
$_SC['appKey'] = '87c00de7499f766bc71bc2aba90194320f0853818fbe0f8b';
$_SC['91apiUrl'] = 'service.sj.91.com';  //service.sj.91.com:8080/usercenter/AP.aspx   service.sj.91.com/usercenter/AP.aspx
/*91游戏接口相关信息结束*/

$_SC['keyPay']          = 'p9msB_C2RNyvPyhj0gWLNbWwEvu6Wkq1avYS0ziFZ41dZ4Or4nI9eTguyt1Ec8WY';
$_SC['isAPI']          = '1';
$_SC['gamenum']       = 10000000;
$_SC['status'] = 1;//关闭服务  0 开启服务 1
$_SC['platform'] = 'qq'; //'9ypage'; 'qq';'uc';

$_SC['iosczdz'] = '';  //苹果支付验证接口
$_SC['AppStoreURL'] = 'itunes.com/apps/streetfighteriicollection'; //苹果支付

//松岗订单指向地址
$_SC['sgpayurl'] = '';
//包软支付订单指向地址
$_SC['brpayurl'] = 'http://117.135.138.248:8080/ucpay/brpay.php';
//松岗发送email地址
$_SC['sgemail']='Mobile7@unalis.com.tw';

$_OPTION = array(0 => "common",1 => "user",2 => "role",3 => "map",4 => "city",5 => "fight",6 => "letters",7 => "social",8 => "quests",9 => "tools",10 => "admin", 11=>"chongzhi", 12 => "dalei",13 => "zyd",14=>'hd',15=>'rank',16=>'achievements'); //模块
$_TASK = array("common"  => array ( "a" => "version"),
                           "user"  => array ( "a" => "register","b" => "authorize","c" => "chagepassword","d"=>"checksid","e"=>"tjyzm","f"=>"xgksdl"),
               "role"  => array ( "a" => "login","b" => "createrole","c" => "logout","d"=>"returnroledatatouser","e"=>"addfriends","f"=>"lottery","g"=>"regetgeneralinfo","h"=>"setregionid",'i'=>'sstt','j'=>'xgnc','k'=>'lpm','l'=>'hqsjyzm', 'm'=>'tjsjyzm'),
                           "map"  => array ( "a" => "move","b" => "arrive","c" => "getnodeinfo","d" => "changemap","e" => "gohome"),
                           "city"  => array ( "a" => "getroleinfo","b" => "getcityinfo","c" => "upgradebuild","d" => "sczs","e" => "sellresource","f" => "hiresoldiers","g" => "getgeneralinfo","h" => "setgeneralstate","i" => "setmaingeneral","j" => "switchgeneralstate","k" => "setallgenaralsstatetobackup","l" => "setgeneralorder","m" => "hiregeneral","n" => "fullsoldier","o" => "quickupdatehiregeneral","p" => "getgenerallistforsetarmy","q" => "addtroops","r" => "quickaddtroops","s"=>"setadstatus","t"=>"replacesoldier","u"=>"addedsoldiers","v"=>"firegerneral","w"=>"requestquickgrow","x"=>"hiregeneralinfo","y"=>"firegerneral","z"=>"exchange","aa"=>"fighttest","ab"=>"getzsinfo","ac"=>"upgradegerneralneili","ad"=>"vip","af"=>"gethiregeneralinfo","ah"=>"szzf","aj"=>"qxzl","ak"=>"sqsy","al"=>"sywjzlsj","am"=>"szcz","an"=>"qxszcz","ao"=>"wjzb","ap"=>"zlck","aq"=>"wjhc","ar"=>"ksck","as"=>"wjhcxx","at"=>"mllcd","au"=>"wjxlxx","av"=>"hqwjxlxx","aw"=>"kqxlw","ax"=>"xlwj","ay"=>"wcxl","az"=>"bqsjcl","b1"=>"lllqsh",'ba'=>'vipxx','bc'=>'kzjlb','bd'=>'qqwjccxx','be'=>'hqdgwj','bf'=>'hqjnsjxx','bh'=>'szgfcl','bi'=>'zc','bj'=>'mzscd','bk'=>'ckjnxx','bl'=>'wjly','bm'=>'hqly','bn'=>'tbxx','bo'=>'tb','bp'=>'czcgjyb','bq'=>'hqmrjllb','br'=>'fsltxx','bs'=>'ltjl','bt'=>'bcpy','bu'=>'pywj','bv'=>'tsjcbl','bw'=>'wjjc','bx'=>'hqlypb','by'=>'scly','bz'=>'pbly','ca'=>'qxlypb','cb'=>'tbjhjs','cc'=>'pbltwj','cd'=>'jbltxx','ce'=>'slkqxx','cf'=>'hqltpb','cg'=>'qxltpb','ch'=>'hqltcmxx','ci'=>'hqlthyxx','cj'=>'tsykrl','ck'=>'hqqrjl','cl'=>'kstqwh','cm'=>'hqpm','cn'=>'sdkpf','co'=>'wjtsxj','cp'=>'gxcz','cq'=>'jhwjzb'),
                           "fight"  => array ( "b" => "getattactcitylist","c"=>"attactcity","d"=>"requestattactcity","e"=>"setpeace","f"=>"gettalk","g"=>"setword","h"=>"occupy","i"=>"zj","j"=>"jbr", "k"=>"qqcgxx", "l"=>"qqxgxx", "m"=>"qqxggwxx", "n"=>"cg", "o"=>"tccg", "p"=>"zdhl", "q"=>"zjcgcs"),
                           "letters"  => array ( "a" => "invitefriend","b" => "agreegift","c" => "agreefriend","d" => "takegift","e" => "agreepractice","f" => "getmessagelist","g" => "getmessageinfo","h" => "deleteletters","i"=>"checkmsg","j"=>"lqxtriwp",'k'=>'qqzdhf','l'=>'lqsylw','m'=>'hzsylw','n'=>'zssylw','o'=>'jssyhyqq','p'=>'sczdlxtb','q'=>'bzsyll'),
                           "social"  => array ("a" => "setgift","b" => "setreturngift","c" => "setrobbery","d" => "getfriendlist","e" => "setreduction","f" => "getenemylist","g" => "getrandfriend","h" => "getpracticeinfo","i" => "setpractice","j" => "delpractice","k" => "submitpractice","l" => "submitlesspractice","m" => "cancelpractice","n" => "lesssilver","o" => "getnpcinfo","p" => "setnpcpractice","q" => "getfriendsnotrepeat","r"=>"getucfriendlist","s"=>"inviteucfriend","t"=>"deletefriends","u"=>"setucgift","v"=>"getblacklist","w"=>"selpractice","x"=>"setranddata","y"=>"setallgift","b1"=>"getneighbor","c1"=>"jgtj","d1"=>"jhysq","e1"=>"cljhysq","f1"=>"openbox","g1"=>"sylw",'h1'=>'sslj','il'=>'bindreqcode'),
               "quests" => array("a"=>"onaward","b"=>"getrwshow","c"=>"getrwlist","d"=>"getrwinfo","e"=>"upgradetest","f"=>"hqmbxx","g"=>"openrwbox","h"=>"regetrwlist"),
                           "tools" => array("a"=>"bglb","b"=>'bgkr',"c"=>"pickupitem","d"=>"csdj","e"=>"sydj","f"=>"sclb","g"=>"gmdj",'k'=>'djhc', 'h'=>'qqqhxx', 'i'=>'qhzb', 'j'=>'jns', 'l'=>'dkj', 'm'=>'hqcjxx', 'n'=>'cqqp', 'o'=>'cqjl', 'p'=>'test', 'q'=>'dzzb' ,'r'=>'qqdzxx','s'=>'latecj', 't'=>'tzjj','u'=>'qqfmxx','v'=>'hqbqclyb','w'=>'bqfmcl','x'=>'fmzb','y'=>'qqrlxx','z'=>'rlzb'),
                           "admin" => array("a"=>"clearmemory","b"=>"updatemessagememory"),
                           "chongzhi" => array("a"=>"dycz","b"=>"dyczfs","c"=>"dyczjl","d"=>"ucpayid","e"=>"iosczcplb","f"=>"iosczjl","g"=>"uczfsq","h"=>"dyszfcz","i"=>"dyszfczjl","j"=>"hqwbxx","k"=>"bdsj","l"=>"dxcz","m"=>"yszfsq"),
                           "dalei" => array("a" => "hqltxx","b"=>"wdph","c"=>"qybm","d"=>"tsjw","e"=>"wdph10","f"=>"dlt","g"=>"dhcl","h"=>"zjcs","i"=>"jscd","j"=>"lthf",'k'=>'sxds','l'=>'hqdlzr','m'=>'dhlb','n'=>'dh','o'=>'pm','p'=>'lqxt','q'=>'hqgmdlxx'),
                           "zyd" => array("a"=>"zydlb","b"=>"wfjq","c"=>"dfjq","d"=>"sxzyd","e"=>"zydzd","f"=>"zydcf","g"=>"zydjs","h"=>"zydxq","i"=>"sqzydsy","j"=>"ldzh","k"=>"jxj","l"=>"zlzydzt","m"=>"zlzydxq","n"=>"yxb","o"=>"zlzydgjdl","p"=>"zlzydzkxx"),
                           'hd'=>array('a'=>'lqhdjl','b'=>'hqhdlb','c'=>'dhdj','d'=>'lqpfjl','e'=>'hqbdylnr','f'=>'lqbdyl'),
                           'rank'=>array('a'=>'qqzcxx','b'=>'qqzcxq','c'=>'qqzczd','d'=>'qqzcyxb','e'=>'qqyxbzd', 'f'=>'qqzctsxx','g'=>'hqzgxx'),
                           'achievements'=>array('a'=>'hqcjdlxx', 'b'=>'hqcjxlxx', 'c'=>'hqcjxlfx'),
);                              //事件

// 游戏webseveice配置
define('WS_USER_NAME', 'admin');
define('WS_PASS', 'admin');
define('WS_KEY_PATH', '/var/key/');
define('WS_CLIENT', '192.168.1.16,192.168.1.102,192.168.1.202,192.168.1.5,192.168.1.121,192.168.1.153,192.168.1.246,127.0.0.1');
define('COINSUPLIMIT',99999999);    //铜钱上限
define('SILVERUPLIMIT',9999999);   //银票上限
define('INGOTUPLIMIT',999999);   //元宝上限

// 闪游支付日志目录(配置在游戏根目录的上级目录)
define('SYPAYLOG', 'syPayLog');
// 闪游支付API请求地址
$payServer = array(0=>array('server'=>'10.144.130.154', 'port'=>'12004'),
                  1=>array('server'=>'10.144.131.26', 'port'=>'12004'));

// 闪游增加勇士礼包名单
define('SYYSLB_WHITELIST', 'a2f58fea20af8ef3e78150edb032bdcc,bd6e907213c4c63f87acf9793fffdbd7,d4da64375ec312ee8a65b3cde2c08a0b,36ff232838f64dea4a14ca1ebc34ce15,48774bb2599b2810a7df3f9cba670401,48fab34144df0cd958bdc29f1e7c36b8,4635fb2a8fab1b9f32d2564326907fc9,aaf09539a131142c49a66609cebce651,702963a77b27e8eba0d295e9eb894c30,0588ab1bd344c29ecd7b53dbee2fd084,da33c9a5c25360d185e6e53429055d11,a5f0419b0f07d35a35b565c510db2db9,107ae888ee8a5319fe8182760a1c523b,ceef528e3669119b685cde20c914c34a,c65c035633c7aa205390ec3960298482,23489e3e44d81e43a59fca35440818fa,4dacbdbadb2624d16f1773847a548342,5073e19953ef6971e132cfe288135d0e,d1072ad60fe137895dde33d601eb703d,3cc942309848767530f4d714bc8e80c1,ad1f6a0cb6b5d0b9ea44c20be316289a,832427b37ab8dafc7e054122dfaae591,5808e2a24b56df440e6fb10b0e9eb21a,2fbff4830001095f77d6340703db8aef,642c1c18ec061bf75074d5aefa95b486,f35702c648f6b789b9eddb68fb982b3b,6483ced6fe77eb3850c902ef00bb4096,d02520c7590c0f9fd6f9029c1c41a5c8,0aa01ed063288396f52268b467e614f3,566740b391fececa3d469bff0afafdd6,3bbfe628c0cbf874ed36dc7dfb91f81a,d837bb568272d18e4d1c2382d74992c8,79c6a60a919d5302ce8dfb63bb5bc471,39877cad7aea09f753cbb4ba8b5af2e0,b1e748d28e33a8bc3f953bc1e2364738,ba6a6ca2fa98bf1ac79cd94f041cca23,62010b863e5d5f6e5be53faa4fa857a8,8cf791e87e91f5cdf85d788053a026b5,dd62dee4bddc7f18e7671183926a4d70,64ed3b631f4391acd88ac667a5dee888,be5a8f1a4a8e6927a90d6af9385b6723');

$_LOG_INFO['split_t'] = 3600;

// 端游服务器是否需要用户进行激活 1需要激活 0不需要激活
define('NEED_ACTIVATE', 0);

// 端游用户充值返还是否开启 1开启 0关闭
define('NEED_RESTITUTION', 0);

// 世界频道聊天相关
define('TOTAL_WORLD_CHAT_RECORD', 30); // 最大条数
define('SHOW_WORLD_CHAT_MESSAGE_NUM', 3); // 每页显示条数

// 切换易宝支付与神州付支付 1是易宝 2是神州付
define('INGAME_PAY_WAY', 1);

// 开服时间
define('KFSJ', 1341763200);
//擂台结算时间(小时，这里指第一次开放的时间。实际结算时间是这个时间减30分)
define('LEITAI_JIESUAN_SHIJIAN', 4);
//1是积分模式，2是元宝模式
define('VIP_CREDITS_CONTROL', 2);

define('LEITAI_WEEK_DAY', 4);

// 发放特权礼包 0 关闭 1 开启
define('SYDD_TQLB', 0);

// 切换语言文件 cn 简体 tw 繁体 en英文
define('LANG_FLAG', 'cn');

// 社交调用类型 sy 闪游
define('SOCIAL_TYPE', 'sy');

// 能否支持邀请码,true支持,false不支持
$_SC['canReq'] = false;

//是否开启礼品码
$_SC['lbhd'] = 0;

//论坛地址
$_SC['ltdz'] = 'http://ucs.jofgame.com/authserver/forum/luntan.php';

//版本检测提示
$apkv_SC['message'] = "请点击这里升级：\n下载安装包";
$apkv_SC['links_a'] = 'http://test_a.com';
$apkv_SC['links_i'] = 'http://test_i.com';
//支付中心地址
$_SC['zfzx'] = 'http://ucs.jofgame.com/paycenter/index.php';
//渠道版本设置
function qdbb($qd) {
        $qdversion = array(
                //'wdj1_1' => array('swf_version'=>'1.371','apkvs'=>'1.371','links_a'=>array(array('http://test_a.com','1','100')),'links_i'=>array(array('http://test_a.com','1','100')))
        );
        if (isset($qdversion[$qd])) {
                return $qdversion[$qd];
        } else {
                return false;
        }
}

define("SMS_ADDR", 'http://119.97.226.138:5218/smsAny.asp');
define('SMS_KEY', '123456'); // 短信网关加密key

//奖励手机号绑定开始时间
$_SC['bind_award_time'] = '2013-10-15 00:00:00';

//memcache实际构造类名称
$_SC['mem_type'] = 'Memcached';

// 数据交换中心url路径
$_SC['data_exchange_url'] = 'http://192.168.1.61:8080/authserver/DataExchangeCenter/';
?>
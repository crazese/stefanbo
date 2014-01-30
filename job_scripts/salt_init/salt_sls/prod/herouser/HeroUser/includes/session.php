<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
//===========================================
// 程序：   Memcache-Based Session Class
// 功能：   基于Memcache存储的 Session 功能类
// 作者:    yejr
// 网站:    http://imysql.cn
// 时间:    2007-01-05
//===========================================

/**
 * 文件:    MemcacheSession.inc.php
 * 类名:    MemcacheSession Class
 * 功能:    自主实现基于Memcache存储的 Session 功能
 * 描述:    这个类就是实现Session的功能，基本上是通过
 *          设置客户端的Cookie来保存SessionID，
 *          然后把用户的数据保存在服务器端，最后通过
 *          Cookie中的Session Id来确定一个数据是否是用户的，
 *          然后进行相应的数据操作
 *
 *          本方式适合Memcache内存方式存储Session数据的方式，
 *          同时如果构建分布式的Memcache服务器，
 *          能够保存相当多缓存数据，并且适合用户量比较多并发比较大的情况
 *
 * 注意: 本类必须要求PHP安装了Memcache扩展或者必须有Memcache的PHP API
 *       获取Memcache扩展请访问: http://pecl.php.net
 */

//设定 SESSION 有效时间，单位是 秒
define('SESS_LIFTTIME', 3600);

//定义memcache配置信息
define('MEMCACHE_HOST', '127.0.0.1');
define('MEMCACHE_PORT', '11211');

if (!defined('MemcacheSession'))
{
    define('MemcacheSession',    TRUE);

class MemacheSession
{

    // {{{ 类成员属性定义
    static  $mSessSavePath;
    static  $mSessName;
    static  $mMemcacheObj;
    // }}}

    // {{{ 初始化构造函数
    /**
     * 构造函数
     *
     * @param string $login_user    登录用户
     * @param int $login_type       用户类型
     * @param string $login_sess    登录Session值
     * @return Esession
     */
    public function __construct()
    {
        //我的memcache是以php模块的方式编译进去的，可以直接调用
        //如果没有，就请自己包含 Memcache-client.php 文件
        global $mc;
        /*if (!class_exists('Memcache') || !function_exists('memcache_connect'))
        {
            die('Fatal Error:Can not load Memcache extension!');
        }

        if (!empty(self::$mMemcacheObj) && is_object(self::$mMemcacheObj))
        {
            return false;
        }

        self::$mMemcacheObj = new Memcache;

        if (!self::$mMemcacheObj->connect(MEMCACHE_HOST , MEMCACHE_PORT))
        {
            die('Fatal Error: Can not connect to memcache host '. MEMCACHE_HOST .':'. MEMCACHE_PORT);
        }*/
        self::$mMemcacheObj = $mc;
        return TRUE;
    }
    // }}}

    /** {{{ sessOpen($pSavePath, $name)
     *
     * @param   String  $pSavePath
     * @param   String  $pSessName
     *
     * @return  Bool    TRUE/FALSE
     */
    public function sessOpen($pSavePath = '', $pSessName = '')
    {
        self::$mSessSavePath    = $pSavePath;
        self::$mSessName        = $pSessName;
        return TRUE;
    }
    // }}}

    /** {{{ sessClose()
     *
     * @param   NULL
     *
     * @return  Bool    TRUE/FALSE
     */
    public static function sessClose()
    {
        return TRUE;
    }
    // }}}

    /** {{{ sessRead($wSessId)
     *
     * @param   String  $wSessId
     *
     * @return  Bool    TRUE/FALSE
     */
    public static function sessRead($wSessId = '')
    {
        $wData = self::$mMemcacheObj->get($wSessId);

        //先读数据，如果没有，就初始化一个
        if (!empty($wData))
        {
            return $wData;
        }
        else
        {
            //初始化一条空记录
            $ret = self::$mMemcacheObj->set($wSessId, '', 0, SESS_LIFTTIME);

            if (TRUE != $ret)
            {
                die("Fatal Error: Session ID $wSessId init failed!");

                return FALSE;
            }

            return TRUE;
        }
    }
    // }}}

    /** {{{ sessWrite($wSessId, $wData)
     *
     * @param   String  $wSessId
     * @param   String  $wData
     *
     * @return  Bool    TRUE/FALSE
     */
    public static function sessWrite($wSessId = '', $wData = '')
    {
        $ret = self::$mMemcacheObj->replace($wSessId, $wData, 0, SESS_LIFTTIME);

        if (TRUE != $ret)
        {
            die("Fatal Error: SessionID $wSessId Save data failed!");

            return FALSE;
        }

        return TRUE;
    }
    // }}}

    /** {{{ sessDestroy($wSessId)
     *
     * @param   String  $wSessId
     *
     * @return  Bool    TRUE/FALSE
     */
    public static function sessDestroy($wSessId = '')
    {
        self::sessWrite($wSessId);

        return FALSE;
    }
    // }}}

    /** {{{ sessGc()
     *
     * @param   NULL
     *
     * @return  Bool    TRUE/FALSE
     */
    public function sessGc()
    {
        //无需额外回收,memcache有自己的过期回收机制

        return TRUE;
    }
    // }}}

    /** {{{ initSess()
     *
     * @param   NULL
     *
     * @return  Bool    TRUE/FALSE
     */
    public function initSess()
    {
        //$domain = '192.168.1.121';

        //不使用 GET/POST 变量方式
        ini_set('session.use_trans_sid',    0);

        //设置垃圾回收最大生存时间
        ini_set('session.gc_maxlifetime',   SESS_LIFTTIME);

        //使用 COOKIE 保存 SESSION ID 的方式
        ini_set('session.use_cookies',      1);
        ini_set('session.cookie_path',      '/');

        //多主机共享保存 SESSION ID 的 COOKIE
        //ini_set('session.cookie_domain',    $domain);

        //将 session.save_handler 设置为 user，而不是默认的 files
        session_module_name('user');

        //定义 SESSION 各项操作所对应的方法名：
        session_set_save_handler(
                array('MemacheSession', 'sessOpen'),   //对应于静态方法 My_Sess::open()，下同。
                array('MemacheSession', 'sessClose'),
                array('MemacheSession', 'sessRead'),
                array('MemacheSession', 'sessWrite'),
                array('MemacheSession', 'sessDestroy'),
                array('MemacheSession', 'sessGc')
                );

        session_start();

        return TRUE;
    }
    // }}}

}//end class

}//end define
?>

<?php 
//echo dirname(__FILE__);
//exit;
//需要配置PHP.INI文件，打开extension=php_openssl.dll 
include 'rsaclass.php'; 
//以下是一个简单的测试demo，如果不需要请删除
$rsa = new Rsa(dirname(__FILE__)); //放项目的PHP目录 
$rsa->createKey();
//私钥加密，公钥解密
echo 'source：Testing:Hello World!<br />'; 
$pre = $rsa->privEncrypt('Testing:Hello World!'); 
echo 'Private Encrypted:' . $pre . '<br />'; 
$pud = $rsa->pubDecrypt($pre); 
echo 'Public Decrypted:' . $pud . '<br />'; 
//公钥加密，私钥解密
echo 'source:working in here!<br />'; 
$pue = $rsa->pubEncrypt('working in here!');
echo 'Public Encrypt:' . $pue . '<br />'; 
$prd = $rsa->privDecrypt($pue); 
echo 'Private Decrypt:' . $prd; 
?>
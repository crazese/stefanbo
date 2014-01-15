<?
@header("Content-Type:image/png");
function decode($code, $seed = 'sothink.com', $safe = true){
       preg_match_all("/.{2}/", $code, $arr);
       $arr = $arr[0];
       $s_m = md5($seed);
       $s_l = strlen($m);
       $a = 0;
       foreach ($arr as $value){
       @$str .= chr(base_convert($value,32,10)-ord($s_m{$s_l % $a+1}));
       $a++;
       }
       if ($safe) $str = str_rot13(strrev(base64_decode($str)));
       return $str;
}

$str = decode($_GET['code']);
$width = 50; //验证码图片的宽度
$height = 25; //验证码图片的高度
$im=imagecreate($width,$height);
//背景色
$back=imagecolorallocate($im,0xFF,0xFF,0xFF);
//模糊点颜色
$pix=imagecolorallocate($im,187,230,247);
//字体色
$font=imagecolorallocate($im,41,163,238);
//绘模糊作用的点
mt_srand();
for($i=0;$i<1000;$i++)
{
imagesetpixel($im,mt_rand(0,$width),mt_rand(0,$height),$pix);
}
imagestring($im, 5, 7, 5,$str, $font);
imagerectangle($im,0,0,$width-1,$height-1,$font);
imagepng($im);
imagedestroy($im);
//$_SESSION["code"] = $str;
//return $str;
//}

?>
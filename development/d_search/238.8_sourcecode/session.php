<?php
      session_start();
	  function encode($code,$seed = "sothink.com", $safe = true){
      if ($safe) $code = base64_encode(strrev(str_rot13($code)));
      $c_l = strlen($code);
      $s_m = md5($seed);
      $s_l = strlen($m);
      $a=0;
      while ($a <$c_l){
      @$str .= sprintf ("%'02s",base_convert(ord($code{$a})+ord($s_m{$s_l % $a+1}),10,32));
      $a++;
      }
      return $str;
      }	  
	  function randoms($stat, $end, $num) 
      {
          $t = range($stat, $end);
          shuffle($t);
          return array_slice($t, -$num);
      }
	  $code = randoms(1000, 9999, 1);
	  $code = $code[0];  
	  $_SESSION['code'] = $code;
	  echo encode($code);
?>
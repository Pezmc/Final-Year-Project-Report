<?php

$timeTarget = 1;
ini_set('memory_limit', '-1');

$strings = array();
for($i = 0; $i < 3000000; $i++) {
	$strings[$i] = randomString(16);
}

$hashingFunctions = array('MD5', 'SHA1', 'BCRYPT');

foreach($hashingFunctions as $hash) {
  for($i = 0; $i < 10; $i++) {
  	$start = microtime(true);
  	$count = 0;
  	do {
      $count++;
      
      if($hash == $hashingFunctions[0])
        md5($strings[$count]);
      elseif($hash == $hashingFunctions[1])
        sha1($strings[$count]);
      elseif($hash == $hashingFunctions[2])
        password_hash($strings[$count], PASSWORD_BCRYPT);
      else
        echo "ERROR: Unknown function";
    
      $end = microtime(true);
  	} while (($end - $start) < $timeTarget);
  	echo "$hash\t$count\n";
  }
}

function randomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

<?php

$informtion = "mijn bericht";
$cipher = 'aes-128-gcm';
$secret = '1234567890123456';
$ivlen = openssl_cipher_iv_length($cipher);
$iv = openssl_random_pseudo_bytes($ivlen);


$encryptString = openssl_encrypt($informtion,$cipher,$secret,0,$iv,$ivlen);

//echo $encryptString .'<br>';


$decryptString = openssl_decrypt($encryptString,$cipher,$secret,0,$iv,$ivlen);
echo $decryptString;



?>
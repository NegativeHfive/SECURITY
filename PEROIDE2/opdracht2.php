<?php

include_once '../vendor/kkiernan/caesar-cipher/src/KKiernan/CaesarCipher.php';

$shiftedNumber = 10;
$stringText = "Apple is good ";

$cipher = new KKiernan\CaesarCipher();

$ciphertext = $cipher->encrypt($stringText,$shiftedNumber);
$plaintext = $cipher->decrypt($ciphertext,$shiftedNumber);

echo 'Original text : '. $stringText . '<br>';
echo 'Encrypted Text :'. $ciphertext . '<br>';
echo 'Decrypted Text : '. $plaintext.  '<br>';


?>


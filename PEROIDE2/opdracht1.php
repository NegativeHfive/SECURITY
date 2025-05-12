<?php

$password = 'password1234';
$salt = 'nmuioas912fdfsf32';

$saltedPassword = md5($salt . $password);
echo md5($saltedPassword)

?>
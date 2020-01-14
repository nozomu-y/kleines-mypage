<?php
require('/home/chorkleines/www/member/mypage/Core/config.php');
require('/home/chorkleines/www/member/mypage/Class/User.php');

$mysqli = new mysqli($host, $username, $password, $dbname);
if ($mysqli->connect_error) {
    error_log($mysqli->connect_error);
    exit;
}

<?php
require '../core/common.php';


$data = json_decode(file_get_contents('php://input'), true);
$login = $data['login'];
$img = $data['data'];
$img = str_replace('data:image/jpeg;base64,', '', $img);
$img = str_replace(' ', '+', $img);
$data = base64_decode($img);
$file = "../images/user_icons/" .$login . '.jpeg';
$success = file_put_contents($file, $data);

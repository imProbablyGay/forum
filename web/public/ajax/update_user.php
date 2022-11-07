<?php
require_once '../core/common.php';

$data = json_decode(file_get_contents('php://input'), true);
$login = $data['login'];
$email = $data['email'];
$u_id = $_SESSION['user']['id'];

execQuery("UPDATE `users` SET `login`='$login',`email`='$email' WHERE id = $u_id");

$_SESSION['user']['login'] = $login;
$_SESSION['user']['email'] = $email;
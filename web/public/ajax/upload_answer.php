<?php
session_start();
require '../core/common.php';
$data = json_decode(file_get_contents('php://input'), true);
$message = $data[0];
$q_id = $data[1];
$sender = $data[2];
$author = select("SELECT author FROM questions WHERE id = $q_id")[0]['author'];
$time = date('d.m.Y, H:i',time());
$user = $_SESSION['user']['login'];
$u_id = $_SESSION['user']['id'];
$time = date('d.m.Y, H:i',time());

execQuery("INSERT INTO `answers`(`time`, `message`, `sender`, `question_id`) VALUES ('$time', '$message', '$sender', '$q_id')");

$answer_id = select("SELECT `id` FROM `answers` WHERE `sender` = '$sender' AND `time` = '$time' AND `question_id` = '$q_id' ORDER BY `id` DESC LIMIT 1")[0]['id'];

// notifications
execQuery("INSERT INTO `notifications`(`user_id`, `question_id`,`answer_id`, `type`, `time`, `author`) VALUES ('$sender', '$q_id', $answer_id, 'answer', '$time', '$author')");

$comm = [
    "message" => $message,
    "sender" => $sender,
    "time" => $time,
    "id" => $answer_id,
    "login" => $user,
    "uID" => $u_id,
    "is_liked" => ''
];

$out = draw_answer($comm, 0, 0);

echo($out);
<?php
session_start();
require '../core/common.php';
$data = json_decode(file_get_contents('php://input'), true);

// upload question table
$title = $data['title'];
$description = addslashes($data['description']);
$images = implode(' ',$data['images']);
$user = $_SESSION['user']['id'];
$date = date('d.m.Y, H:i',time());
execQuery("INSERT INTO `questions`(`title`, `description`, `images`, `author`, `date`, `views`) VALUES ('$title','".$description."','$images', '$user', '$date', 0)");

$questionID = select("SELECT `id` FROM `questions` WHERE `author` = '$user' ORDER BY `id` DESC LIMIT 1")[0]['id'];
print_r($questionID);

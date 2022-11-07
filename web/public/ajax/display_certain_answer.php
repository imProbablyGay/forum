<?php
require '../core/common.php';
$data = json_decode(file_get_contents('php://input'), true);
$q_id = $data['q_id'];
$a_id = $data['a_id'];
$user_id = $_SESSION['user']['id'];

$out = select("SELECT a.id,a.message,a.time,a.sender,u.login,u.id AS uID 
FROM answers AS a INNER JOIN users AS u ON a.sender = u.id WHERE a.question_id = $q_id AND a.id = $a_id")[0];

$out = get_answer($out, $user_id, $q_id);
echo $out;
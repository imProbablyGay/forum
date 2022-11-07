<?php
require '../core/common.php';
$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user'];
$a_id = $data['answer_id'];
$q_id = $data['question_id'];
$r_id = $data['reply_id'] ?? "NULL";
$c_id = $data['comment_id'] ?? "NULL";
$reply = $data['reply'];
$time = date('d.m.Y, H:i',time());
$author = $data['author'];
$check_liked_comm_clause = "&& `parent_id` IS NULL";
$is_reply = $comm = [
    2 => "IS NULL "
];
if ($c_id != "NULL") {
    $comm_id = $c_id;
    $notif_reply_id_clause = "IS NULL";
    if($r_id != "NULL") {
        $comm_id = $r_id;
        $notif_reply_id_clause = "= $r_id";
    }

    $comm = [
        ",`parent_id`",
        ",$comm_id",
        "= $comm_id"
    ];

    if ($reply) {
        $is_reply = [
            ",`is_reply`",
            ",$reply",
            "=$reply"
        ];
    }

    $notif_comm_clause = "AND `parent_id` = $c_id AND `comment_reply_id` $notif_reply_id_clause";
    $check_liked_comm_clause = "&& `parent_id` = $comm_id && `is_reply` $is_reply[2]";
}
$already_liked = select("SELECT id FROM likes WHERE `user_id`=$user_id AND `answer_id`=$a_id $check_liked_comm_clause")[0];
if (!$already_liked) {
    execQuery("INSERT INTO likes (`user_id`, `question_id`, `answer_id` $comm[0] $is_reply[0]) VALUES ('$user_id','$q_id','$a_id' $comm[1] $is_reply[1])");
    execQuery("INSERT INTO notifications (`user_id`,`answer_id`, `type`,`time`, `author`, `question_id`, `parent_id`, `comment_reply_id`) VALUES ('$user_id','$a_id','like', '$time', '$author' , $q_id, $c_id, $r_id)");
}
else {
    execQuery("DELETE FROM likes WHERE `user_id`=$user_id AND `question_id`=$q_id AND `answer_id`=$a_id AND `parent_id` $comm[2] AND `is_reply` $is_reply[2]");
    execQuery("DELETE FROM notifications WHERE `user_id`=$user_id AND `question_id`=$q_id AND `answer_id`=$a_id AND `type` = 'like' $notif_comm_clause");
}
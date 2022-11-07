<?php

require '../core/common.php';

$data = json_decode(file_get_contents('php://input'), true);
$q_id = $data['question_id'];
$a_id = $data['answer_id'];
$c_id = $data['comment_id'];
$user = $data['user'];
$author = $data['author'];
$comment = addslashes($data['message']);
$time = date('d.m.Y, H:i',time());
$type = $data['type'];
$comment_id = [];
$reply = [];
if ($c_id) {
    $comment_id = [
        ", `parent_id`",
        ", $c_id"
    ];

    //handle comment reply
    $reply[0] = ", `comment_reply_id`";
}

// upload comm to db
$query = "INSERT INTO comments(`time`, `comment`, `sender`, `author`, `question_id`, `answer_id` $comment_id[0]) VALUES ('$time', '$comment', '$user', '$author', $q_id, '$a_id' $comment_id[1])";
execQuery($query);
//notifications


$uploaded_c_id = select("SELECT id FROM comments WHERE sender = $user AND time = '$time' AND comment = '$comment'")[0]['id'];
if (!$c_id) {
    // leave comment
    $notif_query = "INSERT INTO notifications(`user_id`, `answer_id`, `question_id`, `type`, `time`, `parent_id`, `author`) VALUES ('$user','$a_id','$q_id', 'comment', '$time', $uploaded_c_id, '$author')";
}
else {
    $notif_query = "INSERT INTO notifications(`user_id`, `answer_id`, `question_id`, `type`, `time`, `parent_id`,`comment_reply_id`, `author`) VALUES ('$user','$a_id','$q_id', 'comment', '$time', $c_id, $uploaded_c_id, '$author')";
}
execQuery($notif_query);



// display comm
$uploaded_comment = select("SELECT c.*,u.login FROM comments AS c INNER JOIN users AS u ON c.sender = u.id WHERE `sender` = '$user' AND `time` = '$time' ORDER BY `id` DESC LIMIT 1")[0];
$view = [
    "liked" => '',
    "like_count" => 0,
    "comm_count" => 0,
    "type" => $type
];

$uploaded_comment = draw_comment($uploaded_comment, $view);

echo $uploaded_comment;
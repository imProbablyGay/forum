<?php
require '../core/common.php';
$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user'];
$a_id = $data['answer_id'];
$q_id = $data['question_id'];
$c_id = $data['comment_id'];
$r_id = $data['reply_id'];
$reply = $data['reply'];

if (!$a_id && !$c_id) {
    // if delete question
    execQuery("DELETE FROM questions WHERE id = $q_id");
    execQuery("DELETE FROM answers WHERE question_id = $q_id");
    execQuery("DELETE FROM comments WHERE question_id = $q_id");
    execQuery("DELETE FROM likes WHERE question_id = $q_id");
    execQuery("DELETE FROM notifications WHERE question_id = $q_id");
}
else if ($a_id && !$c_id) {
    // delete answer
    execQuery("DELETE FROM answers WHERE id = $a_id");
    execQuery("DELETE FROM comments WHERE answer_id = $a_id"); // comment answers query
    execQuery("DELETE FROM likes WHERE answer_id = $a_id"); // comment answers query
    execQuery("DELETE FROM notifications WHERE answer_id = $a_id"); //notif query
}
else if ($c_id) {
    // common comment
    if ($reply != 1) {
        execQuery("DELETE FROM comments WHERE id = $c_id");
        execQuery("DELETE FROM comments WHERE parent_id = $c_id"); // comment answers query
        execQuery("DELETE FROM notifications WHERE parent_id = $c_id"); //notif query
        execQuery("DELETE FROM likes WHERE parent_id = $c_id"); // comment answers query
    }
    // comment reply
    else {
        execQuery("DELETE FROM comments WHERE id = $r_id");
        execQuery("DELETE FROM notifications WHERE parent_id = $c_id && comment_reply_id = $r_id"); //notif query
        execQuery("DELETE FROM likes WHERE parent_id = $r_id"); // comment answers query
    }
}

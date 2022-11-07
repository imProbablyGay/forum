<?php
require '../core/common.php';
$notif_id = json_decode(file_get_contents('php://input'), true)['notif_id'];

execQuery("UPDATE notifications SET is_seen = 1 WHERE id = $notif_id");

// get notification data
$notif = select("SELECT * FROM notifications WHERE id = $notif_id")[0];
$type = $notif['type'];
$out = [
    "answer_id" => $notif['answer_id'],
    "question_id" => $notif['question_id'],
    "parent_id" => $notif['parent_id'],
    "comment_reply_id" => $notif['comment_reply_id'],
    "type" => ""
];

if ($type == 'like') {
    if ($notif['comment_reply_id']) $out['type'] = 'comment_answer';
    else if ($notif['parent_id']) $out['type'] = 'comment';
    else if ($notif['answer_id']) $out['type'] = 'answer';
}
else if ($type == 'answer') $out['type'] = 'answer';
else if ($type == 'comment' && $notif['comment_reply_id']) $out['type'] = 'comment_answer';
else if ($type == 'comment') $out['type'] = 'comment';

echo json_encode($out);
<?php
require '../core/common.php';
$data = json_decode(file_get_contents('php://input'), true);
$a_id = $data['a_id'];
$c_id = $data['c_id'];
$login = $data['user_id'];
$table = $data['table'];
$type = $data['type'];

$is_null = check_cell_null($c_id);
$query = "SELECT c.id,c.time,c.comment,c.sender,u.login FROM comments AS c INNER JOIN users AS u ON c.sender = u.id WHERE `answer_id`='$a_id' && `parent_id` $is_null";
// select comments from db
$comments = [];
if (count($data)>1) $comments = select($query);

// form comments
$out = "<div class='comments__new'><textarea placeholder='Напишите комментарий'></textarea><br><span class='comment__send login-needed'>Отправить</span></div><div class='comments__area'>";//comments
if ($type == 'comment_answer') $out = "<div class='comments__new'><textarea placeholder='Ответить'></textarea><br><span class='comment__send send-comment-answer login-needed'>Отправить</span></div></div><div class='comments__area'>"; //comment answers
else if ($type == 'comment_reply') {//comment reply
    $out = "<div class='comments__new'><textarea placeholder='Ответить'></textarea><br><span class='comment__send send-comment-answer reply login-needed'>Отправить</span></div>";
    echo $out;
    die;
}
else if ($login == null) $out = "<div class='comments__register'><span>Вы должны быть зарегестрированы, чтобы оставлять комментарии <a href='/login'>Логин</a></span></div><div class='comments__area'>";//not logined

// if there is no comments/replies
if (count($comments) == 0) {
    $out .= "<h5>Здесь еще нет комментариев</h5></div>";
    echo $out;
    die;
}

// if it is comment reply, add reply argument
$is_reply;
if ($type == 'comment_answer') $is_reply = 1;
foreach($comments as $comm) {
    // check login
    if (!$login) $liked = '';
    else $liked = is_liked($login, $a_id, $comm['id'], $table, $is_reply);

    // get comm view data
    $view = [
        "liked" => $liked,
        "like_count" => get_count($a_id ,$comm['id'],'likes', $is_reply),
        "comm_count" => get_count($a_id ,$comm['id'],'comments', $is_reply),
        "type" => $type
    ];
    $out .= draw_comment($comm, $view);
}

$out .= '</div>';
echo $out;


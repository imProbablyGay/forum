<?php
require '../core/common.php';
require '../core/LoadMore.php';

$data = json_decode(file_get_contents('php://input'), true);
$query = $data['query'];
$limit = $data['limit'];
$type = $data['type'];
$user_id = $data['user_id'];
$author = $_SESSION['user']['id'];
$limit_args = $limit.','.LoadMore::$show_limit;

// default answer element
$display = new CommonQuestion();

// default search in navbar
if ($type == 'search_page') {
    $search_query_count = "SELECT count(*) AS count FROM questions WHERE title LIKE '%$query%'";
    $search_query = "SELECT q.id,q.title,q.date,q.views,u.login,u.id AS uID FROM questions AS q INNER JOIN users AS u ON q.author = u.id WHERE title LIKE '%$query%' ORDER BY views DESC LIMIT $limit_args";
}
// search page
else if ($type == 'main_page') {
    $cur_date = date('m.Y'); 
    $search_query_count = "SELECT count(*) AS count FROM questions WHERE date LIKE '%$cur_date%'";
    $search_query = "SELECT q.id,q.title,q.date,q.views,u.login,u.id AS uID FROM questions AS q INNER JOIN users AS u ON q.author = u.id WHERE date LIKE '%$cur_date%' ORDER BY views DESC LIMIT $limit_args";
}
//profile page NOT NOTIFICATIONS
else if ($type == 'profile_page') {
    $search_query_count = "SELECT count(*) AS count FROM questions WHERE author = $user_id";
    $search_query = "SELECT q.id,q.title,q.date,q.views,u.login,u.id AS uID FROM questions AS q INNER JOIN users AS u ON q.author = u.id WHERE author = $user_id ORDER BY views DESC LIMIT $limit_args";
}
// notifications
else if ($type == 'notifications_page') {
    $display = new Notification();
    $search_query_count = "SELECT count(*) AS count FROM notifications WHERE author = $author";
    $search_query = "SELECT n.id AS nID,n.parent_id,n.comment_reply_id,n.time,n.question_id as id,n.answer_id,n.is_seen,n.type,n.user_id,u.login,u.id AS uID FROM notifications AS n INNER JOIN users AS u ON u.id = n.user_id WHERE author = $author ORDER BY n.id DESC LIMIT $limit_args";
}

start_class();

function start_class() {
    global $limit, $display, $search_query, $search_query_count;
    $search = new LoadMore($limit, $display);
    $search -> search_query_count = $search_query_count;
    $search -> search_query = $search_query;
    $search -> count();
    $displayS = $search -> display();

    echo $displayS;
    return;
}
<?php
require '_header.php';
require './core/LoadMore.php';

$user = $_SESSION['user']['id'];
$display = new Notification();
$new_notif_count = $display -> get_new_notif_count($user);

$search = new LoadMore(null, $display);
$search -> search_query_count = "SELECT count(*) AS count FROM notifications WHERE author = $user";
$search -> count();
$search -> search_query = "SELECT n.id AS nID,n.parent_id,n.comment_reply_id,n.time,n.question_id as id,n.answer_id,n.is_seen,n.type,n.user_id,u.login,u.id AS uID FROM notifications AS n INNER JOIN users AS u ON u.id = n.user_id WHERE n.author = $user ORDER BY n.id DESC LIMIT $search->limit_args";
$displayS = $search -> display();

?>


<body>
    <div class="container search search-block" data-search-type='notifications_page'>
        <div class="row">
            <div class="col-12">
                <h4>Новые уведомления: (<?=$new_notif_count?>)</h4>
                <hr>
            </div>
            <?=$displayS?>
        </div>
    </div>
</body>
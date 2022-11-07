<?php
require_once '_header.php';
require_once './core/Profile.php';

$user_id = filter_var($url[1], FILTER_SANITIZE_NUMBER_INT);
$profile = new Profile($user_id);
$user_info = $profile->display_title();

$display = new CommonQuestion();

$profile_q = new LoadMore(null, $display);
$profile_q -> search_query_count = "SELECT count(*) AS count FROM questions WHERE author = $user_id";
$profile_q -> count();
$profile_q -> search_query = "SELECT q.id,q.title,q.date,q.views,u.login,u.id AS uID FROM questions AS q INNER JOIN users AS u ON q.author = u.id WHERE author = $user_id ORDER BY views DESC LIMIT $profile_q->limit_args";
$user_questions = $profile->display_questions($profile_q);
?>

<body>
    <div class="container profile search-block" data-search-type='profile_page'>
        <div class="row">
            <div class="col-12 profile__title">
                <?=$user_info?>
                <br>
            </div>
        </div>
        <div class="profile__questions row">
            <?=$user_questions?>
        </div>
    </div>

    <!-- modal -->
    <script src='../js/profileEdit.js' defer></script>
</body>



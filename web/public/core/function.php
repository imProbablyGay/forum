<?php
session_start();

function connect(){
    $conn = mysqli_connect(SERVER, USERNAME, PASSWORD, DB);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    mysqli_set_charset($conn, "utf8");
    return $conn;
}

function select($query) {
    global $conn;
    $queryResult = [];
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $queryResult[] = $row;
        }
    }
    return $queryResult;
}

function execQuery($query) {
    global $conn;
    if (mysqli_query($conn, $query)){
        return true;
    }
    DBerror($query, mysqli_error($conn),mysqli_errno($conn));
    return false;
}

function DBerror($query,$msg,$errno){
    echo "<b> MySQL error".$errno."<br>".htmlspecialchars($msg)."<br>".$query."<hr>";
    }

function drawLogin() {
    $out = "<div class='btn-group'><a class='login' href='/login'>Логин</a></div>";

    // check cookie
    $login = $_SESSION['user']['id'];

    if (!$login) return $out;

    $user = select("SELECT * FROM users WHERE id = '$login'");
    if (!$user) return $out;

    $out = '
        <div class="btn-group">
            <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <img id="icon-img" src="../images/user_icons/'.$user[0]['id'].'.jpeg">
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
            <a class="dropdown-item logout-btn" href="/logout">Выйти</a>
            <a class="dropdown-item change-icon-btn" href="/profile/'.$user[0]['id'].'">Профиль</a>
            <a class="dropdown-item change-icon-btn" href="/question/new">Задать вопрос</a>
            </ul>
        </div>';
   
    return $out;
}

function notif_count() {
    $login = $_SESSION['user']['id'];
    if (!$login) return;

    // check notifications
    $notif = select("SELECT count(*) AS count FROM notifications WHERE author = $login && is_seen IS NULL")[0]['count'];
    if ($notif > 9) $notif = "9+";
    if ($notif != 0) $notif_count = "<span class='notifications'>$notif</span>";

    return "<span>$notif_count</span>";
}

function get_count($a_id, $parent_id, $table, $is_reply = null) {
    $is_null = check_cell_null($parent_id);
    if ($table == 'likes') {
        $r = check_cell_null($is_reply);
        $is_reply = "AND is_reply $r";
    }
    else $is_reply = null;

    $query = "SELECT count(*) AS count FROM $table WHERE answer_id = $a_id AND parent_id $is_null $is_reply";
    $count = select($query)[0]['count'];

    return $count;
}

function is_liked($user_id, $a_id, $c_id, $table,$is_reply = null) {
    $is_null = check_cell_null($c_id);
    $is_reply = check_cell_null($is_reply);
    $query = "SELECT id FROM likes WHERE `user_id` = $user_id AND answer_id = $a_id AND parent_id $is_null AND is_reply $is_reply";
    $liked = select($query)[0]['id'];

    if ($liked != '') {
        return "active";
    }
    else {
        return "";
    }
}

function check_cell_null($data) {
    if ($data == null || $data == 'undefined') return "IS NULL";
    else return "= $data";
}

function draw_comment($comm, $view) {
    // difference
    $is_reply;
    $diff = [
        "class" => "comments__comment",
        "type" => "",
        "replies" => "ответы($view[comm_count])",
        "profile_link" => '',
        "data_comment" => "data-comment-id=$comm[id]"
    ];
    if ($view['type'] != '') {
        $is_reply = 'reply'; // class for delete_answer
        $diff = [
            "class" => "comments__reply",
            "type" => "comment-reply",
            "replies" => "ответить",
            "profile_link" => "<a href='/profile/$comm[sender]'>$comm[login]</a>",
            "data_comment" => "data-comment-id-like=$comm[id]",
            "comment_reply" => "reply__likes"
        ];
    }

    // delete block
    $delete = delete_answer($comm['sender'], $is_reply);
    return "<div class='$diff[class] comment' $diff[data_comment] data-author='$comm[sender]'>
            <div class='comment__author d-flex justify-content-between'>
                <div>
                    <img src='../images/user_icons/$comm[sender].jpeg'>
                    <span>$comm[login]</span>
                    <span class='comment__time'>$comm[time]</span>
                </div>
                $delete
            </div>
            <div class='comment__content'>
                $diff[profile_link]<span>$comm[comment]</span>
            </div>
            <div class='comment__footer'>
                <span class='comment__likes login-needed $diff[comment_reply] $view[liked]'>нравится $view[like_count]</span>
                <span class='comment__answers-display $diff[type]'>$diff[replies]</span> 
            </div>
            <div class='comment__answers hidden'></div>
        </div>";
}

function delete_answer($answer_user, $is_reply = '') {
    if ($_SESSION['user']['id'] === $answer_user) {
        return '
        <div class="dropdown delete-comment">
        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="true">
            <img src="../images/img/show-more.svg">
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
            <li class="dropdown-item"><span class="delete '.$is_reply.'">удалить</span></li>
        </ul>
        </div>';
    };
    return '';
}

function draw_answer($answer, $l_count, $c_count, $is_liked = '') {
    // add delete
    $delete = delete_answer($answer['sender']);
    
    // add expandable to image
    $answer['message'] = str_replace('img', 'img class="expandable"', $answer['message']);
    return "<div class='answers__new answer'  data-answer-id='$answer[id]'  data-author='$answer[sender]'>
                <div class='answer__author d-flex justify-content-between'>
                    <div>
                        <img src='../images/user_icons/$answer[uID].jpeg'>
                        <span>$answer[login]</span>
                        <p>$answer[time]</p>
                    </div>
                    $delete
                </div>
                <div class='answer__content'>
                    $answer[message]
                </div>
                <div class='answer__footer'>
                    <span class='answer__likes login-needed $is_liked'>нравится $l_count</span>
                    <span class='answer__comments-display'>комментарии ($c_count)</span>
                </div>
                <div class='answer__comments comments hidden'></div>
            </div>";
}

function get_answer($answer, $login) {
    // check login
    if ($login == null) {
        $is_liked = '';
    }
    else {
        $is_liked = is_liked($login, $answer['id'], null, 'answers');
    }

    $l_count = get_count($answer['id'],null,'likes');
    $c_count = get_count($answer['id'],null,'comments');

    return draw_answer($answer, $l_count, $c_count, $is_liked);
}
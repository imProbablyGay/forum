<?php
 class LoadMore {
    public $count;
    public static $show_limit = 4;
    public $limit_args = "0,4";
    public $search_query_count;
    public $search_query;
    public $display_class;
    
    function __construct($JS_limit = null, $display_class) {
        $this-> display_class = $display_class;
        $this-> count_displayed = self::$show_limit;
        // if add more searches by JS
        if ($JS_limit !== null) {
            $this->limit_args = "$JS_limit, ".self::$show_limit;
            $this->count_displayed = ($JS_limit+self::$show_limit);
        }
    }

    function count() {
        $this->count = select($this->search_query_count)[0]['count'];
    }

    function display() {
        $data = $this->request_data($this->search_query);
        return $this->form_output($data);
    }
    
    private function request_data($search_query) {
        return select($search_query);
    }

    private function form_output($data) {
        $out;

        foreach($data as $res) {
            // delete block
            $res['delete'] = delete_answer($res['uID']);
            $res['a_count'] = select("SELECT count(*) as count FROM answers WHERE question_id = $res[id]")[0]['count'];
            $res['link'] = "/question/$res[id]";
            $out .= $this->display_class->draw($res);
        }

        // check limit
        if ($this->count > $this->count_displayed) {
            $out .= $this->add_show_more();
        }

        return $out;
    }

    private function add_show_more() {
        return "<div class='search__show-more col-12'><span class='search-span'>Показать еще</span></div>";
    }
 }

 class CommonQuestion{
    function draw($res) {
        return "
        <div class='col-12'>
            <div class='search__question'>
                <div class='search__question-title'>
                    <div>
                        <img src='../images/user_icons/$res[uID].jpeg'>
                        <h5>$res[login]</h5>
                    </div>
                    <div data-question-id='$res[id]'>
                        <span class='d-flex align-items-center search__question-views'><object data='../images/img/views.svg' width='20' height='20'></object>$res[views] &nbsp</span>
                        <span>$res[date]</span>
                        $res[delete]
                    </div>
                </div>
                <div class='search__question-body'>
                    <a href='$res[link]'>$res[title]</a>
                    <p style='margin:0;'>$res[a_count] ответa(ов)</p>
                </div>
            </div>
        </div>";
    }
 }

 class Notification {
    function draw($res) {
        $time_dif = $this->calc_time_diff($res['time']);
        $status = $this->get_status($res['is_seen']);
        $content = $this->get_content($res);

        return "
        <div class='col-12'>
            <div class='search__question'>
                <div class='search__question-title'>
                    <div>
                    <img src='../images/user_icons/$res[uID].jpeg'>
                        <h5>$res[login]</h5>
                    </div>
                    <div class='align-items-start'>
                        $status
                        <span>$time_dif</span>
                    </div>
                </div>
                <div class='search__question-body'>
                    $content
                </div>
            </div>
        </div>";
    }

    function get_new_notif_count($user) {
        return select("SELECT count(*) AS count FROM notifications WHERE author = $user AND is_seen IS NULL")[0]['count'];
    }
    
    private function calc_time_diff($time) {
        $time_dif = time() - strtotime($time);
        $out;

        if ($time_dif < 60) $out .= 'только что</p>';
        else if ($time_dif < 3600) $out.=round($time_dif/60).' мин. назад</p>';
        else if ($time_dif < 3600*24) $out.=round($time_dif/3600).' ч. назад</p>';
        else if ($time_dif < 3600*24*31) $out.=round($time_dif/3600/24).' д. назад</p>';
        else if ($time_dif >= 3600*24*31) $out.=' давно</p>';

        return $out;
    }

    private function get_status($is_seen) {
        if ($is_seen != null) return;

        return "<span class='notification-new'>новое</span>";
    }

    private function get_content($res) {
        $out;
        if ($res['type'] == 'like') {
            $out = "<a href='$res[link]' data-notif-id='$res[nID]'>$res[login] поставил/a вам нравится</a>";
        }
        else if ($res['type'] == 'comment') {
            $out = $this->get_comment($res['answer_id'],$res['parent_id'],$res['comment_reply_id'],$res);
        }
        else if ($res['type'] == 'answer') {
            $out = $this->get_answer($res['answer_id'],$res);
        }

        return $out;
    }

    private function get_comment($a_id,$c_id,$r_id,$res) {
        $out = "<a href='$res[link]' data-notif-id='$res[nID]'>$res[login]";

        if ($r_id) $out .= " ответил вам: \"";
        else $out .= " оставил комментарий: \"";

        $c_id = $r_id ?? $c_id;
        $get_comment = select("SELECT comment FROM comments WHERE answer_id = $a_id AND id = $c_id")[0]['comment'];
        $out .= "<span style='color:black;'>$get_comment</span>\"</a>";
        return $out;
    }

    private function get_answer($a_id,$res) {
        $out = "<a class='notification-answer' href='$res[link]' data-notif-id='$res[nID]'>$res[login] ответил на ваш вопрос:";
        $get_comment = select("SELECT message FROM answers WHERE id = $a_id")[0]['message'];
        // hide images 
        if (strpos($get_comment, '<img') !== FALSE) $get_comment = '<p><i>картинка</i></p>';
        $out .= $get_comment."</a>";
        return $out;
    }
 }
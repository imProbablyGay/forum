<?php
session_start();

abstract class QuestionPage {
    protected $default_count = 3;
    
    function __construct($Q_ID) {
        $data = select("SELECT q.id,q.title,q.description,q.images,q.date,q.views,u.login,u.id AS u_id FROM questions AS q INNER JOIN users AS u ON q.author = u.id WHERE q.id = $Q_ID")[0];
        $this->data = $data;
        $this->id = $data['id'];
        $this->title = $data['title'];
        $this->description = $data['description'];
        $this->images = $data['images'];
        $this->author = $data['login'];
        $this->time = $data['date'];
        $this->login = $_SESSION['user']['id'];
        $this->views = $data['views'];
        $this->u_id = $data['u_id'];
    }
}

class Question extends QuestionPage {
    function increase_views() {
        // increase views on question
        execQuery("UPDATE questions SET views = views + 1 WHERE id = $this->id");
    }

    function not_found() {
        require './template/404.php';
    }

    function display() {
        $time = $this->time;
        $output = [];
        $output['author'] = "<div>
                                <img src='../images/user_icons/$this->u_id.jpeg'><h6>$this->author</h6>
                            </div>
                            <div>
                                <span class='views'><object data='../images/img/views.svg' witdh='20px' height='20px'></object>$this->views</span>
                                <h6 class='question__time'>$time</h6>
                            </div>";
        $output['images'] = $this->get_images();
        $output['title'] = $this->title;
        $output['description'] = $this->description;
        $this->increase_views();
        return $output;
    }

    private function get_images() {
        if ($this->images == '') return false;
        
        $out = "<div class='col-12 question__images'>
        <div class='question__images-display'>";

        foreach(explode(' ', $this->images) as $path) {
            $out.="<div class='question__images-image'><img class='expandable' src='$path'></div>";
        }

        $out .= '</div></div>';
        return $out;
    }
}

class Answers extends QuestionPage {
    // get certain amount of answers
    function get_answers($limit = null, $exclude_query = null, $show_all = false) {
        $_SESSION['answers_offset'] = $limit;
        if ($limit) $limit = "$this->default_count, 10000000";
        else {
            $limit = $this->default_count;
        }
        $query = "SELECT a.id,a.message,a.time,a.sender,u.login,u.id AS uID 
                FROM answers AS a INNER JOIN users AS u ON a.sender = u.id WHERE a.question_id = $this->id $exclude_query LIMIT $limit";
        $answers_data = select($query); 
                                
        // check if there is no answers
        $out = '';
        if (count($answers_data) == 0) {
            $out = '<h4>На этот вопрос еще никто не ответил</h4>';
            return $out;
        }

        // return answers in HTMl
        foreach($answers_data as $answer) {
            // parent method
            $out .= get_answer($answer, $this->login, $this->id);
        }

        // check if there are answers left 
        if (count($answers_data) == $limit && $show_all == FALSE) {
            $all_answers = select("SELECT count(*) AS c FROM answers WHERE question_id = $this->id")[0]['c'];
            if ($all_answers == $limit) return $out;
            $answers_left = $all_answers - $limit;

            $out .= "<div class='answers__more'><span>Показать все ответы (<span class='answers-count'>$answers_left</span>)</span></div>";
        }

        return $out;
    }
}
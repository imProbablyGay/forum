<?php

class Router
{
    private $path = 'template';
    
    function route($url) {
        $path;

        if ($url[0] == 'login') { // login
            $path = "$this->path/login.php";
        }
        else if ($url[0] == 'logout') { // logout
            $path = "$this->path/logout.php";
        }
        else if ($url[0] == 'register') { // register
            $path = "$this->path/register.php";
        }
        else if ($url[0] == '') { // main
            $path = "$this->path/main.php";
        }
        else if ($url[0] == 'question') {
            if ($url[1] == 'update') $path = "$this->path/update_question.php";
            else if ($url[1] == 'new') $path = "$this->path/create_question.php";
            else $path = "$this->path/forum_page.php";
        }
        else if ($url[0] == 'changeicon') { // change_icon
            $path = "$this->path/change_icon.php";
        }
        else if ($url[0] == 'create_question') { // change_icon
            $path = "$this->path/create_question.php";
        }
        else if (strpos($url[0], 'search?q=') !== FALSE) { //search page
            $path = "$this->path/search.php";
        }
        else if ($url[0] == 'profile') {//profile
            if ($url[1] == 'change_icon') $path = './template/change_icon.php';
            else if ($url[1] == 'notifications')  $path = './template/notifications.php';
            else if ($url[1] != '') $path = './template/profile.php';
        }
        else { // 404
            $path = "$this->path/404.php";
        }

        require_once $path;
    }
}
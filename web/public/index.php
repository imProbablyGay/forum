<?php

require 'core/common.php';
require 'core/Routing.php';

// check login
$router = new Router;
$url = explode('/', ltrim($_SERVER['REQUEST_URI'], '/'));
$router->route($url);
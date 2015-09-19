<?php
    session_start();
    require_once('controllers/MadeoController.php');
    $uri = explode('/', $_SERVER['REQUEST_URI']);
    $web_name = $uri[1];
    $controller_name = $uri[2];
    $args = array_slice($uri,3);
    if(empty($args) && empty($controller_name)) {
        $class_name = ucwords(strtolower($web_name)).'Controller';
        print_r($class_name::home());
    } else {
        $class_name = ucwords(strtolower($controller_name)).'Controller';
        require_once('controllers/'.$class_name.'.php');
        $other_args = array_slice($args,1);
        print_r($class_name::$args[0]($other_args));
    }
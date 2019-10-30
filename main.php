<?php

require_once 'vendor/autoload.php';

spl_autoload_register(function ($class_name) {
    include "src/" . $class_name . '.php';
});

$proxer = new Proxer();
var_dump($proxer->request('ya.ru'));
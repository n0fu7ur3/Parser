<?php

require_once 'vendor/autoload.php';

spl_autoload_register(function ($class_name) {
    include "src/" . $class_name . '.php';
});

$parser = new Parser();
$cats = $parser->categories('a.melu_l1', 'span');
echo $cats;
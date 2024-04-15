<?php
require __DIR__ . '/../vendor/autoload.php';
include __DIR__ . "/../src/App/function.php";

$app = include __DIR__ . '/../src/App/boostrap.php';


$app->run();

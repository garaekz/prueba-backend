<?php

use Garaekz\Application;

require_once __DIR__.'/vendor/autoload.php';

$app = new Application(dirname(__DIR__));

$app->router->group([
    'namespace' => 'App\Controllers',
], function ($router) {
    require __DIR__.'/routes/api.php';
});

$app->run();

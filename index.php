<?php

use Garaekz\Application;

require_once __DIR__ . '/vendor/autoload.php';

(new Garaekz\Bootstrap\LoadEnvironmentVariables(
    __DIR__,
))->bootstrap();

$app = new Application(dirname(__DIR__));

$app->router->group([
    'namespace' => 'App\Controllers',
    'prefix' => '/api/v1',
], function ($router) {
    require __DIR__ . '/routes/api.php';
});

$app->run();

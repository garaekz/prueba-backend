<?php

$router->get('/', function () {
    echo 'Hello, World!';
});

$router->get('/users/{userId}/comments', 'CommentController@index');
$router->post('/users/{userId}/comments', 'CommentController@store');
$router->get('/comments/{id}', 'CommentController@show');
$router->put('/comments/{id}', 'CommentController@update');
$router->delete('/comments/{id}', 'CommentController@destroy');

$router->get('/users', 'UserController@index');
$router->get('/users/{id}', 'UserController@show');
$router->post('/users', 'UserController@store');
$router->put('/users/{id}', 'UserController@update');
$router->delete('/users/{id}', 'UserController@destroy');

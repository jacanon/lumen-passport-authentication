<?php

/**
 *-----------------------------------------------
 * API Routes
 *-----------------------------------------------
 */
/** @var Router $router */


$router->group(['prefix' => 'api'], function () use ($router) {
    $router->get('/', function () use ($router) {
        return "API is working.";
    });

    $router->post('/register', 'UsersController@register');
    $router->post('/login', 'UsersController@login');

    $router->group(['prefix' => 'users','middleware' => 'auth'], function() use ($router) {
        $router->get('/{user_id}', 'UsersController@getUserDetails');
                                                                            });
});

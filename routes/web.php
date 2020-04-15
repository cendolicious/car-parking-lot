<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'space'], function () use ($router) {
    $router->post('create', 'SpaceController@create');
    $router->get('list', 'SpaceController@list');
});

$router->post('check-in', 'ParkingController@checkin');
$router->post('check-out', 'ParkingController@checkout');
$router->get('list', 'ParkingController@list');
$router->get('report', 'ParkingController@report');
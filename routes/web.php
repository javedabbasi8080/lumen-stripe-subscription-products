<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

// Route::apiResource('projects', ProjectController::class);
$router->group(['prefix' => 'api/'], function () use ($router) {
    $router->get('/', function () use ($router) {
        return "asdas";
    });

    $router->post('login', 'AuthController@login');
    $router->post('logout', 'AuthController@logout');
    $router->post('user-profile', 'AuthController@me');
    $router->post('assign-role', 'AuthController@assignRole');
    $router->get('user-subscribed-plan', 'AuthController@usersubscribePlan');



    $router->group(['middleware' => ['auth:api', 'checkrole:superadmin']], function ($router) {
        // products
        $router->get('products', 'PlanController@index');
        $router->post('product', 'PlanController@store');
        $router->get('product/{id}', 'PlanController@show');
        $router->put('product/{id}', 'PlanController@update');
        $router->delete('product/{id}', 'PlanController@destroy');
    });
});

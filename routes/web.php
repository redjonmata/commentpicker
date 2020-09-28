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

// Auth
$router->group(['as' => 'auth'], function () use ($router) {
    $router->post('/login', ['uses' => 'AuthController@postLogin', 'as' => 'users.login']);
    $router->post('/logout', ['middleware' => 'auth:api', 'uses' => 'AuthController@logout', 'as' => 'users.logout']);
    $router->post('/passwords/reset-mail', ['uses' => 'PasswordController@sendResetLinkEmail', 'as' => 'passwords.reset_mail']);
    $router->post('/passwords/reset', ['uses' => 'PasswordController@resetPassword', 'as' => 'passwords.reset']);
});

//$router->group(function () use ($router) {
    $router->get('/login/{provider}',['uses' => 'SocialController@redirectToProvider','as' => 'social.login']);
    $router->get('/{provider}/callback', ['uses' => 'SocialController@handleProviderCallback']);
//});

// Profile
$router->group(['middleware' => ['auth:api']], function ($route) {
    $route->group(['prefix' => 'users', 'as' => 'profile'], function () use ($route) {
        $route->put('/profile-update', ['uses' => 'ProfileController@updateProfile', 'as' => 'update']);
        $route->put('/password/change', ['uses' => 'ProfileController@passwordChange', 'as' => 'password.change']);
        $route->post('/upload-image', ['uses' => 'ProfileController@uploadImage', 'as' => 'upload']);
    });
});

$router->group(['middleware' => ['auth:api']], function ($route) {

    // Users
    $route->group(['prefix' => 'users', 'as' => 'users'], function () use ($route) {
        $route->get('/', ['uses' => 'UserController@index', 'as' => 'index']);
        $route->post('/', ['uses' => 'UserController@store', 'as' => 'store']);
        $route->get('/export', ['uses' => 'UserController@export', 'as' => 'export']);
        $route->put('/{id}', ['uses' => 'UserController@update', 'as' => 'update']);
        $route->get('/{id}', ['uses' => 'UserController@show', 'as' => 'show']);
        $route->delete('/{id}', ['uses' => 'UserController@delete', 'as' => 'delete']);
        $route->put('/{id}/block', ['uses' => 'UserController@block', 'as' => 'block']);
        $route->put('/{id}/unblock', ['uses' => 'UserController@unblock', 'as' => 'unblock']);
        $route->put('/{id}/restore', ['uses' => 'UserController@restore', 'as' => 'restore']);
        $route->delete('/{id}/force-delete', ['uses' => 'UserController@forceDelete', 'as' => 'force_delete']);
    });

    // Audits
    $route->group(['prefix' => 'audits', 'as' => 'audits'], function () use ($route) {
        $route->get('/', ['uses' => 'AuditController@getAudits', 'as' => 'get']);
        $route->get('/types', ['uses' => 'AuditController@getAuditTypes', 'as' => 'types']);
        $route->get('/events', ['uses' => 'AuditController@getAuditEvents', 'as' => 'events']);
        $route->get('/export', ['uses' => 'AuditController@export', 'as' => 'export']);
    });

});
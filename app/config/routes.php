<?php

use Baka\Http\RouterCollection;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for api.
 */

$router = new RouterCollection($application);

$router->get('/', [
    'Epade\Controllers\IndexController',
    'index',
]);

# Trucks

$router->get('/trucks', [
    'Epade\Controllers\TrucksController',
    'getTrucks',
]);

$router->get('/trucks/{id}', [
    'Epade\Controllers\TrucksController',
    'getTruck',
]);

$router->post('/trucks', [
    'Epade\Controllers\TrucksController',
    'create',
]);

$router->post('/trucks/{id}', [
    'Epade\Controllers\TrucksController',
    'editTruck',
]);

$router->delete('/trucks/{id}', [
    'Epade\Controllers\TrucksController',
    'deleteTrucks',
]);




# Users

$router->get('/users', [
    'Epade\Controllers\UsersController',
    'getUsers',
]);

$router->get('/users/{id}', [
    'Epade\Controllers\UsersController',
    'getUser',
]);

$router->post('/users', [
    'Epade\Controllers\UsersController',
    'create',
]);

$router->post('/users/{id}', [
    'Epade\Controllers\UsersController',
    'editUser',
]);

$router->delete('/users/{id}', [
    'Epade\Controllers\UsersController',
    'deleteUser',
]);

# User Authentication

$router->post('/login', [
    'Epade\Controllers\UsersController',
    'login',
]);

# Products

$router->get('/products', [
    'Epade\Controllers\ProductsController',
    'index',
]);

$router->post('/products', [
    'Epade\Controllers\ProductsController',
    'create',
]);

$router->post('/products/{id}', [
    'Epade\Controllers\ProductsController',
    'edit',
]);


$router->mount();

/**
 * Route not found
 */
$application->notFound(function () use ($application) {
    $response = new Phalcon\Http\Response();
    $response->setStatusCode(404, "Not Found");
    $response->setContentType('application/json', 'UTF-8');
    $response->setJsonContent([
        'status' => [
            'type' => 'FAILED',
            'message' => 'route was not found',
        ],
    ]);

    $response->send();
});

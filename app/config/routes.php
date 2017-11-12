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
    'edit',
]);

$router->delete('/trucks/{id}', [
    'Epade\Controllers\TrucksController',
    'delete',
]);

#Reports

$router->get('/reports', [
    'Epade\Controllers\ReportsController',
    'getReports',
]);

$router->get('/reports/{id}', [
    'Epade\Controllers\ReportsController',
    'getReport',
]);

$router->post('/reports', [
    'Epade\Controllers\ReportsController',
    'create',
]);

$router->post('/reports/{id}', [
    'Epade\Controllers\ReportsController',
    'edit',
]);

$router->delete('/reports/{id}', [
    'Epade\Controllers\ReportsController',
    'delete',
]);

#Drivers

$router->get('/drivers', [
    'Epade\Controllers\DriversController',
    'getDrivers',
]);

$router->get('/drivers/{id}', [
    'Epade\Controllers\DriversController',
    'getDriver',
]);

$router->post('/drivers', [
    'Epade\Controllers\DriversController',
    'create',
]);

$router->post('/drivers/{id}', [
    'Epade\Controllers\DriversController',
    'edit',
]);

$router->delete('/drivers/{id}', [
    'Epade\Controllers\DriversController',
    'delete',
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
    'edit',
]);

$router->delete('/users/{id}', [
    'Epade\Controllers\UsersController',
    'delete',
]);

# User Authentication

$router->post('/login', [
    'Epade\Controllers\UsersController',
    'login',
]);

$router->post('/logout', [
    'Epade\Controllers\UsersController',
    'logout',
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

$router->delete('/products/{id}', [
    'Epade\Controllers\ProductsController',
    'delete',
]);

$router->get('/products/{id}', [
    'Epade\Controllers\ProductsController',
    'getProduct',
]);

$router->get('/products', [
    'Epade\Controllers\ProductsController',
    'getProducts',
]);

#Vendor --> Vendedores

$router->get('/vendors', [
    'Epade\Controllers\VendorsController',
    'getVendors',
]);

$router->get('/vendors/{id}', [
    'Epade\Controllers\VendorsController',
    'getVendor',
]);

#Clients

$router->get('/clients', [
    'Epade\Controllers\ClientsController',
    'getClients',
]);

$router->get('/clients/{id}', [
    'Epade\Controllers\ClientsController',
    'getClient',
]);

#Routes

$router->get('/routes', [
    'Epade\Controllers\RoutesController',
    'getRoutes',
]);

$router->get('/routes/{id}', [
    'Epade\Controllers\RoutesController',
    'getRoute',
]);

#Orders

$router->post('/orders', [
    'Epade\Controllers\OrdersController',
    'create',
]);



#to Sync all vendors in our database

$router->get('/vendors/quickbooks', [
    'Epade\Controllers\VendorsController',
    'saveVendorsToDb',
]);

#to Sync all clients in our database

$router->get('/clients/quickbooks', [
    'Epade\Controllers\ClientsController',
    'saveVendorsToDb',
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

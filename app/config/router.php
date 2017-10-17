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
$router->setPrefix('/v1');

$router->get('/', [
    'Incursio\Controllers\IndexController',
    'index',
]);

$router->get('/leads/{leadId}/operations', [
    'Incursio\Controllers\OperationsController',
    'index',
]);

$router->get('/operations', [
    'Incursio\Controllers\OperationsController',
    'listAll',
]);

$router->post('/leads/{leadId}/operations', [
    'Incursio\Controllers\OperationsController',
    'create',
]);

$router->get('/leads/{leadId}/operations/{operationId}', [
    'Incursio\Controllers\OperationsController',
    'getById',
]);

$router->get('/leads-fields', [
    'Incursio\Controllers\LeadsController',
    'getFields',
]);

$router->put('/leads/{leadId}/operations/{operationId}', [
    'Incursio\Controllers\OperationsController',
    'edit',
]);

$router->delete('/leads/{leadId}/operations/{operationId}', [
    'Incursio\Controllers\OperationsController',
    'delete',
]);

$router->post('/leads/{leadId}/notes', [
    'Incursio\Controllers\LeadsController',
    'addNote',
]);

$router->post('/leads/{leadId}/printing', [
    'Incursio\Controllers\LeadsController',
    'printing',
]);

$router->put('/leads/{leadId}/status', [
    'Incursio\Controllers\LeadsController',
    'changeStatus',
]);

$router->get('/reports/leads', [
    'Incursio\Controllers\ReportController',
    'lead',
]);

$router->get('/reports/quotes', [
    'Incursio\Controllers\ReportController',
    'quotes',
]);

$router->post('/receivers/{key}', [
    'Incursio\Controllers\ReceiversController',
    'create',
]);

$router->post('/receivers/{key}/lead', [
    'Incursio\Controllers\ReceiversController',
    'createLead',
]);

# Dashboard stats

$router->get('/dashboards/leads', [
    'Incursio\Controllers\DashboardsController',
    'leads',
]);

$router->get('/companies/{id}/leads-stats', [
    'Incursio\Controllers\LeadsController',
    'getStats',
]);

# Companies

$router->get('/companies/{id}/users', [
    'Incursio\Controllers\CompaniesController',
    'users',
]);

/**
 * Need to understand if using this can be a performance disadvantage in the future
 */
$defaultCrudRoutes = [
    'business',
    'clients',
    'contacts',
    'modules',
    'RotationUsers' => 'leads-rotation',
    'customFields' => 'custom-fields',
    'leads',
    'LeadsStatus' => 'leads-status',
    'products',
    'productType' => 'product-type',
    'users',
    'sellers',
    'rules',
    'RulesConditions' => 'rules-conditions',
    'templates',
    'companies',
];

foreach ($defaultCrudRoutes as $key => $route) {

    //set the controller name
    $name = is_int($key) ? $route : $key;
    $controllerName = ucfirst($name) . 'Controller';

    $router->get('/' . $route, [
        'Incursio\Controllers\\' . $controllerName,
        'index',
    ]);

    $router->post('/' . $route, [
        'Incursio\Controllers\\' . $controllerName,
        'create',
    ]);

    $router->get('/' . $route . '/{id}', [
        'Incursio\Controllers\\' . $controllerName,
        'getById',
    ]);

    $router->put('/' . $route . '/{id}', [
        'Incursio\Controllers\\' . $controllerName,
        'edit',
    ]);

    $router->put('/' . $route, [
        'Incursio\Controllers\\' . $controllerName,
        'multipleUpdates',
    ]);

    $router->delete('/' . $route . '/{id}', [
        'Incursio\Controllers\\' . $controllerName,
        'delete',
    ]);
}

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

<?php

namespace Epade\Controllers;

use Epade\Models\Routes;
use Baka\Http\Rest\CrudExtendedController;
use Phalcon\Http\Response;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;

class RoutesController extends \Baka\Http\Rest\CrudExtendedController
{
    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct(): void
    {
        $this->model = new Routes();
    }

    /**
     * Fetches all clients from Quickbooks
     *
     * @return Response
     */
    public function getRoutes(): Response
    {
        $routes = $this->model::find();
        
        if (!$routes) {
            throw new \Exception("There are no Routes");
        }
        
        return $this->response($routes);
    }
        
    /**
     * Fetches a specific Client based on id from Quickbooks
     *
     * @return void
     */
    public function getRoute($id):Response
    {
        $route = $this->model::findFirst([
            "conditions" => "id = ?0",
            "bind" =>[$id],
        ]);

        if (!$route) {
            throw new \Exception("No route found");
        }
        
        return $this->response($route);
    }
}

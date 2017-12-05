<?php

namespace Epade\Controllers;

use Epade\Models\Routes;
use Epade\Models\Trucks;
use Epade\Models\Drivers;
use Epade\Models\Orders;
use Epade\Models\Clients;
use Gen\Plan;
use Gen\Point;
use Gen\Place;
use Gen\Life;
use Epade\Models\OrdersProducts;
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

    /**
     * Calculates and orders routes in a location manner by drivers assigned orders
     *@todo Completado a un 80% pues faltaria hacer unos cambios en el front-end para que se llame esta funcion y ponga los puntos en google maps
     * @return Response
     */
    public function calculateRoute($id): Response{

        if($this->request->isPost()){
        $rawData = $this->request->getRawBody();
        $jsonData = json_decode($rawData);
        
        if($jsonData){
            
            $latitude = $jsonData->latitude;
            $longitude = $jsonData->longitude;
        }

        }

        //Paso 1: Find all orders assigned to a specific driver
        $driverOrders = OrdersProducts::find([
            "conditions" => "driver_id = ?0",
            "bind" => [$id],
            "group" => "order_id"
        ]);

        $plan = new Plan();

        $plan->addPlace(new Place('currentLocation',new Point($latitude,$longitude)));

        foreach ($driverOrders as $driverOrder) {
            
            $order = Orders::findFirst([
                "conditions" => "id = ?0",
                "bind" => [$driverOrder->order_id]
            ]);

            if(!$order){
                throw new \Exception("Order could not be found");
            }

            $route = $this->model::findFirst([
                "conditions" => "id = ?0",
                "bind" => [$order->route_id]
            ]);

            if(!$route){
                throw new \Exception("Route could not be found");
            }

            $client = Clients::findFirst([
                "conditions" => "id = ?0",
                "bind" => [$order->client_id]
            ]);

            if(!$client){
                throw new \Exception("Client could not be found");
            }

            $plan->addPlace(new Place($client->company_name,new Point($route->latitude,$route->longitude)));
            
        }
        

        $life = new Life();
        $roadmap = $life->getShortestPath($plan);

        return $this->response($roadmap->places);

    }
}

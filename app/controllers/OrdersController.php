<?php

namespace Epade\Controllers;

use Epade\Models\Products;
use Epade\Models\Orders;
use Epade\Models\OrdersProducts;
use Epade\Models\Clients;
use Epade\Models\Trucks;
use Baka\Http\Rest\CrudExtendedController;
use Phalcon\Http\Response;
use QuickBooksOnline\API\Facades\Item;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;

class OrdersController extends \Baka\Http\Rest\CrudExtendedController
{
    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct(): void
    {
        $this->model = new Orders();
    }

    /**
     * Creates a new order
     *
     * @return Response
     */
    public function create() :Response
    {
        if ($this->request->isPost()) {
            // $rawData = $this->request->getRawBody();
            // $jsonData = json_decode($rawData);
            
            // //Suponiendo que la data se mandara asi
            // $user_id = 1; //$this->session->get("id");\
            // $client_id = 1;    //$jsonData->client_id;


            // //If data comes from mobile app
            // if ($this->request->getContentType() == "application/x-www-form-urlencoded") {
            //     $request = $this->request->getPost();
            //     $user_id = $request['user_id'];//$this->session->get("id");
            //     $client_id = $request['client_id'];
            // }

            $request = $this->request->getPost();
            $user_id = $request['user_id'];//$this->session->get("id");
            $client_id = $request['client_id'];
            $stringProducts = $request['products']; //string con el id de cada producto separado por coma
            $stringQuantity = $request['quantity']; //Cantidad de products

            //Paso 1: Buscar cliente en la base de datos nuestra
            $client = Clients::findFirst([
                "conditions" => "id = ?0",
                "bind" => [$client_id]
            ]);

            if (!$client) {
                throw new \Exception("Client not found");
            }

            $route_id = $client->route_id;
            $created_at = date("Y-m-d h:i:s");

            $newOrder = new Orders();
            $newOrder->user_id = $user_id;
            $newOrder->client_id = $client_id;
            $newOrder->route_id = $route_id;
            $newOrder->created_at = $created_at;

            if ($newOrder->save()) {
                //Paso 2: Obtener y dar formato a los productos de la orden
                $totalAmount = 0;
                $totalVolume = 0;
                $products = explode(',', $stringProducts);
                $quantities = explode(',', $stringQuantity);

                //Posible solucion: Array Asociativo [product_id] => quantity
                foreach ($products as $product) {
                    $dbProduct = Products::findFirst([
                        "conditions" => "id = ?0",
                        "bind" => [$product]
                    ]);

                    if ($dbProduct) {
                        foreach ($quantities as $productQuantity) {
                            $totalAmount += $this->calculateTotalAmount($productQuantity, $dbProduct->unit_price);
        
                            $totalVolume += $this->calculateTotalProductVolume($productQuantity, $dbProduct->unit_volume);

                            break;//For now
                        }
                    }
                }

                //Paso 4 comparar volumen total de productos con camiones
                
                //Buscamos a que camion se le puede assignar la orden por medio del volumen total de la misma
                $truck = Trucks::findFirst([
                    "conditions" => "capacity >= ?0",
                    "bind" => [$totalVolume]
                ]);

                if ($truck) {
                    $truck->capacity -= $totalVolume;
                    $truck->update();
                }

                //Paso 5: Asignar un chofer

                print_r($truck->toArray());

                die();
            }
        }
    }

    /**
     * Calculates total price of all products
     *
     * @param [int] $quantity
     * @param [double] $unit_price
     * @return double
     */
    public function calculateTotalAmount($quantity, $unit_price)
    {
        return $quantity * $unit_price;
    }

    /**
     * Calculates total product volume
     *
     * @param [int] $quantity
     * @param [double] $unit_volume
     * @return double
     */
    public function calculateTotalProductVolume($quantity, $unit_volume)
    {
        return $quantity * $unit_volume;
    }
}

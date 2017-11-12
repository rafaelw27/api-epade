<?php

namespace Epade\Controllers;

use Epade\Models\Products;
use Epade\Models\Orders;
use Epade\Models\OrdersProducts;
use Epade\Models\Clients;
use Epade\Models\Trucks;
use Epade\Models\Drivers;
use Epade\Models\Routes;
use Baka\Http\Rest\CrudExtendedController;
use Phalcon\Http\Response;
use QuickBooksOnline\API\Facades\Invoice;
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
     *@todo Reparar el error que tenemos cuando creamos un Invoice nuevo en Quickbooks
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

            $routeObj = Routes::findFirst([
                "conditions" => "id = ?0",
                "bind" => [$client->route_id]
            ]);

            if (!$routeObj) {
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
                $productsArray = [];
                $productArray = [];


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

                            //$productArray['Id'] = $dbProduct->id;
                            $productArray['Description'] = $dbProduct->description;
                            $productArray['Amount'] = $dbProduct->unit_price * $productQuantity ;
                            $productArray['DetailType'] = "SalesItemLineDetail";
                            $productArray['SalesItemLineDetail'] = [
                                "UnitPrice" => $dbProduct->unit_price,
                                "Qty"=> $productQuantity,
                                "ItemRef" => [
                                    "value" => (string)$dbProduct->id,
                                    "name" => $dbProduct->name
                                ],
                            ];
                           
                            //$productArray['TotalAmt'] = $totalAmount; //Later make function for total amount of all products

                            break;//For now
                        }
                    }

                    $productsArray[] = $productArray;

                //    print_r(gettype($client_id));
                //     die();
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

                $drivers = Drivers::find();
                $driversCount = count($drivers);
                
                //Now we choose a random drivers from database
                $randomDriver = rand(1,$driversCount);
                $selectDriver = Drivers::findFirst([
                    "conditions" => "id = ?0",
                    "bind" => [$randomDriver]
                ]);

                //Paso 6: Insertar la orden en Quickbooks en la entidad Invoice
                $orderApi = Invoice::create([
                    //     "Deposit" => 0,
                    //     "domain" => "QBO",
                    //     "sparse" => false,
                    //     "Id" =>$newOrder->id,
                    // "ShipAddr"=> [
                    //     "Id" => $routeObj->id,
                    //     "Line1" => $routeObj->street,
                    //     "City" => $routeObj->city,
                    //     "Lat" => $routeObj->latitude,
                    //     "Long" => $routeObj->longitude],
                    // "TotalAmt" => $totalAmount,
                    // "BillEmail" => [
                    //     "Address"=> $client->email ], 
                    "Line" => $productsArray,
                    "CustomerRef"=>[
                        "value"=> $client_id
                    ]
              ]);

              if($resultingObj = $this->quickbooks->Add($orderApi)){
                print_r("Orden creada en Quickbooks");
                die();
                
              }
              $error = $this->quickbooks->getLastError();
              if ($error != null) {
                echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
                echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
                echo "The Response message is: " . $error->getResponseBody() . "\n";
                echo "The Intuit Helper message is: IntuitErrorType:{" . $error->getIntuitErrorType() . "} IntuitErrorCode:{" . $error->getIntuitErrorCode() . "} IntuitErrorMessage:{" . $error->getIntuitErrorMessage() . "} IntuitErrorDetail:{" . $error->getIntuitErrorDetail() . "}";
                } 


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

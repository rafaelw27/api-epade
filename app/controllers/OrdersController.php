<?php

namespace Epade\Controllers;

use Epade\Models\Products;
use Epade\Models\Orders;
use Epade\Models\OrdersProducts;
use Epade\Models\Clients;
use Epade\Models\Trucks;
use Epade\Models\Drivers;
use Epade\Models\Routes;
use Epade\Models\Status;
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
            $rawData = $this->request->getRawBody();
            $jsonData = json_decode($rawData);
            
            if($jsonData){
                
                $user_id = $jsonData->user_id; //$this->session->get("id");\
                $client_id = $jsonData->client_id;
                $stringProducts = $jsonData->products; 
                $stringQuantity = $jsonData->quantity;
            }
            
            //If data comes from mobile app
            if ($this->request->getContentType() == "application/x-www-form-urlencoded") {
                $request = $this->request->getPost();
                $user_id = $request['user_id'];//$this->session->get("id");
                $client_id = $request['client_id'];
                $stringProducts = $request['products']; //string con el id de cada producto separado por coma
                $stringQuantity = $request['quantity']; //Cantidad de products
    
            }

            // $request = $this->request->getPost();
            // $user_id = $request['user_id'];//$this->session->get("id");
            // $client_id = $request['client_id'];
            // $stringProducts = $request['products']; //string con el id de cada producto separado por coma
            // $stringQuantity = $request['quantity']; //Cantidad de products

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
            $newOrder->status_id = 1;
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

                }

                //Paso 4 comparar volumen total de productos con camiones
                
                //Buscamos a que camion se le puede assignar la orden por medio del volumen total de la misma
                $truck = Trucks::findFirst([
                    "conditions" => "capacity >= ?0",
                    "bind" => [$totalVolume]
                ]);

                if ($truck) {
                    $truck->load -= $totalVolume;
                    $truck->update();
                }

                //Paso 5: Asignar un chofer

                $drivers = Drivers::find();
                $driversCount = count($drivers);
                
                //Now we choose a random drivers from database
                $randomDriver = rand(1, $driversCount);
                $selectDriver = Drivers::findFirst([
                    "conditions" => "id = ?0",
                    "bind" => [$randomDriver]
                ]);

                //Paso 6: Insertar la orden en Quickbooks en la entidad Invoice
                $orderApi = Invoice::create([
                        "Deposit" => 0,
                        "domain" => "QBO",
                        "sparse" => false,
                    "ShipAddr"=> [
                        "Id" =>(string)$routeObj->id,
                        "Line1" => $routeObj->street,
                        "City" => $routeObj->city,
                        "Lat" => $routeObj->latitude,
                        "Long" => $routeObj->longitude],
                        "TotalAmt" => $totalAmount,
                        "BillEmail" => [
                        "Address"=> $client->email ],
                    "Line" => $productsArray,
                    "CustomerRef"=>[
                        "value"=> $client_id
                    ],
                    //"Id" => "235", Parece que no se puede asignar Id a una orden nueva
                ]);

                if ($resultingObj = $this->quickbooks->Add($orderApi)) {
                    //Paso 7 Guardar la orden en orden_producto

                    foreach ($products as $product) {
                        $newProduct = Products::findFirst([
                            "conditions" => "id = ?0",
                            "bind" => [$product]
                        ]);

                        $productTotalAmount = 0;
                        $productTotalVolume = 0;
    
                        foreach ($quantities as $newProductQuantity) {
                            $productTotalAmount += $this->calculateTotalAmount($newProductQuantity, $newProduct->unit_price);
        
                            $productTotalVolume += $this->calculateTotalProductVolume($newProductQuantity, $newProduct->unit_volume);
    
                        $orderProduct = new OrdersProducts();
                        $orderProduct->order_id = $newOrder->id;
                        $orderProduct->product_id = $newProduct->id;
                        $orderProduct->truck_id = 1;
                        $orderProduct->driver_id = $selectDriver->id;
                        $orderProduct->quantity = $newProductQuantity;
                        $orderProduct->volume = $productTotalVolume;
                        
                        if(!$orderProduct->save()){
                            throw new \Exception("Order Product could not be created");
                        }

                        break;
                        
                        }
    
    
                    }
                
                }

                return $this->response("Order Created");
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

    /**
     * Fetches all orders products by order id
     *
     * @return void
     */
    public function getOrdersByOrderId($id){

        $ordersArray = [];
        $orderArray = [];
        $totalAmt = 0;

        $orderProducts = OrdersProducts::find([
            "conditions" => "order_id = ?0",
            "bind"=> [$id]
        ]);

        foreach ($orderProducts as $order) {

            $orderInfo = Orders::findFirst([
                "conditions" => "id = ?0",
                "bind"=> [$order->order_id]
            ]);

            $productInfo = Products::findFirst([
                "conditions" => "id = ?0",
                "bind"=> [$order->product_id]
            ]);

            $orderArray['id'] = $order->id;

            $orderArray['order_id'] = $order->order_id;
            $orderArray['user_id'] = $orderInfo->user_id;
            $orderArray['client_id'] = $orderInfo->client_id;
            $orderArray['route_id'] = $orderInfo->route_id;
            $orderArray['status_id'] = $orderInfo->status_id;
            $orderArray['created_at'] = $orderInfo->created_at;

            $orderArray['truck_id'] = $order->truck_id;
            $orderArray['driver_id'] = $order->driver_id;

            $orderArray['product_id'] = $order->product_id;
            $orderArray["name"] = $productInfo->name;
            $orderArray["full_name"] = $productInfo->full_name;
            $orderArray["description"] = $productInfo->description;
            $orderArray["maker"] = $productInfo->maker;
            $orderArray["unit_price"] = $productInfo->unit_price;
            $orderArray['quantity'] = $order->quantity;
            $orderArray['volume'] = $order->volume;
            

            $totalAmt += $this->calculateTotalAmount($order->quantity,$productInfo->unit_price);

            $ordersArray [] =  $orderArray;
   
        }

        $ordersArray ["totalPrice"] =  $totalAmt;
        
        
        if (!$orderProducts) {
            throw new \Exception("There are no Products");
        }
        
        return $this->response($ordersArray);

    }

    /**
     * Fetches all orders products by client id
     *
     * @return void
     */
    public function getOrdersByClientId($id)
    {
        
        $ordersArray = [];
        $clientOrdersArray = [];
        

        $clientOrders = Orders::find([
            "conditions" => "client_id = ?0",
            "bind"=> [$id]
        ]);


        foreach ($clientOrders as $clientOrder) {

            $totalAmt = 0;
            $ordersArray = [];

            $statusInfo = Status::findFirst([
                "conditions" => "id = ?0",
                "bind"=> [$clientOrder->status_id]
            ]);

            $ordersArray['order_id'] = $clientOrder->id;
            $ordersArray['user_id'] = $clientOrder->user_id;
            $ordersArray['client_id'] = $clientOrder->client_id;
            $ordersArray['route_id'] = $clientOrder->route_id;
            $ordersArray ["status_id"] =  $clientOrder->status_id;
            $ordersArray ["status"] =  $statusInfo->status_name;
            $ordersArray['created_at'] = $clientOrder->created_at;           

            $orderProducts = OrdersProducts::find([
                "conditions" => "order_id = ?0",
                "bind"=> [$clientOrder->id]
            ]);
            
            foreach ($orderProducts as $order) {
            
                $productInfo = Products::findFirst([
                    "conditions" => "id = ?0",
                    "bind"=> [$order->product_id]
                ]);
            
                $orderArray['id'] = $order->id;
                $orderArray['truck_id'] = $order->truck_id;
                $orderArray['driver_id'] = $order->driver_id;
            
                $orderArray['product_id'] = $order->product_id;
                $orderArray["name"] = $productInfo->name;
                $orderArray["full_name"] = $productInfo->full_name;
                $orderArray["description"] = $productInfo->description;
                $orderArray["maker"] = $productInfo->maker;
                $orderArray["unit_price"] = $productInfo->unit_price;
                $orderArray['quantity'] = $order->quantity;
                $orderArray['volume'] = $order->volume;
            
                $totalAmt += $this->calculateTotalAmount($order->quantity,$productInfo->unit_price);
            
                $ordersArray [] =  $orderArray;
               
            }
            
            $ordersArray ["totalPrice"] =  $totalAmt;
            
            if (!$orderProducts) {
                throw new \Exception("There are no Products");
            }
        
            $clientOrdersArray [] = $ordersArray;
        }

        return $this->response($clientOrdersArray);
        
    }

    /**
     * Changes the order status from Ready to Delivered
     *
     * @return void
     */
    public function changeOrderStatus($id): Response{

        if($this->request->isPost()){
            
            $request = $this->request->getPost();
            
            $order = Orders::findFirst([
                "conditions" => "id = ?0",
                "bind"=> [$id]
            ]);

            $order->status_id = $request['status_id'];

            if(!$order->update()){
                throw new \Exception("Order status could not be changed");
            }

            return $this->response($order);

        }
        

    }

    /**
     * Fetch Orders that have been assigned to a driver by id
     *
     * @return void
     */
    public function getOrdersByDriver($id){

        $orderArray = [];
        $ordersArray = [];

        $orderProducts = OrdersProducts::find([
            "conditions" => "driver_id = ?0",
            "bind"=> [$id],
            "group" => "order_id"
        ]);

        if($orderProducts){

        foreach ($orderProducts as $orderProduct) {


        $order = Orders::findFirst([
            "conditions" => "id = ?0",
            "bind"=> [$orderProduct->order_id]
        ]);

        if($order){

        $status = Status::findFirst([
            "conditions" => "id = ?0",
            "bind"=> [$order->status_id]
        ]);

        $route = Routes::findFirst([
            "conditions" => "id = ?0",
            "bind"=> [$order->route_id]
        ]);

        $client = Clients::findFirst([
            "conditions" => "id = ?0",
            "bind"=> [$order->client_id]
        ]);

        //Put it in an array

        //Order
        $orderArray['order_id'] = $order->id;
        
        //Clients
        $orderArray['client_id'] = $client->id;
        $orderArray['company_name'] = $client->company_name;
        $orderArray['phone'] = $client->phone;

        //Routes
        $orderArray["route_id"] = $route->id;
        $orderArray["street"] = $route->street;
        $orderArray["city"] = $route->city;
        $orderArray["latitude"] = $route->latitude;
        $orderArray["longitude"] = $route->longitude;

        //Status
        $orderArray['status_id'] = $status->id;
        $orderArray['status_name'] = $status->status_name;

        $ordersArray [] = $orderArray;

        }

        }

    }

        return $this->response($ordersArray);


    }

    /**
     * Function of when an order is succesfully delivered. Funciona en un 80% porque se tiene que hacer update en Quickbooks
     *
     * @return void
     */
    public function orderDelivered($id): Response 
    {

        //Paso 1: Buscamos la orden en la base de datos
        $order = $this->model::findFirst([
            "conditions" => "id = ?0 and status_id = ?1",
            "bind" => [$id,1]
        ]);

        if($order){

            //Paso 2: Buscamos en order_products todas los productos ligados a la orden
            $orderProducts = OrdersProducts::find([
                "conditions" => "order_id = ?0 ",
                "bind" => [$order->id]
            ]);

            if(! $orderProducts){
                throw new \Exception("Order products not found");
            }

            $truck = Trucks::findFirst([
                "conditions" => "id = ?0",
                "bind" => [$orderProducts[0]->truck_id]
            ]);

            if(!$truck){
                throw new \Exception("Truck not found");
            }

            //Paso 4: Iteramos por cada orden_producto para restablecer la capacidad del camion
            foreach ($orderProducts as $orderProduct) {
                
                $truck->load += $orderProduct->volume;
            }

            if($truck->update()){
                
                $order->status_id = 2;
                if($order->update()){
                    return $this->response("Order Delivered");
                }
            }

        }
    }
}

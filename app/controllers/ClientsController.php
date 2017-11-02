<?php

namespace Epade\Controllers;

use Epade\Models\Clients;
use Epade\Models\Routes;
use Baka\Http\Rest\CrudExtendedController;
use Phalcon\Http\Response;
use QuickBooksOnline\API\Facades\Customer;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;

class ClientsController extends \Baka\Http\Rest\CrudExtendedController
{
    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct(): void
    {
        $this->model = new Clients();
    }

    /**
     * Saves all clients in our database
     *
     * @return Response
     */
    public function saveVendorsToDb():Response
    {
        if ($apiClients = $this->quickbooks->Query("SELECT * FROM Customer")) {
            foreach ($apiClients as $apiClient) {
                $client = new Clients();
                $client->id = $apiClient->Id;
                $client->company_name = $apiClient->CompanyName;
                $client->active = $apiClient->Active;
                $client->phone = (string)$apiClient->PrimaryPhone->FreeFormNumber;
                $client->email =(string) $apiClient->PrimaryEmailAddr->Address;
                $client->route_id = $apiClient->BillAddr->Id;
                if ($client->save()) {
                    $route = new Routes();
                    $route->id = $apiClient->BillAddr->Id;
                    $route->street = $apiClient->BillAddr->Line1;
                    $route->city = $apiClient->BillAddr->City;
                    $route->latitude = $apiClient->BillAddr->Lat;
                    $route->longitude = $apiClient->BillAddr->Long;
                    $route->save();
                }
            }

            return $this->response("All clients from Quickbooks added to our Database");
        }
    }
        
    /**
     * Fetches all clients from Quickbooks
     *@todo Append la informacion de las rutas a los clientes para solamente hacer una llamada que tenga todo
     * @return Response
     */
    public function getClients(): Response
    {
        $clients = $this->model::find([
            "conditions" => "active = 'true'"
        ]);
        
        if (!$clients) {
            throw new \Exception("There are no Clients");
        }
        
        return $this->response($clients);
    }
        
    /**
     * Fetches a specific Client based on id from Quickbooks
     *@todo Append la informacion de las rutas a los clientes para solamente hacer una llamada que tenga todo
     * @return void
     */
    public function getClient($id):Response
    {
        $client = $this->model::findFirst([
            "conditions" => "id = ?0 AND active = 'true'",
            "bind" =>[$id],
        ]);

        if (!$client) {
            throw new \Exception("No client found");
        }
        
        return $this->response($client);
    }
}

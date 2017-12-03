<?php

namespace Epade\Controllers;

use Baka\Http\Rest\CrudExtendedController;
use Phalcon\Http\Response;
use Epade\Models\Vendors;
use Epade\Models\VendorsClients;
use Epade\Models\Clients;
use QuickBooksOnline\API\Facades\Employee;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;

class VendorsClientsController extends \Baka\Http\Rest\CrudExtendedController
{
    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct(): void
    {
        $this->model = new VendorsClients();
    }

    /**
     * Saves all vendors(employees) in our database
     *@todo No se esta salvando en vendors de  nuestra base de datos
     * @return Response
     */
    public function saveVendorsClients()
    {
        $clients = Clients::find();

        if(!$clients){
            throw new \Exception("No clients found");
        }

        // print_r($clients->toArray());
        // die();

        foreach ($clients as $client) {
            
            $vendorClient = new VendorsClients();
            $vendorClient->id = $client->id;
            $vendorClient->vendor_id = $client->vendor_id;
            $vendorClient->client_id = $client->id;
            $vendorClient->visited = 0;
            $vendorClient->save();
            print_r($vendorClient->toArray());
            
        }

        // return $this->response("Done");
    }

    /**
     * Fetches all vendors from Quickbooks
     *
     * @return Response
     */
    public function getVendorsClients(): Response
    {
        $vendorsClients = $this->model::find();
        
        if (!$vendorsClients) {
            throw new \Exception("There are no vendors");
        }
        
        return $this->response($vendorsClients);
    }

    /**
     * Fetches a specific Vendor based on id from Quickbooks
     *
     * @return void
     */
    public function getVendorClient($id):Response
    {
        $vendor = $this->model::findFirst([
            "conditions" => "id = ?0 AND active = 'true'",
            "bind" =>[$id],
        ]);

        if (!$vendor) {
            throw new \Exception("No vendor found");
        }
        
        return $this->response($vendor);
    }

    /**
     * Changes the visited status of a vendor's client
     *
     * @param [type] $id
     * @return Response
     */
    public function changeVisitedState($id):Response
    {
        $vendorClient = $this->model::findFirst([
            "conditions" => "client_id = ?0",
            "bind" => [$id]
        ]);

        if($vendorClient){
            $vendorClient->visited = 1;
            if($vendorClient->update()){
                return $this->response("Visited");
            }
        }

    }
}
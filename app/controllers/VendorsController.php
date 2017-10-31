<?php

namespace Epade\Controllers;


use Baka\Http\Rest\CrudExtendedController;
use Phalcon\Http\Response;
use Epade\Models\Vendors;
use Epade\Models\Users;
use QuickBooksOnline\API\Facades\Employee;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;


class VendorsController extends \Baka\Http\Rest\CrudExtendedController
{

    /**
     * Saves all vendors(employees) in our database
     *@todo No se esta salvando en vendors de  nuestra base de datos
     * @return Response
     */
    public function saveVendorsToDb():Response {

        if($apiVendors = $this->quickbooks->Query("SELECT * FROM Employee"))
        {
            foreach ($apiVendors as $apiVendor) {

                $vendor = new Vendors();
                $vendor->id = $apiVendor->Id;
                $vendor->first_name = $apiVendor->GivenName;
                $vendor->last_name = $apiVendor->FamilyName;
                $vendor->active = $apiVendor->Active;
                $vendor->phone = (string)$apiVendor->PrimaryPhone->FreeFormNumber;
                $vendor->email =(string) $apiVendor->PrimaryEmailAddr->Address;

                if($vendor->save()){
                    $user = new Users();
                    $user->id = $apiVendor->Id;
                    $user->user_type_id = 2;
                    $user->first_name = $apiVendor->GivenName;
                    $user->last_name = $apiVendor->FamilyName;
                    $user->email =(string) $apiVendor->PrimaryEmailAddr->Address;
                    $user->password = "placeholder";
                    $user->phone = (string)$apiVendor->PrimaryPhone->FreeFormNumber;
                    $user->save();

                }
            }

            return $this->response("All vendors from Quickbooks added");
        }

    }

    /**
     * Fetches all vendors from Quickbooks
     *
     * @return Response
     */
    public function getVendors(): Response
    {
        if($apiVendors = $this->quickbooks->Query("SELECT * FROM Employee"))
        {
            $vendors = [];
            $vendor= [];
            foreach ($apiVendors as $apiVendor) {

                $vendor['id'] = $apiVendor->Id;
                $vendor['first_name'] = $apiVendor->GivenName;
                $vendor['last_name'] = $apiVendor->FamilyName;
                $vendor['active'] = $apiVendor->Active;
                $vendor['phone'] = $apiVendor->PrimaryPhone->FreeFormNumber;
                $vendor['email'] = $apiVendor->PrimaryEmailAddr->Address;

                $vendors[]= $vendor;
            }
            return $this->response($vendors);
        }


        
    }

    /**
     * Fetches a specific Vendor based on id from Quickbooks
     *
     * @return void
     */
    public function getVendor($id):Response
    {
        if($apiVendors = $this->quickbooks->Query("SELECT * FROM Employee where Id= '{$id}'"))
        {
            $vendor = [];
            foreach ($apiVendors as $apiVendor) {

                $vendor['id'] = $apiVendor->Id;
                $vendor['first_name'] = $apiVendor->GivenName;
                $vendor['last_name'] = $apiVendor->FamilyName;
                $vendor['active'] = $apiVendor->Active;
                $vendor['phone'] = $apiVendor->PrimaryPhone->FreeFormNumber;
                $vendor['email'] = $apiVendor->PrimaryEmailAddr->Address;
                
            }
            return $this->response($vendor);
        }

    }

}
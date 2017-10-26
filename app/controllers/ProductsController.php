<?php

namespace Epade\Controllers;

use Epade\Models\Products;
use Baka\Http\Rest\CrudExtendedController;
use Phalcon\Http\Response;
use QuickBooksOnline\API\Facades\Item;


class ProductsController extends \Baka\Http\Rest\CrudExtendedController{

    /**
     * Index function to test Quickbooks API
     *
     * @return void
     */
    public function index(): Response {

        $CompanyInfo = $this->quickbooks->getCompanyInfo();
        $error = $this->quickbooks->getLastError();
        if ($error != null) {
            echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
            echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
            echo "The Response message is: " . $error->getResponseBody() . "\n";
            echo "The Intuit Helper message is: IntuitErrorType:{" . $error->getIntuitErrorType() . "} IntuitErrorCode:{" . $error->getIntuitErrorCode() . "} IntuitErrorMessage:{" . $error->getIntuitErrorMessage() . "} IntuitErrorDetail:{" . $error->getIntuitErrorDetail() . "}";
        } 

        return $this->response($CompanyInfo);

    }

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct(): void
    {
        $this->model = new Products();
    }

    /**
     * Creates a new product on both our API and Quickbooks
     * Fields that will not be used: IncomeAccountRef, PurchaseDesc , PurchaseCost, AssetAccountRef, TrackQtyOnHand
     *                               QtyOnHand, InvStartDate
     * Field that we are going to use: Name, Description, Active, FullyQualifiedName, Taxable, UnitPrice, Type
     * @return Response
     */
    public function create(): Response {

            if($this->request->isPost()){

                $request = $this->request->getPost();

                $product = $this->model;

                $product->name = $request['name'];
                $product->full_name = $request['full_name'];
                $product->description = $request['description'];
                $product->active = $request['active'];
                $product->type = $request['type'];
                $product->taxable = $request['taxable'];
                $product->maker = $request['maker'];
                $product->unit_price = $request['unit_price'];
                $product->unit_volume = $request['unit_volume'];
                $product->quantity = $request['quantity'];

                if(!$product->save()){
                    throw new \Exception('Product could not be created');
                }

                //Here we create the new product in Quickbooks
                $dateTime = new \DateTime('NOW');
                $productAPI = Item::create([
                      "Name" => $request['name'] ,
                      "Description" => $request['description'],
                      "Active" => $request['active'],
                      "FullyQualifiedName" => $request['full_name'],
                      "Taxable" => $request['taxable'],
                      "UnitPrice" => $request['unit_price'],
                      "Type" => $request['type'],
                      "IncomeAccountRef"=> [
                        "value"=> 0,
                        "name" => "none"
                      ],
                      "PurchaseDesc"=> "none",
                      "PurchaseCost"=> 0,
                      "ExpenseAccountRef"=> [
                        "value"=> 0,
                        "name"=> "none"
                      ],
                      "AssetAccountRef"=> [
                        "value"=> 0,
                        "name"=> "none"
                      ],
                      "TrackQtyOnHand" => true,
                      "QtyOnHand"=> 0,
                      "InvStartDate"=> $dateTime,
                      "Id" => 3,
                ]);

                
                return $this->response($product); 
        }


    }

}
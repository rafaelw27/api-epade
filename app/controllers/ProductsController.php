<?php

namespace Epade\Controllers;

use Epade\Models\Products;
use Baka\Http\Rest\CrudExtendedController;
use Phalcon\Http\Response;
use QuickBooksOnline\API\Facades\Item;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;


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

                //Here we create the new product in Quickbooks
                $dateTime = new \DateTime('NOW');
                $productAPI = Item::create([
                      "Name" => $request['name'] ,
                      "Description" => $request['description'],
                      "Active" => true,
                      "FullyQualifiedName" => $request['full_name'],
                      "Taxable" => $request['taxable'],
                      "UnitPrice" => $request['unit_price'],
                      "Type" => $request['type'],
                      "IncomeAccountRef"=> [
                        "value"=> 79,
                        "name" => "Landscaping Services:Job Materials:Fountains and Garden Lighting"
                      ],
                      "PurchaseDesc"=> "none",
                      "PurchaseCost"=> 0,
                      "ExpenseAccountRef"=> [
                        "value"=> 80,
                        "name"=> "Cost of Goods Sold"
                      ],
                      "AssetAccountRef"=> [
                        "value"=> 81,
                        "name"=> "Inventory Asset"
                      ],
                      "TrackQtyOnHand" => true,
                      "QtyOnHand"=> $request['quantity'],
                      "InvStartDate"=> $dateTime,
                ]);
                 
                    if($resultingObj = $this->quickbooks->Add($productAPI)){

                    //Save the product to our database also
                    $product = $this->model;
                    $product->id = $resultingObj->Id;
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

                    return $this->response($product);

                }
                    //$xmlBody = XmlObjectSerializer::getPostXmlFromArbitraryEntity($resultingObj, $urlResource);

                
        }


    }

    /**
     * Edits a specific product by its id
     *
     * @return Response
     */
    public function edit($id): Response
    {
        if($this->request->isPost()){

        $request = $this->request->getPost();

        if($apiItem = $this->quickbooks->Query("SELECT * FROM Item where Id= '{$id}'")){

            $actualItem = reset($apiItem);
            $updateItem = Item::update($actualItem, [
                //If you are going to do a full Update, set sparse to false
                'sparse' => 'true',
                'Name' => $request['name'],
                'FullyQualifiedName' => $request['full_name'],
                'Description' => $request['description'],
                'Type' => $request['type'],
                'Taxable' =>$request['taxable'],
                'UnitPrice' =>$request['unit_price'],
                'QtyOnHand' => $request['quantity'],
            ]);

            if($resultUpdate = $this->quickbooks->Update($updateItem)){
               
                $ourProduct = $this->model::findFirst([
                    "conditions" => "id = ?0",
                    "bind" => [$id]
                ]);

                if(!$ourProduct){
                    throw new \Exception("Product could not be found");
                }

                $ourProduct->name = $request['name'];
                $ourProduct->full_name = $request['full_name'];
                $ourProduct->description = $request['description'];
                $ourProduct->type = $request['type'];
                $ourProduct->taxable = $request['taxable'];
                $ourProduct->maker = $request['maker'];
                $ourProduct->unit_price = $request['unit_price'];
                $ourProduct->unit_volume = $request['unit_volume'];
                $ourProduct->quantity = $request['quantity'];

                if($ourProduct->update()){
                    return $this->response($ourProduct);
                }
            }
        }

        }

    }

    /**
     * Delete a specific product by its id from Quickbooks and our DB
     * 
     * Note: Products with Active = "false", cannot be changed to active, so it's definitive
     *
     * @param [type] $id
     * @return Response
     */
    public function delete($id) : Response
    {
        if($apiItem = $this->quickbooks->Query("SELECT * FROM Item where Id= '{$id}'")){
            
                        $actualItem = reset($apiItem);
                        $updateItem = Item::update($actualItem, [
                            //If you are going to do a full Update, set sparse to false
                            'sparse' => 'true',
                            'Active' => "false",
                        ]);
            
                        if($resultUpdate = $this->quickbooks->Update($updateItem)){
                           
                            $ourProduct = $this->model::findFirst([
                                "conditions" => "id = ?0",
                                "bind" => [$id]
                            ]);
            
                            if(!$ourProduct){
                                throw new \Exception("Product could not be found");
                            }
        
                            $ourProduct->active = "false";
            
                            if($ourProduct->update()){
                                return $this->response($ourProduct);
                            }   
                }
        }

    }
}
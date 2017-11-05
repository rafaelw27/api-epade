<?php
namespace Epade\Controllers;

use Epade\Models\Drivers;
use Baka\Http\Rest\CrudExtendedController;
use Phalcon\Http\Response;

class DriversController extends \Baka\Http\Rest\CrudExtendedController
{
     /**
     * set objects
     *
     * @return void
     */
    public function onConstruct(): void
    {
        $this->model = new Drivers();
    }

    /**
     * Creates a truck
     *
     * @return Response
     */
    public function create() : Response
    {
        if($this->request->isPost()){

        $driver = $this->model->save(
            $this->request->getPost(),
            [
                "first_name",
                "last_name",
                "phone",
                "email",
                
            ]);

        if(!$driver)
        {
            throw new Exception ("Driver could not be created");

        }

        return $this->response("Driver Created");
        }
    
    }

/**
 * Edits a specific driver based on id
 *
 * @param [int] $id
 * @return Response
 */
public function edit($id): Response
{
    if($this->request->isPost())
    {
        $newData = $this->request->getPost();
        $updateFields = ['first_name','last_name','phone','email'];

        $driver = $this->model::findFirst([
            "conditions" => "id = ?0",
            "bind" => [$id]
        ]);


        if(!$driver){
            throw new \Exception("Driver not found");
        }

        if(!$driver->save($newData,$updateFields)){
            throw new \Exception("Driver could not be updated.");
        }

        return $this->response($driver);
    }
}

/**
 * Deletes a specific driver based on id
 *
 * @param [type] $id
 * @return Response
 */
public function delete($id) : Response
{
    $driver = $this->model::findFirst([
        "conditions" => "id = ?0",
        "bind" =>[$id],
    ]);

    if(!$driver->delete())
    {
        throw new \Exception("Driver not found");
    }

    return $this->response("Driver Deleted");

}

/**
 * Fetches all drivers
 *
 * @return Response
 */
public function getDrivers(): Response{
    
    $drivers = $this->model::find();

    if(!$drivers){
        throw new \Exception("There are no drivers");
    }

    return $this->response($drivers);
}

/**
 * Fetches a specific driver based on id
 *
 * @param [int] $id
 * @return Response
 */
public function getDriver($id): Response{
    
    
    $driver = $this->model::findFirst([
        "conditions" => "id = ?0",
        "bind" =>[$id],
    ]);

    if(!$driver){
        throw new \Exception('driver not found');
    }
    
    return $this->response($driver);
}
  


}
<?php
namespace Epade\Controllers;

use Epade\Models\Trucks;
use Baka\Http\Rest\CrudExtendedController;
use Phalcon\Http\Response;

class TrucksController extends \Baka\Http\Rest\CrudExtendedController
{
     /**
     * set objects
     *
     * @return void
     */
    public function onConstruct(): void
    {
        $this->model = new Trucks();
    }

    /**
     * Creates a truck
     *
     * @return Response
     */
    public function create() : Response
    {
        if($this->request->isPost()){

        $truck = $this->model->save(
            $this->request->getPost(),
            [
                "user_id",
                "brand",
                "model",
                "load",
                "capacity",
                "plate"
            ]);

        if(!$truck)
        {
            throw new Exception ("Truck could not be created.");

        }

        return $this->response("Truck Created.");
        }
    
    }

/**
 * Edits a specific truck based on id
 *
 * @param [int] $id
 * @return Response
 */
public function edit($id): Response
{
    if($this->request->isPost())
    {
        $newData = $this->request->getPost();
        $updateFields = ['user_id','brand','model','load','capacity','plate'];

        $truck = $this->model::findFirst([
            "conditions" => "id = ?0",
            "bind" => [$id]
        ]);


        if(!$truck){
            throw new \Exception("Truck not found.");
        }

        if(!$truck->save($newData,$updateFields)){
            throw new \Exception("Truck could not be updated.");
        }

        return $this->response($truck);
    }
}

/**
 * Deletes a specific truck based on id
 *
 * @param [type] $id
 * @return Response
 */
public function delete($id) : Response
{
    $truck = $this->model::findFirst([
        "conditions" => "id = ?0",
        "bind" =>[$id],
    ]);

    if(!$truck->delete())
    {
        throw new \Exception("Truck not found.");
    }

    return $this->response("Truck Deleted.");

}

/**
 * Fetches all trucks
 *
 * @return Response
 */
public function getTrucks(): Response{
    
    $trucks = $this->model::find();

    if(!$trucks){
        throw new \Exception("There are no trucks");
    }

    return $this->response($trucks);
}

/**
 * Fetches a specific truck based on id
 *
 * @param [int] $id
 * @return Response
 */
public function getTruck($id): Response{
    
    
    $truck = $this->model::findFirst([
        "conditions" => "id = ?0",
        "bind" =>[$id],
    ]);

    if(!$truck){
        throw new \Exception('Truck not found');
    }
    
    return $this->response($truck);
}
  


}
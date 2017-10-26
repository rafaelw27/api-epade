<?php
namespace Epade\Controllers;
Use Epade\Models\Trucks;
use Baka\Http\Rest\CrudExtendedController;
use Phalcon\Http\Response;

class TrucksController extends \Baka\Http\Rest\CrudExtendedController
{
    public function onContruct() : void
    {
        $this->model = new Trucks();
    }

public function Create() : Response
{
    $truck = $this->model->save(
        $this->request->getPost(),
        ["id",
        "user_id",
        "capacity",
        "model",
        "brand",
        ]

    );

    if(!$truck)
    {
        throw new Exception ("Truck could not be created.");

    }

    return $this->response("Truck Created.");
    
}

public function editTruck($id): Response
{
    if($this->request->isPost())
    {
        $newData = $this->request->getPost();
        $updateFields = ['id','userid','capacity','model','branch'];

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


public function deleteTruck($id) : Response
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

public function getTrucks(): Response
{
    $trucks = $this->model::find();

    return $this->response($trucks);
}

public function getTruck($id){
    
            $truck = $this->model::findFirst([
                "conditions" => "id = ?0",
                "bind" =>[$id],
            ]);
            
            return $this->response($truck);
        }
  


}
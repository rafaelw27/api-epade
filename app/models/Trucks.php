<?php
namespace Epade\Models;
use Phalcon\Mvc\Model;
use Exception;

class Trucks extends Model
{
    public $id;
    public $user_id;
    public $capacity;
    public $model;
    public $brand;

}

public function Initialize()
{
    $this->SetSchema("api-epade");
}

public function GetSource()
{
    return 'trucks';
}



?>


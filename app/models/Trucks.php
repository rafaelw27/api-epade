<?php

namespace Epade\Models;

use Phalcon\Mvc\Model;
use Exception;

class Trucks extends Model
{
    
    public $id;

    public $user_id;

    public $brand;

    public $model;

    public $load;

    public $capacity;

    public $plate;

    /**
     * Initializer
     *
     * @return void
     */
    public function initialize()
    {
        $this->setSchema("api-epade");
        //$this->belongsTo('id', '\Epade\Models\Users', 'user_type_id', ['alias' => 'user-type']);

    }

     /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'trucks';
    }

}

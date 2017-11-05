<?php

namespace Epade\Models;

use Phalcon\Mvc\Model;
use Exception;

class Drivers extends Model
{
    
    public $id;

    public $first_name;

    public $last_name;

    public $phone;

    public $email;
    
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
        return 'drivers';
    }

}

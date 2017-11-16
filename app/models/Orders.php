<?php

namespace Epade\Models;

use Phalcon\Mvc\Model;
use Exception;

class Orders extends Model
{

    public $id;

    public $user_id;

    public $client_id;

    public $route_id;

    public $status_id;

    public $created_at;

    /**
     * Undocumented function
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
        return 'orders';
    }
}

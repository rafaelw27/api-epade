<?php

namespace Epade\Models;

use Phalcon\Mvc\Model;
use Exception;

class OrdersProducts extends Model
{

    public $id;

    public $order_id;

    public $product_id;

    public $truck_id;

    public $driver_id;

    public $quantity;

    public $volume;

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
        return 'orders_products';
    }
}

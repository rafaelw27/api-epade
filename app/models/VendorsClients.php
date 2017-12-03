<?php

namespace Epade\Models;

use Phalcon\Mvc\Model;
use Exception;

class VendorsClients extends Model
{
    public $id;

    public $vendor_id;

    public $client_id;

    public $visited;

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
        return 'vendors_clients';
    }
}

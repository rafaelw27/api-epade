<?php

namespace Epade\Models;

use Phalcon\Mvc\Model;
use Exception;

class Reports extends Model
{

    public $id;

    public $title;

    public $description;

    public $created_at;

    public $updated_at;


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
        return 'reports';
    }
}
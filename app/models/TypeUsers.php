<?php

namespace Epade\Models;

use Exception;

class TypeUsers extends Model
{

    /**
     * User type id
     */
    public $id;

    /**
     * User type name
     */
    public $name;

    /**
     * Undocumented function
     *
     * @return void
     */
    public function initialize()
    {
        $this->setSchema("api-epade");
        $this->hasMany('id', '\Epade\Models\Users', 'user_type_id', ['alias' => 'user-type']);

    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'user_type';
    }



    
}

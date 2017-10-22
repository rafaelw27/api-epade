<?php

namespace Epade\Models;
use Phalcon\Mvc\Model;

use Exception;

class Users extends Model
{
    
    public $id;

    public $user_type_id;

    public $first_name;

    public $last_name;

    public $email;

    public $password;

    public $phone;

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
        return 'users';
    }

}

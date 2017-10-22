<?php

namespace Epade\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use Epade\Models\Users;

/**
 * Base controller
 *
 */
class IndexController extends \Baka\Http\Rest\CrudExtendedController
{

    /**
     * Index
     *
     * @method GET
     * @url /
     *
     * @return Phalcon\Http\Response
     */
    public function index($id = null): Response
    {


        return $this->response(['Hello World']);
    }

}
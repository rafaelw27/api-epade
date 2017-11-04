<?php

namespace Epade\Controllers;

use Epade\Models\Users;
use Epade\Models\TypeUsers;
use Baka\Http\Rest\CrudExtendedController;
use Phalcon\Http\Response;


class UsersController extends \Baka\Http\Rest\CrudExtendedController{


     /**
     * set objects
     *
     * @return void
     */
    public function onConstruct(): void
    {
        $this->model = new Users();
    }

    /**
     * Creates a new user
     *
     * @return void
     */
    public function create(): Response 
    {
        if($this->request->isPost()){
            $user = $this->model->save(
                $this->request->getPost(),
                [
                    "user_type_id",
                    "first_name",
                    "last_name",
                    "email",
                    "password",
                    "phone",
                ]);

            if(!$user){
                throw new Exception("User could not be created");
            }

            return $this->response('User created');

        }


    }

    /**
     * Edit user info
     *
     * @return void
     */
    public function edit($id): Response{

        if($this->request->isPost()){
            
            $newData = $this->request->getPost();
            $updateFields = ['user_type_id','first_name','last_name','email','password','phone'];

            $user = $this->model::findFirst([
                "conditions" => "id = ?0",
                "bind" => [$id]
            ]);

            if(!$user){
                throw new \Exception("User not found");
            }

            if(!$user->save($newData,$updateFields)){
                throw new \Exception("User could not be updated");
            }

            return $this->response($user);

        }

    }

    /**
     * Delete an specific User based on id
     *
     * @return void
     */
    public function delete($id): Response{

        $user = $this->model::findFirst([
            "conditions" => "id = ?0",
            "bind" =>[$id],
        ]);

        if(!$user->delete()){
            throw new \Exception('User could not be deleted');
        }

        return $this->response("User Deleted");
        

    }

    /**
     * Get all users from db
     *
     * @return void
     */
    public function getUsers(): Response {

        $users = $this->model::find();

        return $this->response($users);

    }

    /**
     * Get a user based of specific id
     *
     * @return void
     */
    public function getUser($id){

        $user = $this->model::findFirst([
            "conditions" => "id = ?0",
            "bind" =>[$id],
        ]);

        $userType = TypeUsers::findFirst([
            "conditions" => "id = ?0",
            "bind" => [$user->user_type_id]
        ]);

        $userData = [];

        foreach ($user as $key => $value) {
            $userData[$key] = $value;
        }

        $userData['user_type_name'] = $userType->name;
            
        return $this->response($userData);
    }

    /**
     * Authenticates a User
     *
     * @return Response
     */
    public function login(){

        if($this->request->isPost()){

            $email = $this->request->getPost('email','string');
            $password = $this->request->getPost('password','string');
        
            $user = $this->model::findFirst([
                "conditions" => "email = ?0 AND password = ?1",
                "bind" =>[$email,$password],
            ]);

            if(!$user){
                return $this->response('Email or Password incorrect');
            }

            $userType = TypeUsers::findFirst([
                "conditions" => "id = ?0",
                "bind" => [$user->user_type_id]
            ]);

            $userData = [];

            foreach ($user as $key => $value) {
                $userData[$key] = $value;
            }

            $userData['user_type_name'] = $userType->name;
                
            return $this->response($userData);

        }
    }


}
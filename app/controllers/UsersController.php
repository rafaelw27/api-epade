<?php

namespace Epade\Controllers;

use Epade\Models\Users;
use Epade\Models\TypeUsers;
use Epade\Models\Drivers;
use Epade\Models\Vendors;
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

            $request = $this->request->getPost();
            
            $user = $this->model;

            $user->user_type_id = $request['user_type_id'];
            $user->first_name = $request['first_name'];
            $user->last_name = $request['last_name'];
            $user->email = $request['email'];
            $user->password = $request['password'];
            $user->phone = $request['phone'];

            if(!$user->save()){
                throw new Exception("User could not be created");
            }

            if($user->user_type_id == 1){
                $driver = new Drivers();

                $driver->id = $user->id;
                $driver->first_name = $user->first_name;
                $driver->last_name = $user->last_name;
                $driver->phone = $user->phone;
                $driver->email = $user->email;

                if(!$driver->save()){
                    throw new Exception("Driver could not be created");
                }


            }

            if($user->user_type_id == 2){
                $vendor = new Vendors();

                $vendor->id = $user->id;
                $vendor->first_name = $user->first_name;
                $vendor->last_name = $user->last_name;
                $vendor->active = true;
                $vendor->phone = $user->phone;
                $vendor->email = $user->email;

                if(!$vendor->save()){
                    throw new Exception("Vendor could not be created");
                }
            }

            return $this->response($user);

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

            $rawData = $this->request->getRawBody();
            $jsonData = json_decode($rawData);
            
            $email = $jsonData->email;
            $password = $jsonData->password;

            // $email = $this->request->getPost('email','string');
            // $password = $this->request->getPost('password','string');

            //If data comes from mobile app
            if($this->request->getContentType() == "application/x-www-form-urlencoded"){

                $email = $this->request->getPost('email','string');
                $password = $this->request->getPost('password','string');

            }
        
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

            if(!$this->session->has("id")){
                $this->session->set("id", $user->id);
            }

            $userData = [];

            foreach ($user as $key => $value) {
                $userData[$key] = $value;
            }

            $userData['user_type_name'] = $userType->name;
            $userData['session_status'] = "Active " . $this->session->get("id"); 
                
            return $this->response($userData);

        }
    }

    /**
     * Log out 
     *
     * @return void
     */
    public function logout(): Response
    {

        if($this->request->isPost()){

            // Destroy the whole session
             $this->session->destroy();

             return $this->response("Session Destroyed");
        }
        
    }


}
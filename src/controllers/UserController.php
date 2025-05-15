<?php

namespace App\Controllers;

require(__DIR__ . '/../models/User.php');


use App\Helpers\Response;
use App\Models\User;


class UserController
{
    public function registerUser()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['firstname']) || empty($input['lastname']) || empty($input['email']) || empty($input['password'])) {
            return Response::json(['error' => 'All fields are required'], 400);
        }

        // Check if user exist in database
        $user = new User();
        $checkuserexist = $user->checkIfUseralreadyexist($input['email']);
        if($checkuserexist){
            return Response::json(['error' => 'User already exist'], 500);
        }
        $result = $user->register($input['firstname'], $input['lastname'], $input['email'], $input['password']);
        if ($result) {
            return Response::json(['message' => 'User registered successfully'], 201);
        } else {
            return Response::json(['error' => 'Registration failed'], 500);
        }
    }

    public function loginUser(){
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['email']) || empty($input['password'])) {
            return Response::json(['error' => 'All fields are required'], 400);
        } 
        $user = new User();
        $result = $user->login($input['email'], $input['password']);
        if(isset($result) && isset($result['success'])){
            return Response::json(['message' => 'User logged in successfully', "result" => json_encode($result)], 201);
        } else {
            return Response::json(['error' => 'Login failed', "result" => json_encode($result)], 500);
        }
        
    }

    public function changepassword(){
        $input = json_decode(file_get_contents('php://input'), true);
        if(empty('password') || empty('currentpassword') || empty('confirmpassword')){
            return Response::json(['error' => 'All fields are required'], 400);
        }
        $user = new User();
        $result = $user->changepass($input['currentpassword'], $input['password'], $input['confirmpassword']);
        if(isset($result['success'])){
            return Response::json(['message' => $result['success']], 201);
        } else {
            return Response::json(['error' => $result['error']], 500);
        }
    }
 
}

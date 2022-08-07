<?php

        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        
        include_once '../../config/core.php';
        include_once '../../../config/Database.php';
        include_once '../../../models/Users.php';
        include_once '../../../models/utils.php';
        include_once '../../../libs/password/passwordLib.php';

        $database = new Database();
        $conn = $database->connect();

        $user = new Users($conn);
        $utils = new Utils();

       
        
        // make sure data is not empty
        if(
            !empty($_POST["firstName"]) &&
            !empty($_POST["lastName"]) &&
            !empty($_POST["email"]) &&
            !empty($_POST["password"]) &&
            !empty($_POST["gender"]) &&
            !empty($_POST["jobRole"]) &&
            !empty($_POST["departmentId"]) &&
            !empty($_POST["address"]) &&
            !empty($_POST["avatarUrl"]) &&
            !empty($_POST["userRole"])
            
        ){
        
            
            $user->firstName = $_POST["firstName"];
            $user->lastName = $_POST["lastName"];
            $user->email = $_POST["email"];
            $user->password = $_POST["password"];
            $user->gender = $_POST["gender"];
            $user->jobRole = $_POST["jobRole"];
            $user->departmentId = $_POST["departmentId"];
            $user->address = $_POST["address"];
            $user->avatarUrl = $_POST["avatarUrl"];
            $user->userRole = $_POST["userRole"];

            // generate access token for user
            $user->secret=$utils->getToken();
        
            // create the user
            if($user->addUser()){
        
                // set response code - 201 created
                http_response_code(201);

                $payload = array("message"=>"User account successfully created","token"=>$user->secret,"userId"=>$conn->lastInsertId());
        
                // tell the user
                echo json_encode(array("status"=>"success","data" => $payload));
            }
        
            // if unable to create the product, tell the user
            else{
        
                // set response code - 503 service unavailable
                http_response_code(503);
        
                // tell the user
                echo json_encode(array("status"=>"error","error" => "Unable to add user."));
            }
        }
        
        // tell the user data is incomplete
        else{
        
            // set response code - 400 bad request
            http_response_code(400);
        
            // tell the user
            echo json_encode(array("status"=>"error","error" => "Unable to add user. Incomplete entries."));
        }
<?php

        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        
        include_once '../../../config/core.php';
        include_once '../../../config/Database.php';
        include_once '../../../models/Users.php';
        include_once '../../../models/utils.php';
        include_once '../../../libs/password/passwordLib.php';

        $database = new Database();
        $conn = $database->connect();

        $user = new Users($conn);
        $utils = new Utils();

        $data = json_decode(file_get_contents('php://input'));

        

        if($_SERVER['REQUEST_METHOD'] == "POST"){

             // make sure data is not empty
        if(
            !empty($data->firstName) &&
            !empty($data->lastName) &&
            !empty($data->email) &&
            !empty($data->password) &&
            !empty($data->gender) &&
            !empty($data->jobRole) &&
            !empty($data->departmentId) &&
            !empty($data->address) &&
            !empty($data->userRole)
            
        ){
        
            
            $user->firstName = $data->firstName;
            $user->lastName = $data->lastName;
            $user->email = $data->email;
            $user->password = $data->password;
            $user->gender = $data->gender;
            $user->jobRole = $data->jobRole;
            $user->departmentId = $data->departmentId;
            $user->address = $data->address;
            $user->avatarUrl = $data->avatarUrl;
            $user->userRole = $data->userRole;

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

        }

       
        
       
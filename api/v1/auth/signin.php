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

       
        
        // make sure data is not empty
        if( !empty($data->email) && !empty($data->password) ){

            $user->email = $data->email;
            
            // check if email exists, also get user details using this emailExists() method
	        $email_exists = $user->emailExists();

            // password_verify($data->password, $user->password)

            // validate login
            if( $email_exists && password_verify($data->password, $user->password)) {
               
                // set response code - 201 created
                http_response_code(201);


                $payload = array("userId"=>$user->id, "firstName" => $user->firstName, "lastName" => $user->lastName, "email" => $user->email,  "gender" => $user->gender, "jobRole" => $user->jobRole,  "department" => $user->departmentId,  "address" => $user->address,  "avatarUrl" => $user->avatarUrl,  "userRole" => $user->userRole,"token"=>$user->secret);

                // tell the user
                echo json_encode(array("status"=>"success","data" => $payload));

            }
            else{
        
                // set response code
                http_response_code(201);
        
                // tell the user
                echo json_encode(array("status"=>"error","error" => "Invalid credentials."));
            }
        }
        
        // tell the user data is incomplete
        else{
        
            // set response code - 400 bad request
            http_response_code(400);
        
            // tell the user
            echo json_encode(array("status"=>"error","error" => "Unable to add user. Incomplete entries."));
        }
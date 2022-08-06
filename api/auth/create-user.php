<?php

        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        
        include_once '../../config/Database.php';
        include_once '../../models/Customer.php';

        $database = new Database();
        $conn = $database->connect();

        $customer = new Customer($conn);

        // get posted data
        $data = json_decode(file_get_contents("php://input"));
        
        // make sure data is not empty
        if(
            !empty($data->name) &&
            !empty($data->email) &&
            !empty($data->phone) &&
            !empty($data->address) &&
            !empty($data->image) &&
            !empty($data->password) &&
            !empty($data->status)
        ){
        
            // set product property values
            $customer->customerFullname = $data->name;
            $customer->customerEmail = $data->email;
            $customer->customerPhone = $data->phone;
            $customer->customerAddress = $data->address;
            $customer->customerImage = $data->image;
            $customer->customerPassword = $data->password;
            $customer->accountStatus = $data->status;
        
            // create the customer
            if($customer->create()){
        
                // set response code - 201 created
                http_response_code(201);
        
                // tell the user
                echo json_encode(array("message" => "Customer was successfuly added."));
            }
        
            // if unable to create the product, tell the user
            else{
        
                // set response code - 503 service unavailable
                http_response_code(503);
        
                // tell the user
                echo json_encode(array("message" => "Unable to add customer."));
            }
        }
        
        // tell the user data is incomplete
        else{
        
            // set response code - 400 bad request
            http_response_code(400);
        
            // tell the user
            echo json_encode(array("message" => "Unable to add customer. Data is incomplete."));
        }
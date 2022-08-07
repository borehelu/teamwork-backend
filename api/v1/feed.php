<?php

        header("Access-Control-Allow-Origin: *");
        header("Content-Type: multipart/form-data; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        
        include_once '../../config/core.php';
        include_once '../../config/Database.php';
        include_once '../../models/Posts.php';


    if($_SERVER['REQUEST_METHOD'] == "GET"){
        
    }


        ?>
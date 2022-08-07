<?php

        header("Access-Control-Allow-Origin: *");
        header("Content-Type: multipart/form-data; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        
        include_once '../../config/core.php';
        include_once '../../config/Database.php';
        include_once '../../models/Posts.php';
        include_once '../../models/utils.php';

        $database = new Database();
        $conn = $database->connect();

        $post = new Posts($conn);
        $utils = new Utils();


        if($_SERVER['REQUEST_METHOD'] == "POST"){
              // make sure data is not empty
            if( !empty($_POST["title"]) ){
                $post->userId = $_POST["userId"];
                $post->title = $_POST["title"];

                if(!empty($_POST["article"])){
                    $post->postType = 0;
                    $post->article = $_POST["article"];
                    

                    if($post->addPost()){
                        
                        // set response code - 201 created
                        http_response_code(201);

                        $payload = array("message"=>"Article successfully posted","articleId"=>$conn->lastInsertId(),"createdOn"=>$post->createdOn,"title"=>$post->title,"article"=>$post->article);
                
                        // tell the user
                        echo json_encode(array("status"=>"success","data" => $payload));
            

                    }else{
                            // set response code - 503 service unavailable
                            http_response_code(503);
                    
                            // tell the user
                            echo json_encode(array("status"=>"error","error" => "Unable to add post."));

                    }


                }else{
                    $post->postType = 1;
                    $post->image = $utils->getToken();
                    $post->uploadImage();


                    if($post->addPost()){
                            // set response code - 201 created
                            http_response_code(201);

                            $payload = array("message"=>"Gif successfully posted","gifId"=>$conn->lastInsertId(),"createdOn"=>$post->createdOn,"title"=>$post->title,"imageUrl"=>$home_url . "uploads/{$post->image}");
                    
                            // tell the user
                            echo json_encode(array("status"=>"success","data" => $payload));
            

                    }else{
                        // set response code - 503 service unavailable
                            http_response_code(503);
                    
                            // tell the user
                            echo json_encode(array("status"=>"error","error" => "Unable to add post."));
                    }
                }
            
                
        
            
            }
            
            // tell the user data is incomplete
            else{
            
                // set response code - 400 bad request
                http_response_code(400);
            
                // tell the user
                echo json_encode(array("status"=>"error","error" => "Unable to add post. Incomplete entries."));
            }

        }elseif ($_SERVER['REQUEST_METHOD'] == "GET") {

            $payload = array();

            if(!empty($_GET["id"])){
                $stmt = $post->readAllPosts();

                if($stmt->rowCount() > 0 ){
                    extract($row = $stmt->fetch(PDO::FETCH_ASSOC));
                    if($postType == 1){
                        $payload[] = array("id"=>$id,"createdOn"=>$createdOn,"title"=>$title,"imageUrl"=>$home_url . "uploads/{$image}","authorId"=>$userId);

                    }else{
                        $payload[] = array("id"=>$id,"createdOn"=>$createdOn,"title"=>$title,"article"=>$article,"authorId"=>$userId);
                    }
                }


            }else{

                http_response_code(201);

                


                $stmt = $post->readAllPosts();

                if($stmt->rowCount() > 0 ){
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                        extract($row);
                        if($postType == 1){
                            $payload[] = array("id"=>$id,"createdOn"=>$createdOn,"title"=>$title,"imageUrl"=>$home_url . "uploads/{$image}","authorId"=>$userId);

                        }else{
                            $payload[] = array("id"=>$id,"createdOn"=>$createdOn,"title"=>$title,"article"=>$article,"authorId"=>$userId);
                        }
                    }
                }
                
                
            

            }
            

                // tell the user
                echo json_encode(array("status"=>"success","data" => $payload));
 
                
                
        }
        

       
        
      
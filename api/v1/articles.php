<?php

        header("Access-Control-Allow-Origin: *");
        header("Content-Type: multipart/form-data; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        
        include_once '../../config/core.php';
        include_once '../../config/Database.php';
        include_once '../../models/Posts.php';
        include_once '../../models/Comments.php';
        include_once '../../models/utils.php';
        include_once '../../models/Users.php';


        $database = new Database();
        $conn = $database->connect();

        $post = new Posts($conn);
        $user = new Users($conn);
        $comment = new Comments($conn);
        $utils = new Utils();

        $access_token = null;

        foreach(getallheaders() as $name => $value){
            if($name == "token" && $user->getUserIdFromToken($value) != null){
                $access_token = $value;
            }
        }

        $data = json_decode(file_get_contents('php://input'));
      

        $requestMethod = $_SERVER['REQUEST_METHOD'];

        switch ($requestMethod) {
            case 'POST':
                
                break;
            case 'GET':
            
                break;
            case 'PATCH':
            
                break;
            case 'DELETE':
        
                break;
            
            default:
                
                break;
        }



        if($access_token != null){

            if($_SERVER['REQUEST_METHOD'] == "PATCH" && !empty($data->id)){
              

                $post->id = $data->id;
                $post->title = $data->title;
                $post->article = $data->article;

                if($post->updatePost()){
                    $payload = array("message"=>"Post updated successfully");
                    echo json_encode(array("status"=>"success","data" => $payload));
                    exit;


                }else{

                    $payload = array("message"=>"Error updating post");
                    echo json_encode(array("status"=>"error","data" => $payload));
                    exit;

                }

            }


        if($_SERVER['REQUEST_METHOD'] == "POST"){

            if(!empty($data->postId)){

                $comment->postId = $data->postId;
                $comment->userId = $user->getUserIdFromToken($access_token);
                $comment->comment = $data->comment;

                if($comment->addComment()){

                    $payload = array("message"=>"Comment successfully added");
                    echo json_encode(array("status"=>"success","data" => $payload));
                    exit;

                }else{

                    $payload = array("message"=>"Error adding comment");
                    echo json_encode(array("status"=>"error","data" => $payload));
                    exit;
                }

            }


              // make sure data is not empty
            if( !empty($data->title) || !empty($_POST["title"]) ){
              
                $post->userId = $user->getUserIdFromToken($access_token);
                $post->title = (!empty($data->title))? $data->title: $_POST['title'];

                if(!empty($data->article) || !empty($_POST["article"]) ){
                    $post->postType = 0;
                    $post->article = (!empty($data->article))? $data->article: $_POST["article"];
                    

                    if($post->addPost()){
                        
                        // set response code - 201 created
                        http_response_code(201);

                        $payload = array("message"=>"Article successfully posted","articleId"=>$conn->lastInsertId(),"createdOn"=>$post->createdOn,"title"=>$post->title,"article"=>$post->article);
                
                        // tell the user
                        echo json_encode(array("status"=>"success","data" => $payload));
                        exit;

                    }else{
                            // set response code - 503 service unavailable
                            http_response_code(503);
                    
                            // tell the user
                            echo json_encode(array("status"=>"error","error" => "Unable to add post."));

                            exit;
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
                        exit;

                    }else{
                        // set response code - 503 service unavailable
                            http_response_code(503);
                    
                            // tell the user
                            echo json_encode(array("status"=>"error","error" => "Unable to add post."));
                            exit;
                    }
                }
            
                
        
            
            }
            
            // tell the user data is incomplete
            else{
            
                // set response code - 400 bad request
                http_response_code(400);
            
                // tell the user
                echo json_encode(array("status"=>"error","error" => "Unable to add post. Incomplete entries."));
                exit;
            }

        }elseif ($_SERVER['REQUEST_METHOD'] == "GET") {

            $payload = array();

            if(!empty($data->id)){
                $stmt = $post->readOnePost($data->id);

                if($stmt->rowCount() > 0 ){
                    extract($row = $stmt->fetch(PDO::FETCH_ASSOC));
                    if($postType == 1){
                        $payload[] = array("id"=>$id,"createdOn"=>$createdOn,"title"=>$title,"imageUrl"=>$home_url . "uploads/{$image}","authorId"=>$userId,"comments"=>$comment->readAllComments($data->id));

                    }else{
                        $payload[] = array("id"=>$id,"createdOn"=>$createdOn,"title"=>$title,"article"=>$article,"authorId"=>$userId,"comments"=>$comment->readAllComments($data->id));
                    }
                }else{
                     // tell the user
                echo json_encode(array("status"=>"error","data" => array("message"=>"No data found")));
 
                exit;
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
 
                exit;
                
        }elseif (!empty($data->id) && $_SERVER['REQUEST_METHOD'] == "DELETE") {
            
            $post->id = $data->id;
            $post->userId = $user->getUserIdFromToken($access_token);

            if($post->deletePost()){

                $payload = array("message"=>"Post successfully deleted");
                echo json_encode(array("status"=>"success","data" => $payload));
                exit;

            }else{
                $payload = array("message"=>"Error deleting post");
                echo json_encode(array("status"=>"error","data" => $payload));
                exit;
            }
            
            
            
            
        }
    }else{

        echo json_encode(array("status"=>"error","data" => array("message" => "Authentication error")));
        exit;

    }

       
        
      
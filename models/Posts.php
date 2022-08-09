<?php

class Posts{

    
    public $id;
    public $userId;
    public $title;
    public $article;
    public $image;
    public $postType;
    public $createdOn;
 
    
   

    //db properties
    public $conn;
    public $tableName = 'posts';

    public function __construct($db){
        $this->conn = $db;
    }

    public function addPost(){

        $this->createdOn  = date('Y-m-d H:i:s');

        // query to insert record
        $query = "INSERT INTO
                         " . $this->tableName . "
                  SET
                    userId = :userId,
                    title = :title,
                    article = :article,
                    image = :image,
                    postType = :postType,
                    createdOn = :createdOn

                        ";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->title=htmlspecialchars(strip_tags($this->title));
        $this->article=htmlspecialchars(strip_tags($this->article));
        $this->postType=htmlspecialchars(strip_tags($this->postType));
        $this->userId=htmlspecialchars(strip_tags($this->userId));
        

        // bind values
        $stmt->bindParam(":userId", $this->userId);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":article", $this->article);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":postType", $this->postType);
        $stmt->bindParam(":createdOn", $this->createdOn);
        
        

        // execute query
        if($stmt->execute()){
        return true;
        }

        return false;

    }

    public function uploadImage(){

        // specify valid image types / formats
        $valid_formats = array("jpg", "png","gif");
    
        // specify maximum file size of file to be uploaded
        $max_file_size = 1024*3000; // 3MB
    
        // directory where the files will be uploaded
        $path = $_SERVER['DOCUMENT_ROOT']."/teamwork-backend/uploads/";
    
        // count or number of files
        $count = 0;
    
        // if files were posted
        if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST"){
    
          // Loop $_FILES to execute all files
          foreach ($_FILES['image']['name'] as $f => $name){
    
            if ($_FILES['image']['error'][$f] == 4) {
              continue; // Skip file if any error found
            }
    
            if ($_FILES['image']['error'][$f] == 0) {
              if ($_FILES['image']['size'][$f] > $max_file_size) {
                $message[] = "$name is too large!.";
                continue; // Skip large files
              }
              elseif( ! in_array(pathinfo($name, PATHINFO_EXTENSION), $valid_formats) ){
                $message[] = "$name is not a valid format";
                continue; // Skip invalid file formats
              }
    
              // No error found! Move uploaded files
              else{

                $this->image = $this->image.".".pathinfo($name, PATHINFO_EXTENSION);
                if(move_uploaded_file($_FILES["image"]["tmp_name"][$f], $path.$this->image)){
                  $count++; // Number of successfully uploaded file
    

                }
              }
            }
          }

          
        }
        


      }


      public function readAllPosts(){
        // query select all classes
        $query = "SELECT *
        FROM " . $this->tableName . " ORDER BY createdOn";
    
        // prepare query statement
        $stmt = $this->conn->prepare( $query );
    
        // execute query
        $stmt->execute();
    
        // return values
        return $stmt;
        }
      
        public function readOnePost($id){
            // query select all classes
            $query = "SELECT * FROM " . $this->tableName . " WHERE id = :id ORDER BY createdOn";
        
            // prepare query statement
            $stmt = $this->conn->prepare( $query );

            $stmt->bindParam(":id", $id);
        
            // execute query
            $stmt->execute();
        
            // return values
            return $stmt;

            }

            function deletePost(){

                // delete query
                $query = "DELETE FROM " . $this->tableName . " WHERE id = ? AND userId = ?";
        
                // prepare query statement
                $stmt = $this->conn->prepare($query);
        
                // bind record id
                $stmt->bindParam(1, $this->id);
                $stmt->bindParam(2, $this->userId);
                
        
                // execute the query
                if($result = $stmt->execute()){
                    return true;
                }else{
                    return false;
                }
            }

            function updatePost(){

                // update query
                $query = "UPDATE " . $this->tableName . "
                    SET title = :title,
                        article = :article
                    WHERE  id = :id";
            
                // prepare query statement
                $stmt = $this->conn->prepare($query);
            
                // bind values
                $stmt->bindParam(':title', $this->title);
                $stmt->bindParam(':article', $this->article);
                $stmt->bindParam(':id', $this->id);
            
                // execute the query
                if($stmt->execute()){
                  return true;
                }else{
                  return false;
                }

            }

      

    


    }

<?php

class Comments{

    
    public $id;
    public $postId;
    public $userId;
    public $comment;
    public $createdOn;
 
    
   

    //db properties
    public $conn;
    public $tableName = 'comments';

    public function __construct($db){
        $this->conn = $db;
    }

    public function addComment(){

        $this->createdOn  = date('Y-m-d H:i:s');

        // query to insert record
        $query = "INSERT INTO
                         " . $this->tableName . "
                  SET
                    userId = :userId,
                    postId = :postId,
                    comment = :comment,
                    createdOn = :createdOn

                        ";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->postId=htmlspecialchars(strip_tags($this->postId));
        $this->userId=htmlspecialchars(strip_tags($this->userId));
        $this->comment=htmlspecialchars(strip_tags($this->comment));
        

        // bind values
        $stmt->bindParam(":userId", $this->userId);
        $stmt->bindParam(":postId", $this->postId);
        $stmt->bindParam(":comment", $this->comment);
        $stmt->bindParam(":createdOn", $this->createdOn);
        
        

        // execute query
        if($stmt->execute()){
        return true;
        }

        return false;

    }


        


      


      public function readAllComments($postId){
        // query select all classes
        $query = "SELECT * FROM " . $this->tableName . " WHERE postId = :postId ORDER BY createdOn";
    
        // prepare query statement
        $stmt = $this->conn->prepare( $query );

        $stmt->bindParam(":postId", $postId);
    
        // execute query
        $stmt->execute();
    
        // return values
        if($stmt->rowCount() > 0 ){
            $comments = array();

            while($row = $stmt->fetch("PDO::FETCH_ASSOC")){
                extract($row);
                 $comments[] = array("commentId"=>$id,"comment"=>$comment, "authorId"=>$userId, "createdOn"=>$createdOn);

            }

            return $comments;

        }else{
            return array();
        }
        }
      
        public function readOnePost(){
            // query select all classes
            $query = "SELECT *
            FROM " . $this->tableName . "WHERE id = :id ORDER BY createdOn";
        
            // prepare query statement
            $stmt = $this->conn->prepare( $query );

            $stmt->bindParam(":id", $this->id);
        
            // execute query
            $stmt->execute();
        
            // return values
            return $stmt;
            }

            function deletePost(){

                // delete query
                $query = "DELETE FROM " . $this->tableName . " WHERE id = ?";
        
                // prepare query statement
                $stmt = $this->conn->prepare($query);
        
                // bind record id
                $stmt->bindParam(1, $this->id);
                
        
                // execute the query
                if($result = $stmt->execute()){
                    return true;
                }else{
                    return false;
                }
            }

      

    


    }

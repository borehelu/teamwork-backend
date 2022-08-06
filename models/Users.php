<?php

class Users{

    
    public $userid;
    public $firstName;
    public $lastName;
    public $email;
    public $password;
    public $gender;
    public $jobRole;
    public $departmentId;
    public $address;
    public $avatarUrl;
    public $userRole;
    public $secret;
    
   

    //db properties
    public $conn;
    public $tableName = 'users';

    public function __construct($db){
        $this->conn = $db;
    }

    public function addUser(){
        // query to insert record
        $query = "INSERT INTO
                         " . $this->tableName . "
                  SET
                    firstName = :firstName,
                    lastName = :lastName,
                    email = :email,
                    password = :password,
                    gender = :gender,
                    jobRole = :jobRole,
                    departmentId = :departmentId,
                    address = :address,
                    avatarUrl = :avatarUrl,
                    userRole = :userRole,
                    secret = :secret

                        ";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->firstName=htmlspecialchars(strip_tags($this->firstName));
        $this->lastName=htmlspecialchars(strip_tags($this->lastName));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->password=htmlspecialchars(strip_tags($this->password));
        $this->gender=htmlspecialchars(strip_tags($this->gender));
        $this->jobRole=htmlspecialchars(strip_tags($this->jobRole));
        $this->departmentId=htmlspecialchars(strip_tags($this->departmentId));
        $this->address=htmlspecialchars(strip_tags($this->address));
        $this->avatarUrl=htmlspecialchars(strip_tags($this->avatarUrl));
        $this->userRole=htmlspecialchars(strip_tags($this->userRole));
        $this->secret=htmlspecialchars(strip_tags($this->secret));


        // bind values
        $stmt->bindParam(":firstName", $this->firstName);
        $stmt->bindParam(":lastName", $this->lastName);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":jobRole", $this->jobRole);
        $stmt->bindParam(":departmentId", $this->departmentId);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":avatarUrl", $this->avatarUrl);
        $stmt->bindParam(":userRole", $this->userRole);
        $stmt->bindParam(":secret", $this->secret);
        

        // execute query
        if($stmt->execute()){
        return true;
        }

        return false;

    }

    function update(){
  
        // update query
        $query = "UPDATE
                    " . $this->tableName . "
                SET
                    mealName=:name,
                    mealPrice=:price,
                    mealDescription=:description,
                    mealCategoryId=:category_id,
                    mealImage=:mealImage,
                    mealStatus=:mealStatus
                WHERE
                    mealId = :id";
        
            // prepare query statement
            $stmt = $this->conn->prepare($query);
        
            // sanitize
            $this->mealName=htmlspecialchars(strip_tags($this->mealName));
            $this->mealPrice=htmlspecialchars(strip_tags($this->mealPrice));
            $this->mealDescription=htmlspecialchars(strip_tags($this->mealDescription));
            $this->mealCategoryId=htmlspecialchars(strip_tags($this->mealCategoryId));
            $this->mealImage=htmlspecialchars(strip_tags($this->mealImage));
            $this->mealStatus=htmlspecialchars(strip_tags($this->mealStatus));
            $this->mealId=htmlspecialchars(strip_tags($this->mealId));
        
            // bind new values
            $stmt->bindParam(':id', $this->mealId);
            $stmt->bindParam(":name", $this->mealName);
            $stmt->bindParam(":price", $this->mealPrice);
            $stmt->bindParam(":description", $this->mealDescription);
            $stmt->bindParam(":mealImage", $this->mealImage);
            $stmt->bindParam(":category_id", $this->mealCategoryId);
            $stmt->bindParam(":mealStatus", $this->mealStatus);
    
            // execute the query
            if($stmt->execute()){
                return true;
            }
                return false;
}
            // delete the product
            function delete(){

                    // delete query
                    $query = "DELETE FROM " . $this->tableName . " WHERE customer = ?";
                
                    // prepare query
                    $stmt = $this->conn->prepare($query);
                
                    // sanitize
                    $this->mealId=htmlspecialchars(strip_tags($this->mealId));
                
                    // bind id of record to delete
                    $stmt->bindParam(1, $this->mealId);
                
                    // execute query
                    if($stmt->execute()){
                        return true;
                    }
                
                    return false;
            }


}
<?php
function db_connect(){
    $conn = new mysqli("localhost", "root", "", "movies");
    if($conn->connect_error){
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
?>

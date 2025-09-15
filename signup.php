<?php
error_log("pred check");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = $_POST["username"];
    $password = $_POST["password"];

    try {
        error_log("po check");
        error_log($username);
        error_log($password);
        
        require_once 'includes/dbh.inc.php';
        require_once 'includes/signup_model.inc.php';
        require_once 'includes/signup_contr.inc.php';

        if (is_input_empty($username, $password) || is_username_taken($pdo, $username)){
            error_log("signup: controll failed");
            die();
        } else {
            create_user($pdo, $username, $password);
            error_log("signup: successfully created user!");
        }
    } catch (PDOException $e){
        die("Query failed: " . $e->getMessage());
    }
} else {
    error_log("Sigup: not a post connection");
}







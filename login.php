<?php

if ($_SERVER["REQUEST_METHOD"] === "POST"){
    $username = $_POST["username"];
    $password = $_POST["password"];

    try {
        require_once 'includes/dbh.inc.php';
        require_once 'includes/login_model.inc.php';
        require_once 'includes/login_contr.inc.php';

        if (is_input_empty($username, $password)) {
            http_response_code(400); // Bad Request
            echo json_encode(["status" => "error", "message" => "Please fill all forms."]);
            die();
        }
        $result = get_user($pdo, $username);
        if (is_username_wrong($result)) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Username does not exist"]);
            die();
        }elseif (!is_username_wrong($result) && is_password_wrong($password, $result["password"])){
            http_response_code(401); 
            echo json_encode(["status" => "error", "message" => "Wrong password!"]);
            die();
        } else{

            http_response_code(201); // Created
            echo json_encode(["status" => "success", "message" => "Successful login."]);
            error_log("login: login successfull!");

        }
        

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database operation failed: " . $e->getMessage()]);
        die();
    }

}else {
    error_log("Login: not a post method");
    die();
}
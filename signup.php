<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = $_POST["username"];
    $password = $_POST["password"];

    try {
        require_once 'includes/dbh.inc.php';
        require_once 'includes/signup_model.inc.php';
        require_once 'includes/signup_contr.inc.php';

        if (is_input_empty($username, $password)){
            http_response_code(400); // Bad Request
            echo json_encode(["status" => "error", "message" => "Please fill all forms."]);
            die();
        } elseif (is_username_taken($pdo, $username)){
            http_response_code(409); // Conflict
            echo json_encode(["status" => "error", "message" => "Username already taken."]);
            die();
        } else {
            create_user($pdo, $username, $password);
            // tu se bodo tudi naredili zapisi v counter tabeli

            http_response_code(201); // Created
            echo json_encode(["status" => "success", "message" => "Account created successfully."]);
            error_log("signup: successfully created user!");
        }
    } catch (PDOException $e){
        die("Query failed: " . $e->getMessage());
    }
} else {
    error_log("Sigup: not a post connection");
}







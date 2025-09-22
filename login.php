<?php

header('Content-Type: application/json');

require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;

require_once 'includes/credentials.inc.php';

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
        } elseif (!is_username_wrong($result) && is_password_wrong($password, $result["password"])){
            http_response_code(401); 
            echo json_encode(["status" => "error", "message" => "Wrong password!"]);
            die();
        } else {
            // Authentication successful

            // Set session cookie to expire in 7 days (60 * 60 * 24 * 7 seconds)
            $expire_time = time() + (60 * 60 * 24 * 7);
            
            $payload = array(
                "iss" => "sesvete-server.com", // Issuer of the token
                "aud" => "gacha-app-apache", // Audience of the token
                "iat" => time(), // Issued at time
                "exp" => $expire_time, // Expiration time: 7 days
                "uid" => $result["uid"] // The user's unique ID
            );

            $jwt = JWT::encode($payload, $secret_key, 'HS256');

            http_response_code(200);
            // Send the expiration timestamp to the app
            echo json_encode([
                "status" => "success",
                "message" => "Login successful!",
                "uid" => $result["uid"],
                "username" => $result["username"],
                "token" => $jwt
            ]);
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
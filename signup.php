<?php

header('Content-Type: application/json');

require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;

require_once 'includes/credentials.inc.php';

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
            // turns of autocommit mode
            $pdo->beginTransaction();

            create_user($pdo, $username, $password);
            $uid = $pdo->lastInsertId();
            create_user_counter($pdo, $uid);

            // commit database transcation
            $pdo->commit();

            // Authentication successful

            // Set session cookie to expire in 7 days (60 * 60 * 24 * 7 seconds)
            // Set session cookie to expire in 1 day (60 * 60 * 24 seconds)
            $time = time();
            $expire_time = $time + (60 * 60 * 24);

            $payload = array(
                "iss" => "sesvete-server.com", // Issuer of the token
                "aud" => "gacha-app-apache", // Audience of the token
                "iat" => $time, // Issued at time
                "exp" => $expire_time, // Expiration time: 1 day
                "uid" => $uid // The user's unique ID
            );

            $jwt = JWT::encode($payload, $secret_key, 'HS256');

            http_response_code(200);
            // Send the expiration timestamp to the app
            echo json_encode([
                "status" => "success",
                "message" => "Sign up successful!",
                "uid" => $uid,
                "username" => $username,
                "expireTime" => $expire_time,
                "token" => $jwt
            ]);
        }
    } catch (PDOException $e){
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database operation failed: " . $e->getMessage()]);
        die();
    }
} else {
    error_log("Sigup: not a post connection");
}







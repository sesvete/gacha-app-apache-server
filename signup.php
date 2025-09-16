<?php

header('Content-Type: application/json');

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
            $expire_time = time() + (60 * 60 * 24 * 7);
            session_set_cookie_params($expire_time);

            session_start();

            $_SESSION["user_uid"] = $uid;
            $_SESSION["user_username"] = $username;

            http_response_code(200);
            // Send the expiration timestamp to the app
            echo json_encode([
                "status" => "success",
                "message" => "Login successful!",
                "uid" => $uid,
                "username" => $username,
                "expires_at" => $expire_time // UNIX timestamp for expiration
            ]);
            error_log("SignUp successful for user: " . $_SESSION["user_username"]);
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







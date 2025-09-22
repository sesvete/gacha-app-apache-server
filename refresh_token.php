<?php

header('Content-Type: application/json');

require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

require_once 'includes/credentials.inc.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Authorization header missing."]);
        die();
    }

    $auth_header = $headers['Authorization'];
    list($jwt) = sscanf($auth_header, 'Bearer %s');

    if (!$jwt) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "JWT token missing."]);
        die();
    }
    $issued_at = time();
    $expire_time = $issued_at + (60 * 60 * 24 * 7);

    try{
        $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
        $uid = $decoded->uid;
        // Create a new token with a new expiration time
        $payload = array(
                "iss" => "sesvete-server.com", // Issuer of the token
                "aud" => "gacha-app-apache", // Audience of the token
                "iat" => $issued_at, // Issued at time
                "exp" => $expire_time, // Expiration time: 1 day
                "uid" => $uid // The user's unique ID
            );
        
        $newJwt = JWT::encode($payload, $secret_key, 'HS256');

        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Token refreshed successfully.",
            "expireTime" => $expire_time,
            "token" => $newJwt
        ]);

    } catch (ExpiredException $e) {
        error_log("expired token");

        http_response_code(401); // Unauthorized
        echo json_encode(["status" => "error", "message" => "Token is expired: " . $e->getMessage()]);
        die();

        
    } catch (\Exception $e) {
        http_response_code(401); // Unauthorized
        echo json_encode(["status" => "error", "message" => "Invalid token: " . $e->getMessage()]);
        die();
    }

} else {
    error_log("Login: not a post method");
    die();
}
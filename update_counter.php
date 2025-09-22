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

    try {

        $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
        $uid = $decoded->uid;

        $game = $_POST["game"];
        $banner = $_POST["banner"];
        $progress = $_POST["progress"];
        $guaranteed = $_POST["guaranteed"];

        require_once 'includes/dbh.inc.php';
        require_once 'includes/update_counter_model.inc.php';

        update_counter($pdo, $uid, $game, $banner, $progress, $guaranteed);

        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Successfully updated database!"]);

    } catch (ExpiredException $e) {
        http_response_code(401); // Unauthorized
        echo json_encode(["status" => "error", "message" => "Token has expired."]);
        die();
    } catch (\Exception $e) {
        http_response_code(401); // Unauthorized
        echo json_encode(["status" => "error", "message" => "Invalid token: " . $e->getMessage()]);
        die();
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database operation failed: " . $e->getMessage()]);
        die();
    }

} else {
    error_log("Login: not a post method");
    die();
}
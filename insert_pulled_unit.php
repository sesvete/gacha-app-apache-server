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
        $unit_name = $_POST["unit_name"];
        $num_of_pulls = $_POST["num_of_pulls"];
        $from_banner = $_POST["from_banner"];
        $date = $_POST["date"];

        require_once 'includes/dbh.inc.php';
        require_once 'includes/insert_pulled_unit_model.inc.php';

        insert_pull($pdo, $uid, $game, $banner, $unit_name, $num_of_pulls, $from_banner, $date);    
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "pull insert was successfull!"]);
    } catch (ExpiredException $e) {
        $pdo->rollBack();
        http_response_code(401); // Unauthorized
        echo json_encode(["status" => "error", "message" => "Token has expired."]);
        die();
    } catch (\Exception $e) {
        $pdo->rollBack();
        http_response_code(401); // Unauthorized
        echo json_encode(["status" => "error", "message" => "Invalid token: " . $e->getMessage()]);
        die();
    } catch (PDOException $e){
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database operation failed: " . $e->getMessage()]);
        die();
    }

} else {
    error_log("Login: not a post method");
    die();
}
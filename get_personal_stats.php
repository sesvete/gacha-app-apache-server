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

        require_once 'includes/dbh.inc.php';
        require_once 'includes/get_stats_model.inc.php';

        $personal_pulls_history = get_personal_history_for_stats($pdo, $uid, $game, $banner);

        $num_of_pulls_list = get_num_of_pulls_values($personal_pulls_history);
        $from_banner_list = get_from_banner_values($personal_pulls_history);
        $fifty_fifty_outcomes = determine_fifty_fifty_outcomes($from_banner_list);

        $response_data = [
            "numOfPullsList" => $num_of_pulls_list,
            "fiftyFiftyOutcomes" => $fifty_fifty_outcomes
        ];

        http_response_code(200);
        echo json_encode($response_data);

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
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database operation failed: " . $e->getMessage()]);
    }

} else {
    error_log("Login: not a post method");
    die();
}
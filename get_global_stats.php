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
        $game = $_POST["game"];
        $banner = $_POST["banner"];
        require_once 'includes/dbh.inc.php';
        require_once 'includes/get_stats_model.inc.php';

        $all_user_stats = [];
        $global_pulls_history = get_global_history_for_stats($pdo, $game, $banner);

        // iterate through all the users
        foreach( $global_pulls_history as $pull ) {
            $num_of_pulls_list = get_num_of_pulls_values($pull);
            $from_banner_list = get_from_banner_values($pull);
            $fifty_fifty_outcomes = determine_fifty_fifty_outcomes($from_banner_list);

            $user_data = [
            "numOfPullsList" => $num_of_pulls_list,
            "fiftyFiftyOutcomes" => $fifty_fifty_outcomes
            ];
            $all_user_stats[] = $user_data;      
        }

        http_response_code(200);
        echo json_encode($all_user_stats);

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
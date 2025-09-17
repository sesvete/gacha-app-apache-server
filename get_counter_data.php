<?php

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $uid = $_POST["uid"];
    $game = $_POST["game"];
    $banner = $_POST["banner"];

    try {
        require_once 'includes/dbh.inc.php';
        require_once 'includes/get_counter_data_model.inc.php';

        $result = get_counter_data_form_db($pdo, $uid, $game, $banner);
        if ($result) {
            http_response_code(200);
            echo json_encode([
                "progress" => $result["progress"],
                "guaranteed" => $result["guaranteed"]
            ]);

        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Database operation failed: " . $e->getMessage()]);
            die();
        }
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database operation failed: " . $e->getMessage()]);
        die();
    }


} else {
    error_log("Login: not a post method");
    die();
}
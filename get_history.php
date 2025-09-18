<?php

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $uid = $_POST["uid"];
    $game = $_POST["game"];
    $banner = $_POST["banner"];

    try {
        require_once 'includes/dbh.inc.php';
        require_once 'includes/get_history_model.inc.php';

        $result = get_personal_history($pdo, $uid, $game, $banner);
        
        http_response_code(200);
        error_log(json_encode($result));
        echo json_encode($result);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database operation failed: " . $e->getMessage()]);
    }
} else {
    error_log("Login: not a post method");
    die();
}
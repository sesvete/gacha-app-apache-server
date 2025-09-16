<?php

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $uid = $_POST["uid"];
    $game = $_POST["game"];
    $banner = $_POST["banner"];
    $progress = $_POST["progress"];
    $guaranteed = $_POST["guaranteed"];

    try {
        require_once 'includes/dbh.inc.php';
        require_once 'includes/update_counter_model.inc.php';

        update_counter($pdo, $uid, $game, $banner, $progress, $guaranteed);

        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Successfully updated database!"]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database operation failed: " . $e->getMessage()]);
        die();
    }

} else {
    error_log("Login: not a post method");
    die();
}
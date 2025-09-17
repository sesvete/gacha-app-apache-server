<?php

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $uid = $_POST["uid"];
    $game = $_POST["game"];
    $banner = $_POST["banner"];
    $unit_name = $_POST["unit_name"];
    $num_of_pulls = $_POST["num_of_pulls"];
    $from_banner = $_POST["from_banner"];
    $date = $_POST["date"];

    // TODO: also set ccounter to 0 in same call!

    try {
        require_once 'includes/dbh.inc.php';
        require_once 'includes/insert_pulled_unit_model.inc.php';

        insert_pull($pdo, $uid, $game, $banner, $unit_name, $num_of_pulls, $from_banner, $date);
    
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "pull insert was successfull!"]);

    } catch (PDOException $e){
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database operation failed: " . $e->getMessage()]);
        die();
    }

} else {
    error_log("Login: not a post method");
    die();
}
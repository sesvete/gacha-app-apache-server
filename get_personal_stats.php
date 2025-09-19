<?php

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $uid = $_POST["uid"];
    $game = $_POST["game"];
    $banner = $_POST["banner"];

    try {
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

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database operation failed: " . $e->getMessage()]);
    }



} else {
    error_log("Login: not a post method");
    die();
}
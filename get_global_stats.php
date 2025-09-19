<?php

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $game = $_POST["game"];
    $banner = $_POST["banner"];

    try {
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

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database operation failed: " . $e->getMessage()]);
    }

} else {
    error_log("Login: not a post method");
    die();
}
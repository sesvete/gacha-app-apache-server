<?php

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $uid = $_POST["uid"];
    $game = $_POST["game"];
    $banner = $_POST["banner"];

    try {
        require_once 'includes/dbh.inc.php';
        require_once 'includes/get_counter_data_model.inc.php';

        // turns of autocommit mode
        $pdo->beginTransaction();

        $result_counter = get_counter_data_form_db($pdo, $uid, $game, $banner);

        // also get data from last pulled unit
        if ($result_counter) {
            $result_pull = get_last_pulled_unit($pdo, $uid, $game, $banner);

            // commit database transcation
            $pdo->commit();
            if ($result_pull) {
                echo json_encode([
                    "progress" => $result_counter["progress"],
                    "guaranteed" => $result_counter["guaranteed"],
                    "num_of_pulls" => $result_pull["num_of_pulls"],
                    "unit_name" => $result_pull["unit_name"],
                    "from_banner" => $result_pull["from_banner"]
                ]);
            } else {
                echo json_encode([
                    "progress" => $result_counter["progress"],
                    "guaranteed" => $result_counter["guaranteed"],
                    "num_of_pulls" => null,
                    "unit_name" => null,
                    "from_banner" => null
                ]);
            }

        } else {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Database operation failed: " . $e->getMessage()]);
            die();
        }
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database operation failed: " . $e->getMessage()]);
        die();
    }


} else {
    error_log("Login: not a post method");
    die();
}
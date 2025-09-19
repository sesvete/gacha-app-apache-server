<?php

function get_personal_history_for_stats(object $pdo, int $uid, string $game, string $banner){
    $query = "SELECT unit_name, num_of_pulls, from_banner, date FROM pull
                WHERE(user_uid = :uid AND game = :game AND banner = :banner)
                ORDER BY date ASC, pull_id ASC;";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":uid", $uid);
    $stmt->bindParam(":game", $game);
    $stmt->bindParam(":banner", $banner);

    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function get_global_history_for_stats(object $pdo, string $game, string $banner){
    $query = "SELECT user_uid, unit_name, num_of_pulls, from_banner, date FROM pull
                WHERE(game = :game AND banner = :banner)
                ORDER BY user_uid, date ASC, pull_id ASC;";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":game", $game);
    $stmt->bindParam(":banner", $banner);

    $stmt->execute();

    // PDO::FETCH_GROUP - first column used as key - in our case group by user_uid
    $result = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
    return $result;
}

function get_num_of_pulls_values(array $result){
    $num_of_pulls_list = [];
    foreach($result as $pull){
        if (isset($pull["num_of_pulls"])) {
        $num_of_pulls_list[] = $pull["num_of_pulls"];
        }
        
    }
    return $num_of_pulls_list;
}

function get_from_banner_values(array $result){
    $from_banner_list = [];
    foreach($result as $pull){
        if (isset($pull["from_banner"])) {
        $from_banner_list[] = $pull["from_banner"];
        }
    }
    return $from_banner_list;
}

function determine_fifty_fifty_outcomes(array $from_banner_list){
    $fifty_fifty_outcomes = [];
    $lost5050 = false;
    foreach($from_banner_list as $pull) {
        if ($pull == 1){
            if(!$lost5050){
                $fifty_fifty_outcomes[] = true;
            }
            $lost5050 = false;
        } else {
            $fifty_fifty_outcomes[] = false;
            $lost5050 = true;
        }
    }
    return $fifty_fifty_outcomes;
}
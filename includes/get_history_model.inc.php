<?php

function get_personal_history(object $pdo, int $uid, string $game, string $banner){
    $query = "SELECT unit_name, num_of_pulls, from_banner, date FROM pull
                WHERE(user_uid = :uid AND game = :game AND banner = :banner)
                ORDER BY date DESC, pull_id DESC;";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":uid", $uid);
    $stmt->bindParam(":game", $game);
    $stmt->bindParam(":banner", $banner);

    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}
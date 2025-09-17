<?php

function get_counter_data_form_db(object $pdo, int $uid, string $game, string $banner){
    $query = "SELECT progress, guaranteed FROM counter 
                WHERE (user_uid = :uid AND game = :game AND banner = :banner);";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":uid", $uid);
    $stmt->bindParam(":game", $game);
    $stmt->bindParam(":banner", $banner);

    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}
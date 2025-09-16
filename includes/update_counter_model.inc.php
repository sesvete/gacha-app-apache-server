<?php

declare(strict_types=1);

function update_counter(object $pdo, int $uid, string $game, string $banner, int $progress, int $guaranteed){
    $query = "UPDATE counter 
                SET progress = :progress, guaranteed = :guaranteed 
                WHERE (user_uid = :uid AND game = :game AND banner = :banner);";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":uid", $uid);
    $stmt->bindParam(":game", $game);
    $stmt->bindParam(":banner", $banner);
    $stmt->bindParam(":progress", $progress);
    $stmt->bindParam(":guaranteed", $guaranteed);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}
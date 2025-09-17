<?php

declare(strict_types = 1);

function insert_pull(object $pdo, int $uid, string $game, string $banner, string $unit_name, int $num_of_pulls, int $from_banner, string $date){

    $query = "INSERT INTO pull (user_uid, game, banner, unit_name, num_of_pulls, from_banner, date)
                 VALUES(:uid, :game, :banner, :unit_name, :num_of_pulls, :from_banner, :date);";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":uid", $uid);
    $stmt->bindParam(":game", $game);
    $stmt->bindParam(":banner", $banner);
    $stmt->bindParam(":unit_name", $unit_name);
    $stmt->bindParam(":num_of_pulls", $num_of_pulls);
    $stmt->bindParam(":from_banner", $from_banner);
    $stmt->bindParam(":date", $date);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;

}

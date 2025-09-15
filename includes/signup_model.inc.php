<?php

declare(strict_types = 1);

function get_username(object $pdo, string $username)
{
    $query = "SELECT username FROM user WHERE username = :username;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":username", $username);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}


function set_user(object $pdo, string $username, string $password)
{
    $query = "INSERT INTO user (username, password) VALUES(:username, :password);";
    $stmt = $pdo->prepare($query);

    //hashing
    $options = [
        'cost' => 12
    ];
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT, $options);

    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":password", $hashedPassword);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}

function create_user_counter(object $pdo, int $uid){
    $combinations = [
        ['game' => 'genshin_impact', 'banner' => 'chronicled_wish'],
        ['game' => 'genshin_impact', 'banner' => 'limited'],
        ['game' => 'genshin_impact', 'banner' => 'standard'],
        ['game' => 'genshin_impact', 'banner' => 'weapon'],
        ['game' => 'honkai_star_rail', 'banner' => 'collaboration_character'],
        ['game' => 'honkai_star_rail', 'banner' => 'collaboration_light_cone'],
        ['game' => 'honkai_star_rail', 'banner' => 'light_cone'],
        ['game' => 'honkai_star_rail', 'banner' => 'limited'],
        ['game' => 'honkai_star_rail', 'banner' => 'standard'],
        ['game' => 'zenless_zone_zero', 'banner' => 'bangboo'],
        ['game' => 'zenless_zone_zero', 'banner' => 'limited'],
        ['game' => 'zenless_zone_zero', 'banner' => 'standard'],
        ['game' => 'zenless_zone_zero', 'banner' => 'w_engine'],
        ['game' => 'tribe_nine', 'banner' => 'limited'],
        ['game' => 'tribe_nine', 'banner' => 'standard'],
        ['game' => 'tribe_nine', 'banner' => 'tension_card']
    ];

    $query = "INSERT INTO counter (user_uid, game, banner) VALUES (:uid, :game, :banner);";
    $stmt = $pdo->prepare($query);

    // Loop through the combinations and execute the prepared statement
    foreach ($combinations as $combo) {
        $stmt->bindParam(":uid", $uid);
        $stmt->bindParam(":game", $combo['game']);
        $stmt->bindParam(":banner", $combo['banner']);
        $stmt->execute();
    }
}
<?php

function deleteOldGames(){
    global $db;
    $stmt = $db->prepare(query: "DELETE FROM `games` WHERE last_used < NOW() - INTERVAL 1 HOUR");
    if($stmt === false){
        return false;
    }
    $stmt->execute();
    return true;
}
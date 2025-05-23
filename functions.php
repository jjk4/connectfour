<?php

function deleteOldGames(){
    global $db;
    $stmt = $db->prepare(query: "DELETE FROM `games` WHERE created_at < NOW() - INTERVAL 1 HOUR");
    if($stmt === false){
        return false;
    }
    $stmt->execute();
    return true;
}
<?php

require_once('config.php');

$db = new mysqli(hostname: $db_host, username: $db_user, password: $db_pass, database: $db_name, port: $db_port);

if ($db->connect_error) {
    echo json_encode(["error" => "DB Connection failed"]);
    die();
}

// if(!isset($_GET["action"])){
//     echo json_encode(["error" => "No action specified"]);
//     die();
// }
$action = $_GET["action"];
$action = "createGame"; // For testing purposes
switch($action){
    case "createGame":
        $game = new Game();
        $result = $game->saveNew();
        if($result === false){
            echo json_encode(["error" => "Failed to create game"]);
            die();
        } else {
            echo json_encode(["gameid" => $result]);
        }
        break;

}
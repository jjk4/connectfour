<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once('config.php');
require_once('Game.php');
$db = new mysqli(hostname: $db_host, username: $db_user, password: $db_pass, database: $db_name, port: $db_port);

if ($db->connect_error) {
    echo json_encode(["error" => "DB Connection failed"]);
    die();
}

if(!isset($_GET["action"])){
    echo json_encode(["error" => "No action specified"]);
    die();
}
$action = $_GET["action"];

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
    case "play":
        $gameid = intval($_POST["gameid"]);
        $game = new Game();
        if($game->importFromId($gameid) === false){
            echo json_encode(["error" => "Failed to load game"]);
            die();
        } else {
            echo $game->toJson();
        }


}
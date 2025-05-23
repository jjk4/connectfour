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

    case "getGame":
        $gameid = intval($_POST["gameid"]);
        $game = new Game();
        if($game->importFromId($gameid) === false){
            echo json_encode(["error" => "Failed to load game"]);
            die();
        }
        echo $game->toJson();
        break;

    case "play":
        $gameid = intval($_POST["gameid"]);
        $game = new Game();
        if($game->importFromId($gameid) === false){
            echo json_encode(["error" => "Failed to load game"]);
            die();
        }
        $player = intval($_POST["player"]);
        if($player != 1 && $player != 2){
            echo json_encode(["error" => "Invalid player"]);
            die();
        }
        if($game->getNextPlayer() != $player){
            echo json_encode(["error" => "Not your turn"]);
            die();
        }
        $column = intval($_POST["column"]);
        if($column < 0 || $column > 6){
            echo json_encode(["error" => "Invalid column"]);
            die();
        }


        echo $game->toJson();
        break;
        


}
<?php

    class Game{
        private $gamestate;
        private $gameid;

        public function __construct($gamestate=null){
            if(isset($gamestate)){
                $this->gamestate = $gamestate;
            } else {
                $this->gamestate = [
                    [0, 0, 0, 0, 0, 0, 0],
                    [0, 0, 0, 0, 0, 0, 0],
                    [0, 0, 0, 0, 0, 0, 0],
                    [0, 0, 0, 0, 0, 0, 0],
                    [0, 0, 0, 0, 0, 0, 0],
                    [0, 0, 0, 0, 0, 0, 0]

                ];
            }
        }
        public function getGameState(): array{
            return $this->gamestate;
        }
        public function toJson(): bool|string{
            return json_encode($this->gamestate);
        }
        public function importFromId($id): bool{
            global $db;
            $stmt = $db->prepare(query: "SELECT game_data FROM `games` WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($game_data);
            if ($stmt->fetch()) {
                $this->gamestate = json_decode($game_data, associative: true);
                $this->gameid = $id;
                return true;
            } else {
                return false;
            }
        }

        private function createUniqueID(): bool|int{
            global $db;
            $overflow_counter = 0;
            $this->gameid = rand(min: 100000, max: 999999);
            while($overflow_counter < 100){
                $db->query("SELECT id FROM `games` WHERE id = $this->gameid");
                if($db->affected_rows == 0){
                    break;
                } else {
                    $this->gameid = rand(min: 100000, max: 999999);
                    $overflow_counter++;
                }
            }
            if($overflow_counter == 100){
                return false;
            } else {
                return $this->gameid;
            }
        }
        public function saveNew(){
            global $db;
            if(!isset($this->gameid)){ // Neues Spiel erstellen
                $this->gameid = $this->createUniqueID();                
                $stmt = $db->prepare(query: "INSERT INTO `games` (`id`, `last_used`, `game_data`) VALUES (?, current_timestamp(), ?)");

                if($stmt === false){
                    return false;
                }
                $json = $this->toJson();
                if($json === false){
                    return false;
                }
                $stmt->bind_param("is", $this->gameid, $json);
                if($stmt->execute() === false){
                    return false;
                }
                return $this->gameid;;
            }

        }
        public function getNextPlayer(){
            $player1 = 0;
            $player2 = 0;
            foreach($this->gamestate as $row){
                foreach($row as $cell){
                    if($cell == 1){
                        $player1++;
                    } elseif($cell == 2){
                        $player2++;
                    }
                }
            }
            if($player1 > $player2){
                return 2;
            } else {
                return 1;
            }
        }
        public function play($column, $player){
            if($this->gamestate[0][$column] != 0){
                return false;
            }
            for($i = 5; $i >= 0; $i--){
                if($this->gamestate[$i][$column] == 0){
                    $this->gamestate[$i][$column] = $player;
                    break;
                }
            }
            return true;
        }

    }
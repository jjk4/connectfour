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
        private function toJson(): bool|string{
            return json_encode($this->gamestate);
        }
        private function createUniqueID(): bool|int{
            global $db;
            $overflow_counter = 0;
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

    }
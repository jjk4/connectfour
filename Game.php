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
        public function saveNew(){
            global $db;
            if(!isset($this->gameid)){ // Neues Spiel erstellen
                $this->gameid = rand(min: 100000, max: 999999);
                $stmt = $db->prepare(query: "INSERT INTO `games` (`id`, `last_used`, `game_data`) VALUES ('?', current_timestamp(), '?')");
                if($stmt === false){
                    return false;
                }
                $json = $this->toJson();
                if($json === false){
                    return false;
                }
                var_dump($json);
                var_dump($this->gameid);
                $stmt->bind_param("is", $this->gameid, $json);
                if($stmt->execute() === false){
                    return false;
                }
                return $this->gameid;;
            }

        }
    }
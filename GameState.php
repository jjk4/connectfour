<?php

    class GameState{
        private $gamestate;

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
    }
<?php

class Game
{
    private $gamestate;
    private $gameid;

    public function __construct($gamestate = null)
    {
        if (isset($gamestate)) {
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
    public function getGameState(): array
    {
        return $this->gamestate;
    }
    public function toJson(): bool|string
    {
        return json_encode($this->gamestate);
    }
    public function importFromId($id): bool
    {
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

    private function createUniqueID(): bool|int
    {
        global $db;
        $overflow_counter = 0;
        $this->gameid = rand(min: 100000, max: 999999);
        while ($overflow_counter < 100) {
            $db->query("SELECT id FROM `games` WHERE id = $this->gameid");
            if ($db->affected_rows == 0) {
                break;
            } else {
                $this->gameid = rand(min: 100000, max: 999999);
                $overflow_counter++;
            }
        }
        if ($overflow_counter == 100) {
            return false;
        } else {
            return $this->gameid;
        }
    }
    public function saveNew()
    {
        global $db;
        if (!isset($this->gameid)) { // Neues Spiel erstellen
            $this->gameid = $this->createUniqueID();
            $stmt = $db->prepare(query: "INSERT INTO `games` (`id`, `last_used`, `game_data`) VALUES (?, current_timestamp(), ?)");

            if ($stmt === false) {
                return false;
            }
            $json = $this->toJson();
            if ($json === false) {
                return false;
            }
            $stmt->bind_param("is", $this->gameid, $json);
            if ($stmt->execute() === false) {
                return false;
            }
            return $this->gameid;;
        }
    }
    public function save()
    {
        global $db;
        if (!isset($this->gameid)) {
            return false;
        }
        $stmt = $db->prepare(query: "UPDATE `games` SET `last_used` = current_timestamp(), `game_data` = ? WHERE id = ?");
        if ($stmt === false) {
            return false;
        }
        $json = $this->toJson();
        if ($json === false) {
            return false;
        }
        $stmt->bind_param("si", $json, $this->gameid);
        if ($stmt->execute() === false) {
            return false;
        }
        return true;
    }

    public function getNextPlayer()
    {
        $player1 = 0;
        $player2 = 0;
        foreach ($this->gamestate as $row) {
            foreach ($row as $cell) {
                if ($cell == 1) {
                    $player1++;
                } elseif ($cell == 2) {
                    $player2++;
                }
            }
        }
        if ($player1 > $player2) {
            return 2;
        } else {
            return 1;
        }
    }
    public function play($column, $player)
    {
        if ($this->gamestate[0][$column] != 0) {
            return false;
        }
        for ($i = 5; $i >= 0; $i--) {
            if ($this->gamestate[$i][$column] == 0) {
                $this->gamestate[$i][$column] = $player;
                break;
            }
        }
        return true;
    }
    public function checkWinner()
    {
        // Horizontal
        foreach ($this->gamestate as $row) {
            foreach ($row as $cell) {
                $count = 0;
                if ($cell == 1) {
                    $count++;
                    if ($count == 4) {
                        return 1;
                    }
                } else {
                    $count = 0;
                }
            }
        }
        foreach ($this->gamestate as $row) {
            foreach ($row as $cell) {
                $count = 0;
                if ($cell == 2) {
                    $count++;
                    if ($count == 4) {
                        return 2;
                    }
                } else {
                    $count = 0;
                }
            }
        }
        // Vertikal
        for ($i = 0; $i <= 6; $i++) {
            $count = 0;
            for ($j = 0; $j <= 5; $j++) {
                if ($this->gamestate[$j][$i] == 1) {
                    $count++;
                    if ($count == 4) {
                        return 1;
                    }
                } else {
                    $count = 0;
                }
            }
        }
        for ($i = 0; $i <= 6; $i++) {
            $count = 0;
            for ($j = 0; $j <= 5; $j++) {
                if ($this->gamestate[$j][$i] == 2) {
                    $count++;
                    if ($count == 4) {
                        return 2;
                    }
                } else {
                    $count = 0;
                }
            }
        }
        // Diagonal
        for ($i = 0; $i <= 2; $i++) {
            for ($j = 0; $j <= 6; $j++) {
                if ($this->gamestate[$i][$j] != 0) {
                    $count = 0;
                    for ($k = 0; $k <= 3; $k++) {
                        if ($i + $k < 6 && $j + $k < 7) {
                            if ($this->gamestate[$i + $k][$j + $k] == $this->gamestate[$i][$j]) {
                                $count++;
                                if ($count == 4) {
                                    return $this->gamestate[$i][$j];
                                }
                            } else {
                                $count = 0;
                            }
                        }
                    }
                    $count = 0;
                    for ($k = 0; $k <= 3; $k++) {
                        if ($i + $k < 6 && $j - $k >= 0) {
                            if ($this->gamestate[$i + $k][$j - $k] == $this->gamestate[$i][$j]) {
                                $count++;
                                if ($count == 4) {
                                    return $this->gamestate[$i][$j];
                                }
                            } else {
                                $count = 0;
                            }
                        }
                    }
                }
            }
            return 0;
        }
    }
}

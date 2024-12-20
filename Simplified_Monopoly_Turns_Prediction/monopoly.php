<?php
class MonopolyGame {
    private array $boardPositions = [];
    private array $players = [];
    private array $diceRolls = [];
    private array $playerStates = [];
    private int $currentRollIndex = 0;

    private const  BOARD_SIZE = 40;
    private const  JAIL_POSITION = 10;
    private const  GO_TO_JAIL_POSITION = 30;

    /**
     * @param $input
     */
    public function __construct($input) {
        $this->initializeGame($input);
    }

    /**
     * @param $input
     * @return void
     */
    private function initializeGame($input): void
    {
        $nbrPlayers = (int)trim(fgets($input));

        for ($i = 0; $i < $nbrPlayers; $i++) {
            list($name, $position) = explode(" ", trim(fgets($input)));
            $this->players[] = [
                'name' => $name,
                'position' => (int)$position
            ];
            $this->playerStates[$name] = [
                'position' => (int)$position,
                'inJail' => false,
                'jailRolls' => 0
            ];
        }

        $nbrDiceRolls = (int)trim(fgets($input));
        for ($i = 0; $i < $nbrDiceRolls; $i++) {
            $this->diceRolls[] = array_map('intval', explode(" ", trim(fgets($input))));
        }

        for ($i = 0; $i < self::BOARD_SIZE; $i++) {
            $this->boardPositions[] = trim(fgets($input));
        }
    }

    /**
     * @return void
     */
    public function simulate(): void
    {
        while ($this->currentRollIndex < count($this->diceRolls)) {
            foreach ($this->players as $player) {
                $this->processTurn($player);
                if ($this->currentRollIndex >= count($this->diceRolls)) {
                    break;
                }
            }
        }

        $this->outputResults();
    }

    /**
     * @param $player
     * @return void
     */
    private function processTurn($player): void
    {
        $name = $player['name'];
        $state = &$this->playerStates[$name];

        if ($state['inJail']) {
            $this->handleJailTurn($state);
            return;
        }

        $doubleCount = 0;
        while ($this->currentRollIndex < count($this->diceRolls)) {
            $dice = $this->diceRolls[$this->currentRollIndex++];
            $move = array_sum($dice);

            if ($dice[0] === $dice[1]) {
                $doubleCount++;
                if ($doubleCount === 3) {
                    $this->sendToJail($state);
                    break;
                }
            } else {
                $doubleCount = 0;
            }

            $state['position'] = ($state['position'] + $move) % self::BOARD_SIZE;

            if ($state['position'] === self::GO_TO_JAIL_POSITION) {
                $this->sendToJail($state);
                break;
            }

            if ($dice[0] !== $dice[1]) {
                break;
            }
        }
    }

    /**
     * @param $state
     * @return void
     */
    private function handleJailTurn(&$state): void
    {
        $dice = $this->diceRolls[$this->currentRollIndex++];

        if ($dice[0] === $dice[1]) {
            $state['inJail'] = false;
            $state['jailRolls'] = 0;
            $state['position'] = ($state['position'] + array_sum($dice)) % self::BOARD_SIZE;
        } else {
            $state['jailRolls']++;
            if ($state['jailRolls'] >= 3) {
                $state['inJail'] = false;
                $state['jailRolls'] = 0;
                $state['position'] = ($state['position'] + array_sum($dice)) % self::BOARD_SIZE;
            }
        }
    }

    /**
     * @param $state
     * @return void
     */
    private function sendToJail(&$state): void
    {
        $state['position'] = self::JAIL_POSITION;
        $state['inJail'] = true;
        $state['jailRolls'] = 0;
    }

    /**
     * @return void
     */
    private function outputResults(): void
    {
        $output = [];
        foreach ($this->players as $player) {
            $name = $player['name'];
            $output[] = $name . " " . $this->playerStates[$name]['position'];
        }
        echo implode("\n", $output);
    }
}

//$game = new MonopolyGame(fopen("php://stdin", "r"));
//$game->simulate();

//Code pour tester l'exercice sans Code in game
$input = fopen("php://memory", "r+");
fwrite($input, "2\nHorse 0\nTopHat 0\n6\n1 6\n2 1\n6 4\n2 1\n4 5\n6 5\nGo\nMediterranean Avenue\nCommunity Chest\nBaltic Avenue\nIncome Tax\nReading Railroad\nOriental Avenue\nChance\nVermont Avenue\nConnecticut Avenue\nVisit Only / In Jail\nSt. Charles Place\nElectric Company\nStates Avenue\nVirginia Avenue\nPennsylvania Railroad\nSt. James Place\nCommunity Chest\nTennessee Avenue\nNew York Avenue\nFree Parking\nKentucky Avenue\nChance\nIndiana Avenue\nIllinois Avenue\nB. & O. Railroad\nAtlantic Avenue\nVentnor Avenue\nWater Works\nMarvin Gardens\nGo To Jail\nPacific Avenue\nNorth Carolina Avenue\nCommunity Chest\nPennsylvania Avenue\nShort Line\nChance\nPark Place\nLuxury Tax\nBoardwalk\n");
rewind($input);
$game = new MonopolyGame($input);
$game->simulate();
fclose($input);

?>
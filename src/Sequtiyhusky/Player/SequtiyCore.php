<?php

/**
 *   _____           _          ____                       __  __            _              
 *  |  ___|_ _ _ __ (_) __ _   / ___|___  _ __ _ __ __ _  |  \/  | __ _ _ __| | ___ _   _   
 *  | |_ / _` | '_ \| |/ _` | | |   / _ \| '__| '__/ _` | | |\/| |/ _` | '__| |/ _ \ | | |  
 *  |  _| (_| | | | | | (_| | | |__| (_) | |  | | | (_| | | |  | | (_| | |  | |  __/ |_| |_ 
 *  |_|  \__,_|_| |_|_|\__,_|  \____\___/|_|  |_|  \__,_| |_|  |_|\__,_|_|  |_|\___|\__, (_)
 *                                                                                  |___/   
 *
 * @author Sequtiyhusky
 * @link https://github.com/RizkiTri
 */

namespace Sequtiyhusky\Fania\Player;

use Sequtiyhusky\Fania\Helpers\GameRepo;

class SequtiyCore
{
    private $health;

    private $level;

    private $xp;

    private $money;

    public $gameReporsitory;

    public function __construct()
    {
        $this->gameReporsitory = new GameRepo();
    }

    private function toArray(): array
    {
        return [];
    }

    public function addMoney() {}

    public function deductMoney() {}

    public function getHealth()
    {
        return $this->health;
    }

    public function getLand() {}
}

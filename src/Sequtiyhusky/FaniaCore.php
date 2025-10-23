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

namespace Sequtiyhusky\Fania;

use Sequtiyhusky\Fania\Helpers\ChatManager;
use Sequtiyhusky\Fania\Helpers\DataManager;

use Sequtiyhusky\Fania\Utils\RegisterCommand;
use Sequtiyhusky\Fania\Utils\ConsoleFormat as CF;

class FaniaCore
{

    /** @var string Nama dari project dan assisten */
    public const NAME = "FANIA CORRA MARLEY";

    public const PREFIX = CF::BOLD . CF::YELLOW . "[" . CF::CYAN . "FANIA" . CF::YELLOW . "]" . CF::RESET;

    /**
     * construct function for FaniaCore
     * 
     */
    public function __construct() {
        $this->getDataManager("Config")->overrideConfig();
    }

    /**
     * Sebuah functionn yang memanggil home view
     * 
     * @return void `
     */
    private function loadDefault()
    {
        $this->clearConsole();
        echo CF::CYAN . CF::BOLD_WHITE . "  _____           _          ____                       __  __            _              
 |  ___|_ _ _ __ (_) __ _   / ___|___  _ __ _ __ __ _  |  \/  | __ _ _ __| | ___ _   _   
 | |_ / _` | '_ \| |/ _` | | |   / _ \| '__| '__/ _` | | |\/| |/ _` | '__| |/ _ \ | | |  
 |  _| (_| | | | | | (_| | | |__| (_) | |  | | | (_| | | |  | | (_| | |  | |  __/ |_| |_ 
 |_|  \__,_|_| |_|_|\__,_|  \____\___/|_|  |_|  \__,_| |_|  |_|\__,_|_|  |_|\___|\__, (_)
                                                                                 |___/   " . CF::RESET . PHP_EOL;
        echo CF::CYAN .          "------------------------------------------------------------------------------------------" . PHP_EOL;
        echo CF::LIGHT_MAGENTA . "Fania sedang dalam tahap pengembangan...." . PHP_EOL;
        echo CF::CYAN .          "------------------------------------------------------------------------------------------" . PHP_EOL;
        echo CF::YELLOW .        "[DEBUG] ";
        echo CF::RESET;
    }

    /**
     * Sebuah function yang berfungsi untuk menghapus seluruh tampilan yang ada di console  
     * 
     * @return void
     */
    private function clearConsole(): void
    {
        echo chr(27) . chr(91) . 'H' . chr(27) . chr(91) . 'J';
    }

    public function getDataManager(string $data): DataManager
    {
        return (new DataManager($data));
    }

    public function settingLoader(): void
    {
        if (!$this->getDataManager("Config")->get("cache.loadedBefore")) {
            $this->getDataManager("Config")->set("cache.loadedBefore", true);
            $this->loadDefault();
            (new ChatManager());
            (new RegisterCommand());
        }
        (new ChatManager());
    }
}

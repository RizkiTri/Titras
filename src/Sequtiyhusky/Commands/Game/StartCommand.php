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

namespace Sequtiyhusky\Fania\Commands\Game;

use Sequtiyhusky\Fania\Interfaces\ICommand;

use Sequtiyhusky\Fania\Helpers\ChatManager;
use Sequtiyhusky\Fania\Helpers\GameRepo;

use Sequtiyhusky\Fania\Utils\TableFormat;
use Sequtiyhusky\Fania\Utils\ConsoleFormat;

/**
 * Class Start
 *
 * 
 *
 * Category: game
 * Usage: game:start
 * Name: start
 * Description: to start the game
 */
class StartCommand implements ICommand
{
    private $dialogEngine;

    /**
     * CODE LIST 
     * 
     * 4 => Profile decide
     * 3 => Make profile name
     * 2 => generate profile
     * 5 => Choose profile
     * 6 => choose id
     * 7 => actual game
     */

    private function loadData(ChatManager $chat)
    {
        /**
         * search the existing data
         * choose new profilw or existing profile
         * if load, then enter the game 
         * if no then prologue
         */
        $repo = new GameRepo();
        $profile = $repo->getAllProfile();

        if ($chat->getDataManager()->get("cache.session.number") === 6) {
            $idProfile = (int) $chat->message;
            if ($idProfile < 1 or $idProfile > count($profile)) {
                $chat->reply("ID Profile tidak ditemukan");
                $chat->reply("Masukkan id profile: ", newLine: false);
                return;
            }

            $data = json_decode(file_get_contents($profile[$idProfile - 1]), \true);
            $chat->reply("Selamat datang kembali " . $data["name"] . ", semoga perjalanan mu menyenangkan", typing: true, newLine: true, debug: true);
            $chat->getDataManager()->set("isGame", true);
            $chat->getDataManager()->set("gameData", $data);
            $chat->getDataManager()->set("cache.session.number", 7);

            // Initialize dialog game
            $this->dialogEngine = new \Sequtiyhusky\Fania\Helpers\DialogEngine($chat);
            if (isset($data['gameState'])) {
                $this->dialogEngine->setPlayerState($data['gameState']);
            }
            $this->dialogEngine->displayCurrentNode();
            return;
        }

        if ($chat->getDataManager()->get("cache.session.number") === 2) {
            $repo->createProfile($chat->message);
        } else {
            if (empty($profile)) {
                $chat->reply("Profile tidak ditemukan, maukah anda membuat profile baru [Y/n](default yes): ", false);
                $chat->getDataManager()->set("cache.session.number", 4);
            } else {
                $chat->reply("Kami menemukan beberapa profile yang ada di directory ini, berikut directory tersebut", typing: true);
                $data = [["ID", "Profile Name", "Created At", "Progress"]];
                $table = new TableFormat(data: $data, usingHeader: true, align: 'left');

                for ($i = 1; $i <= count($profile); $i++) {
                    $data = json_decode(file_get_contents($profile[$i - 1]), \true);
                    $table->addField([$i, $data["name"], $data["created_at"], $data["id_progress"]]);
                }

                $table->printTable();
                $chat->getDataManager()->set("cache.session.number", 5);
                $chat->reply("Apakah anda mau untuk membuat profile baru (1) atau", typing: true);
                $chat->reply("Memilih profile yang sudah ada untuk bermain (2)", typing: true, debug: true);
            }
        }
        // $chat->reply("Reply successfully");
    }


    private function homeScreem(int $sesi)
    {
        if ($sesi === 0) {
            echo ConsoleFormat::BOLD;
            echo ConsoleFormat::getInstance()->gradientText(" 
/$$$$$$$$ /$$   /$$                                 
|__  $\$__/|__/  | $$                                 
   | $$    /$$ /$$$$$$    /$$$$$$  /$$$$$$   /$$$$$$$
   | $$   | $$|_  $\$_/   /$\$__  $$|____  $$ /$\$_____/
   | $$   | $$  | $$    | $$  \__/ /$$$$$$$|  $$$$$$ 
   | $$   | $$  | $$ /$$| $$      /$\$__  $$ \____  $$
   | $$   | $$  |  $$$$/| $$     |  $$$$$$$ /$$$$$$$/
   |__/   |__/   \___/  |__/      \_______/|_______/ ", "#3C467B", "#ED3F27");
            echo ConsoleFormat::RED . "\n--------------------------------------------------------------------------\n";
            echo ConsoleFormat::WHITE . "Sebuah game yang bertemakan sejarah, dimana kita bisa belajar sambil masuk \n";
            echo ConsoleFormat::WHITE . "Ke dalam cerita tersebut, diharapkan siswa dapat menjadi lebih senang untuk \n";
            echo ConsoleFormat::WHITE . "belajar sejarah";
            echo ConsoleFormat::RED . "\n--------------------------------------------------------------------------\n";
            echo ConsoleFormat::YELLOW .        "[DEBUG] " . ConsoleFormat::RESET;
        }
    }

    /**
     * Jalankan perintah untuk mendapatkan informasi tentang Fania.
     *
     * @param ChatManager $chat  Instance ChatManager untuk mengirim/menerima pesan.
     * @return void
     */
    public function execute(ChatManager $chat): void
    {
        $sesi = $chat->getDataManager()->get("cache.session.number");
        switch ($sesi) {
            case 0:
                $chat->clearConsole();
                $this->homeScreem($sesi);
                $chat->getDataManager()->set("cache.session.number", 1);
                break;
            case 1:
                if ($chat->message === "loaddata") {
                    $this->loadData($chat);
                }
                break;
            case 4:
                switch (\strtolower($chat->message)) {
                    case "yes":
                    case "y":
                    case "":
                        $chat->reply("Pilihan yang bagus, selamat datang di" . ConsoleFormat::getInstance()->gradientText("TITRAS", "#3C467B", "#ED3F27") . "." . " Nikmati perjalanan mu", true);
                        // $chat->getDataManager()->set("cache.session.number", 3);
                        $chat->reply("Sebelum memulai perjalanan, bolehkah kami tahu siapa nama mu ?", typing: true, newLine: true, debug: true);
                        $chat->getDataManager()->set("cache.session.number", 2);
                        break;
                    case "no":
                    case "n":
                        $chat->reply("yahhh, jumpa lagi lain waktu...", typing: true);
                        $chat->getDataManager()->set("cache.loadedBefore", false);
                        $chat->getDataManager()->set("cache.session.name", "none");
                        $chat->getDataManager()->set("cache.session.number", 0);
                        die();
                        break;
                    default:
                        $chat->reply("Perintah tidak diketahui!");
                        break;
                }
                break;

            case 2:
                $this->loadData($chat);
                $chat->reply("Wah, {$chat->message}. Sungguh nama yang indah, mari kita sama sama memulai perjalanan ke dalam dunia kepahlawanan ini", typing: true, newLine: true, debug: true);
                $chat->getDataManager()->set("cache.session.number", 7);
                // load the first dialog only
                $this->dialogEngine = new \Sequtiyhusky\Fania\Helpers\DialogEngine($chat);
                $this->dialogEngine->start();
                return;

                break;

            case 5:
                \var_dump($chat->message);
                if ($chat->message === "1") {
                    $chat->reply("Masukkan id profile: ", newLine: false);
                    $chat->getDataManager()->set("cache.session.number", 6);
                }

                if ($chat->message === "2") {
                    $chat->getDataManager()->set("cache.session.number", 2);
                }

                if ($chat->message !== "1" and $chat->message !== "2") {
                    $chat->reply("Opsi tidak tersedia");
                }

                break;

            case 6:
                $this->loadData($chat);
                break;

            case 7:
                // safe stateless handler: reconstruct per-message but restore state properly
                $gameData = $chat->getDataManager()->get("gameData");
                $savedState = !empty($gameData['gameState']) && is_array($gameData['gameState']) ? $gameData['gameState'] : null;

                // create fresh engine for this message
                $dialogEngine = new \Sequtiyhusky\Fania\Helpers\DialogEngine($chat);
                // enable debug during testing; set to false in production
                // $dialogEngine->setDebug(true);

                if ($savedState) {
                    // force-apply saved state because we are restoring on a new instance
                    $dialogEngine->setPlayerState($savedState, true);
                    $dialogEngine->resume();
                } else {
                    $dialogEngine->start();
                }

                // process the incoming message (trim it)
                $continue = $dialogEngine->processInput(trim((string)($chat->message ?? '')));

                // after processing, persist the latest playerState to gameData
                $gameData['gameState'] = $dialogEngine->getPlayerState();
                $chat->getDataManager()->set("gameData", $gameData);

                // handle end-of-game
                if (!$continue) {
                    $chat->getDataManager()->set("cache.session.number", 1);
                    $chat->reply("Permainan berakhir. Ketik 'loaddata' untuk memuat kembali.", newLine: true, debug: true);
                    return;
                }

                break;
        }
    }

    /**
     * Contoh penggunaan perintah.
     *
     * @return string
     */
    public function getUsage(): string
    {
        return "game:start";
    }

    /**
     * Kategori perintah.
     *
     * @return string
     */
    public function getCategory(): string
    {
        return "game";
    }

    /**
     * Nama perintah.
     *
     * @return string
     */
    public function getName(): string
    {
        return "start";
    }

    /**
     * Deskripsi perintah.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "";
    }
}

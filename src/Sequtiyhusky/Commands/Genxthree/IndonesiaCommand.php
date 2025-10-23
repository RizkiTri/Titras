<?php

namespace Sequtiyhusky\Fania\Commands\Genxthree;

use Sequtiyhusky\Fania\Helpers\ChatManager;
use Sequtiyhusky\Fania\Utils\ConsoleFormat;
use Sequtiyhusky\Fania\Interfaces\ICommand;

class IndonesiaCommand implements ICommand
{
    /**
     * Config Variable
     *
     * @var DataManager\Config
     */
    private $config;

    private $kelompok = [
        [ // Kelompok 1
            "Dhea Novita Wijayanti",
            "Atsila Alma Zulyo Bona",
            "Noventya Ulul Azmi",
            "Reza Yustio Putro",
            "Putra Akmal Kahita",
            "Machamad Kevin Aprilliyanto"
        ],
        [ // Kelompok 2
            "Irma Dyah Ayu Sapitri",
            "Arkalitha Nielza",
            "Countessa Christina De Ilma Alim",
            "Luqman Abdul Malik Assifa",
            "Akhdan Maulana Azkareno",
            "Muhammad Reyhan Andraan H."
        ],
        [ // Kelompok 3
            "Gracia Irine Atalau Lanma",
            "Amelia Putri",
            "Aisyah Kusuma Fitriani",
            "Cintya Canta Ratmiko",
            "Mohamad Aziz Tri Ananto",
            "Rofiqon sena kanzul fikri"
        ],
        [ // Kelompok 4
            "Eiffeleen Princess Edogawa Firsya",
            "Adhimutra Zaqirah Prasetya",
            "Stella Serma Wahyudiansyah",
            "Ani chabibah",
            "Alpandry Radithya Irwan",
            "Cakra Reno Bernadit Widodo"
        ],
        [ // Kelompok 6
            "Natasya Eka Nazwa",
            "Keisha Nurazizah Riyadi",
            "Kayla Areta Ormando",
            "Yesaya Yoga Radithya Rineksa",
            "Achmad David Nugroho"
        ]
    ];

    public function execute(ChatManager $chat): void
    {
        $this->config = $chat->getDataManager();
        $count = $this->config->get("cache.session.number");

        if ($chat->getDataManager()->get("cache.session.name") === $this->getUsage() && $chat->getDataManager()->get("cache.session.number") === 0) {
            $chat->clearConsole();
            $this->printBanner();

            $this->config->set("cache.session.number", $count + 1);
        }

        if ($chat->message === "acak") {
            echo self::PREFIX . " Memulai program rolling...\n";
            usleep(2000000);
            echo self::PREFIX . " Mencatat semua anggota kelas X-3 melalui database\n";
            usleep(2000000);
            echo self::PREFIX . " Data di dapatkan....\n";
            usleep(1000000);
            echo self::PREFIX . " Mendapatkan susunan kelompok \n";
            usleep(500000);
            echo self::PREFIX . " Memulai untuk memilih pemain....\n";
            // Mengeluarkan nama dari setiap perwakilan kelompok secara acak
            $this->aturTempatDuduk("default");
        }
    }

    private function aturTempatDuduk(string $schema): void
    {
        $tempData = [];

        // Mengiterasi setiap kelompok
        foreach ($this->kelompok as $groupKey => &$group) {
            // Mengocok array kelompok beberapa kali untuk menambah kerandoman
            $this->mixAlgorithm($group);
            $this->mixAlgorithm($group);

            // Pilih kandidat secara acak dari kelompok saat ini
            $randomIndex = rand(0, count($group) - 1);
            $candidat = $group[$randomIndex];
            $tempData[] = $candidat;

            // Menghapus kandidat yang telah dipilih dari kelompok
            array_splice($group, $randomIndex, 1);
        }

        $this->printTempatDuduk($tempData, $schema);
    }

    private function mixAlgorithm(array &$array): void
    {
        shuffle($array);
    }

    // Fungsi selectRandomIndex() tidak lagi diperlukan, karena pemilihan indeks kandidat
    // dilakukan langsung berdasarkan ukuran kelompok masing-masing

    private function printTempatDuduk(array $tempData, string $schema): void
    {
        echo ConsoleFormat::BOLD . ConsoleFormat::CYAN . "Susunan perwakilan kelompok " . $schema . " : " . PHP_EOL;
        foreach ($tempData as $index => $name) {
            echo ConsoleFormat::RESET . $name . " Sebagai perwakilan dari kelompok ". ($index+1 === 5 ? 6 : ($index+1)) .PHP_EOL;
        }
    }

    /**
     * Print the banner
     *
     * @return void
     */
    private function printBanner()
    {
        echo ConsoleFormat::BOLD . ConsoleFormat::CYAN . "
   ____                    _   _                   
  / ___| ___ _ __ __  __  | |_| |__  _ __ ___  ___ 
 | |  _ / _ \ '_ \\\ \/ /  | __| '_ \| '__/ _ \/ _ \\
 | |_| |  __/ | | |>  <   | |_| | | | | |  __/  __/
  \____|\___|_| |_/_/\_\___\__|_| |_|_|  \___|\___|
                      |_____|                      
" . PHP_EOL;
        echo ConsoleFormat::CYAN . "------------------------------------------------------------------------------------------" . PHP_EOL;
        echo ConsoleFormat::LIGHT_MAGENTA . "Sebuah program dimana program ini bisa mengacak nama-nama murid X-3, kemudian menyusunnya " . PHP_EOL;
        echo ConsoleFormat::LIGHT_MAGENTA . "kembali untuk menjadi pemain dalam sebuah game yang di buat kelompok 5" . PHP_EOL;
        echo ConsoleFormat::CYAN . "------------------------------------------------------------------------------------------" . PHP_EOL;
        echo ConsoleFormat::YELLOW . "[Sequtiyhusky] ";
        echo ConsoleFormat::RESET;
    }

    public function getName(): string
    {
        return "indonesia";
    }

    public function getCategory(): string
    {
        return "genxthree";
    }

    public function getDescription(): string
    {
        return "Acak nama dalam kelompok bahasa indonesia";
    }

    public function getUsage(): string
    {
        return "genxthree:indonesia";
    }
}

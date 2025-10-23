<?php

namespace Sequtiyhusky\Fania\Helpers;

use Sequtiyhusky\Fania\Utils\ConsoleFormat;

class DataManager
{

    /** @var string Type data to acces */
    public $typeData;

    /** @var array Temporary data yang akan di simpan */
    private $data;

    /** @var string Path for all data saved */
    public $path;

    /** @var array List jenis database yang ada */
    private $avalaible = [
        "Config",
    ];

    /**
     * @param string $type
     */
    public function __construct(string $type)
    {
        if (!$this->checkAvalaibleData($type)) {
            echo ConsoleFormat::RED . "Database not avalaible... \n" . ConsoleFormat::GRAY . "Are you rooted?";
        }

        $this->typeData = $type;
        $this->path = __DIR__ . DIRECTORY_SEPARATOR . "../../../Database" . DIRECTORY_SEPARATOR . strtolower($this->typeData) . ".json";
        $this->data = json_decode(file_get_contents($this->path), true);
    }

    /**
     * Check Avalaible Data
     * 
     * sebuah fungsi untuk memeriksa data yang akan di akses 
     * 
     * @param  string $dataType 
     * @return bool 
     */
    private function checkAvalaibleData(string $dataType): bool
    {
        return in_array($dataType, $this->avalaible);
    }

    public function get(string $key): mixed
    {
        if ($this->typeData !== "Config") {
            return ConsoleFormat::RED . "Database not avalaible... \n" . ConsoleFormat::GRAY . "Are you rooted?" .  PHP_EOL . ConsoleFormat::RESET . PHP_EOL;
        }

        if (strpos($key, ".")) {
            $array = explode(".", $key);

            $dataTemp = $this->data;
            while (count($array) !== 0) {
                $dataTemp = $dataTemp[array_shift($array)];
            }

            return $dataTemp;
        }
        return $this->data[$key];
    }

    public function set(string $key, mixed $value): void
    {
        $this->path = __DIR__ . DIRECTORY_SEPARATOR . "../../../Database" . DIRECTORY_SEPARATOR . strtolower($this->avalaible[0]) . ".json";
        if ($this->typeData !== "Config") {
            return;
        }

        if (strpos($key, ".") !== false) {
            $keys = explode(".", $key);
            $data = &$this->data;

            foreach ($keys as $innerKey) {
                if (!isset($data[$innerKey])) {
                    $data[$innerKey] = [];
                }
                $data = &$data[$innerKey];
            }

            $data = $value;
        } else {
            $this->data[$key] = $value;
        }

        $this->save();
    }

    public function overrideConfig(): void
    {
        $this->data = [
            "online" => true,
            "cache" => [
                "updated" => 289434923,
                "loadedBefore" => false,
                "session" => [
                    "name" => "none",
                    "number" => 0
                ]
            ],
            "isGame" => false,
            "gameData" => []
        ];
        $this->save();
    }

    public function exitSession()
    {
        $this->set("cache.session.name", "none");
        echo ConsoleFormat::YELLOW .        "[DEBUG] " . ConsoleFormat::RESET;
    }

    private function save(): void
    {
        file_put_contents($this->path, json_encode($this->data, JSON_PRETTY_PRINT));
    }
}

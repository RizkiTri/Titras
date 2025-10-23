<?php

namespace Sequtiyhusky\Fania\Helpers;

class GameRepo
{
    private $data = [],
        $information = [];

    private $path = __DIR__ . "/../../../Database/Game/";

    public function __construct()
    {
        $this->validateLocation();
    }

    // public function save() {}

    private function validateLocation()
    {
        if (!file_exists($this->path . "information.json")) {
            exit("Config game file not found");
        }

        $this->information = \json_decode(\file_get_contents($this->path . "information.json"), true);
        if ($this->information["version"] !== "0.0.1") {
            exit("outdated version game");
        }
    }

    public function getAllProfile()
    {
        $allProfile = [];

        foreach (glob($this->path . "profile" .  DIRECTORY_SEPARATOR . "*.json") as $profile) {
            $allProfile[] = $profile;
        }

        return $allProfile;
    }

    public function createProfile(string $name): bool
    {
        $profilePath = $this->path . "profile" . DIRECTORY_SEPARATOR;
        $name = preg_replace('/[^a-zA-Z0-9_-]/', '', $name);
        if (!is_dir($profilePath)) {
            mkdir($profilePath, 0777, true);
        }

        $filename = $profilePath . $name . ".json";
        if (file_exists($filename)) {
            return false;
        }

        $defaultProfile = [
            'name' => $name,
            'created_at' => date('Y-m-d H:i:s'),
            'score' => 0,
            "stats" => [],
            "id_progress" => 0
        ];

        return file_put_contents($filename, json_encode($defaultProfile)) !== false;
    }
}

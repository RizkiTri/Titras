<?php

namespace Sequtiyhusky\Fania\Helpers;

class CommandManager
{
    private $path = __DIR__ . DIRECTORY_SEPARATOR . "../../../Database" . DIRECTORY_SEPARATOR . "registeredcommand.json";

    private $name;

    private $category;

    public function __construct(string $name, string $category)
    {
        $this->name = $name;
        $this->category = $category;
    }

    public function getCommand(ChatManager $chat): void
    {
        $data = json_decode(file_get_contents($this->path), true);
        if (!isset($data[$this->category])) {
            echo "No Category found";
            return;
        }

        if (!isset($data[$this->category][$this->name])) {
            echo "No Command name found";
            return;
        }

        (new $data[$this->category][$this->name]["path"])->execute($chat);
    }
}

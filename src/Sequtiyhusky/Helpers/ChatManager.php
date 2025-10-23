<?php

namespace Sequtiyhusky\Fania\Helpers;

use Sequtiyhusky\Fania\Helpers\DataManager;
use Sequtiyhusky\Fania\Utils\ConsoleFormat;

class ChatManager
{

    /** @var mixed Input from console */
    public $message;

    public const PREFIX = ConsoleFormat::BOLD . ConsoleFormat::YELLOW . "[" . ConsoleFormat::CYAN . "FANIA" . ConsoleFormat::YELLOW . "]" . ConsoleFormat::RESET;

    /** @var array Arguments*/
    public $args = [];

    public $time;

    /** @var DataManager File utama atau otak*/
    private $data;

    public function __construct()
    {
        $this->message = trim(fgets(STDIN));
        $this->time    = time();
        $this->data    = new DataManager("Config");

        $this->parseMessage($this->message);
    }

    private function parseMessage(mixed $data): void
    {
        if ($data === "exit") {
            $this->data->set("cache.loadedBefore", false);
            $this->data->set("cache.session.name", "none");
            $this->data->set("cache.session.number", 0);
            die();
        }

        if ($this->data->get("cache.session.name") === "none") {
            if (strpos($this->message, ":") !== false) {
                $arguments = explode(" ", $this->message);
                $structure = explode(":", array_shift($arguments));

                if (count($structure) < 2) {
                    echo "Format command tidak valid. Gunakan format: kategori:command\n";
                    return;
                }

                $this->args = $arguments;

                $this->data->set("cache.session.name", implode(":", $structure));
                (new CommandManager($structure[1], $structure[0]))->getCommand($this);
            }
        } else {
            $command = explode(":", $this->data->get("cache.session.name"));
            (new CommandManager($command[1], $command[0]))->getCommand($this);
        }
    }

    public function clearConsole(): void
    {
        echo chr(27) . chr(91) . 'H' . chr(27) . chr(91) . 'J';
    }

    public function getArgument(): array
    {
        return $this->args;
    }

    public function getDataManager(): DataManager
    {
        return $this->data;
    }

    public function reply(string $message, bool $newLine = true, bool $debug = false, bool $typing = false): void
    {
        $output = " " . $message;

        if ($typing) {
            echo self::PREFIX;
            $this->typing($output);
        } else {
            echo self::PREFIX;
            echo $output;
        }

        if ($newLine) {
            echo "\n";
            if ($debug) {
                echo ConsoleFormat::YELLOW . "[DEBUG] " . ConsoleFormat::RESET;
            }
        }
    }

    public function typing(string $text, int $speed = 50000): void
    {
        $length = strlen($text);
        for ($i = 0; $i < $length; $i++) {
            echo $text[$i];
            usleep($speed);
            flush();
        }
    }
}

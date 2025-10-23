<?php

namespace Sequtiyhusky\Fania\Utils;

use Sequtiyhusky\Fania\Interfaces\ICommand;

class RegisterCommand
{
    private $pathCache = __DIR__
        . DIRECTORY_SEPARATOR . ".."
        . DIRECTORY_SEPARATOR . ".."
        . DIRECTORY_SEPARATOR . ".."
        . DIRECTORY_SEPARATOR . "Database"
        . DIRECTORY_SEPARATOR . "registeredcommand.json";

    public function __construct()
    {
        $this->pathCache = realpath($this->pathCache);
        $this->generateCacheFile();
    }

    private function getAllFiles(): array
    {
        $filesPath = [];

        foreach (
            glob(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR
                . "Commands" . DIRECTORY_SEPARATOR . "*"
                . DIRECTORY_SEPARATOR . "*.php") as $commandPath
        ) {
            $filesPath[] = $commandPath;
        }
        return $filesPath;
    }

    /**
     * generateCacheFile function
     *
     * @return void
     */
    private function generateCacheFile(): void
    {
        $filesPath     = $this->getAllFiles();
        $verified      = [];
        $baseDir       = realpath(__DIR__ . DIRECTORY_SEPARATOR . "..");
        $baseNamespace = '\\Sequtiyhusky\\Fania\\';
        $lenBase       = strlen(str_replace('\\', '/', $baseDir));

        foreach ($filesPath as $fullPath) {
            $real      = str_replace('\\', '/', realpath($fullPath));
            $relative  = substr($real, $lenBase + 1, -4);
            $fqcn      = $baseNamespace . str_replace('/', '\\', $relative);


            if ((new $fqcn()) instanceof ICommand) {
                $verified[] = new $fqcn();
            }
        }

        $this->generateFormatCache($verified);

        // TODO: Make the registeredCommand.json
        if (!file_exists($this->pathCache)) {
            $file = fopen($this->pathCache, 'wb');
            \fwrite($file, json_encode($this->generateFormatCache($verified), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            fclose($file);
        } else {
            $file = fopen($this->pathCache, 'wb');
            \fwrite($file, json_encode($this->generateFormatCache($verified), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            fclose($file);
        }
    }

    private function generateFormatCache(array $commands): array
    {
        $formatted = [];
        foreach ($commands as $command) {
            $formatted[$command->getCategory()][$command->getName()] = [
                'path'        => $command::class,
                'description' => $command->getDescription(),
                'usage'       => $command->getUsage(),

            ];
        }
        return $formatted;
    }
}

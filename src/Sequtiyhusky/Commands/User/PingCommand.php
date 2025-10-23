<?php

namespace Sequtiyhusky\Fania\Commands\User;

use Sequtiyhusky\Fania\Helpers\ChatManager;
use Sequtiyhusky\Fania\Interfaces\ICommand;

class PingCommand implements ICommand
{
    public function execute(ChatManager $chat): void
    {
        echo self::PREFIX . " Pong!" . PHP_EOL;
    }

    public function getName(): string
    {
        return "ping";
    }

    public function getCategory(): string
    {
        return "user";
    }

    public function getDescription(): string
    {
        return "Test Command";
    }

    public function getUsage(): string
    {
        return "user:ping";
    }
}

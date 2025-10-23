<?php

namespace Sequtiyhusky\Fania\Commands\User;

use Sequtiyhusky\Fania\Helpers\ChatManager;
use Sequtiyhusky\Fania\Interfaces\ICommand;

class HelloCommand implements ICommand
{
    public function execute(ChatManager $chat): void {}

    public function getName(): string
    {
        return "hello";
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
        return "user:hello";
    }
}

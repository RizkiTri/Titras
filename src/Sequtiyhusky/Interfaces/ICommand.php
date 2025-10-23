<?php

namespace Sequtiyhusky\Fania\Interfaces;

use Sequtiyhusky\Fania\Helpers\ChatManager;
use Sequtiyhusky\Fania\Utils\ConsoleFormat;

interface ICommand
{
    public const PREFIX = ConsoleFormat::BOLD . ConsoleFormat::YELLOW . "[" . ConsoleFormat::CYAN . "FANIA" . ConsoleFormat::YELLOW . "]" . ConsoleFormat::RESET;

    public function getName(): string;

    public function getCategory(): string;

    public function getDescription(): string;

    public function getUsage(): string;

    public function execute(ChatManager $chat): void;
}

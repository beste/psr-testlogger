<?php

declare(strict_types=1);

namespace Beste\Psr\Log;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;

final class TestLogger implements LoggerInterface
{
    private function __construct(public readonly Records $records)
    {
    }

    public static function create(): self
    {
        return new self(Records::empty());
    }

    public function emergency(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * @param LogLevel::* $level
     */
    public function log($level, Stringable|string $message, array $context = []): void
    {
        $this->records->add(Record::with($level, (string) $message, $context));
    }
}

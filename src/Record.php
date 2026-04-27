<?php

declare(strict_types=1);

namespace Beste\Psr\Log;

use Psr\Log\LogLevel;

/**
 * @phpstan-type BestePsrLogRecordShape array{
 *     level: LogLevel::*,
 *     message: string,
 *     context: array<mixed>
 * }
 */
final class Record
{
    private function __construct(
        /** @var LogLevel::* $level */
        public readonly string $level,
        public readonly Message $message,
        public readonly Context $context,
    ) {
    }

    /**
     * @param LogLevel::* $level
     * @param array<mixed> $context
     */
    public static function with(string $level, string $message, array $context): self
    {
        return new self(
            $level,
            new Message($message, $context),
            new Context($context),
        );
    }
}

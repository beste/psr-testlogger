<?php

declare(strict_types=1);

namespace Beste\Psr\Log\Tests;

use Beste\Psr\Log\TestLogger;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class TestLoggerTest extends TestCase
{
    /**
     * @test
     */
    public function it_logs_records(): void
    {
        $logger = TestLogger::create();

        $logger->emergency('Emergency', ['key' => 'value']);
        $logger->alert('Alert', ['key' => 'value']);
        $logger->critical('Critical', ['key' => 'value']);
        $logger->error('Error', ['key' => 'value']);
        $logger->warning('Warning', ['key' => 'value']);
        $logger->notice('Notice', ['key' => 'value']);
        $logger->info('Info', ['key' => 'value']);
        $logger->debug('Debug', ['key' => 'value']);

        self::assertCount(8, $logger->records);
    }
}

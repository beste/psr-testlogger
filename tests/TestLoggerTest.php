<?php

declare(strict_types=1);

namespace Beste\Psr\Log\Tests;

use Beste\Psr\Log\TestLogger;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class TestLoggerTest extends TestCase
{
    #[Test]
    public function itLogsRecords(): void
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

        $this->assertCount(8, $logger->records);
    }
}

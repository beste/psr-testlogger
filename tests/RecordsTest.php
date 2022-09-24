<?php

declare(strict_types=1);

namespace Beste\Psr\Log\Tests;

use Beste\Psr\Log\Context;
use Beste\Psr\Log\Record;
use Beste\Psr\Log\Records;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

/**
 * @internal
 *
 * @phpstan-import-type BestePsrLogContextShape from Context
 */
final class RecordsTest extends TestCase
{
    private Records $records;

    protected function setUp(): void
    {
        $this->records = Records::empty();
    }

    /**
     * @test
     */
    public function it_is_empty_when_created(): void
    {
        self::assertLogCount(0, $this->records);
    }

    /**
     * @test
     */
    public function it_accepts_new_records(): void
    {
        $this->records->add(Record::with(LogLevel::INFO, 'message', []));

        self::assertLogCount(1, $this->records);
    }

    /**
     * @test
     */
    public function it_can_be_filtered_by_level(): void
    {
        $this->records->add(self::simpleRecord(LogLevel::NOTICE));
        $this->records->add(self::simpleRecord(LogLevel::NOTICE));
        $this->records->add(self::simpleRecord(LogLevel::INFO));
        $this->records->add(self::simpleRecord(LogLevel::DEBUG));

        self::assertLogCount(4, $this->records);
        self::assertLogCount(2, $this->records->filteredByLevel(LogLevel::NOTICE));
        self::assertLogCount(1, $this->records->filteredByLevel(LogLevel::INFO));
        self::assertLogCount(1, $this->records->filteredByLevel(LogLevel::DEBUG));
        self::assertLogCount(3, $this->records->filteredByLevel(LogLevel::NOTICE, LogLevel::INFO));
    }

    /**
     * @test
     */
    public function it_provides_a_unique_list_of_included_levels(): void
    {
        $this->records->add(self::simpleRecord(LogLevel::NOTICE));
        $this->records->add(self::simpleRecord(LogLevel::NOTICE));
        $this->records->add(self::simpleRecord(LogLevel::INFO));
        $this->records->add(self::simpleRecord(LogLevel::DEBUG));

        self::assertCount(3, $this->records->levels());
        self::assertEqualsCanonicalizing([LogLevel::NOTICE, LogLevel::INFO, LogLevel::DEBUG], $this->records->levels());
    }

    /**
     * @test
     */
    public function it_can_be_filtered_by_messages_containing(): void
    {
        $this->records->add(self::simpleRecord(LogLevel::NOTICE, 'one two'));
        $this->records->add(self::simpleRecord(LogLevel::INFO, 'two three'));
        $this->records->add(self::simpleRecord(LogLevel::WARNING, 'ONE TWO THREE FOUR'));

        self::assertLogCount(3, $this->records->filteredByMessageContaining('two'));
        self::assertLogCount(2, $this->records->filteredByMessageContaining('o t'));
        self::assertLogCount(1, $this->records->filteredByMessageContaining('four'));
    }

    /**
     * @test
     */
    public function it_can_be_filtered_by_messages_that_match(): void
    {
        $this->records->add(self::simpleRecord(LogLevel::NOTICE, 'one two'));
        $this->records->add(self::simpleRecord(LogLevel::INFO, 'two three'));
        $this->records->add(self::simpleRecord(LogLevel::WARNING, 'ONE TWO THREE'));

        self::assertLogCount(1, $this->records->filteredByMessageMatching('/one/'));
        self::assertLogCount(2, $this->records->filteredByMessageMatching('/o\st/i'));
        self::assertLogCount(3, $this->records->filteredByMessageMatching('/two/i'));
    }

    /**
     * @test
     */
    public function it_tells_if_it_includes_certain_kind_of_messages(): void
    {
        $this->records->add(self::simpleRecord(LogLevel::NOTICE));
        $this->records->add(self::simpleRecord(LogLevel::NOTICE));
        $this->records->add(self::simpleRecord(LogLevel::INFO));
        $this->records->add(self::simpleRecord(LogLevel::DEBUG));

        self::assertTrue($this->records->includeMessagesWithLevel(LogLevel::NOTICE));
        self::assertFalse($this->records->includeMessagesWithLevel(LogLevel::CRITICAL));

        self::assertTrue($this->records->includeMessagesContaining('notice'));
        self::assertFalse($this->records->includeMessagesContaining('critical'));

        self::assertTrue($this->records->includeMessagesMatching('/not/i'));
        self::assertFalse($this->records->includeMessagesMatching('/crit/i'));

        self::assertTrue($this->records->includeMessagesBy(static fn (Record $r) => str_contains((string) $r->message, 'n')));
        self::assertFalse($this->records->includeMessagesBy(static fn (Record $r) => str_contains((string) $r->message, 'l')));
    }

    private static function assertLogCount(int $expectedCount, Records $log): void
    {
        self::assertCount($expectedCount, $log);
        self::assertCount($expectedCount, $log->all());
    }

    /**
     * @param LogLevel::* $logLevel
     * @param BestePsrLogContextShape|null $context
     */
    private static function simpleRecord(string $logLevel, ?string $message = null, ?array $context = null): Record
    {
        $message ??= ucwords($logLevel);
        $context ??= ['level' => $logLevel];

        return Record::with($logLevel, $message, $context);
    }
}

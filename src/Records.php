<?php

declare(strict_types=1);

namespace Beste\Psr\Log;

use Countable;
use Psr\Log\LogLevel;

final class Records implements Countable
{
    private function __construct(
        /** @var list<Record> */
        private array $records,
    ) {
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public function add(Record $record): void
    {
        $this->records[] = $record;
    }

    public function filteredBy(callable $callback): self
    {
        return new self(
            array_values(
                array_filter($this->records, $callback),
            ),
        );
    }

    public function count(): int
    {
        return \count($this->records);
    }

    /**
     * @return list<Record>
     */
    public function all(): array
    {
        return $this->records;
    }

    /**
     * @param LogLevel::* $level
     */
    public function filteredByLevel(string ...$level): self
    {
        return $this->filteredBy(
            static fn (Record $r) => \in_array($r->level, $level, strict: true),
        );
    }

    public function filteredByMessageContaining(string $needle): self
    {
        return $this->filteredBy(
            static fn (Record $r) => $r->message->contains($needle),
        );
    }

    public function filteredByMessageMatching(string $pattern): self
    {
        return $this->filteredBy(
            static fn (Record $r) => $r->message->matches($pattern),
        );
    }

    public function includeMessagesContaining(string $needle): bool
    {
        return $this->filteredByMessageContaining($needle)->count() > 0;
    }

    public function includeMessagesMatching(string $pattern): bool
    {
        return $this->filteredByMessageMatching($pattern)->count() > 0;
    }

    public function includeMessagesBy(callable $callback): bool
    {
        return $this->filteredBy($callback)->count() > 0;
    }

    /**
     * @param LogLevel::* $level
     */
    public function includeMessagesWithLevel(string $level): bool
    {
        return $this->filteredByLevel($level)->count() > 0;
    }

    /**
     * @return list<LogLevel::*>
     */
    public function levels(): array
    {
        return array_unique(
            array_map(static fn (Record $r) => $r->level, $this->records),
        );
    }
}

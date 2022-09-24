<?php

declare(strict_types=1);

namespace Beste\Psr\Log\Tests;

use Beste\Psr\Log\Message;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class MessageTest extends TestCase
{
    /**
     * @test
     */
    public function it_matches_a_value_containing(): void
    {
        $message = new Message('This is a test message');

        self::assertTrue($message->contains('test'));
        self::assertTrue($message->contains('Test'));
        self::assertTrue($message->contains('is a'));
    }

    /**
     * @test
     */
    public function it_matches_a_value_by_regular_expression(): void
    {
        $message = new Message('The value 1234 is a number');

        self::assertTrue($message->matches('/\d/'));
    }

    /**
     * @test
     */
    public function it_replaces_placeholders(): void
    {
        $date = new DateTimeImmutable();
        $formattedDate = $date->format(\DATE_ATOM);

        $message = new Message('A {placeholder} with a {date}, an {object}, an {array} and {something else}', [
            'placeholder' => 'message',
            'date' => $date,
            'object' => (object) ['key' => 'value'],
            'array' => ['key' => 'value'],
            'something else' => 'whatever',
            'unknown' => 'unreplaced',
        ]);

        self::assertSame(
            "A message with a {$formattedDate}, an [object], an [array] and whatever",
            (string) $message,
        );
    }
}

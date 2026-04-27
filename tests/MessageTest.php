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
    public function itMatchesAValueContaining(): void
    {
        $message = new Message('This is a test message');

        $this->assertTrue($message->contains('test'));
        $this->assertTrue($message->contains('Test'));
        $this->assertTrue($message->contains('is a'));
    }

    /**
     * @test
     */
    public function itMatchesAValueByRegularExpression(): void
    {
        $message = new Message('The value 1234 is a number');

        $this->assertTrue($message->matches('/\d/'));
    }

    /**
     * @test
     */
    public function itReplacesPlaceholders(): void
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

        $this->assertSame(
            "A message with a {$formattedDate}, an [object], an [array] and whatever",
            (string) $message,
        );
    }
}

<?php

declare(strict_types=1);

namespace Beste\Psr\Log;

use DateTimeInterface;
use Stringable;

/**
 * @phpstan-import-type BestePsrLogContextShape from Context
 */
final class Message implements Stringable
{
    private string $value;

    /**
     * @param Context|BestePsrLogContextShape|null $context
     */
    public function __construct(
        string $message,
        Context|array|null $context = null,
    ) {
        if (!$context instanceof Context) {
            $context = new Context($context ?? []);
        }

        $this->value = self::processMessage($message, $context);
    }

    public function __toString()
    {
        return $this->value;
    }

    public function contains(string $needle): bool
    {
        return str_contains(mb_strtolower($this->value), mb_strtolower($needle)) !== false;
    }

    public function matches(string $pattern): bool
    {
        return preg_match($pattern, $this->value) > 0;
    }

    private static function processMessage(string $message, Context $context): string
    {
        if (str_contains($message, '{') === false) {
            return $message;
        }

        $replacements = [];

        foreach ($context->data as $key => $value) {
            $placeholder = '{' . $key . '}';

            if (str_contains($message, $placeholder) === false) {
                continue;
            }

            if ($value === null || \is_scalar($value) || $value instanceof Stringable) {
                $replacements[$placeholder] = $value;
            } elseif ($value instanceof DateTimeInterface) {
                $replacements[$placeholder] = $value->format(\DATE_ATOM);
            } else {
                $replacements[$placeholder] = '[' . \gettype($value) . ']';
            }
        }

        return strtr($message, $replacements);
    }
}

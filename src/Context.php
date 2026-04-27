<?php

declare(strict_types=1);

namespace Beste\Psr\Log;

/**
 * @phpstan-type BestePsrLogContextShape array<mixed>
 */
final class Context
{
    /**
     * @param BestePsrLogContextShape $data
     */
    public function __construct(
        public readonly array $data = [],
    ) {
    }
}

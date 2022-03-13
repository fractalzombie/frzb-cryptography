<?php

declare(strict_types=1);

namespace FRZB\Component\Cryptography\Tests\Resources\Stub;

final class TestMessage
{
    public function __construct(
        public readonly string $id
    ) {
    }
}

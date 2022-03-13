<?php

declare(strict_types=1);

namespace FRZB\Component\Cryptography\Helper;

class Random
{
    private const DEFAULT_SECURE_STRING_LENGTH = 16;

    /** @noinspection PhpUnhandledExceptionInspection */
    public function secureString(int $length = self::DEFAULT_SECURE_STRING_LENGTH): string
    {
        return bin2hex(random_bytes($length / 2));
    }
}

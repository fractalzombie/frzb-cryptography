<?php

declare(strict_types=1);

namespace FRZB\Component\Cryptography\Helper;

use FRZB\Component\Cryptography\Exception\CryptographyException;

final class RandomHelper
{
    private const DEFAULT_STRING_LENGTH = 16;

    public static function string(int $length = self::DEFAULT_STRING_LENGTH): string
    {
        try {
            return bin2hex(random_bytes($length));
        } catch (\Exception $e) {
            throw CryptographyException::fromThrowable($e);
        }
    }
}

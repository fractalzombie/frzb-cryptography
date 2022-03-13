<?php

declare(strict_types=1);

namespace FRZB\Component\Cryptography\Service;

use FRZB\Component\Cryptography\Exception\CryptographyException;

interface DecryptInterface
{
    /** @throws CryptographyException */
    public function decrypt(string $payload): string;
}

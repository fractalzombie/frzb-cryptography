<?php

declare(strict_types=1);


namespace FRZB\Component\Cryptography\Service;


use FRZB\Component\Cryptography\Exception\CryptographyException;

interface EncryptInterface
{
    /** @throws CryptographyException */
    public function encrypt(string $payload): string;

    public function isEncrypted(string $payload): bool;
}

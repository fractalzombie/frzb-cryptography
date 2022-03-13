<?php

declare(strict_types=1);

namespace FRZB\Component\Cryptography\Service;

interface CheckEncryptInterface
{
    public function isEncrypted(string $payload): bool;
}

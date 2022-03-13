<?php

declare(strict_types=1);

namespace FRZB\Component\Cryptography\Factory;

use phpseclib\Crypt\Base as Crypto;

interface CryptoFactoryInterface
{
    public function createAES(?string $iv = null, ?string $key = null): Crypto;
}

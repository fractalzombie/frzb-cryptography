<?php

declare(strict_types=1);

namespace FRZB\Component\Cryptography\Factory;

use FRZB\Component\Cryptography\Helper\Random;
use phpseclib\Crypt\AES;
use phpseclib\Crypt\Base as Crypto;

class CryptoFactory implements CryptoFactoryInterface
{
    private const DEFAULT_LENGTH = 128;

    public function __construct(
        private readonly Random $randomCrypto,
    ) {
    }

    public function createAES(?string $iv = null, ?string $key = null): Crypto
    {
        $iv ??= $this->randomCrypto->secureString(self::DEFAULT_LENGTH);
        $key ??= $this->randomCrypto->secureString(self::DEFAULT_LENGTH);

        $crypto = new AES(Crypto::MODE_CBC);
        $crypto->setIV($iv);
        $crypto->setKey($key);

        return $crypto;
    }
}

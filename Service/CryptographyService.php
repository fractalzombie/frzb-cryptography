<?php

declare(strict_types=1);

namespace FRZB\Component\Cryptography\Service;

use FRZB\Component\Cryptography\Exception\CryptographyException;
use FRZB\Component\Cryptography\Helper\RandomHelper;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\Common\SymmetricKey as Crypto;

class CryptographyService implements CryptographyInterface
{
    private const DEFAULT_LENGTH = 128;

    private Crypto $crypto;

    public function __construct(
        ?string $iv = null,
        ?string $key = null,
    ) {
        $iv ??= RandomHelper::string(self::DEFAULT_LENGTH);
        $key ??= RandomHelper::string(self::DEFAULT_LENGTH);
        $this->crypto = new AES(Crypto::MODE_CBC);
        $this->crypto->setIV($iv);
        $this->crypto->setKey($key);
    }

    public function isEncrypted(string $payload): bool
    {
        return str_starts_with($payload, '<ENC>') && str_ends_with($payload, '</ENC>');
    }

    public function encrypt(string $payload): string
    {
        try {
            $encrypted = $this->crypto->encrypt($payload);
        } catch (\Throwable $e) {
            throw CryptographyException::fromThrowable($e);
        }

        if (!$encrypted) {
            throw CryptographyException::encryptFailure();
        }

        return sprintf('<ENC>%s</ENC>', base64_encode($encrypted));
    }

    public function decrypt(string $payload): string
    {
        if (!$this->isEncrypted($payload)) {
            throw CryptographyException::notEncrypted();
        }

        $payload = str_replace(['<ENC>', '</ENC>'], ['', ''], $payload);
        $payload = base64_decode($payload, true);

        if (!$payload) {
            throw CryptographyException::decryptFailure();
        }

        try {
            return $this->crypto->decrypt($payload);
        } catch (\Throwable $e) {
            throw CryptographyException::fromThrowable($e);
        }
    }
}

<?php

declare(strict_types=1);

namespace FRZB\Component\Cryptography\Service;

use FRZB\Component\Cryptography\Exception\CryptographyException;
use phpseclib\Crypt\Base as Crypto;

class CryptographyService implements CryptographyInterface
{
    public function __construct(
        private readonly Crypto $crypto,
    ) {
    }

    public function isEncrypted(string $payload): bool
    {
        return str_starts_with($payload, '<ENC>') && str_ends_with($payload, '</ENC>');
    }

    public function encrypt(string $payload): string
    {
        try {
            $encrypted = (string) ($this->crypto->encrypt($payload) ?: throw CryptographyException::encryptFailure());
        } catch (\Throwable $e) {
            throw CryptographyException::fromThrowable($e);
        }

        return sprintf('<ENC>%s</ENC>', base64_encode($encrypted));
    }

    public function decrypt(string $payload): string
    {
        $this->isEncrypted($payload) ?: throw CryptographyException::notEncrypted();
        $payload = str_replace(['<ENC>', '</ENC>'], ['', ''], $payload);
        $payload = (string) (base64_decode($payload, true) ?: throw CryptographyException::decodeFailure());

        try {
            return (string) ($this->crypto->decrypt($payload) ?: throw CryptographyException::decryptFailure());
        } catch (\Throwable $e) {
            throw CryptographyException::fromThrowable($e);
        }
    }
}

<?php

declare(strict_types=1);


namespace FRZB\Component\Cryptography\Exception;


use JetBrains\PhpStorm\Pure;

class CryptographyException extends \LogicException
{
    private const DECRYPT_FAILURE_MESSAGE = 'Decrypt of payload is failure';
    private const ENCRYPT_FAILURE_MESSAGE = 'Encrypt of payload is failure';
    private const NOT_ENCRYPTED_PAYLOAD_MESSAGE = 'Payload is not encrypted';

    public static function fromThrowable(\Throwable $previous): self
    {
        return new self($previous->getMessage(), $previous->getCode(), $previous);
    }

    #[Pure]
    public static function decryptFailure(): self
    {
        return new self(self::DECRYPT_FAILURE_MESSAGE);
    }

    #[Pure]
    public static function encryptFailure(): self
    {
        return new self(self::ENCRYPT_FAILURE_MESSAGE);
    }

    #[Pure]
    public static function notEncrypted(): self
    {
        return new self(self::NOT_ENCRYPTED_PAYLOAD_MESSAGE);
    }
}

<?php

declare(strict_types=1);

namespace FRZB\Component\Cryptography\Integration\Symfony\Messenger\Serializer;

abstract class ExternalCryptoSerializer extends InternalCryptoSerializer
{
    protected static function getHeaders(array $decodedEnvelope): array
    {
        return array_merge($decodedEnvelope['headers'] ?? [], ['type' => static::getMessageType()]);
    }

    abstract protected static function getMessageType(): string;
}

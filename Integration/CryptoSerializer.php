<?php

declare(strict_types=1);

namespace FRZB\Component\Cryptography\Integration;

use FRZB\Component\Cryptography\Service\CryptographyInterface as CryptographyService;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\Serializer as BaseSerializer;
use Symfony\Component\Serializer\SerializerInterface as Serializer;

abstract class CryptoSerializer extends BaseSerializer
{
    private CryptographyService $crypto;

    public function __construct(
        CryptographyService $crypto,
        ?Serializer $serializer = null,
        string $format = 'json',
        array $context = []
    ) {
        parent::__construct($serializer, $format, $context);
        $this->crypto = $crypto;
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        return parent::decode([
            'body' => $this->crypto->decrypt(self::getBody($encodedEnvelope)),
            'headers' => self::getHeaders($encodedEnvelope),
        ]);
    }

    public function encode(Envelope $envelope): array
    {
        $encodedEnvelope = parent::encode($envelope);

        return [
            'body' => $this->crypto->encrypt(self::getBody($encodedEnvelope)),
            'headers' => self::getHeaders($encodedEnvelope),
        ];
    }

    abstract protected static function getMessageType(): string;

    private static function getBody(array $decodedEnvelope): ?string
    {
        return $decodedEnvelope['body'] ?? '{}';
    }

    private static function getHeaders(array $decodedEnvelope): array
    {
        return array_merge($decodedEnvelope['headers'] ?? [], ['type' => static::getMessageType()]);
    }
}

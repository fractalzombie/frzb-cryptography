<?php

declare(strict_types=1);

namespace FRZB\Component\Cryptography\Tests\Unit\Integration\Symfony\Messenger\Serializer;

use FRZB\Component\Cryptography\Factory\CryptoFactory as CryptoFactoryImpl;
use FRZB\Component\Cryptography\Helper\Random;
use FRZB\Component\Cryptography\Service\CryptographyInterface;
use FRZB\Component\Cryptography\Service\CryptographyService;
use FRZB\Component\Cryptography\Tests\Resources\Stub\TestMessage;
use FRZB\Component\Cryptography\Tests\Resources\Stub\TestMessageSerializer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface as MessengerSerializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @internal
 */
final class ExternalCryptoSerializerTest extends KernelTestCase
{
    private MessengerSerializer $serializer;
    private CryptographyInterface $crypto;

    protected function setUp(): void
    {
        $random = new Random();
        $cryptoFactory = new CryptoFactoryImpl($random);

        $this->crypto = new CryptographyService($cryptoFactory->createAES());
        $this->serializer = new TestMessageSerializer($this->crypto, self::getSymfonySerializer());
    }

    public function testDecodeMethod(): void
    {
        $id = (string) Uuid::v4();
        $envelope = new Envelope(new TestMessage($id), [new TransportMessageIdStamp($id)]);

        $encoded = $this->serializer->encode($envelope);

        self::assertIsArray($encoded);
        self::assertArrayHasKey('body', $encoded);
        self::assertArrayHasKey('headers', $encoded);
        self::assertStringStartsWith('<ENC>', $encoded['body']);
        self::assertStringEndsWith('</ENC>', $encoded['body']);
        self::assertTrue($this->crypto->isEncrypted($encoded['body']));
        self::assertSame(TestMessage::class, $encoded['headers']['type']);
        self::assertSame('application/json', $encoded['headers']['Content-Type']);
    }

    /** @throws \JsonException */
    public function testEncodeMethod(): void
    {
        $id = (string) Uuid::v4();
        $json = json_encode(['id' => $id], \JSON_THROW_ON_ERROR);

        $payload = [
            'body' => $this->crypto->encrypt($json),
            'headers' => [
                'type' => TestMessage::class,
                'X-Message-Stamp-Symfony\\Component\\Messenger\\Stamp\\TransportMessageIdStamp' => "[{$json}]",
                'Content-Type' => 'application/json',
            ],
        ];

        $decoded = $this->serializer->decode($payload);

        self::assertInstanceOf(TestMessage::class, $decoded->getMessage());
        self::assertSame($id, $decoded->getMessage()->id);
        self::assertSame($id, $decoded->last(TransportMessageIdStamp::class)->getId());
    }

    public function testEncodeAndThenDecodeMethod(): void
    {
        $id = (string) Uuid::v4();
        $envelope = new Envelope(new TestMessage($id), [new TransportMessageIdStamp($id)]);

        $encoded = $this->serializer->encode($envelope);
        $decoded = $this->serializer->decode($encoded);

        self::assertSame($envelope->getMessage()->id, $decoded->getMessage()->id);
    }

    private static function getSymfonySerializer(): SerializerInterface
    {
        return new Serializer(
            [new ObjectNormalizer(), new ArrayDenormalizer()],
            [JsonEncoder::FORMAT => new JsonEncoder()],
        );
    }
}

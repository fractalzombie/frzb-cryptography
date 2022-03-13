<?php

declare(strict_types=1);

namespace FRZB\Component\Cryptography\Tests\Unit\Service;

use FRZB\Component\Cryptography\Exception\CryptographyException;
use FRZB\Component\Cryptography\Factory\CryptoFactory as CryptoFactoryImpl;
use FRZB\Component\Cryptography\Factory\CryptoFactoryInterface as CryptoFactory;
use FRZB\Component\Cryptography\Helper\Random;
use FRZB\Component\Cryptography\Service\CryptographyInterface as Cryptography;
use FRZB\Component\Cryptography\Service\CryptographyService;
use phpseclib\Crypt\AES;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class CryptographyServiceTest extends TestCase
{
    private Cryptography $crypto;
    private CryptoFactory $cryptoFactory;
    private Random $random;

    protected function setUp(): void
    {
        $this->random = new Random();
        $this->cryptoFactory = new CryptoFactoryImpl($this->random);
        $this->crypto = new CryptographyService($this->cryptoFactory->createAES());
    }

    /** @dataProvider dataProvider */
    public function testItCanEncryptString(string $payload): void
    {
        $this->crypto = new CryptographyService($this->cryptoFactory->createAES());

        $encrypted = $this->crypto->encrypt($payload);

        self::assertStringStartsWith('<ENC>', $encrypted);
        self::assertStringEndsWith('</ENC>', $encrypted);
    }

    /** @dataProvider dataProvider */
    public function testItCanRecognizeEncryptedString(string $payload): void
    {
        $encrypted = $this->crypto->encrypt($payload);

        self::assertFalse($this->crypto->isEncrypted($payload));
        self::assertTrue($this->crypto->isEncrypted($encrypted));
    }

    /** @dataProvider dataProvider */
    public function testItCanDecryptString(string $payload): void
    {
        $this->crypto = new CryptographyService(
            $this->cryptoFactory->createAES($this->random->secureString(), $this->random->secureString())
        );

        $encrypted = $this->crypto->encrypt($payload);
        $decrypted = $this->crypto->decrypt($encrypted);

        self::assertTrue($this->crypto->isEncrypted($encrypted));
        self::assertSame($payload, $decrypted);
    }

    /** @dataProvider dataProvider */
    public function testItFailWhenEncryptWithFalseFromCrypto(string $payload): void
    {
        $crypto = $this->createMock(AES::class);

        $crypto
            ->method('encrypt')
            ->willReturn(false)
        ;

        $this->crypto = new CryptographyService($crypto);

        $this->expectException(CryptographyException::class);
        $this->expectExceptionMessage(CryptographyException::ENCRYPT_FAILURE_MESSAGE);

        $this->crypto->encrypt($payload);
    }

    /** @dataProvider dataProvider */
    public function testItFailWhenEncryptWithExceptionFromCrypto(string $payload): void
    {
        $crypto = $this->createMock(AES::class);
        $message = 'Something goes wrong';
        $exception = new \LogicException($message);

        $crypto
            ->method('encrypt')
            ->willThrowException($exception)
        ;

        $this->crypto = new CryptographyService($crypto);

        $this->expectException(CryptographyException::class);
        $this->expectExceptionMessage($message);

        $this->crypto->encrypt($payload);
    }

    /** @dataProvider dataProvider */
    public function testItFailWhenDecryptStringWithWrongKey(string $payload): void
    {
        $encrypted = $this->crypto->encrypt($payload);

        $this->crypto = new CryptographyService($this->cryptoFactory->createAES());

        $this->expectException(CryptographyException::class);
        $this->expectExceptionMessage(CryptographyException::DECRYPT_FAILURE_MESSAGE);

        $this->crypto->decrypt($encrypted);
    }

    /** @dataProvider dataProvider */
    public function testItFailWhenDecryptStringIsNotEncrypted(string $payload): void
    {
        $this->expectException(CryptographyException::class);
        $this->expectExceptionMessage(CryptographyException::NOT_ENCRYPTED_PAYLOAD_MESSAGE);

        $this->crypto->decrypt($payload);
    }

    /** @dataProvider dataProvider */
    public function testItFailWhenDecryptStringWithoutBase64Encode(string $payload): void
    {
        $this->expectException(CryptographyException::class);
        $this->expectExceptionMessage(CryptographyException::DECODE_BASE64_FAILURE_MESSAGE);

        $this->crypto->decrypt(sprintf('<ENC>%s</ENC>', $payload));
    }

    public function dataProvider(): iterable
    {
        $payload = file_get_contents(__DIR__.'/../../Resources/files/payload.json');

        yield 'with payload' => ['payload' => $payload];
    }
}

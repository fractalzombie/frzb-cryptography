<?php

declare(strict_types=1);

namespace FRZB\Component\Cryptography\Tests\Unit\Helper;

use FRZB\Component\Cryptography\Helper\Random;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class RandomTest extends TestCase
{
    /** @dataProvider dataProvider */
    public function testSecureStringMethod(int $length): void
    {
        $generated = (new Random())->secureString($length);

        self::assertIsString($generated);
        self::assertSame($length, \strlen($generated));
    }

    public function dataProvider(): iterable
    {
        yield 'length 128' => [
            'length' => 128,
        ];

        yield 'length 8' => [
            'length' => 8,
        ];
    }
}

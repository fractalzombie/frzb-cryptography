<?php

declare(strict_types=1);

namespace FRZB\Component\Cryptography\Tests\Resources\Stub;

use FRZB\Component\Cryptography\Integration\Symfony\Messenger\Serializer\ExternalCryptoSerializer;

class TestMessageSerializer extends ExternalCryptoSerializer
{
    protected static function getMessageType(): string
    {
        return TestMessage::class;
    }
}

<?php

declare(strict_types=1);

namespace FRZB\Component\Cryptography;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class CryptographyBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->registerExtension(new CryptographyExtension());
    }
}

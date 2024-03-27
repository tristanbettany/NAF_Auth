<?php

namespace Domain\Definitions;

use Domain\Interfaces\SamlServiceInterface;
use Domain\Services\SamlService;
use Infrastructure\Abstractions\AbstractDefinition;
use Psr\Container\ContainerInterface;

final class SamlServiceDefinition extends AbstractDefinition
{
    public function define(): array
    {
        return [
            SamlServiceInterface::class => function (ContainerInterface $container) {
                return new SamlService();
            },
        ];
    }
}

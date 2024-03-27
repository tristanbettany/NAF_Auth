<?php

use Database\Definitions\DatabaseDefinition;
use Domain\Definitions\RootServiceDefinition;
use Presentation\Definitions\RespondersDefinition;
use Domain\Definitions\AppDefinition;
use Domain\Definitions\AuthServiceDefinition;
use Domain\Definitions\SamlServiceDefinition;

return [
    'definitions' => [
        DatabaseDefinition::class,
        RespondersDefinition::class,
        AppDefinition::class,
        RootServiceDefinition::class,
        AuthServiceDefinition::class,
        SamlServiceDefinition::class,
    ],
];
<?php

namespace Application\Config\Schemas;

use Nette\Schema\Expect;
use Nette\Schema\Schema;

final class SSO
{
    public static function define(): Schema
    {
        return Expect::structure([
            'login_url' => Expect::string()->required(),
            'cert_file_name' => Expect::string()->default('sso.cert')->required(),

            'attribute_names' => Expect::structure([
                'id' => Expect::string()->default('id')->required(),
                'email' => Expect::string()->default('email')->required(),
                'firstName' => Expect::string()->default('firstName')->required(),
                'lastName' => Expect::string()->default('lastName')->required(),
            ]),
        ]);
    }

    public static function values(): array
    {
        return include_once __DIR__ . '/../sso.php';
    }
}
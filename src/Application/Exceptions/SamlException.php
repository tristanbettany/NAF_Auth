<?php

namespace Application\Exceptions;

use RuntimeException;

final class SamlException extends RuntimeException
{
    public function __construct(
        $message = 'Saml Exception',
        $code = 500
    ) {
        parent::__construct(
            $message,
            $code
        );
    }
}
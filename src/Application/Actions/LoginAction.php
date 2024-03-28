<?php

namespace Application\Actions;

use Domain\Interfaces\RootServiceInterface;
use Domain\Interfaces\SamlServiceInterface;
use Infrastructure\Abstractions\AbstractAction;
use Infrastructure\Facades\Auth;
use Infrastructure\Facades\Config;
use Presentation\Interfaces\LoginResponderInterface;
use Psr\Http\Message\ResponseInterface;

final class LoginAction extends AbstractAction
{
    public function __construct(
        private RootServiceInterface $rootService,
        private SamlServiceInterface $samlService,
        private LoginResponderInterface $responder
    ) {
    }

    public function get(): ResponseInterface
    {
        return $this->respond($this->responder, [
            'ssoLoginUrl' => Config::get('sso.login_url'),
        ]);
    }

    public function post(): ResponseInterface
    {
        if ($this->samlService->isRequestSAML($this->request) === true) {
            $isLoggedIn = Auth::samlLogin($this->request);
        } else {
            $body = $this->request->getParsedBody();

            $isLoggedIn = Auth::login(
                $body['email'],
                $body['password']
            );
        }

        return $this->respond($this->responder, [
            'isLoggedIn' => $isLoggedIn
        ]);
    }
}
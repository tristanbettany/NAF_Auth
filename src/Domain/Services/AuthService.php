<?php

namespace Domain\Services;

use Application\Exceptions\NoDataToHydrateException;
use Application\Exceptions\SamlException;
use Application\Exceptions\ValidationException;
use Database\Entities\UserEntity;
use Database\Interfaces\UserRepositoryInterface;
use Domain\Interfaces\AuthServiceInterface;
use Domain\Interfaces\SamlServiceInterface;
use Domain\Interfaces\SessionInterface;
use Infrastructure\Facades\Config;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;

final class AuthService implements AuthServiceInterface
{
    public function __construct(
        private SessionInterface $session,
        private SamlServiceInterface $samlService,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function samlLogin(ServerRequestInterface $request): bool
    {
        $saml = $this->samlService->getSAMLFromRequest($request);

        try {
            $isSigned = $this->samlService->verifySignedSAML($saml);
            if ($isSigned === false) {
                throw new SamlException('Failed to verify SAML signature');
            }
        } catch (\Exception $e) {
            throw new SamlException('Failed to verify SAML signature : ' . $e->getMessage());
        }

        $attributeStatements = $this->samlService->getAttributeStatementsFromSAML($saml);

        if (
            empty($attributeStatements[Config::get('sso.attribute_names.id')]) === true
            || empty($attributeStatements[Config::get('sso.attribute_names.email')]) === true
            || empty($attributeStatements[Config::get('sso.attribute_names.firstName')]) === true
            || empty($attributeStatements[Config::get('sso.attribute_names.lastName')]) === true
        ) {
            return false;
        }

        try {
            $userEntity = $this->userRepository->findBySSOID($attributeStatements['id']);
        } catch (NoDataToHydrateException $e) {
            $userId = $this->userRepository->create(
                Config::get('sso.attribute_names.firstName'),
                Config::get('sso.attribute_names.lastName'),
                Config::get('sso.attribute_names.email'),
                $this->hashPassword(uniqid() . Uuid::uuid4()->toString() . uniqid()),
                Config::get('sso.attribute_names.id')
            );
            $userEntity = $this->userRepository->findByID($userId);
        }

        if ($attributeStatements['email'] !== $userEntity->email) {
            return false;
        }

        $this->session->set('auth', true);
        $this->session->set('auth-uuid', $userEntity->uuid);
        $this->session->set('auth-is-admin', $userEntity->isAdmin);

        return true;
    }

    public function login(
        string $email,
        string $password
    ): bool {
        $user = $this->userRepository->findByEmail($email);

        if (empty($user) === true) {
            return false;
        }

        $loggedIn = password_verify($password, $user->passwordHash);
        if ($loggedIn === false) {
            return false;
        }

        $this->session->set('auth', true);
        $this->session->set('auth-uuid', $user->uuid);
        $this->session->set('auth-is-admin', $user->isAdmin);

        return true;
    }

    public function logout(): void
    {
        $this->session->delete('auth');
        $this->session->delete('auth-uuid');
        $this->session->delete('auth-is-admin');
    }

    public function check(): bool
    {
        $isLoggedIn = $this->session->get('auth');

        if ($isLoggedIn === true) {
            return true;
        }

        return false;
    }

    public function user(): ?UserEntity
    {
        if ($this->check() === true) {
            $userUUID = $this->session->get('auth-uuid');

            return $this->userRepository->findByUUID(Uuid::fromString($userUUID));
        }

        return null;
    }

    public static function validateNewPassword(
        string $password,
        string $passwordMatch
    ): void {
        if ($password !== $passwordMatch) {
            throw new ValidationException('Your passwords do not match');
        }
        if (strlen($password) < 16) {
            throw new ValidationException('Your password must be at least 16 characters long');
        }
        if (!preg_match("#[a-zA-Z]{8,}#", $password)) {
            throw new ValidationException('Your password must include at least 8 standard characters');
        }
        if (!preg_match("#[0-9]{4,}#", $password)) {
            throw new ValidationException('Your password must include at least 4 numbers');
        }
        if (!preg_match("#[\W]{4,}#", $password)) {
            throw new ValidationException('Your password must include at least 4 symbols');
        }
    }

    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
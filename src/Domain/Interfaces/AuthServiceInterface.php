<?php

namespace Domain\Interfaces;

use Database\Entities\UserEntity;
use Infrastructure\Interfaces\ServiceInterface;
use Psr\Http\Message\ServerRequestInterface;

interface AuthServiceInterface extends ServiceInterface
{
    public function samlLogin(ServerRequestInterface $request): bool;
    public function login(
        string $email,
        string $password
    ): bool;
    public function logout(): void;
    public function check(): bool;
    public function user(): ?UserEntity;
    public static function validateNewPassword(
        string $password,
        string $passwordMatch
    ): void;
    public function hashPassword(string $password): string;
}
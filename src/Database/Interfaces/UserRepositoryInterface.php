<?php

namespace Database\Interfaces;

use Database\Entities\UserEntity;
use Infrastructure\Interfaces\RepositoryInterface;
use Ramsey\Uuid\UuidInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByID(int $id): UserEntity;
    public function findByUUID(UuidInterface $uuid): UserEntity;
    public function findByEmail(string $email): UserEntity;
    public function findBySSOID(string $ssoId): UserEntity;
    public function create(
        string $firstName,
        string $lastName,
        string $email,
        string $passwordHash,
        string $ssoId = null,
        bool $isAdmin = false
    ): int;
}
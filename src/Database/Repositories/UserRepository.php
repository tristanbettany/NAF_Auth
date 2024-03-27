<?php

namespace Database\Repositories;

use Database\Entities\UserEntity;
use Database\Interfaces\UserRepositoryInterface;
use Infrastructure\Abstractions\AbstractRepository;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
    protected string $entity = UserEntity::class;

    public function findByID(int $id): UserEntity
    {
        $data = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(UserEntity::TABLE)
            ->where('id = ?')
            ->andWhere('is_active = ?')
            ->setParameter(0, $id)
            ->setParameter(1, 1)
            ->fetchAssociative();

        return $this->hydrate($data);
    }

    public function findByUUID(UuidInterface $uuid): UserEntity
    {
        $data = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(UserEntity::TABLE)
            ->where('uuid = ?')
            ->andWhere('is_active = ?')
            ->setParameter(0, $uuid->toString())
            ->setParameter(1, 1)
            ->fetchAssociative();

        return $this->hydrate($data);
    }

    public function findByEmail(string $email): UserEntity
    {
        $data = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(UserEntity::TABLE)
            ->where('email = ?')
            ->andWhere('is_active = ?')
            ->setParameter(0, $email)
            ->setParameter(1, 1)
            ->fetchAssociative();

        return $this->hydrate($data);
    }

    public function findBySSOID(string $ssoId): UserEntity
    {
        $data = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(UserEntity::TABLE)
            ->where('sso_id = ?')
            ->andWhere('is_active = ?')
            ->setParameter(0, $ssoId)
            ->setParameter(1, 1)
            ->fetchAssociative();

        return $this->hydrate($data);
    }

    public function create(
        string $firstName,
        string $lastName,
        string $email,
        string $passwordHash,
        string $ssoId = null,
        bool $isAdmin = false
    ): int {
        $this->connection->createQueryBuilder()
            ->insert('users')
            ->setValue('uuid', '?')
            ->setValue('first_name', '?')
            ->setValue('last_name', '?')
            ->setValue('email', '?')
            ->setValue('password_hash', '?')
            ->setValue('is_admin', '?')
            ->setValue('created_at', '?')
            ->setValue('updated_at', '?')
            ->setValue('sso_id', '?')
            ->setParameter('0', Uuid::uuid4()->toString())
            ->setParameter('1', $firstName)
            ->setParameter('2', $lastName)
            ->setParameter('3', $email)
            ->setParameter('4', $passwordHash)
            ->setParameter('5', (int) $isAdmin)
            ->setParameter('6', (new DateTimeImmutable())->format('Y-m-d H:i:s'))
            ->setParameter('7', (new DateTimeImmutable())->format('Y-m-d H:i:s'))
            ->setParameter('8', $ssoId)
            ->executeQuery();

        return $this->connection->lastInsertId();
    }
}
<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PushNotificationToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PushNotificationToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method PushNotificationToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method PushNotificationToken|null findOneByToken(string $token, array $orderBy = null)
 * @method PushNotificationToken[]    findAll()
 * @method PushNotificationToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PushNotificationTokenRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PushNotificationToken::class);
    }

    /**
     * @return PushNotificationToken[]
     */
    public function getActiveAndEnabled(): array
    {
        return $this->findBy(['active' => 1, 'enabled' => 1]);
    }
}

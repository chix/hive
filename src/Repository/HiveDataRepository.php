<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\HiveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method HiveData|null find($id, $lockMode = null, $lockVersion = null)
 * @method HiveData|null findOneBy(array $criteria, array $orderBy = null)
 * @method HiveData[]    findAll()
 * @method HiveData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class HiveDataRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, HiveData::class);
    }

    public function getForHive(int $hiveId, int $days = 1): array
    {
        $daysAgo = new \DateTime();
        $daysAgo->modify('-'.$days.' day');

        $qb = $this->createQueryBuilder('hd')
            ->andWhere('hd.hive = :hiveId')
            ->andWhere('hd.createdAt >= :daysAgo')
            ->setParameter('hiveId', $hiveId)
            ->setParameter('daysAgo', $daysAgo)
            ->orderBy('hd.id', 'ASC');

        return $qb->getQuery()->execute();
    }
}

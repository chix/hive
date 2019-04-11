<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Hive;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Hive|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hive|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hive[]    findAll()
 * @method Hive[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class HiveRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Hive::class);
    }
}

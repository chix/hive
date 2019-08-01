<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\MasterNode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MasterNode|null find($id, $lockMode = null, $lockVersion = null)
 * @method MasterNode|null findOneBy(array $criteria, array $orderBy = null)
 * @method MasterNode|null findOneByCode(string $code, array $orderBy = null)
 * @method MasterNode[]    findAll()
 * @method MasterNode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MasterNodeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MasterNode::class);
    }
}

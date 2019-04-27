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

    public function getForHivePerHour(int $hiveId, int $hours = 24): array
    {
        $daysAgo = new \DateTime();
        $daysAgo->modify('-'.$hours.' hour');

        $qb = $this->createQueryBuilder('hd')
            ->andWhere('hd.hive = :hiveId')
            ->andWhere('hd.createdAt >= :daysAgo')
            ->setParameter('hiveId', $hiveId)
            ->setParameter('daysAgo', $daysAgo)
            ->orderBy('hd.createdAt', 'ASC');

        $chartData = [];
        foreach ($qb->getQuery()->execute() as $data) {
            $createdAt = $data->getCreatedAt();
            // floor to the nearest hour
            $timestamp = $createdAt->setTime((int)$createdAt->format('H'), 0, 0, 0)->getTimestamp();
            // remove duplicates
            $chartData[$timestamp] = [
                'x' => $timestamp,
                'y' => $data->getWeight(),
            ];
        }

        return array_values($chartData);
    }

    public function getForHivePerDay(int $hiveId, int $days = 10): array
    {
        $daysAgo = new \DateTime();
        $daysAgo->modify('-'.$days.' day');

        // fetch first row of each day
        $sql = 'SELECT d1.*
            FROM hive_data d1,
            (SELECT DATE(created_at), MIN(created_at) AS min_created_at
            FROM hive_data
            WHERE hive_id = :hiveId
            AND created_at >= :daysAgo
            GROUP BY DATE(created_at)) d2
            WHERE d1.created_at = d2.min_created_at
            AND hive_id = :hiveId
            AND created_at >= :daysAgo
            ORDER BY d1.created_at ASC';
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('hiveId', $hiveId);
        $stmt->bindValue('daysAgo', $daysAgo->format('Y-m-d'));
        $result = $stmt->execute();
        if ($result === false) {
            return [];
        }

        $chartData = [];
        foreach ($stmt->fetchAll() as $data) {
            $createdAt = new \DateTime($data['created_at']);
            // reset time to midnight
            $timestamp = $createdAt->setTime(0, 0, 0, 0)->getTimestamp();
            // remove duplicates
            $chartData[$timestamp] = [
                'x' => $timestamp,
                'y' => (int)$data['weight'],
            ];
        }

        return array_values($chartData);
    }
}

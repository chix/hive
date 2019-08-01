<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Hive;
use App\Entity\HiveData;
use App\Repository\HiveRepository;
use App\Repository\MasterNodeRepository;

/**
 * Parses following JSON into HiveData entities:
 * {"master":"M1","data":{"H1":{"w":12345,"t":37.5},"H2":{"w":23456,"t":40.1}}}
 */
final class HiveDataDto
{
    /**
     * @var MasterNodeRepository
     */
    private $masterNodeRepository;

    /**
     * @var HiveRepository
     */
    private $hiveRepository;

    public function __construct(MasterNodeRepository $masterNodeRepository, HiveRepository $hiveRepository)
    {
        $this->masterNodeRepository = $masterNodeRepository;
        $this->hiveRepository = $hiveRepository;
    }

    /**
     * @return HiveData[]
     */
    public function createEntities(array $rawData)
    {
        $entities = [];

        if (empty($rawData['master']) || empty($rawData['data'])) {
            return $entities;
        }

        $masterNode = $this->masterNodeRepository->findOneByCode($rawData['master']);
        if ($masterNode === null) {
            return $entities;
        }

        foreach ($rawData['data'] as $hiveCode => $rawHiveData) {
            $hive = $this->hiveRepository->findOneByCode((string)$hiveCode);
            if ($hive === null || $hive->getMasterNode()->getId() !== $masterNode->getId()) {
                continue;
            }

            $hiveData = $this->parseHiveData($hive, $rawHiveData);
            $entities[] = $hiveData;
        }

        return $entities;
    }

    private function parseHiveData(Hive $hive, array $rawData): HiveData
    {
        $weight = $temperature = null;

        if (!empty($rawData['w'])) {
            $weight = intval($rawData['w'], 10);
        }

        if (!empty($rawData['t'])) {
            $temperature = floatval($rawData['t']);
        }

        $data = new HiveData();
        $data->setHive($hive);
        $data->setWeight($weight);
        $data->setTemperature($temperature);
        
        return $data;
    }
}

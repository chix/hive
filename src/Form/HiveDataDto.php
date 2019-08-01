<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Hive;
use App\Entity\HiveData;
use App\Entity\MasterNode;
use App\Repository\HiveRepository;

/**
 * Parses following JSON into HiveData entities:
 * {"H1":{"w":12345,"t":37.5},"H2":{"w":23456,"t":40.1}}
 */
final class HiveDataDto
{
    /**
     * @var HiveRepository
     */
    private $hiveRepository;

    public function __construct(HiveRepository $hiveRepository)
    {
        $this->hiveRepository = $hiveRepository;
    }

    /**
     * @return HiveData[]
     */
    public function createEntities(MasterNode $masterNode, array $rawData)
    {
        $entities = [];

        foreach ($rawData as $hiveCode => $rawHiveData) {
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

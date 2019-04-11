<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Table(name="hive_data")
 *
 * @ORM\Entity(repositoryClass="App\Repository\HiveDataRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 */
class HiveData extends BaseEntity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer")
     *
     * @Serializer\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Serializer\Expose
     */
    private $weight;

    /**
     * @ORM\Column(type="float", nullable=true)
     *
     * @Serializer\Expose
     */
    private $temperature;

    /**
     * @var Hive
     *
     * @ORM\ManyToOne(targetEntity="Hive", inversedBy="data")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Serializer\Expose
     */
    private $hive;

    public function getId(): int
    {
        return $this->id;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    public function setTemperature(?float $temperature): self
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function setHive(Hive $hive): self
    {
        $this->hive = $hive;

        return $this;
    }

    public function getHive(): Hive
    {
        return $this->hive;
    }
}

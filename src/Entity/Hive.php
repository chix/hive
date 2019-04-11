<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="hive")
 * @ORM\Entity(repositoryClass="App\Repository\HiveRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @UniqueEntity({"code"})
 */
class Hive extends BaseEntity
{
    use Traits\Locatable;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     *
     * @Serializer\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     *
     * @Serializer\Expose
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Serializer\Expose
     */
    private $name;

    /**
     * @var MasterNode
     *
     * @ORM\ManyToOne(targetEntity="MasterNode", inversedBy="hives")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Serializer\Expose
     */
    private $masterNode;

    /**
     * @var ArrayCollection<HiveData>
     *
     * @ORM\OneToMany(targetEntity="HiveData", mappedBy="hive")
     */
    private $data;

    public function __construct()
    {
        $this->data = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setMasterNode(MasterNode $masterNode): self
    {
        $this->masterNode = $masterNode;

        return $this;
    }

    public function getMasterNode(): MasterNode
    {
        return $this->masterNode;
    }

    public function addData(HiveData $data): self
    {
        $this->data[] = $data;

        return $this;
    }

    public function removeData(HiveData $data): void
    {
        $this->data->removeElement($data);
    }

    public function getData(): Collection
    {
        return $this->data;
    }
}

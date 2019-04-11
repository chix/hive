<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="master_node")
 * @ORM\Entity(repositoryClass="App\Repository\MasterNodeRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @UniqueEntity({"code"})
 */
class MasterNode extends BaseEntity
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
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Serializer\Expose
     */
    private $name;

    /**
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     *
     * @Serializer\Expose
     */
    private $code;

    /**
     * @var ArrayCollection<Hive>
     *
     * @ORM\OneToMany(targetEntity="Hive", mappedBy="masterNode")
     */
    private $hives;

    public function __construct()
    {
        $this->hives = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function addHive(Hive $hive): self
    {
        $this->hives[] = $hive;

        return $this;
    }

    public function removeHive(Hive $hive): void
    {
        $this->hives->removeElement($hive);
    }

    public function getHives(): Collection
    {
        return $this->hives;
    }
}

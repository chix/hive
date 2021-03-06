<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Table(name="push_notification_token", indexes={@ORM\Index(name="token_idx", columns={"token"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PushNotificationTokenRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 */
class PushNotificationToken extends BaseEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255)
     *
     * @Serializer\Expose
     */
    private $token;

    /**
     * For system activation/deactivation
     *
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", options={"default" : 1})
     */
    private $active;

    /**
     * @var int
     *
     * @ORM\Column(name="error_count", type="integer", options={"default" : 0})
     */
    private $errorCount;

    /**
     * @var array|null $lastResponse
     *
     * @ORM\Column(name="last_response", type="json_array", nullable=true)
     */
    private $lastResponse;

    /**
     * For user activation/deactivation
     *
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", options={"default" : 1})
     */
    private $enabled;

    /**
     * @var array|null $filters
     *
     * @ORM\Column(name="filters", type="json_array", nullable=true)
     */
    private $filters;

    public function getId(): int
    {
        return $this->id;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setErrorCount(int $errorCount): self
    {
        $this->errorCount = $errorCount;

        return $this;
    }

    public function getErrorCount(): int
    {
        return $this->errorCount;
    }

    public function setLastResponse(?array $lastResponse): self
    {
        $this->lastResponse = $lastResponse;

        return $this;
    }

    public function getLastResponse(): ?array
    {
        return $this->lastResponse;
    }

    public function setFilters(?array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function getFilters(): ?array
    {
        return $this->filters;
    }

    public function setEnabled(bool $enabled):  self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }
}

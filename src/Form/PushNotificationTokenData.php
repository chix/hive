<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\PushNotificationToken;
use App\Repository\PushNotificationTokenRepository;
use Symfony\Component\Validator\Constraints as Assert;

final class PushNotificationTokenData
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $token;

    /**
     * @var bool
     *
     * @Assert\NotNull()
     */
    public $enabled;

    /**
     * @var PushNotificationTokenRepository
     */
    private $tokenRepository;

    public function __construct(PushNotificationTokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }

    public function createOrUpdateEntity(array $filters): PushNotificationToken
    {
        $existingEntity = $this->tokenRepository->findOneByToken($this->token);
        $entity = $existingEntity ?: new PushNotificationToken();

        $entity->setActive(true);
        $entity->setErrorCount(0);
        $entity->setToken($this->token);
        $entity->setEnabled($this->enabled);
        $entity->setFilters($this->sanitizeFilters($filters));

        return $entity;
    }

    private function sanitizeFilters(array $rawFilters): array
    {
        $filters = [];
        
        return $filters;
    }
}

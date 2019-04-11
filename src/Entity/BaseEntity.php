<?php

declare(strict_types=1);

namespace App\Entity;

use Gedmo\Timestampable\Traits\TimestampableEntity as TimestampableTrait;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity as SoftDeleteableTrait;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 */
abstract class BaseEntity
{
    use TimestampableTrait;
    use SoftDeleteableTrait;
}

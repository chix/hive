<?php

declare(strict_types=1);

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\AbstractFOSRestController;

final class DefaultController extends AbstractFOSRestController
{
    /**
     * @Annotations\Get("/")
     */
    public function indexAction(): array
    {
        return ['Hello', 'Bee', 'Nice', '...'];
    }
}

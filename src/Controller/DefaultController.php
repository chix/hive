<?php

declare(strict_types=1);

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;

final class DefaultController extends FOSRestController
{
    /**
     * @Annotations\Get("/")
     */
    public function indexAction(): array
    {
        return ['Hello', 'Bee', 'Nice', '...'];
    }
}

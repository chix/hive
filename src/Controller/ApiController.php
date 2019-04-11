<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\PushNotificationTokenData;
use App\Form\PushNotificationTokenType;
use App\Repository\HiveRepository;
use App\Repository\HiveDataRepository;
use App\Repository\MasterNodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Annotations\Prefix("/api")
 */
final class ApiController extends FOSRestController
{
    /**
     * @Annotations\Get("/nodes")
     */
    public function getMasterNodesAction(MasterNodeRepository $masterNodeRepository)
    {
        return $masterNodeRepository->findAll();
    }

    /**
     * @Annotations\Get("/nodes/{id}", requirements={"id"="[0-9]+"})
     */
    public function getMasterNodeAction(MasterNodeRepository $masterNodeRepository, string $id)
    {
        $masterNode = $masterNodeRepository->find($id);
        if ($masterNode === null) {
            return $this->createNotFoundException();
        }
        return $masterNode;
    }

    /**
     * @Annotations\Get("/nodes/{id}/hives", requirements={"id"="[0-9]+"})
     */
    public function getMasterNodeHivesAction(MasterNodeRepository $masterNodeRepository, string $id)
    {
        $masterNode = $masterNodeRepository->find($id);
        if ($masterNode === null) {
            return $this->createNotFoundException();
        }
        return $masterNode->getHives();
    }

    /**
     * @Annotations\Get("/hives")
     */
    public function getHivesAction(HiveRepository $hiveRepository)
    {
        return $hiveRepository->findAll();
    }

    /**
     * @Annotations\Get("/hives/{id}", requirements={"id"="[0-9]+"})
     */
    public function getHiveAction(HiveRepository $hiveRepository, string $id)
    {
        $hive = $hiveRepository->find($id);
        if ($hive === null) {
            return $this->createNotFoundException();
        }
        return $hive;
    }

    /**
     * @Annotations\Get("/charts")
     */
    public function getChartsAction(HiveRepository $hiveRepository, HiveDataRepository $dataRepository)
    {
        $charts = [];
        $hives = $hiveRepository->findBy([], ['id' => 'ASC']);
        foreach ($hives as $hive) {
            $hiveData = $dataRepository->getForHive($hive->getId());
            $chartData = [];
            foreach ($hiveData as $data) {
                $chartData[] = [
                    'x' => $data->getCreatedAt()->getTimestamp(),
                    'y' => $data->getWeight(),
                ];
            }
            $chart = [];
            $chart['id'] = $hive->getId();
            $chart['name'] = $hive->getName();
            $chart['type'] = 'line';
            $chart['data'] = $chartData;
            $charts[] = $chart;
        }
        return $charts;
    }

    /**
     * @Annotations\Post("/push-notification-token")
     */
    public function postPushNotificationTokenAction(
        Request $request,
        EntityManagerInterface $entityManager,
        PushNotificationTokenData $dto
    ) {
        $form = $this->createForm(PushNotificationTokenType::class, $dto);
        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            $entity = $dto->createOrUpdateEntity($request->get('filters', []));
            $entityManager->persist($entity);
            $entityManager->flush();

            return $entity;
        }

        return $form;
    }
}

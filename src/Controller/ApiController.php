<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\HiveData;
use App\Form\HiveDataDto;
use App\Form\PushNotificationTokenData;
use App\Form\PushNotificationTokenType;
use App\Repository\HiveRepository;
use App\Repository\HiveDataRepository;
use App\Repository\MasterNodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\PushNotificationsService;
use App\Repository\PushNotificationTokenRepository;

/**
 * @Annotations\Prefix("/api")
 */
final class ApiController extends AbstractFOSRestController
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
     * @Annotations\Post("/nodes/{code}/data", requirements={"id"="[a-zA-Z0-9]+"})
     */
    public function postHiveDataActions(
        EntityManagerInterface $entityManager,
        MasterNodeRepository $masterNodeRepository,
        Request $request,
        HiveDataDto $dto,
        string $code
    ) {
        $masterNode = $masterNodeRepository->findOneByCode($code);
        if ($masterNode === null) {
            return $this->createNotFoundException();
        }

        $json = json_decode((string)$request->getContent(), true);

        if ($json !== null) {
            $entities = $dto->createEntities($masterNode, $json);

            foreach ($entities as $entity) {
                $entityManager->persist($entity);
            }
            if (!empty($entities)) {
                $entityManager->flush();
            }
        }

        return new Response();
    }

    /**
     * @Annotations\Get("/charts/hourly/{type}/{hours}",
     *   requirements={"type": "area|bar|line", "hours": "\d{1,2}"},
     *   defaults={"type": "line", "hours": 24}
     * )
     */
    public function getHourlyChartsAction(HiveRepository $hiveRepository, HiveDataRepository $dataRepository, $type, $hours)
    {
        $charts = [];
        $hives = $hiveRepository->findBy([], ['id' => 'ASC']);
        foreach ($hives as $hive) {
            $chartData = $dataRepository->getForHivePerHour($hive->getId(), (int)$hours);
            $chart = [];
            $chart['id'] = $hive->getId();
            $chart['name'] = $hive->getName();
            $chart['type'] = $type;
            $chart['mode'] = 'hourly';
            $chart['data'] = $chartData;
            $charts[] = $chart;
        }
        return $charts;
    }

    /**
     * @Annotations\Get("/charts/daily/{type}/{days}",
     *   requirements={"type": "area|bar|line", "hours": "\d{1,2}"},
     *   defaults={"type": "line", "days": 20}
     * )
     */
    public function getDailyChartsAction(HiveRepository $hiveRepository, HiveDataRepository $dataRepository, $type, $days)
    {
        $charts = [];
        $hives = $hiveRepository->findBy([], ['id' => 'ASC']);
        foreach ($hives as $hive) {
            $chartData = $dataRepository->getForHivePerDay($hive->getId(), (int)$days);
            $chart = [];
            $chart['id'] = $hive->getId();
            $chart['name'] = $hive->getName();
            $chart['type'] = $type;
            $chart['mode'] = 'daily';
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

    /**
     * @Annotations\Post("/nodes/{code}/setup-notification", requirements={"id"="[a-zA-Z0-9]+"})
     */
    public function postMasterNodeSetupSuccessfulAction(
        EntityManagerInterface $entityManager,
        MasterNodeRepository $masterNodeRepository,
        PushNotificationTokenRepository $pushNotificationTokenRepository,
        PushNotificationsService $pushNotificationsService,
        Request $request,
        string $code
    ) {
        $masterNode = $masterNodeRepository->findOneByCode($code);
        if ($masterNode === null) {
            return $this->createNotFoundException();
        }
        $json = json_decode((string)$request->getContent(), true);
        if ($json === null) {
            return $this->createNotFoundException();
        }

        $hiveMeasurements = [];
        foreach ($masterNode->getHives() as $hive) {
            $weight = (!empty($json[$hive->getCode()]) && !empty($json[$hive->getCode()]['w']))
                ? intval($json[$hive->getCode()]['w'], 10)
                : 0;
            $hiveMeasurements[] = sprintf('%s: %.2fkg', $hive->getName(), $weight / 1000);

            $hiveData = new HiveData();
            $hiveData->setHive($hive);
            $hiveData->setWeight($weight);
            $entityManager->persist($hiveData);
        }
        $entityManager->flush();
        $notification = implode(', ', $hiveMeasurements);
        try {
            foreach ($pushNotificationTokenRepository->getActiveAndEnabled() as $pushNotificationToken) {
                $pushNotificationsService->sendInBulk(
                    [['title' => $masterNode->getName() . ' setup', 'message' => $notification]],
                    $pushNotificationToken->getToken()
                );
            }
        } catch (\Exception $e) {
        }

        return new Response();
    }

    /**
     * @Annotations\Post("/nodes/{code}/error-report", requirements={"id"="[a-zA-Z0-9]+"})
     */
    public function postErrorReportAction(
        EntityManagerInterface $entityManager,
        LoggerInterface $errorReportLogger,
        MasterNodeRepository $masterNodeRepository,
        Request $request,
        string $code
    ) {
        $masterNode = $masterNodeRepository->findOneByCode($code);
        if ($masterNode === null) {
            $errorReportLogger->warning($code . ' not found.');
            return $this->createNotFoundException();
        }

        $errorReportLogger->warning((string)$request->getContent());

        $json = json_decode((string)$request->getContent(), true);
        if ($json !== null) {
            foreach ($masterNode->getHives() as $hive) {
                $weight = (!empty($json[$hive->getCode()]) && !empty($json[$hive->getCode()]['w']))
                    ? intval($json[$hive->getCode()]['w'], 10)
                    : 0;
                $hiveData = new HiveData();
                $hiveData->setHive($hive);
                $hiveData->setWeight($weight);
                $entityManager->persist($hiveData);
            }
            $entityManager->flush();
        }

        return new Response();
    }
}

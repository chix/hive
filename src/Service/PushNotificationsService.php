<?php

declare(strict_types=1);

namespace App\Service;

use Circle\RestClientBundle\Services\RestClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class PushNotificationsService
{
    /**
     * @var RestClient
     */
    private $restClient;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var string
     */
    protected $expoBackendUrl;

    public function __construct(RestClient $restClient, LoggerInterface $logger, string $expoBackendUrl)
    {
        $this->restClient = $restClient;
        $this->logger = $logger;
        $this->expoBackendUrl = $expoBackendUrl;
    }

    /**
     * @throws \Exception
     */
    public function sendInBulk(
        array $notifications,
        string $token,
        string $channel = 'notifications',
        string $priority = 'high',
        string $sound = 'default',
        bool $vibrate = true
    ): string {
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        $this->logger->debug(count($notifications) . ' notifications to be sent');
        if (empty($notifications)) {
            return '';
        }

        $data = [];
        $message = new \stdClass();
        $message->to = $token;
        $message->channelId = $channel;
        $message->priority = $priority;
        $message->sound = $sound;
        $message->vibrate = $vibrate;
        foreach ($notifications as $notification) {
            $messageTmp = clone $message;
            $messageTmp->title = $notification['title'];
            $messageTmp->body = $notification['message'];
            if (isset($notification['data'])) {
                $messageTmp->data = $notification['data'];
            }
            $data[] = $messageTmp;
        }
        $json = (string)$serializer->encode($data, 'json');

        $curlOptions = [
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'Accept-Encoding: gzip, deflate',
            ],
        ];
        $response = $this->restClient->post($this->expoBackendUrl, $json, $curlOptions);
        if ($response->getStatusCode() >= 400) {
            throw new \Exception($response->getContent(), $response->getStatusCode());
        }

        foreach ($data as $notification) {
            $this->logger->debug('Notification sent to ' . $token, json_decode(json_encode($notification), true));
        }

        return $response->getContent();
    }

    /**
     * @throws \Exception
     */
    public function getReceipt(string $id): string
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        $message = new \stdClass();
        $message->ids = [$id];
        $json = (string)$serializer->encode($message, 'json');

        $curlOptions = [
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'Accept-Encoding: gzip, deflate',
            ],
        ];
        $response = $this->restClient->post($this->expoBackendUrl, $json, $curlOptions);
        if ($response->getStatusCode() >= 400) {
            throw new \Exception($response->getContent(), $response->getStatusCode());
        }
        return $response->getContent();
    }
}

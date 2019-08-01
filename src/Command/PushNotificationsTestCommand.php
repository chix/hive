<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\PushNotificationsService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class PushNotificationsTestCommand extends Command
{
    protected static $defaultName = 'app:push-notifications:test';

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var PushNotificationsService
     */
    private $pushNotificationsService;

    public function __construct(
        PushNotificationsService $pushNotificationsService,
        LoggerInterface $logger
    ) {
        $this->pushNotificationsService = $pushNotificationsService;
        $this->logger = $logger;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Send a push notification.')
            ->addArgument('token', InputArgument::REQUIRED, 'Expo token')
            ->addArgument('channel', InputArgument::REQUIRED, 'Android channel ID')
            ->addArgument('id', InputArgument::REQUIRED, 'Detail ID')
            ->addOption('count', 'c', InputOption::VALUE_REQUIRED, 'How many?', 1)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $token = $input->getArgument('token');
        $channel = $input->getArgument('channel');
        if (!is_string($token) || !is_string($channel)) {
            $output->writeln('<error>token and channel arguments have to be string.</error>');
            return 0;
        }
        $id = $input->getArgument('id');
        $count = intval($input->getOption('count'));

        $notifications = [];
        for ($i = 1; $i <= $count; $i++) {
            $notification = [
                'title' => 'Notification ' . $i,
                'message' => 'Test notification',
                'data' => new \stdClass(),
            ];
            $notification['data']->id = $id;
            $notifications[] = $notification;
        }

        try {
            $this->pushNotificationsService->sendInBulk($notifications, $token, $channel);
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
        return 0;
    }
}

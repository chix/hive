<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\PushNotificationsService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class PushNotificationsReceiptsCommand extends Command
{
    protected static $defaultName = 'app:push-notifications:get-receipt';

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var PushNotificationsService
     */
    private $pushNotificationsService;

    /**
     * @var string
     */
    protected $expoBackendUrl;

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
            ->setDescription('Get a push notification receipt')
            ->addArgument('id', InputArgument::REQUIRED, 'Notification ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $id = $input->getArgument('id');
        if (!is_string($id)) {
            $output->writeln('<error>id argument has to be string.</error>');
            return 0;
        }

        try {
            $this->pushNotificationsService->getReceipt($id);
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }

        return 0;
    }
}

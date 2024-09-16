<?php

namespace App\Command;

use App\Entity\TicketTypes;
use App\Service\GorgiasApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:fetch-integrations',
    description: 'Add a short description for your command',
)]
class FetchIntegrationsCommand extends Command
{

    protected static $defaultName = 'app:fetch-integrations';

    private $entityManager;
    private $gorgiasApiService;

    public function __construct(EntityManagerInterface $entityManager, GorgiasApiService $gorgiasApiService)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->gorgiasApiService = $gorgiasApiService;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Update tickets from Gorgias')
            ->setHelp('This command allows you to update tickets with missing contact reason or country code.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $params = [
            'limit' => 100,  // Le maximum permis par l'API
            'order_by' => 'created_datetime:desc',
        ];

        $allIntegrations = [];
        $cursor = null;

        do {
            if ($cursor) {
                $params['cursor'] = $cursor;  // Ajout du curseur pour passer à la page suivante
            }

            $integrations = $this->gorgiasApiService->fetchIntegrations($params);

            if (!$integrations || !isset($integrations['data']) || empty($integrations['data'])) {
                $output->writeln('No integrations found.');
                return Command::FAILURE;
            }

            // Ajout des nouvelles intégrations au tableau global
            $allIntegrations = array_merge($allIntegrations, $integrations['data']);

            // Mise à jour du curseur pour la page suivante
            $cursor = $integrations['meta']['next_cursor'] ?? null;

        } while ($cursor);  // Continuer tant qu'il y a une page suivante

        var_dump($allIntegrations);
        foreach ($allIntegrations as $integration) {
            $ticketType = new TicketTypes();
            $ticketType->setIntegrationId($integration['id']);
            $ticketType->setName($integration['name']);
            $ticketType->setType($integration['type']);

            $this->entityManager->persist($ticketType);
        }

        $this->entityManager->flush();
        $output->writeln('Integrations inserted successfully.');

        return Command::SUCCESS;

    }
}

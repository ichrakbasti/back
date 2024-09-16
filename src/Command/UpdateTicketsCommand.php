<?php

namespace App\Command;

use App\Entity\Tickets;
use App\Service\GorgiasApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-tickets',
    description: 'Add a short description for your command',
)]
class UpdateTicketsCommand extends Command
{
    protected static $defaultName = 'app:update-tickets';

    private $entityManager;
    private $gorgiasService;

    public function __construct(EntityManagerInterface $entityManager, GorgiasApiService $gorgiasService)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->gorgiasService = $gorgiasService;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Update tickets from Gorgias')
            ->setHelp('This command allows you to update tickets with missing contact reason or country code.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $tickets = $this->entityManager->getRepository(Tickets::class)->createQueryBuilder('t')
            ->where('t.createdDatetime >= :date')
            ->andWhere('t.contactReason IS NULL OR t.countryCode IS NULL')
            ->setParameter('date', new \DateTime('-15 days'))
            ->setMaxResults(500)
            ->getQuery()
            ->getResult();

        foreach ($tickets as $ticket) {
            $ticketId = $ticket->getGorgiasTicketId();
            $customFields = $this->gorgiasService->getTicketsCustomField($ticketId);

            // Vérification de l'existence des custom fields
            if (isset($customFields['custom_fields']) && is_array($customFields['custom_fields'])) {
                $fields = $customFields['custom_fields'];

                // Vérification et mise à jour des champs spécifiques
                if (isset($fields["26"]['value']) && !empty($fields["26"]['value'])) {
                    $ticket->setContactReason($fields[26]['value']);
                }

                if (isset($fields["27"]['value']) && !empty($fields["27"]['value'])) {
                    $ticket->setCountryCode($fields[27]['value']);
                }
            }

            $this->entityManager->persist($ticket);
        }

        $this->entityManager->flush();

        $io->success('Tickets have been successfully updated.');

        return Command::SUCCESS;
    }
}

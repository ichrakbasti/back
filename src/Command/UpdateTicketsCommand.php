<?php

namespace App\Command;

use App\Entity\Tickets;
use App\Repository\TeamsRepository;
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
    private $teamsRepository;

    public function __construct(EntityManagerInterface $entityManager, GorgiasApiService $gorgiasService, TeamsRepository $teamsRepository)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->gorgiasService = $gorgiasService;
        $this->teamsRepository = $teamsRepository;
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

        $batchSize = 100; // Taille du lot de tickets à traiter
        $offset = 0; // Position de départ pour la pagination

        do {

            // Récupérer un lot de 100 tickets sans `team` pour les traiter
            $tickets = $this->entityManager->getRepository(Tickets::class)->createQueryBuilder('t')
                ->andWhere('t.team IS NULL')
                ->setFirstResult($offset)
                ->setMaxResults($batchSize)
                ->getQuery()
                ->getResult();

            var_dump($batchSize);
            foreach ($tickets as $ticket) {
                $ticketId = $ticket->getGorgiasTicketId();

                try {
                    $customFields = $this->gorgiasService->getTicketsCustomField($ticketId);


                    $team = $this->teamsRepository->findOneBy(["gorgiasTeamId"=>$customFields['assignee_team_id']]);
                    if ($team) {
                       $ticket->setTeam($team);
                    }

                    usleep(200000);  // Pause de 0.2 secondes entre chaque requête
                    $this->entityManager->persist($ticket);

                } catch (\Exception $e) {
                    if ($e->getCode() === 429) {
                        // Temporisation supplémentaire en cas d'erreur de débit
                        sleep(60);
                    } else {
                        throw $e;
                    }
                }
            }

            // Envoi des mises à jour en base de données pour ce lot
            $this->entityManager->flush();
            $this->entityManager->clear(); // Nettoyage pour éviter les fuites de mémoire

            $offset += $batchSize; // Déplacement vers le lot suivant

        } while (count($tickets) === $batchSize); // Continue jusqu'à ce que le nombre de tickets soit inférieur à la taille du lot

        $io->success('Tickets have been successfully updated.');

        return Command::SUCCESS;
    }
}

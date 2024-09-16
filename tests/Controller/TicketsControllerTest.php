<?php

namespace App\Test\Controller;

use App\Entity\Tickets;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TicketsControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/tickets/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Tickets::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Ticket index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'ticket[gorgiasTicketId]' => 'Testing',
            'ticket[subject]' => 'Testing',
            'ticket[priority]' => 'Testing',
            'ticket[createdAt]' => 'Testing',
            'ticket[updatedAt]' => 'Testing',
            'ticket[userId]' => 'Testing',
            'ticket[status]' => 'Testing',
            'ticket[type]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Tickets();
        $fixture->setGorgiasTicketId('My Title');
        $fixture->setSubject('My Title');
        $fixture->setPriority('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setUserId('My Title');
        $fixture->setStatus('My Title');
        $fixture->setType('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Ticket');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Tickets();
        $fixture->setGorgiasTicketId('Value');
        $fixture->setSubject('Value');
        $fixture->setPriority('Value');
        $fixture->setCreatedAt('Value');
        $fixture->setUpdatedAt('Value');
        $fixture->setUserId('Value');
        $fixture->setStatus('Value');
        $fixture->setType('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'ticket[gorgiasTicketId]' => 'Something New',
            'ticket[subject]' => 'Something New',
            'ticket[priority]' => 'Something New',
            'ticket[createdAt]' => 'Something New',
            'ticket[updatedAt]' => 'Something New',
            'ticket[userId]' => 'Something New',
            'ticket[status]' => 'Something New',
            'ticket[type]' => 'Something New',
        ]);

        self::assertResponseRedirects('/tickets/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getGorgiasTicketId());
        self::assertSame('Something New', $fixture[0]->getSubject());
        self::assertSame('Something New', $fixture[0]->getPriority());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getUpdatedAt());
        self::assertSame('Something New', $fixture[0]->getUserId());
        self::assertSame('Something New', $fixture[0]->getStatus());
        self::assertSame('Something New', $fixture[0]->getType());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Tickets();
        $fixture->setGorgiasTicketId('Value');
        $fixture->setSubject('Value');
        $fixture->setPriority('Value');
        $fixture->setCreatedAt('Value');
        $fixture->setUpdatedAt('Value');
        $fixture->setUserId('Value');
        $fixture->setStatus('Value');
        $fixture->setType('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/tickets/');
        self::assertSame(0, $this->repository->count([]));
    }
}

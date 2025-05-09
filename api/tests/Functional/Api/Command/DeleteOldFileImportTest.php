<?php

namespace App\Tests\Functional\Api\Command;

use App\Entity\Job\ImportFile;
use App\Tests\Functional\Api\AuthApiTestCase;
use App\Tests\Utils\VichUploaderTestTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class DeleteOldFileImportTest extends AuthApiTestCase
{
    use VichUploaderTestTrait;

    private ?Application $application = null;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->application = new Application(self::bootKernel());
        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->resetVichUploaderTestDataDirectory('import');
    }

    protected function tearDown(): void
    {
        $this->application = null;
    }

    private function modifyUpdateDate(): void
    {
        $repo = $this->entityManager->getRepository(ImportFile::class);
        foreach (['test.pdf' => '-36 hours', 'fake.csv' => '-6 hours'] as $fileName => $relativeDate) {
            $entity = $repo->findOneBy(['originalFilename' => $fileName]);
            if (null === $entity) {
                return;
            }
            $entity->setUploadDate(new \DateTimeImmutable($relativeDate));
            $this->entityManager->persist($entity);
        }
        $this->entityManager->flush();
    }

    public function testBaseCommand()
    {
        $this->modifyUpdateDate();

        $client = $this->createAuthenticatedClient();
        $response = $client->request(
            'GET',
            'api/import_files',
            [
                'headers' => ['Content-Type' => 'application/ld+json'],
            ]
        );
        $this->assertEquals(2, $response->toArray()['totalItems']);

        $command = $this->application->find('app:import-files:delete-old');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['--relative-date-time' => '-1 days', '--dry-run' => true]);
        $commandTester->assertCommandIsSuccessful();

        $this->assertStringContainsString('test.pdf', $commandTester->getDisplay());
        $response = $client->request(
            'GET',
            'api/import_files',
            [
                'headers' => ['Content-Type' => 'application/ld+json'],
            ]
        );
        $this->assertEquals(2, $response->toArray()['totalItems']);
        $this->assertTrue($this->vichUploaderFileExists(['import', 'test.pdf']), 'Files should exist');

        $command = $this->application->find('app:import-files:delete-old');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['--relative-date-time' => '-1 days']);
        $commandTester->assertCommandIsSuccessful();
        $response = $client->request(
            'GET',
            'api/import_files',
            [
                'headers' => ['Content-Type' => 'application/ld+json'],
            ]
        );
        $this->assertEquals(1, $response->toArray()['totalItems']);
        $this->assertFalse($this->vichUploaderFileExists(['import', 'test.pdf']), 'Files should not exist');
    }
}

<?php

namespace App\Tests\Functional\Api\Job\Import;

use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Tests\Functional\Api\AuthApiTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class JobImportTestCase extends AuthApiTestCase
{
    protected FileLocatorInterface $locator;
    protected ParameterBagInterface $params;

    private Application $application;

    protected function setUp(): void
    {
        parent::setUp();
        $this->locator = static::getContainer()->get('file_locator');
        $this->params = static::getContainer()->get('parameter_bag');
    }

    protected function getProjectDir(): string
    {
        return $this->params->get('kernel.project_dir');
    }

    protected function getTestUploadFile(string $fileName, ?string $path = ''): UploadedFile
    {
        $tempFileName = tempnam(sys_get_temp_dir(), 'api_test_');
        if (false === $tempFileName) {
            throw new \RuntimeException('Could not create temporary file.');
        }

        if (!copy($this->getFixturesFilePath($fileName, $path), $tempFileName)) {
            unlink($tempFileName); // Cleanup if copy fails
            throw new \RuntimeException('Could not copy file.');
        }

        return new UploadedFile(
            $tempFileName,
            $fileName,
            null,
            null,
            true
        );
    }

    protected function getFixturesFilePath(string $fileName, ?string $path = ''): string
    {
        $filePath = $this->getProjectDir().DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'input';
        if ($path) {
            if ('/' === $path[0]) {
                $filePath = $path;
            } else {
                $filePath .= DIRECTORY_SEPARATOR.$path;
            }
        }

        return $filePath.DIRECTORY_SEPARATOR.$fileName;
    }

    protected function getApplication(): Application
    {
        if (!isset($this->application)) {
            $this->application = new Application(self::bootKernel());
        }

        return $this->application;
    }

    protected function consumeQueue(?string $transportName = 'async', ?int $expectedQueueLength = 1): void
    {
        self::bootKernel();

        /** @var ReceiverInterface $transport */
        $transport = self::getContainer()->get("messenger.transport.$transportName");
        //        $this->assertCount($expectedQueueLength, $transport->get());

        $command = $this->getApplication()->find('messenger:consume');
        $commandTester = new CommandTester($command);
        $commandTester->execute([$transportName, '--limit' => $expectedQueueLength]);

        $this->assertCount(0, $transport->get());
    }

    protected function uploadFile(
        Client $client,
        string $fileName,
        string $url,
        ?string $path = '',
        ?string $description = '',
    ): int|Uuid {
        $uploadedFile = $this->getTestUploadFile($fileName, $path);
        $response = $client->request(
            'POST',
            $url,
            [
                'headers' => ['Content-Type' => 'multipart/form-data'],
                'json' => [
                    'description' => $description,
                ],
                'extra' => [
                    'files' => [
                        'file' => $uploadedFile,
                    ],
                ],
            ],
        );
        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('jsonld');

        return $this->getJsonResponseId($response);
    }

    protected function runJob(Client $client, int|Uuid $jobId, string $url): void
    {
        $response = $client->request(
            'POST',
            "api/work_units/$jobId/run",
            [
                'headers' => ['Content-Type' => 'application/ld+json'],
            ]
        );
        $this->assertResponseIsSuccessful();
        $this->assertEquals((string) $jobId, (string) $this->getJsonResponseId($response));

        $this->assertResponseIsSuccessful();
    }

    protected function fetchApiWorkUnitItem(Client $client, int|Uuid $jobId): ResponseInterface
    {
        return $client->request(
            'GET',
            "api/work_units/$jobId",
            [
                'headers' => ['Content-Type' => 'application/ld+json'],
            ]
        );
    }

    protected function fetchApiWorkUnitCollection(Client $client): ResponseInterface
    {
        return $client->request(
            'GET',
            'api/work_units',
            [
                'headers' => ['Content-Type' => 'application/ld+json'],
            ]
        );
    }
}

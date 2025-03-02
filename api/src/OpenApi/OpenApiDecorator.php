<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\OpenApi;
use ArrayObject;
use Psr\Log\LoggerInterface;

class OpenApiDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        readonly private OpenApiFactoryInterface $decorated,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);
        $this->setUserMeChangePassword($openApi);

        /* foreach (['/api/import_files', '/api/media_objects', '/api/jobs/import/zooarchaeology/bones'] as $path) {
             $this->setBinaryFormat($openApi, $path);
         }*/

        return $openApi;
    }

    private function hasPath(OpenApi $openApi, $path): bool
    {

        $flag = array_key_exists($path, $openApi->getPaths()->getPaths());
        if (!$flag) {
            $this->logger->warning("$path does not exist");
        }

        return $flag;
    }

    private function setUserMeChangePassword(OpenApi $openApi): void
    {
        $path = '/api/users/me/change-password';
        if ($this->hasPath($openApi, $path)) {
            $pathItem = $openApi->getPaths()->getPath('/api/users/me/change-password');
            $operation = $pathItem->getPost();

            $openApi->getPaths()->addPath(
                '/api/users/me/change-password',
                $pathItem->withPost(
                    $operation->withDescription('Change user password')->withSummary('Change user password')
                )
            );
        }
    }

    private function setBinaryFormat(OpenApi $openApi, $path): void
    {
        if ($this->hasPath($openApi, $path)) {
            $paths = $openApi->getPaths();
            $operation = $paths->getPath($path)->getPost();
            if ($operation) {
                $requestBody = new RequestBody(
                    'File upload',
                    new ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary',
                                    ],
                                ],
                                'encoding' => [
                                    'file' => [
                                        'contentType' => 'application/octet-stream',
                                    ],
                                ],
                            ],
                        ],
                    ])
                );
                $updatedOperation = $operation->withRequestBody($requestBody);
                $updatedPath = $paths->getPath($path)->withPost($updatedOperation);
                $paths->addPath($path, $updatedPath);
            }
        }
    }
}

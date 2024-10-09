<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;

class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(readonly private OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);
        $pathItem = $openApi->getPaths()->getPath('/api/users/me/change-password');
        $operation = $pathItem->getPost();

        $openApi->getPaths()->addPath(
            '/api/users/me/change-password',
            $pathItem->withPost(
                $operation->withDescription('Change user password')->withSummary('Change user password')
            )
        );

        foreach (['/api/sites', '/api/stratigraphic_units', '/api/samples'] as $path) {
            $pathItem = $openApi->getPaths()->getPath($path);
            $operation = $pathItem->getGet();
            $openApi->getPaths()->addPath(
                $path,
                $pathItem->withGet(
                    $operation->withOperationId("{$operation->getOperationId()}__root__")
                )
            );
        }


        return $openApi;
    }
}

<?php

namespace App\Metadata\Attribute;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class MediaObjectJoinApiResource extends ApiResource
{
    public function __construct()
    {
        parent::__construct(
            operations: [
                new Get(),
                new GetCollection(
                    parameters: [
                        'item' => new QueryParameter(
                            filter: 'item_collection__media_object.search_filter',
                            property: 'item'
                        ),
                    ]
                ),
                new Post(
                    inputFormats: ['multipart' => 'multipart/form-data'],
                    denormalizationContext: [
                        'groups' => ['MediaObjectJoin:create'],
                    ],
                    securityPostDenormalize: "is_granted('create', object)"
                ),
                new Delete(
                    status: 204,
                    security: "is_granted('delete', object)",
                    output: false
                ),
            ],
            normalizationContext: [
                'groups' => ['MediaObjectJoin:read'],
            ]
        );
    }
}


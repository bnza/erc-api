<?php

namespace App\Serializer;

use App\Entity\Data\M2M\StratigraphicUnitsMediaObject;
use App\Entity\Data\MediaObject;
use App\Service\MediaObjectDuplicateFinder;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class MediaObjectFileDenormalizer implements DenormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.jsonld.normalizer.item')]
        readonly private NormalizerInterface $decorated,
        readonly private MediaObjectDuplicateFinder $duplicateFinder,
    ) {
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = []): object
    {
        $return = $this->decorated->denormalize($data, $type, 'jsonld', $context);
        $file = $data['file'];
        $sha256 = hash_file('sha256', $file);
        $mediaObject = $this->duplicateFinder->get($type, $sha256);
        if (!$mediaObject) {
            $mediaObject = new MediaObject();
            $mediaObject->file = $data['file'];
            $mediaObject->sha256 = hash_file('sha256', $file);
            $mediaObject->uploadDate = new \DateTimeImmutable();
        }

        $return->mediaObject = $mediaObject;

        return $return;
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return StratigraphicUnitsMediaObject::class === $context['resource_class']
            && is_array($data) && array_key_exists('file', $data) && $data['file'] instanceof File;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            'object' => null,
            '*' => false,
            StratigraphicUnitsMediaObject::class => true,
        ];
    }
}

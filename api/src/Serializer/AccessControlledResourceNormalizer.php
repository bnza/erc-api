<?php

namespace App\Serializer;

use App\Entity\Data\M2M\SitesUsers;
use App\Entity\Data\Pottery;
use App\Entity\Data\Sample;
use App\Entity\Data\Site;
use App\Entity\Data\StratigraphicUnit;
use App\Entity\Data\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class AccessControlledResourceNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'ACCESS_CONTROLLED_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';

    public function __construct(
        #[Autowire(service: 'api_platform.jsonld.normalizer.item')]
        private NormalizerInterface $decorated,
        private Security $security,
    ) {
    }

    public function normalize(mixed $object, ?string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;
        $data = $this->decorated->normalize($object, $format, $context);
        if (is_array($data)) {
            $data['_acl'] = [];
            $data['_acl']['canRead'] = $this->security->isGranted('read', $object);
            $data['_acl']['canUpdate'] = $this->security->isGranted('update', $object);
            $data['_acl']['canDelete'] = $this->security->isGranted('delete', $object);
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        // Normalize only the requested resource
        if (array_key_exists('object', $context) && $context['resource_class'] !== get_class($context['object'])) {
            return false;
        }

        if (!$this->decorated->supportsNormalization($data, $format, $context)) {
            return false;
        }

        return array_key_exists('groups', $context)
            && is_array($context['groups'])
            && array_reduce(
                $context['groups'],
                function ($acc, $group) {
                    $acc |= str_contains($group, ':acl:');

                    return $acc;
                },
                false
            );
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Pottery::class => true,
            Sample::class => true,
            Site::class => true,
            SitesUsers::class => true,
            User::class => true,
            StratigraphicUnit::class => true,
        ];
    }
}

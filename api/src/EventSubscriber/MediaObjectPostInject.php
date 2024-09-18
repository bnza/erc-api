<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Data\MediaObject;
use App\Service\MediaObjectThumbnailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Vich\UploaderBundle\Event\Event as VicUploaderEvent;
use Vich\UploaderBundle\Event\Events as VicUploaderEvents;

class MediaObjectPostInject implements EventSubscriberInterface
{
    private ?MediaObject $media = null;

    public function __construct(private readonly MediaObjectThumbnailer $thumbnailer)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            VicUploaderEvents::POST_UPLOAD => ['setFilePath', EventPriorities::POST_DESERIALIZE],
            KernelEvents::TERMINATE => ['generateThumbnail'],
        ];
    }

    public function setFilePath(VicUploaderEvent $event): void
    {
        $this->media = $event->getObject();
    }

    public function generateThumbnail(TerminateEvent $event): void
    {
        if ($this->media) {
            $this->thumbnailer->generateThumbnail($this->media);
        }
    }
}

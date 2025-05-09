<?php

namespace App\Service;

use App\Entity\Data\MediaObject;
use Psr\Log\LoggerInterface;

class MediaObjectThumbnailer
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    private array $supportedFormats = [
        'application/pdf',
        'image/gif',
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    public function support(MediaObject $mediaObject): bool
    {
        return in_array($mediaObject->getMimeType(), $this->supportedFormats);
    }

    public function generateThumbnail(MediaObject $mediaObject): void
    {
        if ($this->support($mediaObject)) {
            $this->createThumb($mediaObject, 256);
        }
    }

    public function geThumbnailPath(string $path): string
    {
        return preg_replace('/(?<filename>.+)(?<extension>\.\w+)?$/U', '$1.thumb.jpeg', $path);
    }

    /**
     * @see https://www.closingtags.com/generate-thumbnails-for-pdf-uploads/
     */
    private function createThumb(MediaObject $mediaObject, $thumbWidth = 100): void
    {
        $suffix = 'application/pdf' === $mediaObject->getFile()->getMimeType() ? '[0]' : '';
        $realPath = $mediaObject->getFile()->getRealPath();
        try {
            $img = new \Imagick($realPath.$suffix);
            $imageDimensions = $img->getImageGeometry();
            $thumbDimensions = $imageDimensions['width'] > $imageDimensions['height'] ? [0, 200] : [200, 0];
            $img->scaleImage(...$thumbDimensions);
            $img->setImageFormat('jpg');
            $img = $img->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
            $img->writeImage($this->geThumbnailPath($realPath));

            $img->clear();
            $this->logger->info(
                'Successfully created thumbnail for media object: '.$mediaObject->getFile()->getRealPath()
            );
        } catch (\ImagickException $e) {
            $this->logger->error($e->getMessage());
        }
    }
}

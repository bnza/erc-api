<?php

namespace App\Controller;

use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\Job\ImportFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
final class CreateImportFileAction extends AbstractController
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function __invoke(Request $request): ImportFile
    {
        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile || false === $uploadedFile->getSize()) {
            throw new BadRequestHttpException('Upload failed: please check uploaded file size. Max 10M allowed');
        }

        $mediaObject = new ImportFile();
        $mediaObject->file = $uploadedFile;
        $mediaObject->uploadDate = new \DateTimeImmutable();

        $this->validator->validate($mediaObject);

        return $mediaObject;
    }
}

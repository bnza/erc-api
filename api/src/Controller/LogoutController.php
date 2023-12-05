<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class LogoutController extends AbstractController
{
    #[Route('/api/logout', name: 'app_logout', methods: ['POST'])]
    public function __invoke(): Response
    {
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}

<?php

namespace App\Controller;

use ApiPlatform\Validator\Exception\ValidationException;
use App\Dto\PasswordChangeDto;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
class ChangePasswordController extends AbstractController
{
    public function __construct(
        private readonly Security $security,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $repository,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function __invoke(PasswordChangeDto $passwordChangeDTO): Response
    {
        $currentUser = $this->security->getUser();

        if (!$currentUser) {
            return new JWTAuthenticationFailureResponse();
        }

        $user = $this->repository->findOneBy(['email' => $currentUser->getUserIdentifier()]);

        if (!$user) {
            return new JWTAuthenticationFailureResponse();
        }

        $errors = $this->validator->validate($passwordChangeDTO);
        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        if (!$this->passwordHasher->isPasswordValid($user, $passwordChangeDTO->oldPassword)) {
            return new JWTAuthenticationFailureResponse('Wrong password');
        }

        $this->repository->upgradePassword(
            $user,
            $this->passwordHasher->hashPassword($user, $passwordChangeDTO->repeatPassword)
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}

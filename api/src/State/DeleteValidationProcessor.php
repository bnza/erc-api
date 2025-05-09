<?php

namespace App\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use App\Validator\IsResourceNotReferenced;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class DeleteValidationProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.remove_processor')]
        private ProcessorInterface $decoratedProcessor,
        private ValidatorInterface $validator,
        private IsResourceNotReferenced $constraint,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        // Check if operation type is "DELETE"
        if ($operation instanceof Delete) {
            $violations = $this->validator->validate($data, $this->constraint);

            if (count($violations) > 0) {
                throw new ValidationException($violations);
            }
        }

        // Proceed with the standard DELETE operation
        $this->decoratedProcessor->process($data, $operation, $uriVariables, $context);
    }
}

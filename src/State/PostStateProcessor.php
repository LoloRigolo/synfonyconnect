<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Post;
use Symfony\Bundle\SecurityBundle\Security;

final class PostStateProcessor implements ProcessorInterface
{
    public function __construct(
        /** @var ProcessorInterface<Post, Post> */
        private readonly ProcessorInterface $persistProcessor,
        private readonly Security           $security,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof Post) {
            $data->setUser($this->security->getUser());
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}

<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class LikeController extends AbstractController
{
    #[Route('/post/{id}/like', name: 'app_post_like', methods: ['POST'])]
    public function like(int $id, PostRepository $postRepository, EntityManagerInterface $em, Request $request): Response
    {
        $post = $postRepository->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Post introuvable.');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$post->isLikedBy($user)) {
            $post->addLike($user);
            $em->flush();
        }

        return $this->redirect($request->headers->get('referer', $this->generateUrl('app_home')));
    }

    #[Route('/post/{id}/unlike', name: 'app_post_unlike', methods: ['POST'])]
    public function unlike(int $id, PostRepository $postRepository, EntityManagerInterface $em, Request $request): Response
    {
        $post = $postRepository->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Post introuvable.');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $post->removeLike($user);
        $em->flush();

        return $this->redirect($request->headers->get('referer', $this->generateUrl('app_home')));
    }
}

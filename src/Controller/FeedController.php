<?php

namespace App\Controller;

use App\Service\FeedCacheService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class FeedController extends AbstractController
{
    #[Route('/feed', name: 'app_feed')]
    public function index(FeedCacheService $feedCache): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $posts          = $feedCache->getFeedForUser($user);
        $followingCount = $user->getFollowing()->count();

        return $this->render('feed/index.html.twig', [
            'posts'          => $posts,
            'followingCount' => $followingCount,
        ]);
    }
}

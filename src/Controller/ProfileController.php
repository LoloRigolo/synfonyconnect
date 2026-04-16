<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profil/{username}', name: 'app_profile', requirements: ['username' => '[a-zA-Z0-9._\-]{3,30}'])]
    public function show(string $username, UserRepository $userRepository, PostRepository $postRepository): Response
    {
        $user = $userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        $posts = $postRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);

        /** @var \App\Entity\User|null $currentUser */
        $currentUser = $this->getUser();
        $isFollowing = $currentUser && $currentUser->getId() !== $user->getId()
            ? $currentUser->isFollowing($user)
            : false;

        return $this->render('profile/show.html.twig', [
            'profileUser'     => $user,
            'posts'           => $posts,
            'isFollowing'     => $isFollowing,
            'followersCount'  => $user->getFollowers()->count(),
            'followingCount'  => $user->getFollowing()->count(),
        ]);
    }
}

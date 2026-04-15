<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profil/{username}', name: 'app_profile')]
    public function show(string $username, UserRepository $userRepository, PostRepository $postRepository): Response
    {
        $user = $userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        $posts = $postRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);

        return $this->render('profile/show.html.twig', [
            'profileUser' => $user,
            'posts'       => $posts,
        ]);
    }
}

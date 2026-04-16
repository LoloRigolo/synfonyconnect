<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class FollowController extends AbstractController
{
    #[Route('/profil/{username}/follow', name: 'app_follow', methods: ['POST'])]
    public function follow(string $username, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $target = $userRepository->findOneBy(['username' => $username]);

        if (!$target) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        /** @var \App\Entity\User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser->getId() === $target->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas vous suivre vous-même.');
            return $this->redirectToRoute('app_profile', ['username' => $username]);
        }

        $currentUser->follow($target);
        $em->flush();

        return $this->redirectToRoute('app_profile', ['username' => $username]);
    }

    #[Route('/profil/{username}/unfollow', name: 'app_unfollow', methods: ['POST'])]
    public function unfollow(string $username, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $target = $userRepository->findOneBy(['username' => $username]);

        if (!$target) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        /** @var \App\Entity\User $currentUser */
        $currentUser = $this->getUser();

        $currentUser->unfollow($target);
        $em->flush();

        return $this->redirectToRoute('app_profile', ['username' => $username]);
    }
}

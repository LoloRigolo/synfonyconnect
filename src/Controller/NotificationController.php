<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class NotificationController extends AbstractController
{
    #[Route('/notifications', name: 'app_notifications')]
    public function index(NotificationRepository $notificationRepository, EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User $user */
        $user          = $this->getUser();
        $notifications = $notificationRepository->findForUser($user);

        foreach ($notifications as $notification) {
            if (!$notification->isRead()) {
                $notification->markAsRead();
            }
        }
        $em->flush();

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }
}

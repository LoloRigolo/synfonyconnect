<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class MessageController extends AbstractController
{
    #[Route('/messages', name: 'app_messages')]
    public function index(MessageRepository $messageRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user          = $this->getUser();
        $conversations = $messageRepository->findConversations($user);

        return $this->render('message/index.html.twig', [
            'conversations' => $conversations,
        ]);
    }

    #[Route('/messages/{username}', name: 'app_conversation')]
    public function conversation(
        string $username,
        Request $request,
        UserRepository $userRepository,
        MessageRepository $messageRepository,
        EntityManagerInterface $em
    ): Response {
        /** @var \App\Entity\User $currentUser */
        $currentUser = $this->getUser();
        $other       = $userRepository->findOneBy(['username' => $username]);

        if (!$other) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        if ($other->getId() === $currentUser->getId()) {
            return $this->redirectToRoute('app_messages');
        }

        // Marquer les messages reçus comme lus
        $messages = $messageRepository->findConversation($currentUser, $other);
        foreach ($messages as $message) {
            if ($message->getRecipient() === $currentUser && !$message->isRead()) {
                $message->markAsRead();
            }
        }
        $em->flush();

        // Traitement de l'envoi
        if ($request->isMethod('POST')) {
            $content = trim($request->request->get('content', ''));

            if ($content !== '' && mb_strlen($content) <= 2000) {
                $message = new Message($currentUser, $other, $content);
                $em->persist($message);
                $em->flush();
            }

            return $this->redirectToRoute('app_conversation', ['username' => $username]);
        }

        return $this->render('message/conversation.html.twig', [
            'other'    => $other,
            'messages' => $messages,
        ]);
    }
}

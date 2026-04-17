<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Security\Voter\PostVoter;
use App\Service\FeedCacheService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class PostController extends AbstractController
{
    #[Route('/post/nouveau', name: 'app_post_new')]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $em, UserRepository $userRepository, FeedCacheService $feedCache): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \App\Entity\User $author */
            $author = $this->getUser();
            $post->setUser($author);
            $em->persist($post);
            $em->flush();

            $feedCache->invalidateFollowersOf($author);

            $this->addFlash('success', 'Votre post a bien été publié !');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('post/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/post/{id}/supprimer', name: 'app_post_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(int $id, PostRepository $postRepository, EntityManagerInterface $em, Request $request, FeedCacheService $feedCache): Response
    {
        $post = $postRepository->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Post introuvable.');
        }

        $this->denyAccessUnlessGranted(PostVoter::DELETE, $post);

        if (!$this->isCsrfTokenValid('delete_post_' . $id, $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        /** @var \App\Entity\User $author */
        $author = $post->getUser();

        $em->remove($post);
        $em->flush();

        $feedCache->invalidateFollowersOf($author);

        $this->addFlash('success', 'Post supprimé avec succès.');

        return $this->redirect($request->headers->get('referer', $this->generateUrl('app_home')));
    }
}

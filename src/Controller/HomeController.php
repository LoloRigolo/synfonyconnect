<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, PostRepository $postRepository): Response
    {
        $sort = $request->query->get('sort', 'DESC');
        if (!in_array(strtoupper($sort), ['ASC', 'DESC'])) {
            $sort = 'DESC';
        }

        $posts = $postRepository->findAllSortedByDate($sort);

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'sort'  => strtoupper($sort),
        ]);
    }
}

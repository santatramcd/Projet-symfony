<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Service\ArticleService;


class CategoryController extends AbstractController
{

    #[Route('/category/{slug}', name: 'category_show')]
    public function show(string $slug, CategoryRepository $categoryRepository , ArticleService $articleService): Response
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);
    
        if (!$category) {
            return $this->redirectToRoute('app_home');
        }
    
        return $this->render('category/show.html.twig', [
            'category' => $category,
            'articles' => $articleService->getPaginatedArticles($category),

        ]);
    }
    
}

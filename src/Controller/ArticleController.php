<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\Type\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class ArticleController extends AbstractController
{
    #[Route('/article/{slug}', name: 'article_show')]
    public function show(ManagerRegistry $doctrine, string $slug): Response
    {
        $article = $doctrine->getRepository(Article::class)->findOneBy(['slug' => $slug]);

        if (!$article) {
            return $this->redirectToRoute('app_home');
        }

        $comment = New Comment($article) ;
        $commentForm = $this->createForm(CommentType::class , $comment);

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'commentForm' => $commentForm->createView(),
        ]);
        
    }
}

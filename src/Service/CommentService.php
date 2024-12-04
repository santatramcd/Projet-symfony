<?php

namespace App\Service;

use App\Repository\CommentRepository; // Assurez-vous d'importer le bon repository
use Knp\Component\Pager\PaginatorInterface; // Assurez-vous d'importer PaginatorInterface
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\Article; // Importation de l'entité Article
use Knp\Component\Pager\Pagination\PaginationInterface; // Importation de l'interface PaginationInterface

class CommentService
{
    public function __construct(
        private RequestStack $requestStack,
        private CommentRepository $commentRepo,
        private PaginatorInterface $paginator
    ) {}

    public function getPaginatedComments(?Article $article = null): PaginationInterface
    {
        $request = $this->requestStack->getMainRequest();
        $page = $request ? $request->query->get('page', 1) : 1; // Utilisez get() pour obtenir la page
        $limit = 10; // Ajustez le nombre de commentaires à afficher par page
        $commentsQuery = $this->commentRepo->findForPagination($article); // Passez l'article pour la pagination

        return $this->paginator->paginate($commentsQuery, $page, $limit);
    }
}

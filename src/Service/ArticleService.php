<?php

namespace App\Service;

use App\Repository\ArticleRepository; // Assurez-vous d'importer le bon repository
use Doctrine\Common\Persistence\ObjectRepository;
use Knp\Component\Pager\PaginatorInterface; // Assurez-vous d'importer PaginatorInterface
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\Category; // Importation de l'entité Category

class ArticleService
{
    public function __construct(
        private RequestStack $requestStack,
        private ArticleRepository $articleRepo,
        private PaginatorInterface $paginator
    ) {}

    public function getPaginatedArticles(?Category $category = null)
    {
        $request = $this->requestStack->getMainRequest();
        $page = $request ? $request->query->get('page', 1) : 1; // Utilisez get() pour obtenir la page
        $limit = 2;
        $articlesQuery = $this->articleRepo->findForPagination($category);

        return $this->paginator->paginate($articlesQuery, $page, $limit);
    }
}

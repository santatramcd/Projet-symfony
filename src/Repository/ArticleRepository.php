<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Category; // Assurez-vous d'importer l'entité Category
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query; // Assurez-vous d'importer la classe Query
use Doctrine\ORM\QueryBuilder; // Assurez-vous d'importer la classe QueryBuilder

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findForPagination(?Category $category = null): Query
    {
        $qb = $this->createQueryBuilder('a')
            ->orderBy('a.createdAt', 'DESC');

        if ($category) {
            $qb->leftJoin('a.categories', 'c')
                ->where('c.id = :categoryId')
                ->setParameter('categoryId', $category->getId());
        }

        return $qb->getQuery();
    }
}

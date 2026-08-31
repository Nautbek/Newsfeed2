<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Feed;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findExistingGuids(Feed $feed, array $guids): array
    {
        return $this->createQueryBuilder('a')
            ->select('a.guid')
            ->where('a.feed = :feed')
            ->andWhere('a.guid IN (:guids)')
            ->setParameter('feed', $feed)
            ->setParameter('guids', $guids)
            ->getQuery()
            ->getSingleColumnResult();
    }
}

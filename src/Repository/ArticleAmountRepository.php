<?php

namespace App\Repository;

use App\Entity\ArticleAmount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ArticleAmount>
 *
 * @method ArticleAmount|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleAmount|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleAmount[]    findAll()
 * @method ArticleAmount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleAmountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleAmount::class);
    }

    public function getAllForCustomerAnalytic($id)
    {
        $sql = 'select aa.amount as amount, aa.value as value, a.title as title, aa.tax as tax
                from article_amount aa
                inner join article_warehouse aw on aw.id = aa.article_warehouse_id
                inner join article a on a.id = aw.article_id
                where aa.bill_id = :id';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);

        $stmt->bindValue(':id', $id, ParameterType::INTEGER);

        return $stmt->executeQuery()->fetchAllAssociative();
    }
}

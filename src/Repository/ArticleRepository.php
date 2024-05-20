<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

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

    public function getArticlesForConsumerReview($id, $title)
    {
        $sql = 'select a.title as title, a.producer as producer, min(aw.selling_price) as min_price, group_concat(w.title) as warehouses
                from article a
                inner join article_warehouse aw on a.id = aw.article_id
                inner join warehouse w on aw.warehouse_id = w.id
                inner join company c on c.id = a.company_id
                where c.id = :id';

        if ($title != '')
        {
            $sql .= ' and (a.title like :title or a.producer like :title)';
        }

        $sql .= ' group by a.title, a.producer';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);

        $stmt->bindValue(':id', $id, ParameterType::INTEGER);

        if ($title != '')
        {
            $stmt->bindValue(':title','%' . $title . '%');
        }

        $result = $stmt->executeQuery()->fetchAllAssociative();

        return $result;
    }
}

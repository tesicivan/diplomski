<?php

namespace App\Repository;

use App\Entity\TableSchedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TableSchedule>
 *
 * @method TableSchedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method TableSchedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method TableSchedule[]    findAll()
 * @method TableSchedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TableScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TableSchedule::class);
    }

    //    /**
    //     * @return TableSchedule[] Returns an array of TableSchedule objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?TableSchedule
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

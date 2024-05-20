<?php

namespace App\Repository;

use App\Entity\PayingDaysRebate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PayingDaysRebate>
 *
 * @method PayingDaysRebate|null find($id, $lockMode = null, $lockVersion = null)
 * @method PayingDaysRebate|null findOneBy(array $criteria, array $orderBy = null)
 * @method PayingDaysRebate[]    findAll()
 * @method PayingDaysRebate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PayingDaysRebateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PayingDaysRebate::class);
    }

    //    /**
    //     * @return PayingDaysRebate[] Returns an array of PayingDaysRebate objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PayingDaysRebate
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

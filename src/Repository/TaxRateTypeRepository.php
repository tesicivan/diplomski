<?php

namespace App\Repository;

use App\Entity\TaxRateType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaxRateType>
 *
 * @method TaxRateType|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaxRateType|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaxRateType[]    findAll()
 * @method TaxRateType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxRateTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaxRateType::class);
    }

    //    /**
    //     * @return TaxRateType[] Returns an array of TaxRateType objects
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

    //    public function findOneBySomeField($value): ?TaxRateType
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

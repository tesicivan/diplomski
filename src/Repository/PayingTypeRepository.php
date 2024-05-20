<?php

namespace App\Repository;

use App\Entity\PayingType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PayingType>
 *
 * @method PayingType|null find($id, $lockMode = null, $lockVersion = null)
 * @method PayingType|null findOneBy(array $criteria, array $orderBy = null)
 * @method PayingType[]    findAll()
 * @method PayingType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PayingTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PayingType::class);
    }

    //    /**
    //     * @return PayingType[] Returns an array of PayingType objects
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

    //    public function findOneBySomeField($value): ?PayingType
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

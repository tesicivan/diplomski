<?php

namespace App\Repository;

use App\Entity\Partner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Partner>
 *
 * @method Partner|null find($id, $lockMode = null, $lockVersion = null)
 * @method Partner|null findOneBy(array $criteria, array $orderBy = null)
 * @method Partner[]    findAll()
 * @method Partner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartnerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Partner::class);
    }

    public function getAll($company)
    {
        $sql = 'select p.id as id, p.title as title, p.street as street, 
                p.number as number, p.post_code as post_code, p.city as city,
                p.country as country, p.tin as tin, pr.paying_days as paying_days, pr.rebate as rebate
                from partner p 
                inner join partner_company pc on pc.partner_id = p.id
                inner join company c on c.id = pc.company_id
                inner join paying_days_rebate pr on (pr.company_id = c.id and pr.partner_id = p.id)
                where c.id = :id';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);

        $stmt->bindValue(':id', $company, ParameterType::INTEGER);

        $result = $stmt->executeQuery()->fetchAllAssociative();

        return $result;
    }

    public function getOtherPartners($company, $tin)
    {
        $sql = 'select p.id as id, p.title as title, p.tin
                from partner p
                where p.id not in (select pc.partner_id
                                  from partner_company pc
                                  where pc.company_id = :id)
                and p.tin != :tin';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);

        $stmt->bindValue(':id', $company, ParameterType::INTEGER);
        $stmt->bindValue(':tin', $tin);

        $result = $stmt->executeQuery()->fetchAllAssociative();

        return $result;
    }
}

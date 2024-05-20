<?php

namespace App\Repository;

use App\Entity\Bill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bill>
 *
 * @method Bill|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bill|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bill[]    findAll()
 * @method Bill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BillRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bill::class);
    }

    public function getBillsForDate($date, $dateTo, $companyId)
    {
        $from = $date->format('Y-m-d 00:00:00');
        $to = $dateTo->format('Y-m-d 23:59:59');

        $sql = 'select b.id as id, b.date as date, sum(aa.value * aa.amount) as value, sum(aa.tax * aa.amount) as tax
                from bill b
                inner join article_amount aa on aa.bill_id = b.id
                where b.date between :from and :to
                and b.company_id = :companyId
                group by b.id';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);

        $stmt->bindValue(':companyId', $companyId, ParameterType::INTEGER);
        $stmt->bindValue(':from', $from);
        $stmt->bindValue(':to', $to);

        $result = $stmt->executeQuery()->fetchAllAssociative();

        return $result;
    }

    public function  getCurrentMonthDataForUser($customerIdNumber)
    {
        $sql = 'select b.date as date, sum(aa.amount * aa.value) as amount
                from article_amount aa
                inner join bill b on b.id = aa.bill_id
                where b.customer_id_number = :customerId
                and month(b.date) = month(current_date)
                group by b.date';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);

        $stmt->bindValue(':customerId', $customerIdNumber);

        return $stmt->executeQuery()->fetchAllAssociative();
    }

    public function getAnalyticsForUser($customerIdNumber)
    {
        $sql = 'select b.id as id, c.title as company, group_concat(distinct(w.title)) as warehouse, pt.title as pay, sum(aa.value * aa.amount) as val
                from bill b
                inner join company c on c.id = b.company_id
                inner join paying_type pt on pt.id = b.paying_type_id
                inner join article_amount aa on aa.bill_id = b.id
                inner join article_warehouse aw on aw.id = aa.article_warehouse_id
                inner join warehouse w on w.id = aw.warehouse_id
                where b.customer_id_number = :customerId
                group by b.id
                order by b.date desc';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);

        $stmt->bindValue(':customerId', $customerIdNumber);

        return $stmt->executeQuery()->fetchAllAssociative();
    }

    public function  getLastYearDataForUser($customerIdNumber)
    {
        $sql = 'select month(b.date) as month, sum(aa.amount * aa.value) as amount
                from article_amount aa
                inner join bill b on b.id = aa.bill_id
                where b.customer_id_number = :customerId
                and datediff(date, current_date()) < 365
                and ((month(current_date()) != month(date)) or ((month(current_date()) = month(date)) and (year(current_date()) = year(date))))
                group by month(b.date)';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);

        $stmt->bindValue(':customerId', $customerIdNumber);

        return $stmt->executeQuery()->fetchAllAssociative();
    }

    public function getAllDataForAdminReview($startDate, $endDate, $title, $tin)
    {
        $endDate .= ' 23:59:59';

        $sql = 'select c.tin as tin, c.title as title, sum(aa.value * aa.amount) as payed, sum(aa.tax * aa.amount) as vat
                from bill b
                inner join company c on b.company_id = c.id
                inner join article_amount aa on aa.bill_id = b.id
                where b.date >= :startDate and b.date <= :endDate';

        if ($title != '')
        {
            $sql .= ' and c.title like :title';
        }

        if ($tin != '')
        {
            $sql .= ' and c.tin like :tin';
        }

        $sql .= ' group by c.tin, c.title order by c.tin';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);

        $stmt->bindValue(':startDate', $startDate);
        $stmt->bindValue(':endDate', $endDate);

        if ($title != '')
        {
            $stmt->bindValue(':title', '%' . $title . '%');
        }

        if ($tin != '')
        {
            $stmt->bindValue(':tin', '%' . $tin . '%');
        }

        return $stmt->executeQuery()->fetchAllAssociative();
    }
}

<?php

namespace App\Repository;

use App\Entity\CreditReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CreditReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreditReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreditReport[]    findAll()
 * @method CreditReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreditReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CreditReport::class);
    }

    /**
     * @return array<int, CreditReport>
     */
    public function findRecentForAccount(int $accountAid, int $limit = 10): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.accountAid = :aid')
            ->setParameter('aid', $accountAid)
            ->orderBy('r.parsedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
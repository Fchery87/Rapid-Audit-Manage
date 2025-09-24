<?php

namespace App\Repository;

use App\Entity\DisputeCase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DisputeCase>
 */
class DisputeCaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DisputeCase::class);
    }

    public function findPrimaryOpenCase(int $accountAid): ?DisputeCase
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.accountAid = :aid')
            ->andWhere('c.status IN (:statuses)')
            ->setParameter('aid', $accountAid)
            ->setParameter('statuses', DisputeCase::activeStatuses())
            ->orderBy('c.updatedAt', 'DESC')
            ->addOrderBy('c.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array<int, DisputeCase>
     */
    public function findAllForAccount(int $accountAid): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.accountAid = :aid')
            ->setParameter('aid', $accountAid)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\AuditLogEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AuditLogEntry>
 */
class AuditLogEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuditLogEntry::class);
    }

    /**
     * @return array<int, AuditLogEntry>
     */
    public function findRecent(int $limit = 50): array
    {
        return $this->createQueryBuilder('log')
            ->orderBy('log.occurredAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<int, AuditLogEntry>
     */
    public function findRecentForAccount(int $accountAid, int $limit = 50): array
    {
        return $this->createQueryBuilder('log')
            ->andWhere('log.accountAid = :aid')
            ->setParameter('aid', $accountAid)
            ->orderBy('log.occurredAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}

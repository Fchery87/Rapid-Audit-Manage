<?php

namespace App\Repository;

use App\Entity\DisputeTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DisputeTask>
 */
class DisputeTaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DisputeTask::class);
    }

    /**
     * @return array<int, DisputeTask>
     */
    public function findOpenTasksForCase(int $caseId): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.disputeCase = :case')
            ->andWhere('t.status != :done')
            ->setParameter('case', $caseId)
            ->setParameter('done', DisputeTask::STATUS_DONE)
            ->orderBy('t.dueAt', 'ASC')
            ->addOrderBy('t.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

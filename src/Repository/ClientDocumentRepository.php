<?php

namespace App\Repository;

use App\Entity\ClientDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ClientDocument>
 */
class ClientDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientDocument::class);
    }

    /**
     * @return array<int, ClientDocument>
     */
    public function findForAccount(int $accountAid): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.accountAid = :aid')
            ->setParameter('aid', $accountAid)
            ->orderBy('d.uploadedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

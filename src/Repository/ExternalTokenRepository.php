<?php

namespace App\Repository;

use App\Entity\ExternalToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExternalToken>
 *
 * @method ExternalToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExternalToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExternalToken[]    findAll()
 * @method ExternalToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExternalTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExternalToken::class);
    }

    //    /**
    //     * @return ExternalToken[] Returns an array of ExternalToken objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ExternalToken
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

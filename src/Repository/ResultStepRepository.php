<?php

namespace App\Repository;

use App\Entity\ResultStep;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ResultStep|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResultStep|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResultStep[]    findAll()
 * @method ResultStep[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultStepRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResultStep::class);
    }

    // /**
    //  * @return ResultStep[] Returns an array of ResultStep objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ResultStep
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

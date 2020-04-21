<?php

namespace App\Repository;

use App\Entity\QuizzCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QuizzCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuizzCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuizzCategory[]    findAll()
 * @method QuizzCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizzCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizzCategory::class);
    }

    // /**
    //  * @return QuizzCategory[] Returns an array of QuizzCategory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QuizzCategory
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository;

use App\Entity\Surname;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Surname|null find($id, $lockMode = null, $lockVersion = null)
 * @method Surname|null findOneBy(array $criteria, array $orderBy = null)
 * @method Surname[]    findAll()
 * @method Surname[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SurnameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Surname::class);
    }

    // /**
    //  * @return Surname[] Returns an array of Surname objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Surname
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

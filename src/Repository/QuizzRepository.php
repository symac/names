<?php

namespace App\Repository;

use App\Entity\Quizz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Quizz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quizz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quizz[]    findAll()
 * @method Quizz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizzRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quizz::class);
    }

    public function findNeedAnagram() {
        $qb = $this->createQueryBuilder('q');
        $quizz = $qb->where("q.anagram is null")
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        return $quizz;
    }

    public function findRandom() {
        $qb = $this->createQueryBuilder('q');
        $countResults = $qb->select("COUNT(q)")
            ->where("q.anagram is not null")
            ->getQuery()->getSingleScalarResult();

        $offset = rand(0, $countResults - 1);

        $result = $qb->select("q")
            ->where("q.anagram is not null")
            ->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults(1)
            ->getOneOrNullResult();

        return $result;

    }
    // /**
    //  * @return Quizz[] Returns an array of Quizz objects
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
    public function findOneBySomeField($value): ?Quizz
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

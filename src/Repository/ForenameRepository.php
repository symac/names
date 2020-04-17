<?php

namespace App\Repository;

use App\Entity\Forename;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Forename|null find($id, $lockMode = null, $lockVersion = null)
 * @method Forename|null findOneBy(array $criteria, array $orderBy = null)
 * @method Forename[]    findAll()
 * @method Forename[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForenameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Forename::class);
    }

    public function findByWikidata(string $wikidata) {
        $forenames = $this->findBy(["wikidata" => $wikidata]);

        if (sizeof($forenames) > 0)
        {
            return $forenames;
        }

        // Looking for a forename with multiple wikidata
        $qb = $this->createQueryBuilder('f');

        $qb->select()
            ->where($qb->expr()->like('f.wikidata', '?1'))
            ->setParameter(1, "%".$wikidata."%");

        $forenames = $qb->getQuery()->getResult();
        if (sizeof($forenames) > 0) {
            return $forenames;
        }
        return [];
    }

    // /**
    //  * @return Forename[] Returns an array of Forename objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Forename
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

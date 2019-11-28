<?php

namespace App\Repository;

use App\Entity\Financeur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Financeur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Financeur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Financeur[]    findAll()
 * @method Financeur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FinanceurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Financeur::class);
    }

    // /**
    //  * @return Financeur[] Returns an array of Financeur objects
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
    public function findOneBySomeField($value): ?Financeur
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

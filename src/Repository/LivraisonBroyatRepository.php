<?php

namespace App\Repository;

use App\Entity\LivraisonBroyat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LivraisonBroyat|null find($id, $lockMode = null, $lockVersion = null)
 * @method LivraisonBroyat|null findOneBy(array $criteria, array $orderBy = null)
 * @method LivraisonBroyat[]    findAll()
 * @method LivraisonBroyat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivraisonBroyatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LivraisonBroyat::class);
    }

    // /**
    //  * @return LivraisonBroyat[] Returns an array of LivraisonBroyat objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LivraisonBroyat
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

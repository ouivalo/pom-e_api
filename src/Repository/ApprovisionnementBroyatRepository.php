<?php

namespace App\Repository;

use App\Entity\ApprovisionnementBroyat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ApprovisionnementBroyat|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApprovisionnementBroyat|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApprovisionnementBroyat[]    findAll()
 * @method ApprovisionnementBroyat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApprovisionnementBroyatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApprovisionnementBroyat::class);
    }

    // /**
    //  * @return ApprovisionnementBroyat[] Returns an array of ApprovisionnementBroyat objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ApprovisionnementBroyat
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
